<?php

namespace Tests\traits;

use Database\Seeders\DatabaseSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\PermissionsSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Support\Facades\Artisan;

trait TestingWmsLogHouse
{
    public function generateSeedersPermissionsAndRoles(){

        /*Artisan::call('cache:clear', ['redis']);
        Artisan::call('config:clear');
        Artisan::call('route:clear');*/
        $this->seed(RoleSeeder::class);
        $this->seed(PermissionSeeder::class);
    }

}
