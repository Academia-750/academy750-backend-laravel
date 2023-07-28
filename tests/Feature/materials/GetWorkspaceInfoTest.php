<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;


class GetWorkspaceInfoTest extends TestCase
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
    public function not_logged_401(): void
    {
        Auth::logout();
        $this->get("api/v1/workspace/{$this->workspace->id}/info")->assertStatus(401);
    }

    /** @test */
    public function only_admin_403(): void
    {
        $user = User::factory()->student()->create();
        $this->actingAs($user)->get("api/v1/workspace/{$this->workspace->id}/info")->assertStatus(403);
    }


    /** @test */
    public function group_not_found(): void
    {
        $this->get("api/v1/workspace/99/info")->assertStatus(404);
    }

    /** @test */
    public function get_workspace_200(): void
    {
        $data = $this->get("api/v1/workspace/{$this->workspace->id}/info")->assertStatus(200)->json();

        $this->assertEquals($data['result']['id'], $this->workspace->id);
        $this->assertEquals($data['result']['name'], $this->workspace->name);
        $this->assertEquals($data['result']['type'], $this->workspace->type);
        $this->assertEquals($data['result']['tags'], $this->workspace->tags);
        $this->assertEquals($data['result']['materials_count'], 0);
    }

    /** @test */
    public function get_material_count_200(): void
    {
        // TODO when we get materials CRUD
        $this->markTestSkipped();
        $data = $this->get("api/v1/workspace/{$this->workspace->id}/info")->assertStatus(200)->json();

        $this->assertEquals($data['result']['materials_count'], 3);
    }

}