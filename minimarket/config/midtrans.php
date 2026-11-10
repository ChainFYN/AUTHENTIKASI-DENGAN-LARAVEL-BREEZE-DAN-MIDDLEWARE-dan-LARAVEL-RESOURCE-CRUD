<?php

return [
    'serverKey' => env('MIDTRANS_SERVER_KEY'),
    'clientKey' => env('MIDTRANS_CLIENT_KEY'),
    'isProduction' => (bool) env('MIDTRANS_IS_PRODUCTION', false),
    'isSanitized' => (bool) env('MIDTRANS_SANITIZE', true),
    'is3ds' => (bool) env('MIDTRANS_3DS', true),
];

