<?php

namespace App\Core\Resources\Users\v1\Services;

use Illuminate\Support\Facades\Auth;

class StatisticsDataHistoryStudent
{
    public static function getPeriodInInteger (string $periodKey): int {
        if ($periodKey === 'last-month') {
            return 1;
        }
        if ($periodKey === 'last-three-months') {
            return 3;
        }

        return 0;
    }

    public static function getTestsIdUser ( $date ): array {
        return Auth::user()?->tests()
            ->where('test_type', '=', 'test')
            ->where('is_solved_test', '=', 'yes')
            ->where('created_at', '<=', $date )
            ->pluck('tests.id')
            ->toArray();
    }

    public static function getQuestionsIdByTests () {

    }
}
