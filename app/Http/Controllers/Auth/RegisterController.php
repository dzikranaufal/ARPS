<?php

namespace App\Http\Controllers\Auth;

use App\Enums\AccountStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisterController extends Controller
{
    /**
     * Show the registration form.
     */
    public function showRegistrationForm(): View
    {
        return view('register.index');
    }

    /**
     * Create a new member account and log the user in.
     *
     * - role & status are hardcoded (risk A2 — never taken from input).
     * - duplicate email handled via DB unique constraint + friendly message (risk C3).
     */
    public function store(Request $request): RedirectResponse
    {
        $key = 'register:' . $request->ip();

        // Rate limit registrasi (risk A7) — registrasi langsung aktif tanpa verifikasi email.
        if (RateLimiter::tooManyAttempts($key, 5)) {
            throw ValidationException::withMessages([
                'nama' => 'Terlalu banyak percobaan pendaftaran. Coba lagi dalam ' . RateLimiter::availableIn($key) . ' detik.',
            ])->status(429);
        }

        // Catat percobaan (decay) — limiter di-clear jika registrasi akhirnya sukses.
        RateLimiter::hit($key, 60);

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'telepon' => ['required', 'string', 'max:20'],
            'organisasi' => ['nullable', 'string', 'max:255'],
            'password' => ['required', 'string', 'confirmed', 'min:8'],
        ]);

        try {
            $user = User::create([
                'nama' => $validated['nama'],
                'email' => $validated['email'],
                'telepon' => $validated['telepon'],
                'organisasi' => $validated['organisasi'] ?? null,
                'password' => $validated['password'],
                // Hardcoded — never taken from user input (risk A2).
                'role' => UserRole::Member,
                'status' => AccountStatus::Aktif,
            ]);
        } catch (QueryException $e) {
            // Unique constraint on email is the last line of defense (risk C3).
            return back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->withErrors(['email' => 'Email sudah terdaftar. Gunakan email lain.']);
        }

        // Registrasi yang sah tidak perlu dihukum oleh limiter.
        RateLimiter::clear($key);

        Auth::login($user);

        return redirect()->route('member.dashboard');
    }
}
