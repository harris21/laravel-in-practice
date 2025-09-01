<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement([
                'Laravel T-Shirt',
                'PHP Mug',
                'Code Editor License',
                'Database Course',
                'API Masterclass',
                'DevOps Toolkit',
                'Testing Guide',
                'Performance Book'
            ]),
            'description' => fake()->sentence(),
            'price' => fake()->randomFloat(2, 10, 500),
            'stock' => fake()->numberBetween(0, 100),
        ];
    }
}
