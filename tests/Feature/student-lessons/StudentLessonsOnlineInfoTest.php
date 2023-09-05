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


class StudentLessonsOnlineInfoTest extends TestCase
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
            Permissions::SEE_ONLINE_LESSON,
        ])->create();

        $this->lesson = Lesson::factory()
            ->withStudents($this->user)
            ->create([
                'is_active' => true,
                'is_online' => true,
                'url' => $this->faker()->url()
            ]);

        $this->actingAs($this->user);
    }



    /** @test */
    public function not_logged_401(): void
    {
        Auth::logout();
        $this->get("api/v1/student-lessons/{$this->lesson->id}/online")->assertStatus(401);
    }


    /** @test */
    public function no_permissions_403(): void
    {
        $user = User::factory()->student()->create(); // Missing SEE LESSONS
        $this->actingAs($user)->get("api/v1/student-lessons/{$this->lesson->id}/online")->assertStatus(403);

        $user->givePermissionTo(Permissions::SEE_LESSONS); // Missing ONLINE LESSONS
        $this->actingAs($user)->get("api/v1/student-lessons/{$this->lesson->id}/online")->assertStatus(403);

    }

    /** @test */
    public function not_found_404(): void
    {
        $this->get("api/v1/student-lessons/404/online")->assertStatus(404);
    }

    /** @test */
    public function lesson_not_active_409(): void
    {
        $lesson = Lesson::factory()->create(['is_active' => false]);
        $this->get("api/v1/student-lessons/{$lesson->id}/online")->assertStatus(409);
    }

    /** @test */
    public function not_online_lesson_409(): void
    {
        $lesson = Lesson::factory()->create(['is_active' => true]);
        $this->get("api/v1/student-lessons/{$lesson->id}/online")->assertStatus(409);
    }
    /** @test */
    public function not_user_lesson_403(): void
    {
        $lesson = Lesson::factory()->create(['is_active' => true, 'is_online' => true]);
        $this->get("api/v1/student-lessons/{$lesson->id}/online")->assertStatus(403);
    }


    /** @test */
    public function get_lesson_info_200(): void
    {
        $data = $this->get("api/v1/student-lessons/{$this->lesson->id}/online")->assertStatus(200);
        $this->assertEquals($data['url'], $this->lesson->url);
    }
}