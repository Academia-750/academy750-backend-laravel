<?php

namespace Tests\Feature;


use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class WorkspaceSearchTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private $user; // Admin user

    public function setUp(): void
    {
        parent::setUp();

        $this->withHeaders([
            'Accept' => 'application/json'
        ]);

        $this->user = User::factory()->admin()->create();

        $this->actingAs($this->user);

        $this->workspaces = Workspace::factory()->count(4)->sequence(
            ['name' => 'forest'],
            ['name' => 'fire'],
            ['name' => 'water'],
            ['name' => 'trees'],
        )->type('material')->create();
    }


    /** @test */
    public function not_logged_401(): void
    {
        Auth::logout();
        $this->get("api/v1/workspace/search", [])->assertStatus(401);
    }


    /** @test */
    public function no_admin_users_200(): void
    {
        $user = User::factory()->student()->create();
        $this->actingAs($user)->get("api/v1/workspace/search", [])->assertStatus(200);

    }


    /** @test */
    public function wrong_params_422(): void
    {

        $this->get("api/v1/workspace/search?" . Arr::query(['content' => ['aa', 'bb']]))->assertStatus(422); // Not a string

        $this->get("api/v1/workspace/search?" . Arr::query(['limit' => 'not_numbber']))->assertStatus(422); // limit wrong type
        $this->get("api/v1/workspace/search?" . Arr::query(['limit' => -1]))->assertStatus(422); // limit negative
        $this->get("api/v1/workspace/search?" . Arr::query(['limit' => 50]))->assertStatus(422); // limit too big
    }



    /** @test */
    public function empty_search_200(): void
    {
        $this->get("api/v1/workspace/search?" . Arr::query([]))->assertStatus(200);
    }

    /** @test */
    public function find_workspace_by_full_name_200(): void
    {
        $workspace = $this->workspaces[0];
        $res = $this->get("api/v1/workspace/search?" . Arr::query(['content' => $workspace->name]))->assertStatus(200);

        $this->assertEquals($res['results'][0]['id'], $workspace->id);
        $this->assertEquals($res['results'][0]['name'], $workspace->name);
    }

    /** @test */
    public function find_workspace_by_partial_name_200(): void
    {
        $workspace = $this->workspaces[0];
        $res = $this->get("api/v1/workspace/search?" . Arr::query(['content' => substr($workspace->name, 0, 3)]))->assertStatus(200);
        $this->assertEquals($res['results'][0]['id'], $workspace->id);

        $workspace = $this->workspaces[2];
        $res = $this->get("api/v1/workspace/search?" . Arr::query(['content' => substr($workspace->name, 0, 3)]))->assertStatus(200);
        $this->assertEquals($res['results'][0]['id'], $workspace->id);
    }

    /** @test */
    public function limit_is_working(): void
    {
        $res = $this->get("api/v1/workspace/search?" . Arr::query(['limit' => 1]))->assertStatus(200);
        $this->assertEquals(count($res['results']), 1);

        $res = $this->get("api/v1/workspace/search?" . Arr::query(['limit' => 3]))->assertStatus(200);
        $this->assertEquals(count($res['results']), 3);
    }

}