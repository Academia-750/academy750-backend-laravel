<?php

namespace App\Http\Controllers\Api\v1;

use App\Core\Resources\Profile\v1\Interfaces\ProfileInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\JsonApiAuth\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller implements ProfileInterface
{
    protected ProfileInterface $profileInterface;

    public function __construct(ProfileInterface $profileInterface)
    {
        $this->profileInterface = $profileInterface;
    }

    public function getDataMyProfile()
    {
        return $this->profileInterface->getDataMyProfile();
    }

    public function checkPreviousSessionAccess (LoginRequest $request): \Illuminate\Http\JsonResponse
    {

        $accessKey = config('json-api-auth.access_key', 'access_key');
        $invalidCredentials = true;
        $thereIsAlreadyAPreviousSession = false;

        $user = User::query()->where('dni', '=', $request->get($accessKey))->first();
        $password = $request->get('password');

        if ($user && Hash::check($password, $user->password)) {
            $invalidCredentials = false;
            $sessionPrevious = DB::table('personal_access_tokens')
                ->where('tokenable_id', '=', $user->id)
                ->get();

            if ($sessionPrevious->count() > 0) {
                $thereIsAlreadyAPreviousSession = true;
            }
        }

        return response()->json(compact('invalidCredentials', 'thereIsAlreadyAPreviousSession'));
    }
}
