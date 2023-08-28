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
        Schema::create('lesson_material', function (Blueprint $table) {
            $table->unsignedBigInteger('lesson_id')->comment('References the the Lessons Table');
            $table->foreign('lesson_id')->references('id')->on('lessons')->onDelete('CASCADE');
            $table->unsignedBigInteger('material_id')->comment('References the the Materials Table');
            $table->foreign('material_id')->references('id')->on('materials')->onDelete('CASCADE');
            $table->timestamps();
            $table->unique(['lesson_id', 'material_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lesson_material');
    }
};