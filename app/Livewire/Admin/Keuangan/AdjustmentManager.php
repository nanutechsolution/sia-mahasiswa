<?php

namespace App\Livewire\Admin\Keuangan;

use Livewire\Component;
use App\Domains\Mahasiswa\Models\Mahasiswa;
use App\Domains\Keuangan\Models\TagihanMahasiswa;
use App\Domains\Keuangan\Models\KeuanganAdjustment;
use App\Domains\Keuangan\Models\KeuanganSaldo;
use App\Domains\Keuangan\Models\KeuanganSaldoTransaction; // [FIX] Import Model Transaksi
use App\Domains\Keuangan\Actions\RecalculateInvoiceAction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdjustmentManager extends Component
{
    // State Pencarian
    public $search = '';
    public $searchResults = [];
    public $selectedMhsId;
    
    // Data View
    public $mahasiswa;
    public $tagihans = [];
    public $saldo;
    public $riwayatSaldo = []; // [BARU] Untuk menampilkan histori di View

    // Form Adjustment
    public $showAdjustmentModal = false;
    public $adj_tagihan_id;
    public $adj_jenis = 'BEASISWA'; 
    public $adj_nominal;
    public $adj_keterangan;

    // Form Refund
    public $showRefundModal = false;
    public $refund_nominal;
    public $refund_keterangan;

    public function updatedSearch()
    {
        if (strlen($this->search) < 3) {
            $this->searchResults = [];
            return;
        }

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
        
        $this->tagihans = TagihanMahasiswa::with(['tahunAkademik', 'adjustments.creator'])
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
            KeuanganAdjustment::create([
                'tagihan_id' => $this->adj_tagihan_id,
                'jenis_adjustment' => $this->adj_jenis,
                'nominal' => $this->adj_nominal,
                'keterangan' => $this->adj_keterangan,
                'created_by' => Auth::id()
            ]);

            $tagihan = TagihanMahasiswa::find($this->adj_tagihan_id);
            $action = new RecalculateInvoiceAction();
            $action->execute($tagihan);
        });

        session()->flash('success', 'Koreksi tagihan berhasil disimpan & dihitung ulang.');
        $this->showAdjustmentModal = false;
        $this->loadData(); 
    }

    // --- REFUND / CASH OUT LOGIC ---

    public function openRefund()
    {
        $this->refund_nominal = $this->saldo->saldo;
        $this->showRefundModal = true;
    }

    public function processRefund()
    {
        $this->validate([
            'refund_nominal' => 'required|numeric|min:1000|max:' . $this->saldo->saldo,
            'refund_keterangan' => 'required'
        ]);

        DB::transaction(function () {
            // 1. Kurangi Saldo
            $this->saldo->decrement('saldo', $this->refund_nominal);
            $this->saldo->touch(); // Update updated_at

            // 2. [FIX] Catat Log Transaksi Keluar (OUT)
            KeuanganSaldoTransaction::create([
                'saldo_id' => $this->saldo->id,
                'tipe' => 'OUT',
                'nominal' => $this->refund_nominal,
                'referensi_id' => 'REF-' . date('ymdHis'),
                'keterangan' => 'Refund/Pencairan: ' . $this->refund_keterangan
            ]);
        });

        session()->flash('success', 'Refund/Penarikan saldo berhasil diproses & dicatat.');
        $this->showRefundModal = false;
        $this->loadData();
    }

    public function render()
    {
        return view('livewire.admin.keuangan.adjustment-manager');
    }
}