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

        $topic = Topic::query()->create([
            'name' => "Hidráulica",
            'topic_group_id' => TopicGroup::query()->firstWhere('key', '=', 'generic')?->getRouteKey() /*TopicGroup::all()->random()->getRouteKey()*/,
            'is_available' => 'yes'
        ]);

        /*foreach ( range(1,4) as $number) {
            $topic = Topic::query()->create([
                'name' => "Topic {$number}",
                'topic_group_id' => TopicGroup::all()->random()->getRouteKey(),
                'is_available' => 'yes'
            ]);
            //$this->syncOppositions($topic);
            //$this->registerQuestionsModel($topic, $topic->name, $this->faker->text(), 'topic');
        }*/

        $topic->refresh();

        $topic->id = '2368f180-4e30-4dc9-ac56-c0be2d43ee0a';

        $topic->save();

        $topicExample = Topic::query()->create([
            //'id' => '2368f180-4e30-4dc9-ac56-c0be2d43ee0a',
            'name' => "Incendios Forestales",
            'topic_group_id' => TopicGroup::query()->firstWhere('key', 'specific')?->getRouteKey() /*TopicGroup::all()->random()->getRouteKey()*/,
            'is_available' => 'yes'
        ]);

        $topicExample->refresh();

        $topicExample->id = 'e5f6b510-f351-4a0e-9302-905f082c8d08';

        $topicExample->save();


    }


}
