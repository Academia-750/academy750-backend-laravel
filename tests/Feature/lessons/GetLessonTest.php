<?php

namespace Tests\Feature;

use App\Models\Lesson;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;


class GetLessonTest extends TestCase
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

        $this->lesson = Lesson::factory()->active()->create();

    }


    /** @test */
    public function not_route_405(): void
    {
        Auth::logout();
        $this->get("api/v1/lesson")->assertStatus(405);
    }

    /** @test */
    public function not_logged_401(): void
    {
        Auth::logout();
        $this->get("api/v1/lesson/{$this->lesson->id}")->assertStatus(401);
    }

    /** @test */
    public function students_can_access_200(): void
    {
        $user = User::factory()->student()->create();
        $this->actingAs($user)->get("api/v1/lesson/{$this->lesson->id}")->assertStatus(200);
    }


    /** @test */
    public function lesson_not_found_404(): void
    {
        $this->get("api/v1/lesson/99")->assertStatus(404);
    }

    /** @test */
    public function get_lesson_200(): void
    {

        $data = $this->get("api/v1/lesson/{$this->lesson->id}")->assertStatus(200)->json();

        $this->assertEquals($data['result']['id'], $this->lesson->id);
        $this->assertEquals($data['result']['name'], $this->lesson->name);
        $this->assertEquals($data['result']['description'], $this->lesson->description);
        $this->assertEquals($data['result']['is_online'], $this->lesson->is_online);
        $this->assertEquals($data['result']['is_active'], $this->lesson->is_active);
        $this->assertEquals($data['result']['date'], $this->lesson->date);
        $this->assertEquals($data['result']['start_time'], $this->lesson->start_time);
        $this->assertEquals($data['result']['end_time'], $this->lesson->end_time);
    }

    /**
     * TODO: Maybe some attach information like the number of students assisting will be
     * only display to an admin role
     */

    /** @test */
    public function total_students_200(): void
    {

        $this->markTestSkipped();
    }

    /** @test */
    public function total_materials_200(): void
    {

        $this->markTestSkipped();
    }
}