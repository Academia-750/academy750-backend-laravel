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
        Schema::create('testables', function (Blueprint $table) {
            //$table->uuid('id')->primary()->comment('Identificador UUID');
            $table->id();

            $table->uuidMorphs('testable');

            $table->foreignUuid('test_id')
                ->comment('El ID del Test al que pertenece el tema o subtema')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();


            /*$table->foreignUuid('topic_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();*/

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
        Schema::dropIfExists('test_topic');
    }
};
