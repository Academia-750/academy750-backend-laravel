<?php

namespace App\Core\Services;

use App\Models\User;
use Faker\Provider\es_ES\Person;
use Illuminate\Support\Str;

trait UserServiceTrait
{
    public function getUUIDUnique ($instanceModel, $fieldUnique = 'id'): \Ramsey\Uuid\UuidInterface
    {
        return UuidGeneratorService::getUUIDUnique($instanceModel, $fieldUnique);
    }

    public function getNumberPhoneSpain (): string
    {
        return $this->generateNumberPhoneSpain();
    }

    public function generateNumberPhoneSpain (): string
    {
        $numberPhone = (string) random_int(6,9);
        for ($i = 0; $i < 8; $i++) {
            $numberPhone.= random_int(1,9);
        }

        return $numberPhone;
    }

    public function existsDNIInTableUser ($dni): bool {
        $existsDNI = User::query()->where("dni","=", $dni)
            ->first();

        return $existsDNI !== null;
    }

    public function generateNewDNI () {
        return Person::dni();
    }

    public function generateDNIUnique () {
        $DNIGenerated = $this->generateNewDNI();

        while ($this->existsDNIInTableUser($DNIGenerated)) {
            $DNIGenerated = $this->generateNewDNI();
        }

        return $DNIGenerated;
    }
}
