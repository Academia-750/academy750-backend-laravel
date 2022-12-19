<?php

namespace App\Core\Resources\Tests\Services;

use App\Models\QuestionaryQuestion;
use App\Models\Test;
use Illuminate\Support\Facades\DB;

class QuestionsTestService
{
    public static function buildQuestionsTest ( array $topicsSelected, $amountQuestionsRequestedByTest ) {
        $numberOfSelectedTopicsForTest = count($topicsSelected);
        $numberOfQuestionsPerTopic = $amountQuestionsRequestedByTest / $numberOfSelectedTopicsForTest;
        $totalNumbersOfQuestionsForQuiz = $amountQuestionsRequestedByTest;

        if ($numberOfQuestionsPerTopic > 0) {
            foreach ( $topicsSelected as $topic_id ) {

            }
        }
    }

    public static function checkAvailableQuestions () {

    }

    public static function resetQuestionAvailability () {

    }

    public static function registerQuestionsHistoryByTest (array $topicsData, Test $test) {
        try {
            DB::beginTransaction();
            foreach ($topicsData as $topic) {
                foreach ($topic['questions'] as $question) {
                    $test->questions()->attach($question, [
                        'have_been_show_test' => 'no',
                        'have_been_show_card_memory' => 'no',
                        'answer_id' => null,
                        'status_solved_test' => 'unanswered'
                    ]);
                }
            }
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            abort(500, $th->getMessage());
        }
    }


}
