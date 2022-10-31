<?php

namespace Tests\Feature\Profile\v1;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class updateProfileTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_update_my_profile_data(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $url = route('api.v1.update-data-my-profile-auth');

        $response = $this->postJson($url, [
            'first-name' => 'Raul Alberto',
            'last-name' => 'Moheno Zavaleta',
            'phone' => '695391881',
            'email' => 'ramz.162025@gmail.com',
        ]);

        $user = User::find($user->getRouteKey());

        $this->assertSame($user->first_name, 'Raul Alberto');
        $this->assertSame($user->last_name, 'Moheno Zavaleta');
        $this->assertSame($user->phone, '695391881');
        $this->assertSame($user->email, 'ramz.162025@gmail.com');

        $this->assertDatabaseHas('users', [
            'first_name' => 'Raul Alberto',
            'last_name' => 'Moheno Zavaleta',
            'phone' => '695391881',
            'email' => 'ramz.162025@gmail.com',
        ]);
        // asserts...
        $response->assertExactJson([
            'data' => [
                'type' => 'users',
                'id' => (string) $user->getRouteKey(),
                'attributes' => [
                    'dni' => $user->dni,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'phone' => $user->phone,
                    'state_account' => $user->state === 1 ? 'enable': 'disabled',
                    'email' => $user->email,
                    "email_verified_at" => ($user->email_verified_at !== null) ? $user->email_verified_at->format('Y-m-d h:m:s') : null ,
                    "last_session" => ($user->last_session !== null) ? $user->last_session->format('Y-m-d h:m:s') : null ,
                    "created_at" => $user->created_at->format('Y-m-d h:m:s')
                ],
                'relationships' => []
            ]
        ]);
    }
}
