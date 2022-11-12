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
        Schema::create('oppositionable', function (Blueprint $table) {
            $table->id();

            $table->foreignUuid("opposition_id")
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->uuidMorphs("oppositionable");

            $table->enum('is_visible', [ 'yes', 'no' ])->comment('Estará disponible para futuros usos?')->default('yes');

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
        Schema::dropIfExists('oppositionable');
    }
};
