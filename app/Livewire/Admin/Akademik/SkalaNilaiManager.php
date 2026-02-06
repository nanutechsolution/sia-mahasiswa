<?php

namespace App\Livewire\Admin\Akademik;

use Livewire\Component;
use App\Domains\Akademik\Models\SkalaNilai;

class SkalaNilaiManager extends Component
{
    public $huruf, $bobot_indeks, $nilai_min, $nilai_max, $is_lulus = true;
    public $selectedId, $showForm = false;
    public $editMode = false;

    // Custom Error Messages
    protected $messages = [
        'huruf.required' => 'Wajib diisi (Contoh: A).',
        'huruf.max' => 'Maks 2 karakter.',
        'bobot_indeks.required' => 'Wajib diisi.',
        'nilai_min.required' => 'Wajib diisi.',
        'nilai_max.required' => 'Wajib diisi.',
        'nilai_max.gt' => 'Harus lebih besar dari Nilai Min.',
    ];

    public function render()
    {
        return view('livewire.admin.akademik.skala-nilai-manager', [
            'skala' => SkalaNilai::orderBy('bobot_indeks', 'desc')->get()
        ]);
    }

    public function create() 
    { 
        $this->resetInput();
        $this->showForm = true; 
        $this->editMode = false;
        $this->is_lulus = true; 
    }

    public function edit($id)
    {
        $data = SkalaNilai::find($id);
        $this->selectedId = $id;
        $this->huruf = $data->huruf;
        $this->bobot_indeks = $data->bobot_indeks;
        $this->nilai_min = $data->nilai_min;
        $this->nilai_max = $data->nilai_max;
        $this->is_lulus = $data->is_lulus;
        
        $this->editMode = true;
        $this->showForm = true;
    }

    public function save()
    {
        $this->validate([
            'huruf' => 'required|max:2',
            'bobot_indeks' => 'required|numeric|min:0|max:4',
            'nilai_min' => 'required|numeric|min:0|max:100',
            'nilai_max' => 'required|numeric|gt:nilai_min|max:100',
        ]);

        SkalaNilai::updateOrCreate(['id' => $this->selectedId], [
            'huruf' => strtoupper($this->huruf),
            'bobot_indeks' => $this->bobot_indeks,
            'nilai_min' => $this->nilai_min,
            'nilai_max' => $this->nilai_max,
            'is_lulus' => $this->is_lulus,
        ]);

        session()->flash('success', 'Data skala nilai berhasil disimpan.');
        $this->batal();
    }

    public function delete($id)
    {
        SkalaNilai::find($id)->delete();
        session()->flash('success', 'Skala nilai berhasil dihapus.');
    }

    public function batal()
    {
        $this->showForm = false;
        $this->resetInput();
    }

    private function resetInput()
    {
        $this->reset(['huruf', 'bobot_indeks', 'nilai_min', 'nilai_max', 'is_lulus', 'selectedId', 'editMode']);
        $this->resetErrorBag();
    }
}