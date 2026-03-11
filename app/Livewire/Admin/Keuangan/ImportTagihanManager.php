<?php

namespace App\Livewire\Admin\Keuangan;

use App\Domains\Core\Models\TahunAkademik;
use App\Domains\Mahasiswa\Models\Mahasiswa;
use App\Domains\Keuangan\Models\TagihanMahasiswa;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\TagihanHistorisImport;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ImportTagihanManager extends Component
{
    use WithFileUploads, WithPagination;

    // --- STATE UI ---
    public $activeTab = 'import'; 
    public $search = '';

    // --- PROPERTIES IMPORT ---
    public $file_excel;
    public $isImporting = false;
    public $importResult = null;

    // --- PROPERTIES FORM MANUAL ---
    public $tagihan_id;
    public $nim;
    public $kode_tahun;
    public $kode_transaksi;
    public $deskripsi = 'Tunggakan Historis Pra-SIAKAD';
    public $total_tagihan;

    public function mount()
    {
        $this->importResult = null;
        $this->generateKodeTransaksi();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function switchTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetForm();
    }

    public function generateKodeTransaksi()
    {
        $this->kode_transaksi = 'INV-LGCY-' . strtoupper(Str::random(8));
    }

    /**
     * Download Template Excel
     */
    public function downloadTemplate()
    {
        $export = new class implements FromArray, WithHeadings {
            public function headings(): array {
                return ['nim', 'kode_tahun', 'kode_transaksi', 'deskripsi', 'total_tagihan'];
            }
            public function array(): array {
                return [
                    ['17010001', '20231', 'INV-OLD-001', 'Tunggakan SPP Semester 3', '3500000'],
                    ['18010055', '', '', 'Sisa Uang Gedung 2018', '1250000']
                ];
            }
        };

        return Excel::download($export, 'Template_Import_Tunggakan_Historis.xlsx');
    }

    /**
     * Proses Import Excel
     */
    public function prosesImport()
    {
        $this->validate(['file_excel' => 'required|mimes:xlsx,xls,csv|max:5120']);

        $this->isImporting = true;
        
        try {
            $import = new TagihanHistorisImport();
            Excel::import($import, $this->file_excel);

            $this->importResult = [
                'status' => 'success',
                'count' => $import->successCount ?? 0,
                'errors' => $import->errors ?? []
            ];

            $this->reset('file_excel');
            $this->dispatch('swal:success', ['title' => 'Selesai', 'text' => 'Proses import tagihan historis selesai.']);
        } catch (\Exception $e) {
            $this->importResult = ['status' => 'error', 'message' => $e->getMessage()];
        }

        $this->isImporting = false;
    }

    /**
     * Simpan Data Secara Manual
     */
    public function saveManual()
    {
        $this->validate([
            'nim' => 'required',
            'kode_transaksi' => 'required|unique:tagihan_mahasiswas,kode_transaksi,' . $this->tagihan_id,
            'deskripsi' => 'required|string|max:255',
            'total_tagihan' => 'required|numeric|min:1'
        ]);

        $mahasiswa = Mahasiswa::where('nim', $this->nim)->first();
        $tahun = null;
        if ($this->kode_tahun) {
            $tahun = TahunAkademik::where('kode_tahun', $this->kode_tahun)->first();
            if (!$tahun) return $this->addError('kode_tahun', 'Kode Tahun tidak ditemukan.');
        }

        if (!$mahasiswa) return $this->addError('nim', 'NIM Mahasiswa tidak terdaftar.');

        try {
            DB::transaction(function () use ($mahasiswa, $tahun) {
                $data = [
                    'mahasiswa_id' => $mahasiswa->id,
                    'tahun_akademik_id' => $tahun ? $tahun->id : null,
                    'kode_transaksi' => strtoupper($this->kode_transaksi),
                    'deskripsi' => $this->deskripsi,
                    'total_tagihan' => $this->total_tagihan,
                    'rincian_item' => json_encode(['Tunggakan Manual' => $this->total_tagihan]),
                ];

                if ($this->tagihan_id) {
                    TagihanMahasiswa::findOrFail($this->tagihan_id)->update($data);
                } else {
                    TagihanMahasiswa::create(array_merge($data, [
                        'id' => (string) Str::uuid(),
                        'total_bayar' => 0,
                        'status_bayar' => 'BELUM',
                        'created_by' => Auth::id()
                    ]));
                }
            });

            $this->dispatch('swal:success', ['title' => 'Tersimpan', 'text' => 'Tagihan tunggakan berhasil ditambahkan.']);
            $this->resetForm();
        } catch (\Exception $e) {
            $this->dispatch('swal:error', ['title' => 'Gagal', 'text' => $e->getMessage()]);
        }
    }

    public function deleteManual($id)
    {
        try {
            $tagihan = TagihanMahasiswa::findOrFail($id);
            // Jangan izinkan hapus jika sudah ada pembayaran masuk
            if ($tagihan->total_bayar > 0) {
                $this->dispatch('swal:error', ['title' => 'Akses Ditolak', 'text' => 'Tagihan ini sudah memiliki riwayat pembayaran (Cicil/Lunas) dan tidak boleh dihapus.']);
                return;
            }
            $tagihan->delete();
            $this->dispatch('swal:success', ['title' => 'Dihapus', 'text' => 'Tagihan historis berhasil dibatalkan/dihapus.']);
        } catch (\Exception $e) {
            $this->dispatch('swal:error', ['title' => 'Error', 'text' => 'Gagal menghapus data.']);
        }
    }

    public function resetForm()
    {
        $this->reset(['tagihan_id', 'nim', 'kode_tahun', 'total_tagihan']);
        $this->deskripsi = 'Tunggakan Historis Pra-SIAKAD';
        $this->generateKodeTransaksi();
        $this->resetValidation();
    }

    public function render()
    {
        $riwayatTagihan = collect();
        if ($this->activeTab === 'manual') {
            // Ambil riwayat tagihan manual/historis saja (bisa ditandai dari keyword di deskripsi atau transaksi)
            $riwayatTagihan = TagihanMahasiswa::with(['mahasiswa.person', 'tahunAkademik'])
                ->where(function($q) {
                    $q->where('deskripsi', 'like', '%Historis%')
                      ->orWhere('kode_transaksi', 'like', '%LEGACY%')
                      ->orWhere('kode_transaksi', 'like', '%LGCY%');
                })
                ->when($this->search, function ($query) {
                    $query->whereHas('mahasiswa', fn($q) => $q->where('nim', 'like', '%' . $this->search . '%'))
                        ->orWhere('kode_transaksi', 'like', '%' . $this->search . '%')
                        ->orWhereHas('mahasiswa.person', fn($q) => $q->where('nama_lengkap', 'like', '%' . $this->search . '%'));
                })
                ->latest()
                ->paginate(10);
        }

        return view('livewire.admin.keuangan.import-tagihan-manager', [
            'riwayatTagihan' => $riwayatTagihan
        ]);
    }
}