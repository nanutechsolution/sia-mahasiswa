<?php

namespace App\Livewire\Admin\Konfigurasi;

use Livewire\Component;
use App\Domains\Core\Models\TahunAkademik;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class TahunAkademikManager extends Component
{
    public $semesters;
    
    // Form State
    public $kode_tahun;
    public $nama_tahun;
    public $semester_tipe = 1;
    public $tanggal_mulai;   // Periode Semester
    public $tanggal_selesai; // Periode Semester
    public $tgl_mulai_krs;   // Periode KRS
    public $tgl_selesai_krs; // Periode KRS
    public $showForm = false;

    // State Edit
    public $editMode = false;
    public $editId;

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $this->semesters = TahunAkademik::orderBy('kode_tahun', 'desc')->get();
    }

    public function edit($id)
    {
        $ta = TahunAkademik::find($id);
        $this->editId = $id;
        $this->kode_tahun = $ta->kode_tahun;
        $this->nama_tahun = $ta->nama_tahun;
        $this->semester_tipe = $ta->semester;
        
        // Format tanggal untuk input date HTML
        $this->tanggal_mulai = $ta->tanggal_mulai ? $ta->tanggal_mulai->format('Y-m-d') : null;
        $this->tanggal_selesai = $ta->tanggal_selesai ? $ta->tanggal_selesai->format('Y-m-d') : null;
        $this->tgl_mulai_krs = $ta->tgl_mulai_krs ? $ta->tgl_mulai_krs->format('Y-m-d') : null;
        $this->tgl_selesai_krs = $ta->tgl_selesai_krs ? $ta->tgl_selesai_krs->format('Y-m-d') : null;
        
        $this->editMode = true;
        $this->showForm = true;
    }

    public function simpanUpdate()
    {
        $this->validate([
            'kode_tahun' => 'required|unique:ref_tahun_akademik,kode_tahun,' . $this->editId,
            'nama_tahun' => 'required',
            'semester_tipe' => 'required|integer',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'tgl_mulai_krs' => 'nullable|date',
            'tgl_selesai_krs' => 'nullable|date|after_or_equal:tgl_mulai_krs',
        ]);

        $ta = TahunAkademik::find($this->editId);
        $ta->update([
            'kode_tahun' => $this->kode_tahun,
            'nama_tahun' => $this->nama_tahun,
            'semester' => $this->semester_tipe,
            'tanggal_mulai' => $this->tanggal_mulai,
            'tanggal_selesai' => $this->tanggal_selesai,
            'tgl_mulai_krs' => $this->tgl_mulai_krs,
            'tgl_selesai_krs' => $this->tgl_selesai_krs,
        ]);

        $this->cleanup();
        session()->flash('success', 'Tahun Akademik berhasil diperbarui.');
    }

    public function simpanBaru()
    {
        $this->validate([
            'kode_tahun' => 'required|unique:ref_tahun_akademik,kode_tahun',
            'nama_tahun' => 'required',
            'semester_tipe' => 'required|integer',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'tgl_mulai_krs' => 'nullable|date',
            'tgl_selesai_krs' => 'nullable|date|after_or_equal:tgl_mulai_krs',
        ]);

        TahunAkademik::create([
            'kode_tahun' => $this->kode_tahun,
            'nama_tahun' => $this->nama_tahun,
            'semester' => $this->semester_tipe,
            'tanggal_mulai' => $this->tanggal_mulai,
            'tanggal_selesai' => $this->tanggal_selesai,
            'tgl_mulai_krs' => $this->tgl_mulai_krs,
            'tgl_selesai_krs' => $this->tgl_selesai_krs,
            'is_active' => false,
            'buka_krs' => false
        ]);

        $this->cleanup();
        session()->flash('success', 'Tahun Akademik baru berhasil ditambahkan.');
    }

    public function aktifkanSemester($id)
    {
        DB::transaction(function () use ($id) {
            TahunAkademik::query()->update(['is_active' => false]);
            TahunAkademik::where('id', $id)->update(['is_active' => true]);
            Cache::forget('ta_aktif_id');
            Cache::forget('ta_aktif_obj');
        });

        $this->loadData();
        session()->flash('success', 'Semester aktif berhasil diubah.');
    }

    public function toggleKrs($id)
    {
        $ta = TahunAkademik::find($id);
        $newState = !$ta->buka_krs;
        
        $updateData = ['buka_krs' => $newState];
        if ($newState && !$ta->tgl_mulai_krs) {
            $updateData['tgl_mulai_krs'] = now();
            $updateData['tgl_selesai_krs'] = now()->addWeeks(2);
        }

        $ta->update($updateData);
        Cache::forget('ta_aktif_obj');
        
        $this->loadData();
        session()->flash('success', "Masa KRS berhasil " . ($newState ? 'DIBUKA' : 'DITUTUP') . ".");
    }

    public function batal()
    {
        $this->reset(['kode_tahun', 'nama_tahun', 'semester_tipe', 'tanggal_mulai', 'tanggal_selesai', 'tgl_mulai_krs', 'tgl_selesai_krs', 'showForm', 'editMode', 'editId']);
        $this->semester_tipe = 1;
    }

    private function cleanup()
    {
        Cache::forget('ta_aktif_obj');
        $this->batal();
        $this->loadData();
    }

    public function render()
    {
        return view('livewire.admin.konfigurasi.tahun-akademik-manager');
    }
}