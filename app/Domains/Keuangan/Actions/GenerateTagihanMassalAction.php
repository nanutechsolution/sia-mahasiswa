<?php

namespace App\Domains\Keuangan\Actions;

use App\Domains\Keuangan\Models\TagihanMahasiswa;
use App\Domains\Keuangan\Models\SkemaTarif;
use App\Domains\Mahasiswa\Models\Mahasiswa;
use App\Helpers\SistemHelper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GenerateTagihanMassalAction
{
    public function execute($tahunAkademikId, $angkatanId, $prodiId = null)
    {
        $query = Mahasiswa::where('angkatan_id', $angkatanId);

        if ($prodiId) {
            $query->where('prodi_id', $prodiId);
        }

        $mahasiswas = $query->with(['programKelas', 'prodi'])->get();

        $stats = ['sukses' => 0, 'skip' => 0, 'errors' => []];
        $userId = Auth::id();

        DB::beginTransaction();
        try {
            foreach ($mahasiswas as $mhs) {

                /* ================= SEMESTER ================= */
                $semester = SistemHelper::semesterMahasiswa($mhs);

                /* ================= SKEMA ================= */
                $skema = SkemaTarif::where('angkatan_id', $mhs->angkatan_id)
                    ->where('prodi_id', $mhs->prodi_id)
                    ->where('program_kelas_id', $mhs->program_kelas_id)
                    ->first();

                if (!$skema) {
                    $stats['errors'][] = "Skema tarif tidak ditemukan: {$mhs->nim}";
                    continue;
                }

                /* ================= DETAIL TARIF ================= */
                $details = $skema->details()
                    ->untukSemester($semester)
                    ->with('komponenBiaya')
                    ->get();

                if ($details->isEmpty()) {
                    $stats['skip']++;
                    continue;
                }

                /* ================= TARGET ================= */
                $totalTarget = 0;
                $rincianTarget = [];

                foreach ($details as $detail) {
                    if (!$detail->komponenBiaya) continue;

                    $totalTarget += $detail->nominal;
                    $rincianTarget[$detail->komponenBiaya->nama_komponen] = $detail->nominal;
                }

                if ($totalTarget <= 0) {
                    $stats['skip']++;
                    continue;
                }

                /* ================= TAGIHAN LAMA ================= */
                $existing = TagihanMahasiswa::where('mahasiswa_id', $mhs->id)
                    ->where('tahun_akademik_id', $tahunAkademikId)
                    ->get();

                $totalSudah = $existing->sum('total_tagihan');
                $kekurangan = $totalTarget - $totalSudah;

                if ($kekurangan <= 0) {
                    $stats['skip']++;
                    continue;
                }

                /* ================= RINCIAN CERDAS ================= */
                $komponenLama = [];
                foreach ($existing as $inv) {
                    if (is_array($inv->rincian_item)) {
                        foreach ($inv->rincian_item as $item) {
                            $komponenLama[$item['nama']] =
                                ($komponenLama[$item['nama']] ?? 0) + $item['nominal'];
                        }
                    }
                }

                $rincianBaru = [];
                $temp = 0;

                foreach ($rincianTarget as $nama => $nominal) {
                    $sudah = $komponenLama[$nama] ?? 0;
                    if ($nominal > $sudah) {
                        $selisih = min($nominal - $sudah, $kekurangan - $temp);
                        if ($selisih > 0) {
                            $rincianBaru[] = [
                                'nama' => $nama,
                                'nominal' => $selisih
                            ];
                            $temp += $selisih;
                        }
                    }
                }

                if ($temp < $kekurangan) {
                    $rincianBaru[] = [
                        'nama' => 'Penyesuaian Biaya',
                        'nominal' => $kekurangan - $temp
                    ];
                }

                /* ================= SIMPAN ================= */
                TagihanMahasiswa::create([
                    'mahasiswa_id' => $mhs->id,
                    'tahun_akademik_id' => $tahunAkademikId,
                    'kode_transaksi' => 'INV-' . $tahunAkademikId . '-' . $mhs->nim . '-' . Str::upper(Str::random(6)),
                    'deskripsi' => "Tagihan Semester {$semester}",
                    'total_tagihan' => $kekurangan,
                    'total_bayar' => 0,
                    'status_bayar' => 'BELUM',
                    'rincian_item' => $rincianBaru,
                    'created_by' => $userId
                ]);

                $stats['sukses']++;
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        return $stats;
    }
}
