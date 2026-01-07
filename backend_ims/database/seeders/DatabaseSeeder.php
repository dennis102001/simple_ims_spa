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
            'name' => 'Dennis D. Admin',
            'email' => 'admin@example.com',
            'role' => 'admin',
            'password' => Hash::make('aaaaaaaa'),
        ]);

        $this->call([
            CategorySeeder::class,
            ItemSeeder::class,
            UnitMeasurementSeeder::class,
            ColorSeeder::class
        ]);
    }
}
