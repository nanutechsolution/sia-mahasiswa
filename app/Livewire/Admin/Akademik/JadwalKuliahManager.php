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
use App\Models\RefRuang;
use App\Helpers\SistemHelper;
use Illuminate\Support\Facades\DB;

class JadwalKuliahManager extends Component
{
    use WithPagination;

    // Filter Global
    public $filterSemesterId;
    public $filterProdiId;

    // Form State
    public $jadwalId;
    public $kurikulum_id;
    public $mata_kuliah_id;
    public $dosen_ids = []; 
    public $koordinator_id; 
    public $nama_kelas;
    public $hari;
    public $jam_mulai;
    public $jam_selesai;
    public $ruang_id;
    public $kuota_kelas = 40;

    // Search & UI States
    public $searchMk = '';
    public $searchDosen = '';
    public $searchRuang = ''; // Tambahan state pencarian ruang
    public $selectedMkName = '';
    public $selectedRuangName = ''; // Tambahan state nama ruang terpilih
    public $selectedDosenList = []; 
    public $showForm = false;

    // Validation States
    public $roomConflict = null;
    public $lecturerConflict = [];
    public $formStatus = 'neutral'; 

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

        if (in_array($propertyName, ['hari', 'jam_mulai', 'jam_selesai', 'ruang_id', 'dosen_ids', 'koordinator_id'])) {
            $this->validateRealTime();
        }
    }

    protected function validateRealTime()
    {
        $this->roomConflict = null;
        $this->lecturerConflict = [];
        $this->formStatus = 'green';

        if (!$this->hari || !$this->jam_mulai || !$this->jam_selesai) {
            $this->formStatus = 'neutral';
            return;
        }

        $baseQuery = JadwalKuliah::where('tahun_akademik_id', $this->filterSemesterId)
            ->where('hari', $this->hari)
            ->where(function ($q) {
                $q->whereBetween('jam_mulai', [$this->jam_mulai, $this->jam_selesai])
                    ->orWhereBetween('jam_selesai', [$this->jam_mulai, $this->jam_selesai])
                    ->orWhere(function ($sub) {
                        $sub->where('jam_mulai', '<=', $this->jam_mulai)
                            ->where('jam_selesai', '>=', $this->jam_selesai);
                    });
            });

        if ($this->jadwalId) {
            $baseQuery->where('id', '!=', $this->jadwalId);
        }

        if ($this->ruang_id) {
            $conflict = (clone $baseQuery)->where('ruang_id', $this->ruang_id)->first();
            if ($conflict) {
                $this->roomConflict = [
                    'mk' => $conflict->mataKuliah->nama_mk,
                    'kelas' => $conflict->nama_kelas,
                    'waktu' => substr($conflict->jam_mulai, 0, 5) . '-' . substr($conflict->jam_selesai, 0, 5)
                ];
                $this->formStatus = 'red';
            }
        }

        if (!empty($this->dosen_ids)) {
            foreach ($this->dosen_ids as $d_id) {
                $conflict = (clone $baseQuery)->whereHas('dosens', fn($q) => $q->where('dosen_id', $d_id))->first();
                if ($conflict) {
                    $dosen = Dosen::find($d_id);
                    $this->lecturerConflict[] = [
                        'nama' => $dosen->person->nama_lengkap,
                        'mk' => $conflict->mataKuliah->nama_mk,
                        'waktu' => substr($conflict->jam_mulai, 0, 5) . '-' . substr($conflict->jam_selesai, 0, 5)
                    ];
                    $this->formStatus = 'red';
                }
            }
        }
    }

    public function render()
    {
        $kurikulumOptions = Kurikulum::where('prodi_id', $this->filterProdiId)->where('is_active', true)->get();
        
        // Fitur Search Ruangan
        $ruangOptions = RefRuang::where('is_active', true)
            ->when($this->searchRuang, function($q) {
                $q->where('nama_ruang', 'like', "%{$this->searchRuang}%")
                  ->orWhere('kode_ruang', 'like', "%{$this->searchRuang}%");
            })
            ->orderBy('kode_ruang')
            ->take(10)
            ->get();

        $formMks = [];
        if ($this->kurikulum_id) {
            $formMks = MataKuliah::join('kurikulum_mata_kuliah', 'master_mata_kuliahs.id', '=', 'kurikulum_mata_kuliah.mata_kuliah_id')
                ->where('kurikulum_mata_kuliah.kurikulum_id', $this->kurikulum_id)
                ->where('nama_mk', 'like', "%{$this->searchMk}%")
                ->select('master_mata_kuliahs.*')
                ->take(8)->get();
        }

        $dosens = Dosen::with('person')->whereHas('person', function ($q) {
            $q->where('nama_lengkap', 'like', "%{$this->searchDosen}%");
        })->whereNotIn('id', $this->dosen_ids)->take(8)->get();

        $jadwals = JadwalKuliah::with(['mataKuliah', 'dosens.person', 'kurikulum', 'ruang'])
            ->where('tahun_akademik_id', $this->filterSemesterId)
            ->when($this->filterProdiId, function ($q) {
                $q->whereHas('mataKuliah', fn($mk) => $mk->where('prodi_id', $this->filterProdiId));
            })
            ->orderBy('hari')->orderBy('jam_mulai')
            ->paginate(15);

        return view('livewire.admin.akademik.jadwal-kuliah-manager', [
            'jadwals' => $jadwals,
            'kurikulumOptions' => $kurikulumOptions,
            'ruangOptions' => $ruangOptions,
            'formMks' => $formMks,
            'dosens' => $dosens,
            'prodis' => Prodi::all(),
            'semesters' => TahunAkademik::orderBy('kode_tahun', 'desc')->get()
        ]);
    }

    public function pilihMk($id, $nama)
    {
        $this->mata_kuliah_id = $id;
        $this->selectedMkName = $nama;
        $this->searchMk = '';
        $this->validateRealTime();
    }

    /**
     * Method untuk memilih Ruangan via Search
     */
    public function pilihRuang($id, $nama)
    {
        $this->ruang_id = $id;
        $this->selectedRuangName = $nama;
        $this->searchRuang = '';
        $this->validateRealTime();
    }

    public function tambahDosen($id, $nama)
    {
        if (!in_array($id, $this->dosen_ids)) {
            $this->dosen_ids[] = $id;
            $this->selectedDosenList[] = ['id' => $id, 'nama' => $nama];
            if (count($this->dosen_ids) === 1) {
                $this->koordinator_id = $id;
            }
        }
        $this->searchDosen = '';
        $this->validateRealTime();
    }

    public function hapusDosen($id)
    {
        $this->dosen_ids = array_values(array_filter($this->dosen_ids, fn($val) => $val != $id));
        $this->selectedDosenList = array_values(array_filter($this->selectedDosenList, fn($val) => $val['id'] != $id));
        if ($this->koordinator_id == $id) {
            $this->koordinator_id = $this->dosen_ids[0] ?? null;
        }
        $this->validateRealTime();
    }

    public function save()
    {
        $this->validateRealTime();
        if ($this->formStatus === 'red' || empty($this->dosen_ids)) return;

        $this->validate([
            'kurikulum_id' => 'required',
            'mata_kuliah_id' => 'required',
            'hari' => 'required',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'ruang_id' => 'required',
            'nama_kelas' => 'required|max:20',
            'kuota_kelas' => 'required|numeric|min:1'
        ]);

        DB::transaction(function () {
            $jadwal = JadwalKuliah::updateOrCreate(['id' => $this->jadwalId], [
                'tahun_akademik_id' => $this->filterSemesterId,
                'kurikulum_id' => $this->kurikulum_id,
                'mata_kuliah_id' => $this->mata_kuliah_id,
                'nama_kelas' => strtoupper($this->nama_kelas),
                'hari' => $this->hari,
                'jam_mulai' => $this->jam_mulai,
                'jam_selesai' => $this->jam_selesai,
                'ruang_id' => $this->ruang_id,
                'kuota_kelas' => $this->kuota_kelas,
            ]);

            $syncData = [];
            foreach ($this->dosen_ids as $id) {
                $syncData[$id] = [
                    'is_koordinator' => ($id == $this->koordinator_id),
                    'rencana_tatap_muka' => 14 
                ];
            }
            $jadwal->dosens()->sync($syncData);
        });

        $this->resetForm();
        session()->flash('success', 'Jadwal Kuliah berhasil diterbitkan ke sistem.');
    }

    public function edit($id)
    {
        $j = JadwalKuliah::with(['mataKuliah', 'dosens.person', 'ruang'])->find($id);
        if (!$j) return;

        $this->jadwalId = $id;
        $this->kurikulum_id = $j->kurikulum_id;
        $this->mata_kuliah_id = $j->mata_kuliah_id;
        $this->nama_kelas = $j->nama_kelas;
        $this->hari = $j->hari;
        $this->jam_mulai = substr($j->jam_mulai, 0, 5);
        $this->jam_selesai = substr($j->jam_selesai, 0, 5);
        $this->ruang_id = $j->ruang_id;
        $this->kuota_kelas = $j->kuota_kelas;
        $this->selectedMkName = $j->mataKuliah->nama_mk;
        $this->selectedRuangName = $j->ruang ? "[{$j->ruang->kode_ruang}] {$j->ruang->nama_ruang}" : '';
        
        $this->dosen_ids = [];
        $this->selectedDosenList = [];
        foreach ($j->dosens as $d) {
            $this->dosen_ids[] = $d->id;
            $this->selectedDosenList[] = ['id' => $d->id, 'nama' => $d->person->nama_lengkap];
            if ($d->pivot->is_koordinator) {
                $this->koordinator_id = $d->id;
            }
        }

        $this->showForm = true;
        $this->validateRealTime();
    }

    public function resetForm()
    {
        $this->reset([
            'jadwalId', 'mata_kuliah_id', 'ruang_id', 'dosen_ids', 'selectedDosenList', 
            'koordinator_id', 'nama_kelas', 'hari', 'jam_mulai', 'jam_selesai', 
            'selectedMkName', 'selectedRuangName', 'showForm', 'roomConflict', 
            'lecturerConflict', 'formStatus', 'searchMk', 'searchDosen', 'searchRuang'
        ]);
        $this->kuota_kelas = 40;
    }
}