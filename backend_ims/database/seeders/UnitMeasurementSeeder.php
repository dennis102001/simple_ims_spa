<?php

namespace Database\Seeders;

use App\Models\UnitMeasurement;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UnitMeasurementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $units = [
            ['unit_name' => 'Piece', 'description' => 'Single item or piece'],
            ['unit_name' => 'Box', 'description' => 'Box containing multiple items'],
            ['unit_name' => 'Pack', 'description' => 'Pack of items'],
            ['unit_name' => 'Set', 'description' => 'Complete set of products'],
            ['unit_name' => 'Meter', 'description' => 'Length in meters'],
            ['unit_name' => 'Kilogram', 'description' => 'Weight in kilograms'],
            ['unit_name' => 'Liter', 'description' => 'Volume in liters'],
            ['unit_name' => 'Roll', 'description' => 'Rolled product'],
            ['unit_name' => 'Pair', 'description' => 'Two items as a pair'],
            ['unit_name' => 'Dozen', 'description' => '12 items'],
        ];

        foreach ($units as $unit) {
            UnitMeasurement::create($unit);
        }
    }
}
