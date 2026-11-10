<?php

namespace Tests\Feature;

use Tests\TestCase;

class ReceiptTest extends TestCase
{
    public function test_print_receipt_endpoint_returns_success_in_simulation_mode(): void
    {
        $response = $this->getJson('/print-receipt');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'mode' => 'simulation_file',
            ]);
    }

    public function test_print_receipt_custom_formatting_endpoint_returns_success(): void
    {
        $response = $this->getJson('/print-receipt/custom');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'mode' => 'simulation_file',
            ]);
    }
}
