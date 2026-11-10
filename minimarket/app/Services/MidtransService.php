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

    /**
     * Inisialisasi konfigurasi Midtrans SDK dari config/midtrans.php
     * Sesuai Acara 37 Hal 14 - 15
     */
    public function init(): void
    {
        Config::$serverKey = config('midtrans.serverKey');
        Config::$clientKey = config('midtrans.clientKey');
        Config::$isProduction = (bool) config('midtrans.isProduction');
        Config::$isSanitized = (bool) config('midtrans.isSanitized');
        Config::$is3ds = (bool) config('midtrans.is3ds');
    }

    /**
     * Membuat Snap Token untuk transaksi
     */
    public function createSnapToken(array $params): string
    {
        $this->init();
        return Snap::getSnapToken($params);
    }

    /**
     * Verifikasi signature hash Midtrans webhook
     * Format SHA512: sha512(order_id + status_code + gross_amount + server_key)
     */
    public function isValidSignature(string $orderId, string $statusCode, string $grossAmount, string $signatureKey): bool
    {
        $serverKey = config('midtrans.serverKey');
        $expected = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);
        return hash_equals($expected, $signatureKey);
    }
}

