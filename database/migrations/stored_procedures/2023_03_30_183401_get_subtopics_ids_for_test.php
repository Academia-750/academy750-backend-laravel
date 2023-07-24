<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function __construct(public string $nameProcedure = 'get_subtopics_ids_for_test_procedure')
    {
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $procedure1 = "DROP PROCEDURE IF EXISTS `{$this->nameProcedure}`;
        CREATE PROCEDURE `{$this->nameProcedure}`(
            IN `__id_oposicion` INT,
            IN `__temas_ids` TEXT
        )
        BEGIN
            SELECT
              o.oppositionable_id
            FROM
              oppositionables o
              JOIN subtopics s ON o.oppositionable_id = s.id
            WHERE
              o.opposition_id = __id_oposicion
              AND FIND_IN_SET(s.topic_id, __temas_ids);
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