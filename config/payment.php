<?php

return [
    // Bank transfer configuration
    'bank_name' => env('BANK_NAME', 'Vietcombank'),
    'account_number' => env('BANK_ACCOUNT_NUMBER', '1234567890'),
    'account_name' => env('BANK_ACCOUNT_NAME', 'GAME MU ONLINE'),
    // Relative path under public/ for static QR image (fallback)
    'qr_image' => env('BANK_QR_IMAGE', 'images/qr.png'),
    // EMV QR merchant info
    'merchant_id' => env('MERCHANT_ID', '123456789012345678'), // Globally Unique Identifier for bank QR
    'merchant_city' => env('MERCHANT_CITY', 'HANOI'),
    'currency' => env('CURRENCY', '704'), // ISO 4217 numeric for VND
];
