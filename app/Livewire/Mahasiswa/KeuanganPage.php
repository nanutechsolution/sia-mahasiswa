<?php

namespace App\Livewire\Mahasiswa;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use App\Domains\Mahasiswa\Models\Mahasiswa;
use App\Domains\Keuangan\Models\TagihanMahasiswa;
use App\Domains\Keuangan\Models\PembayaranMahasiswa;
use App\Domains\Keuangan\Models\KeuanganSaldo;
use App\Domains\Keuangan\Models\KeuanganSaldoTransaction;
use App\Domains\Keuangan\Actions\RecalculateInvoiceAction;
use App\Helpers\SistemHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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
        'nominalBayar.max'           => 'Nominal konfirmasi tidak boleh melebihi sisa tagihan.', 
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
            ->orderByRaw("FIELD(status_bayar, 'BELUM', 'CICIL', 'LUNAS')")
            ->orderBy('created_at', 'desc')
            ->get();

        // 2. Load Saldo Dompet
        $this->saldo = KeuanganSaldo::firstOrCreate(
            ['mahasiswa_id' => $this->mahasiswa->id], 
            ['id' => (string) Str::uuid(), 'saldo' => 0]
        );

        // 3. Load Riwayat Transaksi (GABUNGAN PEMBAYARAN & KOREKSI)
        $history = collect();

        foreach ($this->tagihans as $tagihan) {
            // Memasukkan Log Pembayaran
            foreach ($tagihan->pembayarans as $p) {
                $history->push((object)[
                    'type' => 'PAYMENT',
                    'tanggal' => $p->tanggal_bayar ?? $p->created_at,
                    'keterangan' => 'Pembayaran: ' . $tagihan->deskripsi,
                    'referensi' => $tagihan->kode_transaksi,
                    'nominal' => $p->nominal_bayar,
                    'status' => $p->status_verifikasi,
                    'metode' => str_replace('_', ' ', $p->metode_pembayaran)
                ]);
            }

            // Memasukkan Log Koreksi/Beasiswa (Ini yang hilang sebelumnya)
            foreach ($tagihan->adjustments as $a) {
                $history->push((object)[
                    'type' => 'ADJUSTMENT',
                    'tanggal' => $a->created_at,
                    'keterangan' => 'Koreksi ' . $a->jenis_adjustment . ': ' . $a->keterangan,
                    'referensi' => $tagihan->kode_transaksi,
                    'nominal' => $a->nominal,
                    'status' => 'VALID', // Koreksi otomatis valid
                    'metode' => 'SISTEM'
                ]);
            }
        }

        // Urutkan berdasarkan tanggal terbaru ke terlama
        $this->riwayatPembayaran = $history->sortByDesc('tanggal')->values()->all();
    }

    public function pilihTagihan($id)
    {
        $tagihan = $this->tagihans->find($id);
        if (!$tagihan) return;

        $this->tagihanIdSelected = $id;
        $this->selectedTagihanInfo = $tagihan;
        
        $tagihanAwal = $tagihan->total_tagihan;
        $totalKoreksi = $tagihan->adjustments->sum('nominal');
        $totalVerified = $tagihan->pembayarans->where('status_verifikasi', 'VALID')->sum('nominal_bayar');
        $totalPending = $tagihan->pembayarans->where('status_verifikasi', 'PENDING')->sum('nominal_bayar');

        $tagihanBersih = max(0, $tagihanAwal - $totalKoreksi);
        $sisaReal = $tagihanBersih - $totalVerified - $totalPending;
        $this->sisaTagihanSaatIni = max(0, $sisaReal);
        
        $this->detailHitungan = [
            'tagihan_awal' => $tagihanAwal,
            'total_koreksi' => $totalKoreksi,
            'total_valid' => $totalVerified,
            'total_pending' => $totalPending,
        ];
        
        $this->nominalBayar = ''; 
        $this->tglBayar = date('Y-m-d');
        $this->resetValidation(); 
    }

    public function batalPilih()
    {
        $this->reset(['tagihanIdSelected', 'nominalBayar', 'fileBukti', 'tglBayar', 'selectedTagihanInfo', 'sisaTagihanSaatIni']);
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
            'id'                => (string) Str::uuid(),
            'tagihan_id'        => $this->tagihanIdSelected,
            'nominal_bayar'     => $this->nominalBayar,
            'tanggal_bayar'     => $this->tglBayar,
            'metode_pembayaran' => 'MANUAL_TRANSFER', 
            'bukti_bayar_path'  => $path,
            'status_verifikasi' => 'PENDING'
        ]);

        $this->batalPilih();
        $this->loadData();
        
        session()->flash('success', 'Bukti pembayaran berhasil dikirim! Silakan tunggu proses verifikasi admin.');
    }

    // --- FITUR BARU: BAYAR INSTAN PAKAI SALDO DOMPET ---
    public function bayarPakaiSaldo()
    {
        if (!$this->tagihanIdSelected || !$this->selectedTagihanInfo) return;

        if ($this->saldo->saldo <= 0 || $this->sisaTagihanSaatIni <= 0) {
            session()->flash('error', 'Saldo tidak mencukupi atau tagihan sudah lunas.');
            return;
        }

        // Ambil nominal maksimal yang bisa dibayar pakai saldo
        $nominalDipakai = min($this->saldo->saldo, $this->sisaTagihanSaatIni);

        try {
            DB::transaction(function () use ($nominalDipakai) {
                // 1. Buat Record Pembayaran (Langsung VALID)
                PembayaranMahasiswa::create([
                    'id'                => (string) Str::uuid(),
                    'tagihan_id'        => $this->tagihanIdSelected,
                    'nominal_bayar'     => $nominalDipakai,
                    'tanggal_bayar'     => now('Asia/Makassar'),
                    'metode_pembayaran' => 'SALDO_DOMPET',
                    'bukti_bayar_path'  => 'SISTEM_AUTO',
                    'status_verifikasi' => 'VALID', 
                    'verified_by'       => Auth::id() // Auto verify by system (Mhs itself)
                ]);

                // 2. Potong Saldo Mahasiswa
                $this->saldo->decrement('saldo', $nominalDipakai);

                // 3. Catat Histori Mutasi Keluar
                KeuanganSaldoTransaction::create([
                    'saldo_id'     => $this->saldo->id,
                    'tipe'         => 'OUT',
                    'nominal'      => $nominalDipakai,
                    'referensi_id' => $this->selectedTagihanInfo->kode_transaksi,
                    'keterangan'   => 'Pelunasan otomatis via Saldo Dompet Kampus'
                ]);

                // 4. Hitung Ulang & Kunci Tagihan (LUNAS)
                $action = new RecalculateInvoiceAction();
                $action->execute($this->selectedTagihanInfo);
            });

            $this->batalPilih();
            $this->loadData();
            
            session()->flash('success', "Pembayaran Instan senilai Rp " . number_format($nominalDipakai, 0, ',', '.') . " menggunakan Saldo Dompet berhasil!");

        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.mahasiswa.keuangan-page');
    }
}