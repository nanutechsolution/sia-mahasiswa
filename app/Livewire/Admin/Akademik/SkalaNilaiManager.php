<?php

namespace App\Livewire\Admin\Akademik;

use Livewire\Component;
use App\Domains\Akademik\Models\SkalaNilai;

class SkalaNilaiManager extends Component
{
    public $huruf, $bobot_indeks, $nilai_min, $nilai_max, $is_lulus = true;
    public $selectedId, $showForm = false;

    // Custom Error Messages Bahasa Indonesia
    protected $messages = [
        'huruf.required' => 'Huruf Mutu wajib diisi (Contoh: A, B+).',
        'huruf.max' => 'Huruf Mutu maksimal 2 karakter.',
        'bobot_indeks.required' => 'Bobot Indeks wajib diisi (0.00 - 4.00).',
        'bobot_indeks.numeric' => 'Bobot harus berupa angka.',
        'nilai_min.required' => 'Batas Bawah Nilai Angka wajib diisi.',
        'nilai_max.required' => 'Batas Atas Nilai Angka wajib diisi.',
        'nilai_max.gt' => 'Nilai Batas Atas harus lebih besar dari Batas Bawah.',
    ];

    public function render()
    {
        return view('livewire.admin.akademik.skala-nilai-manager', [
            'skala' => SkalaNilai::orderBy('bobot_indeks', 'desc')->get()
        ]);
    }

    public function create() 
    { 
        $this->reset(); 
        $this->showForm = true; 
        $this->is_lulus = true; // Default Lulus
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

    public function delete($id)
    {
        SkalaNilai::find($id)->delete();
        session()->flash('success', 'Skala nilai berhasil dihapus.');
    }
}