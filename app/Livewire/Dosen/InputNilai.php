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

    // State untuk input form (Array: [krs_detail_id => value])
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
        $this->jadwal = JadwalKuliah::with(['mataKuliah', 'dosen'])->find($this->jadwalId);

        // Ambil mahasiswa yang mengambil kelas ini
        $this->pesertaKelas = KrsDetail::with(['krs.mahasiswa'])
            ->where('jadwal_kuliah_id', $this->jadwalId)
            ->get();

        // Fill form state
        foreach ($this->pesertaKelas as $mhs) {
            $this->nilaiTugas[$mhs->id] = $mhs->nilai_tugas;
            $this->nilaiUts[$mhs->id]   = $mhs->nilai_uts;
            $this->nilaiUas[$mhs->id]   = $mhs->nilai_uas;
        }
    }

    public function simpanNilai($detailId)
    {
        // Validasi Simple
        $tugas = $this->nilaiTugas[$detailId] ?? 0;
        $uts   = $this->nilaiUts[$detailId] ?? 0;
        $uas   = $this->nilaiUas[$detailId] ?? 0;

        DB::transaction(function () use ($detailId, $tugas, $uts, $uas) {
            $detail = KrsDetail::find($detailId);
            
            // 1. Update Komponen Nilai
            $detail->update([
                'nilai_tugas' => $tugas,
                'nilai_uts' => $uts,
                'nilai_uas' => $uas,
            ]);

            // 2. Hitung Akhir (Panggil Action yg kita buat tadi)
            $action = new HitungNilaiAkhirAction();
            $action->execute($detail);
        });

        session()->flash('success-' . $detailId, 'Tersimpan');
    }

    public function publishNilai()
    {
        // Hitung IPS untuk semua mahasiswa di kelas ini
        $action = new HitungNilaiAkhirAction();
        
        foreach ($this->pesertaKelas as $detail) {
            $detail->update(['is_published' => true]);
            // Recalculate IPS mahasiswa ybs
            $action->hitungIps($detail->krs); 
        }

        session()->flash('global_success', 'Nilai berhasil dipublish! Mahasiswa dapat melihat KHS.');
    }

    public function render()
    {
        return view('livewire.dosen.input-nilai');
    }
}