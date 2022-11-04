<?php

namespace Tests\Feature\Students\v1\getStudents;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\traits\AuthServiceTraitTest;

class PaginateStudentsTest extends TestCase
{
    use RefreshDatabase;
    use AuthServiceTraitTest;

    /** @test */
    public function can_paginate_students(): void
    {
        $testInstance = $this;

        $user1 = User::factory()->create([
            'first_name' => 'Adolfo'
        ]);
        $user1->assignRole($this->roleStudent);

        $user2 = User::factory()->create([
            'first_name' => 'Alberto'
        ]);
        $user2->assignRole($this->roleStudent);

        $user3 = User::factory()->create([
            'first_name' => 'Carlos'
        ]);
        $user3->assignRole($this->roleStudent);

        $user4 = User::factory()->create([
            'first_name' => 'Marco'
        ]);
        $user4->assignRole($this->roleStudent);

        $user5 = User::factory()->create([
            'first_name' => 'Ramon'
        ]);
        $user5->assignRole($this->roleStudent);

        $user6 = User::factory()->create([
            'first_name' => 'Raul'
        ]);
        $user6->assignRole($this->roleStudent);

        // articles?page[size]=2&page[number]=2
        $url = route('api.v1.users.index'). "?filter[role]=student&sort=first-name&page[size]=2&page[number]=2";

        $response = $this->getJson($url);

        $response->assertOk();

        $response->assertSee([
            $user3->getRouteKey(),
            $user4->getRouteKey()
        ]);

        $response->assertDontSee([
            $user1->getRouteKey(),
            $user2->getRouteKey(),
            $user5->getRouteKey(),
            $user6->getRouteKey(),
        ]);

        $response->assertJsonStructure([
            'links' => ['first', 'last', 'prev', 'next']
        ]);

        $firstLink = urldecode($response->json('links.first'));
        $lastLink = urldecode($response->json('links.last'));
        $prevLink = urldecode($response->json('links.prev'));
        $nextLink = urldecode($response->json('links.next'));

        $this->assertStringContainsString('page[size]=2', $firstLink);
        $this->assertStringContainsString('page[number]=1', $firstLink);

        $this->assertStringContainsString('page[size]=2', $lastLink);
        $this->assertStringContainsString('page[number]=3', $lastLink);

        $this->assertStringContainsString('page[size]=2', $prevLink);
        $this->assertStringContainsString('page[number]=1', $prevLink);

        $this->assertStringContainsString('page[size]=2', $nextLink);
        $this->assertStringContainsString('page[number]=3', $nextLink);
    }
}
