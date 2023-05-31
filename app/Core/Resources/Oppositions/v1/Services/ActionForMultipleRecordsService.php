<?php

namespace App\Core\Resources\Oppositions\v1\Services;

use App\Models\Opposition;

class ActionForMultipleRecordsService
{
    public static function actionForMultipleRecords ($action, $oppositions): array {
        if ($action === 'delete') {
            return self::deleteMultipleOppositions($oppositions);
        }

        return [];
    }

    public static function deleteMultipleOppositions ($oppositions): array {
        $information = [];

        foreach ($oppositions as $opposition_uuid) {
            $oppositionEloquentOrm = Opposition::query()->firstWhere('uuid', '=', $opposition_uuid);
            $opposition = ActionsOppositionsRecords::deleteOpposition($oppositionEloquentOrm->getKey());
            $information[] = "'Oposicion {$opposition->getRouteKey()}' ha sido eliminado con éxito";
        }

        return $information;
    }
}
