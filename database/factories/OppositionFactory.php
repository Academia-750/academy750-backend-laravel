<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Opposition;
use Illuminate\Support\Str;


class OppositionFactory extends Factory
{
    protected $model = Opposition::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->text(50),
            'period' => "{$this->faker->date()} - {$this->faker->date()}",
            'is_visible' => 'yes'
        ];
    }
}
