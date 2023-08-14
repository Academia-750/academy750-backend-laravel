<?php

namespace App\Core\Resources\Tests\Services;

use App\Models\Opposition;
use App\Models\Test;
use App\Models\TestType;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class QuestionsTestService
{
    public static function buildQuestionsTest(array $data): array
    {
        \Log::debug("-------Aquí ejecutamos SP de obtener preguntas, le pasamos toda la data");

        $TotalQuestionsGottenByAllTopicsSelected = self::getQuestionsByTestProcedure(
            $data['CountQuestionsTest'],
            $data['userAuthID'],
            $data['topics_id'],
            $data['RequestTestIsCardMemory'],
            $data['opposition_id']
        );

        // Una vez obtenemos las preguntas disponibles para este Test, contamos cuantas preguntas obtuvimos y guardamos esa información en la referencia del Test
        \Log::debug("-------Una vez obtenemos las preguntas disponibles para este Test, contamos cuantas preguntas obtuvimos y guardamos esa información en la referencia del Test");
        $data['testRecordReferenceCreated']->number_of_questions_generated = count($TotalQuestionsGottenByAllTopicsSelected);
        $data['testRecordReferenceCreated']->save();

        \Log::debug("-------Registramos las preguntas en el question_test tabla");
        self::registerQuestionsHistoryByTest(
            $TotalQuestionsGottenByAllTopicsSelected,
            $data['testRecordReferenceCreated'],
            $data['RequestTestIsCardMemory']
        );

        \Log::debug("-------Justo aquí ya termina todo el proceso");
        return $TotalQuestionsGottenByAllTopicsSelected;
    }

    public static function getQuestionsByTestProcedure(int $amountQuestionsRequestedByTest, int $user_id, array $topicsSelected_id, bool $isCardMemory, int $opposition_id): array
    {
        $nameProcedure = GetQuestionsByTopicProceduresService::getNameFirstProcedure($isCardMemory);

        $topics__id = implode(',', $topicsSelected_id);
        \Log::debug("----- Aquí imprimo la data que le pasaremos al SP de obtener preguntas");
        \Log::debug("nombre procedure: {$nameProcedure}");
        \Log::debug("topics id: {$topics__id}");
        \Log::debug("Oposicion id: {$opposition_id}");
        \Log::debug("usuario id: {$user_id}");
        \Log::debug("numero preguntas: {$amountQuestionsRequestedByTest}");

        $questions_id = GetQuestionsByTopicProceduresService::callFirstProcedure(
            $nameProcedure,
            array(
                implode(',', $topicsSelected_id),
                $opposition_id,
                $user_id,
                $amountQuestionsRequestedByTest
            )
        );

        \Log::debug("Aquí ya termino el SP, ahora cambiamos el orden de las preguntas");
        shuffle($questions_id);

        return $questions_id;
    }
    public static function registerQuestionsHistoryByTest(array $questions_id, $test, bool $TestRequestedIsCardMemory): void
    {
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