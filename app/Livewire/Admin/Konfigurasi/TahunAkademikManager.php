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
    public $editId;
    public $kode_tahun;
    public $nama_tahun;
    public $semester_tipe = 1;
    public $tanggal_mulai;
    public $tanggal_selesai;
    public $tgl_mulai_krs;
    public $tgl_selesai_krs;
    public $buka_input_nilai = 0;

    public $showForm = false;
    public $editMode = false;

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

        // Format tanggal untuk input HTML
        $this->tanggal_mulai = $ta->tanggal_mulai ? $ta->tanggal_mulai->format('Y-m-d') : null;
        $this->tanggal_selesai = $ta->tanggal_selesai ? $ta->tanggal_selesai->format('Y-m-d') : null;
        $this->tgl_mulai_krs = $ta->tgl_mulai_krs ? $ta->tgl_mulai_krs->format('Y-m-d') : null;
        $this->tgl_selesai_krs = $ta->tgl_selesai_krs ? $ta->tgl_selesai_krs->format('Y-m-d') : null;

        $this->buka_input_nilai = $ta->buka_input_nilai;

        $this->editMode = true;
        $this->showForm = true;
    }

    public function save()
    {
        $rules = [
            'kode_tahun' => 'required|unique:ref_tahun_akademik,kode_tahun,' . $this->editId,
            'nama_tahun' => 'required',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'tgl_mulai_krs' => 'nullable|date',
            'tgl_selesai_krs' => 'nullable|date|after_or_equal:tgl_mulai_krs',
        ];

        $this->validate($rules);

        $data = [
            'kode_tahun' => $this->kode_tahun,
            'nama_tahun' => $this->nama_tahun,
            'semester' => $this->semester_tipe,
            'tanggal_mulai' => $this->tanggal_mulai,
            'tanggal_selesai' => $this->tanggal_selesai,
            'tgl_mulai_krs' => $this->tgl_mulai_krs,
            'tgl_selesai_krs' => $this->tgl_selesai_krs,
            'buka_input_nilai' => $this->buka_input_nilai,
        ];

        if ($this->editMode) {
            TahunAkademik::find($this->editId)->update($data);
            session()->flash('success', 'Tahun Akademik berhasil diperbarui.');
        } else {
            TahunAkademik::create($data);
            session()->flash('success', 'Tahun Akademik baru berhasil ditambahkan.');
        }

        Cache::forget('ta_aktif_id');
        Cache::forget('ta_aktif_obj');

        $this->batal();
        $this->loadData();
    }

    public function toggleInputNilai($id)
    {
        $ta = TahunAkademik::find($id);
        $ta->update(['buka_input_nilai' => !$ta->buka_input_nilai]);
        $this->loadData();
        session()->flash('success', 'Status input nilai berhasil diubah.');
    }

    public function toggleKrs($id)
    {
        $ta = TahunAkademik::findOrFail($id);

        // Jika sekarang buka KRS, berarti mau ditutup
        if ($ta->buka_krs) {
            $ta->update([
                'buka_krs' => false,
                'tgl_selesai_krs' => now(), // set otomatis ke waktu sekarang
            ]);
        } else {
            // Jika KRS ditutup sebelumnya, buka lagi (tanggal tetap seperti semula)
            $ta->update([
                'buka_krs' => true,
                // bisa juga reset tgl_mulai_krs jika mau
            ]);
        }

        $this->loadData();
        session()->flash('success', 'Status masa KRS berhasil diubah.');
    }


    public function aktifkanSemester($id)
    {
        DB::transaction(function () use ($id) {
            TahunAkademik::query()->update(['is_active' => false]);
            TahunAkademik::where('id', $id)->update(['is_active' => true]);
        });

        Cache::forget('ta_aktif_id');
        Cache::forget('ta_aktif_obj');

        session()->flash('success', 'Semester aktif berhasil diubah.');
        $this->loadData();
    }

    public function batal()
    {
        $this->reset(['kode_tahun', 'nama_tahun', 'semester_tipe', 'tanggal_mulai', 'tanggal_selesai', 'tgl_mulai_krs', 'tgl_selesai_krs', 'buka_input_nilai', 'showForm', 'editMode', 'editId']);
    }

    public function render()
    {
        return view('livewire.admin.konfigurasi.tahun-akademik-manager');
    }
}
