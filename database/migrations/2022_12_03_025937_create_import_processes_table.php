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
        Schema::create('import_processes', function (Blueprint $table) {
            $table->uuid('id')->primary()->comment('Identificador UUID');
            //$table->softDeletes();
            $table->text("name_file");
            $table->foreignUuid('user_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->string("total_number_of_records")->nullable();
            $table->string("total_number_failed_records")->nullable();
            $table->string("total_number_successful_records")->nullable();
            $table->enum("status_process_file", ['failed', 'pending', 'complete'])->default('pending');

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
        Schema::dropIfExists('import_processes');
    }
};
