<?php

namespace App\Livewire\Admin\Akademik;

use Livewire\Component;
use Livewire\WithPagination;
use App\Domains\Akademik\Models\JadwalKuliah;
use App\Domains\Core\Models\TahunAkademik;
use App\Domains\Core\Models\Prodi;
use App\Domains\Akademik\Models\MataKuliah;
use App\Domains\Akademik\Models\Dosen;
use App\Domains\Core\Models\ProgramKelas;
use App\Helpers\SistemHelper;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class JadwalKuliahManager extends Component
{
    use WithPagination;

    // View State
    public $viewMode = 'list'; // 'list' atau 'grid'
    
    // Filter Table State
    public $filterSemesterId;
    public $filterProdiId;

    // Form State
    public $jadwalId;
    public $form_prodi_id; 
    public $mata_kuliah_id;
    public $dosen_id;
    public $nama_kelas;
    public $hari;
    public $jam_mulai;
    public $jam_selesai;
    public $ruang;
    public $kuota_kelas = 40;
    public $id_program_kelas_allow;

    // Smart Features State
    public $conflictMessage = null; // Pesan bentrok real-time
    public $cloneSourceSemesterId;  // ID Semester asal untuk cloning
    public $showCloneModal = false;

    // Searchable Select State
    public $searchMk = '';
    public $searchDosen = '';
    public $selectedMkName = '';
    public $selectedDosenName = '';

    public $showForm = false;
    public $editMode = false;

    public function mount()
    {
        $this->filterSemesterId = SistemHelper::idTahunAktif();
        $firstProdi = Prodi::first();
        $this->filterProdiId = $firstProdi ? $firstProdi->id : null;
    }

    // --- REAL-TIME CONFLICT RADAR ---
    public function updated($propertyName)
    {
        // Cek bentrok setiap kali input Waktu/Ruang/Dosen berubah
        if (in_array($propertyName, ['hari', 'jam_mulai', 'jam_selesai', 'ruang', 'dosen_id'])) {
            $this->conflictMessage = $this->cekBentrok(true); // true = return message only, dont block yet
        }
    }

    private function cekBentrok($previewMode = false)
    {
        if (!$this->hari || !$this->jam_mulai || !$this->jam_selesai) return null;

        $query = JadwalKuliah::with(['mataKuliah', 'dosen.person'])
            ->where('tahun_akademik_id', $this->filterSemesterId)
            ->where('hari', $this->hari)
            ->where(function($q) {
                $q->where(function($sub) {
                    $sub->where('jam_mulai', '<', $this->jam_selesai)
                        ->where('jam_selesai', '>', $this->jam_mulai);
                });
            });

        if ($this->editMode) {
            $query->where('id', '!=', $this->jadwalId);
        }

        // 1. Cek Bentrok Ruangan
        if ($this->ruang) {
            $bentrokRuang = (clone $query)->where('ruang', $this->ruang)->first();
            if ($bentrokRuang) {
                return "⚠️ KONFLIK RUANG: Ruang {$this->ruang} dipakai {$bentrokRuang->mataKuliah->nama_mk} ({$bentrokRuang->jam_mulai}-{$bentrokRuang->jam_selesai}).";
            }
        }

        // 2. Cek Bentrok Dosen
        if ($this->dosen_id) {
            $bentrokDosen = (clone $query)->where('dosen_id', $this->dosen_id)->first();
            if ($bentrokDosen) {
                $namaDosen = $bentrokDosen->dosen->person->nama_lengkap ?? 'Dosen';
                return "⚠️ KONFLIK DOSEN: {$namaDosen} sedang mengajar {$bentrokDosen->mataKuliah->nama_mk} di jam tersebut.";
            }
        }
        
        return null; // Aman
    }

    // --- CLONE SCHEDULE FEATURE ---
    public function openCloneModal() { $this->showCloneModal = true; }
    
    public function cloneSchedule()
    {
        $this->validate(['cloneSourceSemesterId' => 'required|different:filterSemesterId']);

        $sourceJadwals = JadwalKuliah::where('tahun_akademik_id', $this->cloneSourceSemesterId)
            ->whereHas('mataKuliah', fn($q) => $q->where('prodi_id', $this->filterProdiId))
            ->get();

        if ($sourceJadwals->isEmpty()) {
            $this->addError('cloneSourceSemesterId', 'Tidak ada jadwal di semester sumber untuk prodi ini.');
            return;
        }

        DB::transaction(function () use ($sourceJadwals) {
            foreach ($sourceJadwals as $old) {
                // Replikasi jadwal ke semester aktif
                JadwalKuliah::create([
                    'tahun_akademik_id' => $this->filterSemesterId,
                    'mata_kuliah_id' => $old->mata_kuliah_id,
                    'dosen_id' => $old->dosen_id,
                    'nama_kelas' => $old->nama_kelas,
                    'hari' => $old->hari,
                    'jam_mulai' => $old->jam_mulai,
                    'jam_selesai' => $old->jam_selesai,
                    'ruang' => $old->ruang,
                    'kuota_kelas' => $old->kuota_kelas,
                    'id_program_kelas_allow' => $old->id_program_kelas_allow
                ]);
            }
        });

        session()->flash('success', "Berhasil menyalin {$sourceJadwals->count()} jadwal kelas.");
        $this->showCloneModal = false;
        $this->viewMode = 'list';
    }


    // --- RENDER & GRID VIEW ---
    public function render()
    {
        $semesters = TahunAkademik::orderBy('kode_tahun', 'desc')->get();
        $prodis = Prodi::all();
        $programKelasList = ProgramKelas::where('is_active', true)->get();

        // Data utk Dropdown
        $formMks = [];
        if ($this->showForm && $this->form_prodi_id) {
            $formMks = MataKuliah::where('prodi_id', $this->form_prodi_id)
                ->where(function($q) {
                    $q->where('nama_mk', 'like', '%'.$this->searchMk.'%')->orWhere('kode_mk', 'like', '%'.$this->searchMk.'%');
                })->take(10)->get();
        }

        $dosens = [];
        if ($this->showForm) {
            $dosens = Dosen::join('ref_person', 'trx_dosen.person_id', '=', 'ref_person.id')
                ->where('trx_dosen.is_active', true)
                ->where('ref_person.nama_lengkap', 'like', '%'.$this->searchDosen.'%')
                ->orderBy('ref_person.nama_lengkap', 'asc')
                ->select('trx_dosen.id', 'ref_person.nama_lengkap', 'trx_dosen.nidn')->take(10)->get();
        }

        // Query Utama
        $query = JadwalKuliah::with(['mataKuliah', 'dosen.person', 'programKelasAllow'])
            ->where('tahun_akademik_id', $this->filterSemesterId)
            ->whereHas('mataKuliah', function($q) {
                $q->where('prodi_id', $this->filterProdiId);
            });

        // Mode List (Pagination) vs Mode Grid (All Data)
        if ($this->viewMode == 'grid') {
            $jadwals = $query->get()->groupBy('hari'); // Group by Hari untuk tampilan kalender
        } else {
            $jadwals = $query->orderBy('hari', 'desc')->orderBy('jam_mulai', 'asc')->paginate(10);
        }

        return view('livewire.admin.akademik.jadwal-kuliah-manager', [
            'jadwals' => $jadwals,
            'semesters' => $semesters,
            'prodis' => $prodis,
            'formMks' => $formMks,
            'dosens' => $dosens,
            'programKelasList' => $programKelasList,
            'hariList' => ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu']
        ]);
    }

    public function create() {
        $this->resetForm();
        $this->resetSearch();
        $this->form_prodi_id = $this->filterProdiId; 
        $this->showForm = true;
        $this->editMode = false;
        $this->conflictMessage = null;
    }

    public function save() {
        $this->validate([
            'form_prodi_id' => 'required', 'mata_kuliah_id' => 'required', 'dosen_id' => 'required',
            'nama_kelas' => 'required', 'hari' => 'required', 'jam_mulai' => 'required', 'jam_selesai' => 'required|after:jam_mulai',
            'ruang' => 'required', 'kuota_kelas' => 'required|integer|min:1',
        ]);

        if ($msg = $this->cekBentrok()) {
            $this->conflictMessage = $msg; // Tampilkan pesan di UI
            return; 
        }

        $data = [
            'tahun_akademik_id' => $this->filterSemesterId,
            'mata_kuliah_id' => $this->mata_kuliah_id,
            'dosen_id' => $this->dosen_id,
            'nama_kelas' => $this->nama_kelas,
            'hari' => $this->hari,
            'jam_mulai' => $this->jam_mulai,
            'jam_selesai' => $this->jam_selesai,
            'ruang' => $this->ruang,
            'kuota_kelas' => $this->kuota_kelas,
            'id_program_kelas_allow' => $this->id_program_kelas_allow ?: null, 
        ];

        if($this->editMode) JadwalKuliah::find($this->jadwalId)->update($data);
        else JadwalKuliah::create($data);

        $this->resetForm();
        $this->showForm = false;
        session()->flash('success', 'Jadwal berhasil disimpan.');
    }

    // Helper functions
    public function pilihMk($id, $nama, $kode, $sks) { $this->mata_kuliah_id = $id; $this->selectedMkName = "$kode - $nama ($sks SKS)"; $this->searchMk = ''; }
    public function pilihDosen($id, $nama) { $this->dosen_id = $id; $this->selectedDosenName = $nama; $this->searchDosen = ''; $this->updated('dosen_id'); } // Trigger cek bentrok
    public function delete($id) { JadwalKuliah::destroy($id); session()->flash('success', 'Jadwal dihapus.'); }
    public function resetForm() { $this->reset(['jadwalId', 'form_prodi_id', 'mata_kuliah_id', 'dosen_id', 'nama_kelas', 'hari', 'jam_mulai', 'jam_selesai', 'ruang', 'kuota_kelas', 'id_program_kelas_allow', 'searchMk', 'searchDosen', 'selectedMkName', 'selectedDosenName', 'conflictMessage']); $this->kuota_kelas = 40; }
    public function batal() { $this->showForm = false; $this->resetForm(); }
    public function updatedShowForm($v) { if(!$v) $this->resetSearch(); }
    public function resetSearch() { $this->reset(['searchMk', 'searchDosen', 'selectedMkName', 'selectedDosenName']); }
    public function updatedFormProdiId() { $this->reset(['mata_kuliah_id', 'selectedMkName', 'searchMk']); }
    
    public function edit($id) {
        $jadwal = JadwalKuliah::with(['mataKuliah', 'dosen.person'])->find($id);
        $this->jadwalId = $id;
        $this->form_prodi_id = $jadwal->mataKuliah->prodi_id; 
        $this->mata_kuliah_id = $jadwal->mata_kuliah_id;
        $this->dosen_id = $jadwal->dosen_id;
        $this->selectedMkName = "{$jadwal->mataKuliah->kode_mk} - {$jadwal->mataKuliah->nama_mk} ({$jadwal->mataKuliah->sks_default} SKS)";
        $this->selectedDosenName = $jadwal->dosen->person->nama_lengkap ?? '-';
        $this->nama_kelas = $jadwal->nama_kelas;
        $this->hari = $jadwal->hari;
        $this->jam_mulai = Carbon::parse($jadwal->jam_mulai)->format('H:i');
        $this->jam_selesai = Carbon::parse($jadwal->jam_selesai)->format('H:i');
        $this->ruang = $jadwal->ruang;
        $this->kuota_kelas = $jadwal->kuota_kelas;
        $this->id_program_kelas_allow = $jadwal->id_program_kelas_allow;
        $this->editMode = true; $this->showForm = true; $this->conflictMessage = null;
    }
}