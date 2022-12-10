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
            $table->string("question")->comment("La pregunta");
            $table->text("reason")->nullable()->comment("La explicacion");
            $table->enum('is_visible', [ 'yes', 'no' ])->comment('Está visible?')->default('yes');

            $table->enum("has_been_used_test", ['yes', 'no'])->default('no')->comment('Ha sido mostrada o usada en la prueba?');
            $table->enum("has_been_used_card_memory", ['yes', 'no'])->default('no')->comment('Ha sido mostrada o usada en la tarjeta de memoria?');

            $table->uuidMorphs('questionable');

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
