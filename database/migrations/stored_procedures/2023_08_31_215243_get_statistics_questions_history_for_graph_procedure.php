<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function __construct(public string $nameProcedure = 'get_results_by_topic_date_procedure')
    {
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (app()->environment() === 'testing') {
            return;
        }
        $procedure1 = "DROP PROCEDURE IF EXISTS `{$this->nameProcedure}`;
        CREATE PROCEDURE `{$this->nameProcedure}`(
            IN `id_topic` INT,
            IN `id_user` INT,
            IN `min_date` TIMESTAMP,
            IN `max_date` TIMESTAMP
        )
        BEGIN
SELECT id_topic as 'TOPIC_ID', min_date, max_date,
              IFNULL(SUM(if(aux.status_solved_question = 'correct',1, 0)),0) AS 'CORRECT_ANS',
              IFNULL(SUM(if(aux.status_solved_question = 'wrong',1, 0)),0) AS 'INCORRECT_ANS',
              IFNULL(SUM(if(aux.status_solved_question = 'unanswered',1, 0)),0) AS 'UNANSWERED_ANS'
FROM (
SELECT p.id as question_id, t.id as test_id, q.status_solved_question
            FROM
              question_test q,
              tests t,
              questions p
            WHERE
              q.test_id = t.id
              and t.user_id = id_user
              and t.test_type = 'test'
              and p.id = q.question_id
              and p.questionable_id in (
                select id
                from topics t
                where t.id = id_topic
              )
              and p.questionable_type = 'App\\\\Models\\\\Topic'
    and (t.created_at >= min_date and t.created_at <= max_date)
UNION
SELECT p.id as question_id, t.id as test_id, q.status_solved_question
            FROM
              question_test q,
              tests t,
              questions p
            WHERE
              q.test_id = t.id
              and t.user_id = id_user
              and t.test_type = 'test'
              and p.id = q.question_id
              and p.questionable_id in (
                select id
                from subtopics t
                where t.topic_id = id_topic
              )
              and p.questionable_type = 'App\\\\Models\\\\Subtopic'
    and (t.created_at >= min_date and t.created_at <= max_date)
    ) AS aux;


            END
            ";

        DB::unprepared($procedure1);


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $procedure1 = "DROP PROCEDURE IF EXISTS `{$this->nameProcedure}`";


        DB::unprepared($procedure1);


    }
};