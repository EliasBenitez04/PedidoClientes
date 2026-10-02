<?php

namespace App\Http\Middleware;

use Closure;

class RoleMiddleware
{
    public function handle($request, Closure $next, ...$roles)
    {
        $user = $request->user();
        abort_unless($user && $user->activo, 403, 'Usuario inactivo.');
        abort_unless(in_array($user->rol, $roles, true), 403, 'No tenés permiso para acceder a esta sección.');
        return $next($request);
    }
}
