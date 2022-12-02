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
            $table->string("reason")->nullable()->comment("La explicacion");
            $table->enum('is_visible', [ 'yes', 'no' ])->comment('Está visible?')->default('yes');

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
