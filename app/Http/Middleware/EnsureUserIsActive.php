<?php

namespace App\Http\Middleware;

use Closure;

class EnsureUserIsActive
{
    public function handle($request, Closure $next)
    {
        $user = $request->user();

        if (!$user || !$user->activo) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'email' => 'Tu usuario está inactivo.',
            ]);
        }

        return $next($request);
    }
}
