<?php

namespace Database\Seeders;

use App\Core\Services\UuidGeneratorService;
use App\Models\Topic;
use App\Models\TopicGroup;
use Database\Seeders\trait\OppositionsHelpersTrait;
use Database\Seeders\trait\QuestionsHelpersTrait;
use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TopicSeeder extends Seeder
{
    use QuestionsHelpersTrait;
    use OppositionsHelpersTrait;

    public $faker;

    public function run(): void
    {
        $this->faker = Factory::create();

        foreach ( range(1,25) as $number) {
            $topic = Topic::query()->create([
                'name' => "Topic {$number}",
                'topic_group_id' => TopicGroup::all()->random()->getRouteKey(),
                'is_available' => 'yes'
            ]);

            $this->registerQuestionsModel($topic, $topic->name, $this->faker->text());
            //$this->syncOppositions($topic);
        }
    }


}
