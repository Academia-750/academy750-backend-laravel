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
        Schema::create('questions', function (Blueprint $table) {
            $table->uuid('id')->primary()->comment('Identificador UUID');
            $table->uuidMorphs('questionable');

            $table->string("question")->comment("La pregunta");
            $table->text("reason")->nullable()->comment("La explicacion");
            $table->enum('is_question_binary_alternatives', [ 'yes', 'no' ])->comment('Es una pregunta con 2 alternativas?')->default('no');
            $table->enum('is_visible', [ 'yes', 'no' ])->comment('Está visible?')->default('yes');
            $table->enum("its_for_test", ['yes', 'no'])->default('yes')->comment('¿Es una pregunta para test?');
            $table->enum("its_for_card_memory", ['yes', 'no'])->default('no')->comment('¿Es una pregunta para tarjeta de memoria?');
            //$table->enum("its_being_used_tests", ['yes', 'no'])->default('no')->comment('¿Está siendo usada en Cuestionarios?');


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
        Schema::dropIfExists('questions');
    }
};
