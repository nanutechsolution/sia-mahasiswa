<?php

namespace App\Livewire\Admin\Akademik;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Domains\Akademik\Models\JadwalKuliah;
use App\Domains\Akademik\Models\KrsDetail;
use App\Domains\Core\Models\Prodi;
use App\Domains\Core\Models\Person;
use App\Domains\Core\Models\TahunAkademik;
use App\Models\RefRuang;
use App\Models\JadwalUjian;
use App\Models\JadwalUjianPengawas;
use App\Models\JadwalUjianPeserta;
use App\Helpers\SistemHelper;

class JadwalUjianManager extends Component
{
    use WithPagination;

    public $showForm = false;

    // Filters
    public $filterSemesterId;
    public $filterProdiId;
    public $filterJenisUjian = '';
    public $search = '';

    // Form State
    public $ujianId;
    public $jadwal_kuliah_id;
    public $jenis_ujian = 'UTS';
    public $tanggal_ujian;
    public $jam_mulai;
    public $jam_selesai;
    public $ruang_id;
    public $metode_ujian = 'TERTULIS';
    public $keterangan;

    // Pengawas State
    public $pengawas_ids = []; // Array of person_id
    public $searchPengawas = '';
    public $selectedPengawas = [];

    // Validations
    public $roomConflict = null;

