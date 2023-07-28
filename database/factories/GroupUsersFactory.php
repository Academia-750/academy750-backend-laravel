<?php

namespace Database\Factories;

use App\Models\Group;
use App\Models\GroupUsers;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class GroupUsersFactory extends Factory
{
    protected $model = GroupUsers::class;

    public function definition(): array
    {

        return [
            'group_id' => Group::factory()->create()->id,
            'user_id' => User::factory()->create()->id,
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