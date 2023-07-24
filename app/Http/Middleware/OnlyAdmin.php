<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

/**
 * Allow only the ROLE admin
 */
class OnlyAdmin
{
    public function handle(Request $request, Closure $next, /*$params*/)
    {

        if (Auth::check() && Auth::user()->hasRole('admin')) {
            return $next($request);
        }

        return response()->json(['error' => 'Not enough permissions'], 403);
    }
}