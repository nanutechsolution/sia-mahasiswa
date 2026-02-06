<?php

namespace App\Livewire\Dosen\Perkuliahan;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Domains\Akademik\Models\JadwalKuliah;
use App\Domains\Akademik\Models\PerkuliahanSesi;
use App\Domains\Akademik\Models\PerkuliahanAbsensi;
use App\Domains\Akademik\Models\KrsDetail; // Tambahkan Import KrsDetail
use Illuminate\Support\Str;

class ManagerKelas extends Component
{
    // Form Buka Kelas
    public $selectedJadwalId;
    public $materi_kuliah;
    public $pertemuan_ke;
    public $metode_validasi = 'GPS'; // Default: GPS
    public $isModalOpen = false;

    // Detail Presensi
    public $isDetailOpen = false;
    public $detailSesi;
    public $daftarPeserta = [];

    /**
     * Mengambil data jadwal hari ini secara real-time.
     * Menggunakan computed property agar data selalu fresh saat render ulang.
     */
    public function getJadwalHariIniData()
    {
        $hariIndo = [
            'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'
        ];
        
        // Paksa Timezone Asia/Makassar (WITA)
        $now = Carbon::now('Asia/Makassar'); 
        $hariIni = $hariIndo[$now->format('l')];

        // Ambil ID Dosen (Support Polymorphic atau Relasi Biasa)
        $dosenId = Auth::user()->profileable_id ?? Auth::user()->person->dosen->id ?? null;

        if (!$dosenId) return collect();

        $jadwals = JadwalKuliah::where('dosen_id', $dosenId)
            ->where('hari', $hariIni)
            ->with(['mataKuliah'])
            ->get();

        foreach ($jadwals as $jadwal) {
            // 1. Load Sesi Aktif
            $sesiAktif = PerkuliahanSesi::where('jadwal_kuliah_id', $jadwal->id)
                ->where('status_sesi', 'dibuka')
                ->latest('created_at')
                ->withCount(['absensi' => function($q) {
                    $q->where('status_kehadiran', 'H');
                }])
                ->first();
            
            $jadwal->setRelation('sesiAktif', $sesiAktif);

            // 2. Hitung Jumlah Peserta Valid (KRS Disetujui)
            // Disimpan sebagai properti dinamis untuk dipakai di Blade
            $jadwal->jumlah_peserta = KrsDetail::where('jadwal_kuliah_id', $jadwal->id)
                ->whereHas('krs', fn($q) => $q->where('status_krs', 'DISETUJUI'))
                ->count();
        }

        return $jadwals;
    }

    public function openModalBuka($jadwalId)
    {
        // Validasi Pre-Flight: Cek peserta sebelum buka modal
        $jumlahPeserta = KrsDetail::where('jadwal_kuliah_id', $jadwalId)
            ->whereHas('krs', fn($q) => $q->where('status_krs', 'DISETUJUI'))
            ->count();

        if ($jumlahPeserta === 0) {
            session()->flash('error', 'Tidak dapat membuka kelas: Belum ada mahasiswa yang mengambil mata kuliah ini (KRS Disetujui).');
            return;
        }

        $this->selectedJadwalId = $jadwalId;
        
        // Auto-increment pertemuan ke-
        $count = PerkuliahanSesi::where('jadwal_kuliah_id', $jadwalId)->count();
        $this->pertemuan_ke = $count + 1;
        $this->materi_kuliah = ''; 
        
        $this->isModalOpen = true;
    }

    public function bukaSesi()
    {
        $this->validate([
            'materi_kuliah' => 'required|string|min:5',
            'pertemuan_ke' => 'required|integer',
        ]);

        // Validasi Ulang (Server-side check)
        $jumlahPeserta = KrsDetail::where('jadwal_kuliah_id', $this->selectedJadwalId)
            ->whereHas('krs', fn($q) => $q->where('status_krs', 'DISETUJUI'))
            ->count();

        if ($jumlahPeserta === 0) {
            $this->isModalOpen = false;
            session()->flash('error', 'Gagal: Belum ada mahasiswa dengan status KRS Disetujui.');
            return;
        }

        // Simpan waktu menggunakan WITA
        $waktuSekarang = Carbon::now('Asia/Makassar');

        PerkuliahanSesi::create([
            'jadwal_kuliah_id' => $this->selectedJadwalId,
            'pertemuan_ke' => $this->pertemuan_ke,
            'waktu_mulai_rencana' => $waktuSekarang,
            'waktu_mulai_realisasi' => $waktuSekarang,
            'materi_kuliah' => $this->materi_kuliah,
            'token_sesi' => strtoupper(Str::random(6)), // Token 6 digit
            'status_sesi' => 'dibuka',
            'metode_validasi' => $this->metode_validasi
        ]);

        $this->isModalOpen = false;
        $this->reset(['materi_kuliah', 'selectedJadwalId']);
        
        session()->flash('success', 'Kelas berhasil dibuka! Token telah digenerate.');
    }

