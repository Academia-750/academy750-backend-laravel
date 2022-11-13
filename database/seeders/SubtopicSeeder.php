<?php

namespace Database\Seeders;

use App\Core\Services\UuidGeneratorService;
use App\Models\Subtopic;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SubtopicSeeder extends Seeder
{
    public function run(): void
    {
        foreach ( range(1, 180) as $number) {
            Subtopic::query()->create([
                'id' => UuidGeneratorService::getUUIDUnique(Subtopic::class),
                'name' => "Subtema {$number}",
                'is_available' => 'yes'
            ]);
        }
    }
}
