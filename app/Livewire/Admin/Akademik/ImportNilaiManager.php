<?php

namespace App\Livewire\Admin\Akademik;

use App\Domains\Akademik\Models\Krs;
use App\Domains\Akademik\Models\KrsDetail;
use App\Domains\Akademik\Models\MataKuliah;
use App\Domains\Akademik\Models\SkalaNilai;
use App\Domains\Core\Models\TahunAkademik;
use App\Domains\Mahasiswa\Models\Mahasiswa;
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

    // --- STATE TAB UI ---
    public $activeTab = 'import'; // 'import' atau 'manual'

    // --- PROPERTIES IMPORT ---
    public $file_excel;
    public $isImporting = false;
    public $importResult = null;

    // --- PROPERTIES CRUD MANUAL ---
    public $search = '';
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

    // ==========================================
    // FITUR 1: IMPORT EXCEL
    // ==========================================
    public function downloadTemplate()
    {
        // Generate template Excel secara otomatis on the fly menggunakan class anonymous!
        $export = new class implements FromArray, WithHeadings {
            public function headings(): array
            {
                return [
                    'nim',
                    'kode_tahun',
                    'kode_mk',
                    'nilai_huruf',
                    'nilai_angka'
                ];
            }

            public function array(): array
            {
                // Berikan baris contoh data agar admin tahu format yang benar
                return [
                    ['17010001', '20171', 'MKU101', 'A', '85'],
                    ['17010001', '20171', 'MKU102', 'B', '72']
                ];
            }
        };

        return Excel::download($export, 'Template_Import_Nilai.xlsx');
    }

    public function prosesImport()
    {
        $this->validate([
            'file_excel' => 'required|mimes:xlsx,xls,csv|max:10240', // Max 10MB
        ]);

        $this->isImporting = true;
        $this->importResult = null;

        try {
            $import = new NilaiHistorisImport();
            Excel::import($import, $this->file_excel);

            $this->importResult = [
                'status' => 'success',
                'success_count' => $import->successCount,
                'errors' => $import->errors
            ];

            // Reset file input
            $this->reset('file_excel');

            // Trigger sweetalert
            $this->dispatch('swal:success', [
                'title' => 'Import Selesai!',
                'text'  => $import->successCount . ' data nilai berhasil dimasukkan.'
            ]);
        } catch (\Exception $e) {
            $this->importResult = [
                'status' => 'error',
                'message' => 'Gagal memproses file Excel: ' . $e->getMessage()
            ];

            $this->dispatch('swal:error', [
                'title' => 'Gagal Import',
                'text'  => 'Format Excel tidak sesuai atau terjadi kesalahan.'
            ]);
        }

        $this->isImporting = false;
    }

    // ==========================================
    // FITUR 2: CRUD MANUAL
    // ==========================================
    public function saveManual()
    {
        // Validasi input form manual
        $this->validate([
            'nim' => 'required',
            'kode_tahun' => 'required',
            'kode_mk' => 'required',
            'nilai_huruf' => 'required|max:2',
            'nilai_angka' => 'nullable|numeric|min:0|max:100'
        ]);

        // Pencarian data master untuk relasi (Lookup)
        $mahasiswa = Mahasiswa::where('nim', $this->nim)->first();
        $tahun = TahunAkademik::where('kode_tahun', $this->kode_tahun)->first();
        $mk = MataKuliah::where('kode_mk', $this->kode_mk)->first();
        $skala = SkalaNilai::where('huruf', strtoupper($this->nilai_huruf))->first();

        // Lempar error jika master data tidak valid
        if (!$mahasiswa) return $this->addError('nim', 'NIM tidak ditemukan di sistem.');
        if (!$tahun) return $this->addError('kode_tahun', 'Tahun Akademik tidak ditemukan.');
        if (!$mk) return $this->addError('kode_mk', 'Kode Mata Kuliah tidak ditemukan.');
        if (!$skala) return $this->addError('nilai_huruf', 'Nilai Huruf tidak valid.');

        try {
            DB::transaction(function () use ($mahasiswa, $tahun, $mk, $skala) {
                // 1. Cari atau buat Header KRS
                $krs = Krs::firstOrCreate(
                    [
                        'mahasiswa_id' => $mahasiswa->id,
                        'tahun_akademik_id' => $tahun->id,
                    ],
                    [
                        'id' => Str::uuid()->toString(),
                        'status_krs' => 'DISETUJUI',
                        'tgl_krs' => now(),
                    ]
                );

                // 2. Insert atau Update Nilai
                if ($this->krs_detail_id) {
                    $detail = KrsDetail::findOrFail($this->krs_detail_id);
                    $detail->update([
                        'krs_id' => $krs->id,
                        'kode_mk_snapshot' => $mk->kode_mk,
                        'nama_mk_snapshot' => $mk->nama_mk,
                        'sks_snapshot' => $mk->sks_default,
                        'activity_type_snapshot' => $mk->activity_type,
                        'nilai_angka' => $this->nilai_angka ?? 0,
                        'nilai_huruf' => $skala->huruf,
                        'nilai_indeks' => $skala->bobot_indeks,
                    ]);
                } else {
                    KrsDetail::updateOrCreate(
                        [
                            'krs_id' => $krs->id,
                            'kode_mk_snapshot' => $mk->kode_mk,
                        ],
                        [
                            'nama_mk_snapshot' => $mk->nama_mk,
                            'sks_snapshot' => $mk->sks_default,
                            'activity_type_snapshot' => $mk->activity_type,
                            'status_ambil' => 'B',
                            'nilai_angka' => $this->nilai_angka ?? 0,
                            'nilai_huruf' => $skala->huruf,
                            'nilai_indeks' => $skala->bobot_indeks,
                            'is_published' => 1,
                            'is_edom_filled' => 1,
                        ]
                    );
                }
            });

            $this->dispatch('swal:success', [
                'title' => 'Berhasil!',
                'text' => 'Data nilai historis berhasil disimpan.'
            ]);
            $this->resetForm();
        } catch (\Exception $e) {
            $this->dispatch('swal:error', [
                'title' => 'Gagal!',
                'text' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ]);
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
            $detail = KrsDetail::findOrFail($id);
            $krsId = $detail->krs_id;
            $detail->delete();

            // Opsional: Hapus KRS header jika sudah tidak ada mata kuliah lagi
            if (KrsDetail::where('krs_id', $krsId)->count() === 0) {
                Krs::find($krsId)->delete();
            }

            $this->dispatch('swal:success', [
                'title' => 'Terhapus!',
                'text' => 'Data nilai berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('swal:error', [
                'title' => 'Gagal!',
                'text' => 'Data tidak bisa dihapus.'
            ]);
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

        // Hanya load data ketika tab manual aktif untuk menghemat query database
        if ($this->activeTab === 'manual') {
            $riwayatNilai = KrsDetail::with(['krs.mahasiswa.person', 'krs.tahunAkademik'])
                ->when($this->search, function ($query) {
                    $query->whereHas('krs.mahasiswa', function ($q) {
                        $q->where('nim', 'like', '%' . $this->search . '%');
                    })
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
