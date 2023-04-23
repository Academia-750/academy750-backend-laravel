<?php

namespace App\Core\Resources\Tests\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GetQuestionsByTopicProceduresService
{
    public static function getNumbersQuestionPerTopic ( $count_total_questions_request, $count_current_total_questions_got_procedure, $count_current_total_remaining_topics ): int {
        // Calculamos cuantas preguntas por tema corresponden

        return ceil( ($count_total_questions_request - $count_current_total_questions_got_procedure) / $count_current_total_remaining_topics );
    }

    public static function getNameFirstProcedure (bool $isCardMemory): string
    {
        if ($isCardMemory) {
            return 'get_questions_card_memory_by_topic';
        }
        return 'get_questions_test_by_topic';
    }

    public static function getNameSecondProcedure (bool $isCardMemory): string
    {
        if ($isCardMemory) {
            return 'complete_questions_card_memory_by_topic';
        }
        return 'complete_questions_test_by_topic';
    }

    public static function getNameOrderByTopicsASCProcedure (bool $isCardMemory): string
    {
        if ($isCardMemory) {
            return 'get_topic_questions_quantity_card_memory';
        }
        return 'get_topic_questions_quantity_test';
    }

    public static function clean_object_std_by_procedure ($questions_procedure) {
        // Una función para que el resultado del procedure sea compatible con un array de PHP

        return array_map(function ($item) {
            $itemCasted = (array) $item;
            return $itemCasted['question_id'];
        }, (array) $questions_procedure);
    }

    public static function countQuestionsFirstProcedureLessThanCountQuestionsRequestedByTopic (array $dataQuestionsIdCasted, int $count_current_questions_per_topic): bool
    {
        return count($dataQuestionsIdCasted) < $count_current_questions_per_topic;
    }

    public static function callFirstProcedure (string $nameProcedure, array $data): array
    {
        $user = auth()?->user();

        $start_time__callFirstProcedure = microtime(true);
        Log::debug("++++Aqui se ejecuta el proceso de realizar la petición para ejecutar el procedimiento almacenado {$nameProcedure} del alumno: {$user?->full_name} con id {$user?->id}");

        $questionsDataIDFirstProcedure = DB::select(
            "call {$nameProcedure}(?,?,?,?)",
            $data
        );

        $elapsed_time__callFirstProcedure = microtime(true) - $start_time__callFirstProcedure;
        Log::debug("----Aqui se termina el proceso de realizar la petición para ejecutar el procedimiento almacenado {$nameProcedure} del alumno el cuál ha tardado: {$elapsed_time__callFirstProcedure} segundos");


        $start_time__clean_object_std_by_procedure = microtime(true);
        Log::debug("++++Aqui se ejecuta el proceso de mapear los datos que devuelve el Procedimiento de SQL para que sea compatible con el Backend en PHP del alumno: {$user?->full_name} con id {$user?->id}");
        $questions_id = self::clean_object_std_by_procedure($questionsDataIDFirstProcedure);
        $elapsed_time__clean_object_std_by_procedure = microtime(true) - $start_time__clean_object_std_by_procedure;
        Log::debug("----Aqui se termina el proceso de mapear los datos que devuelve el Procedimiento de SQL para que sea compatible con el Backend en PHP del alumno el cuál ha tardado: {$elapsed_time__clean_object_std_by_procedure} segundos");

        return $questions_id;
    }

    public static function callSecondProcedure (string $nameProcedureProcedure, array $data): array
    {
        $questionsIdProcedure2Complete = DB::select(
            "call {$nameProcedureProcedure}(?,?,?,?,?)",
            $data
        );

        return array_map(array(__CLASS__, 'clean_object_std_by_procedure'), (array) $questionsIdProcedure2Complete);
    }

    public static function combineQuestionsOfFirstProcedureWithSecondProcedure (array $dataQuestionsIdCasted, array $questionsIdProcedure2CompleteCasted): array
    {
        return array_merge($dataQuestionsIdCasted, $questionsIdProcedure2CompleteCasted);
    }

    public static function getTopicsWithTotalQuestionsAvailable (bool $isCardMemory, array $data): array {
        $nameProcedure = self::getNameOrderByTopicsASCProcedure($isCardMemory);

        $topicsData = DB::select(
            "call {$nameProcedure}(?,?,?)",
            $data
        );

        // \Log::debug('--IMPRIMIR RESULTADOS DEL PROCEDURE NUEVO EN CRUDO--');
        //\Log::debug(array_map(array(__CLASS__, 'clean_object_std_by_procedure_topics_data_order_by_questions_total_available'), (array) $topicsData));

        return array_map(function ($item) {
            $itemCasted = (array) $item;
            return [
                'topic_id' => $itemCasted['topic_id'],
                'topic_name' => $itemCasted['nombre_del_tema'],
                'total_questions' => $itemCasted['total_questions']
            ];

        }, (array) $topicsData);
    }


    public static function sortTopicsAscByQuestionsTotal (array $topics_id, string $opposition_id, bool $isCardMemory, int $amountQuestionsRequestedByTest): array
    {
        return self::getTopicsWithTotalQuestionsAvailable(
            $isCardMemory,
            array(
                implode(',', $topics_id),
                $opposition_id,
                $amountQuestionsRequestedByTest
            )
        );
    }
}
