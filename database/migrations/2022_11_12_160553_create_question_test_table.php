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
        Schema::create('question_test', function (Blueprint $table) {
            $table->id();

            $table->foreignId('test_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('question_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->enum("have_been_show_test", ['yes', 'no'])->default('no')->comment('Ha sido mostrada o usada en la prueba?');
            $table->enum("have_been_show_card_memory", ['yes', 'no'])->default('no')->comment('Ha sido mostrada o usada en la tarjeta de memoria?');

            $table->foreignId("answer_id")
                ->nullable()
                ->comment('El ID de la respuesta seleccionada por el alumno

                para esta preguntada')
                ->constrained()
                ->nullOnDelete();

            $table->enum("status_solved_question", ['unanswered', 'wrong', 'correct'])->default('unanswered')->comment('Estado de resolución de la pregunta');

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
        Schema::dropIfExists('question_test');
    }
};
