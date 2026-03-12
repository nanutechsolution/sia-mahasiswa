<?php

namespace App\Livewire\Admin\Master;

use Livewire\Component;
use App\Domains\Core\Models\Fakultas;
use Livewire\Attributes\On;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests; // Tambahkan ini

class FakultasManager extends Component
{
    use AuthorizesRequests; // Trait untuk proteksi @can di backend

    // Form State
    public $fakultasId;
    public $kode_fakultas;
    public $nama_fakultas;

    // UI State
    public $showForm = false;
    public $editMode = false;

    public function render()
    {
        // Karena sudah pakai PowerGrid, kita tidak perlu kirim data fakultas lagi di sini
        return view('livewire.admin.master.fakultas-manager');
    }

    public function create()
    {
        $this->authorize('create_fakultas'); // Proteksi level Enterprise

        $this->reset(['kode_fakultas', 'nama_fakultas', 'fakultasId']);
        $this->showForm = true;
        $this->editMode = false;
    }

    #[On('editFakultas')]
    public function edit($id)
    {
        $this->authorize('edit_fakultas'); // Proteksi level Enterprise

        $f = Fakultas::findOrFail($id);
        $this->fakultasId = $id;
        $this->kode_fakultas = $f->kode_fakultas;
        $this->nama_fakultas = $f->nama_fakultas;

        $this->editMode = true;
        $this->showForm = true;
    }

    public function save()
    {
        // 1. Otorisasi
        $this->authorize($this->editMode ? 'edit_fakultas' : 'create_fakultas');

        // 2. Validasi
        $rules = [
            'nama_fakultas' => 'required|string|max:100',
            'kode_fakultas' => $this->editMode
                ? 'required|unique:ref_fakultas,kode_fakultas,' . $this->fakultasId
                : 'required|unique:ref_fakultas,kode_fakultas',
        ];

        $message = [
            'nama_fakultas.required' => 'Nama Fakultas wajib diisi!',
            'kode_fakultas.required' => 'Kode Fakultas wajib diisi!',
            'kode_fakultas.unique'   => 'Kode Fakultas sudah digunakan sistem!',
        ];

        $this->validate($rules, $message);

        // 3. Eksekusi
        $data = [
            'kode_fakultas' => strtoupper(trim($this->kode_fakultas)),
            'nama_fakultas' => trim($this->nama_fakultas),
        ];

        if ($this->editMode) {
            Fakultas::find($this->fakultasId)->update($data);
            $msg = 'Data Fakultas berhasil diperbarui.';
        } else {
            Fakultas::create($data);
            $msg = 'Fakultas baru berhasil ditambahkan.';
        }

        // 4. Reset & Notify
        $this->showForm = false;

        // Refresh PowerGrid
        $this->dispatch('pg:eventRefresh-fakultas-table');

        // SweetAlert Toast
        $this->dispatch('toast', [
            'type' => 'success',
            'message' => $msg
        ]);
    }

    #[On('deleteFakultas')]
    public function delete($id)
    {
        // 1. Otorisasi`
        $this->authorize('delete_fakultas');

        // 2. Cari data & Cek Relasi (Pagar Pengaman)
        $f = Fakultas::withCount(['prodis'])->find($id);

        if (!$f) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'Data tidak ditemukan!']);
            return;
        }

        // Jangan hapus jika ada Prodi di dalamnya
        if ($f->prodis_count > 0) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => "Gagal! Fakultas ini masih memiliki {$f->prodis_count} Program Studi aktif."
            ]);
            return;
        }

        // 3. Eksekusi Hapus
        try {
            $f->delete();

            // Refresh PowerGrid
            $this->dispatch('pg:eventRefresh-fakultas-table');

            // SweetAlert Toast
            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Fakultas berhasil dihapus permanen.'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Terjadi kesalahan sistem saat menghapus data.'
            ]);
        }
    }

    public function batal()
    {
        $this->showForm = false;
        $this->resetErrorBag();
    }
}
