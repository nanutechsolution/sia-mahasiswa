<?php

namespace App\Livewire\Dosen;

use Livewire\Component;
use App\Domains\Akademik\Models\JadwalKuliah;
use App\Domains\Akademik\Models\KrsDetail;
use App\Domains\Akademik\Actions\HitungNilaiAkhirAction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class InputNilai extends Component
{
    public $jadwalId;
    public $jadwal;
    public $komponenBobot = []; 
    public $pesertaKelas = [];
    
    public $inputNilai = [];
    public $isLocked = false;

    // State untuk Modal Pengaturan Bobot (Kontrak Kuliah)
    public $showBobotModal = false;
    public $editBobot = []; // Format: [jkn_id => bobot_persen]

    // State untuk menampung kehadiran ujian (UTS/UAS) per mahasiswa
    public $kehadiranUjian = [];

    public function mount($jadwalId)
    {
        $this->jadwalId = $jadwalId;
        $this->loadConfiguration();
        $this->checkSecurity(); 
        $this->loadStudents();
    }

    private function checkSecurity()
    {
        $dosenId = Auth::user()->person->dosen->id ?? null;
        $isAuthorized = $this->jadwal->dosens->contains('id', $dosenId);

        if (!$isAuthorized) {
            abort(403, 'Anda tidak memiliki otorisasi untuk menginput nilai pada kelas ini.');
        }
    }

    public function loadConfiguration()
    {
        $this->jadwal = JadwalKuliah::with(['mataKuliah', 'tahunAkademik', 'dosens', 'ruang'])
            ->findOrFail($this->jadwalId);
        
        // Sinkronisasi Awal Kontrak Kuliah
        $jadwalKomponenCount = DB::table('jadwal_komponen_nilai')->where('jadwal_kuliah_id', $this->jadwalId)->count();
        
        if ($jadwalKomponenCount === 0) {
            $kurikulumId = DB::table('kurikulum_mata_kuliah')
                ->where('mata_kuliah_id', $this->jadwal->mata_kul_id ?? $this->jadwal->mata_kuliah_id)
                ->where('kurikulum_id', $this->jadwal->kurikulum_id)
                ->value('kurikulum_id');

            if ($kurikulumId) {
                $templateKomponen = DB::table('kurikulum_komponen_nilai')->where('kurikulum_id', $kurikulumId)->get();
                $dataToInsert = [];
                foreach ($templateKomponen as $tk) {
                    $dataToInsert[] = [
                        'jadwal_kuliah_id' => $this->jadwalId,
                        'komponen_id' => $tk->komponen_id,
                        'bobot_persen' => $tk->bobot_persen,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                if (!empty($dataToInsert)) {
                    DB::table('jadwal_komponen_nilai')->insert($dataToInsert);
                }
            }
        }

        // Muat bobot yang akan digunakan beserta ID barisnya (jkn_id) untuk keperluan Edit
        $this->komponenBobot = DB::table('jadwal_komponen_nilai as jkn')
            ->join('ref_komponen_nilai as rk', 'jkn.komponen_id', '=', 'rk.id')
            ->where('jkn.jadwal_kuliah_id', $this->jadwalId)
            ->select('jkn.id as jkn_id', 'rk.id as komponen_id', 'rk.nama_komponen', 'jkn.bobot_persen')
            ->get();

        $this->isLocked = !($this->jadwal->tahunAkademik->buka_input_nilai ?? true);
    }

    public function loadStudents()
    {
        $this->pesertaKelas = KrsDetail::with(['krs.mahasiswa.person'])
            ->where('jadwal_kuliah_id', $this->jadwalId)
            ->whereHas('krs', fn($q) => $q->where('status_krs', 'DISETUJUI'))
            ->get();

        $krsDetailIds = $this->pesertaKelas->pluck('id')->toArray();

        // Mengambil status kehadiran ujian (UTS & UAS) untuk kelas ini
        $dataUjian = DB::table('jadwal_ujian_pesertas as jup')
            ->join('jadwal_ujians as ju', 'jup.jadwal_ujian_id', '=', 'ju.id')
            ->whereIn('jup.krs_detail_id', $krsDetailIds)
            ->select('jup.krs_detail_id', 'jup.status_kehadiran', 'ju.jenis_ujian')
            ->get();

        foreach ($this->pesertaKelas as $mhs) {
            // Set default absensi ujian
            $this->kehadiranUjian[$mhs->id] = [
                'UTS' => '-',
                'UAS' => '-'
            ];

            // Inisialisasi nilai komponen
            $existingValues = DB::table('krs_detail_nilai')
                ->where('krs_detail_id', $mhs->id)
                ->pluck('nilai_angka', 'komponen_id');

            foreach ($this->komponenBobot as $k) {
                $this->inputNilai[$mhs->id][$k->komponen_id] = $existingValues[$k->komponen_id] ?? 0;
            }
        }

        // Petakan data kehadiran ujian ke masing-masing mahasiswa
        foreach ($dataUjian as $ujian) {
            if (isset($this->kehadiranUjian[$ujian->krs_detail_id]) && in_array($ujian->jenis_ujian, ['UTS', 'UAS'])) {
                $this->kehadiranUjian[$ujian->krs_detail_id][$ujian->jenis_ujian] = $ujian->status_kehadiran;
            }
        }
    }

    // --- FITUR KONTRAK KULIAH ---
    public function openBobotModal()
    {
        $this->editBobot = [];
        foreach ($this->komponenBobot as $kb) {
            $this->editBobot[$kb->jkn_id] = (float) $kb->bobot_persen;
        }
        $this->showBobotModal = true;
    }

    public function saveBobot()
    {
        if ($this->isLocked) return;

        // Validasi total harus 100%
        $totalBobot = array_sum($this->editBobot);
        if (round($totalBobot, 2) != 100.00) {
            session()->flash('error_bobot', "Total bobot harus tepat 100%. Saat ini: {$totalBobot}%");
            return;
        }

        DB::transaction(function () {
            foreach ($this->editBobot as $jkn_id => $bobot) {
                DB::table('jadwal_komponen_nilai')
                    ->where('id', $jkn_id)
                    ->update(['bobot_persen' => $bobot, 'updated_at' => now()]);
            }
        });

        $this->showBobotModal = false;
        $this->loadConfiguration(); // Refresh bobot
        
        // Kita perlu hitung ulang nilai akhir semua mahasiswa dengan bobot yang baru
        DB::transaction(function () {
            $action = new HitungNilaiAkhirAction();
            foreach ($this->pesertaKelas as $mhs) {
                if (!$mhs->is_locked) {
                    $action->execute($mhs);
                }
            }
        });

        $this->loadStudents(); // Refresh data mahasiswa
        session()->flash('global_success', 'Kontrak Kuliah (Bobot Nilai) berhasil diperbarui dan nilai akhir telah dikalkulasi ulang.');
    }

    public function saveLine($mhsDetailId)
    {
        if ($this->isLocked) return;

        $krsDetail = KrsDetail::find($mhsDetailId);
        if ($krsDetail->is_locked) {
            abort(403, 'Aksi Ilegal: Nilai mahasiswa ini sudah dikunci permanen.');
        }

        DB::transaction(function () use ($mhsDetailId, $krsDetail) {
            foreach ($this->inputNilai[$mhsDetailId] as $komponenId => $nilai) {
                DB::table('krs_detail_nilai')->updateOrInsert(
                    ['krs_detail_id' => $mhsDetailId, 'komponen_id' => $komponenId],
                    ['nilai_angka' => $nilai ?: 0, 'updated_at' => now(), 'created_at' => now()]
                );
            }
            (new HitungNilaiAkhirAction())->execute($krsDetail);
        });

        session()->flash('ok-' . $mhsDetailId, 'Tersimpan');
        $this->loadStudents(); 
    }

    public function publishAll()
    {
        if ($this->isLocked) return;

        DB::transaction(function () {
            $action = new HitungNilaiAkhirAction();
            foreach ($this->pesertaKelas as $mhs) {
                if ($mhs->is_locked) {
                    abort(403, 'Aksi Ilegal: Ditemukan nilai terkunci saat publikasi massal.');
                }
                $mhs->update(['is_published' => true]);
                $action->hitungIps($mhs->krs); 
            }
        });

        session()->flash('global_success', 'Seluruh nilai telah dipublikasikan dan terkunci di KHS Mahasiswa.');
        $this->loadStudents();
    }

    public function render()
    {
        return view('livewire.dosen.input-nilai');
    }
}