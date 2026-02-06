<?php

namespace App\Livewire\Dosen\Perkuliahan;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Domains\Akademik\Models\JadwalKuliah;
use App\Domains\Akademik\Models\PerkuliahanSesi;
use App\Domains\Akademik\Models\PerkuliahanAbsensi;
use Illuminate\Support\Str;

class ManagerKelas extends Component
{
    // Form Buka Kelas
    public $selectedJadwalId;
    public $materi_kuliah;
    public $pertemuan_ke;
    public $metode_validasi = 'GPS'; // GPS, QR, MANUAL
    public $isModalOpen = false;

    // Detail Presensi
    public $isDetailOpen = false;
    public $detailSesi;
    public $daftarPeserta = [];

    // Computed Property untuk mengambil data (agar selalu fresh saat render)
    public function getJadwalHariIniData()
    {
        $hariIndo = [
            'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'
        ];
        $hariIni = $hariIndo[Carbon::now()->format('l')];

        $dosenId = Auth::user()->profileable_id ?? Auth::user()->person->dosen->id ?? null;

        if (!$dosenId) return collect();

        $jadwals = JadwalKuliah::where('dosen_id', $dosenId)
            ->where('hari', $hariIni)
            ->with(['mataKuliah'])
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
        }

        return $jadwals;
    }

    public function openModalBuka($jadwalId)
    {
        $this->selectedJadwalId = $jadwalId;
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

        PerkuliahanSesi::create([
            'jadwal_kuliah_id' => $this->selectedJadwalId,
            'pertemuan_ke' => $this->pertemuan_ke,
            'waktu_mulai_rencana' => Carbon::now(),
            'waktu_mulai_realisasi' => Carbon::now(),
            'materi_kuliah' => $this->materi_kuliah,
            'token_sesi' => strtoupper(Str::random(6)),
            'status_sesi' => 'dibuka',
            'metode_validasi' => $this->metode_validasi
        ]);

        $this->isModalOpen = false;
        $this->reset(['materi_kuliah', 'selectedJadwalId']);
        
        session()->flash('success', 'Kelas berhasil dibuka! Mahasiswa sekarang bisa absen.');
    }

    /**
     * LOGIKA BARU: Tutup Sesi + Auto Alpha
     */
    public function tutupSesi($sesiId)
    {
        // 1. Ambil Sesi beserta data peserta kelas dan absensi yang sudah ada
        $sesi = PerkuliahanSesi::with(['jadwalKuliah.pesertaKelas', 'absensi'])->findOrFail($sesiId);
        
        // 2. Identifikasi siapa yang SUDAH absen (Hadir/Ijin/Sakit/Alpha Manual)
        $sudahAbsenIds = $sesi->absensi->pluck('krs_detail_id')->toArray();
        
        // 3. Ambil SEMUA peserta yang terdaftar di kelas ini
        $semuaPeserta = $sesi->jadwalKuliah->pesertaKelas;
        
        $dataAlpha = [];
        $now = now();

        // 4. Loop: Jika peserta BELUM absen, masukkan ke daftar Alpha
        foreach ($semuaPeserta as $mhs) {
            if (!in_array($mhs->id, $sudahAbsenIds)) {
                $dataAlpha[] = [
                    'id' => (string) Str::uuid(),
                    'perkuliahan_sesi_id' => $sesi->id,
                    'krs_detail_id' => $mhs->id,
                    'status_kehadiran' => 'A', // Set Alpha
                    'waktu_check_in' => null,
                    'bukti_validasi' => json_encode(['auto_alpha' => true]), // Penanda sistem
                    'is_manual_update' => false,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        // 5. Bulk Insert (Sekali query untuk banyak data)
        if (!empty($dataAlpha)) {
            PerkuliahanAbsensi::insert($dataAlpha);
        }

        // 6. Update status sesi menjadi selesai
        $sesi->update([
            'status_sesi' => 'selesai',
            'waktu_selesai_realisasi' => $now
        ]);
        
        $jumlahAlpha = count($dataAlpha);
        session()->flash('success', "Kelas ditutup. $jumlahAlpha mahasiswa yang tidak hadir ditandai Alpha.");
    }

    // --- FITUR DETAIL PRESENSI ---

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
    }

    public function loadPeserta($sesi)
    {
        $peserta = $sesi->jadwalKuliah->pesertaKelas; 
        $absensi = $sesi->absensi->keyBy('krs_detail_id');

        $this->daftarPeserta = $peserta->map(function($p) use ($absensi) {
            $absenRecord = $absensi[$p->id] ?? null;
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
                'waktu_check_in' => now(), // Timestamp saat dosen mengubah manual
                'modified_by_user_id' => Auth::id()
            ]
        );
        
        $this->loadPeserta($this->detailSesi);
        session()->flash('success', 'Status kehadiran diperbarui.');
    }

    public function render()
    {
        return view('livewire.dosen.perkuliahan.manager-kelas', [
            'jadwalHariIni' => $this->getJadwalHariIniData()
        ]);
    }
}