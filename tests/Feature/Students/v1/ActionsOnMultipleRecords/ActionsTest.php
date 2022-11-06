<?php

namespace Tests\Feature\Students\v1\ActionsOnMultipleRecords;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\traits\AuthServiceTraitTest;

class ActionsTest extends TestCase
{
    use RefreshDatabase;
    use AuthServiceTraitTest;

    /** @test */
    public function can_delete_multiple_users(): void
    {
        $user1 = User::factory()->create();
        //$user1->assignRole($this->roleStudent);

        $user2 = User::factory()->create();
        //$user2->assignRole($this->roleStudent);

        $user3 = User::factory()->create();
        //$user3->assignRole($this->roleStudent);

        $url = route('api.v1.users.actions-on-multiple-records');

        $data = [
            "action" => "delete",
            "users" => [
                $user1->getRouteKey(),
                $user2->getRouteKey(),
                $user3->getRouteKey(),
            ]
        ];

        $response = $this->postJson($url, $data);

        // asserts...

        $response->assertOk();

        $this->assertDatabaseMissing('users', $user1->toArray());
        $this->assertDatabaseMissing('users', $user2->toArray());
        $this->assertDatabaseMissing('users', $user3->toArray());
    }

    /** @test */
    public function can_lock_account_multiple_users(): void
    {
        $user1 = User::factory()->create();
        //$user1->assignRole($this->roleStudent);

        $user2 = User::factory()->create();
        //$user2->assignRole($this->roleStudent);

        $user3 = User::factory()->create();
        //$user3->assignRole($this->roleStudent);

        $url = route('api.v1.users.actions-on-multiple-records');

        $data = [
            "action" => "lock-account",
            "users" => [
                $user1->getRouteKey(),
                $user2->getRouteKey(),
                $user3->getRouteKey(),
            ]
        ];

        $response = $this->postJson($url, $data);

        // asserts...

        $response->assertOk();

        $user1->refresh();
        $user2->refresh();
        $user3->refresh();

        $this->assertSame($user1->state, 'disable');
        $this->assertSame($user2->state, 'disable');
        $this->assertSame($user3->state, 'disable');

        $this->assertNotNull($user1->deleted_at);
        $this->assertNotNull($user2->deleted_at);
        $this->assertNotNull($user3->deleted_at);
    }

    /** @test */
    public function can_unlock_account_multiple_users(): void
    {
        $user1 = User::factory()->create([
            'state' => 'disable'
        ]);
        $user1->delete();
        //$user1->assignRole($this->roleStudent);

        $user2 = User::factory()->create([
            'state' => 'disable'
        ]);
        $user2->delete();
        //$user2->assignRole($this->roleStudent);

        $user3 = User::factory()->create([
            'state' => 'disable'
        ]);
        $user3->delete();
        //$user3->assignRole($this->roleStudent);

        $url = route('api.v1.users.actions-on-multiple-records');

        $data = [
            "action" => "unlock-account",
            "users" => [
                $user1->getRouteKey(),
                $user2->getRouteKey(),
                $user3->getRouteKey(),
            ]
        ];

        $response = $this->postJson($url, $data);

        // asserts...

        $response->assertOk();

        $user1->refresh();
        $user2->refresh();
        $user3->refresh();

        $this->assertSame($user1->state, 'enable');
        $this->assertSame($user2->state, 'enable');
        $this->assertSame($user3->state, 'enable');

        $this->assertNull($user1->deleted_at);
        $this->assertNull($user2->deleted_at);
        $this->assertNull($user3->deleted_at);
    }
}
