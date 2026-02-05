<?php

namespace App\Livewire\Admin\Akademik;

use Livewire\Component;
use Livewire\WithPagination;
use App\Domains\Akademik\Models\Ekuivalensi;
use App\Domains\Akademik\Models\MataKuliah;
use App\Domains\Core\Models\Prodi;
use Illuminate\Support\Facades\Auth;

class EkuivalensiManager extends Component
{
    use WithPagination;

    // Filter & Context
    public $filterProdiId;

    // Form Fields
    public $ekuivalensiId;
    public $mk_asal_id;
    public $mk_tujuan_id;
    public $nomor_sk;
    public $keterangan;

    // Searchable States (Mata Kuliah)
    public $searchAsal = '';
    public $searchTujuan = '';
    public $selectedAsalName = '';
    public $selectedTujuanName = '';

    // UI States
    public $showForm = false;
    public $editMode = false;

    public function mount()
    {
        // Default filter ke Prodi pertama
        $this->filterProdiId = Prodi::first()->id ?? null;
    }

    public function render()
    {
        // 1. Ambil List Ekuivalensi (Recognition Layer)
        $listEkuivalensi = Ekuivalensi::with(['mataKuliahAsal', 'mataKuliahTujuan'])
            ->where('prodi_id', $this->filterProdiId)
            ->latest()
            ->paginate(10);

        // 2. Opsi Mata Kuliah Asal (Searchable)
        $optionsAsal = [];
        if (strlen($this->searchAsal) >= 2) {
            $optionsAsal = MataKuliah::where('prodi_id', $this->filterProdiId)
                ->where(function($q) {
                    $q->where('nama_mk', 'like', "%{$this->searchAsal}%")
                      ->orWhere('kode_mk', 'like', "%{$this->searchAsal}%");
                })->take(5)->get();
        }

        // 3. Opsi Mata Kuliah Tujuan (Searchable)
        $optionsTujuan = [];
        if (strlen($this->searchTujuan) >= 2) {
            $optionsTujuan = MataKuliah::where('prodi_id', $this->filterProdiId)
                ->where(function($q) {
                    $q->where('nama_mk', 'like', "%{$this->searchTujuan}%")
                      ->orWhere('kode_mk', 'like', "%{$this->searchTujuan}%");
                })->take(5)->get();
        }

        return view('livewire.admin.akademik.ekuivalensi-manager', [
            'listEkuivalensi' => $listEkuivalensi,
            'prodis' => Prodi::all(),
            'optionsAsal' => $optionsAsal,
            'optionsTujuan' => $optionsTujuan
        ]);
    }

    public function selectAsal($id, $name, $code)
    {
        $this->mk_asal_id = $id;
        $this->selectedAsalName = "[$code] $name";
        $this->searchAsal = '';
    }

    public function selectTujuan($id, $name, $code)
    {
        $this->mk_tujuan_id = $id;
        $this->selectedTujuanName = "[$code] $name";
        $this->searchTujuan = '';
    }

    public function save()
    {
        $this->validate([
            'mk_asal_id' => 'required|different:mk_tujuan_id',
            'mk_tujuan_id' => 'required',
            'nomor_sk' => 'nullable|max:100',
        ], [
            'mk_asal_id.required' => 'Mata kuliah asal wajib dipilih.',
            'mk_asal_id.different' => 'MK Asal dan MK Tujuan tidak boleh sama.',
            'mk_tujuan_id.required' => 'Mata kuliah tujuan wajib dipilih.',
        ]);

        Ekuivalensi::updateOrCreate(
            ['id' => $this->ekuivalensiId],
            [
                'prodi_id' => $this->filterProdiId,
                'mk_asal_id' => $this->mk_asal_id,
                'mk_tujuan_id' => $this->mk_tujuan_id,
                'nomor_sk' => $this->nomor_sk,
                'keterangan' => $this->keterangan,
                'created_by' => Auth::id()
            ]
        );

        session()->flash('success', 'Kebijakan ekuivalensi berhasil disimpan.');
        $this->resetForm();
    }

    public function edit($id)
    {
        $data = Ekuivalensi::with(['mataKuliahAsal', 'mataKuliahTujuan'])->findOrFail($id);
        $this->ekuivalensiId = $id;
        $this->mk_asal_id = $data->mk_asal_id;
        $this->mk_tujuan_id = $data->mk_tujuan_id;
        $this->nomor_sk = $data->nomor_sk;
        $this->keterangan = $data->keterangan;

        $this->selectedAsalName = "[{$data->mataKuliahAsal->kode_mk}] {$data->mataKuliahAsal->nama_mk}";
        $this->selectedTujuanName = "[{$data->mataKuliahTujuan->kode_mk}] {$data->mataKuliahTujuan->nama_mk}";

        $this->editMode = true;
        $this->showForm = true;
    }

    public function delete($id)
    {
        Ekuivalensi::destroy($id);
        session()->flash('success', 'Data ekuivalensi telah dihapus.');
    }

    public function resetForm()
    {
        $this->reset([
            'ekuivalensiId', 'mk_asal_id', 'mk_tujuan_id', 'nomor_sk', 'keterangan',
            'searchAsal', 'searchTujuan', 'selectedAsalName', 'selectedTujuanName',
            'showForm', 'editMode'
        ]);
    }
}