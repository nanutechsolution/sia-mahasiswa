<?php

namespace App\Livewire\Admin\Akademik;

use Livewire\Component;
use Livewire\WithPagination;
use App\Domains\Akademik\Models\JadwalKuliah;
use App\Domains\Akademik\Models\Kurikulum;
use App\Domains\Akademik\Models\MataKuliah;
use App\Domains\Akademik\Models\Dosen;
use App\Domains\Core\Models\Prodi;
use App\Domains\Core\Models\TahunAkademik;
use App\Helpers\SistemHelper;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class JadwalKuliahManager extends Component
{
    use WithPagination;

    // Filter Global
    public $filterSemesterId;
    public $filterProdiId;

    // Form State (Event Data)
    public $jadwalId;
    public $kurikulum_id;
    public $mata_kuliah_id;
    public $dosen_id;
    public $nama_kelas;
    public $hari;
    public $jam_mulai;
    public $jam_selesai;
    public $ruang;
    public $kuota_kelas = 40;

    // Search & UI States
    public $searchMk = '';
    public $searchDosen = '';
    public $selectedMkName = '';
    public $selectedDosenName = '';
    public $showForm = false;

    // REAL-TIME VALIDATION STATES
    public $roomConflict = null;     
    public $lecturerConflict = null; 
    public $curriculumNotice = null; 
    public $timeFormatError = null; // Tambahan state untuk format jam
    public $formStatus = 'neutral';  // green, amber, red

    public function mount()
    {
        $this->filterSemesterId = SistemHelper::idTahunAktif();
        $this->filterProdiId = Prodi::first()->id ?? null;
    }

    public function updated($propertyName)
    {
        if ($propertyName === 'kurikulum_id') {
            $this->reset(['mata_kuliah_id', 'selectedMkName', 'searchMk']);
        }

        // Validasi real-time saat ada perubahan input sensitif
        if (in_array($propertyName, ['hari', 'jam_mulai', 'jam_selesai', 'ruang', 'dosen_id'])) {
            $this->validateRealTime();
        }
    }

    /**
     * CORE ENGINE: Validasi Real-Time dengan format 24 Jam
     */
    protected function validateRealTime()
    {
        $this->roomConflict = null;
        $this->lecturerConflict = null;
        $this->timeFormatError = null;
        $this->formStatus = 'green';

        // 1. Validasi Format Jam (Harus HH:mm - 24 Jam)
        $timeRegex = '/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/';
        
        if ($this->jam_mulai && !preg_match($timeRegex, $this->jam_mulai)) {
            $this->timeFormatError = "Format Jam Mulai salah (Gunakan 00:00 - 23:59)";
            $this->formStatus = 'red';
            return;
        }

        if ($this->jam_selesai && !preg_match($timeRegex, $this->jam_selesai)) {
            $this->timeFormatError = "Format Jam Selesai salah (Gunakan 00:00 - 23:59)";
            $this->formStatus = 'red';
            return;
        }

        if (!$this->hari || !$this->jam_mulai || !$this->jam_selesai) {
            $this->formStatus = 'neutral';
            return;
        }

        // 2. Query dasar overlap waktu (Mencegah Bentrok)
        $baseQuery = JadwalKuliah::where('tahun_akademik_id', $this->filterSemesterId)
            ->where('hari', $this->hari)
            ->where(function ($q) {
                $q->whereBetween('jam_mulai', [$this->jam_mulai, $this->jam_selesai])
                  ->orWhereBetween('jam_selesai', [$this->jam_mulai, $this->jam_selesai])
                  ->orWhere(function($sub) {
                      $sub->where('jam_mulai', '<=', $this->jam_mulai)
                          ->where('jam_selesai', '>=', $this->jam_selesai);
                  });
            });

        if ($this->jadwalId) {
            $baseQuery->where('id', '!=', $this->jadwalId);
        }

        // A. CEK BENTROK RUANGAN
        if ($this->ruang) {
            $conflict = (clone $baseQuery)->where('ruang', $this->ruang)->first();
            if ($conflict) {
                $this->roomConflict = [
                    'mk' => $conflict->mataKuliah->nama_mk,
                    'kelas' => $conflict->nama_kelas,
                    'waktu' => substr($conflict->jam_mulai, 0, 5) . '-' . substr($conflict->jam_selesai, 0, 5)
                ];
                $this->formStatus = 'red';
            }
        }

        // B. CEK BENTROK DOSEN
        if ($this->dosen_id) {
            $conflict = (clone $baseQuery)->where('dosen_id', $this->dosen_id)->first();
            if ($conflict) {
                $this->lecturerConflict = [
                    'mk' => $conflict->mataKuliah->nama_mk,
                    'ruang' => $conflict->ruang,
                    'waktu' => substr($conflict->jam_mulai, 0, 5) . '-' . substr($conflict->jam_selesai, 0, 5)
                ];
                $this->formStatus = 'red';
            }
        }
    }

    public function render()
    {
        $kurikulumOptions = Kurikulum::where('prodi_id', $this->filterProdiId)->where('is_active', true)->get();
        
        $formMks = [];
        if ($this->kurikulum_id) {
            $formMks = MataKuliah::join('kurikulum_mata_kuliah', 'master_mata_kuliahs.id', '=', 'kurikulum_mata_kuliah.mata_kuliah_id')
                ->where('kurikulum_mata_kuliah.kurikulum_id', $this->kurikulum_id)
                ->where('nama_mk', 'like', "%{$this->searchMk}%")
                ->select('master_mata_kuliahs.*', 'kurikulum_mata_kuliah.semester_paket')
                ->take(8)->get();
        }

        $dosens = Dosen::with('person')->whereHas('person', function($q) {
            $q->where('nama_lengkap', 'like', "%{$this->searchDosen}%");
        })->take(8)->get();

        $jadwals = JadwalKuliah::with(['mataKuliah', 'dosen.person', 'kurikulum'])
            ->where('tahun_akademik_id', $this->filterSemesterId)
            ->when($this->filterProdiId, function($q) {
                $q->whereHas('mataKuliah', fn($mk) => $mk->where('prodi_id', $this->filterProdiId));
            })
            ->orderBy('hari')->orderBy('jam_mulai')
            ->paginate(15);

        return view('livewire.admin.akademik.jadwal-kuliah-manager', [
            'jadwals' => $jadwals,
            'kurikulumOptions' => $kurikulumOptions,
            'formMks' => $formMks,
            'dosens' => $dosens,
            'prodis' => Prodi::all(),
            'semesters' => TahunAkademik::orderBy('kode_tahun', 'desc')->get()
        ]);
    }

    public function pilihMk($id, $nama) { $this->mata_kuliah_id = $id; $this->selectedMkName = $nama; $this->searchMk = ''; }
    public function pilihDosen($id, $nama) { $this->dosen_id = $id; $this->selectedDosenName = $nama; $this->searchDosen = ''; $this->validateRealTime(); }

    public function save()
    {
        // Re-validate format sebelum simpan
        $this->validateRealTime();
        if ($this->formStatus === 'red') return;

        $timeFormat = 'regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/';

        $this->validate([
            'kurikulum_id' => 'required',
            'mata_kuliah_id' => 'required',
            'dosen_id' => 'required',
            'hari' => 'required',
            'jam_mulai' => ['required', $timeFormat],
            'jam_selesai' => ['required', $timeFormat],
            'ruang' => 'required',
            'nama_kelas' => 'required',
            'kuota_kelas' => 'required|numeric|min:1'
        ], [
            'jam_mulai.regex' => 'Format jam mulai tidak valid (HH:mm).',
            'jam_selesai.regex' => 'Format jam selesai tidak valid (HH:mm).',
            'kuota_kelas.required' => 'Wajib mengisi kuota kelas.',
        ]);

        $data = [
            'tahun_akademik_id' => $this->filterSemesterId,
            'kurikulum_id' => $this->kurikulum_id,
            'mata_kuliah_id' => $this->mata_kuliah_id,
            'dosen_id' => $this->dosen_id,
            'nama_kelas' => strtoupper($this->nama_kelas),
            'hari' => $this->hari,
            'jam_mulai' => $this->jam_mulai,
            'jam_selesai' => $this->jam_selesai,
            'ruang' => strtoupper($this->ruang),
            'kuota_kelas' => $this->kuota_kelas,
        ];

        JadwalKuliah::updateOrCreate(['id' => $this->jadwalId], $data);
        $this->resetForm();
        session()->flash('success', 'Jadwal berhasil diterbitkan/diperbarui.');
    }

    public function edit($id)
    {
        $j = JadwalKuliah::with(['mataKuliah', 'dosen.person'])->find($id);
        $this->jadwalId = $id;
        $this->kurikulum_id = $j->kurikulum_id;
        $this->mata_kuliah_id = $j->mata_kuliah_id;
        $this->dosen_id = $j->dosen_id;
        $this->nama_kelas = $j->nama_kelas;
        $this->hari = $j->hari;
        $this->jam_mulai = substr($j->jam_mulai, 0, 5);
        $this->jam_selesai = substr($j->jam_selesai, 0, 5);
        $this->ruang = $j->ruang;
        $this->kuota_kelas = $j->kuota_kelas;
        $this->selectedMkName = $j->mataKuliah->nama_mk;
        $this->selectedDosenName = $j->dosen->person->nama_lengkap;
        $this->showForm = true;
        $this->validateRealTime();
    }

    public function resetForm()
    {
        $this->reset(['jadwalId', 'mata_kuliah_id', 'dosen_id', 'nama_kelas', 'hari', 'jam_mulai', 'jam_selesai', 'ruang', 'selectedMkName', 'selectedDosenName', 'showForm', 'roomConflict', 'lecturerConflict', 'formStatus', 'timeFormatError']);
        $this->kuota_kelas = 40;
    }
}