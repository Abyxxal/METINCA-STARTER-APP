<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsManager
{
    /**
     * Handle an incoming request.
     * Only users with role 'manager' can access.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->role === 'manager') {
            return $next($request);
        }

        if (auth()->check()) {
            return redirect()->route('dashboard')->with('error', 'Hanya Manager yang dapat mengakses halaman ini.');
        }

        return redirect()->route('login');
    }
}
