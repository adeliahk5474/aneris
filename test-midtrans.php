<?php
require 'vendor/autoload.php';

\Midtrans\Config::$serverKey    = 'Mid-server-xxxxxx';
\Midtrans\Config::$isProduction = false;
\Midtrans\Config::$isSanitized  = true;
\Midtrans\Config::$is3ds        = true;
\Midtrans\Config::$curlOptions  = [
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => false,
    CURLOPT_TIMEOUT        => 60,
];

$params = [
    'transaction_details' => [
        'order_id'     => 'test-' . time(),
        'gross_amount' => 233100,
    ],
    'item_details' => [[
        'id'       => 'item1',
        'price'    => 233100,
        'quantity' => 1,
        'name'     => 'Test Commission',
    ]],
    'customer_details' => [
        'first_name' => 'Test',
        'email'      => 'test@test.com',
    ],
];

try {
    $token = \Midtrans\Snap::getSnapToken($params);
    echo "SUCCESS! Token: " . $token . "\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
