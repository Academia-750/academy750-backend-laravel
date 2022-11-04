<?php

namespace Tests\Feature\Students\v1\getStudents;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\traits\AuthServiceTraitTest;

class SortStudentsTest extends TestCase
{
    use RefreshDatabase;
    use AuthServiceTraitTest;

    /** @test */
    public function can_sort_students_by_dni_ascending(): void
    {
        $user1 = User::factory()->create([
            'dni' => '99218876G'
        ]);
        $user1->assignRole($this->roleStudent);

        $user2 = User::factory()->create([
            'dni' => '63916529N'
        ]);
        $user2->assignRole($this->roleStudent);

        $user3 = User::factory()->create([
            'dni' => '54912547R'
        ]);
        $user3->assignRole($this->roleStudent);


        $url = route('api.v1.students.index'). "?filter[role]=student&sort=dni";

        $response = $this->getJson($url);

        // asserts...
        $response
            ->assertOk()
            ->assertSeeInOrder([
                '54912547R',
                '63916529N',
                '99218876G',
            ]);
    }

    /** @test */
    public function can_sort_students_by_dni_descending(): void
    {
        $user1 = User::factory()->create([
            'dni' => '99218876G'
        ]);
        $user1->assignRole($this->roleStudent);

        $user2 = User::factory()->create([
            'dni' => '63916529N'
        ]);
        $user2->assignRole($this->roleStudent);

        $user3 = User::factory()->create([
            'dni' => '54912547R'
        ]);
        $user3->assignRole($this->roleStudent);


        $url = route('api.v1.students.index'). "?filter[role]=student&sort=-dni";

        $response = $this->getJson($url);

        // asserts...
        $response
            ->assertOk()
            ->assertSeeInOrder([
                '99218876G',
                '63916529N',
                '54912547R',
            ]);
    }

    /** @test */
    public function can_sort_students_by_first_name_ascending(): void
    {
        $user1 = User::factory()->create([
            'first_name' => 'Carlos'
        ]);
        $user1->assignRole($this->roleStudent);

        $user2 = User::factory()->create([
            'first_name' => 'Adolfo'
        ]);
        $user2->assignRole($this->roleStudent);

        $user3 = User::factory()->create([
            'first_name' => 'Raul'
        ]);
        $user3->assignRole($this->roleStudent);


        $url = route('api.v1.students.index'). "?filter[role]=student&sort=first-name";

        $response = $this->getJson($url);

        // asserts...
        $response
            ->assertOk()
            ->assertSeeInOrder([
                'Adolfo',
                'Carlos',
                'Raul',
            ]);
    }

    /** @test */
    public function can_sort_students_by_first_name_descending(): void
    {
        $user1 = User::factory()->create([
            'first_name' => 'Carlos'
        ]);
        $user1->assignRole($this->roleStudent);

        $user2 = User::factory()->create([
            'first_name' => 'Raul'
        ]);
        $user2->assignRole($this->roleStudent);

        $user3 = User::factory()->create([
            'first_name' => 'Adolfo'
        ]);
        $user3->assignRole($this->roleStudent);


        $url = route('api.v1.students.index'). "?filter[role]=student&sort=-first-name";

        $response = $this->getJson($url);

        // asserts...
        $response
            ->assertOk()
            ->assertSeeInOrder([
                'Raul',
                'Carlos',
                'Adolfo',
            ]);
    }

    /** @test */
    public function can_sort_students_by_last_name_ascending(): void
    {
        $user1 = User::factory()->create([
            'last_name' => 'Herrera'
        ]);
        $user1->assignRole($this->roleStudent);

        $user2 = User::factory()->create([
            'last_name' => 'Moheno'
        ]);
        $user2->assignRole($this->roleStudent);

        $user3 = User::factory()->create([
            'last_name' => 'Feria'
        ]);
        $user3->assignRole($this->roleStudent);


        $url = route('api.v1.students.index'). "?filter[role]=student&sort=last-name";

        $response = $this->getJson($url);

        // asserts...
        $response
            ->assertOk()
            ->assertSeeInOrder([
                'Feria',
                'Herrera',
                'Moheno',
            ]);
    }

    /** @test */
    public function can_sort_students_by_last_name_descending(): void
    {
        $user1 = User::factory()->create([
            'last_name' => 'Herrera'
        ]);
        $user1->assignRole($this->roleStudent);

        $user2 = User::factory()->create([
            'last_name' => 'Moheno'
        ]);
        $user2->assignRole($this->roleStudent);

        $user3 = User::factory()->create([
            'last_name' => 'Feria'
        ]);
        $user3->assignRole($this->roleStudent);


        $url = route('api.v1.students.index'). "?filter[role]=student&sort=-last-name";

        $response = $this->getJson($url);

        // asserts...
        $response
            ->assertOk()
            ->assertSeeInOrder([
                'Moheno',
                'Herrera',
                'Feria',
            ]);
    }

