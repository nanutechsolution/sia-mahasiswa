<?php

namespace App\Livewire\Admin\Pengguna;

use Livewire\Component;
use Livewire\WithPagination;
use App\Domains\Mahasiswa\Models\Mahasiswa;
use App\Domains\Core\Models\Prodi;
use App\Domains\Keuangan\Models\TagihanMahasiswa;
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

        $camabas = Mahasiswa::with(['prodi', 'programKelas', 'user', 'tagihan', 'person'])
            // FILTER: HANYA TAMPILKAN CAMABA (NIM SEMENTARA)
            ->where(function($q) {
                $q->whereRaw('LENGTH(nim) > 15')
                  ->orWhere('nim', 'like', '%PMB%');
            })
            ->when($this->filterProdiId, function($q) {
                $q->where('prodi_id', $this->filterProdiId);
            })
            ->where(function($q) {
                $q->whereHas('person', function($qp) {
                    $qp->where('nama_lengkap', 'like', '%'.$this->search.'%');
                })
                ->orWhere('nim', 'like', '%'.$this->search.'%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.admin.pengguna.camaba-manager', [
            'camabas' => $camabas,
            'prodis' => $prodis
        ]);
    }

    public function edit($id)
    {
        $mhs = Mahasiswa::with('person')->find($id);
        $this->camabaId = $id;
        $this->nama_lengkap = $mhs->nama_lengkap; 
        $this->bebas_keuangan = $mhs->data_tambahan['bebas_keuangan'] ?? false;
        $this->showForm = true;
    }

    public function save()
    {
        $mhs = Mahasiswa::find($this->camabaId);
        $currentData = $mhs->data_tambahan ?? [];
        $currentData['bebas_keuangan'] = $this->bebas_keuangan;
        
        $mhs->update(['data_tambahan' => $currentData]);

        $this->showForm = false;
        session()->flash('success', 'Status dispensasi berhasil diperbarui.');
    }

    public function batal()
    {
        $this->showForm = false;
        $this->reset(['camabaId', 'nama_lengkap', 'bebas_keuangan']);
    }

    /**
     * Logic Peresmian Mahasiswa: Cek Keuangan atau Dispensasi
     */
    public function generateNimResmi($id)
    {
        DB::transaction(function () use ($id) {
            $mhs = Mahasiswa::with(['programKelas', 'tagihan'])->lockForUpdate()->find($id);
            $prodi = Prodi::lockForUpdate()->find($mhs->prodi_id);

            // 1. VALIDASI KEUANGAN (SSOT Logic)
            $isDispensasi = $mhs->data_tambahan['bebas_keuangan'] ?? false;
            $tagihan = $mhs->tagihan->first();
            
            $minPercent = $mhs->programKelas->min_pembayaran_persen ?? 50;
            $paidPercent = ($tagihan && $tagihan->total_tagihan > 0) 
                ? round(($tagihan->total_bayar / $tagihan->total_tagihan) * 100) 
                : 0;

            if (!$isDispensasi && $paidPercent < $minPercent) {
                session()->flash('error', "Gagal: Pembayaran baru {$paidPercent}%. Syarat minimal {$minPercent}% atau aktifkan Dispensasi.");
                return;
            }

            // 2. GENERATE NIM BARU
            $format = $prodi->format_nim ?? '{TAHUN}{KODE}{NO:4}';
            $nextSeq = $prodi->last_nim_seq + 1;

            $newNim = str_replace('{TAHUN}', $mhs->angkatan_id, $format);
            $newNim = str_replace('{THN}', substr($mhs->angkatan_id, 2, 2), $newNim);
            $newNim = str_replace('{KODE}', $prodi->kode_prodi_dikti ?? '00000', $newNim);
            $newNim = str_replace('{INTERNAL}', $prodi->kode_prodi_internal, $newNim);

            if (preg_match('/\{NO:(\d+)\}/', $newNim, $matches)) {
                $length = $matches[1];
                $newNim = str_replace($matches[0], str_pad($nextSeq, $length, '0', STR_PAD_LEFT), $newNim);
            } else {
                $newNim .= str_pad($nextSeq, 4, '0', STR_PAD_LEFT);
            }

            // 3. UPDATE DATA
            $oldNim = $mhs->nim; 
            $mhs->update([
                'nim' => $newNim,
                'data_tambahan' => array_merge($mhs->data_tambahan ?? [], [
                    'nim_lama' => $oldNim,
                    'tgl_resmi_mahasiswa' => now()->toDateString()
                ])
            ]);

            if ($mhs->user) {
                $mhs->user->update([
                    'username' => $newNim,
                    'password' => Hash::make($newNim)
                ]);
            }

            $prodi->update(['last_nim_seq' => $nextSeq]);
            
            session()->flash('success', "Mahasiswa {$mhs->nama_lengkap} resmi memiliki NIM: {$newNim}");
        });
    }
}