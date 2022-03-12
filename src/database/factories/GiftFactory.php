<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class GiftFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'stock' => 100,
            'point' => 10,
            'rating' => 0,
            'image' => $this->faker->imageUrl($width = 200, $height = 200),
        ];
    }
}
