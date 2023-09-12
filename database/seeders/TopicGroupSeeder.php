<?php


use App\Models\TopicGroup;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TopicGroupSeeder extends Seeder
{
    public function run(): void
    {
        TopicGroup::query()->create([
            'name' => 'Legislación',
            'key' => 'legislation',
            'description' => 'Temás del capítulo Legislación'
        ]);
        TopicGroup::query()->create([
            'name' => 'Genérico',
            'key' => 'generic',
            'description' => 'Temás del capítulo Genérico'
        ]);
        TopicGroup::query()->create([
            'name' => 'Específico',
            'key' => 'specific',
            'description' => 'Temás del capítulo Específico'
        ]);
    }
}