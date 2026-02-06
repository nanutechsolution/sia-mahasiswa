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
use Carbon\Carbon;

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

    public function updatedSearch() { $this->resetPage(); }
    public function updatedFilterProdiId() { $this->resetPage(); }

    public function render()
    {
        $prodis = Prodi::all();

        $camabas = Mahasiswa::with(['prodi', 'programKelas', 'user', 'tagihan', 'person'])
            // Filter: NIM Sementara (Panjang > 15 atau mengandung PMB)
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
        $this->nama_lengkap = $mhs->person->nama_lengkap ?? $mhs->nama_lengkap; 
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
     * Logic Peresmian Mahasiswa: Generate NIM Sesuai Prodi & Increment Sequence
     */
    public function generateNimResmi($id)
    {
        DB::transaction(function () use ($id) {
            // Lock row untuk mencegah race condition saat generate nomor urut
            $mhs = Mahasiswa::with(['programKelas', 'tagihan', 'user'])->lockForUpdate()->find($id);
            $prodi = Prodi::lockForUpdate()->find($mhs->prodi_id);

            if (!$prodi) {
                session()->flash('error', 'Prodi tidak ditemukan.');
                return;
            }

            // 1. VALIDASI KEUANGAN
            $isDispensasi = $mhs->data_tambahan['bebas_keuangan'] ?? false;
            
            // Hitung total bayar
            $totalTagihan = $mhs->tagihan->sum('total_tagihan');
            $totalBayar = $mhs->tagihan->sum('total_bayar');

            $minPercent = $mhs->programKelas->min_pembayaran_persen ?? 50;
            
            $paidPercent = ($totalTagihan > 0) 
                ? round(($totalBayar / $totalTagihan) * 100) 
                : 0;

            if (!$isDispensasi && $paidPercent < $minPercent) {
                session()->flash('error', "Gagal: Pembayaran baru {$paidPercent}%. Syarat minimal {$minPercent}% atau aktifkan Dispensasi.");
                return;
            }

            // 2. GENERATE NIM BARU BERDASARKAN FORMAT PRODI
            $format = $prodi->format_nim ?? '{THN}{KODE}{NO:4}'; // Default jika null
            $angkatan = $mhs->angkatan_id; // Contoh: 2024
            
            // Increment Sequence Prodi
            $nextSeq = $prodi->last_nim_seq + 1;

            // Replace Placeholder
            $newNim = str_replace('{TAHUN}', $angkatan, $format); // 2024
            $newNim = str_replace('{THN}', substr($angkatan, 2, 2), $newNim); // 24
            $newNim = str_replace('{KODE}', $prodi->kode_prodi_dikti ?? '00000', $newNim); // Kode Dikti
            $newNim = str_replace('{INTERNAL}', $prodi->kode_prodi_internal, $newNim); // Kode Internal (TI/SI)

            // Handle Nomor Urut {NO:x}
            if (preg_match('/\{NO:(\d+)\}/', $newNim, $matches)) {
                $length = (int) $matches[1];
                $noStr = str_pad($nextSeq, $length, '0', STR_PAD_LEFT);
                $newNim = str_replace($matches[0], $noStr, $newNim);
            } else {
                // Fallback jika format tidak ada {NO:x}, tempel di belakang
                $newNim .= str_pad($nextSeq, 4, '0', STR_PAD_LEFT);
            }

            // Cek Unik (Just in Case)
            if (Mahasiswa::where('nim', $newNim)->exists()) {
                session()->flash('error', "Gagal: NIM {$newNim} sudah ada. Coba lagi (Sequence akan update).");
                // Opsional: $prodi->increment('last_nim_seq');
                return;
            }

            // 3. UPDATE DATA MAHASISWA
            $oldNim = $mhs->nim; 
            $dataTambahan = $mhs->data_tambahan ?? [];
            $dataTambahan['nim_lama'] = $oldNim;
            $dataTambahan['tgl_resmi_mahasiswa'] = now()->toDateString();

            $mhs->update([
                'nim' => $newNim,
                'data_tambahan' => $dataTambahan
            ]);

            // Update Akun Login
            if ($mhs->user) {
                $mhs->user->update([
                    'username' => $newNim,
                    'password' => Hash::make($newNim) // Reset password ke NIM baru
                ]);
            }

            // Update Counter Prodi
            $prodi->update(['last_nim_seq' => $nextSeq]);
            
            session()->flash('success', "Sukses! Mahasiswa {$mhs->nama_lengkap} resmi aktif dengan NIM: {$newNim}");
        });
    }
}