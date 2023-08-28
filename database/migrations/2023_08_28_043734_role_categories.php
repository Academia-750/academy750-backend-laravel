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
        Schema::table('roles', static function (Blueprint $table) {
            $table->tinyInteger('default_role')->default(0);
            $table->tinyInteger('protected')->default(0);
        });

        Schema::table('permissions', static function (Blueprint $table) {
            $table->string('category')->default('');
        });

        // We dont need super admin role anymore. All the super admin will become just admins
        $super_admin = DB::table('roles')->where('name', 'super-admin')->first();
        $admin = DB::table('roles')->where('name', 'admin')->first();
        $student = DB::table('roles')->where('name', 'student')->first();

        if ($super_admin && $admin) {
            DB::table('model_has_roles')->where('role_id', $super_admin->id)->update(['role_id' => $admin->id]);
            DB::table('roles')->delete($super_admin->id);
        }
        if ($admin) {
            DB::table('roles')->where('id', $admin->id)->update(['protected' => 1]);
        }

        if ($admin) {
            DB::table('roles')->where('id', $student->id)->update(['default_role' => 1]);
        }

        // We reset the permissions here. We will have new sets of permissions run by the seeder
        DB::table('role_has_permissions')->delete();
        DB::table('permissions')->delete();

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('roles', static function (Blueprint $table) {
            $table->dropColumn('default_role');
            $table->dropColumn('protected');
        });

        Schema::table('permissions', static function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }
};