<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ColorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('colors')->insert([
            ['color_name' => 'red', 'isSelectedForNo' => true, 'isSelectedForLow' => false], 
            ['color_name' => 'orange', 'isSelectedForNo' => false, 'isSelectedForLow' => true],
            ['color_name' => 'yellow', 'isSelectedForNo' => false, 'isSelectedForLow' => false],
            ['color_name' => 'green', 'isSelectedForNo' => false, 'isSelectedForLow' => false],
            ['color_name' => 'blue', 'isSelectedForNo' => false, 'isSelectedForLow' => false],
            ['color_name' => 'violet', 'isSelectedForNo' => false, 'isSelectedForLow' => false],
            ['color_name' => 'pink', 'isSelectedForNo' => false, 'isSelectedForLow' => false],
            ['color_name' => 'gray', 'isSelectedForNo' => false, 'isSelectedForLow' => false],
        ]);
    }
}
