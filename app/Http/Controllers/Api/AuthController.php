<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Endpoint untuk Login SSO dari Aplikasi Lain (SI ASET)
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required',
        ]);

        $user = User::where('username', $request->username)->first();

        // Verifikasi User dan Password
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Username atau password salah.'
            ], 401);
        }

        // Cek apakah akun aktif
        if (!$user->is_active) {
            return response()->json([
                'message' => 'Akun Anda dinonaktifkan.'
            ], 403);
        }

        // Hapus token lama jika ingin membatasi 1 device, atau biarkan jika multi-device
        $user->tokens()->delete(); 

        // Buat Token Sanctum baru
        $token = $user->createToken('sso-si-aset')->plainTextToken;

        return response()->json([
            'message' => 'Autentikasi berhasil',
            'token' => $token,
        ]);
    }

    /**
     * Endpoint untuk mengecek profil user berdasar token (Dipanggil oleh SI ASET)
     */
    public function me(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'data' => [
                'name' => $user->name,
                'role' => strtolower($user->role), // Pastikan huruf kecil (mahasiswa/dosen)
                'identifier' => $user->username,   // Jadikan username sebagai NIM/NIDN
                'status' => $user->is_active ? 'aktif' : 'nonaktif',
            ]
        ]);
    }

    /**
     * Endpoint untuk Logout
     */
    public function logout(Request $request)
    {
        // Hapus token yang sedang digunakan
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Berhasil logout'
        ]);
    }
}
