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

class JadwalKuliahManager extends Component
{
    use WithPagination;

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

    // Searchable Select State
    public $searchMk = '';
    public $searchDosen = '';
    public $selectedMkName = '';
    public $selectedDosenName = '';

    public $showForm = false;
    public $editMode = false;

    // [BARU] Custom Error Messages Bahasa Indonesia
    protected $messages = [
        'form_prodi_id.required' => 'Mohon pilih Program Studi terlebih dahulu.',
        'mata_kuliah_id.required' => 'Mata Kuliah wajib dipilih.',
        'dosen_id.required' => 'Dosen Pengampu wajib dipilih dari daftar.',
        'nama_kelas.required' => 'Identitas Kelas (A/B/Pagi) wajib diisi.',
        'nama_kelas.max' => 'Nama Kelas terlalu panjang (maksimal 10 karakter).',
        'hari.required' => 'Hari perkuliahan wajib ditentukan.',
        'jam_mulai.required' => 'Jam mulai perkuliahan wajib diisi.',
        'jam_selesai.required' => 'Jam selesai perkuliahan wajib diisi.',
        'jam_selesai.after' => 'Jam selesai harus lebih akhir dari jam mulai.',
        'ruang.required' => 'Ruangan kelas wajib diisi.',
        'kuota_kelas.required' => 'Kuota mahasiswa wajib diisi.',
        'kuota_kelas.min' => 'Kuota kelas minimal harus 1 mahasiswa.',
        'kuota_kelas.integer' => 'Kuota harus berupa angka bulat.',
    ];

    public function mount()
    {
        $this->filterSemesterId = SistemHelper::idTahunAktif();
        $firstProdi = Prodi::first();
        $this->filterProdiId = $firstProdi ? $firstProdi->id : null;
    }

    public function updatedFormProdiId()
    {
        $this->reset(['mata_kuliah_id', 'selectedMkName', 'searchMk']);
    }

    public function updatedShowForm($value)
    {
        if (!$value) {
            $this->resetSearch();
        }
    }

    public function resetSearch()
    {
        $this->reset(['searchMk', 'searchDosen', 'selectedMkName', 'selectedDosenName']);
    }

    public function pilihMk($id, $nama, $kode, $sks)
    {
        $this->mata_kuliah_id = $id;
        $this->selectedMkName = "{$kode} - {$nama} ({$sks} SKS)";
        $this->searchMk = '';
    }

    public function pilihDosen($id, $nama)
    {
        $this->dosen_id = $id;
        $this->selectedDosenName = $nama;
        $this->searchDosen = '';
    }

