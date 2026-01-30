<?php

namespace App\Livewire\Dosen;

use Livewire\Component;
use App\Domains\Akademik\Models\JadwalKuliah;
use App\Domains\Akademik\Models\KrsDetail;
use App\Domains\Akademik\Actions\HitungNilaiAkhirAction;
use Illuminate\Support\Facades\DB;

class InputNilai extends Component
{
    public $jadwalId;
    public $jadwal;
    public $pesertaKelas;
    public $isInputNilaiOpen = false; // Status pembukaan input nilai

    // State form (Array: [krs_detail_id => value])
    public $nilaiTugas = [];
    public $nilaiUts = [];
    public $nilaiUas = [];

    public function mount($jadwalId)
    {
        $this->jadwalId = $jadwalId;
        $this->loadData();
    }

    public function loadData()
    {
        // Load Jadwal beserta status Tahun Akademik
        $this->jadwal = JadwalKuliah::with(['mataKuliah', 'dosen', 'tahunAkademik'])->find($this->jadwalId);

        if (!$this->jadwal) {
            abort(404, 'Jadwal tidak ditemukan.');
        }

        // [FIX] Cek apakah masa input nilai sedang dibuka oleh admin BAAK
        $this->isInputNilaiOpen = (bool) $this->jadwal->tahunAkademik->buka_input_nilai;

        // Hanya ambil mahasiswa yang KRS-nya sudah DISETUJUI
        $this->pesertaKelas = KrsDetail::with(['krs.mahasiswa'])
            ->where('jadwal_kuliah_id', $this->jadwalId)
            ->whereHas('krs', function($q) {
                $q->where('status_krs', 'DISETUJUI');
            })
            ->get();

        // Fill form state
        foreach ($this->pesertaKelas as $mhs) {
            $this->nilaiTugas[$mhs->id] = $mhs->nilai_tugas ?? 0;
            $this->nilaiUts[$mhs->id]   = $mhs->nilai_uts ?? 0;
            $this->nilaiUas[$mhs->id]   = $mhs->nilai_uas ?? 0;
        }
    }

    public function simpanNilai($detailId)
    {
        // Guard: Jika ditutup, gagalkan proses simpan
        if (!$this->isInputNilaiOpen) {
            session()->flash('error', 'Gagal: Masa input nilai untuk semester ini telah ditutup.');
            return;
        }

        $tugas = $this->nilaiTugas[$detailId] ?? 0;
        $uts   = $this->nilaiUts[$detailId] ?? 0;
        $uas   = $this->nilaiUas[$detailId] ?? 0;

        DB::transaction(function () use ($detailId, $tugas, $uts, $uas) {
            $detail = KrsDetail::find($detailId);
            
            $detail->update([
                'nilai_tugas' => $tugas,
                'nilai_uts' => $uts,
                'nilai_uas' => $uas,
            ]);

            $action = new HitungNilaiAkhirAction();
            $action->execute($detail);
        });

        session()->flash('success-' . $detailId, 'Tersimpan');
        $this->loadData();
    }

    public function publishNilai()
    {
        // Guard: Jika ditutup, gagalkan proses publish
        if (!$this->isInputNilaiOpen) {
            session()->flash('error', 'Gagal: Masa publish nilai telah ditutup.');
            return;
        }

        $action = new HitungNilaiAkhirAction();
        
        DB::transaction(function () use ($action) {
            foreach ($this->pesertaKelas as $detail) {
                $detail->update(['is_published' => true]);
                $action->hitungIps($detail->krs); 
            }
        });

        session()->flash('global_success', 'Nilai berhasil dipublish!');
        $this->loadData();
    }

    public function render()
    {
        return view('livewire.dosen.input-nilai');
    }
}