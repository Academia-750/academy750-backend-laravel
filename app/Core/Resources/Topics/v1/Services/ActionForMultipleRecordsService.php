<?php

namespace App\Core\Resources\Topics\v1\Services;

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
            $user = ActionsTopicsRecords::deleteRecord($topic_id);
            $information[] = "'Tema {$user->getRouteKey()}' ha sido eliminado con éxito";
        }

        return $information;
    }

}
