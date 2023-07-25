<?php

namespace Tests\Feature;

use App\Models\Group;
use App\Models\GroupUsers;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class LeaveGroupTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private $user;
    private $group;

    private $student;


    public function setUp(): void
    {
        parent::setUp();

        $this->withHeaders([
            'Accept' => 'application/json'
        ]);

        $this->user = User::factory()->admin()->create();

        $this->actingAs($this->user);

        $this->group = Group::factory()->create();

        $this->student = User::factory()->student()->create();

    }


    /** @test */
    public function not_logged_401(): void
    {
        Auth::logout();
        $this->post("api/v1/group/{$this->group->id}/leave", ['user_id' => $this->student->uuid])->assertStatus(401);
    }

    /** @test */
    public function wrong_parameter_422(): void
    {
        $this->post("api/v1/group/{$this->group->id}/leave", [])->assertStatus(422);
        $this->post("api/v1/group/{$this->group->id}/leave", ['user_id' => 'student_id'])->assertStatus(422);
    }

    /** @test */
    public function only_admin_403(): void
    {
        $user = User::factory()->student()->create();
        $this->actingAs($user)->post("api/v1/group/{$this->group->id}/leave", [])->assertStatus(403);
    }

    /** @test */
    public function group_not_found_404(): void
    {
        $this->post("api/v1/group/99/leave", ['user_id' => $this->student->uuid])->assertStatus(404);
    }

    /** @test */
    public function user_not_found_404(): void
    {
        $this->post("api/v1/group/{$this->group->id}/leave", ['user_id' => $this->faker->uuid()])->assertStatus(404);
    }


    /** @test */
    public function member_not_found_404(): void
    {
        $user = User::factory()->create();
        $this->post("api/v1/group/{$this->group->id}/leave", ['user_id' => $user->uuid])->assertStatus(404);
    }


    /** @test */
    public function leave_group_200(): void
    {
        $student = User::factory()->student()->create();

        $this->post("api/v1/group/{$this->group->id}/join", ['user_id' => $student->uuid])->assertStatus(200);

        $member = GroupUsers::where('group_id', $this->group->id)->where('user_id', $student->id)->first();

        $time = now()->milliseconds(0)->toISOString();
        $response = $this->post("api/v1/group/{$this->group->id}/leave", ['user_id' => $student->uuid])->assertStatus(200)->json();

        $this->assertEquals($response['result']['id'], $member->id);
        $this->assertEquals($response['result']['discharged_at'], $time); // Created At is the time he leaveed the group
    }

    /** @test */
    public function already_left_group_404(): void
    {
        $student = User::factory()->student()->create();
        $this->post("api/v1/group/{$this->group->id}/join", ['user_id' => $student->uuid])->assertStatus(200);
        $this->post("api/v1/group/{$this->group->id}/leave", ['user_id' => $student->uuid])->assertStatus(200);
        // Already left
        $this->post("api/v1/group/{$this->group->id}/leave", ['user_id' => $student->uuid])->assertStatus(404);

    }

    /** @test */
    public function can_join_and_leave_several_times(): void
    {
        $student = User::factory()->student()->create();
        $this->post("api/v1/group/{$this->group->id}/join", ['user_id' => $student->uuid])->assertStatus(200);
        $this->post("api/v1/group/{$this->group->id}/leave", ['user_id' => $student->uuid])->assertStatus(200);
        $this->post("api/v1/group/{$this->group->id}/join", ['user_id' => $student->uuid])->assertStatus(200);
        $this->post("api/v1/group/{$this->group->id}/leave", ['user_id' => $student->uuid])->assertStatus(200);
        $this->post("api/v1/group/{$this->group->id}/join", ['user_id' => $student->uuid])->assertStatus(200);
        $this->post("api/v1/group/{$this->group->id}/leave", ['user_id' => $student->uuid])->assertStatus(200);
    }
}