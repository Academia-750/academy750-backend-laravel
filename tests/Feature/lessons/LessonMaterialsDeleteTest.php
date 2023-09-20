<?php

namespace Tests\Feature;

use App\Models\Lesson;
use App\Models\Material;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Tests\TestCase;


class LessonMaterialsDeleteTest extends TestCase
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

        $this->lesson = Lesson::factory()->active()->create();

        $this->material = Material::factory()->create();
        $this->lesson->materials()->attach($this->material);

    }



    /** @test */
    public function not_logged_401(): void
    {
        Auth::logout();
        $this->delete("api/v1/lesson/{$this->material->id}/material")->assertStatus(401);
    }

    /** @test */
    public function only_admin_403(): void
    {
        $user = User::factory()->student()->create();
        $this->actingAs($user)->delete("api/v1/lesson/{$this->material->id}/material")->assertStatus(403);
    }

    /** @test */
    public function lesson_not_found_404(): void
    {
        $this->delete("api/v1/lesson/99/material", ['material_id' => $this->material->id])->assertStatus(404);
    }


    /** @test */
    public function material_not_found_409(): void
    {
        $this->delete("api/v1/lesson/{$this->material->id}/material", ['material_id' => 99])->assertStatus(409);
    }


    /** @test */
    public function wrong_parameters_422(): void
    {
        $this->delete("api/v1/lesson/{$this->material->id}/material", [])->assertStatus(422); // Missing material_id
        $this->delete("api/v1/lesson/{$this->material->id}/material", ['material_id' => -23])->assertStatus(422); // No negative value
        $this->delete("api/v1/lesson/{$this->material->id}/material", ['material_id' => 2.23])->assertStatus(422); // No decimal
        $this->delete("api/v1/lesson/{$this->material->id}/material", ['material_id' => 'not-uuid'])->assertStatus(422); // Not ID
    }



    /** @test */
    public function delete_material_from_lesson_200(): void
    {
        $this->delete("api/v1/lesson/{$this->material->id}/material", ['material_id' => $this->material->id])->assertStatus(200);
        $this->assertEquals($this->lesson->materials()->count(), 0);
    }

}