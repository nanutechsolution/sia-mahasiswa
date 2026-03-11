<?php

namespace App\Livewire\Admin\Keuangan;

use Livewire\Component;
use App\Domains\Mahasiswa\Models\Mahasiswa;
use App\Domains\Keuangan\Models\TagihanMahasiswa;
use App\Domains\Keuangan\Models\KomponenBiaya;
use App\Domains\Core\Models\TahunAkademik;
use App\Helpers\SistemHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

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

        // PERBAIKAN: Bungkus relasi orWhere ke dalam Closure (Group Query) agar aman
        $this->searchResults = Mahasiswa::with(['prodi', 'person'])
            ->where(function($query) {
                $query->whereHas('person', function($q) {
                    $q->where('nama_lengkap', 'like', '%' . $this->searchMhs . '%');
                })
                ->orWhere('nim', 'like', '%' . $this->searchMhs . '%');
            })
            ->limit(5)
            ->get();
    }

    public function selectMhs($id)
    {
        $this->selectedMhs = Mahasiswa::with(['prodi', 'programKelas', 'person'])->find($id);
        $this->searchMhs = ''; 
        $this->searchResults = [];
        $this->resetValidation('searchMhs');
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
        // Validasi kustom untuk objek Mahasiswa
        if (!$this->selectedMhs) {
            $this->addError('searchMhs', 'Silakan cari dan pilih target mahasiswa terlebih dahulu.');
            return;
        }

        $this->validate([
            'semesterId' => 'required',
            'komponenId' => 'required',
            'nominal'    => 'required|numeric|min:1000',
            'deskripsi'  => 'required|string|max:255',
        ]);

        $komponen = KomponenBiaya::find($this->komponenId);

        try {
            DB::transaction(function () use ($komponen) {
                TagihanMahasiswa::create([
                    'id'                => (string) Str::uuid(), // Security Hardening
                    'mahasiswa_id'      => $this->selectedMhs->id,
                    'tahun_akademik_id' => $this->semesterId,
                    'kode_transaksi'    => 'INV-MNL-' . strtoupper(Str::random(8)),
                    'deskripsi'         => $this->deskripsi,
                    'total_tagihan'     => $this->nominal,
                    'total_bayar'       => 0,
                    'status_bayar'      => 'BELUM',
                    'created_by'        => Auth::id(), // Audit Trail
                    // PERBAIKAN: Gunakan json_encode agar format rincian konsisten dan tidak error Array to String
                    'rincian_item'      => json_encode([
                        $komponen->nama_komponen => (int) $this->nominal
                    ])
                ]);
            });

            $nama = $this->selectedMhs->person->nama_lengkap ?? $this->selectedMhs->nim;
            
            $this->dispatch('swal:success', [
                'title' => 'Tagihan Terbit!',
                'text'  => "Invoice manual berhasil dikirimkan ke akun mahasiswa: {$nama}."
            ]);
            
            $this->reset(['selectedMhs', 'komponenId', 'nominal', 'deskripsi', 'searchMhs']);
            
        } catch (\Exception $e) {
            $this->dispatch('swal:error', [
                'title' => 'Gagal Menyimpan!',
                'text'  => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ]);
        }
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