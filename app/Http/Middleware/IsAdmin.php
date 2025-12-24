<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check jika user sudah login dan memiliki role 'admin'
        // Jika tidak, redirect ke user dashboard
        if (auth()->check() && auth()->user()->role === 'admin') {
            return $next($request);
        }

        // Jika user sudah login tapi bukan admin, redirect ke user dashboard
        if (auth()->check()) {
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses ke halaman ini');
        }

        // Jika belum login, redirect ke login
        return redirect()->route('login');
    }
}
