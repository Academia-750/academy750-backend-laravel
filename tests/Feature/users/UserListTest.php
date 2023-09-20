<?php

namespace Tests\Feature;

use App\Models\Group;
use App\Models\GroupUsers;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class UserListTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private $user; // Admin user
    private $users;

    public function setUp(): void
    {
        parent::setUp();

        $this->withHeaders([
            'Accept' => 'application/json'
        ]);

        $this->user = User::factory()->admin()->create();

        $this->actingAs($this->user);

        $this->users = User::factory()->student()->count(4)->create();
    }


    /** @test */
    public function not_logged_401(): void
    {
        Auth::logout();
        $this->get("api/v1/users", [])->assertStatus(401);
    }


    /** @test */
    public function only_admin_403(): void
    {
        $user = User::factory()->student()->create();
        $this->actingAs($user)->get("api/v1/users/search", [])->assertStatus(403);
    }


    /**
     * TODO: This is and endpoint of the phase 1.
     * Here we just added test cases for some changes in phase 2.
     * Is required to make the core of the test cases for this end point
     */

    /** @test */
    public function user_has_no_groups_200(): void
    {
        $user = $this->users[0];

        $data = $this->get("api/v1/users?" . Arr::query(['filter[search]' => $user->dni]))->assertStatus(200);

        $this->assertEquals(count($data['data'][0]['relationships']['groups']), 0);
    }

    /** @test */
    public function user_has_groups_200(): void
    {
        $group = Group::factory()->create();
        $user = $this->users[0];

        GroupUsers::create(['group_id' => $group->id, 'user_id' => $user->id, 'discharged_at' => null]);

        $data = $this->get("api/v1/users?" . Arr::query(['filter[search]' => $user->dni]))->assertStatus(200);

        $this->assertNotNull($data['data'][0]['relationships']['groups'][0]);

        $this->assertEquals($data['data'][0]['relationships']['groups'][0]['id'], $group->id);
        $this->assertEquals($data['data'][0]['relationships']['groups'][0]['name'], $group->name);
    }

    /** @test */
    public function user_has_groups_discharged_200(): void
    {
        $group = Group::factory()->create();
        $group2 = Group::factory()->create();
        $user = $this->users[0];

        // First group he was discharged, only $group2 shall appear
        GroupUsers::create(['group_id' => $group->id, 'user_id' => $user->id, 'discharged_at' => now()]);
        GroupUsers::create(['group_id' => $group2->id, 'user_id' => $user->id, 'discharged_at' => null]);

        $data = $this->get("api/v1/users?" . Arr::query(['filter[search]' => $user->dni]))->assertStatus(200);

        $this->assertEquals(count($data['data'][0]['relationships']['groups']), 1);

        $this->assertEquals($data['data'][0]['relationships']['groups'][0]['id'], $group2->id);
        $this->assertEquals($data['data'][0]['relationships']['groups'][0]['name'], $group2->name);
    }
}