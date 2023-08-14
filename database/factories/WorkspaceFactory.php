<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;


class WorkspaceFactory extends Factory
{


    public function definition(): array
    {
        return [
            'name' => $this->faker->regexify('[a-zA-Z\s_-]{5,20}'),
            'type' => 'default',
            'created_at' => now(),
            'updated_at' => now(),

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