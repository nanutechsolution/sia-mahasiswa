<?php

namespace App\Livewire\Admin\Lpm;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DokumenManager extends Component
{
    use WithPagination, WithFileUploads;

    public $showForm = false;
    public $search = '';

    // Fields
    public $docId, $nama_dokumen, $jenis = 'KEBIJAKAN', $file, $versi = '1.0', $tgl_berlaku;

    public function render()
    {
        $docs = DB::table('lpm_dokumens')
            ->where('nama_dokumen', 'like', '%'.$this->search.'%')
            ->orderBy('tgl_berlaku', 'desc')
            ->paginate(10);

        return view('livewire.admin.lpm.dokumen-manager', ['docs' => $docs]);
    }

    public function save()
    {
        $this->validate([
            'nama_dokumen' => 'required',
            'file' => $this->docId ? 'nullable|max:5120' : 'required|max:5120', // 5MB
            'tgl_berlaku' => 'required|date'
        ]);

        $data = [
            'nama_dokumen' => $this->nama_dokumen,
            'jenis' => $this->jenis,
            'versi' => $this->versi,
            'tgl_berlaku' => $this->tgl_berlaku,
            'updated_at' => now()
        ];

        if ($this->file) {
            $path = $this->file->store('lpm/dokumen', 'public');
            $data['file_path'] = $path;
        }

        if ($this->docId) {
            DB::table('lpm_dokumens')->where('id', $this->docId)->update($data);
        } else {
            $data['created_at'] = now();
            DB::table('lpm_dokumens')->insert($data);
        }

        session()->flash('success', 'Dokumen mutu berhasil diunggah.');
        $this->showForm = false;
        $this->reset(['docId', 'nama_dokumen', 'file', 'tgl_berlaku']);
    }

    public function delete($id)
    {
        $doc = DB::table('lpm_dokumens')->where('id', $id)->first();
        if ($doc->file_path) Storage::disk('public')->delete($doc->file_path);
        DB::table('lpm_dokumens')->where('id', $id)->delete();
    }
}