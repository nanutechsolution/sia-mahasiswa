<?php

namespace App\Livewire\Admin\Keuangan;

use Livewire\Component;
use App\Domains\Mahasiswa\Models\Mahasiswa;
use App\Domains\Keuangan\Models\TagihanMahasiswa;
use App\Domains\Keuangan\Models\KomponenBiaya;
use App\Domains\Core\Models\TahunAkademik;
use App\Helpers\SistemHelper;
use Illuminate\Support\Facades\DB;

class ManualTagihanManager extends Component
{
    // Search State
    public $searchMhs = '';
    public $searchResults = [];
    public $selectedMhs = null;

    // Form State
    public $semesterId;
    public $komponenId;
    public $nominal;
    public $deskripsi; // Opsional

    public function mount()
    {
        $this->semesterId = SistemHelper::idTahunAktif();
    }

    // Live Search Mahasiswa SSOT
    public function updatedSearchMhs()
    {
        if (strlen($this->searchMhs) < 3) {
            $this->searchResults = [];
            return;
        }

        $this->searchResults = Mahasiswa::with(['prodi', 'person']) // Eager load person
            ->whereHas('person', function($q) {
                $q->where('nama_lengkap', 'like', '%' . $this->searchMhs . '%');
            })
            ->orWhere('nim', 'like', '%' . $this->searchMhs . '%')
            ->limit(5)
            ->get();
    }

    public function selectMhs($id)
    {
        $this->selectedMhs = Mahasiswa::with(['prodi', 'programKelas', 'person'])->find($id);
        $this->searchMhs = ''; 
        $this->searchResults = [];
    }

    public function resetSelection()
    {
        $this->selectedMhs = null;
    }

    public function updatedKomponenId()
    {
        if ($this->komponenId) {
            $komponen = KomponenBiaya::find($this->komponenId);
            if ($komponen && empty($this->deskripsi)) {
                $this->deskripsi = "Tagihan " . $komponen->nama_komponen;
            }
        }
    }

    public function simpanTagihan()
    {
        $this->validate([
            'selectedMhs' => 'required',
            'semesterId' => 'required',
            'komponenId' => 'required',
            'nominal' => 'required|numeric|min:1000',
            'deskripsi' => 'required|string|max:255',
        ]);

        $komponen = KomponenBiaya::find($this->komponenId);

        DB::transaction(function () use ($komponen) {
            TagihanMahasiswa::create([
                'mahasiswa_id' => $this->selectedMhs->id,
                'tahun_akademik_id' => $this->semesterId,
                'kode_transaksi' => 'INV-MANUAL-' . date('ymd') . '-' . rand(1000, 9999),
                'deskripsi' => $this->deskripsi,
                'total_tagihan' => $this->nominal,
                'total_bayar' => 0,
                'status_bayar' => 'BELUM',
                'rincian_item' => [
                    [
                        'nama' => $komponen->nama_komponen,
                        'nominal' => (int) $this->nominal
                    ]
                ]
            ]);
        });

        session()->flash('success', 'Tagihan manual berhasil dibuat untuk ' . ($this->selectedMhs->person->nama_lengkap ?? $this->selectedMhs->nim));
        
        $this->reset(['selectedMhs', 'komponenId', 'nominal', 'deskripsi', 'searchMhs']);
    }

    public function render()
    {
        $semesters = TahunAkademik::orderBy('kode_tahun', 'desc')->get();
        $komponens = KomponenBiaya::orderBy('nama_komponen', 'asc')->get();

        return view('livewire.admin.keuangan.manual-tagihan-manager', [
            'semesters' => $semesters,
            'komponens' => $komponens
        ]);
    }
}