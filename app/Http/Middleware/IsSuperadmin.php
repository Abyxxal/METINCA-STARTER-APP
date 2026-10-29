<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsSuperadmin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->role === 'superadmin') {
            return $next($request);
        }

        if (auth()->check()) {
            return redirect()->route('dashboard')->with('error', 'Akses ditolak. Hanya Superadmin.');
        }

        return redirect()->route('login');
    }
}
