<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function __construct(Public string $nameProcedure = 'complete_questions_test_by_topic_procedure'){}

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $procedure1= "DROP PROCEDURE IF EXISTS `{$this->nameProcedure}`;
        CREATE PROCEDURE `{$this->nameProcedure}`(
            IN `id_tema` INT,
            IN `id_oposicion` INT,
            IN `id_usuario` INT,
            IN `cantidad` INT,
            IN `excluidos` TEXT
        )
        BEGIN
            update
              question_test m,
              questions q,
              tests t
            SET
              m.have_been_show_test = 'no'
            WHERE
              m.question_id = q.id
              AND q.questionable_id IN (
                select
                  t.id
                from
                  topics t
                where
                  t.id = id_tema
                union
                select
                  s.id
                from
                  subtopics s,
                  oppositionables o
                where
                  o.opposition_id = id_oposicion
                  and o.oppositionable_id = s.id
                  and s.topic_id = id_tema
              )
              and t.user_id = id_usuario
              AND t.id = m.test_id
              AND t.test_type = 'test'
              AND q.its_for_test = 'yes';
            SELECT
              p.id
            from
              questions p
            WHERE
              p.question_in_edit_mode = 'no'
              AND p.is_visible = 'yes'
              AND p.questionable_id in (
                select
                  t.id
                from
                  topics t
                where
                  t.id = id_tema
                union
                select
                  s.id
                from
                  subtopics s,
                  oppositionables o
                where
                  o.opposition_id = id_oposicion
                  and o.oppositionable_id = s.id
                  and s.topic_id = id_tema
              )
              AND p.id not in (
                SELECT
                  question_id
                FROM
                  question_test q,
                  tests t
                where
                  t.user_id = id_usuario
                  and t.id = q.test_id
                  and t.test_type = 'test'
                  and q.have_been_show_test = 'yes'
              )
              AND NOT FIND_IN_SET(p.id, excluidos)
              AND p.its_for_test = 'yes'
            ORDER BY
              RAND()
            LIMIT
              cantidad;
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
        $dropProcedure= "DROP PROCEDURE IF EXISTS `{$this->nameProcedure}`";

        DB::unprepared($dropProcedure);


    }
};
