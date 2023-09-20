<?php

namespace Tests\Feature;


use App\Models\Lesson;
use App\Models\Material;
use App\Models\Permission;
use App\Models\User;
use DB;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;


class StudentLessonSearchTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private $user;

    private $lessons;

    private $body;

    public function setUp(): void
    {
        parent::setUp();

        $this->withHeaders([
            'Accept' => 'application/json'
        ]);

        $this->user = User::factory()
            ->student()
            ->allowedTo([Permission::SEE_LESSONS])
            ->create();


        // 2 Lessons, 2 materials 1 recording
        $this->lessons = Lesson::factory()
            ->withStudents($this->user)
            ->count(4)
            ->create(['is_active' => true]);



        $this->actingAs($this->user);
    }



    /** @test */
    public function not_logged_401(): void
    {
        Auth::logout();
        $this->get("api/v1/student-lessons/search?")->assertStatus(401);
    }


    /** @test */
    public function no_permissions_403(): void
    {
        $user = User::factory()->student()->create();
        $this->actingAs($user)->get("api/v1/student-lessons/search?" . Arr::query([]))->assertStatus(403);
    }

    /** @test */
    public function wrong_parameters_422(): void
    {
        $this->get("api/v1/student-lessons/search?" . Arr::query(['limit' => "wrong"]))->assertStatus(422); // Wrong type
        $this->get("api/v1/student-lessons/search?" . Arr::query(['limit' => -10]))->assertStatus(422); //negative
        $this->get("api/v1/student-lessons/search?" . Arr::query(['content' => [false]]))->assertStatus(422); //wrong_type
    }


    /** @test */
    public function search_no_params_200(): void
    {

        $data = $this->get("api/v1/student-lessons/search?" . Arr::query([]))->assertStatus(200);
        $this->assertEquals(count($data['results']), 4);
    }

    /** @test */
    public function search_result_data_200(): void
    {

        $data = $this->get("api/v1/student-lessons/search?" . Arr::query(["content" => $this->lessons[0]->name]))->assertStatus(200);
        $this->assertEquals(count($data['results']), 1);
        $this->assertEquals($data['results'][0]['name'], $this->lessons[0]->name);
        $this->assertEquals($data['results'][0]['id'], $this->lessons[0]->id);
        $this->assertEquals($data['results'][0]['date'], $this->lessons[0]->date);
    }

    /** @test */
    public function search_no_data_200(): void
    {

        $data = $this->get("api/v1/student-lessons/search?" . Arr::query(["content" => 'randomName']))->assertStatus(200);
        $this->assertEquals(count($data['results']), 0);
    }


    /** @test */
    public function limit(): void
    {
        $data = $this->get("api/v1/student-lessons/search?" . Arr::query(["limit" => 1]))->assertStatus(200);
        $this->assertEquals(count($data['results']), 1);

        $data = $this->get("api/v1/student-lessons/search?" . Arr::query(["limit" => 2]))->assertStatus(200);
        $this->assertEquals(count($data['results']), 2);
    }

    /** @test */
    public function can_not_see_other_lessons_200(): void
    {
        // The student is not assigned this lessons
        $lessons = Lesson::factory()
            ->count(2)
            ->create(['is_active' => true]);

        $data = $this->get("api/v1/student-lessons/search?" . Arr::query([]))->assertStatus(200);
        $this->assertEquals(count($data['results']), 4);

        $myLessonsId = $this->map($data['results'], 'id');

        foreach ($lessons as $lesson) {
            $this->assertFalse(in_array($lesson->id, $myLessonsId));
        }
    }

}