<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tests\traits\TestingAcademia750;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, TestingAcademia750;

    public function setUp():void{

        parent::setUp();
        $this->clearCacheApp();
        $this->generateSeedersPermissionsAndRoles();
    }
}
