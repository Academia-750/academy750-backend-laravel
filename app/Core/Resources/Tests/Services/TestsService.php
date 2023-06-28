<?php

namespace App\Core\Resources\Tests\Services;

use App\Models\Opposition;
use App\Models\Test;
use App\Models\Topic;
use Illuminate\Support\Facades\DB;

class TestsService
{
    /**
     * Crea un cuestionario nuevo
     *
     * @param $data
     * @return \App\Models\Test
     */
    public static function createTest ( $data )
    {

        try {
                $test = Test::create([
                    "number_of_questions_requested" => $data["number_of_questions_requested"],
                    "number_of_questions_generated" => $data["number_of_questions_requested"], // Se actualizará una vez se obtenga el numero real de preguntas disponibles
                    "test_result" => "0",
                    "is_solved_test" => 'no',
                    'test_type' => $data["test_type"],
                    'opposition_id' => $data['opposition_id'],
                    'user_id' => $data['user_id'],
                ]);
            return $test;
        } catch (\Exception $e) {
            abort(500, "Error Crear la cabecera del Test -> File: {$e->getFile()} -> Line: {$e->getLine()} -> Code: {$e->getCode()} -> Trace: {$e->getTraceAsString()} -> Message: {$e->getMessage()}");
        }
    }

    /**
     * Devuelve los subtemas de un tema dado, y solo aquellos que tienen vinculo con la Oposición dada
     *
     * @param Topic $topic
     * @param Opposition $opposition
     * @return array
     */
    public static function getSubtopicsByOppositionAndTopic (Topic $topic, Opposition $opposition ): array {
        $subtopics_id = [];

        foreach ( $opposition->subtopics as $subtopic ) {
            $subtopics_id_by_topic = $topic->subtopics()->pluck('subtopics.id')->toArray();

            if (in_array($subtopic?->getKey(), $subtopics_id_by_topic, true)) {
                $subtopics_id[] = $subtopic?->getKey();
            }
        }

        return $subtopics_id;
    }

    /**
     * Dado un arreglo de temas seleccionados, vamos obteniendo los subtemas de cada tema que coinciden con la Oposición dada
     *
     * @param array $topicsSelected_id
     * @param string $opposition_id
     * @return array
     */
    public static function getSubtopicsByOppositionAndTopics (array $topicsSelected_id, int $opposition_id ): array {
        $subtopics_id = DB::select(
            "call get_subtopics_ids_for_test_procedure(?,?)",
            array($opposition_id, implode(',', $topicsSelected_id))
        );

        return array_map(function($item) {
            $itemCasted = (array) $item;
            return $itemCasted['oppositionable_id'];
        }, $subtopics_id);
    }

    /**
     * Vincular los temas y subtemas con un Cuestionario generado
     *
     * @param Test $test
     * @param array $topicsSelected_id
     * @param string $opposition_id
     * @return void
     */
    public static function registerTopicsAndSubtopicsByTest (Test $test, array $topicsSelected_id, int $opposition_id )
    {
        try {

                $subtopicsEveryTopicAndOpposition = self::getSubtopicsByOppositionAndTopics($topicsSelected_id, $opposition_id);

                /*$topics_id = array_map(static function ($topic_id) {
                    return Topic::query()->findOrFail($topic_id)?->getRouteKey();
                }, $topicsSelected_id);*/

                // Vincular el Test creado con cada tema y sus subtemas

                $test->topics()->sync($topicsSelected_id);
                $test->subtopics()->sync($subtopicsEveryTopicAndOpposition);

        } catch (\Exception $e) {
            abort(500, "Error Registrar en Bitácora Temas y Subtemas de un Test -> File: {$e->getFile()} -> Line: {$e->getLine()} -> Code: {$e->getCode()} -> Trace: {$e->getTraceAsString()} -> Message: {$e->getMessage()}");
        }
    }
}
