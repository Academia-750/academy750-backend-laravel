<?php

namespace Tests\Feature;

use App\Core\Resources\Storage\Services\DummyStorage;
use App\Models\Lesson;
use App\Models\Material;
use App\Models\Tag;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Mockery;
use Tests\TestCase;


class TagDeleteTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private $user;
    private $tag;


    public function setUp(): void
    {
        parent::setUp();

        $this->withHeaders([
            'Accept' => 'application/json'
        ]);

        $this->user = User::factory()->admin()->create();

        $this->actingAs($this->user);

        $this->tag = Tag::factory()->create(['type' => 'material']);
    }


    /** @test */
    public function not_logged_401(): void
    {
        Auth::logout();
        $this->delete("api/v1/material/tag/{$this->tag->id}")->assertStatus(401);
    }

    /** @test */
    public function only_admin_403(): void
    {
        $user = User::factory()->student()->create();
        $this->actingAs($user)->delete("api/v1/material/tag/{$this->tag->id}")->assertStatus(403);
    }

    /** @test */
    public function wrong_material_type_409(): void
    {
        $tag = Tag::factory()->create(['type' => 'other']);
        $this->delete("api/v1/material/tag/{$tag->id}")->assertStatus(409);
    }


    /** @test */
    public function material_not_found_404(): void
    {
        $this->delete("api/v1/material/tag/99")->assertStatus(404);
    }


    /** @test */
    public function delete_material_tag_200(): void
    {
        $this->delete("api/v1/material/tag/{$this->tag->id}", )->assertStatus(200);

        $tag = Tag::find($this->tag->id);
        $this->assertEquals($tag, null);
    }

}