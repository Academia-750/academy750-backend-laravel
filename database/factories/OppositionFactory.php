<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Opposition;
use Illuminate\Support\Str;
use Faker\Provider\en_US\Company;

class OppositionFactory extends Factory
{
    protected $model = Opposition::class;

    public function definition(): array
    {
        return [
            'name' => Company::catchPhrase(),
            'period' => "{$this->faker->date()} - {$this->faker->date()}",
            'is_visible' => 'yes'
        ];
    }
}
