<?php

namespace App\Core\Resources\Profile\v1;

use App\Core\Resources\Profile\v1\Interfaces\ProfileInterface;
use App\Models\User;

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
}
