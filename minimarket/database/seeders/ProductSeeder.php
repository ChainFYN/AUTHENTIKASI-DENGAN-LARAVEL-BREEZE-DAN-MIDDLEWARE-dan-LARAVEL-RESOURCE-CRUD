<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'code' => 'BRG001',
                'barcode' => '899123456001',
                'name' => 'Indomie Goreng',
                'category' => 'Makanan',
                'description' => 'Mie instan rasa goreng',
                'price' => 3500,
                'stock' => 100,
                'image' => null,
                'is_active' => true,
            ],
            [
                'code' => 'BRG002',
                'barcode' => '899123456002',
                'name' => 'Aqua 600ml',
                'category' => 'Minuman',
                'description' => 'Air mineral kemasan',
                'price' => 4000,
                'stock' => 80,
                'image' => null,
                'is_active' => true,
            ],
            [
                'code' => 'BRG003',
                'barcode' => '899123456003',
                'name' => 'Teh Botol Sosro',
                'category' => 'Minuman',
                'description' => 'Teh kemasan botol',
                'price' => 5000,
                'stock' => 50,
                'image' => null,
                'is_active' => true,
            ],
            [
                'code' => 'BRG004',
                'barcode' => '899123456004',
                'name' => 'Beras 5 Kg',
                'category' => 'Sembako',
                'description' => 'Beras premium kemasan 5 kg',
                'price' => 75000,
                'stock' => 30,
                'image' => null,
                'is_active' => true,
            ],
            [
                'code' => 'BRG005',
                'barcode' => '899123456005',
                'name' => 'Minyak Goreng 1 Liter',
                'category' => 'Sembako',
                'description' => 'Minyak goreng kemasan botol',
                'price' => 18000,
                'stock' => 40,
                'image' => null,
                'is_active' => true,
            ],
            [
                'code' => 'BRG006',
                'barcode' => '899123456006',
                'name' => 'Gula Pasir 1 Kg',
                'category' => 'Sembako',
                'description' => 'Gula pasir kemasan 1 kg',
                'price' => 17000,
                'stock' => 45,
                'image' => null,
                'is_active' => true,
            ],
        ];

        foreach ($products as $product) {
            Product::updateOrCreate(
                ['code' => $product['code']],
                $product
            );
        }
    }
}