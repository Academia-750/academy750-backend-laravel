<?php

namespace Database\Factories;

use App\Models\Role;
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

    /**
     * Only 1 can be the default role
     */
    public function defaultRole(): Factory
    {
        return $this->state(function (array $attributes) {
            return [];
        })->afterCreating(function (Role $role) {
            Role::where('default_role', true)->update(['default_role' => false]);
            $role->default_role = true;
            $role->save();
        });
    }

}