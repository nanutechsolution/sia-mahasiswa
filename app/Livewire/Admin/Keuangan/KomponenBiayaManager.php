<?php

namespace App\Livewire\Admin\Keuangan;

use Livewire\Component;
use Livewire\WithPagination;
use App\Domains\Keuangan\Models\KomponenBiaya;
use Illuminate\Support\Facades\DB;

class KomponenBiayaManager extends Component
{
    use WithPagination;

    public $search = '';
    
    // Form State
    public $komponenId;
    public $nama_komponen;
    public $tipe_biaya = 'TETAP'; // Default
    
    public $showForm = false;
    public $editMode = false;

    // Reset pagination saat pencarian berubah
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $data = KomponenBiaya::where('nama_komponen', 'like', '%'.$this->search.'%')
            ->orderBy('nama_komponen', 'asc')
            ->paginate(10);

        // Statistik ringkas untuk dashboard internal
        $stats = [
            'total' => KomponenBiaya::count(),
            'tetap' => KomponenBiaya::where('tipe_biaya', 'TETAP')->count(),
            'sks' => KomponenBiaya::where('tipe_biaya', 'SKS')->count(),
        ];

        return view('livewire.admin.keuangan.komponen-biaya-manager', [
            'komponen' => $data,
            'stats' => $stats
        ]);
    }

    public function create()
    {
        $this->reset(['nama_komponen', 'tipe_biaya', 'komponenId']);
        $this->resetValidation();
        $this->showForm = true;
        $this->editMode = false;
    }

    public function edit($id)
    {
        $k = KomponenBiaya::findOrFail($id);
        $this->komponenId = $id;
        $this->nama_komponen = $k->nama_komponen;
        $this->tipe_biaya = $k->tipe_biaya;
        
        $this->editMode = true;
        $this->showForm = true;
        $this->resetValidation();
    }

    public function save()
    {
        $this->validate([
            'nama_komponen' => 'required|string|max:100|min:3',
            'tipe_biaya' => 'required|in:TETAP,SKS,SEKALI,INSIDENTAL',
        ], [
            'nama_komponen.required' => 'Nama komponen wajib diisi sesuai SK Rektor.',
            'tipe_biaya.required' => 'Pilih salah satu klasifikasi biaya.'
        ]);

        $data = [
            'nama_komponen' => strtoupper($this->nama_komponen),
            'tipe_biaya' => $this->tipe_biaya,
        ];

        try {
            if ($this->editMode) {
                KomponenBiaya::find($this->komponenId)->update($data);
            } else {
                KomponenBiaya::create($data);
            }

            $this->showForm = false;
            $this->dispatch('swal:success', [
                'title' => 'Tersimpan!',
                'text' => 'Definisi komponen biaya telah diperbarui dalam sistem.'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('swal:error', [
                'title' => 'Gagal Simpan',
                'text' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ]);
        }
    }

    public function delete($id)
    {
        try {
            // Cek apakah komponen sudah digunakan di tabel tagihan atau skema tarif
            // (Ini asuransi tambahan jika database tidak menggunakan restrict)
            $isUsed = DB::table('tagihan_mahasiswas')->where('rincian_item', 'like', '%'.KomponenBiaya::find($id)->nama_komponen.'%')->exists();
            
            if ($isUsed) {
                $this->dispatch('swal:error', [
                    'title' => 'Akses Ditolak',
                    'text' => 'Komponen ini sudah memiliki riwayat tagihan aktif dan tidak boleh dihapus demi integritas audit.'
                ]);
                return;
            }

            KomponenBiaya::destroy($id);
            $this->dispatch('swal:success', [
                'title' => 'Terhapus',
                'text' => 'Komponen biaya telah dihapus dari katalog.'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('swal:error', [
                'title' => 'Gagal Hapus',
                'text' => 'Data ini terikat dengan modul keuangan lainnya.'
            ]);
        }
    }
}