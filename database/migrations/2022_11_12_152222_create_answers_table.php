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
        Schema::create('answers', function (Blueprint $table) {
            $table->id();
            $table->uuid()->comment('Identificador UUID');

            $table->text("answer")->comment('El texto de la respuesta');
            $table->enum("is_grouper_answer", [ 'yes', 'no' ])->default('no')->comment('Es respuesta agrupadora?');

            $table->enum("is_correct_answer", ['yes', 'no'])->default('no')->comment('Esta es la respuesta correcta de una pregunta?');

            $table->foreignId('question_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

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
        Schema::dropIfExists('answers');
    }
};
