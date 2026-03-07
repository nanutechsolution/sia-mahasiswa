<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * AUTH CONTROLLER - SISI SIAKAD
 * Gunakan kode ini di proyek SIAKAD Anda untuk melayani sistem SIMA.
 */
class AuthController extends Controller
{
    
    /**
     * Endpoint untuk Login SSO dari SIMA
     */
    public function login(Request $request)
    {
        // 1. TAMBAHKAN VALIDASI CLIENT (KEAMANAN TINGGI)
        // Pastikan hanya SIMA yang bisa mengakses endpoint ini.
        if ($request->header('X-SIMA-KEY') !== config('app.sima_api_key')) {
            return response()->json(['message' => 'Unauthorized Client.'], 403);
        }

        $request->validate([
            'username' => 'required|string', // NIM atau NIDN
            'password' => 'required',
        ]);

        $user = User::where('username', $request->username)->first();

        // 2. Verifikasi User dan Password
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'NIM/NIDN atau password salah.'
            ], 401);
        }

        // 3. Cek Status Akun Aktif
        if (!$user->is_active) {
            return response()->json(['message' => 'Akun tidak aktif.'], 403);
        }

        // 4. Buat Token dengan SCOPE khusus (Hanya untuk Survei)
        // Jika token ini bocor, hacker tidak bisa pakai untuk merubah KRS/Nilai
        $token = $user->createToken('survey-session', ['survey:fill'])->plainTextToken;

        return response()->json([
            'message' => 'Autentikasi SIAKAD berhasil',
            'token' => $token,
        ]);
    }

    /**
     * Endpoint Profil (Dipanggil oleh SIMA menggunakan Bearer Token)
     */
    public function me(Request $request)
    {
        $user = $request->user();

        // Pastikan token punya izin untuk mengisi survei
        if (!$user->tokenCan('survey:fill')) {
            return response()->json(['message' => 'Token tidak memiliki izin survei.'], 403);
        }

        return response()->json([
            'data' => [
                'name' => $user->name,
                'role' => strtolower($user->role), // mahasiswa / dosen
                'identifier' => $user->username,   // NIM / NIDN
                'department' => $user->prodi->name ?? 'Umum',
                'status' => 'aktif',
            ]
        ]);
    }
}
