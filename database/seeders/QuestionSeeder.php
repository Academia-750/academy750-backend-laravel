<?php

namespace Database\Seeders;

use App\Models\Question;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class QuestionSeeder extends Seeder
{
    public function run(): void
    {
        foreach ( Question::query()->cursor() as $question ) {
            $question->image()->create([
                'path' => "https://via.placeholder.com/128.webp?text={$question->question}",
                'type_path' => 'url'
            ]);
        }
    }
}
