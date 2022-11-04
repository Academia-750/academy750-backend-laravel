<?php

namespace Tests;

use App\Models\Role;
use Spatie\Permission\Models\Role as RoleSpatie;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tests\traits\TestingAcademia750;

abstract class TestCase extends BaseTestCase
{
    public RoleSpatie $roleAdmin;
    public RoleSpatie $roleStudent;

    use CreatesApplication, TestingAcademia750;

    public function setUp():void{
        parent::setUp();
        $this->clearCacheApp();
        $this->generateSeedersPermissionsAndRoles();

        $this->roleAdmin = Role::query()->where('name', '=', 'admin')->first();
        $this->roleStudent = Role::query()->where('name', '=', 'student')->first();
    }
}
