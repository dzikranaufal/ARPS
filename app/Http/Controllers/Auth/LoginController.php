<?php

namespace App\Http\Controllers\Auth;

use App\Enums\AccountStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /** Batas percobaan login per menit (risk A7). */
    private const MAX_ATTEMPTS = 5;

    /** Decay time dalam detik sebelum hit kedaluwarsa. */
    private const DECAY_SECONDS = 60;

    /**
     * Show the login form.
     */
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    /**
     * Authenticate the user (rate limited — risk A7).
     */
    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $key = 'login:' . strtolower($request->string('email')) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, self::MAX_ATTEMPTS)) {
            throw ValidationException::withMessages([
                'email' => 'Terlalu banyak percobaan login. Coba lagi dalam ' . RateLimiter::availableIn($key) . ' detik.',
            ])->status(429);
        }

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::hit($key, self::DECAY_SECONDS);

            throw ValidationException::withMessages([
                'email' => 'Kredensial tidak cocok dengan catatan kami.',
            ]);
        }

        RateLimiter::clear($key);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Nonaktif account cannot log in (risk: soft-deactivate).
        if ($user->status === AccountStatus::Nonaktif) {
            Auth::logout();

            // NOTE: tidak invalidate session agar pesan error & _previous.url tetap
            // tersimpan dan user diarahkan kembali ke /login dengan pesan dinonaktifkan.
            throw ValidationException::withMessages([
                'email' => 'Akun Anda sedang dinonaktifkan. Hubungi administrator.',
            ]);
        }

        $request->session()->regenerate();

        // Redirect based on role (PRD §2).
        if (in_array($user->role, [UserRole::SuperAdmin, UserRole::AdminManager], true)) {
            return redirect()->intended(route('admin.dashboard'));
        }

        return redirect()->intended(route('member.dashboard'));
    }

    /**
     * Log the user out (POST only — risk A5).
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
