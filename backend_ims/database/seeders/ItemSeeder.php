<?php

namespace Database\Seeders;

use App\Models\Item;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            [
                'item_name' => 'Wireless Mouse',
                'description' => '2.4GHz ergonomic wireless mouse',
                'sku' => 'TECH-MOUSE-001',
                'quantity' => 50,
                'price' => 599.00,
                'cost' => 420.00,
                'reorder_level' => 10,
                'category_id' => 1,
                'unit_id' => 1,
            ],
            [
                'item_name' => 'Mechanical Keyboard',
                'description' => 'RGB mechanical keyboard with blue switches',
                'sku' => 'TECH-KB-002',
                'quantity' => 30,
                'price' => 2999.00,
                'cost' => 2200.00,
                'reorder_level' => 5,
                'category_id' => 1,
                'unit_id' => 1,
            ],
            [
                'item_name' => 'USB-C Charger 65W',
                'description' => 'Fast charging USB-C power adapter',
                'sku' => 'TECH-CHR-003',
                'quantity' => 40,
                'price' => 1899.00,
                'cost' => 1350.00,
                'reorder_level' => 8,
                'category_id' => 2,
                'unit_id' => 1,
            ],
            [
                'item_name' => 'External SSD 1TB',
                'description' => 'Portable high-speed SSD storage',
                'sku' => 'TECH-SSD-004',
                'quantity' => 20,
                'price' => 6999.00,
                'cost' => 5600.00,
                'reorder_level' => 5,
                'category_id' => 3,
                'unit_id' => 1,
            ],
            [
                'item_name' => 'Webcam 1080p',
                'description' => 'Full HD USB webcam with microphone',
                'sku' => 'TECH-CAM-005',
                'quantity' => 25,
                'price' => 2499.00,
                'cost' => 1800.00,
                'reorder_level' => 5,
                'category_id' => 1,
                'unit_id' => 1,
            ],
            [
                'item_name' => 'HDMI Cable 2m',
                'description' => 'High-speed HDMI cable',
                'sku' => 'TECH-HDMI-006',
                'quantity' => 100,
                'price' => 299.00,
                'cost' => 180.00,
                'reorder_level' => 20,
                'category_id' => 4,
                'unit_id' => 1,
            ],
            [
                'item_name' => 'Laptop Stand',
                'description' => 'Adjustable aluminum laptop stand',
                'sku' => 'TECH-STAND-007',
                'quantity' => 35,
                'price' => 1499.00,
                'cost' => 1000.00,
                'reorder_level' => 7,
                'category_id' => 1,
                'unit_id' => 1,
            ],
            [
                'item_name' => 'Noise Cancelling Headset',
                'description' => 'Over-ear headset with ANC',
                'sku' => 'TECH-HSET-008',
                'quantity' => 15,
                'price' => 4999.00,
                'cost' => 3800.00,
                'reorder_level' => 3,
                'category_id' => 1,
                'unit_id' => 1,
            ],
            [
                'item_name' => 'USB Flash Drive 64GB',
                'description' => 'USB 3.0 flash drive',
                'sku' => 'TECH-USB-009',
                'quantity' => 80,
                'price' => 499.00,
                'cost' => 300.00,
                'reorder_level' => 15,
                'category_id' => 3,
                'unit_id' => 1,
            ],
            [
                'item_name' => 'Wi-Fi Router',
                'description' => 'Dual-band wireless router',
                'sku' => 'TECH-ROUTER-010',
                'quantity' => 18,
                'price' => 3599.00,
                'cost' => 2800.00,
                'reorder_level' => 4,
                'category_id' => 2,
                'unit_id' => 1,
            ],
        ];

        foreach ($items as $item) {
            Item::create($item);
        }
    }
}
