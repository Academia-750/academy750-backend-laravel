<?php

namespace Tests\Feature;

use App\Models\Group;
use App\Models\GroupUsers;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class JoinGroupTest extends TestCase
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
        $this->post("api/v1/group/{$this->group->id}/join", ['user_id' => $this->student->id])->assertStatus(401);
    }

    /** @test */
    public function only_admin_403(): void
    {
        $user = User::factory()->student()->create();
        $this->actingAs($user)->post("api/v1/group/{$this->group->id}/join", [])->assertStatus(403);
    }

    /** @test */
    public function wrong_parameter_422(): void
    {
        $this->post("api/v1/group/{$this->group->id}/join", [])->assertStatus(422);
        $this->post("api/v1/group/{$this->group->id}/join", ['user_id' => 'student_id'])->assertStatus(422);
        $this->post("api/v1/group/{$this->group->id}/join", ['user_id' => 22])->assertStatus(422);

    }

    /** @test */
    public function group_not_found_404(): void
    {
        $this->post("api/v1/group/99/join", ['user_id' => $this->student->uuid])->assertStatus(404);
    }

    /** @test */
    public function user_not_found_404(): void
    {
        $this->post("api/v1/group/{$this->group->id}/join", ['user_id' => $this->faker->uuid()])->assertStatus(404);
    }


    /** @test */
    public function join_group_200(): void
    {
        $student = User::factory()->student()->create();
        $time = now()->milliseconds(0)->toISOString();
        $response = $this->post("api/v1/group/{$this->group->id}/join", ['user_id' => $student->uuid])->assertStatus(200)->json();

        $this->assertIsInt($response['result']['id']);
        $this->assertEquals($response['result']['user_id'], $student->id);
        $this->assertEquals($response['result']['group_id'], $this->group->id);
        $this->assertEquals($response['result']['created_at'], $time); // Created At is the time he joined the group
    }

    /** @test */
    public function can_join_two_groups_200(): void
    {
        $student = User::factory()->student()->create();
        $this->post("api/v1/group/{$this->group->id}/join", ['user_id' => $student->uuid])->assertStatus(200);

        $group2 = Group::factory()->create();
        $this->post("api/v1/group/{$group2->id}/join", ['user_id' => $student->uuid])->assertStatus(200);
    }

    /** @test */
    public function cant_join_group_if_already_exists_409(): void
    {
        $student = User::factory()->student()->create();
        $this->post("api/v1/group/{$this->group->id}/join", ['user_id' => $student->uuid])->assertStatus(200);
        $this->post("api/v1/group/{$this->group->id}/join", ['user_id' => $student->uuid])->assertStatus(409);
    }

    /** @test */
    public function disabled_users_cant_join_403(): void
    {
        $student = User::factory()->student()->state(['state' => 'disable'])->create();
        $this->post("api/v1/group/{$this->group->id}/join", ['user_id' => $student->uuid])->assertStatus(403);
    }

    /** @test */
    public function can_join_group_if_was_discharged_200(): void
    {
        $student = User::factory()->student()->create();
        $response = $this->post("api/v1/group/{$this->group->id}/join", ['user_id' => $student->uuid])->json();
        // Discharge the user
        $member = GroupUsers::find($response['result']['id']);
        $this->assertNotEmpty($member);
        $member->discharged_at = now();
        $member->save();

        // Re join the group
        $response2 = $this->post("api/v1/group/{$this->group->id}/join", ['user_id' => $student->uuid])->json();
        $this->assertNotEquals($response['result']['id'], $response2['result']['id']);
    }
}