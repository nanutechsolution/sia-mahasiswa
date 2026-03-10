<?php

namespace App\Livewire\Dosen\Perkuliahan;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Domains\Akademik\Models\JadwalKuliah;
use App\Domains\Akademik\Models\PerkuliahanSesi;
use App\Domains\Akademik\Models\PerkuliahanAbsensi;
use App\Domains\Akademik\Models\KrsDetail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ManagerKelas extends Component
{
    // Form Sesi State
    public $selectedJadwalId;
    public $materi_kuliah;
    public $pertemuan_ke;
    public $metode_validasi = 'GPS';
    public $isModalOpen = false;

    // Attendance Detail State
    public $isDetailOpen = false;
    public $detailSesi;
    public $daftarPeserta = [];

    /**
     * Mengambil data jadwal hari ini berdasarkan Tim Pengajar (Team Teaching)
     */
    public function getJadwalHariIniData()
    {
        $hariIndo = [
            'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'
        ];
        
        $now = Carbon::now('Asia/Makassar'); 
        $hariIni = $hariIndo[$now->format('l')];

        $dosenId = Auth::user()->person->dosen->id ?? null;

        if (!$dosenId) return collect();

        $jadwals = JadwalKuliah::with(['mataKuliah', 'ruang', 'dosens.person'])
            ->whereHas('dosens', function($q) use ($dosenId) {
                $q->where('dosen_id', $dosenId);
            })
            ->where('hari', $hariIni)
            ->where('tahun_akademik_id', \App\Helpers\SistemHelper::idTahunAktif())
            ->get();

        foreach ($jadwals as $jadwal) {
            $sesiAktif = PerkuliahanSesi::where('jadwal_kuliah_id', $jadwal->id)
                ->where('status_sesi', 'dibuka')
                ->latest('created_at')
                ->withCount(['absensi' => function($q) {
                    $q->where('status_kehadiran', 'H');
                }])
                ->first();
            
            $jadwal->setRelation('sesiAktif', $sesiAktif);

            $jadwal->jumlah_peserta = KrsDetail::where('jadwal_kuliah_id', $jadwal->id)
                ->whereHas('krs', fn($q) => $q->where('status_krs', 'DISETUJUI'))
                ->count();
        }

        return $jadwals;
    }

    public function openModalBuka($jadwalId)
    {
        $jumlahPeserta = KrsDetail::where('jadwal_kuliah_id', $jadwalId)
            ->whereHas('krs', fn($q) => $q->where('status_krs', 'DISETUJUI'))
            ->count();

        if ($jumlahPeserta === 0) {
            $this->dispatch('notify', type: 'error', message: 'Gagal: Belum ada mahasiswa yang tervalidasi di kelas ini.');
            return;
        }

        $this->selectedJadwalId = $jadwalId;
        $lastSesi = PerkuliahanSesi::where('jadwal_kuliah_id', $jadwalId)->max('pertemuan_ke');
        $this->pertemuan_ke = ($lastSesi ?? 0) + 1;
        $this->materi_kuliah = ''; 
        $this->isModalOpen = true;
    }

    public function bukaSesi()
    {
        $this->validate([
            'materi_kuliah' => 'required|string|min:10',
            'pertemuan_ke' => 'required|integer|min:1|max:16',
            'metode_validasi' => 'required|in:GPS,QR,DARING,MANUAL,TUGAS'
        ]);

        $waktuSekarang = Carbon::now('Asia/Makassar');

        DB::transaction(function() use ($waktuSekarang) {
            PerkuliahanSesi::create([
                'id' => (string) Str::uuid(),
                'jadwal_kuliah_id' => $this->selectedJadwalId,
                'pertemuan_ke' => $this->pertemuan_ke,
                'waktu_mulai_rencana' => $waktuSekarang,
                'waktu_mulai_realisasi' => $waktuSekarang,
                'materi_kuliah' => $this->materi_kuliah,
                'token_sesi' => strtoupper(Str::random(6)),
                'status_sesi' => 'dibuka',
                'metode_validasi' => $this->metode_validasi,
                'opened_by_user_id' => Auth::id()
            ]);
        });

        $this->isModalOpen = false;
        $this->reset(['materi_kuliah', 'selectedJadwalId']);
        $this->dispatch('notify', type: 'success', message: 'Sesi perkuliahan berhasil dibuka!');
    }

    public function tutupSesi($sesiId)
    {
        $sesi = PerkuliahanSesi::with(['jadwalKuliah'])->findOrFail($sesiId);
        $now = Carbon::now('Asia/Makassar');

        DB::transaction(function () use ($sesi, $now) {
            $semuaPesertaIds = KrsDetail::where('jadwal_kuliah_id', $sesi->jadwal_kul_id ?? $sesi->jadwal_kuliah_id)
                ->whereHas('krs', fn($q) => $q->where('status_krs', 'DISETUJUI'))
                ->pluck('id')
                ->toArray();

            $sudahAbsenIds = PerkuliahanAbsensi::where('perkuliahan_sesi_id', $sesi->id)
                ->pluck('krs_detail_id')
                ->toArray();

            $belumAbsenIds = array_diff($semuaPesertaIds, $sudahAbsenIds);

            $dataAlpha = [];
            foreach ($belumAbsenIds as $krsDetailId) {
                $dataAlpha[] = [
                    'id' => (string) Str::uuid(),
                    'perkuliahan_sesi_id' => $sesi->id,
                    'krs_detail_id' => $krsDetailId,
                    'status_kehadiran' => 'A',
                    'bukti_validasi' => json_encode(['system_log' => 'Auto-Alpha on Close']),
                    'created_at' => $now,
                    'updated_at' => $now
                ];
            }

            if (!empty($dataAlpha)) PerkuliahanAbsensi::insert($dataAlpha);

            $sesi->update([
                'status_sesi' => 'selesai',
                'waktu_selesai_realisasi' => $now
            ]);
        });
        
        $this->dispatch('notify', type: 'success', message: 'Sesi telah ditutup.');
    }

    public function bukaDetailPresensi($sesiId)
    {
        $this->detailSesi = PerkuliahanSesi::with(['jadwalKuliah.mataKuliah', 'jadwalKuliah.ruang'])->findOrFail($sesiId);
        $this->loadPeserta();
        $this->isDetailOpen = true;
    }

    public function loadPeserta()
    {
        if (!$this->detailSesi) return;

        $peserta = KrsDetail::with(['krs.mahasiswa.person'])
            ->where('jadwal_kuliah_id', $this->detailSesi->jadwal_kuliah_id)
            ->whereHas('krs', fn($q) => $q->where('status_krs', 'DISETUJUI'))
            ->get();

        $absensi = PerkuliahanAbsensi::where('perkuliahan_sesi_id', $this->detailSesi->id)
            ->get()
            ->keyBy('krs_detail_id');

        $this->daftarPeserta = $peserta->map(function($p) use ($absensi) {
            $absen = $absensi[$p->id] ?? null;
            return [
                'krs_detail_id' => $p->id,
                'nama' => $p->krs->mahasiswa->person->nama_lengkap ?? 'Unknown',
                'nim' => $p->krs->mahasiswa->nim,
                'status' => $absen->status_kehadiran ?? 'A',
                'waktu' => $absen ? Carbon::parse($absen->waktu_check_in)->format('H:i') : '-'
            ];
        })->sortBy('nama')->values()->toArray();
    }

    public function updateStatusManual($krsDetailId, $status)
    {
        PerkuliahanAbsensi::updateOrCreate(
            ['perkuliahan_sesi_id' => $this->detailSesi->id, 'krs_detail_id' => $krsDetailId],
            [
                'status_kehadiran' => $status,
                'waktu_check_in' => Carbon::now('Asia/Makassar'),
                'is_manual_update' => true,
                'modified_by_user_id' => Auth::id()
            ]
        );
        
        $this->loadPeserta();
    }

    public function tutupDetailPresensi()
    {
        $this->isDetailOpen = false;
        $this->reset(['detailSesi', 'daftarPeserta']);
    }

    // --- METHOD CETAK LAPORAN (MENJAWAB ERROR ANDA) ---

    /**
     * Mencetak daftar hadir pertemuan tertentu (Sesi)
     */
    public function cetakPresensi($sesiId)
    {
        return redirect()->route('dosen.cetak.presensi', ['sesiId' => $sesiId]);
    }

    /**
     * Mencetak rekap absensi satu semester untuk satu jadwal kuliah
     */
    public function cetakRekap($jadwalId)
    {
        return redirect()->route('dosen.cetak.rekap', ['jadwalId' => $jadwalId]);
    }

    public function render()
    {
        return view('livewire.dosen.perkuliahan.manager-kelas', [
            'jadwalHariIni' => $this->getJadwalHariIniData()
        ]);
    }
}