<?php

namespace App\Services;

use Midtrans\Config;
use Midtrans\Snap;

class MidtransService
{
    public function __construct()
    {
        $this->init();
    }


    public function init(): void
    {
        Config::$serverKey = config('midtrans.serverKey');
        Config::$clientKey = config('midtrans.clientKey');
        Config::$isProduction = (bool) config('midtrans.isProduction');
        Config::$isSanitized = (bool) config('midtrans.isSanitized');
        Config::$is3ds = (bool) config('midtrans.is3ds');
        Config::$curlOptions = [
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER => [],
        ];
    }


    public function createSnapToken(array $params): string
    {
        $this->init();
        return Snap::getSnapToken($params);
    }


    public function isValidSignature(string $orderId, string $statusCode, string $grossAmount, string $signatureKey): bool
    {
        $serverKey = config('midtrans.serverKey');
        $expected = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);
        return hash_equals($expected, $signatureKey);
    }
}
