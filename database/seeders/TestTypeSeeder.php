<?php

namespace Database\Seeders;

use App\Models\TestType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TestTypeSeeder extends Seeder
{
    public function run(): void
    {
        TestType::query()->create([
            'name' => 'test',
            'alias_name' => 'Examen'
        ]);
        TestType::query()->create([
            'name' => 'card-memory',
            'alias_name' => 'Tarjeta de memoria'
        ]);
    }
}