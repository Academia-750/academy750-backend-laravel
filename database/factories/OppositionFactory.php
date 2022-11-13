<?php

namespace Database\Factories;

use App\Core\Services\UuidGeneratorService;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Opposition;
use Illuminate\Support\Str;


class OppositionFactory extends Factory
{
    protected $model = Opposition::class;

    /**
     * @throws \Exception
     */
    public function definition(): array
    {
        $nameOpposition = $this->faker->text(40);
        $random_number = random_int(2,3);

        return [
            'id' => UuidGeneratorService::getUUIDUnique(Opposition::class),
            'name' => $nameOpposition,
            'period' => "202{$random_number}-EXAM-{$nameOpposition}-{$this->faker->numerify('####')}",
            'is_available' => 'yes'
        ];
    }
}
