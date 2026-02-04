<?php

namespace App\Livewire\Admin\Lpm;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class StandarManager extends Component
{
    use WithPagination;

    public $search = '';
    public $showForm = false;
    public $editMode = false;

    // Form Fields
    public $standarId, $kode_standar, $nama_standar, $kategori = 'AKADEMIK', $pernyataan_standar, $target_pencapaian = 100, $satuan = '%';

    public function render()
    {
        $standars = DB::table('lpm_standars')
            ->where('nama_standar', 'like', '%'.$this->search.'%')
            ->orWhere('kode_standar', 'like', '%'.$this->search.'%')
            ->paginate(10);

        return view('livewire.admin.lpm.standar-manager', ['standars' => $standars]);
    }

    public function create()
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit($id)
    {
        $data = DB::table('lpm_standars')->where('id', $id)->first();
        $this->standarId = $id;
        $this->kode_standar = $data->kode_standar;
        $this->nama_standar = $data->nama_standar;
        $this->kategori = $data->kategori;
        $this->pernyataan_standar = $data->pernyataan_standar;
        $this->target_pencapaian = $data->target_pencapaian;
        $this->satuan = $data->satuan;
        
        $this->editMode = true;
        $this->showForm = true;
    }

    public function save()
    {
        $this->validate([
            'kode_standar' => 'required|unique:lpm_standars,kode_standar,'.$this->standarId,
            'nama_standar' => 'required',
            'pernyataan_standar' => 'required',
        ]);

        $data = [
            'kode_standar' => strtoupper($this->kode_standar),
            'nama_standar' => $this->nama_standar,
            'kategori' => $this->kategori,
            'pernyataan_standar' => $this->pernyataan_standar,
            'target_pencapaian' => $this->target_pencapaian,
            'satuan' => $this->satuan,
            'updated_at' => now()
        ];

        if ($this->editMode) {
            DB::table('lpm_standars')->where('id', $this->standarId)->update($data);
        } else {
            $data['created_at'] = now();
            DB::table('lpm_standars')->insert($data);
        }

        session()->flash('success', 'Standar mutu berhasil disimpan.');
        $this->showForm = false;
        $this->resetForm();
    }

    public function delete($id)
    {
        DB::table('lpm_standars')->where('id', $id)->delete();
        session()->flash('success', 'Standar berhasil dihapus.');
    }

    public function resetForm()
    {
        $this->reset(['standarId', 'kode_standar', 'nama_standar', 'kategori', 'pernyataan_standar', 'target_pencapaian', 'satuan', 'editMode']);
    }
}