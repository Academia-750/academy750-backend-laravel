<?php

namespace App\Core\Services;

use App\Models\User;
use Faker\Provider\es_ES\Person;
use Hackzilla\PasswordGenerator\Generator\ComputerPasswordGenerator;

class UserService
{
    use UserServiceTrait;

    public static function existsDNIInTableUser ($dni): bool {
        $existsDNI = User::query()->where("dni","=", $dni)
            ->first();

        return $existsDNI !== null;
    }

    public static function generateNewDNI (): string
    {
        return Person::dni();
    }

    public static function generateDNIUnique (): string
    {
        $DNIGenerated = self::generateNewDNI();

        while (self::existsDNIInTableUser($DNIGenerated)) {
            $DNIGenerated = self::generateNewDNI();
        }

        return $DNIGenerated;
    }

    public static function getNumberPhoneSpain (): string
    {
        return self::generateNumberPhoneSpain();
    }

    public static function generateNumberPhoneSpain (): string
    {
        $numberPhone = (string) random_int(6,9);
        for ($i = 0; $i < 8; $i++) {
            $numberPhone.= random_int(1,9);
        }

        return $numberPhone;
    }

    /**
     * @throws \Exception
     */
    public static function generateSecureRandomPassword (): string
    {
        $generator = new ComputerPasswordGenerator();

        $generator
            ->setOptionValue(ComputerPasswordGenerator::OPTION_UPPER_CASE, true)
            ->setOptionValue(ComputerPasswordGenerator::OPTION_LOWER_CASE, true)
            ->setOptionValue(ComputerPasswordGenerator::OPTION_NUMBERS, true)
            ->setOptionValue(ComputerPasswordGenerator::OPTION_SYMBOLS, true)
            ->setLength(random_int(8,15));

        return $generator->generatePassword();
    }
}
