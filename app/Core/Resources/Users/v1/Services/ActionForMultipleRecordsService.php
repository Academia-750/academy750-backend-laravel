<?php

namespace App\Core\Resources\Users\v1\Services;

use App\Models\User;

class ActionForMultipleRecordsService
{
    public static function actionForMultipleRecords ($action, $users): array {
        if ($action === 'delete') {
            return self::deleteMultipleUsers($users);
        }

        if ($action === 'lock-account') {
            return self::disableAccountMultipleUsers($users);
        }

        if ($action === 'unlock-account') {
            return self::enableAccountMultipleUsers($users);
        }

        return [];
    }

    public static function deleteMultipleUsers ($users): array {
        $information = [];

        foreach ($users as $user_id) {
            $user = ActionsTopicsRecords::deleteUser($user_id);
            $information[] = "'Usuario {$user->getRouteKey()}' ha sido eliminado con éxito";
        }

        return $information;
    }



    public static function disableAccountMultipleUsers ($users): array {
        $information = [];

        foreach ($users as $user_id) {
            $user = ActionsTopicsRecords::disableAccountUser($user_id);
            $information[] = "La cuenta del 'Usuario {$user->getRouteKey()}' ha sido deshabilitada.";
        }

        return $information;
    }



    public static function enableAccountMultipleUsers ($users): array {
        $information = [];

        foreach ($users as $user_id) {
            $user = ActionsTopicsRecords::enableAccountUser($user_id);
            $information[] = "La cuenta del 'Usuario {$user->getRouteKey()}' ha sido habilitada.";
        }

        return $information;
    }


}
