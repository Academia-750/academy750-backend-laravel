<?php

namespace Database\Seeders;

use App\Models\TopicGroup;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TopicGroupSeeder extends Seeder
{
    public function run(): void
    {
        TopicGroup::query()->create([
            'name' => 'Legislación',
            'description' => 'Temás del capítulo Legislación'
        ]);
        TopicGroup::query()->create([
            'name' => 'Genérico',
            'description' => 'Temás del capítulo Genérico'
        ]);
        TopicGroup::query()->create([
            'name' => 'Específico',
            'description' => 'Temás del capítulo Específico'
        ]);
    }
}
