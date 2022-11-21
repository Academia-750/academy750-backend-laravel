<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class OnlyUsersWithAccountEnable
{
    public function handle(Request $request, Closure $next, /*$params*/)
    {

        if(Auth::user()?->state !== 'enable'){
            abort(401);

        }

        return $next($request);
    }
}
