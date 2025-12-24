<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check jika user sudah login dan memiliki role 'user'
        // Jika tidak, redirect ke admin dashboard
        if (auth()->check() && auth()->user()->role === 'user') {
            return $next($request);
        }

        // Jika user sudah login tapi bukan user biasa (adalah admin), redirect ke admin dashboard
        if (auth()->check()) {
            return redirect()->route('dashboard')->with('error', 'Halaman ini hanya untuk user biasa');
        }

        // Jika belum login, redirect ke login
        return redirect()->route('login');
    }
}
