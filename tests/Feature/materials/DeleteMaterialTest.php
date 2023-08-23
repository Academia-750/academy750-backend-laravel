<?php

namespace Tests\Feature;

use App\Core\Resources\Storage\Services\DummyStorage;
use App\Models\Lesson;
use App\Models\Material;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Mockery;
use Tests\TestCase;


class DeleteMaterialTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private $user;
    private $material;


    public function setUp(): void
    {
        parent::setUp();

        $this->withHeaders([
            'Accept' => 'application/json'
        ]);

        $this->user = User::factory()->admin()->create();

        $this->actingAs($this->user);

        $this->material = Material::factory()->create();
    }


    /** @test */
    public function not_logged_401(): void
    {
        Auth::logout();
        $this->delete("api/v1/material/{$this->material->id}")->assertStatus(401);
    }

    /** @test */
    public function only_admin_403(): void
    {
        $user = User::factory()->student()->create();
        $this->actingAs($user)->delete("api/v1/material/{$this->material->id}")->assertStatus(403);
    }


    /** @test */
    public function material_not_found_404(): void
    {
        $this->delete("api/v1/material/99")->assertStatus(404);
    }


    /** @test */
    public function delete_material_200(): void
    {

        $storageSpy = $this->spy(DummyStorage::class)->makePartial();

        $this->delete("api/v1/material/{$this->material->id}", )->assertStatus(200)->json();

        $material = Material::find($this->material->id);
        $this->assertEquals($material, null);
        // Because URL was empty
        $storageSpy->shouldNotHaveReceived('deleteFile'); // because original URL is empty

    }

    /** @test */
    public function delete_material_with_url_200(): void
    {

        $storageSpy = $this->spy(DummyStorage::class)->makePartial();

        $material = Material::factory()->state(['type' => 'material', 'url' => $this->faker()->url()])->create();

        $this->delete("api/v1/material/{$material->id}")->assertStatus(200)->json();

        $storageSpy->shouldHaveReceived('deleteFile')->once()->with(Mockery::on(function ($argument) use ($material) {
            return $argument->id === $material->id;
        }));

    }

    /** @test */
    public function error_from_storage_424(): void
    {

        $storageMock = $this->mock(DummyStorage::class)->makePartial();
        $storageMock->shouldReceive('deleteFile')->andReturn(['status' => 401, 'error' => 'Not authorized']);

        $material = Material::factory()->state(['type' => 'material', 'url' => $this->faker()->url()])->create();
        $this->delete("api/v1/material/{$material->id}")
            ->assertStatus(424);

    }

    /** @test */
    public function deleted_material_removed_from_lesson_200(): void
    {
        $lesson = Lesson::factory()->create();
        $lesson->materials()->attach($this->material);
        $this->assertEquals($lesson->materials()->count(), 1);

        $this->delete("api/v1/material/{$this->material->id}")
            ->assertStatus(200);

        $this->assertEquals($lesson->materials()->count(), 0);
    }


}