<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Tentukan apakah pendaftaran akun publik (/register) diizinkan.
     * - Mode Demo Portofolio: selalu DITUTUP agar pengunjung memakai 1-Klik Akun Demo.
     * - Jika ALLOW_PUBLIC_REGISTRATION diset eksplisit di .env (true/false): ikuti nilainya.
     * - Default (null): di production otomatis tertutup jika sudah ada minimal 1 akun terdaftar.
     */
    public static function isPublicRegistrationAllowed(): bool
    {
        if (config('app.demo_mode', false)) {
            return false;
        }

        $configured = config('app.allow_public_registration');
        if ($configured !== null && $configured !== '') {
            return filter_var($configured, FILTER_VALIDATE_BOOLEAN);
        }

        if (app()->environment('production') && User::exists()) {
            return false;
        }

        return true;
    }

    /**
     * Tampilkan formulir login.
     */
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login', [
            'canRegister' => self::isPublicRegistrationAllowed(),
            'isDemoMode' => (bool) config('app.demo_mode', false),
        ]);
    }

    /**
     * Proses autentikasi login pengguna (dilengkapi proteksi Rate Limiting anti brute-force).
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        $throttleKey = Str::transliterate(Str::lower($request->input('email', '')) . '|' . $request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            return back()->withErrors([
                'email' => "Terlalu banyak percobaan login gagal. Silakan coba lagi dalam {$seconds} detik.",
            ])->onlyInput('email');
        }

        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'))
                ->with('success', 'Selamat datang kembali, ' . Auth::user()->name . '!');
        }

        RateLimiter::hit($throttleKey, 60);

        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan tidak sesuai.',
        ])->onlyInput('email');
    }

    /**
     * Login 1-Klik khusus Mode Demo Portofolio (hanya aktif saat DEMO_MODE=true).
     */
    public function demoLogin(Request $request): RedirectResponse
    {
        if (!config('app.demo_mode', false)) {
            abort(404);
        }

        $demoEmail = config('app.demo_email', 'demo@konterku.com');
        $demoPassword = config('app.demo_password', 'demo123');

        $demoUser = User::firstOrCreate(
            ['email' => $demoEmail],
            [
                'name' => 'Demo Pemilik Konter',
                'phone' => '081234567890',
                'password' => Hash::make($demoPassword),
                'role' => 'admin',
            ]
        );

        Auth::login($demoUser);
        $request->session()->regenerate();

        return redirect()->route('dashboard')
            ->with('info', 'Selamat datang di Mode Demo Portofolio KonterKu! Anda bebas menjelajahi & mencoba seluruh fitur kasir, stok, kasbon, dan laporan.');
    }

    /**
     * Tampilkan formulir registrasi akun baru.
     */
    public function showRegister(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        if (!self::isPublicRegistrationAllowed()) {
            return redirect()->route('login')
                ->with('warning', 'Pendaftaran akun publik dinonaktifkan demi keamanan. Silakan masuk menggunakan akun yang tersedia.');
        }

        return view('auth.register');
    }

    /**
     * Proses pendaftaran akun pengguna baru.
     */
    public function register(RegisterRequest $request): RedirectResponse
    {
        if (!self::isPublicRegistrationAllowed()) {
            return redirect()->route('login')
                ->with('warning', 'Pendaftaran akun publik dinonaktifkan demi keamanan.');
        }

        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'role' => 'admin',
        ]);

        Auth::login($user);

        $request->session()->regenerate();

        return redirect()->route('dashboard')
            ->with('success', 'Registrasi berhasil! Selamat datang di KonterKu, ' . $user->name . '.');
    }

    /**
     * Tampilkan formulir permohonan reset password (lupa kata sandi).
     */
    public function showForgotPassword(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.forgot-password');
    }

    /**
     * Kirimkan tautan reset kata sandi ke email pengguna.
     */
    public function sendResetLink(Request $request): RedirectResponse
    {
        if (config('app.demo_mode', false)) {
            return redirect()->route('login')
                ->with('warning', 'Reset kata sandi dinonaktifkan pada Mode Demo Portofolio. Gunakan tombol Masuk Akun Demo (1 Klik).');
        }

        $request->validate([
            'email' => ['required', 'email'],
        ], [
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
        ]);

        $status = Password::sendResetLink($request->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('status', 'Tautan untuk mengatur ulang kata sandi telah dikirimkan ke email Anda.');
        }

        return back()->withErrors([
            'email' => 'Gagal mengirimkan tautan reset sandi. Pastikan alamat email telah terdaftar.',
        ]);
    }

    /**
     * Tampilkan formulir atur ulang kata sandi dengan token.
     */
    public function showResetPassword(Request $request, string $token): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email', ''),
        ]);
    }

    /**
     * Proses update password baru dari tautan token reset.
     */
    public function resetPassword(Request $request): RedirectResponse
    {
        if (config('app.demo_mode', false)) {
            return redirect()->route('login')
                ->with('warning', 'Reset kata sandi dinonaktifkan pada Mode Demo Portofolio.');
        }

        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', PasswordRule::min(6)],
        ], [
            'token.required' => 'Token reset tidak ditemukan.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'password.min' => 'Kata sandi minimal terdiri dari 6 karakter.',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')
                ->with('success', 'Kata sandi Anda berhasil diperbarui! Silakan masuk dengan kata sandi baru.');
        }

        return back()->withErrors([
            'email' => 'Gagal mengatur ulang kata sandi. Token sudah kedaluwarsa atau tidak valid.',
        ]);
    }

    /**
     * Proses logout pengguna.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('info', 'Anda telah berhasil keluar.');
    }
}
