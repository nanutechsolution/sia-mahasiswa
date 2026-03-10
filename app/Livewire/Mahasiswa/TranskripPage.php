<?php

namespace App\Livewire\Mahasiswa;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Domains\Mahasiswa\Models\Mahasiswa;
use App\Models\AkademikTranskrip;
use Illuminate\Support\Facades\DB;

class TranskripPage extends Component
{
    public $mahasiswa;

    // Statistik Akademik Kumulatif
    public $totalSks = 0;
    public $totalMutu = 0;
    public $ipk = 0;

    public function mount()
    {
        $user = Auth::user();

        if (!$user->person_id) {
            abort(403, 'Akun Anda belum terhubung dengan Data Personil (SSOT).');
        }

        $this->mahasiswa = Mahasiswa::with(['prodi.fakultas', 'programKelas', 'person'])
            ->where('person_id', $user->person_id)
            ->firstOrFail();
    }

    public function render()
    {
        // PERBAIKAN: Ambil data dari Materialized View 'akademik_transkrip'
        // Ini menjamin performa tinggi dan akurasi nilai terbaik (logic retake sudah dihandle observer)
        $riwayatBelajar = AkademikTranskrip::with([
                'mataKuliah', 
                'krsDetail.krs.tahunAkademik'
            ])
            ->where('mahasiswa_id', $this->mahasiswa->id)
            ->get()
            ->sortBy(function($item) {
                // Urutkan berdasarkan kode tahun akademik dari relasi krsDetail
                return $item->krsDetail->krs->tahunAkademik->kode_tahun ?? 0;
            });

        // Hitung Statistik Kumulatif
        $this->totalSks = $riwayatBelajar->sum('sks_diakui');
        $this->totalMutu = $riwayatBelajar->sum(function ($item) {
            return $item->sks_diakui * $item->nilai_indeks_final;
        });

        if ($this->totalSks > 0) {
            $this->ipk = round($this->totalMutu / $this->totalSks, 2);
        }

        // Group per Semester untuk tampilan UI yang rapi
        $transkripGrouped = $riwayatBelajar->groupBy(function($item) {
            return $item->krsDetail->krs->tahunAkademik->nama_tahun ?? 'Data Konversi/Lainnya';
        });

        return view('livewire.mahasiswa.transkrip-page', [
            'transkripGrouped' => $transkripGrouped
        ]);
    }
}