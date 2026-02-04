<?php

namespace App\Livewire\Admin\Akademik;

use Livewire\Component;
use Livewire\WithPagination;
use App\Domains\Akademik\Models\Kurikulum;
use App\Domains\Akademik\Models\MataKuliah;
use App\Domains\Akademik\Models\SkalaNilai;
use App\Domains\Core\Models\Prodi;
use Illuminate\Support\Facades\DB;

class KurikulumManager extends Component
{
    use WithPagination;

    public $viewMode = 'list';
    
    // Header State
    public $kurId; // [FIX] Tambahkan properti ini untuk mode Edit
    public $prodi_id;
    public $nama_kurikulum;
    public $tahun_mulai;
    public $id_semester_mulai;
    public $jumlah_sks_lulus = 144;
    public $jumlah_sks_wajib = 0;
    public $jumlah_sks_pilihan = 0;
    public $is_active = true;

    // Detail State
    public $selectedKurikulum;
    public $mk_id_to_add;
    public $semester_paket_to_add = 1;
    public $sifat_mk_to_add = 'W';
    public $prasyarat_mk_id;
    public $min_nilai_prasyarat_to_add = 'D';

    // Bobot SKS Fallback State
    public $sks_tatap_muka_to_add = 0;
    public $sks_praktek_to_add = 0;
    public $sks_lapangan_to_add = 0;

    public function render()
    {
        $availableGrades = SkalaNilai::where('is_lulus', true)->orderBy('bobot_indeks', 'desc')->get();

        if ($this->viewMode == 'detail') {
            $existingMkIds = $this->selectedKurikulum->mataKuliahs->pluck('id')->toArray();
            $availableMks = MataKuliah::where('prodi_id', $this->selectedKurikulum->prodi_id)
                ->whereNotIn('id', $existingMkIds)->orderBy('nama_mk')->get();

            $prerequisiteOptions = $this->selectedKurikulum->mataKuliahs()
                ->wherePivot('semester_paket', '<', (int)$this->semester_paket_to_add)
                ->orderBy('nama_mk')->get();

            return view('livewire.admin.akademik.kurikulum-detail', [
                'availableMks' => $availableMks,
                'prerequisiteOptions' => $prerequisiteOptions,
                'availableGrades' => $availableGrades
            ]);
        }

        $kurikulums = Kurikulum::with('prodi')->orderBy('tahun_mulai', 'desc')->get();
        $prodis = Prodi::all();
        return view('livewire.admin.akademik.kurikulum-manager', ['kurikulums' => $kurikulums, 'prodis' => $prodis]);
    }

    // --- [FIX] METHOD YANG HILANG ---

    public function saveHeader()
    {
        $this->validate([
            'prodi_id' => 'required',
            'nama_kurikulum' => 'required|max:100',
            'tahun_mulai' => 'required|digits:4',
        ]);

        $data = [
            'prodi_id' => $this->prodi_id,
            'nama_kurikulum' => $this->nama_kurikulum,
            'tahun_mulai' => $this->tahun_mulai,
            'id_semester_mulai' => $this->id_semester_mulai,
            'jumlah_sks_lulus' => $this->jumlah_sks_lulus,
            'is_active' => $this->is_active,
        ];

        // Create or Update
        if ($this->kurId) {
            Kurikulum::find($this->kurId)->update($data);
            session()->flash('success', 'Kurikulum berhasil diperbarui.');
        } else {
            Kurikulum::create($data);
            session()->flash('success', 'Kurikulum baru berhasil dibuat.');
        }

        $this->resetHeaderForm();
    }

    public function toggleActive($id)
    {
        $kur = Kurikulum::find($id);
        if($kur) {
            $kur->update(['is_active' => !$kur->is_active]);
            session()->flash('success', 'Status aktif kurikulum diubah.');
        }
    }
    
    public function resetHeaderForm()
    {
        $this->reset(['kurId', 'prodi_id', 'nama_kurikulum', 'tahun_mulai', 'id_semester_mulai', 'jumlah_sks_lulus', 'is_active']);
    }

    // --- DETAIL LOGIC ---

    public function updatedMkIdToAdd($value)
    {
        if ($value) {
            $mk = MataKuliah::find($value);
            if ($mk) {
                $this->sks_tatap_muka_to_add = $mk->sks_tatap_muka;
                $this->sks_praktek_to_add = $mk->sks_praktek;
                $this->sks_lapangan_to_add = $mk->sks_lapangan;
            }
        }
    }

    public function manage($id)
    {
        $this->selectedKurikulum = Kurikulum::with('mataKuliahs.prodi')->find($id);
        $this->recalculateSksHeader();
        
        $defaultMin = SkalaNilai::where('is_lulus', true)->orderBy('bobot_indeks', 'asc')->first();
        $this->min_nilai_prasyarat_to_add = $defaultMin->huruf ?? 'D';
        $this->viewMode = 'detail';
    }

    public function addMk()
    {
        $this->validate([
            'mk_id_to_add' => 'required',
            'semester_paket_to_add' => 'required|integer|min:1',
            'min_nilai_prasyarat_to_add' => 'required',
        ]);

        $mk = MataKuliah::find($this->mk_id_to_add);
        
        $this->selectedKurikulum->mataKuliahs()->attach($mk->id, [
            'semester_paket' => $this->semester_paket_to_add,
            'sks_tatap_muka' => $this->sks_tatap_muka_to_add ?: $mk->sks_tatap_muka,
            'sks_praktek' => $this->sks_praktek_to_add ?: $mk->sks_praktek,
            'sks_lapangan' => $this->sks_lapangan_to_add ?: $mk->sks_lapangan,
            'sifat_mk' => $this->sifat_mk_to_add,
            'prasyarat_mk_id' => $this->prasyarat_mk_id ?: null,
            'min_nilai_prasyarat' => $this->min_nilai_prasyarat_to_add
        ]);

        $this->recalculateSksHeader();
        $this->reset(['mk_id_to_add', 'prasyarat_mk_id', 'sks_tatap_muka_to_add', 'sks_praktek_to_add', 'sks_lapangan_to_add']);
        $this->selectedKurikulum->refresh();
        session()->flash('success', 'Mata kuliah berhasil ditambahkan ke struktur.');
    }

    public function removeMk($mkId)
    {
        $this->selectedKurikulum->mataKuliahs()->detach($mkId);
        $this->recalculateSksHeader();
        $this->selectedKurikulum->refresh();
        session()->flash('success', 'Mata kuliah dihapus dari struktur.');
    }

    private function recalculateSksHeader()
    {
        if(!$this->selectedKurikulum) return;
        $this->selectedKurikulum->load('mataKuliahs');
        $wajib = 0; $pilihan = 0;
        foreach($this->selectedKurikulum->mataKuliahs as $mk) {
            $sks = $mk->pivot->sks_tatap_muka + $mk->pivot->sks_praktek + $mk->pivot->sks_lapangan;
            if ($mk->pivot->sifat_mk == 'W') $wajib += $sks; else $pilihan += $sks;
        }
        $this->selectedKurikulum->update(['jumlah_sks_wajib' => $wajib, 'jumlah_sks_pilihan' => $pilihan]);
    }

    public function backToList() { $this->viewMode = 'list'; }
}