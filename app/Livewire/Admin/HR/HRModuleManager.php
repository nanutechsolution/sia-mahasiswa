<?php

namespace App\Livewire\Admin\HR;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class HRModuleManager extends Component
{
    use WithPagination;

    // State Navigasi
    public $activeTab = 'personil';
    public $showForm = false;
    public $editMode = false;
    public $search = '';
    
    // State Modal (Pop-up)
    public $activeModal = null; // 'role' atau 'gelar'

    // Form State
    public $personId, $nama_lengkap, $nik, $email, $no_hp, $tanggal_lahir, $jenis_kelamin = 'L';
    public $pegawaiId, $pegawai_person_id, $nip, $jenis_pegawai = 'TENDIK', $is_active_pegawai = true;
    public $jabatanId, $kode_jabatan, $nama_jabatan_input, $jenis_jabatan = 'STRUKTURAL', $is_active_jabatan = true;
    public $gelarId, $kode_gelar, $nama_gelar_input, $posisi_gelar = 'BELAKANG', $jenjang_gelar = 'S1';
    public $roleId, $kode_role_input, $nama_role_input;
    public $penugasanId, $target_person_id, $target_jabatan_id, $fakultas_id, $prodi_id, $tanggal_mulai, $tanggal_selesai;

    // State Assignment
    public $assign_person_id, $assign_gelar_id, $assign_urutan = 1;
    public $assign_role_id, $assign_tgl_mulai_role;

    protected $queryString = ['activeTab', 'search'];

    public function mount()
    {
        $this->tanggal_mulai = date('Y-m-d');
        $this->assign_tgl_mulai_role = date('Y-m-d');
    }

    public function render()
    {
        return view('livewire.admin.hr.hr-module-manager', [
            'listData' => $this->getDataByTab(),
            'listPerson' => DB::table('ref_person')->orderBy('nama_lengkap')->get(),
            'listJabatan' => DB::table('ref_jabatan')->where('is_active', true)->orderBy('nama_jabatan')->get(),
            'listGelar' => DB::table('ref_gelar')->orderBy('nama')->get(),
            'listRoles' => DB::table('ref_person_role')->orderBy('nama_role')->get(),
            'listProdi' => DB::table('ref_prodi')->get(),
            'listFakultas' => DB::table('ref_fakultas')->get(),
        ]);
    }

    private function getDataByTab()
    {
        if ($this->activeTab == 'personil') {
            return DB::table('ref_person')
                ->where('nama_lengkap', 'like', "%{$this->search}%")
                ->orWhere('nik', 'like', "%{$this->search}%")
                ->orderBy('nama_lengkap', 'asc')->paginate(10);
        }
        if ($this->activeTab == 'pegawai') {
            return DB::table('trx_pegawai as tp')
                ->join('ref_person as p', 'tp.person_id', '=', 'p.id')
                ->select('tp.*', 'p.nama_lengkap', 'p.nik as person_nik')
                ->where('p.nama_lengkap', 'like', "%{$this->search}%")
                ->orWhere('tp.nip', 'like', "%{$this->search}%")
                ->orderBy('p.nama_lengkap')->paginate(10);
        }
        if ($this->activeTab == 'jabatan') {
            return DB::table('ref_jabatan')->where('nama_jabatan', 'like', "%{$this->search}%")->paginate(10);
        }
        if ($this->activeTab == 'gelar') {
            return DB::table('ref_gelar')->where('nama', 'like', "%{$this->search}%")->paginate(10);
        }
        if ($this->activeTab == 'role') {
            return DB::table('ref_person_role')->where('nama_role', 'like', "%{$this->search}%")->paginate(10);
        }
        if ($this->activeTab == 'penugasan') {
            return DB::table('trx_person_jabatan as pj')
                ->join('ref_person as p', 'pj.person_id', '=', 'p.id')
                ->join('ref_jabatan as j', 'pj.jabatan_id', '=', 'j.id')
                ->leftJoin('ref_prodi as pr', 'pj.prodi_id', '=', 'pr.id')
                ->leftJoin('ref_fakultas as f', 'pj.fakultas_id', '=', 'f.id')
                ->select('pj.*', 'p.nama_lengkap', 'j.nama_jabatan', 'pr.nama_prodi', 'f.nama_fakultas')
                ->where('p.nama_lengkap', 'like', "%{$this->search}%")
                ->orderBy('pj.tanggal_mulai', 'desc')->paginate(10);
        }
        return collect();
    }

