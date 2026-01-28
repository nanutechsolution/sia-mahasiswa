<?php

namespace App\Livewire\Admin\Akademik;

use Livewire\Component;
use App\Domains\Akademik\Models\Kurikulum;
use App\Domains\Akademik\Models\MataKuliah;
use App\Domains\Akademik\Models\SkalaNilai;
use App\Domains\Core\Models\Prodi;

class KurikulumManager extends Component
{
    public $viewMode = 'list';

    // Header State
    public $kurikulumId;
    public $prodi_id;
    public $nama_kurikulum;
    public $tahun_mulai;
    public $id_semester_mulai;
    public $jumlah_sks_lulus = 144;
    public $jumlah_sks_wajib = 0;
    public $jumlah_sks_pilihan = 0;
    public $is_active = true;

    // State Detail
    public $selectedKurikulum;
    public $mk_id_to_add;
    public $semester_paket_to_add = 1;
    public $sifat_mk_to_add = 'W';
    public $prasyarat_mk_id;
    // Property Baru
    public $min_nilai_prasyarat_to_add = 'D';

    // State SKS
    public $sks_tatap_muka_to_add = 0;
    public $sks_praktek_to_add = 0;
    public $sks_lapangan_to_add = 0;

    public function render()
    {

        if ($this->viewMode == 'detail') {
            return $this->renderDetail();
        }
        return $this->renderList();
    }

    public function renderList()
    {
        $kurikulums = Kurikulum::with('prodi')->orderBy('tahun_mulai', 'desc')->get();
        $prodis = Prodi::all();
        return view('livewire.admin.akademik.kurikulum-manager', ['kurikulums' => $kurikulums, 'prodis' => $prodis]);
    }

    public function renderDetail()
    {

        $existingMkIds = $this->selectedKurikulum->mataKuliahs->pluck('id')->toArray();
        $availableMks = MataKuliah::where('prodi_id', $this->selectedKurikulum->prodi_id)
            ->whereNotIn('id', $existingMkIds)->orderBy('nama_mk')->get();

        $prerequisiteOptions = $this->selectedKurikulum->mataKuliahs()
            ->wherePivot('semester_paket', '<', (int)$this->semester_paket_to_add)
            ->orderBy('nama_mk')->get();
        $availableGrades = SkalaNilai::where('is_lulus', true)->orderBy('bobot_indeks', 'desc')->get();

        return view('livewire.admin.akademik.kurikulum-detail', [
            'availableMks' => $availableMks,
            'prerequisiteOptions' => $prerequisiteOptions,
            'availableGrades' => $availableGrades,
        ]);
    }

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
        $this->viewMode = 'detail';
    }

    public function addMk()
    {
        $this->validate([
            'mk_id_to_add' => 'required',
            'semester_paket_to_add' => 'required|integer|min:1|max:8',
            'min_nilai_prasyarat_to_add' => 'required|in:A,B,C,D',
        ]);

        $mk = MataKuliah::find($this->mk_id_to_add);
        $tm = $this->sks_tatap_muka_to_add ?: $mk->sks_tatap_muka;
        $pr = $this->sks_praktek_to_add ?: $mk->sks_praktek;
        $lp = $this->sks_lapangan_to_add ?: $mk->sks_lapangan;

        $this->selectedKurikulum->mataKuliahs()->attach($mk->id, [
            'semester_paket' => $this->semester_paket_to_add,
            'sks_tatap_muka' => $tm,
            'sks_praktek' => $pr,
            'sks_lapangan' => $lp,
            'sifat_mk' => $this->sifat_mk_to_add,
            'prasyarat_mk_id' => $this->prasyarat_mk_id ?: null,
            'min_nilai_prasyarat' => $this->min_nilai_prasyarat_to_add // Simpan ke Pivot
        ]);

        $this->recalculateSksHeader();
        $this->reset(['mk_id_to_add', 'prasyarat_mk_id', 'min_nilai_prasyarat_to_add', 'sks_tatap_muka_to_add', 'sks_praktek_to_add', 'sks_lapangan_to_add']);
        $this->selectedKurikulum->refresh();
        session()->flash('success', 'Mata kuliah berhasil ditambahkan.');
    }

    public function removeMk($mkId)
    {
        $this->selectedKurikulum->mataKuliahs()->detach($mkId);
        $this->recalculateSksHeader();
        $this->selectedKurikulum->refresh();
    }

    private function recalculateSksHeader()
    {
        if (!$this->selectedKurikulum) return;
        $this->selectedKurikulum->load('mataKuliahs');
        $wajib = 0;
        $pilihan = 0;
        foreach ($this->selectedKurikulum->mataKuliahs as $mk) {
            $sks = $mk->pivot->sks_tatap_muka + $mk->pivot->sks_praktek + $mk->pivot->sks_lapangan;
            if ($mk->pivot->sifat_mk == 'W') $wajib += $sks;
            else $pilihan += $sks;
        }
        $this->selectedKurikulum->update(['jumlah_sks_wajib' => $wajib, 'jumlah_sks_pilihan' => $pilihan]);
    }

    public function backToList()
    {
        $this->viewMode = 'list';
    }
}
