<?php

namespace App\Livewire\Admin\Keuangan;

use Livewire\Component;
use Livewire\WithPagination;
use App\Domains\Keuangan\Models\KomponenBiaya;

class KomponenBiayaManager extends Component
{
    use WithPagination;

    public $search = '';
    
    // Form State
    public $komponenId;
    public $nama_komponen;
    public $tipe_biaya = 'TETAP'; // Default
    
    public $showForm = false;
    public $editMode = false;

    public function render()
    {
        $data = KomponenBiaya::where('nama_komponen', 'like', '%'.$this->search.'%')
            ->orderBy('id', 'asc')
            ->paginate(10);

        return view('livewire.admin.keuangan.komponen-biaya-manager', ['komponen' => $data]);
    }

    public function create()
    {
        $this->reset(['nama_komponen', 'tipe_biaya', 'komponenId']);
        $this->showForm = true;
        $this->editMode = false;
    }

    public function edit($id)
    {
        $k = KomponenBiaya::find($id);
        $this->komponenId = $id;
        $this->nama_komponen = $k->nama_komponen;
        $this->tipe_biaya = $k->tipe_biaya;
        
        $this->editMode = true;
        $this->showForm = true;
    }

    public function save()
    {
        $this->validate([
            'nama_komponen' => 'required|string|max:100',
            'tipe_biaya' => 'required|in:TETAP,SKS,SEKALI,INSIDENTAL',
        ]);

        $data = [
            'nama_komponen' => $this->nama_komponen,
            'tipe_biaya' => $this->tipe_biaya,
        ];

        if ($this->editMode) {
            KomponenBiaya::find($this->komponenId)->update($data);
        } else {
            KomponenBiaya::create($data);
        }

        $this->showForm = false;
        session()->flash('success', 'Komponen Biaya berhasil disimpan.');
    }

    public function delete($id)
    {
        try {
            KomponenBiaya::destroy($id);
            session()->flash('success', 'Komponen dihapus.');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal hapus. Komponen ini sedang dipakai di Skema Tarif/Tagihan.');
        }
    }
}