<?php

namespace App\Core\Resources\Tests\Services;

use Illuminate\Support\Facades\DB;

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

    public static function clean_object_std_by_procedure ($item) {
        // Una función para que el resultado del procedure sea compatible con un array de PHP

        $itemCasted = (array) $item;
        return $itemCasted['id'];
    }

    public static function clean_object_std_by_procedure_topics_data_order_by_questions_total_available ($item): array {
        // Una función para que el resultado del procedure sea compatible con un array de PHP

        $itemCasted = (array) $item;
        return [
            'topic_id' => $itemCasted['topic_id'],
            'topic_name' => $itemCasted['nombre_del_tema'],
            'total_questions' => $itemCasted['total_questions']
        ];
    }

    public static function countQuestionsFirstProcedureLessThanCountQuestionsRequestedByTopic (array $dataQuestionsIdCasted, int $count_current_questions_per_topic): bool
    {
        return count($dataQuestionsIdCasted) < $count_current_questions_per_topic;
    }

    public static function callFirstProcedure (string $nameProcedure, array $data): array
    {
        $questionsDataIDFirstProcedure = DB::select(
            "call {$nameProcedure}(?,?,?,?)",
            $data
        );

        return array_map(array(__CLASS__, 'clean_object_std_by_procedure'), (array) $questionsDataIDFirstProcedure);
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
            "call {$nameProcedure}(?,?)",
            $data
        );

        // \Log::debug('--IMPRIMIR RESULTADOS DEL PROCEDURE NUEVO EN CRUDO--');
        //\Log::debug(array_map(array(__CLASS__, 'clean_object_std_by_procedure_topics_data_order_by_questions_total_available'), (array) $topicsData));

        return array_map(array(__CLASS__, 'clean_object_std_by_procedure_topics_data_order_by_questions_total_available'), (array) $topicsData);
    }


    public static function sortTopicsAscByQuestionsTotal (array $topics_id, string $opposition_id, bool $isCardMemory): array
    {

        /*$topicsDataForOrderByTotalQuestions = [];

        foreach ($topics_id as $topic_id) {

            $resultProcedure = self::getTopicsWithTotalQuestionsAvailable($isCardMemory, array( $topic_id, $opposition_id ))[0];

            // \Log::debug('--IMPRIMIR RESULTADOS DEL PROCEDURE NUEVO--');
            // \Log::debug($resultProcedure);

            $topicsDataForOrderByTotalQuestions[] = $resultProcedure;
        }

        return collect($topicsDataForOrderByTotalQuestions)->sortBy('total_questions')->pluck('topic_id')->toArray();*/

        return self::getTopicsWithTotalQuestionsAvailable($isCardMemory, array( implode(',', $topics_id), $opposition_id ));
    }
}
