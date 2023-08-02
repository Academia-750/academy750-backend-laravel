<?php

namespace Tests\Feature;

use App\Models\Lesson;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;


class DeleteLessonTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private $user;

    private $lesson;


    public function setUp(): void
    {
        parent::setUp();

        $this->withHeaders([
            'Accept' => 'application/json'
        ]);

        $this->user = User::factory()->admin()->create();

        $this->actingAs($this->user);

        $this->lesson = Lesson::factory()->create();

    }


    /** @test */
    public function not_route_405(): void
    {
        Auth::logout();
        $this->delete("api/v1/lesson")->assertStatus(405);
    }

    /** @test */
    public function not_logged_401(): void
    {
        Auth::logout();
        $this->delete("api/v1/lesson/{$this->lesson->id}")->assertStatus(401);
    }

    /** @test */
    public function only_admin_403(): void
    {
        $user = User::factory()->student()->create();
        $this->actingAs($user)->delete("api/v1/lesson/{$this->lesson->id}")->assertStatus(403);
    }


    /** @test */
    public function lesson_not_found_404(): void
    {
        $this->delete("api/v1/lesson/99")->assertStatus(404);
    }


    /** @test */
    public function cant_edit_active_lesson_403(): void
    {
        $this->lesson->is_active = true;
        $this->lesson->save();

        $this->delete("api/v1/lesson/{$this->lesson->id}")->assertStatus(403);
    }

    /** @test */
    public function delete_lesson_200(): void
    {

        $this->delete("api/v1/lesson/{$this->lesson->id}")->assertStatus(200)->json();
        $lesson = Lesson::find($this->lesson->id);

        $this->assertNull($lesson);
    }


    /** @test */
    public function delete_lesson_clear_students_200(): void
    {
        $this->markTestSkipped();
    }

    /** @test */
    public function delete_lesson_clear_materials_200(): void
    {
        $this->markTestSkipped();
    }
}