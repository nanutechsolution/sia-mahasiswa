<?php

namespace App\Livewire\Mahasiswa;

use Livewire\Component;
use App\Domains\Akademik\Models\KrsDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SurveiEdomPage extends Component
{
    public $krsDetailId;
    public $detail;
    public $groups = [];
    public $answers = []; // answers[pertanyaan_id] = skor

    public function mount($krsDetailId)
    {
        $this->krsDetailId = $krsDetailId;
        $this->loadData();
    }

    public function loadData()
    {
        // Load detail KRS beserta informasi Dosen dan Mata Kuliah
        $this->detail = KrsDetail::with(['jadwalKuliah.mataKuliah', 'jadwalKuliah.dosen.person'])
            ->findOrFail($this->krsDetailId);
        
        // Ambil kuesioner aktif dari database
        $this->groups = DB::table('lpm_kuisioner_kelompok')
            ->where('is_active', true)
            ->orderBy('urutan')
            ->get()->map(function($g) {
                $g->questions = DB::table('lpm_kuisioner_pertanyaan')
                    ->where('kelompok_id', $g->id)
                    ->orderBy('urutan')
                    ->get();
                return $g;
            });
    }

    /**
     * Menyimpan jawaban kuesioner ke database
     */
    public function submit()
    {
        // 1. Ambil semua ID pertanyaan yang aktif untuk validasi kelengkapan
        $allQuestionIds = DB::table('lpm_kuisioner_pertanyaan')
            ->join('lpm_kuisioner_kelompok', 'lpm_kuisioner_pertanyaan.kelompok_id', '=', 'lpm_kuisioner_kelompok.id')
            ->where('lpm_kuisioner_kelompok.is_active', true)
            ->pluck('lpm_kuisioner_pertanyaan.id')
            ->toArray();

        // 2. Validasi: Semua pertanyaan wajib dijawab
        foreach ($allQuestionIds as $qId) {
            if (!isset($this->answers[$qId]) || empty($this->answers[$qId])) {
                session()->flash('error', 'Mohon selesaikan semua kuesioner. Masih ada butir penilaian yang terlewat.');
                return;
            }
        }

        DB::beginTransaction();
        try {
            // 3. Bersihkan jawaban lama untuk mata kuliah ini jika ada (mencegah duplikasi)
            DB::table('lpm_edom_jawaban')->where('krs_detail_id', $this->krsDetailId)->delete();

            // 4. Simpan Jawaban Baru
            foreach ($allQuestionIds as $qId) {
                DB::table('lpm_edom_jawaban')->insert([
                    'krs_detail_id' => $this->krsDetailId,
                    'pertanyaan_id' => $qId,
                    'skor'          => (int) $this->answers[$qId],
                    'created_at'    => now(),
                    'updated_at'    => now()
                ]);
            }

            // 5. Update flag is_edom_filled di tabel krs_detail secara eksplisit
            // Menggunakan query builder untuk memastikan bypass mass-assignment jika belum di-set di model
            DB::table('krs_detail')
                ->where('id', $this->krsDetailId)
                ->update(['is_edom_filled' => true, 'updated_at' => now()]);

            DB::commit();

            // 6. Cek apakah masih ada mata kuliah lain di semester ini yang belum diisi EDOM
            $remainingEdom = DB::table('krs_detail')
                ->where('krs_id', $this->detail->krs_id)
                ->where('is_edom_filled', false)
                ->count();

            if ($remainingEdom > 0) {
                return redirect()->route('mhs.khs')->with('success', "Berhasil disimpan! Masih ada $remainingEdom mata kuliah lagi yang perlu Anda evaluasi.");
            }

            return redirect()->route('mhs.khs')->with('success', 'Selamat! Semua kuesioner telah terisi. KHS Anda sekarang sudah terbuka.');

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Terjadi kesalahan sistem saat menyimpan jawaban. Silakan coba lagi.');
        }
    }

    public function render()
    {
        return view('livewire.mahasiswa.survei-edom-page');
    }
}