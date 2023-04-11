<?php

namespace App\Core\Resources\Tests\Services;

use App\Models\Test;
use App\Models\TestType;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Exception;

class QuestionsTestService
{
    /**
     * Es una función que simplemente manda a llamar a los métodos correspondientes, para generar las preguntas
     * registrarlas en el historial de preguntas con su Test respectivo, y devolver las preguntas para mostrarlos al usuario
     *
     * @param int $amountQuestionsRequestedByTest
     * @param TestType $testType
     * @param User $user
     * @param Test $test
     * @return array|void|null
     */
    public static function buildQuestionsTest (int $amountQuestionsRequestedByTest, string $testType, User $user, Test $test, array $topicsSelected_id, string $opposition_id )
    {
        $TotalQuestionsGottenByAllTopicsSelected = self::getQuestionsByTestProcedure($amountQuestionsRequestedByTest, $user, $topicsSelected_id, $testType === 'card_memory', $opposition_id);

        $test->number_of_questions_generated = count($TotalQuestionsGottenByAllTopicsSelected);
        $test->save();

        // Registramos que todas las preguntas disponibles recolectadas, se registren en el Test a generar
        self::registerQuestionsHistoryByTest($TotalQuestionsGottenByAllTopicsSelected, $test, $testType, $user);

        return $TotalQuestionsGottenByAllTopicsSelected;
    }

    /**
     * Invoca el procedure correspondiente para generar las preguntas dependiendo
     * si es cuestionario o tarjeta de memoria
     *
     * @param int $amountQuestionsRequestedByTest
     * @param User $user
     * @param bool $isCardMemory
     * @param string $opposition_id
     * @return array|void
     */
    public static function getQuestionsByTestProcedure (int $amountQuestionsRequestedByTest, User $user, array $topicsSelected_id, bool $isCardMemory, string $opposition_id ) {

        try {
            //$nameProcedure = $isCardMemory ? 'get_questions_by_card_memory' : 'get_questions_by_test';
            $nameProcedure = GetQuestionsByTopicProceduresService::getNameFirstProcedure($isCardMemory);

            $questions_id = GetQuestionsByTopicProceduresService::callFirstProcedure($nameProcedure, array(implode(',',$topicsSelected_id), $opposition_id, $user->getRouteKey(), (int) $amountQuestionsRequestedByTest));

            shuffle($questions_id);

            return $questions_id;
        } catch (Exception $e) {
            abort(500, "Error Ejecutar Procedure para obtener las preguntas por cada Tema -> File: {$e->getFile()} -> Line: {$e->getLine()} -> Code: {$e->getCode()} -> Trace: {$e->getTraceAsString()} -> Message: {$e->getMessage()}");

        }
    }

    /**
     * Registra el historial de preguntas de un Test
     *
     * @param array $questions_id
     * @param Test $test
     * @param TestType $testType
     * @return void
     */
    public static function registerQuestionsHistoryByTest (array $questions_id, Test $test, string $testType, User $user): void {
        try {
            $start_time = microtime(true);

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
            $elapsed_time = microtime(true) - $start_time;
            \Log::debug("Time elapsed {$user->full_name} for QuestionsTestService::registerQuestionsHistoryByTest(): $elapsed_time seconds");
        } catch (Exception $e) {
            abort(500, "Error Registrar preguntas obtenidas del procedure -> File: {$e->getFile()} -> Line: {$e->getLine()} -> Code: {$e->getCode()} -> Trace: {$e->getTraceAsString()} -> Message: {$e->getMessage()}");
        }
    }


}
