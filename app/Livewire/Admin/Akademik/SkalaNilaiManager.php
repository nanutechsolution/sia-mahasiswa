<?php

namespace App\Livewire\Admin\Akademik;

use Livewire\Component;
use App\Domains\Akademik\Models\SkalaNilai;

class SkalaNilaiManager extends Component
{
    public $huruf, $bobot_indeks, $nilai_min, $nilai_max, $is_lulus = true;
    public $selectedId, $showForm = false;

    public function render()
    {
        return view('livewire.admin.akademik.skala-nilai-manager', [
            'skala' => SkalaNilai::orderBy('bobot_indeks', 'desc')->get()
        ]);
    }

    public function create() { $this->reset(); $this->showForm = true; }

    public function save()
    {
        $this->validate([
            'huruf' => 'required|max:2',
            'bobot_indeks' => 'required|numeric|min:0|max:4',
            'nilai_min' => 'required|numeric',
            'nilai_max' => 'required|numeric|gt:nilai_min',
        ]);

        SkalaNilai::updateOrCreate(['id' => $this->selectedId], [
            'huruf' => strtoupper($this->huruf),
            'bobot_indeks' => $this->bobot_indeks,
            'nilai_min' => $this->nilai_min,
            'nilai_max' => $this->nilai_max,
            'is_lulus' => $this->is_lulus,
        ]);

        session()->flash('success', 'Skala nilai berhasil disimpan.');
        $this->showForm = false;
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
        $this->showForm = true;
    }
}