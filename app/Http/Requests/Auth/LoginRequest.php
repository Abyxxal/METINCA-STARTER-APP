<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email_or_nik' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'email_or_nik.required' => 'Email atau NIK harus diisi',
            'password.required' => 'Password harus diisi',
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     * Support login dengan Email ATAU NIK
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $emailOrNik = $this->input('email_or_nik');
        $password = $this->input('password');

        // ===== TRY LOGIN DENGAN EMAIL DULU (untuk admin & user) =====
        if (Auth::attempt(['email' => $emailOrNik, 'password' => $password], $this->boolean('remember'))) {
            RateLimiter::clear($this->throttleKey());
            return;
        }

        // ===== JIKA GAGAL, TRY LOGIN DENGAN NIK (untuk user) =====
        // Cek apakah input adalah NIK (bukan email format)
        if (!filter_var($emailOrNik, FILTER_VALIDATE_EMAIL)) {
            if (Auth::attempt(['nik' => $emailOrNik, 'password' => $password], $this->boolean('remember'))) {
                RateLimiter::clear($this->throttleKey());
                return;
            }
        }

        // ===== KEDUA-DUANYA GAGAL =====
        RateLimiter::hit($this->throttleKey());

        throw ValidationException::withMessages([
            'email_or_nik' => 'Email/NIK atau password salah',
        ]);
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->input('email_or_nik')).'|'.$this->ip());
    }
}