    public function render()
    {
        $semesters = TahunAkademik::orderBy('kode_tahun', 'desc')->get();
        $prodis = Prodi::all();
        $programKelasList = ProgramKelas::where('is_active', true)->get();

        $formMks = [];
        if ($this->showForm && $this->form_prodi_id) {
            $formMks = MataKuliah::where('prodi_id', $this->form_prodi_id)
                ->where(function($q) {
                    $q->where('nama_mk', 'like', '%'.$this->searchMk.'%')
                      ->orWhere('kode_mk', 'like', '%'.$this->searchMk.'%');
                })
                ->orderBy('nama_mk')
                ->take(10)
                ->get();
        }

        $dosens = [];
        if ($this->showForm) {
            $dosens = Dosen::join('ref_person', 'trx_dosen.person_id', '=', 'ref_person.id')
                ->where('trx_dosen.is_active', true)
                ->where('ref_person.nama_lengkap', 'like', '%'.$this->searchDosen.'%')
                ->orderBy('ref_person.nama_lengkap', 'asc')
                ->select('trx_dosen.id', 'ref_person.nama_lengkap', 'trx_dosen.nidn')
                ->take(10)
                ->get();
        }

        $jadwals = JadwalKuliah::with(['mataKuliah', 'dosen', 'programKelasAllow'])
            ->where('tahun_akademik_id', $this->filterSemesterId)
            ->whereHas('mataKuliah', function($q) {
                $q->where('prodi_id', $this->filterProdiId);
            })
            ->orderBy('hari', 'desc')
            ->orderBy('jam_mulai', 'asc')
            ->paginate(10);

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

    public function create()
    {
        $this->resetForm();
        $this->resetSearch();
        $this->form_prodi_id = $this->filterProdiId; 
        $this->showForm = true;
        $this->editMode = false;
    }

    public function edit($id)
    {
        $jadwal = JadwalKuliah::with(['mataKuliah', 'dosen'])->find($id);
        $this->jadwalId = $id;
        
        $this->form_prodi_id = $jadwal->mataKuliah->prodi_id; 
        
        $this->mata_kuliah_id = $jadwal->mata_kuliah_id;
        $this->dosen_id = $jadwal->dosen_id;
        
        $this->selectedMkName = "{$jadwal->mataKuliah->kode_mk} - {$jadwal->mataKuliah->nama_mk} ({$jadwal->mataKuliah->sks_default} SKS)";
        $this->selectedDosenName = $jadwal->dosen->nama_lengkap_gelar;

        $this->nama_kelas = $jadwal->nama_kelas;
        $this->hari = $jadwal->hari;
        $this->jam_mulai = Carbon::parse($jadwal->jam_mulai)->format('H:i');
        $this->jam_selesai = Carbon::parse($jadwal->jam_selesai)->format('H:i');
        $this->ruang = $jadwal->ruang;
        $this->kuota_kelas = $jadwal->kuota_kelas;
        $this->id_program_kelas_allow = $jadwal->id_program_kelas_allow;

        $this->editMode = true;
        $this->showForm = true;
    }

    public function save()
    {
        $this->validate([
            'form_prodi_id' => 'required',
            'mata_kuliah_id' => 'required',
            'dosen_id' => 'required',
            'nama_kelas' => 'required|max:10',
            'hari' => 'required',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required|after:jam_mulai',
            'ruang' => 'required',
            'kuota_kelas' => 'required|integer|min:1',
        ]);

        if ($pesanError = $this->cekBentrok()) {
            session()->flash('error', $pesanError);
            $this->dispatch('scroll-to-top'); 
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

        if ($this->editMode) {
            JadwalKuliah::find($this->jadwalId)->update($data);
            session()->flash('success', 'Jadwal berhasil diperbarui.');
        } else {
            JadwalKuliah::create($data);
            session()->flash('success', 'Jadwal baru berhasil ditambahkan.');
        }

        $this->dispatch('scroll-to-top'); 
        $this->resetForm();
        $this->showForm = false;
    }

    private function cekBentrok()
    {
        $query = JadwalKuliah::with(['mataKuliah', 'dosen'])
            ->where('tahun_akademik_id', $this->filterSemesterId)
            ->where('hari', $this->hari)
            ->where(function($q) {
                $q->where('jam_mulai', '<', $this->jam_selesai)
                  ->where('jam_selesai', '>', $this->jam_mulai);
            });

        if ($this->editMode) {
            $query->where('id', '!=', $this->jadwalId);
        }

        $bentrokRuang = (clone $query)->where('ruang', $this->ruang)->first();
        if ($bentrokRuang) {
            $jam = Carbon::parse($bentrokRuang->jam_mulai)->format('H:i') . ' - ' . Carbon::parse($bentrokRuang->jam_selesai)->format('H:i');
            return "KONFLIK RUANGAN: Ruang {$this->ruang} sudah digunakan oleh mata kuliah {$bentrokRuang->mataKuliah->nama_mk} (Kelas {$bentrokRuang->nama_kelas}) pada jam {$jam}.";
        }

        $bentrokDosen = (clone $query)->where('dosen_id', $this->dosen_id)->first();
        if ($bentrokDosen) {
            $jam = Carbon::parse($bentrokDosen->jam_mulai)->format('H:i') . ' - ' . Carbon::parse($bentrokDosen->jam_selesai)->format('H:i');
            $namaDosen = $bentrokDosen->dosen->nama_lengkap_gelar ?? 'Dosen';
            return "KONFLIK DOSEN: {$namaDosen} sudah memiliki jadwal mengajar MK {$bentrokDosen->mataKuliah->nama_mk} di tempat lain pada jam {$jam}.";
        }
        
        return null;
    }

    public function delete($id)
    {
        JadwalKuliah::destroy($id);
        session()->flash('success', 'Jadwal berhasil dihapus.');
    }

    public function resetForm()
    {
        $this->reset(['jadwalId', 'form_prodi_id', 'mata_kuliah_id', 'dosen_id', 'nama_kelas', 'hari', 'jam_mulai', 'jam_selesai', 'ruang', 'kuota_kelas', 'id_program_kelas_allow', 'searchMk', 'searchDosen', 'selectedMkName', 'selectedDosenName']);
        $this->kuota_kelas = 40;
    }

    public function batal()
    {
        $this->showForm = false;
        $this->resetForm();
    }
}