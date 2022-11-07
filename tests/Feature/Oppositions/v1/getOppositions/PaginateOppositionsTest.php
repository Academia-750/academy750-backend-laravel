<?php

namespace Tests\Feature\Oppositions\v1\getOppositions;

use App\Models\Opposition;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\traits\AuthServiceTraitTest;

class PaginateOppositionsTest extends TestCase
{
    use RefreshDatabase;
    use AuthServiceTraitTest;

    /** @test */
    public function can_paginate_oppositions(): void
    {
        $opposition1 = Opposition::factory()->create([
            'name' => 'A'
        ]);
        $opposition2 = Opposition::factory()->create([
            'name' => 'B'
        ]);
        $opposition3 = Opposition::factory()->create([
            'name' => 'C'
        ]);
        $opposition4 = Opposition::factory()->create([
            'name' => 'D'
        ]);
        $opposition5 = Opposition::factory()->create([
            'name' => 'E'
        ]);
        $opposition6 = Opposition::factory()->create([
            'name' => 'F'
        ]);

        $url = route('api.v1.oppositions.index'). "?sort=name&page[size]=2&page[number]=2";

        $response = $this->getJson($url);

        $response->assertOk();

        $response->assertSee([
            $opposition3->getRouteKey(),
            $opposition4->getRouteKey()
        ]);

        $response->assertDontSee([
            $opposition1->getRouteKey(),
            $opposition2->getRouteKey(),
            $opposition5->getRouteKey(),
            $opposition6->getRouteKey(),
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
