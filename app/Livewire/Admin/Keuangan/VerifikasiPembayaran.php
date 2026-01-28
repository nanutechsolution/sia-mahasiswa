<?php

namespace App\Livewire\Admin\Keuangan;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Domains\Keuangan\Models\PembayaranMahasiswa;
use App\Domains\Keuangan\Models\TagihanMahasiswa;

class VerifikasiPembayaran extends Component
{
    public $pembayarans;
    public $catatanReject;

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        // Load hanya yang PENDING
        $this->pembayarans = PembayaranMahasiswa::with(['tagihan.mahasiswa', 'tagihan.tahunAkademik'])
            ->where('status_verifikasi', 'PENDING')
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function approve($id)
    {
        DB::transaction(function () use ($id) {
            // 1. Lock Row Pembayaran
            $pembayaran = PembayaranMahasiswa::lockForUpdate()->find($id);

            if ($pembayaran->status_verifikasi !== 'PENDING') {
                return; // Prevent double click race condition
            }

            // 2. Update Status Pembayaran
            $pembayaran->update([
                'status_verifikasi' => 'VALID',
                'verified_by' => auth()->id() ?? null, // Handle jika login pake tinker/test route
                'verified_at' => now(),
            ]);

            // 3. Update Saldo Tagihan Induk
            $tagihan = TagihanMahasiswa::lockForUpdate()->find($pembayaran->tagihan_id);
            $tagihan->total_bayar += $pembayaran->nominal_bayar;

            // 4. Cek Status Lunas
            if ($tagihan->total_bayar >= $tagihan->total_tagihan) {
                $tagihan->status_bayar = 'LUNAS';
            } else {
                $tagihan->status_bayar = 'CICIL';
            }
            $tagihan->save();
        });

        session()->flash('success', 'Pembayaran berhasil divalidasi.');
        $this->loadData();
    }

    public function reject($id)
    {
        $pembayaran = PembayaranMahasiswa::find($id);
        
        $pembayaran->update([
            'status_verifikasi' => 'INVALID',
            'verified_by' => auth()->id() ?? null,
            'verified_at' => now(),
            'catatan_verifikasi' => $this->catatanReject ?? 'Bukti tidak valid/buram.'
        ]);

        session()->flash('error', 'Pembayaran ditolak.');
        $this->loadData();
        $this->catatanReject = '';
    }

    public function render()
    {
        return view('livewire.admin.keuangan.verifikasi-pembayaran');
    }
}