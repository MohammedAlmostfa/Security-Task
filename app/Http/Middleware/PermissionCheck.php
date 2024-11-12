<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PermissionCheck
{
    public function handle(Request $request, Closure $next)
    {
        $permission = $request->route()->getName();
        $user = Auth::user();


        if (!$user || !$user->role || !$user->role->permissions->contains('permission_name', $permission)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return $next($request);
    }
}
