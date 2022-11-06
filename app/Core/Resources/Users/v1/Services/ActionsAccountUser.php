<?php

namespace App\Core\Resources\Users\v1\Services;

use App\Models\User;

class ActionsAccountUser
{
    public static function deleteUser ($user) {
        if ( !($user instanceof User) ) {
            $user = User::query()->find($user);
        }

        $user->forceDelete();

        return $user;
    }
    public static function lockAccountUser ($user) {

        if ( !($user instanceof User) ) {
            $user = User::query()->find($user);
        }

        $user->state = 'disable';
        $user->save();
        $user->refresh();

        $user->delete(); // Soft Delete
        $user->refresh();

        return $user;
    }
    public static function unlockAccountUser ($user) {

        if ( !($user instanceof User) ) {
            $user = User::query()->onlyTrashed()->find($user);
        }

        $user->state = 'enable';
        $user->save();
        $user->refresh();

        $user->restore(); // Soft Delete
        $user->refresh();

        return $user;
    }
}
