<?php
/**
 * Media Upload Handler
 * Handles profile pictures, order images, and other media uploads
 * Enhanced Delivery Pro System v2.0
 */

session_start();
require_once 'config.php';
require_once 'functions.php';

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

$user = $_SESSION['user'];
$response = ['success' => false, 'error' => 'Unknown error'];

// Configuration
$config = [
    'avatar' => [
        'max_size' => 5 * 1024 * 1024, // 5MB
        'allowed_types' => ['image/jpeg', 'image/png', 'image/webp', 'image/gif'],
        'allowed_extensions' => ['jpg', 'jpeg', 'png', 'webp', 'gif'],
        'directory' => 'uploads/avatars/',
        'max_width' => 800,
        'max_height' => 800
    ],
    'order' => [
        'max_size' => 10 * 1024 * 1024, // 10MB
        'allowed_types' => ['image/jpeg', 'image/png', 'image/webp'],
        'allowed_extensions' => ['jpg', 'jpeg', 'png', 'webp'],
        'directory' => 'uploads/orders/',
        'max_width' => 1920,
        'max_height' => 1920
    ],
    'document' => [
        'max_size' => 15 * 1024 * 1024, // 15MB
        'allowed_types' => ['image/jpeg', 'image/png', 'application/pdf'],
        'allowed_extensions' => ['jpg', 'jpeg', 'png', 'pdf'],
        'directory' => 'uploads/documents/',
        'max_width' => 2000,
        'max_height' => 2000
    ]
];

/**
 * Validate uploaded file
 */
function validateFile($file, $type, $config) {
    $errors = [];
    $settings = $config[$type] ?? $config['avatar'];

    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $uploadErrors = [
            UPLOAD_ERR_INI_SIZE => 'File exceeds server maximum size',
            UPLOAD_ERR_FORM_SIZE => 'File exceeds form maximum size',
            UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
            UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the upload'
        ];
        $errors[] = $uploadErrors[$file['error']] ?? 'Unknown upload error';
        return $errors;
    }

    // Check file size
    if ($file['size'] > $settings['max_size']) {
        $maxMB = $settings['max_size'] / 1024 / 1024;
        $errors[] = "File too large. Maximum size is {$maxMB}MB";
    }

    // Check MIME type using finfo (more secure than relying on upload)
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);

    if (!in_array($mimeType, $settings['allowed_types'])) {
        $errors[] = 'Invalid file type. Allowed: ' . implode(', ', $settings['allowed_extensions']);
    }

    // Check file extension
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, $settings['allowed_extensions'])) {
        $errors[] = 'Invalid file extension';
    }

    // For images, verify it's a valid image
    if (strpos($mimeType, 'image/') === 0) {
        $imageInfo = @getimagesize($file['tmp_name']);
        if ($imageInfo === false) {
            $errors[] = 'File is not a valid image';
        } else {
            // Check dimensions
            if ($imageInfo[0] > $settings['max_width'] || $imageInfo[1] > $settings['max_height']) {
                // Will be resized, just warn
            }
        }
    }

    return $errors;
}

/**
 * Resize image if needed
 */
