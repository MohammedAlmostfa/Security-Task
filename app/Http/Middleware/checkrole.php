<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class checkrole
{
    public function handle($request, Closure $next, ...$roles)
    {
        // check role and auth
        if (auth()->user() && in_array(auth()->user()->role, $roles)) {
            return $next($request);
        } elseif (!auth()->user()) {
            return response()->json([
                "message" => 'قم بتسجيل الدخول'
            ], 401); // 401 Unauthorized
        } else {
            return response()->json([
                "message" => 'غير مصرح لك بهذه العملية'
            ], 403); // 403 Forbidden
        }
    }
}
