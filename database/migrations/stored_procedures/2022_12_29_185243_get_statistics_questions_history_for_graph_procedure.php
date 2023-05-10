<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function __construct(Public string $nameProcedure = 'get_results_by_topic_date_procedure'){}

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $procedure1= "DROP PROCEDURE IF EXISTS `{$this->nameProcedure}`;
        CREATE PROCEDURE `{$this->nameProcedure}`(
            IN `id_topic` VARCHAR(255),
            IN `id_user` VARCHAR(255),
            IN `min_date` DATE,
            IN `max_date` DATE
        )
        BEGIN
            SELECT
              aux_tb.TOPIC_ID,
              aux_tb.TOPIC_NAME,
              SUM(
                CASE WHEN aux_tb.STATUS = 'correct' THEN 1 ELSE 0 END
              ) AS CORRECT_ANS,
              SUM(
                CASE WHEN aux_tb.STATUS = 'wrong' THEN 1 ELSE 0 END
              ) AS INCORRECT_ANS,
              SUM(
                CASE WHEN aux_tb.STATUS = 'unanswered' THEN 1 ELSE 0 END
              ) AS UNANSWERED_ANS,
              (
                (
                  SUM(
                    CASE WHEN aux_tb.STATUS = 'correct' THEN 1 ELSE 0 END
                  ) * 100
                ) / (
                  SUM(
                    CASE WHEN aux_tb.STATUS = 'correct' THEN 1 ELSE 0 END
                  ) + SUM(
                    CASE WHEN aux_tb.STATUS = 'wrong' THEN 1 ELSE 0 END
                  ) + SUM(
                    CASE WHEN aux_tb.STATUS = 'unanswered' THEN 1 ELSE 0 END
                  )
                )
              ) AS PERCENTAGE
            FROM
              (
                SELECT
                  D.id AS TOPIC_ID,
                  D.name AS TOPIC_NAME,
                  A.id AS TEST_ID,
                  C.id AS QUESTION_ID,
                  B.status_solved_question AS STATUS
                FROM
                  tests AS A
                  INNER JOIN question_test AS B ON B.test_id = A.id
                  INNER JOIN questions AS C ON C.id = B.question_id
                  INNER JOIN topics AS D ON D.id = C.questionable_id
                WHERE
                  A.user_id = id_user
                  AND A.finished_at IS NOT NULL
                UNION
                SELECT
                  E.id AS TOPIC_ID,
                  E.name AS TOPIC_NAME,
                  A.id AS TEST_ID,
                  C.id AS QUESTION_ID,
                  B.status_solved_question AS STATUS
                FROM
                  tests AS A
                  INNER JOIN question_test AS B ON B.test_id = A.id
                  INNER JOIN questions AS C ON C.id = B.question_id
                  INNER JOIN subtopics AS D ON D.id = C.questionable_id
                  INNER JOIN topics AS E ON E.id = D.topic_id
                WHERE
                  A.user_id = id_user
                  AND A.finished_at IS NOT NULL
              ) AS aux_tb
            GROUP BY
              aux_tb.TOPIC_ID,
              aux_tb.TOPIC_NAME
            ORDER BY
              PERCENTAGE
            LIMIT
              5;
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
        $procedure1= "DROP PROCEDURE IF EXISTS `{$this->nameProcedure}`";


        DB::unprepared($procedure1);


    }
};
