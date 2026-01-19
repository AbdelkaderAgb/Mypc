<?php

// Helper function to get currency symbol
function get_currency_symbol($code) {
    $symbols = [
        'USD' => '$',
        'EUR' => '€',
        'TRY' => '₺',
        'DZD' => 'DZD',
    ];
    return $symbols[$code] ?? $code;
}

// Helper function to generate a simple avatar with initials
function generate_initials_avatar($name) {
    $words = explode(' ', $name);
    $initials = '';
    $initials .= isset($words[0][0]) ? strtoupper($words[0][0]) : '';
    $initials .= isset($words[1][0]) ? strtoupper($words[1][0]) : '';
    if (strlen($initials) == 1 && isset($words[0][1])) {
        $initials .= strtoupper($words[0][1]);
    }
    return '<div class="avatar-initials">' . ($initials ?: 'C') . '</div>';
}
