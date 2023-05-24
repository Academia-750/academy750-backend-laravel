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
        Schema::create('images', function (Blueprint $table) {
            $table->id();
            $table->uuid()->comment('Identificador UUID');

            $table->text("path")->comment('La dirección en la que está almacenada la imagen');
            $table->enum("type_path", [ 'local', 'url' ])->default('url')->comment('Es una imagen guardada en Storage o una URL externa?');

            $table->uuidMorphs('imageable');

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
        Schema::dropIfExists('images');
    }
};
