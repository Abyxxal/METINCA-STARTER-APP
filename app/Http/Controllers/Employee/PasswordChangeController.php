<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

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
            'password_lama' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if (! Hash::check($validated['password_lama'], $request->user()->password)) {
            return back()->withErrors(['password_lama' => 'Password lama tidak sesuai.']);
        }

        $request->user()->update([
            'password' => Hash::make($validated['password']),
            'password_changed_at' => now(),
        ]);

        // Revoke other sessions
        $request->user()->sessions()->where('id', '!=', Session::getId())->delete();
        auth()->logoutOtherDevices($validated['password']);

        return redirect()
            ->route('dashboard')
            ->with('success', 'Password berhasil diganti. Selamat bekerja!');
    }

    /**
     * Ubah password dari halaman profil (wajib verifikasi password lama).
     */
    public function updateFromProfile(Request $request)
    {
        $validated = $request->validate([
            'password_lama' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if (! Hash::check($validated['password_lama'], $request->user()->password)) {
            return back()->withErrors(['password_lama' => 'Password lama tidak sesuai.']);
        }

        $request->user()->update([
            'password' => Hash::make($validated['password']),
            'password_changed_at' => now(),
        ]);

        // Revoke other sessions
        $request->user()->sessions()->where('id', '!=', Session::getId())->delete();
        auth()->logoutOtherDevices($validated['password']);

        return back()->with('success', 'Password berhasil diganti.');
    }
}
