<?php

namespace App\Livewire\Mahasiswa;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use App\Domains\Mahasiswa\Models\Mahasiswa;
use App\Domains\Keuangan\Models\TagihanMahasiswa;
use App\Domains\Keuangan\Models\PembayaranMahasiswa;
use App\Domains\Keuangan\Models\KeuanganSaldo;
use App\Helpers\SistemHelper;

class KeuanganPage extends Component
{
    use WithFileUploads;

    public $mahasiswa;
    public $tagihans;
    public $saldo; 
    public $taAktifId;
    public $riwayatPembayaran = [];
    
    // Form State
    public $tagihanIdSelected;
    public $nominalBayar;
    public $fileBukti;
    public $tglBayar;
    
    public $selectedTagihanInfo = null; 
    public $sisaTagihanSaatIni = 0; 
    
    // Rincian Hitungan untuk UI (Transparansi)
    public $detailHitungan = [
        'tagihan_awal' => 0,
        'total_koreksi' => 0,
        'total_verified' => 0,
        'total_pending' => 0,
    ];

    // Custom Error Messages
    protected $messages = [
        'tagihanIdSelected.required' => 'Silakan pilih salah satu tagihan yang ingin dibayar.',
        'nominalBayar.required'      => 'Nominal transfer wajib diisi.',
        'nominalBayar.numeric'       => 'Nominal harus berupa angka valid.',
        'nominalBayar.min'           => 'Minimal pembayaran adalah Rp 10.000.',
        'nominalBayar.max'           => 'Nominal pembayaran tidak boleh melebihi sisa kewajiban saat ini.', 
        'tglBayar.required'          => 'Tanggal transfer wajib diisi.',
        'tglBayar.date'              => 'Format tanggal tidak valid.',
        'fileBukti.required'         => 'Bukti transfer wajib diunggah.',
        'fileBukti.image'            => 'File bukti harus berupa gambar (JPG, PNG).',
        'fileBukti.max'              => 'Ukuran file maksimal 2MB.',
    ];

    public function mount()
    {
        $user = Auth::user();
        if (!$user->person_id) abort(403, 'Akun belum terhubung data personil.');

        $this->mahasiswa = Mahasiswa::where('person_id', $user->person_id)->firstOrFail();
        $this->taAktifId = SistemHelper::idTahunAktif();
        $this->loadData();
    }

    public function loadData()
    {
        // 1. Load Tagihan
        $this->tagihans = TagihanMahasiswa::with(['pembayarans', 'tahunAkademik', 'adjustments'])
            ->where('mahasiswa_id', $this->mahasiswa->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // 2. Load Saldo Dompet
        $this->saldo = KeuanganSaldo::firstOrCreate(
            ['mahasiswa_id' => $this->mahasiswa->id], ['saldo' => 0]
        );

        // 3. Load Riwayat Pembayaran (Log Transaksi)
        $this->riwayatPembayaran = PembayaranMahasiswa::with('tagihan')
            ->whereHas('tagihan', function($q) {
                $q->where('mahasiswa_id', $this->mahasiswa->id);
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function pilihTagihan($id)
    {
        $tagihan = $this->tagihans->find($id);
        
        if (!$tagihan) return;

        $this->tagihanIdSelected = $id;
        $this->selectedTagihanInfo = $tagihan;
        
        // [LOGIC SSOT] Hitung Sisa Kewajiban Real-time
        // Ambil nominal asli
        $tagihanAwal = $tagihan->total_tagihan;

        // 1. Hitung Total Pengurang (Beasiswa/Potongan/Koreksi)
        $totalKoreksi = $tagihan->adjustments->sum('nominal');

        // 2. Hitung Total Cicilan (Verified + Pending)
        // Kita hitung PENDING sebagai pengurang agar user tidak bayar double
        $totalVerified = $tagihan->pembayarans->where('status_verifikasi', 'VALID')->sum('nominal_bayar');
        $totalPending = $tagihan->pembayarans->where('status_verifikasi', 'PENDING')->sum('nominal_bayar');

        // 3. Kalkulasi Tagihan Bersih (Awal - Koreksi)
        $tagihanBersih = max(0, $tagihanAwal - $totalKoreksi);
        
        // 4. Kalkulasi Sisa Akhir (Bersih - Verified - Pending)
        $sisaReal = $tagihanBersih - $totalVerified - $totalPending;
        $this->sisaTagihanSaatIni = max(0, $sisaReal);
        
        // Simpan rincian untuk ditampilkan di View agar user paham perhitungannya
        $this->detailHitungan = [
            'tagihan_awal' => $tagihanAwal,
            'total_koreksi' => $totalKoreksi,
            'total_valid' => $totalVerified,
            'total_pending' => $totalPending,
        ];
        
        // Reset input agar mahasiswa mengetik manual
        $this->nominalBayar = ''; 
        $this->tglBayar = date('Y-m-d');
        $this->resetValidation(); 
    }

    public function simpanPembayaran()
    {
        $this->validate([
            'tagihanIdSelected' => 'required',
            'nominalBayar'      => 'required|numeric|min:10000|max:' . $this->sisaTagihanSaatIni, 
            'tglBayar'          => 'required|date',
            'fileBukti'         => 'required|image|max:2048',
        ]);

        $path = $this->fileBukti->store('bukti-bayar', 'public');

        PembayaranMahasiswa::create([
            'tagihan_id'        => $this->tagihanIdSelected,
            'nominal_bayar'     => $this->nominalBayar,
            'tanggal_bayar'     => $this->tglBayar,
            'metode_pembayaran' => 'MANUAL', 
            'bukti_bayar_path'  => $path,
            'status_verifikasi' => 'PENDING'
        ]);

        $this->reset(['tagihanIdSelected', 'nominalBayar', 'fileBukti', 'tglBayar', 'selectedTagihanInfo', 'sisaTagihanSaatIni']);
        $this->loadData();
        
        session()->flash('success', 'Bukti pembayaran berhasil diupload. Mohon tunggu verifikasi admin.');
    }

    public function render()
    {
        return view('livewire.mahasiswa.keuangan-page');
    }
}