<?php

namespace Tests\Feature\Profile\v1;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class changePasswordTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_update_my_password_secure(): void
    {

        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $url = route('api.v1.change-password-auth');

        $response = $this->postJson($url, [
            'current-password' => 'academia750',
            'password' => 'QQScLnAZ#HTg4',
            'password_confirmation' => 'QQScLnAZ#HTg4'
        ]);

        // asserts...
        $response->assertOk();

        $this->assertTrue(Hash::check('QQScLnAZ#HTg4', $user->password));
    }

    /** @test */
    public function cannot_update_with_password_unsure(): void {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $url = route('api.v1.change-password-auth');

        $response = $this->postJson($url, [
            'current-password' => 'academia750',
            'password' => 'QQScLnAZHTg4',
            'password_confirmation' => 'QQScLnAZHTg4'
        ]);

        // asserts...
        $response->assertStatus(422);

        $response->assertJsonValidationErrors('password');

        // Attemp 2

        $response = $this->postJson($url, [
            'current-password' => 'academia750',
            'password' => 'QQScLnAZHTg$',
            'password_confirmation' => 'QQScLnAZHTg$'
        ]);

        // asserts...
        $response->assertStatus(422);

        $response->assertJsonValidationErrors('password');
    }
}
