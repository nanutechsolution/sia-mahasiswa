<?php

namespace App\Livewire\Admin\Keuangan;

use Livewire\Component;
use App\Domains\Mahasiswa\Models\Mahasiswa;
use App\Domains\Keuangan\Models\TagihanMahasiswa;
use App\Domains\Keuangan\Models\KeuanganAdjustment;
use App\Domains\Keuangan\Models\KeuanganSaldo;
use App\Domains\Keuangan\Models\KeuanganSaldoTransaction;
use App\Domains\Keuangan\Actions\RecalculateInvoiceAction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdjustmentManager extends Component
{
    // Search State
    public $search = '';
    public $searchResults = [];
    public $selectedMhsId;
    
    // Data View
    public $mahasiswa;
    public $tagihans = [];
    public $saldo;
    public $riwayatSaldo = []; 

    // Form Adjustment
    public $showAdjustmentModal = false;
    public $selectedTagihanForAdj = null; // Object tagihan yang sedang diedit
    public $adj_tagihan_id;
    public $adj_jenis = 'BEASISWA'; 
    public $adj_nominal;
    public $adj_keterangan;

    // Form Refund
    public $showRefundModal = false;
    public $refund_nominal;
    public $refund_keterangan;

    // Custom Messages
    protected $messages = [
        'adj_jenis.required' => 'Jenis koreksi wajib dipilih.',
        'adj_nominal.required' => 'Nominal koreksi wajib diisi.',
        'adj_nominal.min' => 'Nominal minimal Rp 1.000.',
        'adj_keterangan.required' => 'Keterangan/Dasar koreksi wajib diisi (misal: No SK).',
        'refund_nominal.required' => 'Nominal refund wajib diisi.',
        'refund_nominal.max' => 'Nominal melebihi saldo tersedia.',
        'refund_keterangan.required' => 'Catatan transfer wajib diisi.',
    ];

    public function updatedSearch()
    {
        if (strlen($this->search) < 3) {
            $this->searchResults = [];
            return;
        }

        // Search via SSOT (Person)
        $this->searchResults = Mahasiswa::with(['prodi', 'person'])
            ->whereHas('person', fn($q) => $q->where('nama_lengkap', 'like', '%' . $this->search . '%'))
            ->orWhere('nim', 'like', '%' . $this->search . '%')
            ->limit(5)->get();
    }

    public function selectMahasiswa($id)
    {
        $this->selectedMhsId = $id;
        $this->search = '';
        $this->searchResults = [];
        $this->loadData();
    }

    public function loadData()
    {
        if (!$this->selectedMhsId) return;

        $this->mahasiswa = Mahasiswa::with(['prodi', 'person', 'programKelas'])->find($this->selectedMhsId);
        
        $this->tagihans = TagihanMahasiswa::with(['tahunAkademik', 'adjustments.creator', 'pembayarans'])
            ->where('mahasiswa_id', $this->selectedMhsId)
            ->orderBy('created_at', 'desc')
            ->get();

        $this->saldo = KeuanganSaldo::firstOrCreate(
            ['mahasiswa_id' => $this->selectedMhsId],
            ['saldo' => 0]
        );
        
        // Load Riwayat Mutasi Saldo
        $this->riwayatSaldo = KeuanganSaldoTransaction::where('saldo_id', $this->saldo->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $this->saldo->refresh();
    }

    // --- ADJUSTMENT LOGIC ---
    
    public function openAdjustment($tagihanId)
    {
        $this->selectedTagihanForAdj = TagihanMahasiswa::find($tagihanId);
        $this->adj_tagihan_id = $tagihanId;
        $this->reset(['adj_jenis', 'adj_nominal', 'adj_keterangan']);
        $this->showAdjustmentModal = true;
    }

    public function saveAdjustment()
    {
        $this->validate([
            'adj_jenis' => 'required',
            'adj_nominal' => 'required|numeric|min:1000',
            'adj_keterangan' => 'required|string|max:255'
        ]);

        DB::transaction(function () {
            // 1. Simpan Adjustment
            KeuanganAdjustment::create([
                'tagihan_id' => $this->adj_tagihan_id,
                'jenis_adjustment' => $this->adj_jenis,
                'nominal' => $this->adj_nominal,
                'keterangan' => $this->adj_keterangan,
                'created_by' => Auth::id()
            ]);

            // 2. Hitung Ulang Tagihan (Panggil Action)
            $tagihan = TagihanMahasiswa::find($this->adj_tagihan_id);
            $action = new RecalculateInvoiceAction();
            $action->execute($tagihan);
        });

        session()->flash('success', 'Koreksi berhasil disimpan. Tagihan telah dihitung ulang.');
        $this->showAdjustmentModal = false;
        $this->loadData(); 
    }

    // --- REFUND / CASH OUT LOGIC ---

    public function openRefund()
    {
        $this->refund_nominal = ''; // Kosongkan agar diketik manual (safety)
        $this->showRefundModal = true;
    }

    public function processRefund()
    {
        $this->validate([
            'refund_nominal' => 'required|numeric|min:1000|max:' . $this->saldo->saldo,
            'refund_keterangan' => 'required|string|max:255'
        ]);

        DB::transaction(function () {
            // 1. Kurangi Saldo
            $this->saldo->decrement('saldo', $this->refund_nominal);
            $this->saldo->touch(); 

            // 2. Catat Log Transaksi Keluar (OUT)
            KeuanganSaldoTransaction::create([
                'saldo_id' => $this->saldo->id,
                'tipe' => 'OUT',
                'nominal' => $this->refund_nominal,
                'referensi_id' => 'REF-' . date('ymdHis'), 
                'keterangan' => 'Pencairan Dana (Refund): ' . $this->refund_keterangan
            ]);
        });

        session()->flash('success', 'Dana berhasil dicairkan dan saldo telah dipotong.');
        $this->showRefundModal = false;
        $this->loadData();
    }

    public function render()
    {
        return view('livewire.admin.keuangan.adjustment-manager');
    }
}