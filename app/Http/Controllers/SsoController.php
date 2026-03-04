<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SsoController extends Controller
{
    /**
     * Mengirim user yang sedang login di SIAKAD ke aplikasi SI ASET
     */
    public function goToSiAset(Request $request, $surveyId)
    {
        // 1. Ambil data mahasiswa/dosen yang sedang login di web SIAKAD
        $user = auth()->user();

        // 2. Hapus token SSO lama agar database personal_access_tokens tidak bengkak
        $user->tokens()->where('name', 'sso-siaset')->delete();

        // 3. Buat token Sanctum baru khusus untuk menyeberang ke SI ASET
        $token = $user->createToken('sso-siaset')->plainTextToken;

        // 4. Arahkan user ke URL SI ASET (Sesuaikan domain SI ASET Anda)
        // Gunakan env('SIASET_URL') agar rapi, atau hardcode sementara
        $siAsetUrl = env('SIASET_URL', 'http://127.0.0.1:8000');

        $redirectUrl = "{$siAsetUrl}/survei/{$surveyId}?token={$token}";

        // Lempar keluar dari aplikasi SIAKAD menuju SI ASET
        return redirect()->away($redirectUrl);
    }
}
