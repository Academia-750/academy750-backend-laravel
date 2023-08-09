<?php

namespace Tests\Feature;

use App\Models\Lesson;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;


class ActiveLessonTest extends TestCase
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
    public function not_logged_401(): void
    {
        Auth::logout();
        $this->put("api/v1/lesson/{$this->lesson->id}/active")->assertStatus(401);
    }

    /** @test */
    public function only_admin_403(): void
    {
        $user = User::factory()->student()->create();
        $this->actingAs($user)->put("api/v1/lesson/{$this->lesson->id}/active")->assertStatus(403);
    }

    /** @test */
    public function not_found_404(): void
    {
        $this->put("api/v1/lesson/99/active", ['active' => true])->assertStatus(404);
    }

    /** @test */
    public function wrong_params_422(): void
    {

        $this->put("api/v1/lesson/{$this->lesson->id}/active", [])->assertStatus(422); // No params

        $this->put("api/v1/lesson/{$this->lesson->id}/active", ['active' => []])->assertStatus(422); // Wrong format
        $this->put("api/v1/lesson/{$this->lesson->id}/active", ['active' => "wrong"])->assertStatus(422); // Wrong format

    }


    /** @test */
    public function activate_lesson_200(): void
    {
        // Initially is false
        $lesson = Lesson::find($this->lesson->id);
        $this->assertEquals($lesson->is_active, false);

        $this->put("api/v1/lesson/{$this->lesson->id}/active", ['active' => true])->assertStatus(200);

        $lesson = Lesson::find($this->lesson->id);
        $this->assertEquals($lesson->is_active, true);

        $this->put("api/v1/lesson/{$this->lesson->id}/active", ['active' => false])->assertStatus(200);

        $lesson = Lesson::find($this->lesson->id);
        $this->assertEquals($lesson->is_active, false);

    }


}