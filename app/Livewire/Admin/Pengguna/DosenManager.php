<?php

namespace App\Livewire\Admin\Pengguna;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Domains\Akademik\Models\Dosen;
use App\Models\User;
use App\Domains\Core\Models\Prodi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use App\Domains\Core\Models\Person;

class DosenManager extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';
    public $filterProdiId;

    // Form State
    public $dosenId;
    public $nidn;
    public $nuptk;
    public $nama_lengkap;
    public $email;
    public $no_hp;
    public $homebase_prodi_id;
    public $is_active = true;
    public $password_baru;

    // Field Baru untuk Dosen Luar
    public $jenis_dosen = 'TETAP';
    public $asal_institusi;

    // Import State
    public $fileImport;
    public $showForm = false;
    public $editMode = false;
    public $showImportModal = false;

    public $assign_person_id, $assign_gelar_id, $assign_urutan = 1;
    public $listGelar = [];
    public function render()
    {
        $prodis = Prodi::all();
        $this->listGelar = DB::table('ref_gelar')->orderBy('nama')->get();

        $dosens = Dosen::join('ref_person', 'trx_dosen.person_id', '=', 'ref_person.id')
            ->leftJoin('users', 'ref_person.id', '=', 'users.person_id')
            ->select(
                'trx_dosen.*',
                'ref_person.nama_lengkap',
                'ref_person.email',
                'ref_person.no_hp',
                'users.username as user_login'
            )
            ->when($this->filterProdiId, function ($q) {
                $q->where('trx_dosen.prodi_id', $this->filterProdiId);
            })
            ->where(function ($q) {
                $q->where('ref_person.nama_lengkap', 'like', '%' . $this->search . '%')
                    ->orWhere('trx_dosen.nidn', 'like', '%' . $this->search . '%');
            })
            ->orderBy('ref_person.nama_lengkap', 'asc')
            ->paginate(10);

        return view('livewire.admin.pengguna.dosen-manager', [
            'dosens' => $dosens,
            'prodis' => $prodis
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
        $dosen = Dosen::with(['person.user'])->find($id);

        $this->dosenId = $id;
        $this->nidn = $dosen->nidn;
        $this->nuptk = $dosen->nuptk;
        $this->homebase_prodi_id = $dosen->prodi_id;
        $this->is_active = $dosen->is_active;
        $this->jenis_dosen = $dosen->jenis_dosen;
        $this->asal_institusi = $dosen->asal_institusi;

        $this->nama_lengkap = $dosen->person->nama_lengkap ?? '';
        $this->email = $dosen->person->email ?? '';
        $this->no_hp = $dosen->person->no_hp ?? '';

        $this->editMode = true;
        $this->showForm = true;
    }

    public function save()
    {
        $rules = [
            'nama_lengkap' => 'required',
            'homebase_prodi_id' => 'required',
            'email' => 'nullable|email',
            'jenis_dosen' => 'required',
            'asal_institusi' => 'nullable|string',
        ];

        if ($this->editMode) {
            if ($this->nidn) $rules['nidn'] = 'unique:trx_dosen,nidn,' . $this->dosenId;
            if ($this->nuptk) $rules['nuptk'] = 'unique:trx_dosen,nuptk,' . $this->dosenId;
        } else {
            if ($this->nidn) $rules['nidn'] = 'unique:trx_dosen,nidn';
            if ($this->nuptk) $rules['nuptk'] = 'unique:trx_dosen,nuptk';
            $rules['password_baru'] = 'required|min:6';
        }

        $this->validate($rules);

        DB::transaction(function () {

            // 1. Identitas (Person)
            $personData = [
                'nama_lengkap' => $this->nama_lengkap,
                'email' => $this->email,
                'no_hp' => $this->no_hp,
                'updated_at' => now()
            ];

            if ($this->editMode) {
                $dosen = Dosen::find($this->dosenId);
                $dosen->person->update($personData);
                $person = $dosen->person;
            } else {
                $personData['created_at'] = now();
                $person = Person::create($personData);
            }

            // 2. User Login
            $user = User::where('person_id', $person->id)->first();
            $username = $this->nidn ?: ($this->nuptk ?: 'dosen.' . rand(1000, 9999));

            if (!$user) {
                $user = User::create([
                    'name' => $this->nama_lengkap,
                    'username' => $username,
                    'email' => $this->email ?: $username . '@lecturer.unmaris.ac.id',
                    'password' => Hash::make($this->password_baru ?? 'password'),
                    'role' => 'dosen',
                    'is_active' => true,
                    'person_id' => $person->id
                ]);
                $user->assignRole('dosen');
            } else {
                $user->update(['name' => $this->nama_lengkap]);
                if ($this->password_baru) {
                    $user->update(['password' => Hash::make($this->password_baru)]);
                }
            }

            // 3. Data Akademik Dosen
            $dataDosen = [
                'prodi_id' => $this->homebase_prodi_id,
                'nidn' => $this->nidn,
                'nuptk' => $this->nuptk,
                'is_active' => $this->is_active,
                'jenis_dosen' => $this->jenis_dosen, // Simpan Jenis
                'asal_institusi' => $this->asal_institusi, // Simpan Asal
                'person_id' => $person->id,
            ];

            if ($this->editMode) {
                Dosen::find($this->dosenId)->update($dataDosen);
            } else {
                Dosen::create($dataDosen);
            }
        });

        session()->flash('success', 'Data Dosen berhasil disimpan.');
        $this->showForm = false;
    }

    // ... (Fungsi delete, openImport, downloadTemplate, processImport, batal sama seperti sebelumnya) ...
    public function delete($id)
    {
        $dosen = Dosen::find($id);
        if ($dosen->person && $dosen->person->user) $dosen->person->user->delete();
        $dosen->delete();
        session()->flash('success', 'Dosen dihapus.');
    }

    public function resetForm()
    {
        $this->reset(['dosenId', 'nidn', 'nuptk', 'nama_lengkap', 'email', 'no_hp', 'homebase_prodi_id', 'password_baru', 'editMode', 'is_active', 'fileImport', 'jenis_dosen', 'asal_institusi']);
        $this->is_active = true;
        $this->jenis_dosen = 'TETAP';
    }

    public function batal()
    {
        $this->showForm = false;
        $this->showImportModal = false;
        $this->resetForm();
    }

    public function openImport()
    {
        $this->reset(['fileImport']);
        $this->showImportModal = true;
    }

    public function openDegreeModal($personId)
    {
        $this->assign_person_id = $personId;
    }

    public function closeModal()
    {
        $this->reset(['assign_person_id', 'assign_gelar_id', 'assign_urutan']);
    }

    public function saveAssignmentGelar()
    {
        $this->validate(['assign_gelar_id' => 'required']);
        DB::table('trx_person_gelar')->updateOrInsert(
            ['person_id' => $this->assign_person_id, 'gelar_id' => $this->assign_gelar_id],
            ['urutan' => $this->assign_urutan, 'updated_at' => now()]
        );
        $this->reset(['assign_gelar_id', 'assign_urutan']);
    }

    public function removeAssignmentGelar($id)
    {
        DB::table('trx_person_gelar')->where('id', $id)->delete();
    }

    public function downloadTemplate()
    {
        // ... kode download template lama ...
        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['No', 'NIDN', 'NUPTK', 'Nama', 'Program Studi', 'L/P', 'Tempat,Tanggal Lahir', 'Agama']);
            fputcsv($handle, ['1', '0412058801', '', 'Budi Santoso', 'S1 Teknik Informatika', 'L', 'Jakarta, 12 June 1980', 'Islam']);
            fclose($handle);
        }, 'template_import_dosen_warek.csv');
    }

    public function processImport()
    {
        // ... kode import lama (tetap pakai TETAP sebagai default jika import warek) ...
        $this->validate(['fileImport' => 'required|mimes:csv,txt|max:2048']);
        $path = $this->fileImport->getRealPath();
        $file = fopen($path, 'r');
        $headerFound = false;
        $countSuccess = 0;
        DB::beginTransaction();
        try {
            while (($row = fgetcsv($file)) !== false) {
                if (!$headerFound) {
                    if (isset($row[0]) && strtolower(trim($row[0])) == 'no' && isset($row[3]) && strtolower(trim($row[3])) == 'nama') $headerFound = true;
                    continue;
                }
                if (count($row) < 4 || empty($row[3])) continue;

                $nidn = (trim($row[1]) === 'null' || trim($row[1]) === '') ? null : trim($row[1]);
                $nuptk = (trim($row[2]) === 'null' || trim($row[2]) === '') ? null : trim($row[2]);
                $nama = trim($row[3]);
                $prodiName = trim($row[4]);
                $gender = (strtoupper(trim($row[5])) == 'P') ? 'P' : 'L';
                $ttlRaw = $row[6];
                $tglLahir = null;
                $parts = explode(',', $ttlRaw);
                if (count($parts) > 1) {
                    try {
                        $dateStr = trim($parts[1]);
                        $tglLahir = Carbon::parse($dateStr)->format('Y-m-d');
                    } catch (\Exception $e) {
                        $tglLahir = null;
                    }
                }

                $prodi = Prodi::where('nama_prodi', 'like', '%' . $prodiName . '%')->orWhere('nama_prodi', 'like', '%' . str_replace('S1 ', '', $prodiName) . '%')->first();
                $prodiId = $prodi ? $prodi->id : 1;

                $person = Person::updateOrCreate(['nama_lengkap' => $nama], ['jenis_kelamin' => $gender, 'tanggal_lahir' => $tglLahir]);

                $dosenData = ['person_id' => $person->id, 'prodi_id' => $prodiId, 'is_active' => true, 'jenis_dosen' => 'TETAP']; // Default import jadi TETAP
                if ($nidn) $dosen = Dosen::updateOrCreate(['nidn' => $nidn], $dosenData);
                elseif ($nuptk) {
                    $dosenData['nidn'] = null;
                    $dosen = Dosen::updateOrCreate(['nuptk' => $nuptk], $dosenData);
                } else continue;

                $username = $nidn ?: $nuptk;
                if ($username && !User::where('username', $username)->exists()) {
                    $user = User::create(['name' => $nama, 'username' => $username, 'email' => $username . '@lecturer.unmaris.ac.id', 'password' => Hash::make($username), 'role' => 'dosen', 'is_active' => true, 'person_id' => $person->id]);
                    $user->assignRole('dosen');
                }
                $countSuccess++;
            }
            DB::commit();
            session()->flash('success', "Berhasil import $countSuccess data dosen.");
            $this->showImportModal = false;
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Gagal import: ' . $e->getMessage());
        }
        fclose($file);
    }
}
