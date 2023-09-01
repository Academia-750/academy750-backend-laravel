<?php

namespace App\Core\Resources\Users\v1\Services;

use App\Models\Topic;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StatisticsDataHistoryStudent
{
    public static function getPeriodInKey(string $periodKey): string
    {
        if ($periodKey === 'last-month') {
            return '-1 month';
        }
        if ($periodKey === 'last-three-months') {
            return '-3 month';
        }

        return '0 month';
    }


    public static function getCollectGroupsStatisticsQuestionsTopic($topics_uuid, $period, $data): array
    {
        $topicsDataStatistic = [];

        $nameProcedure = $period === 'total' ? 'get_results_by_topic_total_procedure' : 'get_results_by_topic_date_procedure';

        foreach ($topics_uuid as $topic_uuid) {

            $topicEloquentOrm = Topic::query()->firstWhere('uuid', '=', $topic_uuid);

            if ($nameProcedure === 'get_results_by_topic_total_procedure') {
                $topicsDataStatistic[] = self::callProcedureGetResultsByTopicTotal(
                    $topicEloquentOrm->getKey(), $data['student_id']
                )[0];
            } else {
                \Log::debug(
                    array(
                        $topicEloquentOrm->getKey(),
                        $data['student_id'],
                        $data['last_date'],
                        $data['today']
                    )
                );
                $topicsDataStatistic[] = self::callProcedureGetResultsByTopicDate(
                    $topicEloquentOrm->getKey(),
                    $data['student_id'],
                    $data['last_date'],
                    $data['today']
                )[0];
            }
        }


        return $topicsDataStatistic;
    }
    public static function callProcedureGetResultsByTopicTotal($topic_id, $student_id)
    {
        return DB::select(
            'call get_results_by_topic_total_procedure(?,?)',
            array(
                $topic_id,
                $student_id
            )
        );
    }

    public static function callProcedureGetResultsByTopicDate($topic_id, $student_id, $last_date, $today)
    {
        return DB::select(
            'call get_results_by_topic_date_procedure(?,?,?,?)',
            array(
                $topic_id,
                $student_id,
                $last_date,
                $today
            )
        );
    }

}