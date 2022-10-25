<?php

namespace App\Http\Controllers\JsonApiAuth;

use App\Http\Controllers\JsonApiAuth\Actions\AuthKit;
use App\Http\Controllers\JsonApiAuth\Revokers\SanctumRevoker;
use App\Http\Controllers\JsonApiAuth\Traits\HasToShowApiTokens;
use App\Http\Requests\JsonApiAuth\LoginRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use JetBrains\PhpStorm\ArrayShape;

class LoginController
{
    use HasToShowApiTokens;

    /**
     * @param LoginRequest $request
     *
     * @return JsonResponse
     */
    public function __invoke(LoginRequest $request): JsonResponse
    {
        try {

            /*Auth::attempt($request->only(['access_key', 'password']))*/
            $attemptAuth = $this->attemptAuthentication(
                $request->get(config('json-api-auth.access_key', 'access_key')),
                $request->get('password')
            );
            if($attemptAuth['attempt']) {

                $this->removeAllTokenSanctumOfCurrentUserAuth($attemptAuth['user']);

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

    /**
     * @param $access_key
     * @param $password
     *
     * @return array
     */
    #[ArrayShape(['attempt' => "bool", 'user' => "mixed"])] public function attemptAuthentication ($access_key, $password): array
    {
        $user = User::query()->orWhere('dni','=', $access_key)
            /*->orWhere('email', '=', $access_key)
            ->orWhere('username', '=', $access_key)*/
            ->first();

        return [
            'attempt' => ($user && Hash::check($password, $user->password)),
            'user' => $user
        ];
    }

    /**
     * @param $user
     * @param int $code
     * @param bool $showToken
     *
     * @return JsonResponse
     */
    public function showCredentials($user, int $code = 200, bool $showToken = true): JsonResponse
    {
        return response()->json([
            'user_id' => $user->getRouteKey(),
            'token' => $this->createToken($user),
            'type' => 'Bearer'
        ]);
    }

    private function removeAllTokenSanctumOfCurrentUserAuth ($authUser): void {
        $instanceRevokerTokensSanctum = new SanctumRevoker($authUser);
        $instanceRevokerTokensSanctum->deleteAllTokens();
    }
}
