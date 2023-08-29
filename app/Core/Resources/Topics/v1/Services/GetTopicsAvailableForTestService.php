<?php

namespace App\Core\Resources\Topics\v1\Services;

use App\Models\Opposition;
use Illuminate\Support\Facades\DB;

class GetTopicsAvailableForTestService
{
    public static function mapDataTopicsAvailableForTest($topics_id_data)
    {
        return array_map(function ($item) {
            $topic = (array) $item;

            return [
                'id' => $topic['id']
            ];
        }, $topics_id_data);
    }

    public static function executeQueryFilterTopicsAvailableByOppositionAndTopicGroup(int $opposition_id, string $topics_group_id): array
    {
        $topic_data = DB::select(
            "call topics_available_for_create_test_procedure(?,?)",
            array(
                $opposition_id,
                $topics_group_id
            )
        );

        return self::mapDataTopicsAvailableForTest(
            $topic_data
        );
    }
}