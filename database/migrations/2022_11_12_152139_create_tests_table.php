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
            $table->uuid('id')->primary()->comment('Identificador UUID');

            $table->string("number_of_questions_requested")->comment('Numero total de preguntas solicitidas');
            $table->string("number_of_questions_generated")->comment('Numero total de preguntas generadas');

            $table->string("test_result")->comment('Calificación final de la Prueba');
            $table->enum("is_solved_test", ['yes', 'no'])->default('no')->comment('Ha sido completado la prueba?');

            $table->enum('test_type', ['test', 'card_memory'])->comment('Tipo de cuestionario');

            $table->foreignUuid("opposition_id")
                ->comment('El ID de la Oposición')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignUuid("user_id")


                ->comment("El alumno que resolverá la prueba")
                ->nullable()
                ->constrained()
                ->nullOnDelete();

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
