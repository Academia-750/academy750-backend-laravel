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
        $procedure1= "DROP PROCEDURE IF EXISTS `get_questions_by_status_and_period_of_tests_student`;
        CREATE PROCEDURE `get_questions_by_status_and_period_of_tests_student`(
            IN `id_user` VARCHAR(255),
            IN `status_question` VARCHAR(50),
            IN `min_date` DATE,
            IN `max_date` DATE
        )
        BEGIN
        SELECT q.question_id
        from question_test q, tests t
        where t.user_id=id_user and t.id=q.test_id
        AND (DATE_FORMAT(t.finished_at,'%Y-%m-%d') BETWEEN min_date and max_date)
        AND q.status_solved_question=status_question;
        END";

        DB::unprepared($procedure1);

        $procedure2 = "DROP PROCEDURE IF EXISTS `get_tests_of_student_by_period_date`;
        CREATE PROCEDURE `get_tests_of_student_by_period_date`(
            IN `id_user` VARCHAR(255),
            IN `min_date` DATE,
            IN `max_date` DATE
        )
        BEGIN

        SELECT t.id FROM tests t WHERE t.user_id=id_user AND (DATE_FORMAT(t.finished_at,'%Y-%m-%d') BETWEEN min_date and max_date) and is_solved_test='yes' and test_type='test';

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
        $procedure1= "DROP PROCEDURE IF EXISTS `get_questions_by_status_and_period_of_tests_student`";
        $procedure2= "DROP PROCEDURE IF EXISTS `get_tests_of_student_by_period_date`";


        DB::unprepared($procedure1);
        DB::unprepared($procedure2);


    }
};
