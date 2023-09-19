<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('opposition_user', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->comment('References the the users Table');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('CASCADE');
            $table->unsignedBigInteger('opposition_id')->comment('References the the oppositions Table');
            $table->foreign('opposition_id')->references('id')->on('oppositions')->onDelete('CASCADE');

            $table->primary(['user_id', 'opposition_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('opposition_user');
    }
};