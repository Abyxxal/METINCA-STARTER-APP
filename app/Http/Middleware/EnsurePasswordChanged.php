<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Memaksa karyawan (role 'user') mengganti password bawaan admin
 * sebelum dapat menggunakan aplikasi.
 *
 * Penanda: kolom users.password_changed_at masih NULL.
 * Admin/manager tidak dipaksa. Allowlist: halaman & proses ganti password, logout.
 */
class EnsurePasswordChanged
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->role === 'user' && is_null($user->password_changed_at)) {
            $allowed = ['user.password.change', 'user.password.update', 'logout'];

            if (! $request->routeIs($allowed)) {
                return redirect()->route('user.password.change');
            }
        }

        return $next($request);
    }
}
