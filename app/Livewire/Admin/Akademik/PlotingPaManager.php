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
        // Default prodi pertama biar langsung tampil data
        $this->filterProdiId = Prodi::first()->id ?? null;
    }

    // Reset pagination & selection saat filter berubah
    public function updatedFilterProdiId() { $this->resetPage(); $this->resetSelection(); }
    public function updatedFilterAngkatan() { $this->resetPage(); $this->resetSelection(); }
    public function updatedFilterStatusPa() { $this->resetPage(); $this->resetSelection(); }
    public function updatedSearch() { $this->resetPage(); $this->resetSelection(); }

    public function resetSelection()
    {
        $this->selectedMhs = [];
        $this->selectAll = false;
    }

    // Logic Select All (Hanya halaman ini atau semua query? Kita buat per halaman dulu biar aman/ringan)
    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedMhs = $this->getMahasiswaQuery()->pluck('id')->map(fn($id) => (string) $id)->toArray();
        } else {
            $this->selectedMhs = [];
        }
    }

    private function getMahasiswaQuery()
    {
        return Mahasiswa::query()
            ->where('prodi_id', $this->filterProdiId)
            ->where('angkatan_id', $this->filterAngkatan)
            ->when($this->search, function($q) {
                // Fix Search: Cari Nama via relasi Person
                $q->whereHas('person', function($qp) {
                    $qp->where('nama_lengkap', 'like', '%'.$this->search.'%');
                })
                ->orWhere('nim', 'like', '%'.$this->search.'%');
            })
            ->when($this->filterStatusPa == 'belum', function($q) {
                $q->whereNull('dosen_wali_id');
            })
            ->when($this->filterStatusPa == 'sudah', function($q) {
                $q->whereNotNull('dosen_wali_id');
            });
    }

    public function simpanPloting()
    {
        $this->validate([
            'targetDosenId' => 'required',
            'selectedMhs' => 'required|array|min:1',
        ], [
            'targetDosenId.required' => 'Pilih Dosen Wali terlebih dahulu.',
            'selectedMhs.required' => 'Pilih minimal satu mahasiswa.',
        ]);

        $count = count($this->selectedMhs);
        $dosen = Dosen::find($this->targetDosenId);

        // Bulk Update
        Mahasiswa::whereIn('id', $this->selectedMhs)
            ->update(['dosen_wali_id' => $this->targetDosenId]);

        session()->flash('success', "Berhasil memploting $count mahasiswa ke Dosen Wali: {$dosen->nama_lengkap_gelar}.");
        
        $this->resetSelection();
    }

    public function render()
    {
        $prodis = Prodi::all();
        $angkatans = DB::table('ref_angkatan')->orderBy('id_tahun', 'desc')->get();
        
        // [FIX] Ambil Dosen dengan Join ke Person untuk sorting nama
        // Agar tidak error "column nama_lengkap_gelar not found"
        $dosens = Dosen::join('ref_person', 'trx_dosen.person_id', '=', 'ref_person.id')
            ->where('trx_dosen.is_active', true)
            ->orderBy('ref_person.nama_lengkap', 'asc')
            ->select('trx_dosen.*') // Ambil kolom dosen saja agar model hydration benar
            ->get();

        $mahasiswas = $this->getMahasiswaQuery()
            ->with(['dosenWali.person', 'programKelas', 'person']) // Load person
            ->orderBy('nim', 'asc')
            ->paginate(50); // Tampilkan banyak biar enak check-nya

        return view('livewire.admin.akademik.ploting-pa-manager', [
            'mahasiswas' => $mahasiswas,
            'prodis' => $prodis,
            'angkatans' => $angkatans,
            'dosens' => $dosens
        ]);
    }
}