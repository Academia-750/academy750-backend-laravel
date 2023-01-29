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
    public static function buildQuestionsTest (int $amountQuestionsRequestedByTest, string $testType, User $user, Test $test, array $topicsSelected_id, string $opposition_id )
    {

        $questions = self::getQuestionsByTestProcedure($amountQuestionsRequestedByTest, $testType, $user, $topicsSelected_id, $testType === 'card_memory', $opposition_id);

        \Log::debug("EL PROCEDURE YA SE HA EJECUTADO");
        \Log::debug("Número de preguntas generadas: " . count($questions));

        $test->number_of_questions_generated = count($questions);
        $test->save();

        self::registerQuestionsHistoryByTest($questions, $test, $testType);

        return $questions;
    }

    public static function getNumbersQuestionPerTopic ( $count_total_questions_request, $count_current_total_questions_got_procedure, $count_current_total_remaining_topics ) {
        \Log::debug('______getNumbersQuestionPerTopic________');
        \Log::debug($count_total_questions_request);
        \Log::debug($count_current_total_questions_got_procedure);
        \Log::debug($count_current_total_questions_got_procedure);
        \Log::debug(($count_total_questions_request - $count_current_total_questions_got_procedure));
        \Log::debug($count_current_total_remaining_topics);
        return ceil( ($count_total_questions_request - $count_current_total_questions_got_procedure) / $count_current_total_remaining_topics );
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
    public static function getQuestionsByTestProcedure (int $amountQuestionsRequestedByTest, string $testType, User $user, array $topicsSelected_id, bool $isCardMemory, string $opposition_id ) {
        try {
            DB::beginTransaction();

            //$nameProcedure = $isCardMemory ? 'get_questions_by_card_memory' : 'get_questions_by_test';
            $nameProcedure = $isCardMemory ? 'get_questions_card_memory_by_topic' : 'get_questions_test_by_topic';

            $questions_id = [];

            $count_current_questions_got_procedure = 0;
            $count_current_remaining_topics_requested = count($topicsSelected_id);

            $count_current_questions_per_topic = self::getNumbersQuestionPerTopic($amountQuestionsRequestedByTest, 0, $count_current_remaining_topics_requested);

            foreach ($topicsSelected_id as $topic_id) {

                // procedure 1
                $dataQuestionsId =  DB::select(
                    "call {$nameProcedure}(?,?,?,?)",
                    array($topic_id, $opposition_id, $user->getRouteKey(), (int) $count_current_questions_per_topic)
                );

                $dataQuestionsIdCasted = (array) $dataQuestionsId;

                //array_merge($questions_id, $dataQuestionsIdCasted);

                 foreach ($dataQuestionsIdCasted as $question_id) {
                    $questions_id[] = $question_id;
                }
                $count_current_questions_got_procedure+= count($dataQuestionsIdCasted);
                \Log::debug('___Numero de preguntas generadas por el procedure___');
                \Log::debug(count($dataQuestionsIdCasted));
                \Log::debug($count_current_questions_got_procedure);
                $count_current_remaining_topics_requested--;

                if ($count_current_remaining_topics_requested === 0) {
                    break;
                }

                $count_current_questions_per_topic = self::getNumbersQuestionPerTopic($amountQuestionsRequestedByTest, $count_current_questions_got_procedure, $count_current_remaining_topics_requested);
            }

            DB::commit();

            return $questions_id;
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
            $index = 0;

            foreach ($questions_id as $question_id) {
                $index++;

                $test->questions()->attach($question_id, [
                    'index' => $index,
                    'have_been_show_test' => 'no',
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
