<?php

namespace Database\Seeders;

use App\Core\Services\UuidGeneratorService;
use App\Models\Opposition;
use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class OppositionSeeder extends Seeder
{
    /**
     * @throws \Exception
     */
    public function run(): void
    {

        $factoryInstance = Factory::create();

        $acronym = [
            "I",
            "II",
            "III",
            "IV",
            "V",
            "VI",
            "VII",
            "VIII",
            "IX",
        ];

        $oppositions = [
            [
                'name' => 'Temas de reflexion y ejes temáticos',
            ],
            [
                'name' => 'Aplicación de tácticas de enseñanza.',
            ],
            [
                'name' => 'Conocimientos básicos de informática y aplicaciones virtuales.',
            ],
            [
                'name' => 'Conocimientos de las TIC.',
            ],
            [
                'name' => 'Conocimientos de pedagogía y metodología',
            ],
            [
                'name' => 'Habilidades de comunicación.',
            ],
            [
                'name' => 'Habilidades pedagógicas.',
            ],
        ];

        foreach ($oppositions as $opposition) {

            $random_number = random_int(2,3);

            Opposition::query()->create([
                'id' => UuidGeneratorService::getUUIDUnique(Opposition::class),
                'name' => $opposition['name'],
                'period' => "202{$random_number}-EXAM-{$acronym[random_int(0,count($acronym) - 1)]}-{$factoryInstance->numerify('####')}"
            ]);
        }
    }
}
