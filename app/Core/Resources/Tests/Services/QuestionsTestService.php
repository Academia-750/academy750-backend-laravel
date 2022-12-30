<?php

namespace App\Core\Resources\Tests\Services;

use App\Models\Opposition;
use App\Models\Test;
use App\Models\TestType;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use function Symfony\Component\String\u;

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
     * @return array|void
     */
    public static function buildQuestionsTest (int $amountQuestionsRequestedByTest, string $testType, User $user, Test $test )
    {

        $questions = self::getQuestionsByTestProcedure($amountQuestionsRequestedByTest, $testType, $user, $test, $testType === 'card-memory');

        \Log::debug("EL PROCEDURE YA SE HA EJECUTADO");
        \Log::debug("Número de preguntas generadas: " . count($questions));

        $test->number_of_questions_generated = count($questions);
        $test->save();

        self::registerQuestionsHistoryByTest($questions, $test, $testType);

        return $questions;
    }

    /**
     * Invoca el procedure correspondiente para generar las preguntas dependiendo
     * si es cuestionario o tarjeta de memoria
     *
     * @param int $amountQuestionsRequestedByTest
     * @param TestType $testType
     * @param User $user
     * @param Test $test
     * @param bool $isCardMemory
     * @return array|void
     */
    public static function getQuestionsByTestProcedure (int $amountQuestionsRequestedByTest, string $testType, User $user, Test $test, bool $isCardMemory ) {
        try {
            DB::beginTransaction();

            $nameProcedure = $isCardMemory ? 'get_questions_by_card_memory' : 'get_questions_by_test';
            \Log::debug("Nombre del procedure a ejecutar: {$nameProcedure}");
            \Log::debug("LOS DATOS QUE PASO COMO PARÁMETRO");
            \Log::debug("ID del Usuario Alumno: {$user->getRouteKey()}");
            \Log::debug("ID del Test: {$test->getRouteKey()}");
            \Log::debug("Tipo de Test: {$testType}");
            \Log::debug("Cantidad de preguntas solicitadas: {$amountQuestionsRequestedByTest}");

            $data =  DB::select(
                "call {$nameProcedure}(?,?,?,?)",
                array($user->getRouteKey(), $test->getRouteKey() , $testType, (int) $amountQuestionsRequestedByTest)
            );

            \Log::debug("AQUÍ DESPUÉS SE GENERA LA DATA DEL PROCEDURE");
            \Log::debug($data);

            DB::commit();

            return $data;
        } catch (\Throwable $th) {
            \Log::debug("SE PRODUJO UN ERROR JUSTO DESPUÉS DE EJECUTAR EL PROCEDURE");
            DB::rollBack();
            abort(500, $th->getMessage());
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
    public static function registerQuestionsHistoryByTest (array $questions_id, Test $test, string $testType): void {
        try {
            DB::beginTransaction();
            foreach ($questions_id as $question_id) {

                $test->questions()->attach($question_id, [
                    'have_been_show_test' => $testType === 'test' ? 'yes' : 'no',
                    'have_been_show_card_memory' => $testType === 'card_memory' ? 'yes' : 'no',
                    'answer_id' => null,
                    'status_solved_question' => 'unanswered'
                ]);

            }
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            abort(500, $th->getMessage());
        }
    }


}
