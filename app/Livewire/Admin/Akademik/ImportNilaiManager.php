<?php

namespace App\Livewire\Admin\Akademik;

use App\Domains\Akademik\Models\Krs;
use App\Domains\Akademik\Models\KrsDetail;
use App\Domains\Akademik\Models\MataKuliah;
use App\Domains\Akademik\Models\SkalaNilai;
use App\Domains\Core\Models\TahunAkademik;
use App\Domains\Mahasiswa\Models\Mahasiswa;
use App\Models\AkademikTranskrip;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\NilaiHistorisImport;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ImportNilaiManager extends Component
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
    public $krs_detail_id;
    public $nim;
    public $kode_tahun;
    public $kode_mk;
    public $nilai_huruf;
    public $nilai_angka;

    public function mount()
    {
        $this->importResult = null;
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

    /**
     * Download Template Excel untuk Import Massal
     */
    public function downloadTemplate()
    {
        $export = new class implements FromArray, WithHeadings {
            public function headings(): array {
                return ['nim', 'kode_tahun', 'kode_mk', 'nilai_huruf', 'nilai_angka'];
            }
            public function array(): array {
                return [
                    ['21010001', '20211', 'MK001', 'A', '90'],
                    ['21010001', '20211', 'MK002', 'B+', '78']
                ];
            }
        };

        return Excel::download($export, 'Template_Nilai_Historis.xlsx');
    }

    /**
     * Proses Import Excel
     */
    public function prosesImport()
    {
        $this->validate(['file_excel' => 'required|mimes:xlsx,xls|max:5120']);

        $this->isImporting = true;
        
        try {
            $import = new NilaiHistorisImport();
            Excel::import($import, $this->file_excel);

            $this->importResult = [
                'status' => 'success',
                'count' => $import->successCount ?? 0,
                'errors' => $import->errors ?? []
            ];

            $this->reset('file_excel');
            $this->dispatch('notify', type: 'success', message: 'Import nilai historis selesai.');
        } catch (\Exception $e) {
            $this->importResult = ['status' => 'error', 'message' => $e->getMessage()];
        }

        $this->isImporting = false;
    }

    /**
     * Simpan Data Secara Manual (Single Entry)
     */
    public function saveManual()
    {
        $this->validate([
            'nim' => 'required',
            'kode_tahun' => 'required',
            'kode_mk' => 'required',
            'nilai_huruf' => 'required|max:2',
            'nilai_angka' => 'nullable|numeric|min:0|max:100'
        ]);

        $mahasiswa = Mahasiswa::where('nim', $this->nim)->first();
        $tahun = TahunAkademik::where('kode_tahun', $this->kode_tahun)->first();
        $mk = MataKuliah::where('kode_mk', $this->kode_mk)->first();
        $skala = SkalaNilai::where('huruf', strtoupper($this->nilai_huruf))->first();

        if (!$mahasiswa) return $this->addError('nim', 'NIM Mahasiswa tidak terdaftar.');
        if (!$tahun) return $this->addError('kode_tahun', 'Kode Tahun tidak ditemukan.');
        if (!$mk) return $this->addError('kode_mk', 'Kode MK tidak ditemukan.');
        if (!$skala) return $this->addError('nilai_huruf', 'Gunakan huruf A, B, C, D, atau E.');

        try {
            DB::transaction(function () use ($mahasiswa, $tahun, $mk, $skala) {
                // 1. Pastikan Header KRS ada (Historis biasanya status DISETUJUI)
                $krs = Krs::firstOrCreate(
                    ['mahasiswa_id' => $mahasiswa->id, 'tahun_akademik_id' => $tahun->id],
                    ['id' => (string) Str::uuid(), 'status_krs' => 'DISETUJUI', 'tgl_krs' => now()]
                );

                // 2. Insert/Update KrsDetail (Snapshot data penting untuk transkrip)
                $dataDetail = [
                    'nama_mk_snapshot' => $mk->nama_mk,
                    'sks_snapshot' => $mk->sks_default,
                    'activity_type_snapshot' => $mk->activity_type ?? 'KULIAH',
                    'nilai_angka' => $this->nilai_angka ?? $skala->batas_bawah,
                    'nilai_huruf' => $skala->huruf,
                    'nilai_indeks' => $skala->bobot_indeks,
                    'is_published' => 1,
                    'is_edom_filled' => 1, // Bypass EDOM untuk data historis
                ];

                if ($this->krs_detail_id) {
                    $detail = KrsDetail::findOrFail($this->krs_detail_id);
                    $detail->update($dataDetail);
                } else {
                    $detail = KrsDetail::create(array_merge($dataDetail, [
                        'id' => (string) Str::uuid(),
                        'krs_id' => $krs->id,
                        'mata_kuliah_id' => $mk->id,
                        'kode_mk_snapshot' => $mk->kode_mk,
                        'status_ambil' => 'B'
                    ]));
                }

                // 3. SINKRONISASI KE TABEL TRANSKRIP (SSOT)
                AkademikTranskrip::updateOrCreate(
                    ['mahasiswa_id' => $mahasiswa->id, 'mata_kuliah_id' => $mk->id],
                    [
                        'krs_detail_id' => $detail->id,
                        'sks_diakui' => $mk->sks_default,
                        'nilai_angka_final' => $detail->nilai_angka,
                        'nilai_huruf_final' => $detail->nilai_huruf,
                        'nilai_indeks_final' => $detail->nilai_indeks,
                    ]
                );
            });

            $this->dispatch('notify', type: 'success', message: 'Data nilai historis berhasil diperbarui.');
            $this->resetForm();
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: 'Gagal simpan: ' . $e->getMessage());
        }
    }

    public function editManual($id)
    {
        $detail = KrsDetail::with(['krs.mahasiswa', 'krs.tahunAkademik'])->findOrFail($id);
        $this->krs_detail_id = $detail->id;
        $this->nim = $detail->krs->mahasiswa->nim;
        $this->kode_tahun = $detail->krs->tahunAkademik->kode_tahun;
        $this->kode_mk = $detail->kode_mk_snapshot;
        $this->nilai_huruf = $detail->nilai_huruf;
        $this->nilai_angka = $detail->nilai_angka;
        $this->activeTab = 'manual';
    }

    public function deleteManual($id)
    {
        try {
            DB::transaction(function() use ($id) {
                $detail = KrsDetail::findOrFail($id);
                // Hapus juga dari transkrip jika ini adalah record yang diakui
                AkademikTranskrip::where('krs_detail_id', $detail->id)->delete();
                $detail->delete();
            });
            $this->dispatch('notify', type: 'success', message: 'Record nilai telah dihapus.');
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: 'Gagal menghapus data.');
        }
    }

    public function resetForm()
    {
        $this->reset(['krs_detail_id', 'nim', 'kode_tahun', 'kode_mk', 'nilai_huruf', 'nilai_angka']);
        $this->resetValidation();
    }

    public function render()
    {
        $riwayatNilai = collect();
        if ($this->activeTab === 'manual') {
            $riwayatNilai = KrsDetail::with(['krs.mahasiswa.person', 'krs.tahunAkademik'])
                ->when($this->search, function ($query) {
                    $query->whereHas('krs.mahasiswa', fn($q) => $q->where('nim', 'like', '%' . $this->search . '%'))
                        ->orWhere('kode_mk_snapshot', 'like', '%' . $this->search . '%')
                        ->orWhere('nama_mk_snapshot', 'like', '%' . $this->search . '%');
                })
                ->latest()
                ->paginate(10);
        }

        return view('livewire.admin.akademik.import-nilai-manager', [
            'riwayatNilai' => $riwayatNilai
        ]);
    }
}