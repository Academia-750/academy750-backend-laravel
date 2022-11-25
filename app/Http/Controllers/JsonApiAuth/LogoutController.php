<?php

namespace App\Http\Controllers\JsonApiAuth;

use App\Events\Api\ActionUserLogoutEvent;
use App\Http\Controllers\JsonApiAuth\Revokers\RevokerFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class LogoutController
{
    public function __invoke(Request $request): Response
    {
        /*broadcast(
            new ActionUserLogoutEvent(Auth::user())
        )*/
        //ActionUserLogoutEvent::dispatch(Auth::user());
        //broadcast(new ActionUserLogoutEvent(Auth::user()));

        (new RevokerFactory)->make()->{$this->applyRevokeStrategy()}();

        return response([
            'message' => '¡Has cerrado sesión con éxito!',
        ], 200);
    }

    /** It guess what method is going to use on logout based on the package config file. */
    public function applyRevokeStrategy(): string
    {
        $methods = [
            'revoke_only_current_token',
            'revoke_all_tokens',
            'delete_current_token',
            'delete_all_tokens',
        ];

        foreach ($methods as $method) {
            if(config('json-api-auth.' . $method)) {
                return (string) Str::of($method)->camel();
            }
        }

        return (string) Str::of($methods[3])->camel();
    }
}
