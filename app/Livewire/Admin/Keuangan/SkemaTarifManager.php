<?php

namespace App\Livewire\Admin\Keuangan;

use Livewire\Component;
use Livewire\WithPagination;
use App\Domains\Keuangan\Models\SkemaTarif;
use App\Domains\Keuangan\Models\DetailTarif;
use App\Domains\Keuangan\Models\KomponenBiaya;
use App\Domains\Core\Models\Prodi;
use App\Domains\Core\Models\ProgramKelas;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule; // PENTING: Tambahkan Import Ini

class SkemaTarifManager extends Component
{
    use WithPagination;

    // View Mode: 'list', 'form', 'detail'
    public $mode = 'list';

    // Header State
    public $skemaId;
    public $nama_skema;
    public $angkatan_id;
    public $prodi_id;
    public $program_kelas_id;

    // Detail State (Array of Items)
    public $items = []; // [{komponen_id, nominal, semester}]

    public function mount()
    {
        $this->angkatan_id = date('Y');
    }

    public function render()
    {
        if ($this->mode == 'list') {
            $skemas = SkemaTarif::with(['details', 'details.komponenBiaya'])
                ->orderBy('angkatan_id', 'desc')
                ->paginate(10);

            return view('livewire.admin.keuangan.skema-tarif-manager', [
                'skemas' => $skemas
            ]);
        }

        // Form Mode
        $prodis = Prodi::all();
        $programKelas = ProgramKelas::where('is_active', true)->get();
        $angkatans = DB::table('ref_angkatan')->orderBy('id_tahun', 'desc')->get();
        $komponens = KomponenBiaya::where('is_active', true)->orderBy('nama_komponen')->get();

        return view('livewire.admin.keuangan.skema-tarif-form', [
            'prodis' => $prodis,
            'programKelas' => $programKelas,
            'angkatans' => $angkatans,
            'komponens' => $komponens
        ]);
    }

    public function create()
    {
        $this->reset(['skemaId', 'nama_skema', 'prodi_id', 'program_kelas_id', 'items']);
        $this->angkatan_id = date('Y');
        $this->addItem();
        $this->mode = 'form';
    }

    public function edit($id)
    {
        $skema = SkemaTarif::with('details')->find($id);
        $this->skemaId = $id;
        $this->nama_skema = $skema->nama_skema;
        $this->angkatan_id = $skema->angkatan_id;
        $this->prodi_id = $skema->prodi_id;
        $this->program_kelas_id = $skema->program_kelas_id;

        $this->items = [];
        foreach ($skema->details as $det) {
            $this->items[] = [
                'komponen_id' => $det->komponen_biaya_id,
                'nominal' => $det->nominal,
                'semester' => $det->berlaku_semester
            ];
        }

        $this->mode = 'form';
    }

    public function addItem()
    {
        $this->items[] = ['komponen_id' => '', 'nominal' => 0, 'semester' => ''];
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function save()
    {
        // VALIDASI UNIK KOMPLEKS (Agar tidak crash database)
        $this->validate([
            'nama_skema' => 'required',
            'angkatan_id' => [
                'required',
                // Cek apakah kombinasi Angkatan + Prodi + Kelas sudah ada?
                Rule::unique('keuangan_skema_tarif')
                    ->where(function ($query) {
                        return $query->where('prodi_id', $this->prodi_id)
                            ->where('program_kelas_id', $this->program_kelas_id);
                    })
                    ->ignore($this->skemaId) // Abaikan diri sendiri jika sedang Edit
            ],
            'prodi_id' => 'required',
            'program_kelas_id' => 'required',
            'items.*.komponen_id' => 'required',
            'items.*.nominal' => 'required|numeric|min:0',
        ], [
            'nama_skema' => 'Nama Skema wajib diisi',
            'prodi_id' => 'Silakan Pilih Prodi',
            'program_kelas_id' => 'Silakan Program Kelas',
            'items.*.komponen_id' => 'Komponen Biaya Wajib',
            'items.*.nominal' => 'Nominal tidak boleh kosong',
            'angkatan_id.unique' => 'Skema Tarif untuk kombinasi Angkatan, Prodi, dan Program Kelas ini SUDAH ADA. Silakan edit skema yang lama.'
        ]);

        DB::transaction(function () {
            // 1. Save Header
            $dataHeader = [
                'nama_skema' => $this->nama_skema,
                'angkatan_id' => $this->angkatan_id,
                'prodi_id' => $this->prodi_id,
                'program_kelas_id' => $this->program_kelas_id,
            ];

            if ($this->skemaId) {
                $skema = SkemaTarif::find($this->skemaId);
                $skema->update($dataHeader);
                DetailTarif::where('skema_tarif_id', $this->skemaId)->delete();
            } else {
                $skema = SkemaTarif::create($dataHeader);
            }

            // 2. Save Details
            foreach ($this->items as $item) {
                DetailTarif::create([
                    'skema_tarif_id' => $skema->id,
                    'komponen_biaya_id' => $item['komponen_id'],
                    'nominal' => $item['nominal'],
                    'berlaku_semester' => $item['semester'] ?: null
                ]);
            }
        });

        session()->flash('success', 'Skema Tarif berhasil disimpan.');
        $this->mode = 'list';
    }

    public function delete($id)
    {
        SkemaTarif::find($id)->delete();
        session()->flash('success', 'Skema Tarif dihapus.');
    }

    public function batal()
    {
        $this->mode = 'list';
    }
}
