<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    private $nameProcedure = "get_available_oppositions_by_user";

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
        $procedure1 = "DROP PROCEDURE IF EXISTS `{$this->nameProcedure}`;
        CREATE PROCEDURE `{$this->nameProcedure}`(
               IN `id_usuario` INT
        )
        BEGIN
            SELECT opposition_id
            FROM opposition_user ou
        INNER JOIN oppositions o ON o.id = ou.opposition_id
            WHERE ou.user_id = id_usuario
            AND o.is_available = 'yes';
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
        $procedure1 = "DROP PROCEDURE IF EXISTS `{$this->nameProcedure}`";


        DB::unprepared($procedure1);


    }
};