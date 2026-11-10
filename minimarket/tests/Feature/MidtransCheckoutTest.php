<?php

namespace Tests\Feature;

use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MidtransCheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_digital_products_page_renders_successfully(): void
    {
        $response = $this->get('/digital-products');
        $response->assertStatus(200)
            ->assertSee('Payment Gateway Midtrans');
    }

    public function test_checkout_endpoint_creates_order_and_returns_snap_token(): void
    {
        $payload = [
            'product_id' => 'DIG-01',
            'product_name' => 'E-Book Laravel Testing',
            'price' => 50000,
            'customer_name' => 'John Doe',
            'customer_email' => 'john@example.com',
        ];

        $response = $this->postJson('/checkout', $payload);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'snap_token',
                'order_id',
            ]);

        $orderId = $response->json('order_id');
        $this->assertDatabaseHas('orders', [
            'order_id' => $orderId,
            'status' => 'pending',
            'customer_name' => 'John Doe',
            'gross_amount' => 50000,
        ]);
    }

    public function test_midtrans_notification_webhook_settlement_updates_order_to_paid(): void
    {
        $order = Order::create([
            'order_id' => 'ORDER-TEST-001',
            'status' => 'pending',
            'gross_amount' => 50000,
            'customer_name' => 'John Doe',
            'customer_email' => 'john@example.com',
        ]);

        $webhookPayload = [
            'order_id' => $order->order_id,
            'transaction_status' => 'settlement',
            'payment_type' => 'qris',
            'fraud_status' => 'accept',
        ];

        // Webhook tidak memerlukan CSRF token karena sudah di-exempt di bootstrap/app.php
        $response = $this->postJson('/midtrans/notification', $webhookPayload);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Notification handled',
                'order_id' => $order->order_id,
                'status' => 'PAID',
            ]);

        $this->assertDatabaseHas('orders', [
            'order_id' => $order->order_id,
            'status' => 'PAID',
            'payment_type' => 'qris',
        ]);
    }

    public function test_midtrans_notification_webhook_expire_updates_order_to_failed(): void
    {
        $order = Order::create([
            'order_id' => 'ORDER-TEST-002',
            'status' => 'pending',
            'gross_amount' => 75000,
        ]);

        $webhookPayload = [
            'order_id' => $order->order_id,
            'transaction_status' => 'expire',
            'payment_type' => 'bank_transfer',
        ];

        $response = $this->postJson('/midtrans/notification', $webhookPayload);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'FAILED',
            ]);

        $this->assertDatabaseHas('orders', [
            'order_id' => $order->order_id,
            'status' => 'FAILED',
        ]);
    }

    public function test_order_status_check_endpoint(): void
    {
        $order = Order::create([
            'order_id' => 'ORDER-TEST-003',
            'status' => 'PAID',
            'gross_amount' => 120000,
            'customer_name' => 'Jane Doe',
        ]);

        $response = $this->getJson("/orders/{$order->order_id}/status");

        $response->assertStatus(200)
            ->assertJson([
                'order_id' => $order->order_id,
                'status' => 'PAID',
                'customer_name' => 'Jane Doe',
            ]);
    }
}
