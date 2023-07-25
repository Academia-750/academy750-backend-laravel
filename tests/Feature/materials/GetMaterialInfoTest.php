<?php

namespace Tests\Feature;

use App\Models\Material;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;


class GetMaterialInfoTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private $user;
    private $material;


    public function setUp(): void
    {
        parent::setUp();

        $this->withHeaders([
            'Accept' => 'application/json'
        ]);

        $this->user = User::factory()->admin()->create();

        $this->actingAs($this->user);

        $this->material = Material::factory()->withTags()->withUrl()->create();
    }


    /** @test */
    public function not_logged_401(): void
    {
        Auth::logout();
        $this->get("api/v1/material/{$this->material->id}/info")->assertStatus(401);
    }

    /** @test */
    public function only_admin_403(): void
    {
        $user = User::factory()->student()->create();
        $this->actingAs($user)->get("api/v1/material/{$this->material->id}/info")->assertStatus(403);
    }


    /** @test */
    public function material_not_found_404(): void
    {
        $this->get("api/v1/material/99/info")->assertStatus(404);
    }


    /** @test */
    public function get_material_info_200(): void
    {

        $response = $this->get("api/v1/material/{$this->material->id}/info", )->assertStatus(200)->json();

        $this->assertEquals($response['result']['id'], $this->material->id);
        $this->assertEquals($response['result']['tags'], $this->material->tags);
        $this->assertEquals($response['result']['url'], $this->material->url);
        $this->assertEquals($response['result']['type'], $this->material->type);
        $this->assertEquals($response['result']['workspace_id'], $this->material->workspace_id);
    }

}