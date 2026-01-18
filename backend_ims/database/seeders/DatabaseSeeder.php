<?php

namespace Database\Seeders;

use App\Models\Color;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Admin A. Admin',
            'email' => 'admin@example.com',
            'role' => 'admin',
            'password' => Hash::make('aaaaaaaa'),
        ]);

        User::factory()->create([
            'name' => 'Staff S. Staff',
            'email' => 'staff@example.com',
            'role' => 'user',
            'password' => Hash::make('aaaaaaaa'),
        ]);

        $this->call([
            CategorySeeder::class,
            UnitMeasurementSeeder::class,
            ItemSeeder::class,
            CustomerSeeder::class,
            SupplierSeeder::class,
            ColorSeeder::class
        ]);
    }
}
