<?php

namespace App\Core\Services;

use App\Models\User;
use Illuminate\Support\Str;

trait UserServiceTrait
{
    public function getUUIDUnique (): \Ramsey\Uuid\UuidInterface
    {
        $uuidGenerated = $this->generateNewUUID();

        while ($this->existUUIDInTableUser($uuidGenerated)) {
            $uuidGenerated = $this->generateNewUUID();
        }

        return $uuidGenerated;
    }

    public function existUUIDInTableUser ($uuid): bool {
        $existsUuid = User::query()->where("id","=", $uuid)
            ->first();

        return $existsUuid !== null;
    }

    public function generateNewUUID (): \Ramsey\Uuid\UuidInterface
    {
        return Str::orderedUuid();
    }
}
