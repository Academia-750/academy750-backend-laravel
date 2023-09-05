<?php

namespace Tests\Feature;

use App\Models\Group;
use App\Models\GroupUsers;
use App\Models\Lesson;
use App\Models\User;
use Database\Seeders\Permissions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;


class StudentLessonsJoinTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private $user;

    private $lesson;

    private $body;

    public function setUp(): void
    {
        parent::setUp();

        $this->withHeaders([
            'Accept' => 'application/json'
        ]);

        $this->user = User::factory()->student()->allowedTo([
            Permissions::SEE_LESSONS,
            Permissions::JOIN_LESSONS,
        ])->create();

        $this->lesson = Lesson::factory()
            ->withStudents($this->user)
            ->create(['is_active' => true]);

        $this->actingAs($this->user);
    }



    /** @test */
    public function not_logged_401(): void
    {
        Auth::logout();
        $this->put("api/v1/student-lessons/{$this->lesson->id}/join")->assertStatus(401);
    }


    /** @test */
    public function no_permissions_403(): void
    {
        $user = User::factory()->student()->create(); // Missing SEE
        $this->actingAs($user)->put("api/v1/student-lessons/{$this->lesson->id}/join")->assertStatus(403);

        $user->givePermissionTo(Permissions::SEE_LESSONS); // Missing JOIN
        $this->actingAs($user)->put("api/v1/student-lessons/{$this->lesson->id}/join")->assertStatus(403);

    }

    /** @test */
    public function not_found_404(): void
    {
        $this->put("api/v1/student-lessons/404/join", ['join' => true])->assertStatus(404);
    }

    /** @test */
    public function wrong_parameters_422(): void
    {
        // Join
        $this->put("api/v1/student-lessons/{$this->lesson->id}/join", [])->assertStatus(422); //  Missing join
        $this->put("api/v1/student-lessons/{$this->lesson->id}/join", ['join' => 'not_boolean'])->assertStatus(422); // Wrong type
    }

    /** @test */
    public function not_user_lesson_403(): void
    {
        $lesson = Lesson::factory()->create(['is_active' => true]);
        $this->put("api/v1/student-lessons/{$lesson->id}/join", ['join' => true])->assertStatus(403);
    }

    /** @test */
    public function lesson_not_active_409(): void
    {
        $lesson = Lesson::factory()->create(['is_active' => false]);
        $this->put("api/v1/student-lessons/{$lesson->id}/join", ['join' => true])->assertStatus(409);
    }

    /** @test */
    public function join_a_lesson_200(): void
    {
        $this->put("api/v1/student-lessons/{$this->lesson->id}/join", ['join' => true])->assertStatus(200);

        $student = $this->lesson->students()->find($this->user->id);
        $this->assertEquals($student->pivot->will_join, true);
    }

    /** @test */
    public function un_join_a_lesson_200(): void
    {
        $this->put("api/v1/student-lessons/{$this->lesson->id}/join", ['join' => true])->assertStatus(200);
        $this->put("api/v1/student-lessons/{$this->lesson->id}/join", ['join' => false])->assertStatus(200);

        $student = $this->lesson->students()->find($this->user->id);
        $this->assertEquals($student->pivot->will_join, false);
    }
}