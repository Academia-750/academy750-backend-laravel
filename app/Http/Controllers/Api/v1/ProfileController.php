<?php

namespace App\Http\Controllers\Api\v1;

use App\Core\Resources\Profile\v1\Interfaces\ProfileInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\Profile\ChangeMyPasswordRequest;
use App\Http\Requests\Api\v1\Profile\UpdateDataProfileRequest;
use App\Http\Requests\JsonApiAuth\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;


/**
 * @group My profile
 *
 * APIs for managing users profile
 */
class ProfileController extends Controller
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

    public function updateDataMyProfile(UpdateDataProfileRequest $request)
    {
        return $this->profileInterface->updateDataMyProfile($request);
    }

    public function unsubscribeFromSystem()
    {
        return $this->profileInterface->unsubscribeFromSystem();
    }

    public function changePasswordAuth(ChangeMyPasswordRequest $request)
    {
        return $this->profileInterface->changePasswordAuth($request);
    }

    public function checkPreviousSessionAccess(LoginRequest $request): \Illuminate\Http\JsonResponse
    {

        $accessKey = config('json-api-auth.access_key', 'access_key');
        $invalidCredentials = true;
        $thereIsAlreadyAPreviousSession = false;

        $user = User::query()
            ->where('dni', '=', $request->get($accessKey))
            ->where('state', '=', 'enable')
            ->first();
        $password = $request->get('password');

        if ($user && Hash::check($password, $user->password)) {
            $invalidCredentials = false;
            $sessionPrevious = DB::table('personal_access_tokens')
                ->where('tokenable_id', '=', $user->getKey())
                ->get();

            if ($sessionPrevious->count() > 0) {
                $thereIsAlreadyAPreviousSession = true;
            }
        }

        return response()->json(compact('invalidCredentials', 'thereIsAlreadyAPreviousSession'));
    }

    public function getNotificationsUser()
    {
        return $this->profileInterface->getNotificationsUser();
    }

    public function read_notification_user($notification_id)
    {
        return $this->profileInterface->read_notification_user($notification_id);
    }
}