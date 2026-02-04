<?php

namespace App\Livewire\Admin\Lpm;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class IndikatorManager extends Component
{
    use WithPagination;

    public $search = '';
    public $showForm = false;
    public $editMode = false;

    // Form State
    public $indikatorId;
    public $standar_id;
    public $nama_indikator;
    public $bobot = 0;
    public $sumber_data_siakad;

    protected $rules = [
        'standar_id' => 'required|exists:lpm_standars,id',
        'nama_indikator' => 'required|string|max:255',
        'bobot' => 'required|numeric|min:0|max:100',
        'sumber_data_siakad' => 'nullable|string|max:100',
    ];

    public function render()
    {
        $indikators = DB::table('lpm_indikators as i')
            ->join('lpm_standars as s', 'i.standar_id', '=', 's.id')
            ->select('i.*', 's.nama_standar', 's.kode_standar')
            ->where('i.nama_indikator', 'like', '%' . $this->search . '%')
            ->orderBy('s.kode_standar')
            ->orderBy('i.id')
            ->paginate(10);

        $standars = DB::table('lpm_standars')->orderBy('kode_standar')->get();

        return view('livewire.admin.lpm.indikator-manager', [
            'indikators' => $indikators,
            'standars' => $standars
        ]);
    }

    public function create()
    {
        $this->resetForm();
        $this->showForm = true;
        $this->editMode = false;
    }

    public function edit($id)
    {
        $data = DB::table('lpm_indikators')->where('id', $id)->first();
        if (!$data) return;

        $this->indikatorId = $id;
        $this->standar_id = $data->standar_id;
        $this->nama_indikator = $data->nama_indikator;
        $this->bobot = $data->bobot;
        $this->sumber_data_siakad = $data->sumber_data_siakad;

        $this->editMode = true;
        $this->showForm = true;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'standar_id' => $this->standar_id,
            'nama_indikator' => $this->nama_indikator,
            'slug' => Str::slug($this->nama_indikator),
            'bobot' => $this->bobot,
            'sumber_data_siakad' => $this->sumber_data_siakad,
            'updated_at' => now(),
        ];

        if ($this->editMode) {
            DB::table('lpm_indikators')->where('id', $this->indikatorId)->update($data);
            session()->flash('success', 'Indikator berhasil diperbarui.');
        } else {
            $data['created_at'] = now();
            DB::table('lpm_indikators')->insert($data);
            session()->flash('success', 'Indikator baru berhasil ditambahkan.');
        }

        $this->showForm = false;
        $this->resetForm();
    }

    public function delete($id)
    {
        // Cek apakah sudah digunakan di target IKU
        $isUsed = DB::table('lpm_iku_targets')->where('indikator_id', $id)->exists();
        if ($isUsed) {
            session()->flash('error', 'Gagal hapus: Indikator sudah memiliki data target capaian.');
            return;
        }

        DB::table('lpm_indikators')->where('id', $id)->delete();
        session()->flash('success', 'Indikator berhasil dihapus.');
    }

    public function resetForm()
    {
        $this->reset(['indikatorId', 'standar_id', 'nama_indikator', 'bobot', 'sumber_data_siakad', 'editMode']);
    }

    public function batal()
    {
        $this->showForm = false;
        $this->resetForm();
    }
}