<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Item>
 */
class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'item_name' => fake()->realText(10),
            'description' => fake()->realText(20),
            'sku' => fake()->realText(10),
            'price' => fake()->randomFloat(2, 51, 100),
            'cost' => fake()->randomFloat(2, 1, 50)
        ];
    }
}
