<?php

namespace Tests\Feature;

use App\Services\MidtransService;
use Midtrans\Config;
use Tests\TestCase;

class MidtransConfigTest extends TestCase
{
    public function test_midtrans_configuration_loads_properly(): void
    {
        $service = app(MidtransService::class);
        $service->init();

        $this->assertEquals(config('midtrans.serverKey'), Config::$serverKey);
        $this->assertEquals(config('midtrans.clientKey'), Config::$clientKey);
        $this->assertEquals(config('midtrans.isProduction'), Config::$isProduction);
        $this->assertEquals(config('midtrans.isSanitized'), Config::$isSanitized);
        $this->assertEquals(config('midtrans.is3ds'), Config::$is3ds);
    }

    public function test_midtrans_signature_verification_algorithm(): void
    {
        config(['midtrans.serverKey' => 'test-server-key']);
        $service = app(MidtransService::class);

        $orderId = 'ORDER-12345';
        $statusCode = '200';
        $grossAmount = '50000.00';
        $validSignature = hash('sha512', $orderId . $statusCode . $grossAmount . 'test-server-key');

        $this->assertTrue($service->isValidSignature($orderId, $statusCode, $grossAmount, $validSignature));
        $this->assertFalse($service->isValidSignature($orderId, $statusCode, $grossAmount, 'invalid-signature'));
    }
}

