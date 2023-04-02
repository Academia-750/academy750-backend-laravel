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
     * @return array|void|null
     */
    public static function buildQuestionsTest (int $amountQuestionsRequestedByTest, string $testType, User $user, Test $test, array $topicsSelected_id, string $opposition_id )
    {

        $TotalQuestionsGottenByAllTopicsSelected = self::getQuestionsByTestProcedure($amountQuestionsRequestedByTest, $testType, $user, $topicsSelected_id, $testType === 'card_memory', $opposition_id);

        $test->number_of_questions_generated = count($TotalQuestionsGottenByAllTopicsSelected);
        $test->save();

        // Desordenamos array de preguntas
        shuffle($TotalQuestionsGottenByAllTopicsSelected);

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
    public static function getQuestionsByTestProcedure (int $amountQuestionsRequestedByTest, string $testType, User $user, array $topicsSelected_id, bool $isCardMemory, string $opposition_id ) {
        try {

            //$nameProcedure = $isCardMemory ? 'get_questions_by_card_memory' : 'get_questions_by_test';
            $nameProcedure = GetQuestionsByTopicProceduresService::getNameFirstProcedure($isCardMemory);

            // \Log::debug("Nombre del primer Procedure ejecutado {$nameProcedure}");

            $questions_id = [];

            $count_current_questions_got_procedure = 0;
            $count_current_remaining_topics_requested = count($topicsSelected_id);

            $count_current_questions_per_topic = GetQuestionsByTopicProceduresService::getNumbersQuestionPerTopic($amountQuestionsRequestedByTest, 0, $count_current_remaining_topics_requested);
            // \Log::debug("_________________________________________________________________________________________________");
            // \Log::debug("Primera ves obtenemos la cantidad de preguntas por tema que necesitaremos extraer del primer tema");
            // \Log::debug("Cantidad de preguntas que necesitaremos del tema 1: {$count_current_questions_per_topic}");
            // \Log::debug("Cantidad de temas seleccionados: {$count_current_remaining_topics_requested}");
            // \Log::debug($topicsSelected_id);

            $topicsSelectedOrdered = GetQuestionsByTopicProceduresService::sortTopicsAscByQuestionsTotal($topicsSelected_id, $opposition_id, $isCardMemory);
            // \Log::debug($topicsSelectedOrdered);

            $start_time_getQuestionsByTestProcedure = microtime(true);

            foreach ($topicsSelectedOrdered as $topic_id) {

                // procedure 1 (Pedimos que busque todas las preguntas disponibles y no visibles para este tema)
                $dataQuestionsIdCasted = GetQuestionsByTopicProceduresService::callFirstProcedure($nameProcedure, array($topic_id, $opposition_id, $user->getRouteKey(), (int) $count_current_questions_per_topic));

                // \Log::debug("----Preguntas Procedure 1----");
                // \Log::debug($dataQuestionsIdCasted);
                // \Log::debug(count($dataQuestionsIdCasted));

                // Aquí será la variable que almacenará las preguntas del procedure 1 y en caso de no haber suficientes preguntas para este tema, se usará para almacenar también las del prcoedure 2
                $questionsTotalForThisTopic = $dataQuestionsIdCasted;

                // Si no me devolvió el número de preguntas que necesito de este tema, tocará buscar entre las preguntas visibles
                //$start_time_countQuestionsFirstProcedureLessThanCountQuestionsRequestedByTopic = microtime(true);
                if (GetQuestionsByTopicProceduresService::countQuestionsFirstProcedureLessThanCountQuestionsRequestedByTopic($dataQuestionsIdCasted, $count_current_questions_per_topic)) {
                    // \Log::debug("Al parecer no hubo suficientes preguntas del procedure 1 para completar las que se necesitaban del tema");

                    $nameProcedureProcedure = GetQuestionsByTopicProceduresService::getNameSecondProcedure($isCardMemory);
                    // \Log::debug("Nombre del segundo Procedure ejecutado {$nameProcedureProcedure}");
                    //$questionsIdProcedure2Complete = (array) $questionsIdProcedure2Complete;
                    $questionsIdProcedure2CompleteCasted = GetQuestionsByTopicProceduresService::callSecondProcedure(
                        $nameProcedureProcedure,
                        array(
                            $topic_id,
                            $opposition_id,
                            $user->getRouteKey(),
                            (int) ( $count_current_questions_per_topic - count($dataQuestionsIdCasted) ), // Ejemplo: Si se requiere 5 preguntas por tema, y el procedure 1 me dió 2 (preguntas no visibles), entonces al procedure 2 solo le pediré lo que falta para la meta, que son 2 preguntas, pero buscará entre las preguntas visibles
                            implode(',', $dataQuestionsIdCasted) // Paso las preguntas que ya me dió el procedure 1 para evitar que el procedure 2 me las vaya a devolver nuevamente
                        )
                    );

                    // \Log::debug("----Preguntas Procedure 2----");
                    // \Log::debug($questionsIdProcedure2CompleteCasted);
                    // \Log::debug(count($questionsIdProcedure2CompleteCasted));

                    // Unimos las preguntas del procedure 1 y las del procedure 2

                    $questionsTotalForThisTopic = GetQuestionsByTopicProceduresService::combineQuestionsOfFirstProcedureWithSecondProcedure($dataQuestionsIdCasted, $questionsIdProcedure2CompleteCasted);
                }
                /*$elapsed_time_start_time_countQuestionsFirstProcedureLessThanCountQuestionsRequestedByTopic = microtime(true) - $start_time_countQuestionsFirstProcedureLessThanCountQuestionsRequestedByTopic;
                \Log::debug("Time elapsed {$user->first_name} for QuestionsTestService::countQuestionsFirstProcedureLessThanCountQuestionsRequestedByTopic(): $elapsed_time_start_time_countQuestionsFirstProcedureLessThanCountQuestionsRequestedByTopic seconds");*/
                // Creamos una referencia del array que almacena todas las preguntas absolutamente de todas las preguntas que se vayan recoletando de cada tema
                $questionsCurrentID = $questions_id;

                // Unimos todas las preguntas que hemos juntado hasta ahora de los demás temas, con las nuevas preguntas de este tema
                $questions_id = array_merge($questionsCurrentID, $questionsTotalForThisTopic);
                $asfafsa = count($questions_id);
                // \Log::debug("Cantidad de preguntas que se recojieron de este tema en total: {$asfafsa}");

                $ahuifsa0fa = count($questionsTotalForThisTopic);
                // \Log::debug("Cantidad de preguntas que llevamos ya recolectadas entre los anteriores temas: {$ahuifsa0fa}");

                // Aquí llevamos el conteo de cuantas preguntas ya hemos acumulado por cada tema, para así saber cuantas preguntas necesitaremos para el siguiente tema
                $count_current_questions_got_procedure+= count($questionsTotalForThisTopic);

                // \Log::debug("La cantidad de preguntas que llevamos sumando las de este tema: {$count_current_questions_got_procedure}");

                // \Log::debug("La cantidad de temas que nos falta recorrer actualmente: {$count_current_remaining_topics_requested}");
                // Restamos 1 tema por buscar preguntas, ya que en este punto ya hemos obtenido preguntas del tema actual, y analizaremos cuantas preguntas necesitaremos del siguiente tema
                $count_current_remaining_topics_requested--;

                // \Log::debug("La cantidad de temas que nos falta recorrer actualmente quitando el tema que hemos recorrido justo ahora: {$count_current_remaining_topics_requested}");

                if ($count_current_remaining_topics_requested === 0) {
                    // \Log::debug("Ya se acabaron los temas recorridos");
                    break;
                }

                // En caso de que todavía queden temas disponibles, hacemos el cálculo nuevamente de cuantas preguntas necesitaremos del siguiente tema
                $count_current_questions_per_topic = GetQuestionsByTopicProceduresService::getNumbersQuestionPerTopic($amountQuestionsRequestedByTest, $count_current_questions_got_procedure, $count_current_remaining_topics_requested);

                // \Log::debug("En este punto hemos recorrido el tema, por lo que para el siguiente tema tenemos que recojer: {$count_current_questions_per_topic} preguntas");
            }


            $elapsed_time_getQuestionsByTestProcedure = microtime(true) - $start_time_getQuestionsByTestProcedure;
            \Log::debug("Time elapsed {$user->first_name} for QuestionsTestService::getQuestionsByTestProcedure() foreach: {$elapsed_time_getQuestionsByTestProcedure} seconds");
            // Devolvemos todas los ID de las preguntas que hemos recolectado entre todos los temas seleccionados por el alumno
            return $questions_id;
        } catch (\Throwable $th) {
            // \Log::debug("SE PRODUJO UN ERROR JUSTO DESPUÉS DE EJECUTAR EL PROCEDURE");
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
    public static function registerQuestionsHistoryByTest (array $questions_id, Test $test, string $testType, User $user): void {
        try {
            $index = 0;

            $start_time = microtime(true);
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
            $elapsed_time = microtime(true) - $start_time;
            \Log::debug("Time elapsed {$user->first_name} for QuestionsTestService::registerQuestionsHistoryByTest(): $elapsed_time seconds");
        } catch (\Throwable $th) {
            DB::rollBack();
            abort(500, $th->getMessage());
        }
    }


}
