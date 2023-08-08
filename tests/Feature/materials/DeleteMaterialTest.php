<?php

namespace Tests\Feature;

use App\Models\Material;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;


class DeleteMaterialTest extends TestCase
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

        $this->material = Material::factory()->create();
    }


    /** @test */
    public function not_logged_401(): void
    {
        Auth::logout();
        $this->delete("api/v1/material/{$this->material->id}")->assertStatus(401);
    }

    /** @test */
    public function only_admin_403(): void
    {
        $user = User::factory()->student()->create();
        $this->actingAs($user)->delete("api/v1/material/{$this->material->id}")->assertStatus(403);
    }


    /** @test */
    public function material_not_found_404(): void
    {
        $this->delete("api/v1/material/99")->assertStatus(404);
    }


    /** @test */
    public function delete_material_200(): void
    {

        $this->delete("api/v1/material/{$this->material->id}", )->assertStatus(200)->json();

        $material = Material::find($this->material->id);
        $this->assertEquals($material, null);
    }

}