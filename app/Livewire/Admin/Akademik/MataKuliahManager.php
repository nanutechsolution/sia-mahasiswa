<?php

namespace App\Livewire\Admin\Akademik;

use Livewire\Component;
use Livewire\WithPagination;
use App\Domains\Akademik\Models\MataKuliah;
use App\Domains\Core\Models\Prodi;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class MataKuliahManager extends Component
{
    use WithPagination;

    // --- State ---
    public $mkId;
    public $kode_mk;
    public $nama_mk;

    // SKS
    public $sks_default = 0;
    public $sks_tatap_muka = 0;
    public $sks_praktek = 0;
    public $sks_lapangan = 0;

    public $jenis_mk = 'B';
    public $prodi_id;
    public $activity_type = 'REGULAR'; // Default

    // UI
    public $showForm = false;
    public $editMode = false;
    public $search = '';
    public $filterProdiId;

    // --- Constants ---
    const TYPE_REGULAR = 'REGULAR';
    const TYPE_THESIS = 'THESIS';
    const TYPE_MBKM = 'MBKM';
    const TYPE_CONTINUATION = 'CONTINUATION';

    public function mount()
    {
        $this->filterProdiId = Prodi::first()->id ?? null;
        $this->prodi_id = $this->filterProdiId;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterProdiId()
    {
        $this->resetPage();
    }

    // --- Logic Inti ---

    public function updatedActivityType($value)
    {
        // Reset komponen SKS saat tipe berubah
        if ($value === self::TYPE_THESIS) {
            // Thesis: Komponen 0, Default boleh diisi manual (set default suggestion 6)
            $this->sks_tatap_muka = 0;
            $this->sks_praktek = 0;
            $this->sks_lapangan = 0;
            $this->sks_default = 6;
        } elseif ($value === self::TYPE_CONTINUATION) {
            // Continuation: Semua 0
            $this->sks_tatap_muka = 0;
            $this->sks_praktek = 0;
            $this->sks_lapangan = 0;
            $this->sks_default = 0;
        } else {
            // Regular/MBKM: Hitung ulang
            $this->calculateSks();
        }
    }

    public function updated($propertyName)
    {
        // Hanya hitung otomatis jika BUKAN Thesis dan BUKAN Continuation
        if (in_array($propertyName, ['sks_tatap_muka', 'sks_praktek', 'sks_lapangan'])) {
            if (!in_array($this->activity_type, [self::TYPE_THESIS, self::TYPE_CONTINUATION])) {
                $this->calculateSks();
            }
        }
    }

    private function calculateSks()
    {
        $this->sks_default = (int)$this->sks_tatap_muka + (int)$this->sks_praktek + (int)$this->sks_lapangan;
    }

    // --- CRUD ---

    public function create()
    {
        $this->resetForm();
        $this->prodi_id = $this->filterProdiId ?? (Prodi::first()->id ?? null);
        $this->showForm = true;
        $this->editMode = false;
    }

    public function edit($id)
    {
        $mk = MataKuliah::findOrFail($id);
        $this->mkId = $id;
        $this->kode_mk = $mk->kode_mk;
        $this->nama_mk = $mk->nama_mk;
        $this->sks_default = $mk->sks_default;
        $this->sks_tatap_muka = $mk->sks_tatap_muka;
        $this->sks_praktek = $mk->sks_praktek;
        $this->sks_lapangan = $mk->sks_lapangan;
        $this->jenis_mk = $mk->jenis_mk;
        $this->prodi_id = $mk->prodi_id;
        $this->activity_type = $mk->activity_type ?? self::TYPE_REGULAR;

        $this->showForm = true;
        $this->editMode = true;
    }

    public function save()
    {
        $this->validate([
            'nama_mk' => 'required|string|max:255',
            'kode_mk' => ['required', Rule::unique('master_mata_kuliahs', 'kode_mk')->ignore($this->mkId)],
            'prodi_id' => 'required',
            'activity_type' => 'required',
            // SKS Default wajib > 0 KECUALI Continuation
            'sks_default' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    if ($this->activity_type !== self::TYPE_CONTINUATION && $value < 1) {
                        $fail('Total SKS minimal 1 untuk tipe ini.');
                    }
                }
            ],
        ]);

        MataKuliah::updateOrCreate(['id' => $this->mkId], [
            'kode_mk' => strtoupper($this->kode_mk),
            'nama_mk' => $this->nama_mk,
            'sks_default' => $this->sks_default,
            'sks_tatap_muka' => $this->sks_tatap_muka ?: 0,
            'sks_praktek' => $this->sks_praktek ?: 0,
            'sks_lapangan' => $this->sks_lapangan ?: 0,
            'jenis_mk' => $this->jenis_mk,
            'prodi_id' => $this->prodi_id,
            'activity_type' => $this->activity_type,
        ]);

        session()->flash('success', 'Data Mata Kuliah berhasil ' . ($this->editMode ? 'diperbarui' : 'disimpan') . '.');
        $this->showForm = false;
        $this->resetForm();
    }

    public function delete($id)
    {
        try {
            // Karena kita menggunakan Soft Deletes, QueryException (Foreign Key Constraint) 
            // tidak akan ter-trigger secara otomatis. Kita harus cek manual.
            $isUsedInKurikulum = DB::table('kurikulum_mata_kuliah')->where('mata_kuliah_id', $id)->exists();
            $isUsedInJadwal = DB::table('jadwal_kuliah')->where('mata_kuliah_id', $id)->exists();

            if ($isUsedInKurikulum || $isUsedInJadwal) {
                session()->flash('error', 'Gagal menghapus! Mata kuliah ini sedang terikat dalam Kurikulum atau Jadwal Perkuliahan aktif.');
                return;
            }

            $mk = MataKuliah::findOrFail($id);
            $mk->delete(); // Eksekusi Soft Delete
            
            session()->flash('success', 'Mata kuliah berhasil dihapus (Soft Delete) dari sistem.');
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage());
        }
    }

    public function resetForm()
    {
        $this->reset(['mkId', 'kode_mk', 'nama_mk', 'sks_default', 'sks_tatap_muka', 'sks_praktek', 'sks_lapangan', 'jenis_mk', 'editMode']);
        $this->resetValidation();
        $this->activity_type = self::TYPE_REGULAR;
    }

    public function batal()
    {
        $this->showForm = false;
        $this->resetForm();
    }

    public function render()
    {
        $mks = MataKuliah::with('prodi')
            ->when($this->filterProdiId, fn($q) => $q->where('prodi_id', $this->filterProdiId))
            ->where(function($q) {
                $q->where('nama_mk', 'like', '%' . $this->search . '%')
                  ->orWhere('kode_mk', 'like', '%' . $this->search . '%');
            })
            ->orderBy('kode_mk', 'asc')
            ->paginate(10);

        return view('livewire.admin.akademik.mata-kuliah-manager', [
            'mks' => $mks,
            'prodis' => Prodi::all(),
            'types' => [
                self::TYPE_REGULAR => ['icon' => '📚', 'label' => 'Reguler', 'desc' => 'Kuliah Biasa (Auto SKS)'],
                self::TYPE_MBKM => ['icon' => '🌍', 'label' => 'MBKM', 'desc' => 'Merdeka Belajar (Auto SKS)'],
                self::TYPE_THESIS => ['icon' => '🎓', 'label' => 'Tugas Akhir', 'desc' => 'Skripsi/Tesis (Manual SKS)'],
                self::TYPE_CONTINUATION => ['icon' => '⏳', 'label' => 'Lanjutan', 'desc' => 'Masa Studi (0 SKS)'],
            ]
        ]);
    }
}