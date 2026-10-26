<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */

    public function handle(Request $request, Closure $next, $permission)
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (!$permission) {
            abort(403, 'No permission specified.');
        }

        $permissions = explode('|', $permission);

        foreach ($permissions as $perm) {
            if ($user && $user->hasPermission($perm)) {
                return $next($request);
            }
        }

        abort(403, 'Unauthorized access');
    }
}
