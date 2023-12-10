<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class CheckJsonRequest
{
    public function handle(Request $request, Closure $next, /*$params*/)
    {

        // This URL access direct access via the browser.
        // The security check is perform through cookies in the API it self
        if (str_contains($request->getRequestUri(), "api/v1/resource/")) {
            return $next($request);
        }

        if (!$request->expectsJson()) {
            return response()->json(['error' => $request->getRequestUri()], 400);
            // return redirect(config('app.url_frontend'));
        }

        return $next($request);
    }
}
