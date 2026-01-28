<?php

namespace App\Livewire\Admin\Akademik;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Domains\Akademik\Models\MataKuliah;
use App\Domains\Core\Models\Prodi;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MataKuliahManager extends Component
{
    use WithPagination, WithFileUploads;

    // Filter
    public $search = '';
    public $filterProdiId;

    // Form State
    public $mkId;
    public $kode_mk;
    public $nama_mk;
    
    // SKS Details
    public $sks_default = 3;
    public $sks_tatap_muka = 0;
    public $sks_praktek = 0;
    public $sks_lapangan = 0;

    public $jenis_mk = 'A'; 
    public $prodi_id;

    public $showForm = false;
    public $editMode = false;

    // Import State
    public $fileImport;
    public $showImportModal = false;

    public function mount()
    {
        $this->filterProdiId = Prodi::first()->id ?? null;
        $this->prodi_id = $this->filterProdiId;
    }

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['sks_tatap_muka', 'sks_praktek', 'sks_lapangan'])) {
            $this->sks_default = (int)$this->sks_tatap_muka + (int)$this->sks_praktek + (int)$this->sks_lapangan;
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $prodis = Prodi::all();

        $mks = MataKuliah::with('prodi')
            ->where('prodi_id', $this->filterProdiId)
            ->where(function($q) {
                $q->where('nama_mk', 'like', '%'.$this->search.'%')
                  ->orWhere('kode_mk', 'like', '%'.$this->search.'%');
            })
            ->orderBy('kode_mk', 'asc')
            ->paginate(10);

        return view('livewire.admin.akademik.mata-kuliah-manager', [
            'mks' => $mks,
            'prodis' => $prodis
        ]);
    }

    public function create()
    {
        $this->resetForm();
        $this->prodi_id = $this->filterProdiId; 
        $this->showForm = true;
        $this->editMode = false;
    }

    public function openImport()
    {
        $this->reset(['fileImport']);
        $this->showImportModal = true;
    }

    public function downloadTemplate()
    {
        $response = new StreamedResponse(function(){
            $handle = fopen('php://output', 'w');
            
            // Header CSV
            fputcsv($handle, ['Kode MK', 'Nama MK', 'Total SKS', 'SKS Teori', 'SKS Praktek', 'Jenis(A/B/C)']);
            
            // Contoh Data 1
            fputcsv($handle, ['TI101', 'Algoritma Pemrograman', '3', '2', '1', 'A']);
            
            // Contoh Data 2
            fputcsv($handle, ['TI102', 'Basis Data', '3', '3', '0', 'A']);

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="template_matakuliah.csv"');

        return $response;
    }

    public function processImport()
    {
        $this->validate([
            'fileImport' => 'required|mimes:csv,txt|max:2048', // Max 2MB, format CSV
            'filterProdiId' => 'required'
        ]);

        $path = $this->fileImport->getRealPath();
        $file = fopen($path, 'r');
        
        // Skip Header Row
        fgetcsv($file); 
        
        $count = 0;
        
        DB::beginTransaction();
        try {
            while (($row = fgetcsv($file)) !== false) {
                // Struktur CSV: Kode, Nama, SKS Total, Teori, Praktek, Jenis(A/B/C)
                // Contoh: TI101, Algoritma, 3, 2, 1, A
                
                if (count($row) < 3) continue; // Skip baris tidak lengkap

                $kode = trim($row[0]);
                $nama = trim($row[1]);
                $sksTotal = (int) $row[2];
                
                // Optional columns
                $teori = isset($row[3]) && $row[3] !== '' ? (int)$row[3] : $sksTotal; // Default teori = total
                $praktek = isset($row[4]) && $row[4] !== '' ? (int)$row[4] : 0;
                $jenis = isset($row[5]) ? strtoupper(trim($row[5])) : 'A';

                MataKuliah::updateOrCreate(
                    [
                        'kode_mk' => $kode,
                        'prodi_id' => $this->filterProdiId // Import masuk ke prodi yang sedang difilter
                    ],
                    [
                        'nama_mk' => $nama,
                        'sks_default' => $sksTotal,
                        'sks_tatap_muka' => $teori,
                        'sks_praktek' => $praktek,
                        'sks_lapangan' => 0,
                        'jenis_mk' => in_array($jenis, ['A','B','C','D']) ? $jenis : 'A'
                    ]
                );
                $count++;
            }
            DB::commit();
            session()->flash('success', "Berhasil import $count Mata Kuliah.");
            $this->showImportModal = false;
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Gagal import: ' . $e->getMessage());
        }
        
        fclose($file);
    }

    public function edit($id)
    {
        $mk = MataKuliah::find($id);
        $this->mkId = $id;
        $this->kode_mk = $mk->kode_mk;
        $this->nama_mk = $mk->nama_mk;
        
        $this->sks_default = $mk->sks_default;
        $this->sks_tatap_muka = $mk->sks_tatap_muka;
        $this->sks_praktek = $mk->sks_praktek;
        $this->sks_lapangan = $mk->sks_lapangan;

        $this->jenis_mk = $mk->jenis_mk;
        $this->prodi_id = $mk->prodi_id;

        $this->editMode = true;
        $this->showForm = true;
    }

    public function save()
    {
        $rules = [
            'nama_mk' => 'required',
            'sks_default' => 'required|integer|min:0',
            'sks_tatap_muka' => 'required|integer|min:0',
            'sks_praktek' => 'required|integer|min:0',
            'sks_lapangan' => 'required|integer|min:0',
            'jenis_mk' => 'required',
            'prodi_id' => 'required',
        ];

        if ($this->editMode) {
            $rules['kode_mk'] = 'required|unique:master_mata_kuliahs,kode_mk,' . $this->mkId . ',id,prodi_id,' . $this->prodi_id;
        } else {
            $rules['kode_mk'] = 'required|unique:master_mata_kuliahs,kode_mk,NULL,id,prodi_id,' . $this->prodi_id;
        }

        $this->validate($rules);

        $totalRincian = (int)$this->sks_tatap_muka + (int)$this->sks_praktek + (int)$this->sks_lapangan;
        if ($totalRincian != $this->sks_default) {
            $this->sks_default = $totalRincian;
        }

        $data = [
            'kode_mk' => $this->kode_mk,
            'nama_mk' => $this->nama_mk,
            'sks_default' => $this->sks_default,
            'sks_tatap_muka' => $this->sks_tatap_muka,
            'sks_praktek' => $this->sks_praktek,
            'sks_lapangan' => $this->sks_lapangan,
            'jenis_mk' => $this->jenis_mk,
            'prodi_id' => $this->prodi_id,
        ];

        if ($this->editMode) {
            MataKuliah::find($this->mkId)->update($data);
            session()->flash('success', 'Mata Kuliah berhasil diperbarui.');
        } else {
            MataKuliah::create($data);
            session()->flash('success', 'Mata Kuliah baru berhasil ditambahkan.');
        }

        $this->resetForm();
        $this->showForm = false;
    }

    public function delete($id)
    {
        try {
            MataKuliah::destroy($id);
            session()->flash('success', 'Mata Kuliah berhasil dihapus.');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal hapus. MK ini mungkin sudah dipakai di Kurikulum/Jadwal.');
        }
    }

    public function resetForm()
    {
        $this->reset([
            'mkId', 'kode_mk', 'nama_mk', 
            'sks_default', 'sks_tatap_muka', 'sks_praktek', 'sks_lapangan', 
            'jenis_mk', 'editMode'
        ]);
        $this->sks_default = 3;
        $this->sks_tatap_muka = 0;
        $this->sks_praktek = 0;
        $this->sks_lapangan = 0;
    }

    public function batal()
    {
        $this->showForm = false;
        $this->showImportModal = false;
        $this->resetForm();
    }
}