<?php
include 'auth.php'; // Include auth.php to restrict access

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import hotels</title>
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <style>
        body {
            font-family: 'Nunito', sans-serif;
            background-color: #f4f7f6;
            margin: 0;
            padding: 20px;
            color: #333;
        }

        h2 {
            color: #27ae60;
            margin-bottom: 20px;
        }

        form {
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 500px;
            margin: auto;
        }

        input[type="file"] {
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 5px;
            width: calc(100% - 22px);
            margin-bottom: 10px;
        }

        button[type="submit"] {
            background-color: #27ae60;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
            display: block;
            width: 100%;
        }

        button[type="submit"]:hover {
            background-color: #218c4a;
        }

        .message {
            margin-top: 20px;
            padding: 10px;
            border-radius: 5px;
            color: #fff;
            width: 100%;
            max-width: 500px;
            margin: 20px auto;
        }

        .error {
            background-color: #e74c3c;
        }

        .success {
            background-color: #2ecc71;
        }
    </style>
</head>
<body>
    <h2>Import Hotels</h2>
    <form method="post" enctype="multipart/form-data">
        <input type="file" name="file" accept=".xlsx">
        <button type="submit" name="submit">Upload</button>
    </form>

    <?php
    if(isset($_POST['submit'])){
        $servername = "localhost";
        $username = "cyntzsrb_cyn";
        $password = "Qj!d$}Zh,-~m";
        $dbname = "cyntzsrb_cyn";

        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $allowedFileType = [
            'application/vnd.ms-excel',
            'text/xls',
            'text/xlsx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        ];

        if (in_array($_FILES["file"]["type"], $allowedFileType)) {

            $targetPath = 'uploads/' . $_FILES['file']['name'];
            move_uploaded_file($_FILES['file']['tmp_name'], $targetPath);

            $Reader = new Xlsx();

            $spreadSheet = $Reader->load($targetPath);
            $excelSheet = $spreadSheet->getActiveSheet();
            $spreadSheetAry = $excelSheet->toArray();
            $sheetCount = count($spreadSheetAry);

            for ($i = 1; $i <= $sheetCount; $i ++) {
                $name = "";
                if (isset($spreadSheetAry[$i][0])) {
                    $name = mysqli_real_escape_string($conn, $spreadSheetAry[$i][0]);
                    $city = mysqli_real_escape_string($conn, $spreadSheetAry[$i][1]);
                    $district= mysqli_real_escape_string($conn, $spreadSheetAry[$i][2]);

                    // Insert data into hotels table
                    $sql = "INSERT INTO hotels (name, city ,district) VALUES ('$name', '$city','$district')";
                    $conn->query($sql);
                    $id = mysqli_insert_id($conn);
                }
                
            }
        } else {
            $type = "error";
            $message = "Invalid File Type. Upload Excel File.";
        }


        // Close connection
        $conn->close();

        echo "<p>Data imported successfully!</p>";
    }
    ?>
 
</body>
</html>