    /**
     * Menutup sesi dan otomatis menandai Alpha bagi yang tidak absen.
     */
    public function tutupSesi($sesiId)
    {
        $sesi = PerkuliahanSesi::with(['jadwalKuliah.pesertaKelas', 'absensi'])->findOrFail($sesiId);
        
        // Identifikasi mahasiswa yang sudah absen
        $sudahAbsenIds = $sesi->absensi->pluck('krs_detail_id')->toArray();
        $semuaPeserta = $sesi->jadwalKuliah->pesertaKelas;
        
        $dataAlpha = [];
        $now = Carbon::now('Asia/Makassar');

        foreach ($semuaPeserta as $mhs) {
            if (!in_array($mhs->id, $sudahAbsenIds)) {
                $dataAlpha[] = [
                    'id' => (string) Str::uuid(),
                    'perkuliahan_sesi_id' => $sesi->id,
                    'krs_detail_id' => $mhs->id,
                    'status_kehadiran' => 'A', // Alpha Otomatis
                    'waktu_check_in' => null,
                    'bukti_validasi' => json_encode(['auto_alpha' => true]),
                    'is_manual_update' => false,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        if (!empty($dataAlpha)) {
            PerkuliahanAbsensi::insert($dataAlpha);
        }

        $sesi->update([
            'status_sesi' => 'selesai',
            'waktu_selesai_realisasi' => $now
        ]);
        
        session()->flash('success', 'Kelas ditutup. Mahasiswa tanpa keterangan ditandai Alpha.');
    }

    // --- MANAJEMEN DETAIL PRESENSI ---

    public function bukaDetailPresensi($sesiId)
    {
        $this->detailSesi = PerkuliahanSesi::with('jadwalKuliah.mataKuliah')->findOrFail($sesiId);
        $this->loadPeserta($this->detailSesi);
        $this->isDetailOpen = true;
    }

    public function tutupDetailPresensi()
    {
        $this->isDetailOpen = false;
        $this->reset(['detailSesi', 'daftarPeserta']);
        // Tidak perlu panggil loadJadwal manual karena getJadwalHariIniData jalan di render
    }

    public function loadPeserta($sesi)
    {
        $peserta = $sesi->jadwalKuliah->pesertaKelas; 
        $absensi = $sesi->absensi->keyBy('krs_detail_id');

        $this->daftarPeserta = $peserta->map(function($p) use ($absensi) {
            $absenRecord = $absensi[$p->id] ?? null;
            
            // Null coalescing untuk data relasi
            $nama = $p->krs->mahasiswa->person->nama_lengkap ?? 'Mahasiswa';
            $nim = $p->krs->mahasiswa->nim ?? '-';

            return [
                'krs_detail_id' => $p->id,
                'nama' => $nama,
                'nim' => $nim,
                'status' => $absenRecord ? $absenRecord->status_kehadiran : 'A',
                'waktu_absen' => $absenRecord ? $absenRecord->waktu_check_in : null,
            ];
        })->sortBy('nama')->values(); 
    }

    public function updateStatus($krsDetailId, $status)
    {
        PerkuliahanAbsensi::updateOrCreate(
            [
                'perkuliahan_sesi_id' => $this->detailSesi->id,
                'krs_detail_id' => $krsDetailId
            ],
            [
                'status_kehadiran' => $status,
                'is_manual_update' => true,
                'waktu_check_in' => Carbon::now('Asia/Makassar'),
                'modified_by_user_id' => Auth::id()
            ]
        );
        
        $this->loadPeserta($this->detailSesi);
        session()->flash('success', 'Status kehadiran berhasil diubah.');
    }

    // --- CETAK LAPORAN ---

    public function cetakPresensi($sesiId)
    {
        return redirect()->route('dosen.cetak.presensi', ['sesiId' => $sesiId]);
    }

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