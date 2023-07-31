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

/**
 * This API dont handle generic cases for the multiple-records-endpoint but specific
 * for the lock-user scenario
 */
class DeactivateUserTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private $user; // Admin user
    private $student;

    private $body = [];

    public function setUp(): void
    {
        parent::setUp();

        $this->withHeaders([
            'Accept' => 'application/json'
        ]);

        $this->user = User::factory()->admin()->create();

        $this->actingAs($this->user);

        $this->student = User::factory()->student()->create();

        $this->body = ["action" => 'lock-account', 'users' => [$this->student->uuid]];
    }


    /** @test */
    public function not_logged_401(): void
    {
        Auth::logout();
        $this->post("api/v1/users/actions-on-multiple-records", $this->body)->assertStatus(401);
    }


    /** @test */
    public function only_admin_403(): void
    {
        $user = User::factory()->student()->create();
        $response = $this->actingAs($user)->post("api/v1/users/actions-on-multiple-records", $this->body)->assertStatus(403);

    }

    /** @test */
    public function empty_user_array_200(): void
    {
        $this->body['users'] = [];
        $this->post("api/v1/users/actions-on-multiple-records", $this->body)->assertStatus(422);


    }


    /** @test */
    public function disable_single_user_200(): void
    {
        $this->post("api/v1/users/actions-on-multiple-records", $this->body)->assertStatus(200);
        $user = User::find($this->student->id);
        $this->assertEquals($user->state, 'disable');
    }


    /** @test */
    public function disable_several_users_200(): void
    {
        $students = User::factory()->student()->count(3)->create();
        $this->body['users'] = $this->map($students->toArray(), 'uuid');

        $this->post("api/v1/users/actions-on-multiple-records", $this->body)->assertStatus(200);
        $studentsAfter = User::findMany($this->map($students->toArray(), 'uuid'));

        foreach ($studentsAfter as $student) {
            $this->assertEquals($student->state, 'disable');
        }
    }

    /** @test */
    public function student_are_discharged_from_groups_200(): void
    {
        // We make the student member of two groups
        $members = GroupUsers::factory()->count(2)->state(['user_id' => $this->student->id])->create()->toArray();

        $this->post("api/v1/users/actions-on-multiple-records", $this->body)->assertStatus(200);

        $membersAfter = GroupUsers::findMany($this->map($members, 'id'))->toArray();

        foreach ($membersAfter as $member) {
            $this->assertEquals($member['user_id'], $this->student->id);
            $this->assertNotNull($member['discharged_at']);
        }
    }
}