    // --- CRUD METHODS (Sama seperti sebelumnya) ---
    public function savePerson() {
        $this->validate(['nama_lengkap' => 'required', 'nik' => ['nullable', Rule::unique('ref_person', 'nik')->ignore($this->personId)]]);
        $data = ['nama_lengkap' => $this->nama_lengkap, 'nik' => $this->nik, 'email' => $this->email, 'no_hp' => $this->no_hp, 'jenis_kelamin' => $this->jenis_kelamin, 'updated_at' => now()];
        if ($this->editMode) DB::table('ref_person')->where('id', $this->personId)->update($data);
        else { $data['created_at'] = now(); DB::table('ref_person')->insert($data); }
        $this->resetForm(); session()->flash('success', 'Data personil disimpan.');
    }
    public function editPerson($id) {
        $p = DB::table('ref_person')->find($id);
        $this->personId = $id; $this->nama_lengkap = $p->nama_lengkap; $this->nik = $p->nik;
        $this->email = $p->email; $this->no_hp = $p->no_hp; $this->jenis_kelamin = $p->jenis_kelamin;
        $this->editMode = true; $this->showForm = true;
    }

    public function savePegawai() {
        $this->validate(['pegawai_person_id' => 'required', 'nip' => ['nullable', Rule::unique('trx_pegawai', 'nip')->ignore($this->pegawaiId)], 'jenis_pegawai' => 'required']);
        $data = ['person_id' => $this->pegawai_person_id, 'nip' => $this->nip, 'jenis_pegawai' => $this->jenis_pegawai, 'is_active' => $this->is_active_pegawai, 'updated_at' => now()];
        if ($this->editMode) DB::table('trx_pegawai')->where('id', $this->pegawaiId)->update($data);
        else { $data['created_at'] = now(); DB::table('trx_pegawai')->insert($data); }
        $this->resetForm(); session()->flash('success', 'Data Pegawai disimpan.');
    }
    public function editPegawai($id) {
        $p = DB::table('trx_pegawai')->find($id);
        $this->pegawaiId = $id; $this->pegawai_person_id = $p->person_id; $this->nip = $p->nip;
        $this->jenis_pegawai = $p->jenis_pegawai; $this->is_active_pegawai = $p->is_active;
        $this->editMode = true; $this->showForm = true;
    }

    public function saveRole() {
        $this->validate(['kode_role_input' => ['required', Rule::unique('ref_person_role', 'kode_role')->ignore($this->roleId)], 'nama_role_input' => 'required']);
        $data = ['kode_role' => strtoupper($this->kode_role_input), 'nama_role' => $this->nama_role_input, 'updated_at' => now()];
        if ($this->editMode) DB::table('ref_person_role')->where('id', $this->roleId)->update($data);
        else { $data['created_at'] = now(); DB::table('ref_person_role')->insert($data); }
        $this->resetForm(); session()->flash('success', 'Master Role disimpan.');
    }
    public function editRole($id) {
        $r = DB::table('ref_person_role')->find($id);
        $this->roleId = $id; $this->kode_role_input = $r->kode_role; $this->nama_role_input = $r->nama_role;
        $this->editMode = true; $this->showForm = true;
    }

    public function saveJabatan() {
        $this->validate(['kode_jabatan' => ['required', Rule::unique('ref_jabatan', 'kode_jabatan')->ignore($this->jabatanId)], 'nama_jabatan_input' => 'required']);
        $data = ['kode_jabatan' => strtoupper($this->kode_jabatan), 'nama_jabatan' => $this->nama_jabatan_input, 'jenis' => $this->jenis_jabatan, 'is_active' => $this->is_active_jabatan, 'updated_at' => now()];
        if ($this->editMode) DB::table('ref_jabatan')->where('id', $this->jabatanId)->update($data);
        else { $data['created_at'] = now(); DB::table('ref_jabatan')->insert($data); }
        $this->resetForm(); session()->flash('success', 'Master Jabatan disimpan.');
    }
    public function editJabatan($id) {
        $j = DB::table('ref_jabatan')->find($id);
        $this->jabatanId = $id; $this->kode_jabatan = $j->kode_jabatan; $this->nama_jabatan_input = $j->nama_jabatan;
        $this->jenis_jabatan = $j->jenis; $this->is_active_jabatan = $j->is_active;
        $this->editMode = true; $this->showForm = true;
    }

    public function saveGelar() {
        $this->validate(['kode_gelar' => ['required', Rule::unique('ref_gelar', 'kode')->ignore($this->gelarId)], 'nama_gelar_input' => 'required']);
        $data = ['kode' => $this->kode_gelar, 'nama' => $this->nama_gelar_input, 'posisi' => $this->posisi_gelar, 'jenjang' => $this->jenjang_gelar, 'updated_at' => now()];
        if ($this->editMode) DB::table('ref_gelar')->where('id', $this->gelarId)->update($data);
        else { $data['created_at'] = now(); DB::table('ref_gelar')->insert($data); }
        $this->resetForm(); session()->flash('success', 'Master Gelar disimpan.');
    }
    public function editGelar($id) {
        $g = DB::table('ref_gelar')->find($id);
        $this->gelarId = $id; $this->kode_gelar = $g->kode; $this->nama_gelar_input = $g->nama;
        $this->posisi_gelar = $g->posisi; $this->jenjang_gelar = $g->jenjang;
        $this->editMode = true; $this->showForm = true;
    }

