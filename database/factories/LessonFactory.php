<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;


class LessonFactory extends Factory
{


    public function definition(): array
    {
        return [
            'name' => $this->faker->regexify('[a-zA-Z\s_-]{5,20}'),
            // 'description' => $this->faker->lor('[a-zA-Z\s_-]{5,20}'), TODO: Lorem ipsum
            'date' => now()->add($this->faker->randomDigit(), 'days')->startOf('day')->toISOString(),
            'start_time' => now()->add(3, 'hours')->format('hh:mm'),
            'end_time' => now()->add(3, 'hours')->format('hh:mm'),
            'is_online' => false,
            'is_active' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function active(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'active' => true,
            ];
        });
    }
}