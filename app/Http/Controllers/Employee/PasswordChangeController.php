<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PasswordChangeController extends Controller
{
    /**
     * Form wajib ganti password (password default dari admin).
     */
    public function show()
    {
        return view('user.change-password');
    }

    /**
     * Proses ganti password: simpan hash baru + buka akses penuh.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
            'password_changed_at' => now(),
        ]);

        return redirect()
            ->route('dashboard')
            ->with('success', 'Password berhasil diganti. Selamat bekerja!');
    }
}
