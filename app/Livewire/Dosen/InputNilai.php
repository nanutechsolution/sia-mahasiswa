<?php

namespace App\Livewire\Dosen;

use Livewire\Component;
use App\Domains\Akademik\Models\JadwalKuliah;
use App\Domains\Akademik\Models\KrsDetail;
use App\Domains\Akademik\Actions\HitungNilaiAkhirAction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class InputNilai extends Component
{
    public $jadwalId;
    public $jadwal;
    public $komponenBobot = []; // Daftar komponen (nama & persen)
    public $pesertaKelas = [];
    
    // State Form: inputNilai[mhs_id][komponen_id] = nilai
    public $inputNilai = [];
    public $isLocked = false;

    public function mount($jadwalId)
    {
        $this->jadwalId = $jadwalId;
        $this->loadConfiguration();
        $this->loadStudents();
    }

    /**
     * Load konfigurasi komponen nilai berdasarkan kurikulum matakuliah
     */
    public function loadConfiguration()
    {
        $this->jadwal = JadwalKuliah::with(['mataKuliah', 'tahunAkademik'])->findOrFail($this->jadwalId);
        
        // 1. Cari Kurikulum yang memuat matakuliah ini
        $kurikulumId = DB::table('kurikulum_mata_kuliah')
            ->where('mata_kuliah_id', $this->jadwal->mata_kuliah_id)
            ->value('kurikulum_id');

        if (!$kurikulumId) {
            session()->flash('error', 'Konfigurasi kurikulum untuk matakuliah ini tidak ditemukan.');
            return;
        }

        // 2. Ambil Komponen & Bobot (SSOT dari Manajemen Bobot Nilai)
        $this->komponenBobot = DB::table('kurikulum_komponen_nilai as kkn')
            ->join('ref_komponen_nilai as rk', 'kkn.komponen_id', '=', 'rk.id')
            ->where('kkn.kurikulum_id', $kurikulumId)
            ->select('rk.id', 'rk.nama_komponen', 'kkn.bobot_persen')
            ->get();

        $this->isLocked = !$this->jadwal->tahunAkademik->buka_input_nilai;
    }

    /**
     * Load daftar mahasiswa dan nilai yang sudah ada
     */
    public function loadStudents()
    {
        $this->pesertaKelas = KrsDetail::with(['krs.mahasiswa.person'])
            ->where('jadwal_kuliah_id', $this->jadwalId)
            ->whereHas('krs', fn($q) => $q->where('status_krs', 'DISETUJUI'))
            ->get();

        foreach ($this->pesertaKelas as $mhs) {
            // Ambil nilai per komponen dari tabel transaksi krs_detail_nilai
            $existingValues = DB::table('krs_detail_nilai')
                ->where('krs_detail_id', $mhs->id)
                ->pluck('nilai_angka', 'komponen_id');

            foreach ($this->komponenBobot as $k) {
                $this->inputNilai[$mhs->id][$k->id] = $existingValues[$k->id] ?? 0;
            }
        }
    }

    /**
     * Simpan nilai mahasiswa (per baris)
     */
    public function saveLine($mhsDetailId)
    {
        if ($this->isLocked) return;

        DB::transaction(function () use ($mhsDetailId) {
            // 1. Simpan/Update nilai per komponen
            foreach ($this->inputNilai[$mhsDetailId] as $komponenId => $nilai) {
                DB::table('krs_detail_nilai')->updateOrInsert(
                    ['krs_detail_id' => $mhsDetailId, 'komponen_id' => $komponenId],
                    ['nilai_angka' => $nilai ?: 0, 'updated_at' => now(), 'created_at' => now()]
                );
            }

            // 2. Jalankan Action Hitung Nilai Akhir (Total, Huruf, Indeks)
            $detail = KrsDetail::find($mhsDetailId);
            (new HitungNilaiAkhirAction())->execute($detail);
        });

        session()->flash('ok-' . $mhsDetailId, 'Tersimpan');
        $this->loadStudents(); // Refresh data tampilan
    }

    /**
     * Publikasikan nilai agar muncul di KHS Mahasiswa
     */
    public function publishAll()
    {
        if ($this->isLocked) return;

        DB::transaction(function () {
            $action = new HitungNilaiAkhirAction();
            foreach ($this->pesertaKelas as $mhs) {
                $mhs->update(['is_published' => true]);
                $action->hitungIps($mhs->krs); // Update IPK/IPS mahasiswa
            }
        });

        session()->flash('global_success', 'Seluruh nilai berhasil dipublikasikan ke KHS.');
        $this->loadStudents();
    }

    public function render()
    {
        return view('livewire.dosen.input-nilai');
    }
}