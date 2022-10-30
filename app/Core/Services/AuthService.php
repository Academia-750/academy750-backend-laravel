<?php

namespace App\Core\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
class AuthService
{
    public static string $table_name_manage_tokens = "personal_access_tokens";

    public static function hasTableManageTokens (): bool
    {
        return Schema::hasTable(self::$table_name_manage_tokens);
    }

    public static function RemoveExpiredTokens (): void
    {
        $recordsTokenExpired = DB::table('personal_access_tokens')->where('expires_at', '<', Carbon::now())->pluck('id');

        if ($recordsTokenExpired->count() > 0 ) {
            foreach ($recordsTokenExpired as $token_id) {
                DB::table('personal_access_tokens')->where('id', '=', $token_id)->delete();
            }
        }
    }

    public static function RemoveExpiredTokensAction (): void
    {
        if (self::hasTableManageTokens()) {
            self::RemoveExpiredTokens();
        }
    }
}