function resizeImage($sourcePath, $destPath, $maxWidth, $maxHeight, $mimeType) {
    $imageInfo = getimagesize($sourcePath);
    if (!$imageInfo) return false;

    $width = $imageInfo[0];
    $height = $imageInfo[1];

    // Check if resize is needed
    if ($width <= $maxWidth && $height <= $maxHeight) {
        return copy($sourcePath, $destPath);
    }

    // Calculate new dimensions
    $ratio = min($maxWidth / $width, $maxHeight / $height);
    $newWidth = (int)($width * $ratio);
    $newHeight = (int)($height * $ratio);

    // Create source image based on type
    switch ($mimeType) {
        case 'image/jpeg':
            $source = imagecreatefromjpeg($sourcePath);
            break;
        case 'image/png':
            $source = imagecreatefrompng($sourcePath);
            break;
        case 'image/webp':
            $source = imagecreatefromwebp($sourcePath);
            break;
        case 'image/gif':
            $source = imagecreatefromgif($sourcePath);
            break;
        default:
            return false;
    }

    if (!$source) return false;

    // Create destination image
    $dest = imagecreatetruecolor($newWidth, $newHeight);

    // Preserve transparency for PNG and GIF
    if ($mimeType === 'image/png' || $mimeType === 'image/gif') {
        imagealphablending($dest, false);
        imagesavealpha($dest, true);
        $transparent = imagecolorallocatealpha($dest, 255, 255, 255, 127);
        imagefilledrectangle($dest, 0, 0, $newWidth, $newHeight, $transparent);
    }

    // Resize
    imagecopyresampled($dest, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

    // Save
    $quality = 85;
    switch ($mimeType) {
        case 'image/jpeg':
            $result = imagejpeg($dest, $destPath, $quality);
            break;
        case 'image/png':
            $result = imagepng($dest, $destPath, 8);
            break;
        case 'image/webp':
            $result = imagewebp($dest, $destPath, $quality);
            break;
        case 'image/gif':
            $result = imagegif($dest, $destPath);
            break;
        default:
            $result = false;
    }

    // Cleanup
    imagedestroy($source);
    imagedestroy($dest);

    return $result;
}

/**
 * Generate unique filename
 */
function generateFilename($originalName, $userId, $type) {
    $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    $timestamp = time();
    $random = bin2hex(random_bytes(4));
    return "{$type}_{$userId}_{$timestamp}_{$random}.{$extension}";
}

/**
 * Create directory if not exists
 */
function ensureDirectory($path) {
    $fullPath = __DIR__ . '/' . $path;
    if (!is_dir($fullPath)) {
        return mkdir($fullPath, 0755, true);
    }
    return true;
}

/**
 * Delete old file
 */
function deleteOldFile($path) {
    if (!empty($path)) {
        $fullPath = __DIR__ . '/' . $path;
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
    }
}

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uploadType = $_POST['type'] ?? 'avatar';

    // Validate upload type
    if (!isset($config[$uploadType])) {
        $response = ['success' => false, 'error' => 'Invalid upload type'];
        echo json_encode($response);
        exit();
    }

    // Check if file was uploaded
    if (!isset($_FILES['file']) || $_FILES['file']['error'] === UPLOAD_ERR_NO_FILE) {
        $response = ['success' => false, 'error' => 'No file uploaded'];
        echo json_encode($response);
        exit();
    }

    $file = $_FILES['file'];
    $settings = $config[$uploadType];

    // Validate file
    $errors = validateFile($file, $uploadType, $config);
    if (!empty($errors)) {
        $response = ['success' => false, 'error' => implode('. ', $errors)];
        echo json_encode($response);
        exit();
    }

    // Ensure directory exists
    $directory = $settings['directory'] . $user['id'] . '/';
    if (!ensureDirectory($directory)) {
        $response = ['success' => false, 'error' => 'Failed to create upload directory'];
        echo json_encode($response);
        exit();
    }

    // Generate filename and path
    $filename = generateFilename($file['name'], $user['id'], $uploadType);
    $relativePath = $directory . $filename;
    $fullPath = __DIR__ . '/' . $relativePath;

    // Get MIME type
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);

    // Process upload
    $uploadSuccess = false;

    if (strpos($mimeType, 'image/') === 0 && $mimeType !== 'application/pdf') {
        // Resize image if needed
        $uploadSuccess = resizeImage(
            $file['tmp_name'],
            $fullPath,
            $settings['max_width'],
            $settings['max_height'],
            $mimeType
        );
    } else {
        // Move file directly
        $uploadSuccess = move_uploaded_file($file['tmp_name'], $fullPath);
    }

    if ($uploadSuccess) {
        // Handle different upload types
        switch ($uploadType) {
            case 'avatar':
                // Get old avatar path
                $stmt = $conn->prepare("SELECT avatar_url FROM users1 WHERE id = ?");
                $stmt->execute([$user['id']]);
                $oldAvatar = $stmt->fetchColumn();

                // Delete old avatar
                deleteOldFile($oldAvatar);

                // Update database
                $stmt = $conn->prepare("UPDATE users1 SET avatar_url = ? WHERE id = ?");
                $stmt->execute([$relativePath, $user['id']]);

                // Update session
                $_SESSION['user']['avatar_url'] = $relativePath;

                $response = [
                    'success' => true,
                    'message' => 'Profile picture updated successfully',
                    'path' => $relativePath,
                    'url' => $relativePath . '?v=' . time()
                ];
                break;

            case 'order':
                // Return path for order attachment
                $response = [
                    'success' => true,
                    'message' => 'Image uploaded successfully',
                    'path' => $relativePath,
                    'url' => $relativePath . '?v=' . time()
                ];
                break;

            case 'document':
                // Return path for document
                $response = [
                    'success' => true,
                    'message' => 'Document uploaded successfully',
                    'path' => $relativePath,
                    'url' => $relativePath
                ];
                break;
        }
    } else {
        $response = ['success' => false, 'error' => 'Failed to save file'];
    }
}

// Handle AJAX avatar upload from profile page
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['avatar'])) {
    $_FILES['file'] = $_FILES['avatar'];
    $_POST['type'] = 'avatar';

    // Re-run the upload logic
    $file = $_FILES['file'];
    $uploadType = 'avatar';
    $settings = $config[$uploadType];

    $errors = validateFile($file, $uploadType, $config);
    if (empty($errors)) {
        $directory = $settings['directory'] . $user['id'] . '/';
        ensureDirectory($directory);

        $filename = generateFilename($file['name'], $user['id'], $uploadType);
        $relativePath = $directory . $filename;
        $fullPath = __DIR__ . '/' . $relativePath;

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);

        if (resizeImage($file['tmp_name'], $fullPath, $settings['max_width'], $settings['max_height'], $mimeType)) {
            // Delete old avatar
            $stmt = $conn->prepare("SELECT avatar_url FROM users1 WHERE id = ?");
            $stmt->execute([$user['id']]);
            $oldAvatar = $stmt->fetchColumn();
            deleteOldFile($oldAvatar);

            // Update database
            $stmt = $conn->prepare("UPDATE users1 SET avatar_url = ? WHERE id = ?");
            $stmt->execute([$relativePath, $user['id']]);
            $_SESSION['user']['avatar_url'] = $relativePath;

            $response = ['success' => true, 'url' => $relativePath . '?v=' . time()];
        }
    } else {
        $response = ['success' => false, 'error' => implode('. ', $errors)];
    }
}

header('Content-Type: application/json');
echo json_encode($response);
?>
