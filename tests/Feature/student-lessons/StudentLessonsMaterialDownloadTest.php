<?php

namespace Tests\Feature;

use App\Models\Group;
use App\Models\GroupUsers;
use App\Models\Lesson;
use App\Models\Material;
use App\Models\User;
use Database\Seeders\Permissions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;


class StudentLessonsMaterialDownloadTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private $user;

    private $lesson;

    private $material;

    private $recording;


    public function setUp(): void
    {
        parent::setUp();

        $this->withHeaders([
            'Accept' => 'application/json'
        ]);

        $this->user = User::factory()->student()->allowedTo([
            Permissions::SEE_LESSON_MATERIALS,
            Permissions::SEE_LESSON_RECORDINGS,
        ])->create();

        $this->material = Material::factory()->withUrl()->create(['type' => 'material']);
        $this->recording = Material::factory()->withUrl()->create(['type' => 'recording']);


        // Our Lesson has 1 student, 1 material and 1 recording
        $this->lesson = Lesson::factory()
            ->withStudents($this->user)
            ->withMaterials([$this->material, $this->recording])
            ->create(['is_active' => true]);

        $this->actingAs($this->user);
    }



    /** @test */
    public function not_logged_401(): void
    {
        Auth::logout();
        $this->get("api/v1/student-lessons/{$this->material->id}/download")->assertStatus(401);
    }


    /** @test */
    public function no_permissions_403(): void
    {

        $user = User::factory()->student()->create(); // Missing SEE LESSONS
        $this->actingAs($user)->get("api/v1/student-lessons/{$this->material->id}/download")->assertStatus(403);

        $user->permissions()->detach();
        $user->givePermissionTo(Permissions::SEE_LESSON_RECORDINGS); // Wrong Permission type
        $this->actingAs($user)->get("api/v1/student-lessons/{$this->material->id}/download")->assertStatus(403);

        $user->permissions()->detach();
        $user->givePermissionTo(Permissions::SEE_LESSON_MATERIALS); // Wrong Permission type
        $this->actingAs($user)->get("api/v1/student-lessons/{$this->recording->id}/download")->assertStatus(403);

    }

    /** @test */
    public function not_found_404(): void
    {
        $this->get("api/v1/student-lessons/404/download")->assertStatus(404);
    }

    /** @test */
    public function material_not_in_lesson_409(): void
    {
        $material = Material::factory()->create();
        $this->get("api/v1/student-lessons/{$material->id}/download")->assertStatus(409);
    }

    /** @test */
    public function material_in_lesson_not_active_409(): void
    {
        $this->lesson->is_active = false;
        $this->lesson->save();

        $this->get("api/v1/student-lessons/{$this->material->id}/download")->assertStatus(409);
    }

    /** @test */
    public function download_recording_url_200(): void
    {
        $data = $this->get("api/v1/student-lessons/{$this->recording->id}/download")->assertStatus(200);
        $this->assertEquals($data['url'], $this->recording->url);
    }

    /** @test */
    public function download_material_special_type_200(): void
    {
        $this->markTestSkipped();
        $data = $this->get("api/v1/student-lessons/{$this->recording->id}/download")->assertStatus(200);
        $this->assertEquals($data['url'], $this->recording->url);
    }

    /** @test */
    public function download_pdf_watermark_200(): void
    {
        $this->markTestSkipped();
    }

    /** @test */
    public function download_image_watermark_200(): void
    {
        $this->markTestSkipped();
    }

    /** @test */
    public function cache_temp_material_with_water_mark_200(): void
    {
        $this->markTestSkipped();
    }

    /** @test */
    public function delete_temporal_downloads_200(): void
    {
        $this->markTestSkipped();
    }
}