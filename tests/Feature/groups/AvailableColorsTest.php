<?php

namespace Tests\Feature;

use App\Models\Group;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class AvailableColorsTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private $user;
    private $groups;

    private $colors;


    public function setUp(): void
    {
        parent::setUp();

        $this->withHeaders([
            'Accept' => 'application/json'
        ]);

        $this->user = User::factory()->admin()->create();

        $this->actingAs($this->user);

        $this->colors = config('data.group_colors');
    }


    /** @test */
    public function not_logged_401(): void
    {
        Auth::logout();
        $this->get("api/v1/group/colors")->assertStatus(401);
    }

    /** @test */
    public function only_admin_403(): void
    {
        $user = User::factory()->student()->create();
        $this->actingAs($user)->get("api/v1/group/colors")->assertStatus(403);
    }


    /** @test */
    public function get_all_colors_200(): void
    {
        $data = $this->get("api/v1/group/colors", [])->assertStatus(200)->decodeResponseJson();
        $this->assertEquals(count($data['results']), count($this->colors));

    }

    /** @test */
    public function get_skip_used_colors_200(): void
    {

        $this->post('api/v1/group', Group::factory()->raw(['color' => $this->colors[1]]))->assertStatus(200);
        $this->post('api/v1/group', Group::factory()->raw(['color' => $this->colors[3]]))->assertStatus(200);

        $data = $this->get("api/v1/group/colors", [])->assertStatus(200)->decodeResponseJson();
        $diff = array_diff($this->colors, $data['results']);
        $usedColors = [$this->colors[1], $this->colors[3]];
        $this->assertEquals(sort($diff), sort($usedColors));
    }
}