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

            $table->string("number_of_questions")->comment('Numero total de preguntas');

            $table->string("test_result")->comment('Calificación final de la Prueba');
            $table->enum("is_solved_test", ['yes', 'no'])->default('no')->comment('Ha sido completado la prueba?');

            $table->foreignUuid("test_type_id")
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignUuid("opposition_id")
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
