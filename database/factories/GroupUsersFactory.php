<?php

namespace Database\Factories;

use App\Models\Group;
use App\Models\GroupUsers;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class GroupUsersFactory extends Factory
{
    protected $model = GroupUsers::class;

    public function definition(): array
    {

        return [
            'group_id' => config('app.env') === 'documentation' ? 1 : Group::factory()->create()->id,
            'user_id' => config('app.env') === 'documentation' ? 2 : User::factory()->student()->create()->id,
            'discharged_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function group($group): Factory
    {
        return $this->state(function (array $attributes) use ($group) {
            return [
                'group_id' => $group->id,
            ];
        });
    }

    public function discharged(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'discharged_at' => now(),
            ];
        });
    }
}