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
        $procedure1= "DROP PROCEDURE IF EXISTS `get_topic_questions_quantity_card_memory`;
        CREATE PROCEDURE `get_topic_questions_quantity_card_memory`(
            IN `id_tema` VARCHAR(255),
            IN `id_oposicion` VARCHAR(255)
        )
        BEGIN
        select id_tema AS 'topic_id',COUNT(*) AS 'total_questions'
        from questions p
        WHERE p.question_in_edit_mode='no' AND p.is_visible='yes' AND p.questionable_id in
        (select t.id from topics t where t.id=id_tema union select s.id from subtopics s, oppositionables o where o.opposition_id=id_oposicion and o.oppositionable_id=s.id and s.topic_id=id_tema)
        AND p.its_for_card_memory='yes';
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
        $procedure1= "DROP PROCEDURE IF EXISTS `get_topic_questions_quantity_card_memory`";

        DB::unprepared($procedure1);


    }
};
