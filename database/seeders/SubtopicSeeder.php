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

        foreach ( Topic::query()->cursor() as $topic) {
            $topic = Topic::all()->random();
            foreach ( range(1,random_int(2,4)) as $number ) {
                $subtopic = $topic->subtopics()->create([
                    'name' => "Subtema {$number} - {$topic->name}",
                    'is_available' => 'yes',
                    'topic_id' => $topic->getRouteKey()
                ]);

                //$this->syncOppositions($subtopic);
                $this->registerQuestionsModel($subtopic, $subtopic->name, $this->faker->text());
            }

        }
    }
}
