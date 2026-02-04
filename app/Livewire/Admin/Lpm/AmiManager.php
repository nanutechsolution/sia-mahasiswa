<?php

namespace App\Livewire\Admin\Lpm;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class AmiManager extends Component
{
    use WithPagination;

    public $activeTab = 'periode'; // 'periode' atau 'temuan'
    public $showForm = false;

    // Periode State
    public $periodeId, $nama_periode, $tgl_mulai, $tgl_selesai, $status = 'DRAFT';
    
    // Temuan State
    public $findingId, $target_periode_id, $prodi_id, $standar_id, $auditor_name, $klasifikasi = 'OB', $deskripsi_temuan;

    public function render()
    {
        return view('livewire.admin.lpm.ami-manager', [
            'periodes' => DB::table('lpm_ami_periodes')->orderBy('tgl_mulai', 'desc')->paginate(10, ['*'], 'pPage'),
            'findings' => DB::table('lpm_ami_findings as f')
                ->join('ref_prodi as p', 'f.prodi_id', '=', 'p.id')
                ->join('lpm_standars as s', 'f.standar_id', '=', 's.id')
                ->select('f.*', 'p.nama_prodi', 's.kode_standar')
                ->orderBy('f.created_at', 'desc')->paginate(10, ['*'], 'fPage'),
            'prodis' => DB::table('ref_prodi')->get(),
            'standars' => DB::table('lpm_standars')->get()
        ]);
    }

    public function savePeriode()
    {
        $this->validate(['nama_periode' => 'required', 'tgl_mulai' => 'required|date', 'tgl_selesai' => 'required|date|after:tgl_mulai']);
        
        DB::table('lpm_ami_periodes')->updateOrInsert(
            ['id' => $this->periodeId],
            ['nama_periode' => $this->nama_periode, 'tgl_mulai' => $this->tgl_mulai, 'tgl_selesai' => $this->tgl_selesai, 'status' => $this->status, 'created_at' => now(), 'updated_at' => now()]
        );

        session()->flash('success', 'Periode AMI berhasil disimpan.');
        $this->showForm = false;
        $this->reset(['periodeId', 'nama_periode', 'tgl_mulai', 'tgl_selesai']);
    }

    public function saveFinding()
    {
        $this->validate(['prodi_id' => 'required', 'standar_id' => 'required', 'deskripsi_temuan' => 'required']);
        
        DB::table('lpm_ami_findings')->insert([
            'periode_id' => $this->target_periode_id,
            'prodi_id' => $this->prodi_id,
            'standar_id' => $this->standar_id,
            'auditor_name' => $this->auditor_name,
            'klasifikasi' => $this->klasifikasi,
            'deskripsi_temuan' => $this->deskripsi_temuan,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        session()->flash('success', 'Temuan audit berhasil dicatat.');
        $this->showForm = false;
    }

    public function switchTab($tab) { $this->activeTab = $tab; $this->showForm = false; }
}