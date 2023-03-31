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
        $procedure1 = "DROP PROCEDURE IF EXISTS `get_subtopics_ids_for_test`;
        CREATE PROCEDURE `get_subtopics_ids_for_test`(
            IN `oposicion` TEXT,
            IN `temas` TEXT
        )
        BEGIN
        SELECT o.oppositionable_id FROM oppositionables o JOIN subtopics s ON o.oppositionable_id = s.id WHERE o.opposition_id=oposicion AND FIND_IN_SET(s.topic_id,temas);
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
        $procedure1 = "DROP PROCEDURE IF EXISTS `get_subtopics_ids_for_test`";

        DB::unprepared($procedure1);
    }
};
