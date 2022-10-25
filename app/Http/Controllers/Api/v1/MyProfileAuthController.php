<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\User\v1\UserResource;
use App\Models\User;

class MyProfileAuthController extends Controller
{
    public function __invoke()
    {
        $user = User::applyIncludes()->find(auth()->user()->getRouteKey());

        return UserResource::make($user);
    }
}
