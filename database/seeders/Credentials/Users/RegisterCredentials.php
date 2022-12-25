<?php

namespace Database\Seeders\Credentials\Users;

class RegisterCredentials
{
    public static function registerCredentials (): void {
        self::registerAcademia();
        self::registerRaul();
        self::registerAdolfo();
        self::registerCarlos();
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
}
