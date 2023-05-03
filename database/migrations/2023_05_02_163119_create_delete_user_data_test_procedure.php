<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public string $nameProcedure;

    public function __construct()
    {
        $this->nameProcedure = 'delete_user_data_test';
    }

    public function up(): void
    {
        $procedure = "DROP PROCEDURE IF EXISTS `{$this->nameProcedure}`;
        CREATE PROCEDURE `{$this->nameProcedure}`(
            IN `id_usuario` VARCHAR(36),
        )

        BEGIN DELETE te
        FROM
          tests t
          INNER JOIN testables te on te.test_id = t.id
        WHERE
          t.user_id = id_usuario;
        DELETE qt
        FROM
          tests t
          INNER JOIN question_test qt on qt.test_id = t.id
        WHERE
          t.user_id = id_usuario;
        DELETE FROM
          tests
        WHERE
          user_id = id_usuario;
        DELETE FROM
          questions_used_test
        WHERE
          user_id = id_usuario;
        END
        ";

        DB::unprepared($procedure);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        $procedure= "DROP PROCEDURE IF EXISTS `{$this->nameProcedure}`";

        DB::unprepared($procedure);
    }
};
