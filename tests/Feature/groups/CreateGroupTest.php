<?php

namespace Tests\Feature;

use App\Models\Group;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class CreateGroupTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private $user;



    public function setUp(): void
    {
        parent::setUp();

        $this->withHeaders([
            'Accept' => 'application/json'
        ]);

        $this->user = User::factory()->admin()->create();

        $this->actingAs($this->user);

    }


    /** @test */
    public function wrong_params_422(): void
    {
        $group = Group::factory()->raw();

        $this->post('api/v1/group', array_diff_key(Group::factory()->raw(['name' => null])))->assertStatus(422);
        // Short
        $this->post('api/v1/group', Group::factory()->raw(["name" => "S"]))->assertStatus(422);
        // Long
        $this->post('api/v1/group', Group::factory()->raw(["name" => "This is too long name for a group that shall not pass"]))->assertStatus(422);
        $this->post('api/v1/group', array_diff_key($group, Group::factory()->raw(['color' => null])))->assertStatus(422);
        $this->post('api/v1/group', array_diff_key($group, Group::factory()->raw(['code' => null])))->assertStatus(422);
        $this->post('api/v1/group', array_merge($group, ['color' => 'PPPP']))->assertStatus(422);
        $this->post('api/v1/group', array_merge($group, ['name' => '????']))->assertStatus(422);
        $this->post('api/v1/group', array_merge($group, ['code' => 123]))->assertStatus(422);
    }

    /** @test */
    public function not_logged_401(): void
    {
        Auth::logout();
        $this->post('api/v1/group')->assertStatus(401);
    }

    /** @test */
    public function only_admin_403(): void
    {
        $user = User::factory()->student()->create();
        $response = $this->actingAs($user)->post('api/v1/group', Group::factory()->raw());
        $response->assertStatus(403);
    }

    /** @test */
    public function create_group_200(): void
    {
        $group = Group::factory()->raw();
        $response = $this->post('api/v1/group', $group)->assertStatus(200);
        $data = $response->decodeResponseJson();


        $this->assertEquals($group['name'], $data['result']['name']);
        $this->assertEquals($group['code'], $data['result']['code']);
        $this->assertEquals($group['color'], $data['result']['color']);
    }


    /** @test */
    public function group_name_format_200(): void
    {
        array_map(function ($valid_string) {
            $this->post("api/v1/group", Group::factory()->raw(["name" => $valid_string]))->assertStatus(200);
        }, $this->valid_string_input);
    }


    /** @test */
    public function code_duplicated_409(): void
    {
        $group1 = Group::factory()->create();

        $group2 = Group::factory()->raw(['code' => $group1['code']]);

        $this->post('api/v1/group', $group2)->assertStatus(409);
    }

    /** @test */
    public function color_duplicated_409(): void
    {
        $group1 = Group::factory()->create();

        $group2 = Group::factory()->raw(['color' => $group1['color']]);

        $this->post('api/v1/group', $group2)->assertStatus(409);
    }

    /** @test */
    public function create_two_groups_200(): void
    {
        $this->post('api/v1/group', Group::factory()->raw())->assertStatus(200);
        $this->post('api/v1/group', Group::factory()->raw())->assertStatus(200);
    }
}