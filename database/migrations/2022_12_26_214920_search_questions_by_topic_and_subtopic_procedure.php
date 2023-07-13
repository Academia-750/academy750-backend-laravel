<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
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
        $procedure = "DROP PROCEDURE IF EXISTS `search_question_in_topics_and_subtopics`;
        CREATE PROCEDURE `search_question_in_topics_and_subtopics`(
          IN buscado VARCHAR(255),
          IN tema_id VARCHAR(255)
        )
        BEGIN

        SELECT q.id
        FROM questions q
        where q.questionable_id=tema_id and q.question like CONCAT('%', buscado , '%')

        union distinct
        SELECT q2.id FROM questions q2,subtopics s2
        where s2.topic_id=tema_id and q2.questionable_id=s2.id
         and (q2.question like CONCAT('%', buscado , '%') or s2.name like CONCAT('%', buscado , '%')) ;
        END";

        DB::unprepared($procedure);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (app()->environment() === 'testing') {
            return;
        }
        $procedure = "DROP PROCEDURE IF EXISTS `search_question_in_topics_and_subtopics`";
        DB::unprepared($procedure);
    }
};
