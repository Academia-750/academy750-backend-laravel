<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function __construct(public string $nameProcedure = 'get_topic_questions_quantity_test_procedure')
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
            IN `id_tema` INT,
            IN `id_oposicion` INT,
            IN `num_preguntas` INT
        )
        BEGIN
            DROP
              TEMPORARY TABLE IF EXISTS tmp_topics;
            CREATE TEMPORARY TABLE tmp_topics (
              topic_id LONGTEXT,
              topic_uuid VARCHAR(36),
              nombre_del_tema VARCHAR(255),
              total_questions INT
            );
            INSERT INTO tmp_topics (
              topic_id, topic_uuid, nombre_del_tema,
              total_questions
            )
            SELECT
              TB.topic_id as topic_id
            FROM
              (
                SELECT
                  DISTINCT q.questionable_id as topic_id,
                  t.name as name_topic,
                  '' as subtopic_id,
                  q.id as id_q
                FROM
                  questions q
                  INNER JOIN topics t on t.id = q.questionable_id
                  INNER JOIN oppositionables op ON op.oppositionable_id = q.questionable_id
                WHERE
                  op.opposition_id = id_oposicion
                  AND q.questionable_type = 'App\\Models\\Topic'
                UNION
                SELECT
                  DISTINCT st.topic_id as topic_id,
                  t.name as name_topic,
                  q.questionable_id as subtopic_id,
                  q.id
                FROM
                  questions q
                  INNER JOIN subtopics st on q.questionable_id = st.id
                  INNER JOIN topics t on t.id = st.topic_id
                  INNER JOIN oppositionables op ON op.oppositionable_id = st.id
                WHERE
                  op.opposition_id = id_oposicion
                  AND q.questionable_type = 'App\\Models\\Subtopic'
              ) as TB
            WHERE
              FIND_IN_SET(TB.topic_id, topic_uuids) > 0
              AND TB.topic_id IN (
                SELECT
                  t2.id
                FROM
                  topics t2
                  INNER JOIN oppositionables o2 ON o2.oppositionable_id = t2.id
                WHERE
                  o2.opposition_id = id_oposicion
              )
            GROUP BY
              topic_id
            ORDER BY
              total_questions ASC;
            IF (
              SELECT
                COUNT(*)
              FROM
                tmp_topics
            ) > num_preguntas THEN
            SELECT
              *
            FROM
              tmp_topics
            ORDER BY
              RAND()
            LIMIT
              num_preguntas;
            ELSE
            SELECT
              *
            FROM
              tmp_topics;
            END IF;
            DROP
              TEMPORARY TABLE tmp_topics;
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