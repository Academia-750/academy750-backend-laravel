<?php

namespace App\Core\Resources\Oppositions\v1\Services;

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

        foreach ($oppositions as $opposition_id) {
            $opposition = ActionsOppositionsRecords::deleteOpposition($opposition_id);
            $information[] = "'Oposicion {$opposition->getRouteKey()}' ha sido eliminado con éxito";
        }

        return $information;
    }
}
