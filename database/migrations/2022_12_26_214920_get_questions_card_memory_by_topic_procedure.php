<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $procedure= "DROP PROCEDURE IF EXISTS `get_questions_card_memory_by_topic`;
        CREATE PROCEDURE `get_questions_card_memory_by_topic`(
            IN `id_tema` VARCHAR(255),
            IN `id_oposicion` VARCHAR(255),
            IN `id_usuario` VARCHAR(255),
            IN `cantidad` INT
        )
        BEGIN

                DECLARE c INTEGER;

                        SET c := (select count(*)
                             from questions p
                             WHERE p.question_in_edit_mode='no' AND p.is_visible='yes' and p.questionable_id in
                             (select t.id from topics t where t.id=id_tema union select s.id from subtopics s, oppositionables o where o.opposition_id=id_oposicion and o.oppositionable_id=s.id and s.topic_id=id_tema)
                              AND p.id not in
                              (SELECT question_id FROM question_test q, tests t where t.user_id=id_usuario and t.id=q.test_id and t.test_type='card_memory' and q.have_been_show_card_memory='yes')
                              AND p.its_for_card_memory='yes');

                        IF c < 1 THEN

                        update question_test m, questions q, tests t  SET m.have_been_show_test='no' WHERE m.question_id=q.id AND
                              q.questionable_id IN (select t.id from topics t where t.id=id_tema union select s.id from subtopics s, oppositionables o where o.opposition_id=id_oposicion and o.oppositionable_id=s.id and s.topic_id=id_tema) and
                              t.user_id=id_usuario AND t.id=m.test_id AND t.test_type='card_memory' AND q.its_for_card_memory='yes';

                        END IF;



                        SELECT p.id from questions p
                             WHERE p.question_in_edit_mode='no' AND p.is_visible='yes' AND p.questionable_id in
                             (select t.id from topics t where t.id=id_tema union select s.id from subtopics s, oppositionables o where o.opposition_id=id_oposicion and o.oppositionable_id=s.id and s.topic_id=id_tema)
                              AND p.id not in
                              (SELECT question_id FROM question_test q, tests t where t.user_id=id_usuario and t.id=q.test_id and t.test_type='card_memory' and q.have_been_show_card_memory='yes')
                              AND p.its_for_card_memory='yes'
                          ORDER BY RAND() LIMIT cantidad;

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
        $procedure = "DROP PROCEDURE IF EXISTS `get_questions_card_memory_by_topic";
        DB::unprepared($procedure);
    }
};
