<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
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
        // 1. Validasi Input Eksternal
        $validator = Validator::make($request->all(), [
            'nomor_pendaftaran' => 'required|unique:mahasiswas,data_tambahan->pmb_no_daftar',
            'nama_lengkap'      => 'required|string|max:150',
            'nik'               => 'required|digits:16|unique:mahasiswas,nik',
            'email'             => 'required|email|unique:users,email',
            'nomor_hp'          => 'required|string',
            'kode_prodi'        => 'required|exists:ref_prodi,kode_prodi_internal', // Ex: TI, SI
            'kode_program'      => 'required|exists:ref_program_kelas,kode_internal', // Ex: REG, EKS
            'tahun_masuk'       => 'required|integer|digits:4',
            'jenis_kelamin'     => 'required|in:L,P',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi Gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $data = $request->all();

            // 2. Ambil Data Referensi
            $prodi = Prodi::where('kode_prodi_internal', $data['kode_prodi'])->firstOrFail();
            $programKelas = ProgramKelas::where('kode_internal', $data['kode_program'])->firstOrFail();

            // 3. SETUP IDENTITAS SEMENTARA (CAMABA)
            // Jangan generate NIM dulu. Gunakan No Pendaftaran sebagai identitas & login.
            // NIM resmi akan dibuatkan Admin/Sistem setelah pembayaran Daftar Ulang lunas.
            $identityTemp = $data['nomor_pendaftaran'];

            // 4. Buat User Login (Username = No Pendaftaran)
            $user = User::create([
                'name' => $data['nama_lengkap'],
                'username' => $identityTemp,
                'email' => $data['email'],
                'password' => Hash::make($identityTemp), // Password default = No Pendaftaran
                'role' => 'mahasiswa', // Role tetap mahasiswa agar bisa login, tapi akses KRS dibatasi via logic keuangan
                'is_active' => true,
            ]);

            // Assign Role Spatie
            $user->assignRole('mahasiswa');

            // 5. Buat Data Mahasiswa (NIM diisi No Pendaftaran dulu)
            $mhs = Mahasiswa::create([
                'user_id' => $user->id,
                'nim' => $identityTemp,
                'nama_lengkap' => $data['nama_lengkap'],
                'angkatan_id' => $data['tahun_masuk'],
                'prodi_id' => $prodi->id,
                'program_kelas_id' => $programKelas->id,
                'nik' => $data['nik'],
                'nomor_hp' => $data['nomor_hp'],
                'email_pribadi' => $data['email'],
                'jenis_kelamin' => $data['jenis_kelamin'],
                'data_tambahan' => [
                    'pmb_no_daftar' => $data['nomor_pendaftaran'],
                    'status_awal' => 'CAMABA', // Flag untuk membedakan
                    'jalur_masuk' => $data['jalur_masuk'] ?? 'UMUM',
                    'asal_sekolah' => $data['asal_sekolah'] ?? null,
                ]
            ]);

            // 6. Generate Tagihan Awal (Uang Pangkal/Daftar Ulang)
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
     * Logic Generate NIM Custom
     * Format: {THN}{KODE}{NO:4} -> 24552010001
     * NOTE: Method ini akan dipanggil nanti di fitur "Validasi Daftar Ulang"
     */
    public function generateNim(Prodi $prodi, $tahunMasuk)
    {
        // Kunci database agar tidak race condition (Berebut nomor urut)
        // Kita lock row prodi sebentar
        $prodi = Prodi::where('id', $prodi->id)->lockForUpdate()->first();

        $format = $prodi->format_nim ?? '{TAHUN}{KODE}{NO:4}'; // Default pattern
        $nextSeq = $prodi->last_nim_seq + 1;

        // Parse Variabel
        $nim = str_replace('{TAHUN}', $tahunMasuk, $format);
        $nim = str_replace('{THN}', substr($tahunMasuk, 2, 2), $nim);
        $nim = str_replace('{KODE}', $prodi->kode_prodi_dikti ?? '00000', $nim);
        $nim = str_replace('{INTERNAL}', $prodi->kode_prodi_internal, $nim);

        // Parse Nomor Urut (Regex untuk mencari {NO:x})
        if (preg_match('/\{NO:(\d+)\}/', $nim, $matches)) {
            $length = $matches[1];
            $number = str_pad($nextSeq, $length, '0', STR_PAD_LEFT);
            $nim = str_replace($matches[0], $number, $nim);
        } else {
            // Fallback jika tidak ada pattern NO
            $nim .= str_pad($nextSeq, 4, '0', STR_PAD_LEFT);
        }

        // Update Counter di Prodi
        $prodi->update(['last_nim_seq' => $nextSeq]);

        return $nim;
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

        if (!$skema) return; // Jika belum ada tarif, skip saja (nanti admin generate manual)

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
                'kode_transaksi' => 'INV-PMB-' . $mhs->nim,
                'deskripsi' => "Tagihan Daftar Ulang (Awal Masuk)",
                'total_tagihan' => $total,
                'total_bayar' => 0,
                'status_bayar' => 'BELUM',
                'rincian_item' => $rincian
            ]);
        }
    }
}
