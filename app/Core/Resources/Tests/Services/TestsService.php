<?php

namespace App\Core\Resources\Tests\Services;

use App\Models\Opposition;
use App\Models\Test;
use App\Models\Topic;
use Illuminate\Support\Facades\DB;

class TestsService
{
    public static function createTest ( $request ): \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Builder
    {

        try {
            DB::beginTransaction();
                $test = Test::query()->create([
                    "number_of_questions_requested" => $request->get("number_of_questions_requested"),
                    "number_of_questions_generated" => $request->get("number_of_questions_requested"), // Se actualizará una vez se obtenga el numero real de preguntas disponibles
                    "test_result" => "0",
                    "is_solved_test" => 'no',
                    'test_type_id' => $request->get("test_type_id"),
                    'opposition_id' => $request->get('opposition_id'),
                    'user_id' => $request->get('user_id'),
                ]);
            DB::commit();

            return $test;
        } catch (\Throwable $th) {
            DB::rollBack();
            abort(500, $th->getMessage());
        }


    }

    public static function getSubtopicsByOppositionAndTopic ( $topicsSelected, Opposition $opposition ): array {
        $subtopics = [];

        foreach ($topicsSelected as $topic_id) {
            $topic = Topic::query()->findOrFail($topic_id);

            foreach ( $opposition->subtopics as $subtopic ) {
                $subtopics_id_by_topic = $topic->subtopics->pluck('id')->toArray();

                if (in_array($subtopic?->getRouteKey(), $subtopics_id_by_topic, true)) {
                    $subtopic[] = $subtopic;
                }
            }
        }

        return $subtopics;
    }

    public static function registerTopicsAndSubtopicsByTest ( Test $test, array $topicsSelected, Opposition $opposition ): array
    {
        try {
            DB::beginTransaction();

                $subtopics = self::getSubtopicsByOppositionAndTopic($topicsSelected, $opposition);

                $topics = array_map(static function ($topic_id) {
                    return Topic::query()->findOrFail($topic_id);
                }, $topicsSelected);

                $test->topics()->sync($topics);
                $test->subtopics()->sync($subtopics);

            DB::commit();
            return array_merge($topics, $subtopics);

        } catch (\Throwable $th) {
            DB::rollBack();
            abort(500, $th->getMessage());
        }
    }
}
