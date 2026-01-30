<?php

namespace App\Livewire\Dosen;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Domains\Akademik\Models\Krs;
use App\Domains\Mahasiswa\Models\RiwayatStatusMahasiswa;
use App\Domains\Akademik\Models\KrsDetail; // Import Model

class PerwalianDetail extends Component
{
    public $krsId;
    public $krs;
    public $riwayat; 
    public $khsLalu = []; 
    public $totalSks = 0;

    public function mount($krsId)
    {
        $this->krsId = $krsId;
        $this->loadData();
    }

    public function loadData()
    {
        $user = Auth::user();
        
        if (!$user->person || !$user->person->dosen) {
            abort(403, 'Akses Ditolak. Profil Dosen tidak ditemukan.');
        }
        $dosenId = $user->person->dosen->id;

        $this->krs = Krs::with(['mahasiswa.person', 'mahasiswa.prodi', 'details.jadwalKuliah.mataKuliah', 'details.jadwalKuliah.dosen'])
            ->findOrFail($this->krsId);

        if ($this->krs->mahasiswa->dosen_wali_id !== $dosenId) {
            abort(403, 'Anda tidak memiliki hak akses ke KRS mahasiswa ini.');
        }

        // Ambil Riwayat Akademik Terakhir
        $this->riwayat = RiwayatStatusMahasiswa::where('mahasiswa_id', $this->krs->mahasiswa_id)
            ->where('tahun_akademik_id', '<', $this->krs->tahun_akademik_id)
            ->orderBy('tahun_akademik_id', 'desc')
            ->first();

        // [BARU] Ambil Detail Mata Kuliah & Nilai Semester Lalu
        if ($this->riwayat) {
            $this->khsLalu = KrsDetail::join('jadwal_kuliah', 'krs_detail.jadwal_kuliah_id', '=', 'jadwal_kuliah.id')
                ->join('master_mata_kuliahs', 'jadwal_kuliah.mata_kuliah_id', '=', 'master_mata_kuliahs.id')
                ->join('krs', 'krs_detail.krs_id', '=', 'krs.id')
                ->where('krs.mahasiswa_id', $this->krs->mahasiswa_id)
                ->where('krs.tahun_akademik_id', $this->riwayat->tahun_akademik_id) // Sesuai semester riwayat
                ->where('is_published', true) // Hanya nilai final
                ->select('master_mata_kuliahs.nama_mk', 'master_mata_kuliahs.kode_mk', 'master_mata_kuliahs.sks_default', 'krs_detail.nilai_huruf', 'krs_detail.nilai_indeks')
                ->get();
        }

        $this->totalSks = $this->krs->details->sum(fn($d) => $d->jadwalKuliah->mataKuliah->sks_default ?? 0);
    }

    public function setujui()
    {
        $this->krs->update(['status_krs' => 'DISETUJUI']);
        session()->flash('success', 'KRS Berhasil Disetujui.');
    }

    public function tolak()
    {
        $this->krs->update(['status_krs' => 'DRAFT']); 
        session()->flash('success', 'KRS Ditolak/Dikembalikan ke Mahasiswa.');
    }

    public function render()
    {
        return view('livewire.dosen.perwalian-detail');
    }
}