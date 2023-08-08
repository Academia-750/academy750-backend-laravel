<?php

namespace Tests\Feature;

use App\Models\Material;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;


class DeleteWorkspaceTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private $user;

    private $workspace;


    public function setUp(): void
    {
        parent::setUp();

        $this->withHeaders([
            'Accept' => 'application/json'
        ]);

        $this->user = User::factory()->admin()->create();

        $this->actingAs($this->user);

        $this->workspace = Workspace::factory()->type('material')->create();
    }


    /** @test */
    public function id_required_405(): void
    {
        $this->delete("api/v1/workspace")->assertStatus(405);
    }

    /** @test */
    public function not_logged_401(): void
    {
        Auth::logout();
        $this->delete("api/v1/workspace/{$this->workspace->id}")->assertStatus(401);
    }

    /** @test */
    public function only_admin_403(): void
    {
        $user = User::factory()->student()->create();
        $this->actingAs($user)->delete("api/v1/workspace/{$this->workspace->id}")->assertStatus(403);
    }

    /** @test */
    public function workspace_not_found_404(): void
    {
        $this->delete("api/v1/workspace/99")->assertStatus(404);
    }


    /** @test */
    public function delete_workspace_200(): void
    {

        $this->delete("api/v1/workspace/{$this->workspace->id}")->assertStatus(200);
        $workspace = Workspace::find($this->workspace->id);

        $this->assertNull($workspace);
    }

    /** @test */
    public function delete_workspace_and_materials_200(): void
    {

        Material::factory()->state(['workspace_id' => $this->workspace->id])->count(4)->create();

        $this->delete("api/v1/workspace/{$this->workspace->id}")->assertStatus(200);

        $this->assertEmpty(Material::all());
    }

}