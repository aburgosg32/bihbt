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
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'dni' => ['required', 'digits:8'],
            'password' => ['required', 'string'],
        ];
    }

    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $credenciales = [
            'dni' => $this->input('dni'),
            'password' => $this->input('password'),
            'activo' => 1,
        ];

        if (! Auth::attempt($credenciales, $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'dni' => __('El DNI o la contraseña son incorrectos, o el usuario está inactivo.'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'dni' => __('Demasiados intentos. Inténtalo nuevamente en :seconds segundos.', [
                'seconds' => $seconds,
            ]),
        ]);
    }

    public function throttleKey(): string
    {
        return Str::lower((string) $this->input('dni')) . '|' . $this->ip();
    }
}
