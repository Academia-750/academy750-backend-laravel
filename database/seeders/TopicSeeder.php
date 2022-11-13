<?php

namespace Database\Seeders;

use App\Core\Services\UuidGeneratorService;
use App\Models\Topic;
use App\Models\TopicGroup;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TopicSeeder extends Seeder
{
    public function run(): void
    {
        foreach ( range(0,100) as $number) {
            Topic::query()->create([
                'id' => UuidGeneratorService::getUUIDUnique(Topic::class),
                'name' => "Topic {$number}",
                'topic_group_id' => TopicGroup::all()->random()->getRouteKey(),
                'is_available' => 'yes'
            ]);
        }
    }
}
