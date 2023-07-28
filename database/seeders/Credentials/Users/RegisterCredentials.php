<?php

namespace Database\Seeders\Credentials\Users;

use Database\Seeders\Credentials\Users\Interface\GinesCredentials;

class RegisterCredentials
{
    public static function registerCredentials (): void {
        self::registerAcademia();
        self::registerRaul();
        self::registerAdolfo();
        self::registerCarlos();
        self::registerGines();
    }

    private static function registerAcademia (): void {
        AcademiaCredentials::academiaAccount();
        AcademiaCredentials::academiaImpugnaciones();
    }

    private static function registerRaul (): void {
        RaulCredentials::AdminCredentials();
        RaulCredentials::StudentCredentials();
    }

    private static function registerAdolfo (): void {
        AdolfoCredentials::AdminCredentials();
        AdolfoCredentials::StudentCredentials();
    }

    private static function registerCarlos (): void {
        CarlosCredentials::AdminCredentials();
        CarlosCredentials::StudentCredentials();
    }
    private static function registerGines (): void {
        GinesCredentials::AdminCredentials();
        GinesCredentials::StudentCredentials();
    }
}
