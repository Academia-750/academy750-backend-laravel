<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;


class TagFactory extends Factory
{


    public function definition(): array
    {
        return [
            'name' => $this->faker->regexify('[a-zA-Z\s_-]{5,20}'),
            'type' => $this->faker->regexify('[a-zA-Z\s_-]{5,20}'),
        ];
    }

    public function type($type): Factory
    {
        return $this->state(function (array $attributes) use ($type) {
            return [
                'type' => $type,
            ];
        });
    }

}