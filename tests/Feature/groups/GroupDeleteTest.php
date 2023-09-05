<?php

namespace Tests\Feature;

use App\Models\Group;
use App\Models\GroupUsers;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class GroupDeleteTest extends TestCase
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
        $this->delete("api/v1/group/{$this->group->id}")->assertStatus(401);
    }

    /** @test */
    public function only_admin_403(): void
    {
        $user = User::factory()->student()->create();
        $this->actingAs($user)->delete("api/v1/group/{$this->group->id}")->assertStatus(403);
    }


    /** @test */
    public function group_not_found_404(): void
    {
        $this->delete("api/v1/group/99", Group::factory()->raw())->assertStatus(404);
    }


    /** @test */
    public function delete_empty_group_200(): void
    {
        $this->delete("api/v1/group/{$this->group->id}")->assertStatus(200);
        $this->assertEmpty(GroupUsers::find($this->group->id));
    }


    /** @test */
    public function delete_group_remove_students_200(): void
    {
        GroupUsers::factory()->group($this->group)->count(4)->create();
        GroupUsers::factory()->group($this->group)->discharged()->count(2)->create();
        $this->delete("api/v1/group/{$this->group->id}")->assertStatus(200);
        $this->assertEquals(GroupUsers::query()->where('group_id', $this->group->id)->count(), 0);
    }
}