<?php

namespace App\Livewire\Admin\Lpm;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class IkuManager extends Component
{
    public $activeYear;
    public $showForm = false;
    
    // Form State
    public $targetId, $indikator_id, $target_nilai, $capaian_nilai;

    public function mount()
    {
        $this->activeYear = date('Y');
    }

    public function render()
    {
        $indicators = DB::table('lpm_indikators')->get();
        
        $targets = DB::table('lpm_iku_targets as t')
            ->join('lpm_indikators as i', 't.indikator_id', '=', 'i.id')
            ->select('t.*', 'i.nama_indikator', 'i.bobot')
            ->where('t.tahun', $this->activeYear)
            ->get();

        return view('livewire.admin.lpm.iku-manager', [
            'indicators' => $indicators,
            'targets' => $targets
        ]);
    }

    public function saveTarget()
    {
        $this->validate([
            'indikator_id' => 'required',
            'target_nilai' => 'required|numeric'
        ]);

        DB::table('lpm_iku_targets')->updateOrInsert(
            ['indikator_id' => $this->indikator_id, 'tahun' => $this->activeYear],
            [
                'target_nilai' => $this->target_nilai,
                'capaian_nilai' => $this->capaian_nilai ?? 0,
                'updated_at' => now(),
                'created_at' => now()
            ]
        );

        $this->showForm = false;
        session()->flash('success', 'Target IKU berhasil diperbarui.');
    }
}