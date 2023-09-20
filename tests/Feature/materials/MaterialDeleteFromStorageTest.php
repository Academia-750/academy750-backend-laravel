<?php

namespace Tests\Feature;

use App\Core\Resources\Storage\Services\DummyStorage;
use App\Models\Material;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

/**
 * Here we just test the specific model function
 */
class MaterialDeleteFromStorageTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;




    public function setUp(): void
    {
        parent::setUp();

    }


    /** @test */
    public function empty_url_204(): void
    {
        $material = Material::factory()->make(['type' => 'material', 'url' => '']);
        $response = Material::deleteFromStorage($material);
        $this->assertEquals($response['status'], 204);
    }


    /** @test */
    public function recording_type_204(): void
    {
        $material = Material::factory()->make(['type' => 'recording', 'url' => $this->faker->url()]);
        $response = Material::deleteFromStorage($material);
        $this->assertEquals($response['status'], 204);
    }

}