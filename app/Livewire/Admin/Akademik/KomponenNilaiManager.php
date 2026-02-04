<?php

namespace App\Livewire\Admin\Akademik;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Domains\Akademik\Models\Kurikulum;

class KomponenNilaiManager extends Component
{
    use WithPagination;

    // Navigation State
    public $activeTab = 'config'; // 'master' atau 'config'
    public $showForm = false;
    public $search = '';

    // Master Component State
    public $komponenId, $nama_komponen, $is_active = true;

    // Weight Configuration State
    public $selectedKurikulumId;
    public $weights = []; // Array of [komponen_id => bobot]

    public function render()
    {
        return view('livewire.admin.akademik.komponen-nilai-manager', [
            'masterKomponens' => $this->getMasterData(),
            'kurikulums' => $this->getKurikulumData(),
            'allMaster' => DB::table('ref_komponen_nilai')->where('is_active', true)->get()
        ]);
    }

    private function getMasterData()
    {
        return DB::table('ref_komponen_nilai')
            ->where('nama_komponen', 'like', '%' . $this->search . '%')
            ->orderBy('id', 'asc')
            ->paginate(10, ['*'], 'masterPage');
    }

    private function getKurikulumData()
    {
        return Kurikulum::with('prodi')
            ->where('nama_kurikulum', 'like', '%' . $this->search . '%')
            ->orderBy('tahun_mulai', 'desc')
            ->paginate(10, ['*'], 'configPage');
    }

    // --- LOGIKA MASTER KOMPONEN ---

    public function createMaster()
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function saveMaster()
    {
        $this->validate([
            'nama_komponen' => 'required|string|max:50',
        ]);

        $slug = Str::slug($this->nama_komponen);

        DB::table('ref_komponen_nilai')->updateOrInsert(
            ['id' => $this->komponenId],
            [
                'nama_komponen' => $this->nama_komponen,
                'slug' => $slug,
                'is_active' => $this->is_active,
                'updated_at' => now(),
                'created_at' => $this->komponenId ? DB::raw('created_at') : now()
            ]
        );

        session()->flash('success', 'Master komponen berhasil disimpan.');
        $this->showForm = false;
        $this->resetForm();
    }

    public function editMaster($id)
    {
        $data = DB::table('ref_komponen_nilai')->where('id', $id)->first();
        $this->komponenId = $id;
        $this->nama_komponen = $data->nama_komponen;
        $this->is_active = (bool) $data->is_active;
        $this->showForm = true;
    }

    // --- LOGIKA CONFIG BOBOT ---

    public function selectKurikulum($id)
    {
        $this->selectedKurikulumId = $id;
        $this->weights = [];
        
        // Load existing weights
        $existing = DB::table('kurikulum_komponen_nilai')
            ->where('kurikulum_id', $id)
            ->pluck('bobot_persen', 'komponen_id')
            ->toArray();

        // Ambil semua komponen aktif
        $komponens = DB::table('ref_komponen_nilai')->where('is_active', true)->get();
        
        foreach ($komponens as $k) {
            $this->weights[$k->id] = $existing[$k->id] ?? 0;
        }

        $this->showForm = true;
    }

    public function saveWeights()
    {
        $total = array_sum($this->weights);

        if ($total != 100) {
            $this->addError('total_weight', 'Total bobot harus tepat 100%. Saat ini: ' . $total . '%');
            return;
        }

        DB::transaction(function () {
            // Bersihkan bobot lama
            DB::table('kurikulum_komponen_nilai')
                ->where('kurikulum_id', $this->selectedKurikulumId)
                ->delete();

            // Simpan bobot baru (hanya yang > 0)
            foreach ($this->weights as $komponenId => $persen) {
                if ($persen > 0) {
                    DB::table('kurikulum_komponen_nilai')->insert([
                        'kurikulum_id' => $this->selectedKurikulumId,
                        'komponen_id' => $komponenId,
                        'bobot_persen' => $persen,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }
        });

        session()->flash('success', 'Konfigurasi bobot berhasil diperbarui.');
        $this->showForm = false;
    }

    public function resetForm()
    {
        $this->reset(['komponenId', 'nama_komponen', 'is_active', 'selectedKurikulumId', 'weights', 'showForm']);
    }

    public function switchTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetForm();
    }
}