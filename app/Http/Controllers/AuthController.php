<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Menampilkan formulir login.
     */
    public function loginForm()
    {
        // Jika sudah login, langsung arahkan ke dashboard
        if (Auth::check()) {
            return $this->redirectBasedOnRole();
        }
        return view('auth.login');
    }

    /**
     * Proses Autentikasi dengan Proteksi Berlapis.
     */
    public function authenticate(Request $request)
    {
        // 1. Validasi Honeypot (Anti-Bot)
        // Mencegah bot yang otomatis mengisi seluruh field input.
        if ($request->filled('website')) {
            return abort(403, 'Akses ditolak: Bot terdeteksi.');
        }

        // 2. Validasi Input Dasar
        $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'username.required' => 'Username atau NIM wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $credentials = $request->only('username', 'password');

        // 3. Proteksi Brute Force (Rate Limiting)
        // Membatasi percobaan login maksimal 5 kali per 5 menit berdasarkan Username & IP.
        $throttleKey = Str::transliterate(Str::lower($credentials['username']) . '|' . $request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            $this->safeLog('security', 'brute-force', "Lockout: {$credentials['username']} dari IP {$request->ip()}");

            throw ValidationException::withMessages([
                'username' => "Keamanan: Terlalu banyak percobaan masuk. Silakan coba lagi dalam $seconds detik.",
            ]);
        }

        // 4. Eksekusi Percobaan Login
        if (Auth::attempt($credentials, $request->boolean('remember'))) {

            // 5. Validasi Status Akun Aktif
            if (!Auth::user()->is_active) {
                Auth::logout();
                return back()->withErrors([
                    'username' => 'Akses ditangguhkan: Akun Anda telah dinonaktifkan oleh administrator.',
                ]);
            }

            // Sukses: Bersihkan limiter & Regenerasi Sesi (Cegah Session Fixation)
            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();

            $this->safeLog('auth', 'login-success', 'User berhasil masuk.');

            return $this->redirectBasedOnRole();
        }

        // Gagal: Tambah hit pada limiter untuk mekanisme lockout
        RateLimiter::hit($throttleKey, 300); // Lockout 5 menit setelah 5x gagal

        $this->safeLog('security', 'failed-login', "Gagal login: Username '{$credentials['username']}'");

        return back()->withErrors([
            'username' => 'Kombinasi ID atau Kata Sandi yang Anda masukkan tidak valid.',
        ])->onlyInput('username');
    }

    /**
     * Proses Keluar dari Sistem.
     */
    public function logout(Request $request)
    {
        $this->safeLog('auth', 'logout', 'User keluar.');

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    /**
     * Helper untuk mencatat aktivitas secara aman (Anti-Crash jika tabel log belum ada).
     */
    private function safeLog($logName, $event, $message)
    {
        try {
            if (function_exists('activity')) {
                activity($logName)
                    ->event($event)
                    ->log($message);
            }
        } catch (\Exception $e) {
            // Abaikan jika tabel log belum dimigrasi
        }
    }

    /**
     * Pengalihan rute pasca-login.
     */
    protected function redirectBasedOnRole()
    {
        return redirect()->route('dashboard');
    }
}
