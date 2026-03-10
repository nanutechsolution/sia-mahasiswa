<?php

namespace App\Livewire\Admin\Akademik;

use Livewire\Component;
use Livewire\WithPagination;
use App\Domains\Core\Models\Prodi;
use App\Domains\Core\Models\TahunAkademik;
use App\Domains\Akademik\Models\JadwalKuliah;
use App\Helpers\SistemHelper;

class CetakAbsensiManager extends Component
{
    use WithPagination;

    // Filter States
    public $filterProdiId;
    public $filterSemesterId;
    public $search = '';

    public function mount()
    {
        $this->filterSemesterId = SistemHelper::idTahunAktif();
        $this->filterProdiId = Prodi::first()->id ?? null;
    }

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['filterProdiId', 'filterSemesterId', 'search'])) {
            $this->resetPage();
        }
    }

    public function render()
    {
        $prodis = Prodi::all();
        $semesters = TahunAkademik::orderBy('kode_tahun', 'desc')->get();

        $jadwals = JadwalKuliah::with(['mataKuliah.prodi', 'dosens.person', 'ruang', 'programKelasAllow'])
            ->where('tahun_akademik_id', $this->filterSemesterId)
            ->when($this->filterProdiId, function ($q) {
                $q->whereHas('mataKuliah', function ($subQ) {
                    $subQ->where('prodi_id', $this->filterProdiId);
                });
            })
            ->when($this->search, function ($q) {
                $q->where(function($query) {
                    $query->whereHas('mataKuliah', function ($subQ) {
                        $subQ->where('nama_mk', 'like', '%' . $this->search . '%')
                             ->orWhere('kode_mk', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('dosens.person', function ($subQ) {
                        $subQ->where('nama_lengkap', 'like', '%' . $this->search . '%');
                    });
                });
            })
            ->withCount(['krsDetails as peserta_count' => function ($query) {
                $query->whereHas('krs', function ($q) {
                    $q->where('status_krs', 'DISETUJUI');
                });
            }])
            ->orderBy('hari')
            ->orderBy('jam_mulai')
            ->paginate(12);

        return view('livewire.admin.akademik.cetak-absensi-manager', [
            'jadwals' => $jadwals,
            'prodis' => $prodis,
            'semesters' => $semesters
        ]);
    }
}