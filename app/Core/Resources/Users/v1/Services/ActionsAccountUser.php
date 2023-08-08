<?php

namespace App\Core\Resources\Users\v1\Services;

use App\Models\GroupUsers;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ActionsAccountUser
{
    public static function removeAllTokensByUserReally($user, string $nameTable = 'personal_access_tokens', string $nameFieldToken = 'tokenable_id')
    {
        DB::table($nameTable)
            ->where(
                $nameFieldToken,
                '=',
                $user->getKey()
            )->delete();
    }

    public static function deleteUser($user)
    {
        if (!($user instanceof User)) {
            $user = User::query()->findOrFail($user);
        }

        self::removeAllTokensByUserReally($user);


        DB::select(
            "CALL delete_user_data_test_procedure(?)",
            array($user->getKey())
        );

        $user->delete();

        return $user;
    }
    public static function disableAccountUser($user)
    {

        if (!($user instanceof User)) {
            $user = User::query()->findOrFail($user->getKey());
        }

        self::removeAllTokensByUserReally($user);

        $user->state = 'disable';
        $user->save();
        $user->refresh();

        GroupUsers::where('user_id', $user->id)->whereNull('discharged_at')->update(['discharged_at' => now()]);

        return $user;
    }
    public static function enableAccountUser($user)
    {

        if (!($user instanceof User)) {
            $user = User::query()->findOrFail($user->getKey());
        }

        self::removeAllTokensByUserReally($user);

        $user->state = 'enable';
        $user->save();
        $user->refresh();

        /*$user->restore(); // Soft Delete
        $user->refresh();*/

        return $user;
    }
}