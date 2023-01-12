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
            $table->uuid('id')/*->primary()*/->comment('Identificador UUID');
            $table->string('dni', 20)->nullable()->unique()->comment('Documento Nacional de Identidad');
            $table->string('first_name', 90);
            $table->string('last_name', 90);
            $table->string('full_name');
            $table->string('phone', 25)->unique()->comment('Numero de telefono');
            $table->timestamp('last_session')->nullable()->comment('Fecha de ultimo login');
            $table->enum('state', ['enable', 'disable'])->default('enable')->comment('Cuenta -> enable=Habilitada, disabled=Deshabilitada');
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
