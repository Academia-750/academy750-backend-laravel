<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;


class GroupFactory extends Factory
{


    public function definition(): array
    {
        return [
            'color' => $this->faker->hexColor(),
            'name' => $this->faker->regexify('[a-zA-Z\s_-]{5,20}'),
            'code' => $this->faker->regexify('[A-Z]{8}'),
            'created_at' => now()
        ];
    }

}