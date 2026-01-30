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

    // Filter
    public $filterProdiId;
    public $filterSemesterId;
    public $search = '';

    public function mount()
    {
        $this->filterSemesterId = SistemHelper::idTahunAktif();
        $this->filterProdiId = Prodi::first()->id ?? null;
    }

    public function render()
    {
        $prodis = Prodi::all();
        $semesters = TahunAkademik::orderBy('kode_tahun', 'desc')->get();
        
        $jadwals = JadwalKuliah::with(['mataKuliah', 'dosen.person', 'programKelasAllow'])
            ->where('tahun_akademik_id', $this->filterSemesterId)
            ->when($this->filterProdiId, function($q) {
                $q->whereHas('mataKuliah', function($subQ) {
                    $subQ->where('prodi_id', $this->filterProdiId);
                });
            })
            ->when($this->search, function($q) {
                $q->whereHas('mataKuliah', function($subQ) {
                    $subQ->where('nama_mk', 'like', '%'.$this->search.'%')
                         ->orWhere('kode_mk', 'like', '%'.$this->search.'%');
                })
                ->orWhereHas('dosen.person', function($subQ) {
                    $subQ->where('nama_lengkap', 'like', '%'.$this->search.'%');
                });
            })
            // Hitung jumlah mahasiswa yang KRS-nya DISETUJUI
            ->withCount(['krsDetails as peserta_count' => function ($query) {
                $query->whereHas('krs', function ($q) {
                    $q->where('status_krs', 'DISETUJUI');
                });
            }])
            ->orderBy('id', 'desc') // Atau order by hari/jam
            ->paginate(10);

        return view('livewire.admin.akademik.cetak-absensi-manager', [
            'jadwals' => $jadwals,
            'prodis' => $prodis,
            'semesters' => $semesters
        ]);
    }
}