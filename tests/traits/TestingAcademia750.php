<?php

namespace Tests\traits;

use Database\Seeders\DatabaseSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Support\Facades\Artisan;

trait TestingAcademia750
{
    public function generateSeedersPermissionsAndRoles(): void
    {
        $this->seed(RoleSeeder::class);
        $this->seed(PermissionSeeder::class);
    }

    public function clearCacheApp(): void
    {
        Artisan::call('cache:clear', ['store' => 'redis']);
    }

}
