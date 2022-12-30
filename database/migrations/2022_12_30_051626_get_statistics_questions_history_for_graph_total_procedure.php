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
        $procedure1= "DROP PROCEDURE IF EXISTS `getResults_bytopic_total`;
        CREATE PROCEDURE `getResults_bytopic_total`(
            IN `id_topic` VARCHAR(255),
            IN `id_user` VARCHAR(255)
        )
        BEGIN

        SELECT id_topic as 'topic_id',
        IFNULL(SUM(if(q.status_solved_question = 'correct', 1, 0)),0) AS 'correct',
        IFNULL(SUM(if(q.status_solved_question = 'wrong', 1, 0)),0) AS 'wrong',
        IFNULL(SUM(if(q.status_solved_question = 'unanswered', 1, 0)),0) AS 'unanswered'
        FROM question_test q, tests t, questions p
        WHERE q.test_id=t.id and
        t.user_id=id_user and t.test_type='test' and p.id=q.question_id and p.questionable_id in
        (SELECT id FROM `subtopics` s WHERE s.topic_id=id_topic union select id from topics t where t.id=id_topic);

        END";

        DB::unprepared($procedure1);

        $procedure2 = "DROP PROCEDURE IF EXISTS `get_topics_by_test_of_student`;
        CREATE PROCEDURE `get_topics_by_test_of_student`(
            IN `id_user` VARCHAR(255)
        )
        BEGIN

        SELECT distinct(l.testable_id) FROM testables l, tests t where t.user_id=id_user and l.testable_type='App\Models\Topic';

        END";

        DB::unprepared($procedure2);


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $procedure1= "DROP PROCEDURE IF EXISTS `getResults_bytopic_total`";
        $procedure2= "DROP PROCEDURE IF EXISTS `get_topics_by_test_of_student`";


        DB::unprepared($procedure1);
        DB::unprepared($procedure1);


    }
};
