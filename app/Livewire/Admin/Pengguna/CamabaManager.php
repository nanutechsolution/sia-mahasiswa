<?php

namespace App\Livewire\Admin\Pengguna;

use Livewire\Component;
use Livewire\WithPagination;
use App\Domains\Mahasiswa\Models\Mahasiswa;
use App\Domains\Core\Models\Prodi;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CamabaManager extends Component
{
    use WithPagination;

    public $search = '';
    public $filterProdiId;

    // Form State (Dispensasi)
    public $camabaId;
    public $nama_lengkap;
    public $bebas_keuangan = false;
    public $showForm = false;

    public function render()
    {
        $prodis = Prodi::all();

        $camabas = Mahasiswa::with(['prodi', 'programKelas', 'user', 'tagihan']) 
            // FILTER: HANYA TAMPILKAN CAMABA (NIM SEMENTARA)
            ->where(function($q) {
                $q->whereRaw('LENGTH(nim) > 15')
                  ->orWhere('nim', 'like', '%PMB%');
            })
            ->when($this->filterProdiId, function($q) {
                $q->where('prodi_id', $this->filterProdiId);
            })
            ->where('nama_lengkap', 'like', '%'.$this->search.'%')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.admin.pengguna.camaba-manager', [
            'camabas' => $camabas,
            'prodis' => $prodis
        ]);
    }

    public function edit($id)
    {
        $mhs = Mahasiswa::find($id);
        $this->camabaId = $id;
        $this->nama_lengkap = $mhs->nama_lengkap;
        // Load status dispensasi dari JSON
        $this->bebas_keuangan = $mhs->data_tambahan['bebas_keuangan'] ?? false;
        
        $this->showForm = true;
    }

    public function save()
    {
        $mhs = Mahasiswa::find($this->camabaId);
        
        // Update data tambahan (Merge agar data PMB lain tidak hilang)
        $currentData = $mhs->data_tambahan ?? [];
        $currentData['bebas_keuangan'] = $this->bebas_keuangan;
        
        $mhs->update(['data_tambahan' => $currentData]);

        $this->showForm = false;
        session()->flash('success', 'Status dispensasi calon mahasiswa berhasil diupdate.');
    }

    public function batal()
    {
        $this->showForm = false;
        $this->reset(['camabaId', 'nama_lengkap', 'bebas_keuangan']);
    }

    public function generateNimResmi($id)
    {
        DB::transaction(function () use ($id) {
            $mhs = Mahasiswa::lockForUpdate()->find($id);
            $prodi = Prodi::lockForUpdate()->find($mhs->prodi_id);

            // 1. Generate NIM Baru
            $format = $prodi->format_nim ?? '{TAHUN}{KODE}{NO:4}';
            $nextSeq = $prodi->last_nim_seq + 1;

            $newNim = str_replace('{TAHUN}', $mhs->angkatan_id, $format);
            $newNim = str_replace('{THN}', substr($mhs->angkatan_id, 2, 2), $newNim);
            $newNim = str_replace('{KODE}', $prodi->kode_prodi_dikti ?? '00000', $newNim);
            $newNim = str_replace('{INTERNAL}', $prodi->kode_prodi_internal, $newNim);

            if (preg_match('/\{NO:(\d+)\}/', $newNim, $matches)) {
                $length = $matches[1];
                $number = str_pad($nextSeq, $length, '0', STR_PAD_LEFT);
                $newNim = str_replace($matches[0], $number, $newNim);
            } else {
                $newNim .= str_pad($nextSeq, 4, '0', STR_PAD_LEFT);
            }

            // 2. Update Data Mahasiswa
            $oldNim = $mhs->nim; 
            $mhs->update([
                'nim' => $newNim,
                'data_tambahan' => array_merge($mhs->data_tambahan ?? [], [
                    'nim_lama' => $oldNim,
                    'tgl_resmi_mahasiswa' => now()->toDateString()
                ])
            ]);

            // 3. Update User Login
            if ($mhs->user_id) {
                $user = User::find($mhs->user_id);
                $user->update([
                    'username' => $newNim,
                    'password' => Hash::make($newNim) // Password jadi NIM baru
                ]);
            }

            // 4. Update Counter Prodi
            $prodi->update(['last_nim_seq' => $nextSeq]);
        });

        session()->flash('success', 'Camaba berhasil diresmikan! Data pindah ke menu Data Mahasiswa.');
    }
}