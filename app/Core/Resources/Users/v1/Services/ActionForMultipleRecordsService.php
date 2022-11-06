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
            return self::lockAccountMultipleUsers($users);
        }

        if ($action === 'unlock-account') {
            return self::unlockAccountMultipleUsers($users);
        }

        return [];
    }

    public static function deleteMultipleUsers ($users): array {
        $information = [];

        foreach ($users as $user_id) {
            $user = ActionsAccountUser::deleteUser($user_id);
            $information[] = "'Usuario {$user->getRouteKey()}' ha sido eliminado con éxito";
        }

        return $information;
    }



    public static function lockAccountMultipleUsers ($users): array {
        $information = [];

        foreach ($users as $user_id) {
            $user = ActionsAccountUser::lockAccountUser($user_id);
            $information[] = "La cuenta del 'Usuario {$user->getRouteKey()}' ha sido deshabilitada.";
        }

        return $information;
    }



    public static function unlockAccountMultipleUsers ($users): array {
        $information = [];

        foreach ($users as $user_id) {
            $user = ActionsAccountUser::unlockAccountUser($user_id);
            $information[] = "La cuenta del 'Usuario {$user->getRouteKey()}' ha sido habilitada.";
        }

        return $information;
    }


}
