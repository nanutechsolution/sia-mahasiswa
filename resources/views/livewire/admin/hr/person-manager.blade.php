<div class="space-y-6">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Manajemen Personil & Pejabat</h1>
            <p class="text-sm text-slate-500">Kelola identitas, gelar akademik, dan penugasan jabatan struktural.</p>
        </div>
        @if(!$showForm)
        <button wire:click="create" class="bg-indigo-600 text-white px-4 py-2 rounded-xl font-bold text-sm shadow-lg hover:bg-indigo-700 transition-all">
            + Tambah Personil
        </button>
        @endif
    </div>

    @if (session()->has('success'))
        <div class="bg-emerald-50 border border-emerald-200 p-4 rounded-xl text-emerald-800 text-sm font-bold animate-in fade-in">
            {{ session('success') }}
        </div>
    @endif

    {{-- Form Personil --}}
    @if($showForm)
    <div class="bg-white p-6 shadow-sm rounded-2xl border border-slate-200 animate-in slide-in-from-top-4">
        <h3 class="text-lg font-bold text-slate-800 mb-6">{{ $editMode ? 'Edit Biodata' : 'Registrasi Personil Baru' }}</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Nama Lengkap (Tanpa Gelar)</label>
                <input type="text" wire:model="nama_lengkap" class="w-full rounded-xl border-slate-300 focus:ring-indigo-500 text-sm">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">NIK / NIP / Identitas</label>
                <input type="text" wire:model="nik" class="w-full rounded-xl border-slate-300 focus:ring-indigo-500 text-sm">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Email</label>
                <input type="email" wire:model="email" class="w-full rounded-xl border-slate-300 focus:ring-indigo-500 text-sm">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Nomor HP</label>
                <input type="text" wire:model="no_hp" class="w-full rounded-xl border-slate-300 focus:ring-indigo-500 text-sm">
            </div>
        </div>
        <div class="mt-8 flex justify-end gap-3 border-t pt-6">
            <button wire:click="$set('showForm', false)" class="px-6 py-2 text-sm font-bold text-slate-500">Batal</button>
            <button wire:click="save" class="bg-indigo-600 text-white px-8 py-2 rounded-xl font-bold text-sm shadow-md hover:bg-indigo-700">Simpan Identitas</button>
        </div>
    </div>
    @endif

    {{-- Tabel Data --}}
    <div class="bg-white shadow-sm rounded-2xl border border-slate-200 overflow-hidden">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-black text-slate-400 uppercase tracking-widest">Nama / NIK</th>
                    <th class="px-6 py-4 text-left text-xs font-black text-slate-400 uppercase tracking-widest">Gelar & Jabatan Aktif</th>
                    <th class="px-6 py-4 text-right text-xs font-black text-slate-400 uppercase tracking-widest">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white">
                @foreach($persons as $p)
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="text-sm font-bold text-slate-800">{{ $p->nama_lengkap }}</div>
                        <div class="text-[10px] font-mono text-slate-400">ID: {{ $p->nik ?? '-' }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex flex-wrap gap-2">
                            <button wire:click="openDegree({{ $p->id }})" class="px-2 py-1 bg-indigo-50 text-indigo-600 text-[10px] font-black rounded uppercase border border-indigo-100 hover:bg-indigo-100 transition-colors">
                                + Atur Gelar
                            </button>
                            <button wire:click="openJabatan({{ $p->id }})" class="px-2 py-1 bg-amber-50 text-amber-600 text-[10px] font-black rounded uppercase border border-amber-100 hover:bg-amber-100 transition-colors">
                                + Atur Jabatan
                            </button>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-right space-x-2">
                        <button wire:click="delete({{ $p->id }})" wire:confirm="Hapus personil ini?" class="text-rose-500 hover:underline text-xs font-bold uppercase tracking-tighter">Hapus</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-4 bg-slate-50 border-t border-slate-200">
            {{ $persons->links() }}
        </div>
    </div>

    {{-- Modal Gelar (Logic Inline untuk simulasi) --}}
    @if($showDegreeModal)
    <div class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center bg-slate-900/50 backdrop-blur-sm">
        <div class="bg-white rounded-3xl p-8 w-full max-w-lg shadow-2xl">
            <h4 class="text-lg font-black text-slate-800 uppercase mb-4 tracking-tight">Atur Gelar Akademik</h4>
            <p class="text-sm text-slate-500 mb-6">Personil: <span class="font-bold text-indigo-600">{{ $selectedPerson->nama_lengkap }}</span></p>
            
            <div class="space-y-4">
                <div class="grid grid-cols-4 gap-4">
                    <div class="col-span-3">
                        <label class="block text-[10px] font-black text-slate-400 uppercase mb-1">Pilih Gelar</label>
                        <select wire:model="gelar_id" class="w-full rounded-xl border-slate-300 text-sm">
                            <option value="">-- Pilih Gelar --</option>
                            @foreach($allGelar as $g)
                                <option value="{{ $g->id }}">{{ $g->kode }} ({{ $g->nama }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase mb-1">Urutan</label>
                        <input type="number" wire:model="urutan" class="w-full rounded-xl border-slate-300 text-sm text-center">
                    </div>
                </div>
                <button wire:click="addGelar" class="w-full bg-slate-800 text-white py-2 rounded-xl font-bold text-xs uppercase tracking-widest hover:bg-slate-700 transition-all">
                    Hubungkan Gelar
                </button>
            </div>

            <div class="mt-8 pt-6 border-t flex justify-end">
                <button wire:click="$set('showDegreeModal', false)" class="text-sm font-bold text-slate-400 hover:text-slate-600">Selesai & Tutup</button>
            </div>
        </div>
    </div>
    @endif
</div>