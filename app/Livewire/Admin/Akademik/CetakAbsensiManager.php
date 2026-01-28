<?php

namespace App\Livewire\Admin\Akademik;

use Livewire\Component;
use App\Domains\Core\Models\Prodi;
use App\Domains\Akademik\Models\JadwalKuliah;
use App\Domains\Akademik\Models\KrsDetail;
use App\Helpers\SistemHelper;
use Barryvdh\DomPDF\Facade\Pdf;

class CetakAbsensiManager extends Component
{
    // Filter
    public $filterProdiId;
    public $semesterId;

    public function mount()
    {
        $this->semesterId = SistemHelper::idTahunAktif();
        $this->filterProdiId = Prodi::first()->id ?? null;
    }

    public function render()
    {
        $prodis = Prodi::all();
        
        // Ambil jadwal kuliah beserta jumlah mahasiswa yang mengambil
        // KrsDetail -> status_krs harus DISETUJUI agar masuk absen
        $jadwals = JadwalKuliah::with(['mataKuliah', 'dosen', 'programKelasAllow'])
            ->where('tahun_akademik_id', $this->semesterId)
            ->whereHas('mataKuliah', function($q) {
                $q->where('prodi_id', $this->filterProdiId);
            })
            // Hitung jumlah mahasiswa yang KRS-nya DISETUJUI
            ->withCount(['krsDetails as peserta_count' => function ($query) {
                $query->whereHas('krs', function ($q) {
                    $q->where('status_krs', 'DISETUJUI');
                });
            }])
            ->orderBy('nama_kelas', 'asc')
            ->get();

        return view('livewire.admin.akademik.cetak-absensi-manager', [
            'jadwals' => $jadwals,
            'prodis' => $prodis
        ]);
    }

    public function cetakPdf($jadwalId)
    {
        // Redirect ke Controller untuk download PDF
        return redirect()->route('admin.cetak.absensi', ['jadwalId' => $jadwalId]);
    }
}