    /** @test */
    public function can_sort_students_by_phone_ascending(): void
    {
        $user1 = User::factory()->create([
            'phone' => '912345678'
        ]);
        $user1->assignRole($this->roleStudent);

        $user2 = User::factory()->create([
            'phone' => '712345678'
        ]);
        $user2->assignRole($this->roleStudent);

        $user3 = User::factory()->create([
            'phone' => '812345678'
        ]);
        $user3->assignRole($this->roleStudent);

        $user4 = User::factory()->create([
            'phone' => '922345678'
        ]);
        $user4->assignRole($this->roleStudent);


        $url = route('api.v1.students.index'). "?filter[role]=student&sort=phone";

        $response = $this->getJson($url);

        // asserts...
        $response
            ->assertOk()
            ->assertSeeInOrder([
                '712345678',
                '812345678',
                '912345678',
                '922345678',
            ]);
    }

    /** @test */
    public function can_sort_students_by_phone_descending(): void
    {
        $user1 = User::factory()->create([
            'phone' => '912345678'
        ]);
        $user1->assignRole($this->roleStudent);

        $user2 = User::factory()->create([
            'phone' => '712345678'
        ]);
        $user2->assignRole($this->roleStudent);

        $user3 = User::factory()->create([
            'phone' => '812345678'
        ]);
        $user3->assignRole($this->roleStudent);

        $user4 = User::factory()->create([
            'phone' => '922345678'
        ]);
        $user4->assignRole($this->roleStudent);


        $url = route('api.v1.students.index'). "?filter[role]=student&sort=-phone";

        $response = $this->getJson($url);

        // asserts...
        $response
            ->assertOk()
            ->assertSeeInOrder([
                '922345678',
                '912345678',
                '812345678',
                '712345678',
            ]);
    }

    /** @test */
    public function can_sort_students_by_last_session_ascending(): void
    {
        $lastSession1 = Carbon::now()->addDay();
        $lastSession2 = Carbon::now()->addDays(20);
        $lastSession3 = Carbon::now()->addDays(10);
        $lastSession4 = Carbon::now()->addDays(3);

        $user1 = User::factory()->create([
            'last_session' => $lastSession1
        ]);
        $user1->assignRole($this->roleStudent);

        $user2 = User::factory()->create([
            'last_session' => $lastSession2
        ]);
        $user2->assignRole($this->roleStudent);

        $user3 = User::factory()->create([
            'last_session' => $lastSession3
        ]);
        $user3->assignRole($this->roleStudent);

        $user4 = User::factory()->create([
            'last_session' => $lastSession4
        ]);
        $user4->assignRole($this->roleStudent);


        $url = route('api.v1.students.index'). "?filter[role]=student&sort=last-session";

        $response = $this->getJson($url);

        // asserts...
        $response
            ->assertOk()
            ->assertSeeInOrder([
                $user1->last_session->format('Y-m-d h:m:s'),
                $user4->last_session->format('Y-m-d h:m:s'),
                $user3->last_session->format('Y-m-d h:m:s'),
                $user2->last_session->format('Y-m-d h:m:s'),
            ]);
    }

    /** @test */
    public function can_sort_students_by_last_session_descending(): void
    {
        $lastSession1 = Carbon::now()->addDay();
        $lastSession2 = Carbon::now()->addDays(20);
        $lastSession3 = Carbon::now()->addDays(10);
        $lastSession4 = Carbon::now()->addDays(3);

        $user1 = User::factory()->create([
            'last_session' => $lastSession1
        ]);
        $user1->assignRole($this->roleStudent);

        $user2 = User::factory()->create([
            'last_session' => $lastSession2
        ]);
        $user2->assignRole($this->roleStudent);

        $user3 = User::factory()->create([
            'last_session' => $lastSession3
        ]);
        $user3->assignRole($this->roleStudent);

        $user4 = User::factory()->create([
            'last_session' => $lastSession4
        ]);
        $user4->assignRole($this->roleStudent);


        $url = route('api.v1.students.index'). "?filter[role]=student&sort=-last-session";

        $response = $this->getJson($url);

        // asserts...
        $response
            ->assertOk()
            ->assertSeeInOrder([
                $user2->last_session->format('Y-m-d h:m:s'),
                $user3->last_session->format('Y-m-d h:m:s'),
                $user4->last_session->format('Y-m-d h:m:s'),
                $user1->last_session->format('Y-m-d h:m:s'),
            ]);
    }

