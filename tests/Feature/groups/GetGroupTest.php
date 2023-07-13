<?php

namespace Tests\Feature;

use App\Models\Group;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class GetGroupTest extends TestCase
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
    public function not_logged_401(): void
    {
        Auth::logout();
        $this->get("api/v1/group/{$this->group->id}")->assertStatus(401);
    }

    /** @test */
    public function group_not_found_404(): void
    {
        $this->get("api/v1/group/99", Group::factory()->raw())->assertStatus(404);
    }


    /** @test */
    public function get_group_200(): void
    {

        $data = $this->get("api/v1/group/{$this->group->id}")->assertStatus(200)->decodeResponseJson();

        $this->assertEquals($this->group['name'], $data['result']['name']);
        $this->assertEquals($this->group['code'], $data['result']['code']);
        $this->assertEquals($this->group['color'], $data['result']['color']);
        $this->assertEquals($this->group['id'], $data['result']['id']);
    }
}