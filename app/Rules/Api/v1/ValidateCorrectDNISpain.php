<?php

namespace App\Rules\Api\v1;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Str;

class ValidateCorrectDNISpain implements Rule
{
    public function __construct()
    {
        //
    }

    public function passes($attribute, $value): bool
    {
        if (preg_match('/^\d{8}[a-zA-Z]$/', $value)) {
            return $this->validateDNI($value);
        }

        return $this->validateNIE($value);
    }

    private function validateDNI($value): bool
    {
        $state = false;
        $letra = substr($value, -1);
        $numeros = substr($value, 0, -1);
        if (is_numeric($numeros) && is_string($letra)) {
            $letraValida = substr("TRWAGMYFPDXBNJZSQVHLCKE", $numeros % 23, 1);
            if ($letraValida == $letra && strlen($letra) == 1 && strlen($numeros) == 8) {
                $state = true;
            }
        }
        return $state;
    }
    //formato NIE
    private function validateNIE($value): bool
    {
        $state = false;
        $reg = "/^[XYZ]\d{7,8}[A-Z]$/";

        if (preg_match($reg, $value)) {
            $letter = substr($value, 0, 1);
            $letterToNumber = $letter;
            switch (strtoupper($letter)) {
                case 'X':
                    $letterToNumber = 0;
                    break;
                case 'Y':
                    $letterToNumber = 1;
                    break;
                case 'Z':
                    $letterToNumber = 2;
                    break;
            }
            $NIE = $letterToNumber . substr($value, 1);
            $state = $this->validateDNI($NIE);
        }
        return $state;
    }

    public function message(): string
    {
        return 'Este no es un DNI válido para Ciudadano de España';
    }
}
