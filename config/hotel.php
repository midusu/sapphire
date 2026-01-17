<?php

return [
    'booking' => [
        'require_deposit' => env('BOOKING_REQUIRE_DEPOSIT', true),
        'deposit_percentage' => env('BOOKING_DEPOSIT_PERCENTAGE', 20),
        'payment_capture_method' => env('PAYMENT_CAPTURE_METHOD', 'automatic'), // 'automatic' or 'manual'
    ],
];
