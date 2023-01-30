<?php

namespace Database\Seeders;

use App\Models\Question;
use App\Models\Topic;
use Database\Seeders\trait\QuestionsHelpersTrait;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class QuestionSeeder extends Seeder
{

    use QuestionsHelpersTrait;
    public function run(): void
    {
        /*foreach ( Question::query()->cursor() as $question ) {
            $question->image()->create([
                'path' => "https://via.placeholder.com/128.webp?text=Question+{$question->getRouteKey()}",
                'type_path' => 'url'
            ]);
        }*/


        $hidraulica = Topic::query()->firstWhere('id', '2368f180-4e30-4dc9-ac56-c0be2d43ee0a');

        if ($hidraulica) {
            $this->registerQuestionsModel($hidraulica, " Explicacion..." , 'yes', 'no', 3, '(TIPO TEST)');
            $this->registerQuestionsModel($hidraulica, " Explicacion..." , 'no', 'yes', 3, '(TIPO MEMORIA)');
            $this->registerQuestionsModel($hidraulica, " Explicacion..." , 'yes', 'yes', 3, '(TIPO MEMORIA y TEST)');
        }

        $incendiosForestales = Topic::query()->firstWhere('id', 'e5f6b510-f351-4a0e-9302-905f082c8d08');

        if ($incendiosForestales) {
            $this->registerQuestionsModel($incendiosForestales, " Explicacion..." , 'yes', 'no', 20, '(TIPO TEST)');
            $this->registerQuestionsModel($incendiosForestales, " Explicacion..." , 'no', 'yes', 20, '(TIPO MEMORIA)');
            $this->registerQuestionsModel($incendiosForestales, " Explicacion..." , 'yes', 'yes', 20, '(TIPO MEMORIA y TEST)');
        }
    }
}
