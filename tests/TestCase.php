<?php

namespace Tests;

use App\Models\Role;
use Spatie\Permission\Models\Role as RoleSpatie;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tests\traits\TestingAcademia750;

abstract class TestCase extends BaseTestCase
{
    protected $valid_string_input = ['Value', 'Value with space', 'Under_score', 'minus_sign', '999', 'spanish chars áéíóúÁÉÍÓÚñÑ'];
    protected $pagination_wrong_inputs = [
        ['order' => 0],
        ['order' => 2],
        ['offset' => -10],
        ['limit' => -10],
        ['orderBy' => 'random']
    ];

    use CreatesApplication, TestingAcademia750;

    public function setUp(): void
    {
        parent::setUp();
        $this->clearCacheApp();
        $this->generateSeedersPermissionsAndRoles();
    }

    /**
     * Helper
     */
    public function map($array, $key)
    {
        return array_map(function ($data) use ($key) {
            return $data[$key];
        }, $array);
    }
}