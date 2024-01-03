<?php

use App\Models\Material;
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
        Schema::table('materials', static function (Blueprint $table) {
            $table->tinyInteger('watermark')->comment('Flag to indicate which file needs to be watermarked')->default(0);
        });

        Material::query()->where('type', 'material')->update(['watermark' => 1]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('materials', static function (Blueprint $table) {
            $table->dropColumn('watermark');
        });
    }
};
