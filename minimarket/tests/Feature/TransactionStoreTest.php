<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionStoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_calculates_transaction_totals_and_decrements_stock(): void
    {
        $user = User::factory()->create(['role' => 'kasir']);
        $product = Product::create([
            'code' => 'PRD-001',
            'name' => 'Produk',
            'price' => 10000,
            'stock' => 5,
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->postJson('/transactions', [
            'items' => [
                ['product_id' => $product->id, 'qty' => 1],
                ['product_id' => $product->id, 'qty' => 1],
            ],
            'discount_percent' => 10,
            'discount_amount' => 500,
            'tax' => 1000,
            'other_fee' => 500,
            'paid_amount' => 20000,
            'payment_method' => 'Tunai',
            'subtotal' => 1,
            'grand_total' => 1,
            'change_amount' => 0,
        ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'grand_total' => '19000.00',
                'change_amount' => '1000.00',
            ]);

        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'subtotal' => 20000,
            'discount' => 2500,
            'grand_total' => 19000,
        ]);
        $this->assertDatabaseHas('transaction_details', [
            'transaction_id' => Transaction::first()->id,
            'product_id' => $product->id,
            'qty' => 2,
            'subtotal' => 20000,
        ]);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 3,
        ]);
    }

    public function test_it_rejects_insufficient_stock_without_creating_a_transaction(): void
    {
        $user = User::factory()->create(['role' => 'kasir']);
        $product = Product::create([
            'name' => 'Produk Stok Terbatas',
            'price' => 10000,
            'stock' => 1,
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->postJson('/transactions', [
            'items' => [['product_id' => $product->id, 'qty' => 2]],
            'paid_amount' => 20000,
            'payment_method' => 'Tunai',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('items');
        $this->assertDatabaseCount('transactions', 0);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 1,
        ]);
    }

    public function test_it_rejects_payment_below_the_server_calculated_total(): void
    {
        $user = User::factory()->create(['role' => 'kasir']);
        $product = Product::create([
            'name' => 'Produk',
            'price' => 10000,
            'stock' => 1,
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->postJson('/transactions', [
            'items' => [['product_id' => $product->id, 'qty' => 1]],
            'paid_amount' => 1,
            'payment_method' => 'Tunai',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('paid_amount');
        $this->assertDatabaseCount('transactions', 0);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 1,
        ]);
    }
}
