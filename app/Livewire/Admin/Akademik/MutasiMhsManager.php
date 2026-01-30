<?php

namespace App\Livewire\Admin\Akademik;

use Livewire\Component;
use Livewire\WithPagination;
use App\Domains\Mahasiswa\Models\Mahasiswa;
use App\Domains\Mahasiswa\Models\RiwayatStatusMahasiswa;
use App\Domains\Keuangan\Models\TagihanMahasiswa;
use App\Helpers\SistemHelper;
use Illuminate\Support\Facades\DB;

class MutasiMhsManager extends Component
{
    use WithPagination;

    public $search = '';
    public $taAktifId;

    // State Modal Mutasi
    public $showModal = false;
    public $selectedMhsId;
    public $selectedMhsName;
    public $selectedMhsNim;

    // Form Input
    public $status_baru = 'C'; // C=Cuti, K=Keluar, D=DropOut, L=Lulus
    public $nomor_sk;
    public $keterangan;

    // Form Keuangan Cuti
    public $buat_tagihan_cuti = true;
    public $nominal_biaya_cuti = 250000; // Default biaya cuti

    public function mount()
    {
        $this->taAktifId = SistemHelper::idTahunAktif();
    }

    public function render()
    {
        // Query dengan relasi Person untuk pencarian nama (SSOT)
        $mahasiswas = Mahasiswa::with(['prodi', 'person', 'riwayatStatus' => function ($q) {
            // Ambil status di semester aktif saja
            $q->where('tahun_akademik_id', $this->taAktifId);
        }])
            ->where(function ($q) {
                $q->whereHas('person', function ($qp) {
                    $qp->where('nama_lengkap', 'like', '%' . $this->search . '%');
                })
                    ->orWhere('nim', 'like', '%' . $this->search . '%');
            })
            ->orderBy('nim', 'desc')
            ->paginate(10);

        return view('livewire.admin.akademik.mutasi-mhs-manager', [
            'mahasiswas' => $mahasiswas
        ]);
    }

    public function openMutasi($id)
    {
        $mhs = Mahasiswa::find($id);
        $this->selectedMhsId = $id;
        $this->selectedMhsName = $mhs->nama_lengkap;
        $this->selectedMhsNim = $mhs->nim;

        // Reset Form
        $this->status_baru = 'C';
        $this->nomor_sk = '';
        $this->keterangan = '';
        $this->buat_tagihan_cuti = true;
        $this->nominal_biaya_cuti = 250000;

        $this->showModal = true;
    }

    public function simpanMutasi()
    {
        $this->validate([
            'status_baru' => 'required|in:A,C,D,K,L,N',
            'nomor_sk' => 'nullable|string',
            'nominal_biaya_cuti' => 'required_if:buat_tagihan_cuti,true|numeric|min:0',
        ]);

        if (!$this->taAktifId) {
            session()->flash('error', 'Tidak ada semester aktif.');
            return;
        }

        DB::transaction(function () {
            // 1. Update/Create Riwayat Status Semester Ini
            RiwayatStatusMahasiswa::updateOrCreate(
                [
                    'mahasiswa_id' => $this->selectedMhsId,
                    'tahun_akademik_id' => $this->taAktifId
                ],
                [
                    'status_kuliah' => $this->status_baru,
                    'nomor_sk' => $this->nomor_sk,
                ]
            );

            // 2. Generate Tagihan Cuti (Jika status Cuti dan dicentang)
            if ($this->status_baru == 'C' && $this->buat_tagihan_cuti && $this->nominal_biaya_cuti > 0) {
                $mhs = Mahasiswa::find($this->selectedMhsId);

                TagihanMahasiswa::create([
                    'mahasiswa_id' => $this->selectedMhsId,
                    'tahun_akademik_id' => $this->taAktifId,
                    'kode_transaksi' => 'INV-CUTI-' . $mhs->nim . '-' . rand(100, 999),
                    'deskripsi' => "Biaya Administrasi Cuti Akademik",
                    'total_tagihan' => $this->nominal_biaya_cuti,
                    'total_bayar' => 0,
                    'status_bayar' => 'BELUM',
                    'rincian_item' => [
                        ['nama' => 'Biaya Administrasi Cuti', 'nominal' => (int)$this->nominal_biaya_cuti]
                    ]
                ]);
            }
        });

        $this->showModal = false;
        session()->flash('success', "Status mahasiswa berhasil diubah menjadi {$this->status_baru}.");
    }

    public function batal()
    {
        $this->showModal = false;
    }
}
