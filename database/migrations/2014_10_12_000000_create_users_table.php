<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            /*$table->string('url_photo')->nullable()->default(null);*/
            $table->uuid('id')->primary()->comment('Identificador UUID');
            $table->string('dni', 20)->unique()->comment('Documento Nacional de Identidad');
            $table->string('first_name', 70);
            $table->string('last_name', 70);
            $table->string('phone', 25)->comment('Numero de telefono');
            $table->timestamp('last_session')->nullable()->comment('Fecha de ultimo login');
            $table->smallInteger('state')->default(1)->comment('0=disabled, 1=enabled');
            $table->string('email', 120)->unique();
            $table->timestamp('email_verified_at')->nullable()->comment('Está el correo verificado?');
            $table->string('password', 80);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
