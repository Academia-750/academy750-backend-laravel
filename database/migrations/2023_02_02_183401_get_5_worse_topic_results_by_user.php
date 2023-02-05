<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $procedure1 = "DROP PROCEDURE IF EXISTS `get_5_worse_topic_results_by_user`;
        CREATE PROCEDURE `get_5_worse_topic_results_by_user`(
            IN `id_usuario` VARCHAR(255)
        )
        BEGIN
        select aux_tb.TOPIC_ID, aux_tb.TOPIC_NAME,
        sum( case when aux_tb.STATUS= 'correct' then 1 else 0 end ) as CORRECT_ANS,
        sum( case when aux_tb.STATUS= 'wrong' then 1 else 0 end ) as INCORRECT_ANS,
        sum( case when aux_tb.STATUS= 'unanswered' then 1 else 0 end ) as UNANSWERED_ANS,
        ((sum( case when aux_tb.STATUS= 'correct' then 1 else 0 end ) * 100)/ (sum( case when aux_tb.STATUS= 'correct' then 1 else 0 end ) + sum( case when aux_tb.STATUS= 'wrong' then 1 else 0 end ) + sum( case when aux_tb.STATUS= 'unanswered' then 1 else 0 end ))) as PERCENTAGE
        from (
        select D.id as TOPIC_ID, D.name as TOPIC_NAME, A.id as TEST_ID, C.id as QUESTION_ID, B.status_solved_question as STATUS
        from tests as A
        inner join question_test as B on B.test_id = A.id
        inner join questions as C on C.id = B.question_id
        inner join topics as D on D.id = C.questionable_id
        where A.user_id = id_usuario and A.finished_at is not null
        union
        select E.id as TOPIC_ID, E.name as TOPIC_NAME, A.id as TEST_ID, C.id as QUESTION_ID, B.status_solved_question as STATUS
        from tests as A
        inner join question_test as B on B.test_id = A.id
        inner join questions as C on C.id = B.question_id
        inner join subtopics as D on D.id = C.questionable_id
        inner join topics as E on E.id = D.topic_id
        where A.user_id = id_usuario and A.finished_at is not null) as aux_tb
        group by aux_tb.TOPIC_ID, aux_tb.TOPIC_NAME
        order by PERCENTAGE limit 5;
        END";

        DB::unprepared($procedure1);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $procedure1 = "DROP PROCEDURE IF EXISTS `get_5_worse_topic_results_by_user`";

        DB::unprepared($procedure1);
    }
};
