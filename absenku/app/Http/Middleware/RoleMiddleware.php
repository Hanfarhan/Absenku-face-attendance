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
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Cek apakah user sudah login
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // Cek apakah role user sesuai
        if (auth()->user()->role !== $role) {

            // Jika admin mencoba akses route user → arahkan ke admin panel
            if (auth()->user()->isAdmin()) {
                return redirect('/admin');
            }

            // Jika user biasa mencoba akses route admin → arahkan ke dashboard
            return redirect()->route('dashboard');
        }

        return $next($request);
    }
}