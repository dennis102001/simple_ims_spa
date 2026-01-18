<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['category_name' => 'Accessories', 'description' => 'Tech accessories like mouse, keyboard, etc.'],
            ['category_name' => 'Cables & Chargers', 'description' => 'Cables, chargers, and adapters'],
            ['category_name' => 'Storage Devices', 'description' => 'SSDs, flash drives, external storage'],
            ['category_name' => 'Networking', 'description' => 'Routers, switches, Wi-Fi devices'],
            ['category_name' => 'Audio Devices', 'description' => 'Headsets, speakers, microphones'],
            ['category_name' => 'Displays', 'description' => 'Monitors and screens'],
            ['category_name' => 'Computing Devices', 'description' => 'Laptops, desktops, tablets'],
            ['category_name' => 'Office Equipment', 'description' => 'Printers, scanners, office gadgets'],
            ['category_name' => 'Power Supply', 'description' => 'Power banks, batteries, adapters'],
            ['category_name' => 'Furniture', 'description' => 'Laptop stands, desks, chairs'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
