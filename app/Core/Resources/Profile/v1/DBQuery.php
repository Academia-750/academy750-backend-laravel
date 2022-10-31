<?php

namespace App\Core\Resources\Profile\v1;

use App\Core\Resources\Profile\v1\Interfaces\ProfileInterface;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DBQuery implements ProfileInterface
{
    protected User $model;

    public function __construct(User $user ){
        $this->model = $user;
    }

    public function getDataMyProfile()
    {
        return $this->model->applyIncludes()->find(auth()->user()->getRouteKey());
    }
    public function updateDataMyProfile($request)
    {
        try {

            DB::beginTransaction();
                $user = $this->model->find(auth()->user()->getRouteKey());

                //$user->dni = $request->get('dni');
                $user->first_name = $request->get('first-name') ?? $user->first_name;
                $user->first_name = $request->get('first-name') ?? $user->first_name;
                $user->last_name = $request->get('last-name') ?? $user->last_name;
                $user->phone = $request->get('phone') ?? $user->phone;
                $user->email = $request->get('email') ?? $user->email;
                $user->save();

            DB::commit();

            return $this->model->applyIncludes()->find($user->getRouteKey());
        } catch (\Exception $e) {
            DB::rollback();
            abort(500,$e->getMessage());
        }
    }

    public function unsubscribeFromSystem()
    {
        try {

            DB::beginTransaction();

                $user = Auth::user();

                $user->delete();

            DB::commit();

            return "Se ha dado de baja del sistema con éxito";
        } catch (\Exception $e) {
            DB::rollback();
            abort(500,$e->getMessage());
        }
    }

    public function changePasswordAuth($request)
    {
        try {

            DB::beginTransaction();

            $user = Auth::user();
            $user->password = Hash::make($request->get('password'));
            $user->save();

            DB::commit();

            return "La contraseña ha sido actualizada con éxito";
        } catch (\Exception $e) {
            DB::rollback();
            abort(500,$e->getMessage());
        }
    }
}
