<?php

namespace App\Livewire\Admin\Lpm;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class KuisionerManager extends Component
{
    public $showFormGroup = false;
    public $showFormQuestion = false;

    // Group State
    public $groupId, $nama_kelompok, $urutan_group = 1;
    
    // Question State
    public $qId, $target_group_id, $bunyi_pertanyaan, $urutan_q = 1;

    public function render()
    {
        $groups = DB::table('lpm_kuisioner_kelompok')->orderBy('urutan')->get();
        
        $questions = DB::table('lpm_kuisioner_pertanyaan as q')
            ->join('lpm_kuisioner_kelompok as g', 'q.kelompok_id', '=', 'g.id')
            ->select('q.*', 'g.nama_kelompok')
            ->orderBy('g.urutan')
            ->orderBy('q.urutan')
            ->get();

        return view('livewire.admin.lpm.kuisioner-manager', [
            'groups' => $groups,
            'questions' => $questions
        ]);
    }

    public function saveGroup()
    {
        $this->validate(['nama_kelompok' => 'required']);
        DB::table('lpm_kuisioner_kelompok')->insert([
            'nama_kelompok' => $this->nama_kelompok,
            'urutan' => $this->urutan_group,
            'created_at' => now()
        ]);
        $this->reset(['nama_kelompok', 'showFormGroup']);
    }

    public function saveQuestion()
    {
        $this->validate(['target_group_id' => 'required', 'bunyi_pertanyaan' => 'required']);
        DB::table('lpm_kuisioner_pertanyaan')->insert([
            'kelompok_id' => $this->target_group_id,
            'bunyi_pertanyaan' => $this->bunyi_pertanyaan,
            'urutan' => $this->urutan_q,
            'created_at' => now()
        ]);
        $this->reset(['bunyi_pertanyaan', 'showFormQuestion']);
    }

    public function deleteQuestion($id) { DB::table('lpm_kuisioner_pertanyaan')->where('id', $id)->delete(); }
}