    /** @test */
    public function can_sort_students_by_created_at_ascending(): void
    {
        $createdAt1 = Carbon::now()->addDay();
        $createdAt2 = Carbon::now()->addDays(20);
        $createdAt3 = Carbon::now()->addDays(10);
        $createdAt4 = Carbon::now()->addDays(3);

        $user1 = User::factory()->create([
            'created_at' => $createdAt1
        ]);
        $user1->assignRole($this->roleStudent);

        $user2 = User::factory()->create([
            'created_at' => $createdAt2
        ]);
        $user2->assignRole($this->roleStudent);

        $user3 = User::factory()->create([
            'created_at' => $createdAt3
        ]);
        $user3->assignRole($this->roleStudent);

        $user4 = User::factory()->create([
            'created_at' => $createdAt4
        ]);
        $user4->assignRole($this->roleStudent);


        $url = route('api.v1.students.index'). "?filter[role]=student&sort=created-at";

        $response = $this->getJson($url);

        // asserts...
        $response
            ->assertOk()
            ->assertSeeInOrder([
                $user1->created_at->format('Y-m-d h:m:s'),
                $user4->created_at->format('Y-m-d h:m:s'),
                $user3->created_at->format('Y-m-d h:m:s'),
                $user2->created_at->format('Y-m-d h:m:s'),
            ]);
    }

    /** @test */
    public function can_sort_students_by_created_at_descending(): void
    {
        $createdAt1 = Carbon::now()->addDay();
        $createdAt2 = Carbon::now()->addDays(20);
        $createdAt3 = Carbon::now()->addDays(10);
        $createdAt4 = Carbon::now()->addDays(3);

        $user1 = User::factory()->create([
            'created_at' => $createdAt1
        ]);
        $user1->assignRole($this->roleStudent);

        $user2 = User::factory()->create([
            'created_at' => $createdAt2
        ]);
        $user2->assignRole($this->roleStudent);

        $user3 = User::factory()->create([
            'created_at' => $createdAt3
        ]);
        $user3->assignRole($this->roleStudent);

        $user4 = User::factory()->create([
            'created_at' => $createdAt4
        ]);
        $user4->assignRole($this->roleStudent);


        $url = route('api.v1.students.index'). "?filter[role]=student&sort=-created-at";

        $response = $this->getJson($url);

        // asserts...
        $response
            ->assertOk()
            ->assertSeeInOrder([
                $user2->created_at->format('Y-m-d h:m:s'),
                $user3->created_at->format('Y-m-d h:m:s'),
                $user4->created_at->format('Y-m-d h:m:s'),
                $user1->created_at->format('Y-m-d h:m:s'),
            ]);
    }

    /** @test */
    public function can_sort_students_by_email_ascending(): void
    {
        $user1 = User::factory()->create([
            'email' => 'raul.moheno.webmaster@gmail.com'
        ]);
        $user1->assignRole($this->roleStudent);

        $user2 = User::factory()->create([
            'email' => 'carlos.herrera.academia750@gmail.com'
        ]);
        $user2->assignRole($this->roleStudent);

        $user3 = User::factory()->create([
            'email' => 'adolfo.feria.academia750@gmail.com'
        ]);
        $user3->assignRole($this->roleStudent);


        $url = route('api.v1.students.index'). "?filter[role]=student&sort=email";

        $response = $this->getJson($url);

        // asserts...
        $response
            ->assertOk()
            ->assertSeeInOrder([
                'adolfo.feria.academia750@gmail.com',
                'carlos.herrera.academia750@gmail.com',
                'raul.moheno.webmaster@gmail.com',
            ]);
    }

    /** @test */
    public function can_sort_students_by_email_descending(): void
    {
        $user1 = User::factory()->create([
            'email' => 'raul.moheno.webmaster@gmail.com'
        ]);
        $user1->assignRole($this->roleStudent);

        $user2 = User::factory()->create([
            'email' => 'carlos.herrera.academia750@gmail.com'
        ]);
        $user2->assignRole($this->roleStudent);

        $user3 = User::factory()->create([
            'email' => 'adolfo.feria.academia750@gmail.com'
        ]);
        $user3->assignRole($this->roleStudent);


        $url = route('api.v1.students.index'). "?filter[role]=student&sort=-email";

        $response = $this->getJson($url);

        // asserts...
        $response
            ->assertOk()
            ->assertSeeInOrder([
                'raul.moheno.webmaster@gmail.com',
                'carlos.herrera.academia750@gmail.com',
                'adolfo.feria.academia750@gmail.com',
            ]);
    }
}
