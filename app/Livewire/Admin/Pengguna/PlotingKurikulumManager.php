<?php

namespace App\Livewire\Admin\Pengguna;

use Livewire\Component;
use Livewire\WithPagination;
use App\Domains\Mahasiswa\Models\Mahasiswa;
use App\Domains\Core\Models\Prodi;
use App\Domains\Akademik\Models\Kurikulum;
use Illuminate\Support\Facades\DB;

class PlotingKurikulumManager extends Component
{
    use WithPagination;

    // Filters
    public $search = '';
    public $filterProdiId = '';
    public $filterAngkatan = '';
    public $filterStatusKurikulum = ''; // 'all', 'null', 'has'

    // Bulk Selection
    public $selectedMahasiswa = [];
    public $selectAll = false;

    // Target Action
    public $targetKurikulumId = '';

    public function mount()
    {
        $this->filterAngkatan = date('Y');
        // Set default prodi jika ada
        $firstProdi = Prodi::first();
        if ($firstProdi) {
            $this->filterProdiId = $firstProdi->id;
        }
    }

    public function updated($propertyName)
    {
        // Reset paginasi dan seleksi jika filter berubah
        if (in_array($propertyName, ['search', 'filterProdiId', 'filterAngkatan', 'filterStatusKurikulum'])) {
            $this->resetPage();
            $this->resetSelection();
        }
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            // Ambil ID mahasiswa di halaman ini saja
            $this->selectedMahasiswa = $this->getMahasiswaQuery()->pluck('id')->map(fn($id) => (string) $id)->toArray();
        } else {
            $this->selectedMahasiswa = [];
        }
    }

    public function resetSelection()
    {
        $this->selectedMahasiswa = [];
        $this->selectAll = false;
        $this->targetKurikulumId = '';
    }

    private function getMahasiswaQuery()
    {
        return Mahasiswa::with(['person', 'prodi', 'kurikulum'])
            ->when($this->filterProdiId, fn($q) => $q->where('prodi_id', $this->filterProdiId))
            ->when($this->filterAngkatan, fn($q) => $q->where('angkatan_id', $this->filterAngkatan))
            ->when($this->filterStatusKurikulum === 'null', fn($q) => $q->whereNull('kurikulum_id'))
            ->when($this->filterStatusKurikulum === 'has', fn($q) => $q->whereNotNull('kurikulum_id'))
            ->when($this->search, function ($q) {
                $q->where(function($query) {
                    $query->where('nim', 'like', '%' . $this->search . '%')
                          ->orWhereHas('person', fn($p) => $p->where('nama_lengkap', 'like', '%' . $this->search . '%'));
                });
            })
            ->orderBy('nim', 'asc');
    }

    public function terapkanKurikulum()
    {
        // Validasi input
        $this->validate([
            'targetKurikulumId' => 'required',
            'selectedMahasiswa' => 'required|array|min:1'
        ], [
            'targetKurikulumId.required' => 'Pilih Kurikulum Tujuan terlebih dahulu.',
            'selectedMahasiswa.required' => 'Pilih minimal 1 mahasiswa untuk di-ploting.'
        ]);

        try {
            DB::transaction(function () {
                Mahasiswa::whereIn('id', $this->selectedMahasiswa)
                    ->update(['kurikulum_id' => $this->targetKurikulumId]);
            });

            $count = count($this->selectedMahasiswa);
            
            $this->dispatch('swal:success', [
                'title' => 'Ploting Berhasil!',
                'text'  => "Sebanyak $count mahasiswa berhasil dipetakan ke kurikulum baru."
            ]);

            $this->resetSelection();
            
        } catch (\Exception $e) {
            $this->dispatch('swal:error', [
                'title' => 'Gagal!',
                'text'  => 'Terjadi kesalahan saat memproses data: ' . $e->getMessage()
            ]);
        }
    }

    public function render()
    {
        $prodis = Prodi::all();
        $angkatans = DB::table('ref_angkatan')->orderBy('id_tahun', 'desc')->get();
        
        // Pilihan target kurikulum difilter berdasarkan prodi yang sedang aktif dipilih
        $targetKurikulums = collect();
        if ($this->filterProdiId) {
            $targetKurikulums = Kurikulum::where('prodi_id', $this->filterProdiId)
                                         ->orderBy('tahun_mulai', 'desc')
                                         ->get();
        }

        $mahasiswas = $this->getMahasiswaQuery()->paginate(15);

        // Auto-uncheck 'select all' jika tidak semua item di halaman ini terpilih
        $currentPageIds = $mahasiswas->pluck('id')->map(fn($id) => (string) $id)->toArray();
        if (count(array_intersect($this->selectedMahasiswa, $currentPageIds)) !== count($currentPageIds)) {
            $this->selectAll = false;
        }

        return view('livewire.admin.pengguna.ploting-kurikulum-manager', [
            'prodis' => $prodis,
            'angkatans' => $angkatans,
            'targetKurikulums' => $targetKurikulums,
            'mahasiswas' => $mahasiswas,
            'totalSelected' => count($this->selectedMahasiswa)
        ]);
    }
}