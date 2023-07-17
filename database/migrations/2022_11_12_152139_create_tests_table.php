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
        Schema::create('tests', function (Blueprint $table) {
            $table->id();
            $table->uuid()->comment('Identificador UUID');

            $table->string("number_of_questions_requested")->comment('Numero total de preguntas solicitidas');
            $table->string("number_of_questions_generated")->comment('Numero total de preguntas generadas');

            $table->string("test_result")->default('0')->comment('Calificación final de la Prueba');
            $table->string("total_questions_correct")->default('0')->comment('Número total de preguntas correctas');
            $table->string("total_questions_wrong")->default('0')->comment('Número total de preguntas incorrectas');
            $table->string("total_questions_unanswered")->default('0')->comment('Número total de preguntas no respondidas');

            $table->enum("is_solved_test", ['yes', 'no'])->default('no')->comment('Ha sido completado la prueba?');

            $table->enum('test_type', ['test', 'card_memory'])->comment('Tipo de cuestionario');

            $table->foreignId("opposition_id")
                ->comment('El ID de la Oposición')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId("user_id")
                ->comment("El alumno que resolverá la prueba")
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->timestamp('finished_at', 0)
                ->nullable();

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
        Schema::dropIfExists('tests');
    }
};
