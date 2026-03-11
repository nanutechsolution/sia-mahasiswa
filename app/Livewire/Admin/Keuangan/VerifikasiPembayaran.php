<?php

namespace App\Livewire\Admin\Keuangan;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Domains\Keuangan\Models\PembayaranMahasiswa;
use App\Domains\Keuangan\Models\TagihanMahasiswa;
use App\Domains\Keuangan\Actions\RecalculateInvoiceAction;

class VerifikasiPembayaran extends Component
{
    public $pembayarans;
    public $catatanReject;
    
    // UI Modal State
    public $showPreviewModal = false;
    public $selectedPayment = null;

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        // Load hanya yang PENDING untuk diverifikasi
        $this->pembayarans = PembayaranMahasiswa::with([
                'tagihan.mahasiswa.person', 
                'tagihan.tahunAkademik'
            ])
            ->where('status_verifikasi', 'PENDING')
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function openPreview($id)
    {
        $this->selectedPayment = PembayaranMahasiswa::with('tagihan.mahasiswa.person')->find($id);
        $this->showPreviewModal = true;
    }

    /**
     * PROSES VERIFIKASI (APPROVE)
     */
    public function approve($id)
    {
        try {
            DB::transaction(function () use ($id) {
                // 1. Lock Row Pembayaran agar tidak di-double click
                $pembayaran = PembayaranMahasiswa::lockForUpdate()->find($id);

                if (!$pembayaran || $pembayaran->status_verifikasi !== 'PENDING') {
                    return;
                }

                // 2. Update Status Pembayaran jadi VALID
                $pembayaran->update([
                    'status_verifikasi' => 'VALID',
                    'verified_by'       => Auth::id(),
                    'verified_at'       => now(),
                ]);

                // 3. AMBIL TAGIHAN & JALANKAN RECALCULATE ENGINE
                // Menggunakan Action yang sudah kita buat agar otomatis menangani:
                // - Update total_bayar di tagihan
                // - Update status LUNAS/CICIL
                // - Pemindahan ke Saldo Deposit jika ternyata pembayarannya berlebih
                $tagihan = TagihanMahasiswa::find($pembayaran->tagihan_id);
                $action = new RecalculateInvoiceAction();
                $action->execute($tagihan);
            });

            $this->dispatch('swal:success', [
                'title' => 'Berhasil!',
                'text'  => 'Pembayaran telah divalidasi dan saldo tagihan telah diperbarui.'
            ]);
            
            $this->showPreviewModal = false;
            $this->loadData();

        } catch (\Exception $e) {
            $this->dispatch('swal:error', [
                'title' => 'Gagal!',
                'text'  => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * PROSES PENOLAKAN (REJECT)
     */
    public function reject($id)
    {
        $this->validate([
            'catatanReject' => 'required|string|min:5'
        ], [
            'catatanReject.required' => 'Wajib memberikan alasan penolakan.'
        ]);

        $pembayaran = PembayaranMahasiswa::find($id);

        $pembayaran->update([
            'status_verifikasi'  => 'REJECTED', // Sesuai dengan status di LaporanKeuangan
            'verified_by'        => Auth::id(),
            'verified_at'        => now(),
            'catatan_verifikasi' => $this->catatanReject
        ]);

        $this->dispatch('swal:info', [
            'title' => 'Ditolak',
            'text'  => 'Pembayaran telah ditolak. Mahasiswa akan menerima alasan penolakan.'
        ]);

        $this->showPreviewModal = false;
        $this->loadData();
        $this->catatanReject = '';
    }

    public function render()
    {
        return view('livewire.admin.keuangan.verifikasi-pembayaran');
    }
}