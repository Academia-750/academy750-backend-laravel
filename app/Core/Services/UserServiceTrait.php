<?php

namespace App\Core\Services;

use App\Models\User;
use Faker\Provider\es_ES\Person;
use Illuminate\Support\Str;

trait UserServiceTrait
{
    public function getUUIDUnique ($instanceModel, $fieldUnique = 'uuid'): \Ramsey\Uuid\UuidInterface
    {
        return UuidGeneratorService::getUUIDUnique($instanceModel, $fieldUnique);
    }

    public function generateDNIUnique (): string
    {
        return UserService::generateDNIUnique();
    }

    public function getNumberPhoneSpain (): string
    {
        return UserService::getNumberPhoneSpain();
    }

    /**
     * @throws \Exception
     */
    public function generateSecureRandomPasswordUser (): string {
        return UserService::generateSecureRandomPassword();
    }

}
