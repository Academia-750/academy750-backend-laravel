<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function __construct(Public string $nameProcedure = 'get_topic_questions_quantity_card_memory_procedure'){}

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
            IN `id_oposicion` INT
        )
        BEGIN
            SELECT
              id_tema AS 'topic_id',
              COUNT(*) AS 'total_questions'
            FROM
              questions p
            WHERE
              p.question_in_edit_mode = 'no'
              AND p.is_visible = 'yes'
              AND p.questionable_id in (
                SELECT
                  t.id
                FROM
                  topics t
                WHERE
                  t.id = id_tema
                UNION
                SELECT
                  s.id
                FROM
                  subtopics s,
                  oppositionables o
                WHERE
                  o.opposition_id = id_oposicion
                  and o.oppositionable_id = s.id
                  and s.topic_id = id_tema
              )
              AND p.its_for_card_memory = 'yes';
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
