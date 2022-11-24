<?php

namespace Database\Seeders;

use App\Core\Services\UuidGeneratorService;
use App\Models\Subtopic;
use App\Models\Topic;
use Database\Seeders\trait\OppositionsHelpersTrait;
use Database\Seeders\trait\QuestionsHelpersTrait;
use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SubtopicSeeder extends Seeder
{
    use QuestionsHelpersTrait;
    use OppositionsHelpersTrait;

    public $faker;
    public function run(): void
    {
        $this->faker = Factory::create();

        foreach ( range(1, 400) as $number) {
            $subtopic = Subtopic::query()->create([
                'id' => UuidGeneratorService::getUUIDUnique(Subtopic::class),
                'name' => "Subtema {$number}",
                'is_available' => 'yes',
                'topic_id' => Topic::all()->random()->getRouteKey()
            ]);

            //$this->registerQuestionsModel($subtopic, $subtopic->name, $this->faker->text());
            $this->syncOppositions($subtopic);
        }
    }
}
