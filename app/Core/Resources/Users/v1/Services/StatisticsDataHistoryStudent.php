<?php

namespace App\Core\Resources\Users\v1\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StatisticsDataHistoryStudent
{
    public static function getPeriodInKey (string $periodKey): string {
        if ($periodKey === 'last-month') {
            return '-1 month';
        }
        if ($periodKey === 'last-three-months') {
            return '-3 month';
        }

        return '0 month';
    }

 /*   public static function getStatisticsByPeriod ($topic_id, $student_id, $last_date, $today) {
        // \Log::debug($last_date);
        // \Log::debug($today);

        return DB::select('call get_results_by_topic_date_procedure(?,?,?,?)', array(
            $topic_id, $student_id, $last_date, $today
        ));
    }

    public static function getStatisticsByTotal ($topic_id, $student_id) {
        return DB::select('call get_results_by_topic_total_procedure(?,?,?,?)', array(
            $topic_id, $student_id
        ));
    }*/

    public static function getCollectGroupsStatisticsQuestionsTopic ($topics_id, $period, $data): array {
        $topicsDataStatistic = [];

        $nameProcedure = $period === 'total' ? 'get_results_by_topic_total_procedure' : 'get_results_by_topic_date_procedure';

        foreach ($topics_id as $topic_id) {
            if ($nameProcedure === 'get_results_by_topic_total_procedure') {
                $arguments = array(
                    $topic_id, $data['student_id']
                );
            } else {
                $arguments = array(
                    $topic_id, $data['student_id'], $data['last_date'], $data['today']
                );
            }

            /* // \Log::debug($nameProcedure);
            // \Log::debug($arguments);
            // \Log::debug("{$data['last_date']} 00:00:00");
            // \Log::debug("{$data['today']} 00:00:00"); */
;
            $topicsDataStatistic[] = DB::select("call {$nameProcedure}(?,?,?,?)", $arguments)[0];
            //// \Log::debug($topicsDataStatistic);
        }

        return $topicsDataStatistic;
    }
}