    public function savePenugasan() {
        $this->validate(['target_person_id' => 'required', 'target_jabatan_id' => 'required', 'tanggal_mulai' => 'required|date']);
        $data = [
            'person_id' => $this->target_person_id, 
            'jabatan_id' => $this->target_jabatan_id, 
            'fakultas_id' => $this->fakultas_id ?: null, 
            'prodi_id' => $this->prodi_id ?: null, 
            'tanggal_mulai' => $this->tanggal_mulai, 
            'tanggal_selesai' => $this->tanggal_selesai ?: null, 
            'updated_at' => now()
        ];
        if ($this->editMode) DB::table('trx_person_jabatan')->where('id', $this->penugasanId)->update($data);
        else { $data['created_at'] = now(); DB::table('trx_person_jabatan')->insert($data); }
        $this->resetForm(); session()->flash('success', 'Penugasan pejabat disimpan.');
    }
    public function editPenugasan($id) {
        $pj = DB::table('trx_person_jabatan')->find($id);
        $this->penugasanId = $id; $this->target_person_id = $pj->person_id; $this->target_jabatan_id = $pj->jabatan_id;
        $this->fakultas_id = $pj->fakultas_id; $this->prodi_id = $pj->prodi_id;
        $this->tanggal_mulai = $pj->tanggal_mulai; $this->tanggal_selesai = $pj->tanggal_selesai;
        $this->editMode = true; $this->showForm = true;
    }

    // --- ASSIGNMENT MODALS (Role & Gelar) ---
    
    public function openRoleModal($id) { 
        $this->assign_person_id = $id; 
        $this->activeModal = 'role'; 
    }
    public function saveAssignmentRole() {
        $this->validate(['assign_role_id' => 'required', 'assign_tgl_mulai_role' => 'required|date']);
        DB::table('trx_person_role')->insert(['person_id' => $this->assign_person_id, 'role_id' => $this->assign_role_id, 'tanggal_mulai' => $this->assign_tgl_mulai_role, 'created_at' => now()]);
        $this->reset(['assign_role_id']);
    }
    public function removeAssignmentRole($id) { DB::table('trx_person_role')->where('id', $id)->delete(); }

    public function openDegreeModal($id) { 
        $this->assign_person_id = $id; 
        $this->activeModal = 'gelar'; 
    }
    public function saveAssignmentGelar() {
        $this->validate(['assign_gelar_id' => 'required']);
        DB::table('trx_person_gelar')->updateOrInsert(['person_id' => $this->assign_person_id, 'gelar_id' => $this->assign_gelar_id], ['urutan' => $this->assign_urutan, 'updated_at' => now()]);
        $this->reset(['assign_gelar_id', 'assign_urutan']);
    }
    public function removeAssignmentGelar($id) { DB::table('trx_person_gelar')->where('id', $id)->delete(); }

    public function closeModal() {
        $this->reset(['assign_person_id', 'activeModal', 'assign_role_id', 'assign_gelar_id', 'assign_urutan']);
    }

    public function deleteData($id) {
        $map = ['personil' => 'ref_person', 'pegawai' => 'trx_pegawai', 'jabatan' => 'ref_jabatan', 'gelar' => 'ref_gelar', 'penugasan' => 'trx_person_jabatan', 'role' => 'ref_person_role'];
        DB::table($map[$this->activeTab])->where('id', $id)->delete();
        session()->flash('success', 'Data dihapus.');
    }

    public function resetForm() {
        $this->reset(['personId', 'nama_lengkap', 'nik', 'email', 'no_hp', 'jabatanId', 'kode_jabatan', 'nama_jabatan_input', 'gelarId', 'kode_gelar', 'nama_gelar_input', 'penugasanId', 'target_person_id', 'target_jabatan_id', 'fakultas_id', 'prodi_id', 'roleId', 'kode_role_input', 'nama_role_input', 'pegawaiId', 'pegawai_person_id', 'nip', 'jenis_pegawai', 'is_active_pegawai', 'showForm', 'editMode', 'assign_person_id', 'activeModal']);
        $this->tanggal_mulai = date('Y-m-d');
        $this->assign_tgl_mulai_role = date('Y-m-d');
    }

    public function switchTab($tab) { $this->activeTab = $tab; $this->resetForm(); $this->resetPage(); }
}