<?php

namespace Tests\Feature;

use App\Models\Lesson;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Tests\TestCase;


class LessonStudentsDeleteTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private $user;

    private $lesson;
    private $student;


    public function setUp(): void
    {
        parent::setUp();

        $this->withHeaders([
            'Accept' => 'application/json'
        ]);

        $this->user = User::factory()->admin()->create();

        $this->actingAs($this->user);

        $this->lesson = Lesson::factory()->active()->create();

        $this->student = User::factory()->student()->create();

        $this->lesson->students()->attach($this->student);
    }



    /** @test */
    public function not_logged_401(): void
    {
        Auth::logout();
        $this->delete("api/v1/lesson/{$this->lesson->id}/student")->assertStatus(401);
    }

    /** @test */
    public function only_admin_403(): void
    {
        $user = User::factory()->student()->create();
        $this->actingAs($user)->delete("api/v1/lesson/{$this->lesson->id}/student", ['user_id' => $this->student->uuid])->assertStatus(403);
    }

    /** @test */
    public function lesson_not_found_404(): void
    {
        $this->delete("api/v1/lesson/99/student", ['user_id' => $this->student->uuid])->assertStatus(404);
    }


    /** @test */
    public function user_not_in_lesson_409(): void
    {
        $this->delete("api/v1/lesson/{$this->lesson->id}/student", ['user_id' => (string) Str::uuid()])->assertStatus(409);
    }


    /** @test */
    public function wrong_parameters_422(): void
    {
        $this->delete("api/v1/lesson/{$this->lesson->id}/student", [])->assertStatus(422); // Missing user_id
        $this->delete("api/v1/lesson/{$this->lesson->id}/student", ['user_id' => 99])->assertStatus(422); // Wrong type
        $this->delete("api/v1/lesson/{$this->lesson->id}/student", ['user_id' => 'not-uuid'])->assertStatus(422); // Not UUID
    }



    /** @test */
    public function student_removed_from_lesson_200(): void
    {
        $this->delete("api/v1/lesson/{$this->lesson->id}/student", ['user_id' => $this->student->uuid])->assertStatus(200)->json();

        $this->assertEquals($this->lesson->students()->count(), 0);
    }


}