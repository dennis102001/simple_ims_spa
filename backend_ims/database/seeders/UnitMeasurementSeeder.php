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
        UnitMeasurement::factory()->count(10)->create();
    }
}
