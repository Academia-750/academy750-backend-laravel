<?php

namespace App\Core\Resources\Tests\Services;

use App\Models\Opposition;
use App\Models\Test;
use App\Models\TestType;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class QuestionsTestService
{
    public static function buildQuestionsTest (int $amountQuestionsRequestedByTest, string $testType, User $user, Test $test, array $topicsSelected_id, string $opposition_id )
    {
        $TotalQuestionsGottenByAllTopicsSelected = self::getQuestionsByTestProcedure($amountQuestionsRequestedByTest, $user, $topicsSelected_id, $testType === 'card_memory', $opposition_id);

        $test->number_of_questions_generated = count($TotalQuestionsGottenByAllTopicsSelected);
        $test->save();

        self::registerQuestionsHistoryByTest($TotalQuestionsGottenByAllTopicsSelected, $test, $testType);

        return $TotalQuestionsGottenByAllTopicsSelected;
    }

    public static function getQuestionsByTestProcedure (int $amountQuestionsRequestedByTest, $user, array $topicsSelected_id, bool $isCardMemory, string $opposition_id ) {

        $nameProcedure = GetQuestionsByTopicProceduresService::getNameFirstProcedure($isCardMemory);

        $questions_id = GetQuestionsByTopicProceduresService::callFirstProcedure(
            $nameProcedure,
            array(
                implode(',',$topicsSelected_id),
                Opposition::query()->firstWhere('uuid', $opposition_id)?->getKey(),
                $user->getKey(),
                $amountQuestionsRequestedByTest
            )
        );

        //$questions_id = [];

        //$start_time__shuffle_questions = microtime(true);
        //Log::debug("+++Aqui se ejecuta el proceso de desordenar las preguntas mapeadas que ya son compatibles en este momento con el Backend PHP del alumno: {$user?->full_name} con id {$user?->id}");
        shuffle($questions_id);
        //$elapsed_time__shuffle_questions = microtime(true) - $start_time__shuffle_questions;
        //Log::debug("---Aqui se termina el proceso de desordenar las preguntas mapeadas que ya son compatibles en este momento con el Backend PHP del alumno: {$user?->full_name} con id {$user?->id} el cuál ha tardado: {$elapsed_time__shuffle_questions} segundos");

        return $questions_id;
    }
    public static function registerQuestionsHistoryByTest (array $questions_id, Test $test, string $testType): void {
        $index = 0;
        $pivotData = [];

        foreach ($questions_id as $question_id) {
            $index++;
            $pivotData[$question_id] = [
                'index' => $index,
                'have_been_show_test' => 'no',
                'have_been_show_card_memory' => $testType === 'card_memory' ? 'yes' : 'no',
                'answer_id' => null,
                'status_solved_question' => 'unanswered',
            ];
        }

        $test->questions()->attach($pivotData);

    }


}
