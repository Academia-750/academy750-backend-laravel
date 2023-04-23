<?php

namespace App\Core\Resources\Tests\Services;

use App\Models\Test;
use App\Models\TestType;
use App\Models\User;
use Illuminate\Support\Facades\Log;
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

        $start_time__update_field_number_of_questions_generated_test = microtime(true);
        Log::debug("+++Aqui se ejecuta el proceso de actualizar el campo 'number_of_questions_generated' el cuál significa cuantas preguntas de forma precisa tendrá este test del alumno: {$user?->full_name} con id {$user?->id}");

        $test->number_of_questions_generated = count($TotalQuestionsGottenByAllTopicsSelected);
        $test->save();

        $elapsed_time__update_field_number_of_questions_generated_test = microtime(true) - $start_time__update_field_number_of_questions_generated_test;
        Log::debug("---Aqui se termina el proceso de actualizar el campo 'number_of_questions_generated' el cuál significa cuantas preguntas de forma precisa tendrá este test del alumno: {$user?->full_name} con id {$user?->id} el cuál ha tardado: {$elapsed_time__update_field_number_of_questions_generated_test} segundos");

        // Registramos que todas las preguntas disponibles recolectadas, se registren en el Test a generar

        $start_time__registerQuestionsHistoryByTest = microtime(true);
        Log::debug("+++Aqui se ejecuta el proceso de registrar las preguntas en la tabla 'question_test' para relacionar cada test con sus respectivas preguntas del alumno: {$user?->full_name} con id {$user?->id}");
        self::registerQuestionsHistoryByTest($TotalQuestionsGottenByAllTopicsSelected, $test, $testType, $user);
        $elapsed_time__registerQuestionsHistoryByTest = microtime(true) - $start_time__registerQuestionsHistoryByTest;
        Log::debug("---Aqui se termina el proceso de registrar las preguntas en la tabla 'question_test' para relacionar cada test con sus respectivas preguntas del alumno: {$user?->full_name} con id {$user?->id} el cuál ha tardado: {$elapsed_time__registerQuestionsHistoryByTest} segundos");

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

            $start_time__shuffle_questions = microtime(true);
            Log::debug("+++Aqui se ejecuta el proceso de desordenar las preguntas mapeadas que ya son compatibles en este momento con el Backend PHP del alumno: {$user?->full_name} con id {$user?->id}");
            shuffle($questions_id);
            $elapsed_time__shuffle_questions = microtime(true) - $start_time__shuffle_questions;
            Log::debug("---Aqui se termina el proceso de desordenar las preguntas mapeadas que ya son compatibles en este momento con el Backend PHP del alumno: {$user?->full_name} con id {$user?->id} el cuál ha tardado: {$elapsed_time__shuffle_questions} segundos");

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
        } catch (Exception $e) {
            abort(500, "Error Registrar preguntas obtenidas del procedure -> File: {$e->getFile()} -> Line: {$e->getLine()} -> Code: {$e->getCode()} -> Trace: {$e->getTraceAsString()} -> Message: {$e->getMessage()}");
        }
    }


}
