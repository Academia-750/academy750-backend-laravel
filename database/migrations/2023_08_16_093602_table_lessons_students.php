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
        Schema::create('lesson_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lesson_id')->comment('References the the Lessons Table');
            $table->foreign('lesson_id')->references('id')->on('lessons')->onDelete('CASCADE');
            $table->unsignedBigInteger('user_id')->comment('References the the Users Table');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('CASCADE');
            $table->string('group_name')->nullable()->comment('Soft relation, not a Foreigner Key');
            $table->unsignedBigInteger('group_id')->nullable()->comment('Soft relation, not a Foreigner Key');
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
        Schema::dropIfExists('lesson_user');
    }
};