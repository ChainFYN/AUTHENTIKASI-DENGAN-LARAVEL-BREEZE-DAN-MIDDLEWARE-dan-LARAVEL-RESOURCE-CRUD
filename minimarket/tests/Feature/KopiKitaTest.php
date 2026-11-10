<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KopiKitaTest extends TestCase
{
    use RefreshDatabase;

    public function test_kopi_kita_print_sample_returns_success_and_valid_structure(): void
    {
        $response = $this->getJson('/kopi-kita/print');

        $response->assertStatus(200)
            ->assertJson([
                'study_case' => 'Acara 36 - Toko Kopi Kita',
                'result' => [
                    'success' => true,
                    'mode' => 'simulation_file',
                ],
                'order_summary' => [
                    'toko' => 'Kopi Kita',
                    'discount' => 5000,
                ],
            ]);
    }

    public function test_kopi_kita_checkout_and_print_endpoint(): void
    {
        $payload = [
            'cashier' => 'Siti Nurhaliza',
            'items' => [
                ['name' => 'Matcha Latte', 'qty' => 1, 'price' => 22000],
                ['name' => 'Roti Bakar Keju', 'qty' => 1, 'price' => 15000],
            ],
            'discount' => 2000,
            'paid_amount' => 50000,
        ];

        $response = $this->postJson('/kopi-kita/checkout', $payload);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'print_details' => [
                    'success' => true,
                    'mode' => 'simulation_file',
                ],
            ]);
    }

    public function test_pos_transaction_store_triggers_automatic_receipt_printing(): void
    {
        $user = User::factory()->create(['role' => 'kasir']);
        $product = Product::create([
            'code' => 'PRD-KK-01',
            'name' => 'Kopi Tubruk',
            'price' => 8000,
            'stock' => 10,
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->postJson('/transactions', [
            'items' => [
                ['product_id' => $product->id, 'qty' => 2],
            ],
            'discount_amount' => 1000,
            'paid_amount' => 20000,
            'payment_method' => 'Tunai',
            'auto_print' => true,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'receipt' => [
                    'success' => true,
                    'mode' => 'simulation_file',
                ],
            ]);
    }
}
