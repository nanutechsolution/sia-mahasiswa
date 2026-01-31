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

    // View Config
    public $viewMode = 'list'; // 'list' | 'grid'

    // Filters
    public $filterSemesterId;
    public $filterProdiId;

    // Form Fields
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

    // Searchable Select State
    public $searchMk = '';
    public $searchDosen = '';
    public $selectedMkName = '';
    public $selectedDosenName = '';

    // Smart Features
    public $conflictMessage = null;
    public $cloneSourceSemesterId;
    public $showCloneModal = false;

    // UI State
    public $showForm = false;
    public $editMode = false;

    public function mount()
    {
        $this->filterSemesterId = SistemHelper::idTahunAktif();
        // Default filter ke Prodi pertama agar list tidak kosong
        $firstProdi = Prodi::first();
        $this->filterProdiId = $firstProdi ? $firstProdi->id : null;
    }

    // --- REAL-TIME CONFLICT CHECKER ---
    public function updated($propertyName)
    {
        // Cek bentrok setiap kali parameter waktu/tempat berubah
        if (in_array($propertyName, ['hari', 'jam_mulai', 'jam_selesai', 'ruang', 'dosen_id'])) {
            $this->conflictMessage = $this->cekBentrok(true);
        }
    }

    private function cekBentrok($previewMode = false)
    {
        if (!$this->hari || !$this->jam_mulai || !$this->jam_selesai || !$this->filterSemesterId) return null;

        // Query dasar: Semester sama, Hari sama, Waktu beririsan
        // Logic Irisan: (StartA < EndB) && (EndA > StartB)
        $query = JadwalKuliah::with(['mataKuliah', 'dosen.person'])
            ->where('tahun_akademik_id', $this->filterSemesterId)
            ->where('hari', $this->hari)
            ->where(function ($q) {
                $q->where('jam_mulai', '<', $this->jam_selesai)
                    ->where('jam_selesai', '>', $this->jam_mulai);
            });

        // Exclude diri sendiri saat Edit
        if ($this->editMode && $this->jadwalId) {
            $query->where('id', '!=', $this->jadwalId);
        }

        // 1. Cek Bentrok RUANGAN
        if ($this->ruang) {
            $bentrokRuang = (clone $query)->where('ruang', $this->ruang)->first();
            if ($bentrokRuang) {
                return "⚠️ KONFLIK RUANG: R.{$this->ruang} dipakai {$bentrokRuang->mataKuliah->nama_mk} ({$bentrokRuang->jam_mulai}-{$bentrokRuang->jam_selesai}).";
            }
        }

        // 2. Cek Bentrok DOSEN
        if ($this->dosen_id) {
            $bentrokDosen = (clone $query)->where('dosen_id', $this->dosen_id)->first();
            if ($bentrokDosen) {
                $namaDosen = $bentrokDosen->dosen->person->nama_lengkap ?? 'Dosen';
                return "⚠️ KONFLIK DOSEN: {$namaDosen} mengajar di kelas lain pada jam ini.";
            }
        }

        return null; // Aman
    }

    // --- RENDER & DATA FETCHING ---
    public function render()
    {
        $semesters = TahunAkademik::orderBy('kode_tahun', 'desc')->get();
        $prodis = Prodi::all();
        $programKelasList = ProgramKelas::where('is_active', true)->get();

        // A. Data Search Mata Kuliah (Hanya load jika form terbuka & user mengetik)
        $formMks = [];
        if ($this->showForm && $this->form_prodi_id) {
            // Prioritaskan cari di kurikulum aktif, atau fallback ke master
            $formMks = MataKuliah::where('prodi_id', $this->form_prodi_id)
                ->where(function ($q) {
                    $q->where('nama_mk', 'like', '%' . $this->searchMk . '%')
                        ->orWhere('kode_mk', 'like', '%' . $this->searchMk . '%');
                })
                ->take(10)->get();
        }

        // B. Data Search Dosen
        $dosens = [];
        if ($this->showForm) {
            $dosens = Dosen::join('ref_person', 'trx_dosen.person_id', '=', 'ref_person.id')
                ->where('trx_dosen.is_active', true)
                ->where('ref_person.nama_lengkap', 'like', '%' . $this->searchDosen . '%')
                ->select('trx_dosen.id', 'ref_person.nama_lengkap', 'trx_dosen.nidn')
                ->take(10)->get();
        }

        // C. Data Tabel Jadwal
        $query = JadwalKuliah::with(['mataKuliah', 'dosen.person', 'programKelasAllow'])
            ->where('tahun_akademik_id', $this->filterSemesterId)
            ->when($this->filterProdiId, function ($q) {
                $q->whereHas('mataKuliah', fn($sub) => $sub->where('prodi_id', $this->filterProdiId));
            });

        // Mode Tampilan
        if ($this->viewMode == 'grid') {
            $jadwals = $query->get()->groupBy('hari'); // Grouping untuk visual kalender
        } else {
            $jadwals = $query->orderBy('hari', 'desc')
                ->orderBy('jam_mulai', 'asc')
                ->paginate(10);
        }

        return view('livewire.admin.akademik.jadwal-kuliah-manager', [
            'jadwals' => $jadwals,
            'semesters' => $semesters,
            'prodis' => $prodis,
            'formMks' => $formMks,
            'dosens' => $dosens,
            'programKelasList' => $programKelasList,
            'hariList' => ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']
        ]);
    }

    // --- CRUD ACTIONS ---

    public function create()
    {
        $this->resetForm();
        $this->form_prodi_id = $this->filterProdiId;
        $this->showForm = true;
        $this->editMode = false;
        $this->conflictMessage = null;
    }

    public function edit($id)
    {
        $jadwal = JadwalKuliah::with(['mataKuliah', 'dosen.person'])->find($id);
        if (!$jadwal) return;

        $this->jadwalId = $id;
        $this->form_prodi_id = $jadwal->mataKuliah->prodi_id;
        $this->mata_kuliah_id = $jadwal->mata_kuliah_id;
        $this->dosen_id = $jadwal->dosen_id;

        $this->selectedMkName = "{$jadwal->mataKuliah->kode_mk} - {$jadwal->mataKuliah->nama_mk}";
        $this->selectedDosenName = $jadwal->dosen->person->nama_lengkap ?? '-';

        $this->nama_kelas = $jadwal->nama_kelas;
        $this->hari = $jadwal->hari;
        // Format jam agar sesuai input type time HTML5
        $this->jam_mulai = Carbon::parse($jadwal->jam_mulai)->format('H:i');
        $this->jam_selesai = Carbon::parse($jadwal->jam_selesai)->format('H:i');
        $this->ruang = $jadwal->ruang;
        $this->kuota_kelas = $jadwal->kuota_kelas;
        $this->id_program_kelas_allow = $jadwal->id_program_kelas_allow;

        $this->editMode = true;
        $this->showForm = true;
        $this->conflictMessage = null;
    }

    public function save()
    {
        $this->validate([
            'form_prodi_id' => 'required',
            'mata_kuliah_id' => 'required',
            'dosen_id' => 'required',
            'nama_kelas' => 'required|max:10',
            'hari' => 'required',
            'jam_mulai' => ['required', 'date_format:H:i'], 
            'jam_selesai' => ['required', 'date_format:H:i', 'after:jam_mulai'], 
            'ruang' => 'required',
            'kuota_kelas' => 'required|integer|min:1',
        ], [
            'form_prodi_id.required' => 'Program studi harus dipilih.',
            'mata_kuliah_id.required' => 'Mata kuliah harus dipilih.',
            'dosen_id.required' => 'Dosen pengampu harus dipilih.',
            'nama_kelas.required' => 'Nama kelas wajib diisi.',
            'nama_kelas.max' => 'Nama kelas maksimal 10 karakter.',
            'hari.required' => 'Hari wajib dipilih.',
            'jam_mulai.date_format' => 'Format jam mulai salah (Harus HH:MM, misal 08:00)',
            'jam_selesai.date_format' => 'Format jam selesai salah (Harus HH:MM, misal 10:00)',
            'jam_mulai.required' => 'Jam mulai wajib diisi.',
            'jam_selesai.required' => 'Jam selesai wajib diisi.',
            'jam_selesai.after' => 'Jam selesai harus setelah jam mulai.',
            'ruang.required' => 'Ruang wajib diisi.',
            'kuota_kelas.required' => 'Kuota kelas wajib diisi.',
            'kuota_kelas.integer' => 'Kuota kelas harus berupa angka.',
            'kuota_kelas.min' => 'Kuota kelas minimal 1.',
        ]);


        // Cek bentrok sekali lagi di server side sebelum simpan
        if ($msg = $this->cekBentrok()) {
            $this->conflictMessage = $msg;
            return;
        }

        $data = [
            'tahun_akademik_id' => $this->filterSemesterId,
            'mata_kuliah_id' => $this->mata_kuliah_id,
            'dosen_id' => $this->dosen_id,
            'nama_kelas' => strtoupper($this->nama_kelas),
            'hari' => $this->hari,
            'jam_mulai' => $this->jam_mulai,
            'jam_selesai' => $this->jam_selesai,
            'ruang' => strtoupper($this->ruang),
            'kuota_kelas' => $this->kuota_kelas,
            'id_program_kelas_allow' => $this->id_program_kelas_allow ?: null,
        ];

        if ($this->editMode) JadwalKuliah::find($this->jadwalId)->update($data);
        else JadwalKuliah::create($data);

        $this->resetForm();
        $this->showForm = false;
        session()->flash('success', 'Jadwal perkuliahan berhasil disimpan.');
    }

    public function delete($id)
    {
        JadwalKuliah::destroy($id);
        session()->flash('success', 'Jadwal berhasil dihapus.');
    }

    // --- CLONE UTILITIES ---

    public function openCloneModal()
    {
        $this->showCloneModal = true;
    }

    public function cloneSchedule()
    {
        $this->validate(['cloneSourceSemesterId' => 'required|different:filterSemesterId']);

        $source = JadwalKuliah::where('tahun_akademik_id', $this->cloneSourceSemesterId)
            ->whereHas('mataKuliah', fn($q) => $q->where('prodi_id', $this->filterProdiId))
            ->get();

        if ($source->isEmpty()) {
            $this->addError('cloneSourceSemesterId', 'Tidak ada jadwal di semester sumber untuk Prodi ini.');
            return;
        }

        DB::transaction(function () use ($source) {
            foreach ($source as $old) {
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

        session()->flash('success', "Berhasil menyalin {$source->count()} jadwal kelas.");
        $this->showCloneModal = false;
    }

    // --- HELPER HANDLERS ---

    public function pilihMk($id, $nama, $kode, $sks)
    {
        $this->mata_kuliah_id = $id;
        $this->selectedMkName = "$kode - $nama ($sks SKS)";
        $this->searchMk = '';
    }

    public function pilihDosen($id, $nama)
    {
        $this->dosen_id = $id;
        $this->selectedDosenName = $nama;
        $this->searchDosen = '';
        $this->updated('dosen_id'); // Trigger cek bentrok
    }

    public function resetForm()
    {
        $this->reset([
            'jadwalId',
            'form_prodi_id',
            'mata_kuliah_id',
            'dosen_id',
            'nama_kelas',
            'hari',
            'jam_mulai',
            'jam_selesai',
            'ruang',
            'kuota_kelas',
            'id_program_kelas_allow',
            'searchMk',
            'searchDosen',
            'selectedMkName',
            'selectedDosenName',
            'conflictMessage'
        ]);
        $this->kuota_kelas = 40;
    }

    public function batal()
    {
        $this->showForm = false;
        $this->resetForm();
    }
    public function resetSearch()
    {
        $this->reset(['searchMk', 'searchDosen', 'selectedMkName', 'selectedDosenName']);
    }
}
