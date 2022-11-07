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
        Schema::create('oppositions', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('period', 50)->nullable()->default(null);
            $table->enum('is_visible', [ 'yes', 'no' ])->default('yes');
            $table->softDeletes();

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
        Schema::dropIfExists('oppositions');
    }
};
