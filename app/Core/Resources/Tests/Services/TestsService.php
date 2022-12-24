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
            DB::beginTransaction();
                $test = Test::create([
                    "number_of_questions_requested" => $data["number_of_questions_requested"],
                    "number_of_questions_generated" => $data["number_of_questions_requested"], // Se actualizará una vez se obtenga el numero real de preguntas disponibles
                    "test_result" => "0",
                    "is_solved_test" => 'no',
                    'test_type' => $data["test_type"],
                    'opposition_id' => $data['opposition_id'],
                    'user_id' => $data['user_id'],
                ]);
            DB::commit();

            return $test;
        } catch (\Throwable $th) {
            DB::rollBack();
            abort(500, $th->getMessage());
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
            $subtopics_id_by_topic = $topic->subtopics->pluck('id')->toArray();

            if (in_array($subtopic?->getRouteKey(), $subtopics_id_by_topic, true)) {
                $subtopics_id[] = $subtopic?->getRouteKey();
            }
        }

        return $subtopics_id;
    }

    /**
     * Dado un arreglo de temas seleccionados, vamos obteniendo los subtemas de cada tema que coinciden con la Oposición dada
     *
     * @param array $topicsSelected_id
     * @param Opposition $opposition
     * @return array
     */
    public static function getSubtopicsByOppositionAndTopics (array $topicsSelected_id, Opposition $opposition ): array {
        $subtopics_id = [];

        foreach ($topicsSelected_id as $topic_id) {
            $topic = Topic::findOrFail($topic_id);

            $subtopics_id[] = self::getSubtopicsByOppositionAndTopic($topic, $opposition);
        }

        return $subtopics_id;
    }

    /**
     * Vincular los temas y subtemas con un Cuestionario generado
     *
     * @param Test $test
     * @param array $topicsSelected_id
     * @param Opposition $opposition
     * @return array
     */
    public static function registerTopicsAndSubtopicsByTest (Test $test, array $topicsSelected_id, Opposition $opposition ): array
    {
        try {
            DB::beginTransaction();

                $subtopicsIdByTopic = self::getSubtopicsByOppositionAndTopics($topicsSelected_id, $opposition);

                $subtopics_id = [];

                foreach ( $subtopicsIdByTopic as $array_subtopics_by_topic ) {
                    foreach ($array_subtopics_by_topic as $subtopic_id) {
                        $subtopics_id[] = $subtopic_id;
                    }
                }

                $topics_id = array_map(static function ($topic_id) {
                    return Topic::query()->findOrFail($topic_id)?->getRouteKey();
                }, $topicsSelected_id);

                // Vincular el Test creado con cada tema y sus subtemas
                $test->topics()->sync($topics_id);
                $test->subtopics()->sync($subtopics_id);

            DB::commit();
            return array_merge($topics_id, $subtopics_id);

        } catch (\Throwable $th) {
            DB::rollBack();
            abort(500, $th->getMessage());
        }
    }
}
