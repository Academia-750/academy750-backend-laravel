<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\User\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MyProfileAuthController extends Controller
{
    public function __invoke()
    {
        $user = User::applyIncludes()->find(auth()->user()->getRouteKey());

        return UserResource::make($user);
    }
}
