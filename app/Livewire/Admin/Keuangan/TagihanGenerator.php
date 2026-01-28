<?php

namespace App\Livewire\Admin\Keuangan;

use Livewire\Component;
use App\Domains\Core\Models\TahunAkademik;
use App\Domains\Core\Models\Prodi;
use App\Domains\Keuangan\Actions\GenerateTagihanMassalAction;
use App\Helpers\SistemHelper;
use Illuminate\Support\Facades\DB;

class TagihanGenerator extends Component
{
    // Filter
    public $semesterId;
    public $angkatanId;
    public $prodiId;

    public $hasil = null;

    public function mount()
    {
        $this->semesterId = SistemHelper::idTahunAktif();
    }

    public function generate()
    {
        $this->validate([
            'semesterId' => 'required',
            'angkatanId' => 'required',
        ]);

        $action = new GenerateTagihanMassalAction();
        
        try {
            $this->hasil = $action->execute(
                $this->semesterId, 
                $this->angkatanId, 
                $this->prodiId
            );
            
            session()->flash('success', 'Proses generate selesai.');
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $semesters = TahunAkademik::orderBy('kode_tahun', 'desc')->get();
        // Ambil list angkatan dari DB
        $angkatans = DB::table('ref_angkatan')->orderBy('id_tahun', 'desc')->get();
        $prodis = Prodi::all();

        return view('livewire.admin.keuangan.tagihan-generator', [
            'semesters' => $semesters,
            'angkatans' => $angkatans,
            'prodis' => $prodis
        ]);
    }
}