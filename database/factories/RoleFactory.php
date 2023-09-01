<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;


class RoleFactory extends Factory
{


    public function definition(): array
    {

        return [
            'name' => $this->faker->regexify('[a-zA-Z\s_-]{5,20}'),
            'alias_name' => $this->faker->regexify('[a-zA-Z\s_-]{5,20}'),
            'default_role' => false,
            'created_at' => now(),
            'updated_at' => now(),

        ];
    }

}