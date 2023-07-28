<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;


class EditWorkspaceTest extends TestCase
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
        Auth::logout();
        $this->put("api/v1/workspace")->assertStatus(405);
    }

    /** @test */
    public function not_logged_401(): void
    {
        Auth::logout();
        $this->put("api/v1/workspace/{$this->workspace->id}")->assertStatus(401);
    }

    /** @test */
    public function workspace_not_found_404(): void
    {
        $this->put("api/v1/workspace/99", ['name' => $this->faker()->word()])->assertStatus(404);
    }

    /** @test */
    public function only_admin_403(): void
    {
        $user = User::factory()->student()->create();
        $this->actingAs($user)->put("api/v1/workspace/{$this->workspace->id}")->assertStatus(403);
    }

    /** @test */
    public function wrong_params_422(): void
    {
        $this->put("api/v1/workspace/{$this->workspace->id}", [])->assertStatus(422);
        $this->put("api/v1/workspace/{$this->workspace->id}", ['name' => ''])->assertStatus(422);
        $this->put("api/v1/workspace/{$this->workspace->id}", ['name' => 'Others!'])->assertStatus(422);
    }
    /** @test */
    public function edit_workspace_200(): void
    {
        $name = $this->faker->word();
        $data = $this->put("api/v1/workspace/{$this->workspace->id}", ['name' => $name])->assertStatus(200)->json();

        $this->assertNotNull($data['result']['id']);
        $this->assertEquals($data['result']['name'], $name);

    }

    /** @test */
    public function name_format_200(): void
    {
        array_map(function ($valid_string) {
            $this->put("api/v1/workspace/{$this->workspace->id}", ['name' => $valid_string])->assertStatus(200);
        }, $this->valid_string_input);
    }

}