<?php

namespace App\Livewire\Dosen;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Domains\Akademik\Models\Krs;
use App\Domains\Mahasiswa\Models\RiwayatStatusMahasiswa;
use App\Domains\Akademik\Models\KrsDetail;
use App\Models\AkademikTranskrip;
use App\Helpers\SistemHelper;

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

        // Load KRS dengan relasi Team Teaching dan Ruangan terbaru
        $this->krs = Krs::with([
            'mahasiswa.person', 
            'mahasiswa.prodi', 
            'details.jadwalKuliah.mataKuliah', 
            'details.jadwalKuliah.dosens.person',
            'details.jadwalKuliah.ruang'
        ])->findOrFail($this->krsId);

        if ($this->krs->mahasiswa->dosen_wali_id !== $dosenId) {
            abort(403, 'Anda tidak memiliki hak akses ke KRS mahasiswa ini.');
        }

        // Ambil Riwayat Akademik Terakhir (Snapshot IPK/IPS)
        $this->riwayat = RiwayatStatusMahasiswa::where('mahasiswa_id', $this->krs->mahasiswa_id)
            ->where('tahun_akademik_id', '<', $this->krs->tahun_akademik_id)
            ->orderBy('tahun_akademik_id', 'desc')
            ->first();

        // Ambil Detail KHS Semester Lalu menggunakan relasi yang benar
        if ($this->riwayat) {
            $this->khsLalu = KrsDetail::with(['jadwalKuliah.mataKuliah'])
                ->whereHas('krs', function($q) {
                    $q->where('mahasiswa_id', $this->krs->mahasiswa_id)
                      ->where('tahun_akademik_id', $this->riwayat->tahun_akademik_id);
                })
                ->where('is_published', true)
                ->get();
        }

        $this->totalSks = $this->krs->details->sum('sks_snapshot');
    }

    public function setujui()
    {
        $this->krs->update(['status_krs' => 'DISETUJUI']);
        session()->flash('success', 'KRS Mahasiswa berhasil disetujui.');
    }

    public function tolak()
    {
        $this->krs->update(['status_krs' => 'DRAFT']); 
        session()->flash('success', 'KRS telah dikembalikan ke Mahasiswa untuk direvisi.');
    }

    public function render()
    {
        return view('livewire.dosen.perwalian-detail');
    }
}