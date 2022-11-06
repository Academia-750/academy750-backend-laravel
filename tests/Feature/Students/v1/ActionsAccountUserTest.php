<?php

namespace Tests\Feature\Students\v1;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\traits\AuthServiceTraitTest;

class ActionsAccountUserTest extends TestCase
{
    use RefreshDatabase;
    use AuthServiceTraitTest;

    /** @test */
    public function can_enable_account_user(): void
    {
        $user = User::factory()->create();

        $url = route('api.v1.users.disable-account', [ 'user' => $user->getRouteKey() ]);

        $response = $this->postJson($url);

        // asserts...

        $response->assertOk();

        $user->refresh();

        $this->assertSame($user->state, 'disable');

        $this->assertNotNull($user->deleted_at);

        $response->assertExactJson([
            'data' => [
                'type' => 'users',
                'id' => (string) $user->getRouteKey(),
                'attributes' => [
                    'dni' => $user->dni,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'phone' => $user->phone,
                    'state_account' => $user->state,
                    'email' => $user->email,
                    "email_verified_at" => ($user->email_verified_at !== null) ? $user->email_verified_at->format('Y-m-d h:m:s') : null ,
                    "last_session" => ($user->last_session !== null) ? $user->last_session->format('Y-m-d h:m:s') : null ,
                    "created_at" => $user->created_at->format('Y-m-d h:m:s')
                ],
                'relationships' => []
            ]
        ]);
    }

    /** @test */
    public function can_unlock_account_user(): void
    {
        $user = User::factory()->create();

        $url = route('api.v1.users.enable-account', [ 'user' => $user->getRouteKey() ]);

        $response = $this->postJson($url);

        // asserts...

        $response->assertOk();

        $user->refresh();

        $this->assertSame($user->state, 'enable');

        $this->assertNull($user->deleted_at);

        $response->assertExactJson([
            'data' => [
                'type' => 'users',
                'id' => (string) $user->getRouteKey(),
                'attributes' => [
                    'dni' => $user->dni,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'phone' => $user->phone,
                    'state_account' => $user->state,
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
