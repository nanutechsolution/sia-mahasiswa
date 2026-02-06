<?php

namespace App\Livewire\Admin\Akademik;

use Livewire\Component;
use Livewire\WithPagination;
use App\Domains\Mahasiswa\Models\Mahasiswa;
use App\Domains\Akademik\Models\Dosen;
use App\Domains\Core\Models\Prodi;
use Illuminate\Support\Facades\DB;

class PlotingPaManager extends Component
{
    use WithPagination;

    // Filters
    public $filterProdiId;
    public $filterAngkatan;
    public $filterStatusPa = 'all'; // all, belum, sudah
    public $search = '';

    // Action State
    public $selectedMhs = []; // Array ID mahasiswa yang dicentang
    public $targetDosenId;    // ID Dosen yang akan dipilih
    public $selectAll = false; // Toggle Select All

    public function mount()
    {
        $this->filterAngkatan = date('Y');
        $this->filterProdiId = Prodi::first()->id ?? null;
    }

    // Reset pagination & selection saat filter berubah
    public function updatedFilterProdiId() { $this->resetPage(); $this->resetSelection(); }
    public function updatedFilterAngkatan() { $this->resetPage(); $this->resetSelection(); }
    public function updatedFilterStatusPa() { $this->resetPage(); $this->resetSelection(); }
    public function updatedSearch() { $this->resetPage(); $this->resetSelection(); }
    
    // Handle Select All di Halaman Ini
    public function updatedSelectAll($value)
    {
        if ($value) {
            // Hanya ambil ID dari halaman saat ini agar tidak berat
            $ids = $this->getMahasiswaQuery()
                ->orderBy('nim', 'asc')
                ->paginate(50)
                ->pluck('id')
                ->map(fn($id) => (string) $id)
                ->toArray();
            
            $this->selectedMhs = $ids;
        } else {
            $this->selectedMhs = [];
        }
    }

    public function resetSelection()
    {
        $this->selectedMhs = [];
        $this->selectAll = false;
    }

    private function getMahasiswaQuery()
    {
        return Mahasiswa::query()
            ->with(['person', 'programKelas', 'dosenWali.person']) // Eager load relations
            ->where('prodi_id', $this->filterProdiId)
            ->where('angkatan_id', $this->filterAngkatan)
            ->when($this->search, function($q) {
                $q->where(function($sub) {
                    $sub->whereHas('person', fn($p) => $p->where('nama_lengkap', 'like', '%'.$this->search.'%'))
                        ->orWhere('nim', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->filterStatusPa == 'belum', fn($q) => $q->whereNull('dosen_wali_id'))
            ->when($this->filterStatusPa == 'sudah', fn($q) => $q->whereNotNull('dosen_wali_id'));
    }

    public function simpanPloting()
    {
        // Validasi Ketat
        $this->validate([
            'targetDosenId' => 'required|exists:trx_dosen,id',
            'selectedMhs' => 'required|array|min:1',
            'selectedMhs.*' => 'exists:mahasiswas,id' // Pastikan setiap ID valid
        ], [
            'targetDosenId.required' => 'Silakan pilih Dosen Wali terlebih dahulu.',
            'targetDosenId.exists' => 'Dosen yang dipilih tidak valid.',
            'selectedMhs.required' => 'Pilih minimal satu mahasiswa dari tabel.',
        ]);

        $count = count($this->selectedMhs);
        $dosen = Dosen::with('person')->find($this->targetDosenId);
        $namaDosen = $dosen->person->nama_dengan_gelar ?? $dosen->person->nama_lengkap;

        DB::transaction(function () {
            Mahasiswa::whereIn('id', $this->selectedMhs)
                ->update(['dosen_wali_id' => $this->targetDosenId]);
        });

        session()->flash('success', "Berhasil! $count Mahasiswa telah dipindahkan ke Dosen Wali: $namaDosen.");
        
        $this->resetSelection();
    }

    public function render()
    {
        $prodis = Prodi::all();
        $angkatans = DB::table('ref_angkatan')->orderBy('id_tahun', 'desc')->get();
        
        // Ambil data dosen untuk dropdown (Searchable)
        $dosens = Dosen::with('person')
            ->where('is_active', true)
            ->get()
            ->sortBy(fn($d) => $d->person->nama_lengkap ?? '')
            ->values();

        $mahasiswas = $this->getMahasiswaQuery()
            ->orderBy('nim', 'asc')
            ->paginate(50);

        return view('livewire.admin.akademik.ploting-pa-manager', [
            'mahasiswas' => $mahasiswas,
            'prodis' => $prodis,
            'angkatans' => $angkatans,
            'dosens' => $dosens
        ]);
    }
}