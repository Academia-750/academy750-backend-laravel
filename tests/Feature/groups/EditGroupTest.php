<?php

namespace Tests\Feature;

use App\Models\Group;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class EditGroupTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private $user;
    private $group;


    public function setUp(): void
    {
        parent::setUp();

        $this->withHeaders([
            'Accept' => 'application/json'
        ]);

        $this->user = User::factory()->admin()->create();

        $this->actingAs($this->user);

        $this->group = Group::factory()->create();

    }


    /** @test */
    public function wrong_params_422(): void
    {

        $this->put("api/v1/group/{$this->group->id}", ['color' => 'PPPP'])->assertStatus(422);
        $this->put("api/v1/group/{$this->group->id}", ['name' => '????'])->assertStatus(422);
        $this->put("api/v1/group/{$this->group->id}", ['code' => 123])->assertStatus(422);

    }

    /** @test */
    public function not_logged_401(): void
    {
        Auth::logout();
        $this->put("api/v1/group/{$this->group->id}")->assertStatus(401);
    }

    /** @test */
    public function only_admin_403(): void
    {
        $user = User::factory()->student()->create();
        $response = $this->actingAs($user)->put("api/v1/group/{$this->group->id}", Group::factory()->raw());
        $response->assertStatus(403);
    }


    /** @test */
    public function group_not_found_404(): void
    {
        $this->put("api/v1/group/99", Group::factory()->raw())->assertStatus(404);
    }


    /** @test */
    public function edit_single_item_200(): void
    {
        $newData = Group::factory()->raw();

        $data = $this->put("api/v1/group/{$this->group->id}", ['name' => $newData['name']])->assertStatus(200)->decodeResponseJson();

        $this->assertEquals($newData['name'], $data['result']['name']);
        $this->assertEquals($this->group['code'], $data['result']['code']);
        $this->assertEquals($this->group['color'], $data['result']['color']);
        $this->assertEquals($this->group['id'], $data['result']['id']);
    }

    /** @test */
    public function edit_all_item_200(): void
    {
        $newData = Group::factory()->raw();

        $data = $this->put("api/v1/group/{$this->group->id}", $newData)->assertStatus(200)->decodeResponseJson();

        $this->assertEquals($newData['name'], $data['result']['name']);
        $this->assertEquals($newData['code'], $data['result']['code']);
        $this->assertEquals($newData['color'], $data['result']['color']);
        $this->assertEquals($this->group['id'], $data['result']['id']);
    }


    /** @test */
    public function code_duplicated_409(): void
    {
        $otherGroup = Group::factory()->create();
        $this->put("api/v1/group/{$this->group->id}", ['code' => $otherGroup['code']])->assertStatus(409);
    }

    /** @test */
    public function color_duplicated_409(): void
    {
        $otherGroup = Group::factory()->create();
        $this->put("api/v1/group/{$this->group->id}", ['color' => $otherGroup['color']])->assertStatus(409);
    }

}