    public function mount()
    {
        $this->filterSemesterId = SistemHelper::idTahunAktif();
        $this->filterProdiId = Prodi::first()->id ?? null;
    }

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['tanggal_ujian', 'jam_mulai', 'jam_selesai', 'ruang_id'])) {
            $this->validateConflict();
        }

        if (in_array($propertyName, ['filterSemesterId', 'filterProdiId', 'filterJenisUjian', 'search'])) {
            $this->resetPage();
        }
    }

    protected function validateConflict()
    {
        $this->roomConflict = null;

        if ($this->tanggal_ujian && $this->jam_mulai && $this->jam_selesai && $this->ruang_id) {
            $conflict = JadwalUjian::where('tanggal_ujian', $this->tanggal_ujian)
                ->where('ruang_id', $this->ruang_id)
                ->where(function ($q) {
                    $q->whereBetween('jam_mulai', [$this->jam_mulai, $this->jam_selesai])
                        ->orWhereBetween('jam_selesai', [$this->jam_mulai, $this->jam_selesai])
                        ->orWhere(function ($sub) {
                            $sub->where('jam_mulai', '<=', $this->jam_mulai)
                                ->where('jam_selesai', '>=', $this->jam_selesai);
                        });
                });

            if ($this->ujianId) {
                $conflict->where('id', '!=', $this->ujianId);
            }

            $conflict = $conflict->first();

            if ($conflict) {
                $this->roomConflict = [
                    'mk' => $conflict->jadwalKuliah->mataKuliah->nama_mk,
                    'waktu' => substr($conflict->jam_mulai, 0, 5) . ' - ' . substr($conflict->jam_selesai, 0, 5)
                ];
            }
        }
    }

    public function tambahPengawas($personId, $nama)
    {
        if (!in_array($personId, $this->pengawas_ids)) {
            $this->pengawas_ids[] = $personId;
            $this->selectedPengawas[] = ['id' => $personId, 'nama' => $nama, 'peran' => count($this->pengawas_ids) === 1 ? 'UTAMA' : 'PENDAMPING'];
        }
        $this->searchPengawas = '';
    }

    public function hapusPengawas($personId)
    {
        $this->pengawas_ids = array_filter($this->pengawas_ids, fn($id) => $id != $personId);
        $this->selectedPengawas = array_filter($this->selectedPengawas, fn($p) => $p['id'] != $personId);
    }

    public function setPeranPengawas($personId, $peran)
    {
        foreach ($this->selectedPengawas as $key => $p) {
            if ($p['id'] == $personId) {
                $this->selectedPengawas[$key]['peran'] = $peran;
            }
        }
    }

    public function save()
    {
        $this->validateConflict();
        if ($this->roomConflict) {
            $this->dispatch('swal:error', ['title' => 'Gagal', 'text' => 'Terjadi bentrok ruangan ujian.']);
            return;
        }

        $this->validate([
            'jadwal_kuliah_id' => 'required',
            'jenis_ujian' => 'required|in:UTS,UAS,SUSULAN,LAINNYA',
            'tanggal_ujian' => 'required|date',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'ruang_id' => 'nullable',
            'metode_ujian' => 'required'
        ]);

        try {
            DB::transaction(function () {
                $ujian = JadwalUjian::updateOrCreate(
                    ['id' => $this->ujianId ?? (string) Str::uuid()],
                    [
                        'jadwal_kuliah_id' => $this->jadwal_kuliah_id,
                        'jenis_ujian' => $this->jenis_ujian,
                        'tanggal_ujian' => $this->tanggal_ujian,
                        'jam_mulai' => $this->jam_mulai,
                        'jam_selesai' => $this->jam_selesai,
                        'ruang_id' => $this->ruang_id,
                        'metode_ujian' => $this->metode_ujian,
                        'keterangan' => $this->keterangan,
                    ]
                );

                // Sinkronisasi Pengawas Ujian
                JadwalUjianPengawas::where('jadwal_ujian_id', $ujian->id)->delete();
                $pengawasData = [];
                foreach ($this->selectedPengawas as $p) {
                    $pengawasData[] = [
                        'jadwal_ujian_id' => $ujian->id,
                        'person_id' => $p['id'],
                        'peran' => $p['peran'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                if (!empty($pengawasData)) {
                    JadwalUjianPengawas::insert($pengawasData);
                }

                // Generate Peserta Otomatis jika ini ujian baru
                if (!$this->ujianId) {
                    $krsDetails = KrsDetail::where('jadwal_kuliah_id', $this->jadwal_kuliah_id)
                        ->whereHas('krs', fn($q) => $q->where('status_krs', 'DISETUJUI'))
                        ->pluck('id');

                    $pesertaData = [];
                    foreach ($krsDetails as $kd_id) {
                        $pesertaData[] = [
                            'jadwal_ujian_id' => $ujian->id,
                            'krs_detail_id' => $kd_id,
                            'status_kehadiran' => 'A', // Default Alpha, diubah saat absen ujian
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                    if (!empty($pesertaData)) {
                        JadwalUjianPeserta::insert($pesertaData);
                    }
                }
            });

            $this->dispatch('swal:success', ['title' => 'Berhasil', 'text' => 'Jadwal Ujian berhasil disimpan.']);
            $this->resetForm();
        } catch (\Exception $e) {
            $this->dispatch('swal:error', ['title' => 'Gagal', 'text' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $ujian = JadwalUjian::with(['pengawas.person'])->findOrFail($id);

        $this->ujianId = $ujian->id;
        $this->jadwal_kuliah_id = $ujian->jadwal_kuliah_id;
        $this->jenis_ujian = $ujian->jenis_ujian;
        $this->tanggal_ujian = $ujian->tanggal_ujian;
        $this->jam_mulai = substr($ujian->jam_mulai, 0, 5);
        $this->jam_selesai = substr($ujian->jam_selesai, 0, 5);
        $this->ruang_id = $ujian->ruang_id;
        $this->metode_ujian = $ujian->metode_ujian;
        $this->keterangan = $ujian->keterangan;

        $this->pengawas_ids = [];
        $this->selectedPengawas = [];
        foreach ($ujian->pengawas as $p) {
            $this->pengawas_ids[] = $p->person_id;
            $this->selectedPengawas[] = [
                'id' => $p->person_id,
                'nama' => $p->person->nama_lengkap ?? 'Unknown',
                'peran' => $p->peran
            ];
        }

        $this->showForm = true;
    }

    public function delete($id)
    {
        JadwalUjian::findOrFail($id)->delete();
        $this->dispatch('swal:success', ['title' => 'Terhapus', 'text' => 'Jadwal ujian berhasil dihapus.']);
    }

    public function resetForm()
    {
        $this->reset(['ujianId', 'jadwal_kuliah_id', 'tanggal_ujian', 'jam_mulai', 'jam_selesai', 'ruang_id', 'keterangan', 'pengawas_ids', 'selectedPengawas', 'roomConflict', 'showForm']);
        $this->jenis_ujian = 'UTS';
        $this->metode_ujian = 'TERTULIS';
        $this->resetValidation();
    }

    public function render()
    {
        $prodis = Prodi::all();
        $semesters = TahunAkademik::orderBy('kode_tahun', 'desc')->get();
        $ruangan = RefRuang::where('is_active', true)->get();

        // Ambil jadwal kuliah reguler untuk dipilih di form
        $jadwalKuliahOptions = JadwalKuliah::with('mataKuliah')
            ->where('tahun_akademik_id', $this->filterSemesterId)
            ->whereHas('mataKuliah', fn($q) => $q->where('prodi_id', $this->filterProdiId))
            ->get();
        // Cari Pengawas (Bisa Dosen atau Staff BAAK yang ada di ref_person)
        $calonPengawas = [];
        if (strlen($this->searchPengawas) > 2) {
            $calonPengawas = Person::where('nama_lengkap', 'like', "%{$this->searchPengawas}%")
                ->whereNotIn('id', $this->pengawas_ids)
                ->take(5)->get();
        }

        // List Ujian yang sudah dibuat
        $ujians = JadwalUjian::with(['jadwalKuliah.mataKuliah', 'ruang', 'pengawas.person'])
            ->whereHas('jadwalKuliah', function ($q) {
                $q->where('tahun_akademik_id', $this->filterSemesterId)
                    ->when($this->filterProdiId, fn($sq) => $sq->whereHas('mataKuliah', fn($mq) => $mq->where('prodi_id', $this->filterProdiId)));
            })
            ->when($this->filterJenisUjian, fn($q) => $q->where('jenis_ujian', $this->filterJenisUjian))
            ->when($this->search, function ($q) {
                $q->whereHas('jadwalKuliah.mataKuliah', fn($sq) => $sq->where('nama_mk', 'like', '%' . $this->search . '%'));
            })
            ->orderBy('tanggal_ujian')
            ->orderBy('jam_mulai')
            ->paginate(15);

        return view('livewire.admin.akademik.jadwal-ujian-manager', compact('prodis', 'semesters', 'ruangan', 'jadwalKuliahOptions', 'calonPengawas', 'ujians'));
    }
}
