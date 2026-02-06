<?php

namespace App\Livewire\Admin\Akademik;

use Livewire\Component;
use Livewire\WithPagination;
use App\Domains\Mahasiswa\Models\Mahasiswa;
use App\Domains\Akademik\Models\KrsDetail;
use App\Domains\Akademik\Models\SkalaNilai;
use App\Domains\Akademik\Actions\HitungNilaiAkhirAction;
use App\Helpers\SistemHelper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PerbaikanNilaiManager extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedMhsId;
    public $mahasiswa;

    // Form Perbaikan
    public $showModal = false;
    public $detailTargetId;
    public $detailTarget;
    public $nilai_baru_angka;
    public $alasan;
    public $nomor_sk;

    public function render()
    {
        // Cari Mahasiswa
        $mahasiswas = [];
        if (strlen($this->search) >= 3) {
            $mahasiswas = Mahasiswa::with(['prodi', 'person'])
                ->whereHas('person', fn($q) => $q->where('nama_lengkap', 'like', "%{$this->search}%"))
                ->orWhere('nim', 'like', "%{$this->search}%")
                ->limit(5)->get();
        }

        // Ambil riwayat nilai jika mahasiswa dipilih
        $riwayatNilai = [];
        if ($this->selectedMhsId) {
            $riwayatNilai = KrsDetail::join('krs', 'krs_detail.krs_id', '=', 'krs.id')
                ->join('jadwal_kuliah', 'krs_detail.jadwal_kuliah_id', '=', 'jadwal_kuliah.id')
                ->join('master_mata_kuliahs', 'jadwal_kuliah.mata_kuliah_id', '=', 'master_mata_kuliahs.id')
                ->where('krs.mahasiswa_id', $this->selectedMhsId)
                ->where('krs_detail.is_published', true) // Hanya yang sudah publish yang butuh modul perbaikan ini
                ->select('krs_detail.*', 'master_mata_kuliahs.nama_mk', 'master_mata_kuliahs.kode_mk')
                ->orderBy('krs.tahun_akademik_id', 'desc')
                ->get();
        }

        return view('livewire.admin.akademik.perbaikan-nilai-manager', [
            'mahasiswas' => $mahasiswas,
            'riwayatNilai' => $riwayatNilai
        ]);
    }

    public function selectMhs($id)
    {
        $this->selectedMhsId = $id;
        $this->mahasiswa = Mahasiswa::find($id);
        $this->search = '';
    }

    public function openRevision($detailId)
    {
        $this->detailTargetId = $detailId;
        $this->detailTarget = KrsDetail::find($detailId);
        $this->nilai_baru_angka = $this->detailTarget->nilai_angka;
        $this->reset(['alasan', 'nomor_sk']);
        $this->showModal = true;
    }

    public function processRevision()
    {
        $this->validate([
            'nilai_baru_angka' => 'required|numeric|min:0|max:100',
            'alasan' => 'required|min:10',
        ]);

        DB::transaction(function () {
            $oldNilaiAngka = $this->detailTarget->nilai_angka;
            $oldNilaiHuruf = $this->detailTarget->nilai_huruf;

            // 1. Konversi Nilai Baru ke Huruf (SSOT Master Skala)
            $skala = SkalaNilai::where('nilai_min', '<=', $this->nilai_baru_angka)
                ->where('nilai_max', '>=', $this->nilai_baru_angka)
                ->first();
            
            $newHuruf = $skala ? $skala->huruf : 'E';
            $newIndeks = $skala ? $skala->bobot_indeks : 0.00;

            // 2. Update KRS Detail
            $this->detailTarget->update([
                'nilai_angka' => $this->nilai_baru_angka,
                'nilai_huruf' => $newHuruf,
                'nilai_indeks' => $newIndeks
            ]);

            // 3. Catat ke Log Perbaikan (Akuntabilitas)
            DB::table('akademik_grade_revision_logs')->insert([
                'krs_detail_id' => $this->detailTargetId,
                'old_nilai_angka' => $oldNilaiAngka,
                'old_nilai_huruf' => $oldNilaiHuruf,
                'new_nilai_angka' => $this->nilai_baru_angka,
                'new_nilai_huruf' => $newHuruf,
                'alasan_perbaikan' => $this->alasan,
                'nomor_sk_perbaikan' => $this->nomor_sk,
                'executed_by' => Auth::id(),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // 4. Hitung Ulang IPS/IPK Mahasiswa di semester tersebut
            $action = new HitungNilaiAkhirAction();
            $action->hitungIps($this->detailTarget->krs);
        });

        session()->flash('success', 'Nilai berhasil diperbaiki dan IPK telah dikalkulasi ulang.');
        $this->showModal = false;
    }
}