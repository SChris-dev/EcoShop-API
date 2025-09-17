<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class EcoShopSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@ecoshop.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Create regular user
        User::create([
            'name' => 'Regular User',
            'email' => 'user@ecoshop.com',
            'password' => Hash::make('password'),
            'role' => 'user',
        ]);

        // Create eco-friendly products
        $products = [
            [
                'name' => 'Bamboo Toothbrush',
                'description' => 'Biodegradable bamboo toothbrush with soft bristles. Eco-friendly alternative to plastic toothbrushes.',
                'price' => 5.99,
                'stock' => 100,
            ],
            [
                'name' => 'Reusable Water Bottle',
                'description' => 'Stainless steel water bottle that keeps drinks cold for 24 hours and hot for 12 hours.',
                'price' => 24.99,
                'stock' => 50,
            ],
            [
                'name' => 'Organic Cotton Tote Bag',
                'description' => 'Durable organic cotton tote bag perfect for grocery shopping and everyday use.',
                'price' => 12.99,
                'stock' => 75,
            ],
            [
                'name' => 'Solar Power Bank',
                'description' => 'Portable solar power bank with 20000mAh capacity. Charge your devices with renewable energy.',
                'price' => 39.99,
                'stock' => 30,
            ],
            [
                'name' => 'Beeswax Food Wraps',
                'description' => 'Set of 3 reusable beeswax wraps to replace plastic wrap for food storage.',
                'price' => 18.99,
                'stock' => 60,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
