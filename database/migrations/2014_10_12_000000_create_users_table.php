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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('identification_number',10)->unique();
            $table->string('name',25);
            $table->string('last_name',25);
            $table->timestamp('last_session')->nullable();
            $table->string('number_phone',20)->nullable()->unique();
            $table->string('email')->unique();
            $table->smallInteger('state')->default(1)->comment('0=disabled, 1=enabled');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
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
};
