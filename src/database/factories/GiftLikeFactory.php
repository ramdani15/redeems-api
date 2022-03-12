<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class GiftLikeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'gift_id' => $this->faker->numberBetween(1, 10),
            'user_id' => $this->faker->numberBetween(2, 3),
        ];
    }
}
