<?php

namespace App\Core\Resources\Profile\v1;

use App\Core\Resources\Profile\v1\Interfaces\ProfileInterface;
use App\Core\Resources\Users\v1\Services\ActionsAccountUser;
use App\Models\User;
use App\Notifications\Api\StudentHasBeenRemovedFromTheSystemNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DBQuery implements ProfileInterface
{
    protected User $model;

    public function __construct(User $user)
    {
        $this->model = $user;
    }

    public function getDataMyProfile()
    {
        return $this->model->applyIncludes()->findOrFail(auth()->user()->getKey());
    }
    public function updateDataMyProfile($request)
    {
        try {

            DB::beginTransaction();
            $user = $this->model->findOrFail(auth()->user()->getKey());

            //$user->dni = $request->get('dni');
            $user->first_name = $request->get('first-name') ?? $user->first_name;
            $user->last_name = $request->get('last-name') ?? $user->last_name;
            $user->phone = $request->get('phone') ?? $user->phone;
            $user->email = $request->get('email') ?? $user->email;
            $user->save();

            DB::commit();

            return $this->model->applyIncludes()->findOrFail($user->getKey());
        } catch (\Exception $e) {
            DB::rollback();
            abort(500, $e->getMessage());
        }
    }

    public function unsubscribeFromSystem()
    {
        try {

            DB::beginTransaction();
            $user = User::query()->where('id', Auth::user()->id)->first();

            ActionsAccountUser::disableAccountUser($user);

            $userAcademy750 = User::query()->firstWhere('email', '=', config('mail.from.address'));

            if (!$userAcademy750) {
                abort(500, 'No se ha podido encontrar el correo oficial de la Academia 750');
            }

            DB::commit();

            $userAcademy750->notify(new StudentHasBeenRemovedFromTheSystemNotification($user));


            return "Se ha dado de baja del sistema con éxito";
        } catch (\Exception $e) {
            DB::rollback();
            abort(500, $e->getMessage());
        }
    }

    public function changePasswordAuth($request)
    {
        try {

            DB::beginTransaction();

            $user = Auth::user();

            if (!$user) {
                abort(404);
            }

            $user->password = Hash::make($request->get('password'));
            $user->save();

            /*DB::table('password_resets')->where('email', $user->email)->delete();
            DB::table('personal_access_tokens')->where('tokenable_id', '=', $user->getRouteKey())->delete();*/

            DB::commit();

            return "La contraseña ha sido actualizada con éxito";
        } catch (\Exception $e) {
            DB::rollback();
            abort(500, $e->getMessage());
        }
    }

    public function getNotificationsUser()
    {
        return Auth::user()?->notifications()->orderBy('created_at', 'asc')->jsonPaginate();
    }

    public function read_notification_user($notification_id)
    {
        $notificationUser = Auth::user()?->notifications()?->findOrFail($notification_id);
        $notificationUser?->markAsRead();

        return Auth::user()?->notifications()->jsonPaginate();
    }
}
