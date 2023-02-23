<?php

namespace App\Rules\Api\v1\ContactUs;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class RecaptchaValidationRule implements Rule
{
    public function __construct()
    {

    }

    public function passes($attribute, $value): bool
    {
        $uri = config('app.app_url_recaptcha_google');

        $data = [
            'secret' => config('app.app_token_recaptcha_google'),
            'response' => $value
        ];

        $response = Http::asForm()->post($uri, $data)->object();

        return $response?->success && $response?->score >= 0.7;

    }

    public function message(): string
    {
        return 'La solicitud fue rechazada por incumplir las reglas de Recaptcha del sistema.';
    }
}
