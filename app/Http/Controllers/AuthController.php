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
        // UPDATE: Arahkan semua user ke Dashboard Utama
        // Halaman ini (DashboardPage) sudah memiliki logika adaptif untuk 
        // menampilkan statistik dan menu yang berbeda sesuai role masing-masing.
        return redirect()->route('dashboard');
    }
}