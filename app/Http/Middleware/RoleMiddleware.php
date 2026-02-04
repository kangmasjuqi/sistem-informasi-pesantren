<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            return redirect()->route('login')
                ->with('error', 'Silakan login terlebih dahulu');
        }

        // Check if user is active
        if (!auth()->user()->isActive()) {
            auth()->logout();
            return redirect()->route('login')
                ->with('error', 'Akun Anda tidak aktif. Hubungi administrator.');
        }

        // If no specific roles required, allow authenticated active users
        if (empty($roles)) {
            return $next($request);
        }

        // Check if user has required role
        if (auth()->user()->hasAnyRole($roles)) {
            return $next($request);
        }

        // User doesn't have required role
        abort(403, 'Anda tidak memiliki akses ke halaman ini');
    }
}