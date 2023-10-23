<?php

namespace Tests\Feature;


use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class UserSearchTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private $user; // Admin user
    private $users;

    public function setUp(): void
    {
        parent::setUp();

        $this->withHeaders([
            'Accept' => 'application/json'
        ]);

        $this->user = User::factory()->admin()->create();

        $this->actingAs($this->user);

        $this->users = User::factory()->student()->count(4)->create();
    }


    /** @test */
    public function not_logged_401(): void
    {
        Auth::logout();
        $this->get("api/v1/users/search", [])->assertStatus(401);
    }


    /** @test */
    public function only_admin_403(): void
    {
        $user = User::factory()->student()->create();
        $this->actingAs($user)->get("api/v1/users/search", [])->assertStatus(403);

    }


    /** @test */
    public function wrong_params_422(): void
    {
        $this->get("api/v1/users/search?" . Arr::query(['content' => ['aa', 'bb']]))->assertStatus(422);
    }



    /** @test */
    public function empty_search_200(): void
    {
        $this->get("api/v1/users/search?" . Arr::query(['content' => '']))->assertStatus(200);
    }

    /** @test */
    public function find_user_by_first_name_200(): void
    {
        $user = $this->users[0];
        $res = $this->get("api/v1/users/search?" . Arr::query(['content' => substr($user->first_name, 0, 3)]))->assertStatus(200)->json();
        $this->assertEquals($res['results'][0]['id'], $user->id);
    }

    /** @test */
    public function content_format_200(): void
    {
        array_map(function ($valid_string) {
            $this->get("api/v1/users/search?" . Arr::query(['content' => $valid_string]))->assertStatus(200);
        }, $this->valid_string_input);

    }


    /** @test */
    public function find_user_by_last_name_200(): void
    {
        $user = $this->users[0];
        $res = $this->get("api/v1/users/search?" . Arr::query(['content' => substr($user->last_name, 0, 3)]))->assertStatus(200)->json();
        $this->assertEquals($res['results'][0]['id'], $user->id);
    }

    /** @test */
    public function find_user_by_dni_200(): void
    {
        $user = $this->users[0];
        $res = $this->get("api/v1/users/search?" . Arr::query(['content' => substr($user->dni, 0, 3)]))->assertStatus(200)->json();
        $this->assertEquals($res['results'][0]['id'], $user->id);
    }

    /** @test */
    public function no_admin_users_exposed_200(): void
    {
        $adminUser = User::factory()->admin()->create();

        $res = $this->get("api/v1/users/search?" . Arr::query(['content' => $adminUser->dni]))->assertStatus(200)->json();
        $this->assertEmpty($res['results']);
    }

    /** @test */
    public function no_disabled_user_exposed_200(): void
    {
        $disabledUser = User::factory()->student()->state(['state' => 'disable'])->create();

        $res = $this->get("api/v1/users/search?" . Arr::query(['content' => $disabledUser->dni]))->assertStatus(200)->json();
        $this->assertEmpty($res['results']);
    }
}