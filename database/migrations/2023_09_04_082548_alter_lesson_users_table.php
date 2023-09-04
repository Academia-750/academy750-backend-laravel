<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lesson_user', static function (Blueprint $table) {
            $table->boolean('will_join')->comment('A student marks this flag to say he plans to join the lesson')->default(0);
            $table->boolean('has_joined')->comment('A teacher can mark this flag to say who has actually joined')->default(0);
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lesson_user', static function (Blueprint $table) {
            $table->dropColumn('will_join');
            $table->dropColumn('has_joined');
        });
    }
};