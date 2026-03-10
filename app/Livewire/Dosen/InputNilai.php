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
    public $komponenBobot = []; 
    public $pesertaKelas = [];
    
    public $inputNilai = [];
    public $isLocked = false;

    public function mount($jadwalId)
    {
        $this->jadwalId = $jadwalId;
        $this->loadConfiguration();
        $this->checkSecurity(); // Tambahan Keamanan
        $this->loadStudents();
    }

    /**
     * Memastikan dosen yang login adalah bagian dari Team Teaching jadwal ini
     */
    private function checkSecurity()
    {
        $dosenId = Auth::user()->person->dosen->id ?? null;
        
        $isAuthorized = $this->jadwal->dosens->contains('id', $dosenId);

        if (!$isAuthorized) {
            abort(403, 'Anda tidak memiliki otorisasi untuk menginput nilai pada kelas ini.');
        }
    }

    public function loadConfiguration()
    {
        // Eager load dosens dan ruang untuk efisiensi
        $this->jadwal = JadwalKuliah::with(['mataKuliah', 'tahunAkademik', 'dosens', 'ruang'])
            ->findOrFail($this->jadwalId);
        
        $kurikulumId = DB::table('kurikulum_mata_kuliah')
            ->where('mata_kuliah_id', $this->jadwal->mata_kul_id ?? $this->jadwal->mata_kuliah_id)
            ->where('kurikulum_id', $this->jadwal->kurikulum_id)
            ->value('kurikulum_id');

        if (!$kurikulumId) {
            session()->flash('error', 'Konfigurasi kurikulum tidak ditemukan.');
            return;
        }

        $this->komponenBobot = DB::table('kurikulum_komponen_nilai as kkn')
            ->join('ref_komponen_nilai as rk', 'kkn.komponen_id', '=', 'rk.id')
            ->where('kkn.kurikulum_id', $kurikulumId)
            ->select('rk.id', 'rk.nama_komponen', 'kkn.bobot_persen')
            ->get();

        $this->isLocked = !($this->jadwal->tahunAkademik->buka_input_nilai ?? true);
    }

    public function loadStudents()
    {
        $this->pesertaKelas = KrsDetail::with(['krs.mahasiswa.person'])
            ->where('jadwal_kuliah_id', $this->jadwalId)
            ->whereHas('krs', fn($q) => $q->where('status_krs', 'DISETUJUI'))
            ->get();

        foreach ($this->pesertaKelas as $mhs) {
            $existingValues = DB::table('krs_detail_nilai')
                ->where('krs_detail_id', $mhs->id)
                ->pluck('nilai_angka', 'komponen_id');

            foreach ($this->komponenBobot as $k) {
                $this->inputNilai[$mhs->id][$k->id] = $existingValues[$k->id] ?? 0;
            }
        }
    }

    public function saveLine($mhsDetailId)
    {
        if ($this->isLocked) return;

        DB::transaction(function () use ($mhsDetailId) {
            foreach ($this->inputNilai[$mhsDetailId] as $komponenId => $nilai) {
                DB::table('krs_detail_nilai')->updateOrInsert(
                    ['krs_detail_id' => $mhsDetailId, 'komponen_id' => $komponenId],
                    ['nilai_angka' => $nilai ?: 0, 'updated_at' => now(), 'created_at' => now()]
                );
            }

            $detail = KrsDetail::find($mhsDetailId);
            (new HitungNilaiAkhirAction())->execute($detail);
        });

        session()->flash('ok-' . $mhsDetailId, 'Tersimpan');
        $this->loadStudents(); 
    }

    public function publishAll()
    {
        if ($this->isLocked) return;

        DB::transaction(function () {
            $action = new HitungNilaiAkhirAction();
            foreach ($this->pesertaKelas as $mhs) {
                $mhs->update(['is_published' => true]);
                // Logika Observer akan otomatis mengupdate akademik_transkrip (Materialized View)
                $action->hitungIps($mhs->krs); 
            }
        });

        session()->flash('global_success', 'Seluruh nilai telah dipublikasikan dan terkunci di KHS Mahasiswa.');
        $this->loadStudents();
    }

    public function render()
    {
        return view('livewire.dosen.input-nilai');
    }
}