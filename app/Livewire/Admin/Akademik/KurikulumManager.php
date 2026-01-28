<?php

namespace App\Livewire\Admin\Akademik;

use Livewire\Component;
use App\Domains\Akademik\Models\Kurikulum;
use App\Domains\Akademik\Models\MataKuliah;
use App\Domains\Core\Models\Prodi;
use Illuminate\Support\Facades\DB;

class KurikulumManager extends Component
{
    // Mode: 'list', 'detail'
    public $viewMode = 'list';
    
    // State Header
    public $kurikulumId;
    public $prodi_id;
    public $nama_kurikulum;
    public $tahun_mulai;
    public $is_active = true;

    // State Detail (Isi MK)
    public $selectedKurikulum;
    public $mk_id_to_add;
    public $semester_paket_to_add = 1;
    public $sifat_mk_to_add = 'W'; // W=Wajib, P=Pilihan

    public function render()
    {
        if ($this->viewMode == 'detail') {
            return $this->renderDetail();
        }
        return $this->renderList();
    }

    public function renderList()
    {
        $kurikulums = Kurikulum::with('prodi')
            ->orderBy('tahun_mulai', 'desc')
            ->get();
            
        $prodis = Prodi::all();

        return view('livewire.admin.akademik.kurikulum-manager', [
            'kurikulums' => $kurikulums,
            'prodis' => $prodis
        ]);
    }

    public function renderDetail()
    {
        // Load MK yang belum masuk ke kurikulum ini (untuk dropdown)
        $existingMkIds = $this->selectedKurikulum->mataKuliahs->pluck('id')->toArray();
        
        $availableMks = MataKuliah::where('prodi_id', $this->selectedKurikulum->prodi_id)
            ->whereNotIn('id', $existingMkIds)
            ->orderBy('nama_mk')
            ->get();

        return view('livewire.admin.akademik.kurikulum-detail', [
            'availableMks' => $availableMks
        ]);
    }

    // --- Actions Header ---

    public function saveHeader()
    {
        $this->validate([
            'nama_kurikulum' => 'required',
            'prodi_id' => 'required',
            'tahun_mulai' => 'required|digits:4',
        ]);

        Kurikulum::create([
            'nama_kurikulum' => $this->nama_kurikulum,
            'prodi_id' => $this->prodi_id,
            'tahun_mulai' => $this->tahun_mulai,
            'is_active' => $this->is_active
        ]);

        $this->reset(['nama_kurikulum', 'tahun_mulai']);
        session()->flash('success', 'Kurikulum berhasil dibuat.');
    }

    public function manage($id)
    {
        $this->selectedKurikulum = Kurikulum::with('mataKuliahs.prodi')->find($id);
        $this->viewMode = 'detail';
    }

    public function backToList()
    {
        $this->viewMode = 'list';
        $this->selectedKurikulum = null;
    }

    // --- Actions Detail (Structure) ---

    public function addMk()
    {
        $this->validate([
            'mk_id_to_add' => 'required',
            'semester_paket_to_add' => 'required|integer|min:1|max:8',
        ]);

        $mk = MataKuliah::find($this->mk_id_to_add);

        // Attach ke pivot table
        $this->selectedKurikulum->mataKuliahs()->attach($mk->id, [
            'semester_paket' => $this->semester_paket_to_add,
            'sks_tatap_muka' => $mk->sks_tatap_muka, // Copy default dari master MK
            'sks_praktek' => $mk->sks_praktek,
            'sks_lapangan' => $mk->sks_lapangan,
            'sifat_mk' => $this->sifat_mk_to_add
        ]);

        session()->flash('success', 'MK berhasil ditambahkan ke kurikulum.');
        $this->selectedKurikulum->refresh(); // Reload relation
    }

    public function removeMk($mkId)
    {
        $this->selectedKurikulum->mataKuliahs()->detach($mkId);
        session()->flash('success', 'MK dihapus dari kurikulum.');
        $this->selectedKurikulum->refresh();
    }
    
    public function toggleActive($id) {
        $k = Kurikulum::find($id);
        $k->update(['is_active' => !$k->is_active]);
    }
}