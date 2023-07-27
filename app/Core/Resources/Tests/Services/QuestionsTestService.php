<?php

namespace App\Core\Resources\Tests\Services;

use App\Models\Opposition;
use App\Models\Test;
use App\Models\TestType;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class QuestionsTestService
{
    public static function buildQuestionsTest ( array $data ): array
    {
        $TotalQuestionsGottenByAllTopicsSelected = self::getQuestionsByTestProcedure(
            $data['CountQuestionsTest'],
            $data['userAuthID'],
            $data['topics_id'],
            $data['RequestTestIsCardMemory'],
            $data['opposition_id']
        );

        // Una vez obtenemos las preguntas disponibles para este Test, contamos cuantas preguntas obtuvimos y guardamos esa información en la referencia del Test
        $data['testRecordReferenceCreated']->number_of_questions_generated = count($TotalQuestionsGottenByAllTopicsSelected);
        $data['testRecordReferenceCreated']->save();

        self::registerQuestionsHistoryByTest(
            $TotalQuestionsGottenByAllTopicsSelected,
            $data['testRecordReferenceCreated'],
            $data['RequestTestIsCardMemory']
        );

        return $TotalQuestionsGottenByAllTopicsSelected;
    }

    public static function getQuestionsByTestProcedure (int $amountQuestionsRequestedByTest, int $user_id, array $topicsSelected_id, bool $isCardMemory, int $opposition_id ): array
    {

        $nameProcedure = GetQuestionsByTopicProceduresService::getNameFirstProcedure($isCardMemory);

        $questions_id = GetQuestionsByTopicProceduresService::callFirstProcedure(
            $nameProcedure,
            array(
                implode(',',$topicsSelected_id),
                $opposition_id,
                $user_id,
                $amountQuestionsRequestedByTest
            )
        );

        shuffle($questions_id);

        return $questions_id;
    }
    public static function registerQuestionsHistoryByTest (array $questions_id, $test, bool $TestRequestedIsCardMemory): void {
        $index = 0;
        $pivotData = [];

        foreach ($questions_id as $question_id) {
            $index++;
            $pivotData[$question_id] = [
                'index' => $index,
                'have_been_show_test' => 'no',
                'have_been_show_card_memory' => $TestRequestedIsCardMemory ? 'yes' : 'no',
                'answer_id' => null,
                'status_solved_question' => 'unanswered',
            ];
        }

        $test->questions()->attach($pivotData);
    }


}
