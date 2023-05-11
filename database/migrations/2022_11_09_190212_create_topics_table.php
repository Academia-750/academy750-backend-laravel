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
    public function up(): void
    {
        Schema::create('topics', static function (Blueprint $table) {
            $table->id();
$table->uuid()->comment('Identificador UUID');

            $table->string("name");
            // Relationship for group of topics
            $table->foreignId("topic_group_id")
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->enum('is_available', [ 'yes', 'no' ])->comment('Estará disponible para futuros usos?')->default('yes');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('topics');
    }
};
