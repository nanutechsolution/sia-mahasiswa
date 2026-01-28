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
use Carbon\Carbon; // Pastikan import Carbon

class JadwalKuliahManager extends Component
{
    use WithPagination;

    // Filter State
    public $filterSemesterId;
    public $filterProdiId;

    // Form State
    public $jadwalId;
    public $mata_kuliah_id;
    public $dosen_id;
    public $nama_kelas;
    public $hari;
    public $jam_mulai;
    public $jam_selesai;
    public $ruang;
    public $kuota_kelas = 40;
    public $id_program_kelas_allow;

    public $showForm = false;
    public $editMode = false;

    public function mount()
    {
        $this->filterSemesterId = SistemHelper::idTahunAktif();
        $firstProdi = Prodi::first();
        $this->filterProdiId = $firstProdi ? $firstProdi->id : null;
    }

    public function render()
    {
        $semesters = TahunAkademik::orderBy('kode_tahun', 'desc')->get();
        $prodis = Prodi::all();
        $programKelasList = ProgramKelas::where('is_active', true)->get();

        $mks = [];
        $dosens = [];
        
        if ($this->filterProdiId) {
            $mks = MataKuliah::where('prodi_id', $this->filterProdiId)->orderBy('nama_mk')->get();
            $dosens = Dosen::where('is_active', true)->orderBy('nama_lengkap_gelar')->get();
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
            'mks' => $mks,
            'dosens' => $dosens,
            'programKelasList' => $programKelasList,
            'hariList' => ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu']
        ]);
    }

    public function create()
    {
        $this->resetForm();
        $this->showForm = true;
        $this->editMode = false;
    }

    public function edit($id)
    {
        $jadwal = JadwalKuliah::find($id);
        $this->jadwalId = $id;
        $this->mata_kuliah_id = $jadwal->mata_kuliah_id;
        $this->dosen_id = $jadwal->dosen_id;
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
            'mata_kuliah_id' => 'required',
            'dosen_id' => 'required',
            'nama_kelas' => 'required|max:10',
            'hari' => 'required',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required|after:jam_mulai',
            'ruang' => 'required',
            'kuota_kelas' => 'required|integer|min:1',
        ]);

        // CEK BENTROK DETIL
        if ($pesanError = $this->cekBentrok()) {
            session()->flash('error', $pesanError);
            // Trigger event browser untuk scroll ke atas (ditangkap oleh JS di view)
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

        $this->dispatch('scroll-to-top'); // Scroll ke atas juga saat sukses
        $this->resetForm();
        $this->showForm = false;
    }

    /**
     * Logika Deteksi Bentrok dengan Pesan Detail
     */
    private function cekBentrok()
    {
        $query = JadwalKuliah::with(['mataKuliah', 'dosen']) // Eager load relasi
            ->where('tahun_akademik_id', $this->filterSemesterId)
            ->where('hari', $this->hari)
            ->where(function($q) {
                $q->where('jam_mulai', '<', $this->jam_selesai)
                  ->where('jam_selesai', '>', $this->jam_mulai);
            });

        if ($this->editMode) {
            $query->where('id', '!=', $this->jadwalId);
        }

        // A. Cek Bentrok Ruangan
        $bentrokRuang = (clone $query)->where('ruang', $this->ruang)->first();
        if ($bentrokRuang) {
            $jam = Carbon::parse($bentrokRuang->jam_mulai)->format('H:i') . ' - ' . Carbon::parse($bentrokRuang->jam_selesai)->format('H:i');
            return "GAGAL SIMPAN: Ruang {$this->ruang} SEDANG DIPAKAI pada jam {$jam} untuk mata kuliah {$bentrokRuang->mataKuliah->nama_mk} (Kelas {$bentrokRuang->nama_kelas}).";
        }

        // B. Cek Bentrok Dosen
        $bentrokDosen = (clone $query)->where('dosen_id', $this->dosen_id)->first();
        if ($bentrokDosen) {
            $jam = Carbon::parse($bentrokDosen->jam_mulai)->format('H:i') . ' - ' . Carbon::parse($bentrokDosen->jam_selesai)->format('H:i');
            $namaDosen = $bentrokDosen->dosen->nama_lengkap_gelar ?? 'Dosen';
            return "GAGAL SIMPAN: Dosen {$namaDosen} SEDANG MENGAJAR di tempat lain pada jam {$jam} (MK: {$bentrokDosen->mataKuliah->nama_mk} - Ruang {$bentrokDosen->ruang}).";
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
        $this->reset(['jadwalId', 'mata_kuliah_id', 'dosen_id', 'nama_kelas', 'hari', 'jam_mulai', 'jam_selesai', 'ruang', 'kuota_kelas', 'id_program_kelas_allow']);
        $this->kuota_kelas = 40;
    }

    public function batal()
    {
        $this->showForm = false;
        $this->resetForm();
    }
}