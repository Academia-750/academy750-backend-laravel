<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class CheckJsonRequest
{
    public function handle(Request $request, Closure $next, /*$params*/)
    {

        if (!$request->expectsJson()) {
            //return response()->json(['error' => 'Solo se permiten solicitudes JSON.'], 400);
            return redirect(config('app.url_frontend'));
        }

        return $next($request);
    }
}
