<?php

namespace Tests\Feature\Students\v1\getStudents;

use App\Core\Services\UserServiceTrait;
use App\Models\User;
use Carbon\Carbon;
use Faker\Provider\es_ES\Person;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Tests\traits\AuthServiceTraitTest;

class FilterStudentsTest extends TestCase
{
    use RefreshDatabase;
    use UserServiceTrait;
    use AuthServiceTraitTest;

    /** @test */
    public function can_filter_students_by_dni(): void
    {
        $dni1 = $this->generateDNIUnique();
        $dni2 = $this->generateDNIUnique();
        $dni3 = $this->generateDNIUnique();

        $user1 = User::factory()->create([
            'dni' => $dni1
        ]);
        $user1->assignRole($this->roleStudent);
        $user2 = User::factory()->create([
            'dni' => $dni2
        ]);
        $user2->assignRole($this->roleStudent);
        $user3 = User::factory()->create([
            'dni' => $dni3
        ]);
        $user3->assignRole($this->roleStudent);

        $url = route('api.v1.students.index'). "?filter[role]=student&filter[dni]={$dni1}";

        $response = $this->getJson($url);

        // asserts...
        $response
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertSee($dni1)
            ->assertDontSee($dni2)
            ->assertDontSee($dni3);
    }

    /** @test */
    public function can_filter_students_by_first_name(): void
    {
        $user1 = User::factory()->create([
            'first_name' => 'Adolfo'
        ]);
        $user1->assignRole($this->roleStudent);

        $user2 = User::factory()->create([
            'first_name' => 'Albert'
        ]);
        $user2->assignRole($this->roleStudent);

        $user3 = User::factory()->create([
            'first_name' => 'Carlos'
        ]);
        $user3->assignRole($this->roleStudent);

        $user4 = User::factory()->create([
            'first_name' => 'Alberto'
        ]);
        $user4->assignRole($this->roleStudent);

        $url = route('api.v1.students.index'). "?filter[role]=student&filter[first-name]=Alber";

        $response = $this->getJson($url);

        // asserts...
        $response
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertSee('Albert')
            ->assertSee('Alberto')
            ->assertDontSee('Carlos')
            ->assertDontSee('Adolfo');

        $url = route('api.v1.students.index'). "?filter[role]=student&filter[first-name]=Carlos";

        $response = $this->getJson($url);

        // asserts...
        $response
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertDontSee('Adolfo')
            ->assertDontSee('Alberto')
            ->assertSee('Carlos')
            ->assertDontSee('Raul');
    }

    /** @test */
    public function can_filter_students_by_last_name(): void
    {
        $user1 = User::factory()->create([
            'last_name' => 'Moheno'
        ]);
        $user1->assignRole($this->roleStudent);

        $user2 = User::factory()->create([
            'last_name' => 'Martinez'
        ]);
        $user2->assignRole($this->roleStudent);

        $user3 = User::factory()->create([
            'last_name' => 'Feria'
        ]);
        $user3->assignRole($this->roleStudent);

        $user4 = User::factory()->create([
            'last_name' => 'Herrera'
        ]);
        $user4->assignRole($this->roleStudent);

        $url = route('api.v1.students.index'). "?filter[role]=student&filter[last-name]=M";

        $response = $this->getJson($url);

        // asserts...
        $response
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertSee('Moheno')
            ->assertSee('Martinez')
            ->assertDontSee('Feria')
            ->assertDontSee('Herrera');

        $url = route('api.v1.students.index'). "?filter[role]=student&filter[last-name]=Feria";

        $response = $this->getJson($url);

        // asserts...
        $response
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertDontSee('Martinez')
            ->assertDontSee('Moheno')
            ->assertSee('Feria')
            ->assertDontSee('Herrera');
    }

    /** @test */
    public function can_filter_students_by_phone(): void
    {

        $phone1 = $this->getNumberPhoneSpain();
        $phone2 = $this->getNumberPhoneSpain();
        $phone3 = $this->getNumberPhoneSpain();
        $phone4 = $this->getNumberPhoneSpain();

        $user1 = User::factory()->create([
            'phone' => $phone1
        ]);
        $user1->assignRole($this->roleStudent);

        $user2 = User::factory()->create([
            'phone' => $phone2
        ]);
        $user2->assignRole($this->roleStudent);

        $user3 = User::factory()->create([
            'phone' => $phone3
        ]);
        $user3->assignRole($this->roleStudent);

        $user4 = User::factory()->create([
            'phone' => $phone4
        ]);
        $user4->assignRole($this->roleStudent);

        $url = route('api.v1.students.index'). "?filter[role]=student&filter[phone]={$phone2}";

        $response = $this->getJson($url);

        // asserts...
        $response
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertSee($phone2)
            ->assertDontSee($phone1)
            ->assertDontSee($phone3)
            ->assertDontSee($phone4);
    }

    /** @test */
    public function can_filter_students_by_last_session(): void
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

        $url = route('api.v1.students.index'). "?filter[role]=student&filter[last-session]={$lastSession3->format('Y-m-d')}";

        $response = $this->getJson($url);

        // asserts...
        $response
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertSee($user3->last_session->format('Y-m-d h:m:s'))
            ->assertDontSee($user1->last_session->format('Y-m-d h:m:s'))
            ->assertDontSee($user2->last_session->format('Y-m-d h:m:s'))
            ->assertDontSee($user4->last_session->format('Y-m-d h:m:s'));
    }

    /** @test */
    public function can_filter_students_by_state_account(): void
    {

        $user1 = User::factory()->create();
        $user1->assignRole($this->roleStudent);

        $user2 = User::factory()->create();
        $user2->assignRole($this->roleStudent);

        $user3 = User::factory()->create([
            'state' => 'disable'
        ]);
        $user3->assignRole($this->roleStudent);

        $url = route('api.v1.students.index'). "?filter[role]=student&filter[state-account]=enable";

        $response = $this->getJson($url);

        // asserts...
        $response
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertSee($user1->name)
            ->assertSee($user2->name)
            ->assertDontSee($user3->name);

        $url = route('api.v1.students.index'). "?filter[role]=student&filter[state-account]=disable";

        $response = $this->getJson($url);

        // asserts...
        $response
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertDontSee($user1->name)
            ->assertDontSee($user2->name)
            ->assertSee($user3->name);
    }

    /** @test */
    public function can_filter_students_by_email(): void
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

        $url = route('api.v1.students.index'). "?filter[role]=student&filter[email]=raul.moheno.webmaster@gmail.com";

        $response = $this->getJson($url);

        // asserts...
        $response
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertSee($user1->name)
            ->assertDontSee($user2->name)
            ->assertDontSee($user3->name);
    }
}
