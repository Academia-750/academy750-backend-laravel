<?php

namespace App\Core\Resources\Profile\v1;

use App\Core\Resources\Profile\v1\Interfaces\ProfileInterface;
use App\Models\User;
use Illuminate\Support\Facades\DB;

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
                $user->first_name = $request->get('first-name');
                $user->last_name = $request->get('last-name');
                $user->phone = $request->get('phone');
                $user->email = $request->get('email');
                $user->save();

            DB::commit();

            return $this->model->applyIncludes()->find($user->getRouteKey());
        } catch (\Exception $e) {
            DB::rollback();
            abort(500,$e->getMessage());
        }
    }
}
