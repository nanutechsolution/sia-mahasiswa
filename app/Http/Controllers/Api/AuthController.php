<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        // $token = $user->createToken('survey-session', ['survey:fill'])->plainTextToken;
        $token = $user->createToken(
            'spmi-session',
            ['survey:fill', 'audit:view']
        )->plainTextToken;
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
    /**
     * Helper untuk validasi Header X-SIMA-KEY
     */
    private function isUnauthorized(Request $request)
    {
        // Pastikan key ini sama dengan yang ada di .env SIAKAD
        $serverKey = config('app.sima_api_key');
        $clientKey = $request->header('X-SIMA-KEY');

        return (!$clientKey || $clientKey !== $serverKey);
    }

    public function getSemesters(Request $request)
    {
        // Gunakan helper pengecekan yang sama
        if ($this->isUnauthorized($request)) {
            return response()->json(['message' => 'Unauthorized API Call.'], 401);
        }

        $semesters = DB::table('ref_tahun_akademik')
            ->select('id as id', 'nama_tahun as name')
            ->where('is_active', true)
            ->orderBy('nama_tahun', 'desc')
            ->get();

        return response()->json(['data' => $semesters]);
    }

    /**
     * Mengambil data KRS mahasiswa untuk diisi EDOM-nya
     */
    public function getUserKrs(Request $request)
    {
        if ($this->isUnauthorized($request)) {
            return response()->json(['message' => 'Unauthorized API Call.'], 401);
        }

        $studentId = $request->query('student_id'); // NIM atau UUID
        $semesterId = $request->query('semester_id'); // ID dari ref_tahun_akademik

        // Query disesuaikan dengan schema:
        // krs -> krs_detail -> jadwal_kuliah -> jadwal_kuliah_dosen -> trx_dosen -> ref_person
        $krs = DB::table('krs')
            ->join('mahasiswas', 'krs.mahasiswa_id', '=', 'mahasiswas.id')
            ->join('krs_detail', 'krs.id', '=', 'krs_detail.krs_id')
            ->join('jadwal_kuliah', 'krs_detail.jadwal_kuliah_id', '=', 'jadwal_kuliah.id')
            ->join('master_mata_kuliahs', 'jadwal_kuliah.mata_kuliah_id', '=', 'master_mata_kuliahs.id')
            // Join ke Dosen Pengampu melalui jadwal_kuliah_dosen
            ->leftJoin('jadwal_kuliah_dosen', function ($join) {
                $join->on('jadwal_kuliah.id', '=', 'jadwal_kuliah_dosen.jadwal_kuliah_id')
                    ->where('jadwal_kuliah_dosen.is_koordinator', 1); // Ambil koordinator sebagai perwakilan
            })
            ->leftJoin('trx_dosen', 'jadwal_kuliah_dosen.dosen_id', '=', 'trx_dosen.id')
            ->leftJoin('ref_person', 'trx_dosen.person_id', '=', 'ref_person.id')
            ->where(function ($query) use ($studentId) {
                $query->where('mahasiswas.nim', $studentId)
                    ->orWhere('mahasiswas.id', $studentId);
            })
            ->where('krs.tahun_akademik_id', $semesterId)
            ->select([
                'master_mata_kuliahs.kode_mk as course_id',
                'master_mata_kuliahs.nama_mk as course_name',
                'trx_dosen.nidn as lecturer_id',
                'ref_person.nama_lengkap as lecturer_name',
                'jadwal_kuliah.id as class_id',
                'jadwal_kuliah.nama_kelas as class_name',
            ])
            ->get();

        return response()->json(['data' => $krs]);
    }

    /**
     * BARU: Mengambil daftar Program Studi
     * Dipakai untuk filter grafik di SPMI
     */
    public function getProdi(Request $request)
    {
        if ($this->isUnauthorized($request)) {
            return response()->json(['message' => 'Unauthorized API Call.'], 401);
        }

        $prodi = DB::table('ref_prodi')
            ->select('id', 'nama_prodi as name')
            ->where('is_active', true)
            ->orderBy('nama_prodi', 'asc')
            ->get();

        return response()->json(['data' => $prodi]);
    }
}
