<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function loginForm()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole();
        }
        return view('auth.login');
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return $this->redirectBasedOnRole();
        }

        return back()->withErrors([
            'username' => 'Username atau password salah.',
        ])->onlyInput('username');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    /**
     * Helper untuk mengarahkan user sesuai role-nya
     */
    protected function redirectBasedOnRole()
    {
        $role = Auth::user()->role; // Mengambil dari kolom native 'role'

        // 1. SUPERADMIN -> Ke Manajemen User/System
        if ($role == 'superadmin') {
            return redirect()->route('admin.users');
        }

        // 2. ADMIN KEUANGAN -> Ke Verifikasi
        if ($role == 'keuangan' || $role == 'admin') {
            return redirect()->route('admin.keuangan');
        }

        // 3. ADMIN BAAK -> Ke Jadwal/Akademik
        if ($role == 'baak') {
            return redirect()->route('admin.jadwal');
        }

        // 4. DOSEN -> Ke Jadwal Mengajar
        if ($role == 'dosen') {
            return redirect()->route('dosen.jadwal');
        }

        // 5. MAHASISWA -> Ke Dashboard/KRS
        if ($role == 'mahasiswa') {
            return redirect()->route('mhs.krs');
        }

        // Fallback default
        return redirect('/dashboard');
    }
}