<?php

namespace Tests\Unit;

use App\Core\Services\UuidGeneratorService;
use App\Models\User;
use Illuminate\Support\Str;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class GeneratorUuidTest extends TestCase
{
    /** @test */
    public function can_generate_a_valid_uuid(): void
    {
        $uuidGenerated = UuidGeneratorService::generateNewUUID();
        $this->assertTrue(Uuid::isValid($uuidGenerated));
    }

    /** @test */
    public function can_generate_a_no_valid_uuid(): void
    {
        $stringRandom = Str::random(36);
        $this->assertFalse(Uuid::isValid($stringRandom));
    }
}
