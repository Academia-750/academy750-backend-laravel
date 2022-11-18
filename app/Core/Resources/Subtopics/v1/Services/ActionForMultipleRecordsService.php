<?php

namespace App\Core\Resources\Subtopics\v1\Services;

use App\Models\User;

class ActionForMultipleRecordsService
{
    public static function actionForMultipleRecords ($action, $records): array {
        if ($action === 'delete') {
            return self::deleteMultipleRecords($records);
        }

        return [];
    }

    public static function deleteMultipleRecords ($records): array {
        $information = [];

        foreach ($records as $topic_id) {
            $subtopic = ActionsSubtopicsRecords::deleteRecord($topic_id);
            $information[] = "'Subtema {$subtopic->getRouteKey()}' ha sido eliminado con éxito";
        }

        return $information;
    }

}
