<?php

namespace App\Livewire\Dosen;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Domains\Mahasiswa\Models\Mahasiswa;
use App\Domains\Akademik\Models\Krs;
use App\Domains\Core\Models\ProgramKelas;
use App\Helpers\SistemHelper;

class PerwalianManager extends Component
{
    use WithPagination;

    public $dosen;
    public $taAktifId;
    public $taAktifNama;

    // Filters
    public $search = '';
    public $filterStatus = 'all'; // all, AJUKAN, DISETUJUI, BELUM_ISI
    public $filterProgramId = '';

    // Stats
    public $stats = [
        'total' => 0,
        'pending' => 0,
        'approved' => 0
    ];

    public function mount()
    {
        $user = Auth::user();
        
        // SSOT: Ambil Dosen melalui Person
        if (!$user->person || !$user->person->dosen) {
            abort(403, 'Profil Dosen Anda tidak ditemukan dalam sistem SSOT.');
        }
        $this->dosen = $user->person->dosen;

        $ta = SistemHelper::getTahunAktif();
        $this->taAktifId = $ta->id ?? null;
        $this->taAktifNama = $ta->nama_tahun ?? 'Semester Tidak Aktif';
    }

    /**
     * Reset pagination saat filter berubah
     */
    public function updatedSearch() { $this->resetPage(); }
    public function updatedFilterStatus() { $this->resetPage(); }
    public function updatedFilterProgramId() { $this->resetPage(); }

    /**
     * Kalkulasi Statistik (Tanpa Filter Pagination)
     */
    private function calculateStats()
    {
        $allBimbingan = Mahasiswa::where('dosen_wali_id', $this->dosen->id)
            ->with(['krs' => fn($q) => $q->where('tahun_akademik_id', $this->taAktifId)])
            ->get();

        $this->stats['total'] = $allBimbingan->count();
        $this->stats['pending'] = 0;
        $this->stats['approved'] = 0;

        foreach ($allBimbingan as $mhs) {
            $krs = $mhs->krs->first();
            if ($krs) {
                if ($krs->status_krs === 'AJUKAN') $this->stats['pending']++;
                if ($krs->status_krs === 'DISETUJUI') $this->stats['approved']++;
            }
        }
    }

    public function render()
    {
        $this->calculateStats();

        // Query Utama dengan Filter
        $query = Mahasiswa::with(['programKelas', 'person', 'prodi', 'krs' => function($q) {
                $q->where('tahun_akademik_id', $this->taAktifId);
            }])
            ->where('dosen_wali_id', $this->dosen->id);

        // Filter: Nama / NIM
        if ($this->search) {
            $query->where(function($q) {
                $q->where('nim', 'like', '%' . $this->search . '%')
                  ->orWhereHas('person', fn($p) => $p->where('nama_lengkap', 'like', '%' . $this->search . '%'));
            });
        }

        // Filter: Program Kelas
        if ($this->filterProgramId) {
            $query->where('program_kelas_id', $this->filterProgramId);
        }

        // Filter: Status KRS (Menggunakan whereHas atau whereDoesntHave untuk 'BELUM_ISI')
        if ($this->filterStatus !== 'all') {
            if ($this->filterStatus === 'BELUM_ISI') {
                $query->whereDoesntHave('krs', fn($q) => $q->where('tahun_akademik_id', $this->taAktifId));
            } else {
                $query->whereHas('krs', function($q) {
                    $q->where('tahun_akademik_id', $this->taAktifId)
                      ->where('status_krs', $this->filterStatus);
                });
            }
        }

        $mahasiswas = $query->orderBy('nim', 'asc')->paginate(15);
        $programKelas = ProgramKelas::where('is_active', true)->get();

        return view('livewire.dosen.perwalian-manager', [
            'mahasiswas' => $mahasiswas,
            'programKelas' => $programKelas
        ]);
    }
}