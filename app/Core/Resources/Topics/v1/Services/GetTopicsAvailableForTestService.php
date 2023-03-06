<?php

namespace App\Core\Resources\Topics\v1\Services;

use Illuminate\Support\Facades\DB;

class GetTopicsAvailableForTestService
{
    public static function mapDataTopicsAvailableForTest ($topics_id_data) {
        return array_map(function ($item) {
            $topic = (array) $item;
            // \Log::debug("---------------------Topic Data Map Procedure---------------------");
            // \Log::debug($topic);

            return [
                'id' => $topic['id']
            ];
        }, $topics_id_data);
    }

    public static function executeQueryFilterTopicsAvailableByOppositionAndTopicGroup (string $opposition_id, string $topic_group_id): array {
        $topic_data = DB::select(
            "call topics_available_for_create_test(?,?)",
            array($opposition_id, $topic_group_id)
        );

        // \Log::debug("---------------------Topic Data Procedure---------------------");
        // \Log::debug($topic_data);

        return self::mapDataTopicsAvailableForTest(
            $topic_data
        );
    }
}
