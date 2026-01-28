<?php

namespace App\Livewire\Admin\Keuangan;

use Livewire\Component;
use Livewire\WithPagination;
use App\Domains\Keuangan\Models\TagihanMahasiswa;
use App\Domains\Core\Models\Prodi;
use App\Helpers\SistemHelper;
use Illuminate\Support\Facades\DB;

class LaporanKeuangan extends Component
{
    use WithPagination;

    // Filter
    public $semesterId;
    public $filterProdiId;
    public $filterStatus; // LUNAS, BELUM, CICIL
    public $search = '';

    // Summary Cards
    public $totalTagihan = 0;
    public $totalTerbayar = 0;
    public $totalTunggakan = 0;
    public $countLunas = 0;
    public $countBelum = 0;

    public function mount()
    {
        $this->semesterId = SistemHelper::idTahunAktif();
    }

    public function updated($fields)
    {
        $this->resetPage(); // Reset pagination saat filter berubah
    }

    public function render()
    {
        // 1. Base Query
        $query = TagihanMahasiswa::with(['mahasiswa.prodi', 'mahasiswa.programKelas'])
            ->where('tahun_akademik_id', $this->semesterId);

        // 2. Apply Filters
        if ($this->filterProdiId) {
            $query->whereHas('mahasiswa', function ($q) {
                $q->where('prodi_id', $this->filterProdiId);
            });
        }

        if ($this->filterStatus) {
            if ($this->filterStatus == 'LUNAS') {
                $query->where('status_bayar', 'LUNAS');
            } elseif ($this->filterStatus == 'BELUM') {
                $query->whereIn('status_bayar', ['BELUM', 'CICIL']);
            }
        }

        if ($this->search) {
            $query->whereHas('mahasiswa', function ($q) {
                $q->where('nama_lengkap', 'like', '%' . $this->search . '%')
                  ->orWhere('nim', 'like', '%' . $this->search . '%');
            });
        }

        // 3. Hitung Statistik (Tanpa Pagination untuk Summary Card)
        // Clone query agar tidak merusak query utama pagination
        $statsQuery = clone $query;
        
        // Gunakan aggregate DB raw untuk performa
        $stats = $statsQuery->selectRaw('
            SUM(total_tagihan) as total_bill, 
            SUM(total_bayar) as total_paid,
            COUNT(CASE WHEN status_bayar = "LUNAS" THEN 1 END) as count_lunas,
            COUNT(*) as count_total
        ')->first();

        $this->totalTagihan = $stats->total_bill ?? 0;
        $this->totalTerbayar = $stats->total_paid ?? 0;
        $this->totalTunggakan = $this->totalTagihan - $this->totalTerbayar;
        $this->countLunas = $stats->count_lunas ?? 0;
        $this->countBelum = ($stats->count_total ?? 0) - $this->countLunas;

        // 4. Get Data Table
        $tagihans = $query->orderBy('created_at', 'desc')->paginate(20);
        $semesters = \App\Domains\Core\Models\TahunAkademik::orderBy('kode_tahun', 'desc')->get();
        $prodis = Prodi::all();

        return view('livewire.admin.keuangan.laporan-keuangan', [
            'tagihans' => $tagihans,
            'semesters' => $semesters,
            'prodis' => $prodis
        ]);
    }
}