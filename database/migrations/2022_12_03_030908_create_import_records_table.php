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
        Schema::create('import_records', function (Blueprint $table) {
            $table->uuid('id')->primary()->comment('Identificador UUID');
            $table->string("number_of_row");
            $table->string("reference_number");
            $table->enum("has_errors", ['yes', 'no']);
            $table->json("errors_validation");
            $table->foreignUuid('import_process_id')
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
        Schema::dropIfExists('import_records');
    }
};
