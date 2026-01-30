<?php

namespace App\Http\Controllers\Api;

use App\Domains\Core\Models\Person as ModelsPerson;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Person; // Pastikan Model Person diimport
use App\Domains\Mahasiswa\Models\Mahasiswa;
use App\Domains\Core\Models\Prodi;
use App\Domains\Core\Models\ProgramKelas;
use App\Domains\Keuangan\Models\SkemaTarif;
use App\Domains\Keuangan\Models\TagihanMahasiswa;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Helpers\SistemHelper;

class PmbIntegrationController extends Controller
{
    /**
     * Endpoint: POST /api/v1/pmb/receive-camaba
     * Menerima data kelulusan dari PMB
     */
    public function receiveCamaba(Request $request)
    {
        // [BARU] Custom Messages Bahasa Indonesia
        $messages = [
            'required' => ':attribute wajib diisi.',
            'unique'   => ':attribute sudah terdaftar di dalam sistem SIAKAD.',
            'email'    => 'Format email tidak valid.',
            'digits'   => ':attribute harus terdiri dari :digits digit angka.',
            'string'   => ':attribute harus berupa teks.',
            'integer'  => ':attribute harus berupa angka.',
            'exists'   => 'Data :attribute tidak ditemukan di database SIAKAD.',
            'in'       => 'Pilihan :attribute tidak valid.',
        ];

        // 1. Validasi Input Eksternal
        $validator = Validator::make($request->all(), [
            'nomor_pendaftaran' => 'required',
            'nama_lengkap'      => 'required|string|max:150',
            'nik'               => 'required|digits:16|unique:ref_person,nik',
            'email'             => 'required|email|unique:ref_person,email',
            'nomor_hp'          => 'required|string',
            'kode_prodi'        => 'required|exists:ref_prodi,kode_prodi_internal',
            'kode_program'      => 'required|exists:ref_program_kelas,kode_internal',
            'tahun_masuk'       => 'required|integer|digits:4',
            'jenis_kelamin'     => 'required|in:L,P',
        ], $messages); // <--- Masukkan pesan custom di sini

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi Gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // Cek duplikasi nomor pendaftaran di JSON data_tambahan
        $exists = Mahasiswa::where('data_tambahan->pmb_no_daftar', $request->nomor_pendaftaran)->exists();
        if ($exists) {
            return response()->json([
                'status' => 'error',
                'message' => 'Nomor Pendaftaran ini sudah terdaftar di SIAKAD.'
            ], 409);
        }

        DB::beginTransaction();
        try {
            $data = $request->all();

            // 2. Ambil Data Referensi
            $prodi = Prodi::where('kode_prodi_internal', $data['kode_prodi'])->firstOrFail();
            $programKelas = ProgramKelas::where('kode_internal', $data['kode_program'])->firstOrFail();

            // 3. SETUP IDENTITAS SEMENTARA (CAMABA)
            $identityTemp = $data['nomor_pendaftaran'];

            // 4. Buat Data Person (SSOT Biodata)
            $person = ModelsPerson::create([
                'nama_lengkap' => $data['nama_lengkap'],
                'nik' => $data['nik'],
                'email' => $data['email'],
                'no_hp' => $data['nomor_hp'],
                'jenis_kelamin' => $data['jenis_kelamin'],
                'created_at' => now()
            ]);

            // 5. Buat User Login (Linked to Person)
            $user = User::create([
                'name' => $data['nama_lengkap'],
                'username' => $identityTemp,
                'email' => $data['email'],
                'password' => Hash::make($identityTemp), // Password default = No Pendaftaran
                'role' => 'mahasiswa',
                'is_active' => true,
                'person_id' => $person->id // Link ke Person
            ]);
            
            $user->assignRole('mahasiswa');

            // 6. Buat Data Akademik Mahasiswa (Linked to Person)
            $mhs = Mahasiswa::create([
                'person_id' => $person->id, // KUNCI SSOT
                'nim' => $identityTemp, // Masih pakai No Pendaftaran
                'angkatan_id' => $data['tahun_masuk'],
                'prodi_id' => $prodi->id,
                'program_kelas_id' => $programKelas->id,
                'data_tambahan' => [
                    'pmb_no_daftar' => $data['nomor_pendaftaran'],
                    'status_awal' => 'CAMABA',
                    'jalur_masuk' => $data['jalur_masuk'] ?? 'UMUM',
                    'asal_sekolah' => $data['asal_sekolah'] ?? null,
                ]
            ]);

            // 7. Generate Tagihan Awal
            $this->generateTagihanAwal($mhs);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Data Camaba diterima. Silakan lakukan pembayaran untuk mendapatkan NIM.',
                'data' => [
                    'nomor_pendaftaran' => $identityTemp,
                    'user_id' => $user->id,
                    'tagihan_info' => 'Tagihan awal telah diterbitkan'
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate Tagihan Pertama (Biasanya Uang Pangkal + SPP Smt 1)
     */
    private function generateTagihanAwal(Mahasiswa $mhs)
    {
        // Cari Semester Aktif
        $taId = SistemHelper::idTahunAktif();
        if (!$taId) return;

        // Cari Skema Tarif yang cocok
        $skema = SkemaTarif::with('details.komponenBiaya')
            ->where('angkatan_id', $mhs->angkatan_id)
            ->where('prodi_id', $mhs->prodi_id)
            ->where('program_kelas_id', $mhs->program_kelas_id)
            ->first();

        if (!$skema) return; 

        $total = 0;
        $rincian = [];

        foreach ($skema->details as $detail) {
            // Logika: Ambil biaya yang Semester=1 (Awal) atau Kosong (Rutin)
            if ($detail->berlaku_semester == 1 || is_null($detail->berlaku_semester)) {
                $total += $detail->nominal;
                $rincian[] = [
                    'nama' => $detail->komponenBiaya->nama_komponen,
                    'nominal' => $detail->nominal
                ];
            }
        }

        if ($total > 0) {
            TagihanMahasiswa::create([
                'mahasiswa_id' => $mhs->id,
                'tahun_akademik_id' => $taId,
                'kode_transaksi' => 'INV-PMB-' . $mhs->nim . '-' . rand(100, 999),
                'deskripsi' => "Tagihan Daftar Ulang (Awal Masuk)",
                'total_tagihan' => $total,
                'total_bayar' => 0,
                'status_bayar' => 'BELUM',
                'rincian_item' => $rincian
            ]);
        }
    }
}