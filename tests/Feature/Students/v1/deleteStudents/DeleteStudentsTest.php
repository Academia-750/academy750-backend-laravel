<?php

namespace Tests\Feature\Students\v1\deleteStudents;

use App\Core\Services\UuidGeneratorService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\traits\AuthServiceTraitTest;

class DeleteStudentsTest extends TestCase
{
    use RefreshDatabase;
    use AuthServiceTraitTest;

    /** @test */
    public function can_delete_a_student(): void
    {
        $user = User::factory()->create();

        $url = route('api.v1.users.delete', [ 'user' => $user->getRouteKey() ]);

        $response = $this->deleteJson($url);

        // asserts...

        $response->assertNoContent();

        $this->assertDatabaseMissing('users', $user->toArray());
    }

    /** @test */
    public function cannot_a_delete_a_unknown_student(): void
    {
        $url = route('api.v1.users.delete', [ 'user' => UuidGeneratorService::getUUIDUnique(User::class) ]);

        $response = $this->deleteJson($url);

        // asserts...

        $response->assertNotFound();
    }
}
