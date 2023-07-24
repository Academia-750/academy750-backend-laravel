<?php

namespace App\Core\Services;

use App\Models\User;
use Illuminate\Support\Str;

class UuidGeneratorService
{
    public static function getUUIDUnique ($instanceModel, string $fieldUnique = 'uuid'): \Ramsey\Uuid\UuidInterface
    {
        $uuidGenerated = self::generateNewUUID();

        while (self::existUUIDInTableUser($uuidGenerated, $instanceModel, $fieldUnique)) {
            $uuidGenerated = self::generateNewUUID();
        }

        return $uuidGenerated;
    }

    public static function existUUIDInTableUser ($uuid, $instanceModel, $fieldUnique): bool {
        $existsUuid = $instanceModel::query()
            ->where($fieldUnique , "=", $uuid)
            ->first();

        return $existsUuid !== null;
    }

    public static function generateNewUUID (): \Ramsey\Uuid\UuidInterface
    {
        return Str::uuid();
    }
}
