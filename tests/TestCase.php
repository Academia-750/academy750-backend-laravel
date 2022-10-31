<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tests\traits\TestingWmsLogHouse;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, TestingWmsLogHouse;

    public function setUp():void{

        parent::setUp();
        $this->generateSeedersPermissionsAndRoles();
    }
}
