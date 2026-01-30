<?php

namespace App\Livewire\Mahasiswa;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use App\Domains\Mahasiswa\Models\Mahasiswa;
use App\Domains\Keuangan\Models\TagihanMahasiswa;
use App\Domains\Keuangan\Models\PembayaranMahasiswa;
use App\Helpers\SistemHelper;

class KeuanganPage extends Component
{
    use WithFileUploads;

    public $mahasiswa;
    public $tagihans;
    public $taAktifId;
    
    // Form State
    public $tagihanIdSelected;
    public $nominalBayar;
    public $fileBukti;
    public $tglBayar;

    public function mount()
    {
        $user = Auth::user();
        
        // [SSOT FIX] Ambil Mahasiswa berdasarkan person_id dari User
        if (!$user->person_id) {
            abort(403, 'Akun Anda belum terhubung dengan Data Personil (SSOT).');
        }

        $this->mahasiswa = Mahasiswa::where('person_id', $user->person_id)->firstOrFail();
        $this->taAktifId = SistemHelper::idTahunAktif();
        
        $this->loadData();
    }

    public function loadData()
    {
        // Ambil semua tagihan, urutkan dari yang terbaru
        $this->tagihans = TagihanMahasiswa::with(['pembayarans', 'tahunAkademik'])
            ->where('mahasiswa_id', $this->mahasiswa->id)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function pilihTagihan($id)
    {
        $tagihan = $this->tagihans->find($id);
        $this->tagihanIdSelected = $id;
        $this->nominalBayar = $tagihan->sisa_tagihan; 
        $this->tglBayar = date('Y-m-d');
    }

    public function simpanPembayaran()
    {
        $this->validate([
            'tagihanIdSelected' => 'required',
            'nominalBayar' => 'required|numeric|min:10000',
            'tglBayar' => 'required|date',
            'fileBukti' => 'required|image|max:2048',
        ]);

        $path = $this->fileBukti->store('bukti-bayar', 'public');

        PembayaranMahasiswa::create([
            'tagihan_id' => $this->tagihanIdSelected,
            'nominal_bayar' => $this->nominalBayar,
            'tanggal_bayar' => $this->tglBayar,
            'metode_pembayaran' => 'MANUAL',
            'bukti_bayar_path' => $path,
            'status_verifikasi' => 'PENDING'
        ]);

        $this->reset(['tagihanIdSelected', 'nominalBayar', 'fileBukti', 'tglBayar']);
        $this->loadData();
        
        session()->flash('success', 'Bukti pembayaran berhasil diupload. Mohon tunggu verifikasi admin.');
    }

    public function render()
    {
        return view('livewire.mahasiswa.keuangan-page');
    }
}