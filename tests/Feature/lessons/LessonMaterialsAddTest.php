<?php

namespace Tests\Feature;

use App\Models\Lesson;
use App\Models\Material;
use App\Models\User;
use App\Notifications\Api\NewMaterialAvailable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;


class LessonMaterialsAddTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private $user;

    private $lesson;
    private $material;


    public function setUp(): void
    {
        parent::setUp();

        $this->withHeaders([
            'Accept' => 'application/json'
        ]);

        $this->user = User::factory()->admin()->create();

        $this->actingAs($this->user);

        $this->lesson = Lesson::factory()->create();

        $this->material = Material::factory()->create();
    }



    /** @test */
    public function not_logged_401(): void
    {
        Auth::logout();
        $this->post("api/v1/lesson/{$this->lesson->id}/material")->assertStatus(401);
    }

    /** @test */
    public function only_admin_403(): void
    {
        $user = User::factory()->student()->create();
        $this->actingAs($user)->post("api/v1/lesson/{$this->lesson->id}/material")->assertStatus(403);
    }

    /** @test */
    public function lesson_not_found_404(): void
    {
        $this->post("api/v1/lesson/99/material", ['material_id' => $this->material->id])->assertStatus(404);
    }


    /** @test */
    public function material_not_found_404(): void
    {
        $this->post("api/v1/lesson/{$this->lesson->id}/material", ['material_id' => 99])->assertStatus(404);
    }


    /** @test */
    public function wrong_parameters_422(): void
    {
        $this->post("api/v1/lesson/{$this->lesson->id}/material", [])->assertStatus(422); // Missing material_id
        $this->post("api/v1/lesson/{$this->lesson->id}/material", ['material_id' => -23])->assertStatus(422); // No negative value
        $this->post("api/v1/lesson/{$this->lesson->id}/material", ['material_id' => 2.23])->assertStatus(422); // No decimal
        $this->post("api/v1/lesson/{$this->lesson->id}/material", ['material_id' => 'not-uuid'])->assertStatus(422); // Not ID
    }



    /** @test */
    public function add_material_to_lesson_200(): void
    {
        $this->post("api/v1/lesson/{$this->lesson->id}/material", ['material_id' => $this->material->id])->assertStatus(200);

        $material = $this->lesson->materials()->find($this->material->id);

        $this->assertNotNull($material);
        $this->assertEquals($this->material->id, $material->pivot->material_id);
        $this->assertNotNull($material->pivot->created_at);
        $this->assertNotNull($material->pivot->updated_at);
    }

    /** @test */
    public function material_already_exists_409(): void
    {
        $this->post("api/v1/lesson/{$this->lesson->id}/material", ['material_id' => $this->material->id])->assertStatus(200);
        $this->post("api/v1/lesson/{$this->lesson->id}/material", ['material_id' => $this->material->id])->assertStatus(409);
    }

    /** @test */
    public function add_material_non_active_lesson_no_notify_email_200(): void
    {
        Notification::fake();
        Notification::assertNothingSent();

        $this->lesson->students()->attach(User::factory()->student()->count(2)->create());

        $this->post("api/v1/lesson/{$this->lesson->id}/material", ['material_id' => $this->material->id])->assertStatus(200);

        Notification::assertCount(0);
    }

    /** @test */
    public function add_material_active_lesson_notify_email_200(): void
    {
        Notification::fake();
        Notification::assertNothingSent();

        $this->lesson->students()->attach(User::factory()->student()->count(2)->create());

        $this->lesson->update(['is_active' => true]);

        $this->post("api/v1/lesson/{$this->lesson->id}/material", ['material_id' => $this->material->id])->assertStatus(200);

        Notification::assertCount(2);

        $students = $this->lesson->students()->get();
        Notification::assertSentTo($students[0], NewMaterialAvailable::class);
        Notification::assertSentTo($students[1], NewMaterialAvailable::class);
    }

    /** @test */
    public function test_new_material_notification_content(): void
    {
        $student = $this->lesson->students->first();
        $date = date('d/m/Y', strtotime($this->lesson->date));
        $notification = new NewMaterialAvailable($this->lesson, $this->material);
        $rendered = $notification->toMail($student)->render();

        $this->assertStringContainsString("Se ha incorporado el material {$this->material->name}", $rendered);
        $this->assertStringContainsString("a la clase {$this->lesson->name}", $rendered);
        $this->assertStringContainsString("del día {$date}", $rendered);
    }
}
