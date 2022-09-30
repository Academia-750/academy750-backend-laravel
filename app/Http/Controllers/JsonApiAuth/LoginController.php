<?php

namespace App\Http\Controllers\JsonApiAuth;

use App\Http\Controllers\JsonApiAuth\Actions\AuthKit;
use App\Http\Controllers\JsonApiAuth\Traits\HasToShowApiTokens;
use App\Http\Requests\JsonApiAuth\LoginRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController
{
    use HasToShowApiTokens;

    public function __invoke(LoginRequest $request): JsonResponse
    {
        try {

            /*Auth::attempt($request->only(['access_key', 'password']))*/
            $attemptAuth = $this->attemptAuthentication(
                $request->get(config('json-api-auth.access_key', 'access_key')),
                $request->get('password')
            );
            if($attemptAuth['attempt']) {

                $attemptAuth['user']->last_session = now();
                $attemptAuth['user']->save();

                return $this->showCredentials(
                    $attemptAuth['user']
                );
            }

        } catch (Exception $exception) {
            abort(500, $exception->getMessage());
        }

        $error = \Illuminate\Validation\ValidationException::withMessages([
            config('json-api-auth.access_key', 'access_key') => ['Estas credenciales no coinciden con nuestros registros']
        ]);

        throw $error;

    }

    public function attemptAuthentication ($access_key, $password) {
        $user = User::query()->where('identification_number', $access_key)
            /*->orWhere('email', $access_key)*/
            ->first();

        return [
            'attempt' => ($user && Hash::check($password, $user->password)),
            'user' => $user
        ];
    }

    public function showCredentials($user, int $code = 200, bool $showToken = true){
        return response()->json([
            'user_id' => $user->getRouteKey(),
            'token' => $this->createToken($user)
        ]);
    }

    protected function createToken(User $user)
    {
        $token = $user->createToken(
            config('json-api-auth.token_id') ?? 'App',
            // Here you can customize the scopes for a new user
            config('json-api-auth.scopes') ?? []
        );

        if(AuthKit::isSanctum()) {
            return $token->plainTextToken;
        }

        return $token->accessToken;
    }
}
