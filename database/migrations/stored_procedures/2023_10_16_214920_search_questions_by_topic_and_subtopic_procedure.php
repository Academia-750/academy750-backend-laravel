<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function __construct(public string $nameProcedure = 'search_question_in_topics_and_subtopics_procedure')
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
        $procedure = "DROP PROCEDURE IF EXISTS `{$this->nameProcedure}`;
        CREATE PROCEDURE `{$this->nameProcedure}`(
          IN buscado VARCHAR(255),
          IN tema_id INT
        )
        BEGIN
            SELECT
              q.id
            FROM
              questions q
            where
              q.questionable_id = tema_id
              and q.question like CONCAT('%', buscado, '%')  COLLATE utf8mb4_general_ci
            union
              distinct
            SELECT
              q2.id
            FROM
              questions q2,
              subtopics s2
            where
              s2.topic_id = tema_id
              and q2.questionable_id = s2.id
              and (
                q2.question like CONCAT('%', buscado, '%')  COLLATE utf8mb4_general_ci
                or s2.name like CONCAT('%', buscado, '%')  COLLATE utf8mb4_general_ci
              );
            END
            ";

        DB::unprepared($procedure);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $procedure = "DROP PROCEDURE IF EXISTS `{$this->nameProcedure}`";
        DB::unprepared($procedure);
    }
};