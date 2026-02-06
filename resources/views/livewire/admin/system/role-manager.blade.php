<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-[#002855]">Manajemen Role & Akses</h1>
            <p class="text-slate-500 text-sm mt-1">Buat jabatan baru dan tentukan fitur apa saja yang boleh diakses.</p>
        </div>
        @if(!$showForm)
        <button wire:click="create" class="inline-flex items-center px-5 py-2.5 bg-[#fcc000] text-[#002855] rounded-xl font-black text-sm shadow-lg shadow-orange-500/20 hover:bg-[#fbbf24] transition-all">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" /></svg>
            Buat Role Baru
        </button>
        @endif
    </div>

    @if (session()->has('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-100 text-emerald-800 rounded-xl text-sm font-bold flex items-center shadow-sm">{{ session('success') }}</div>
    @endif
    @if (session()->has('error'))
        <div class="p-4 bg-rose-50 border border-rose-100 text-rose-800 rounded-xl text-sm font-bold shadow-sm">{{ session('error') }}</div>
    @endif

    {{-- Form Section --}}
    @if($showForm)
    <div class="bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden animate-in zoom-in-95 duration-200">
        <div class="px-8 py-5 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
            <h3 class="text-sm font-bold text-[#002855] uppercase tracking-wider">
                @if($editMode) Edit Role: {{ $name }} @else Buat Role Baru @endif
            </h3>
            <button wire:click="batal" class="text-slate-400 hover:text-slate-600">&times;</button>
        </div>
        
        <div class="p-8 space-y-8">
            {{-- Nama Role --}}
            <div class="max-w-md">
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Nama Role / Jabatan</label>
                <input type="text" wire:model="name" class="block w-full rounded-xl border-slate-200 bg-slate-50 text-slate-900 py-2.5 pl-4 text-sm font-bold focus:ring-[#002855] focus:border-[#002855] placeholder-slate-300 uppercase" placeholder="CONTOH: WAKIL_REKTOR_1">
                @error('name') <span class="text-rose-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
            </div>

            {{-- Permission Checklist --}}
            <div class="bg-indigo-50/50 p-6 rounded-2xl border border-indigo-100">
                <h4 class="text-xs font-black text-[#002855] uppercase tracking-widest mb-4 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" /></svg>
                    Pilih Hak Akses (Permission)
                </h4>
                
                @if($name === 'superadmin')
                    <div class="p-3 bg-emerald-100 text-emerald-800 text-xs font-bold rounded-lg text-center border border-emerald-200">
                        Superadmin memiliki akses penuh ke seluruh sistem secara otomatis.
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($allPermissions as $perm)
                        <label class="flex items-center p-3 bg-white border border-slate-200 rounded-xl cursor-pointer hover:border-indigo-300 transition-colors">
                            <input type="checkbox" wire:model="selectedPermissions" value="{{ $perm->name }}" class="rounded border-slate-300 text-[#002855] focus:ring-[#fcc000] h-4 w-4 mr-3">
                            <span class="text-xs font-bold text-slate-600 select-none">{{ $perm->name }}</span>
                        </label>
                        @endforeach
                    </div>
                    @if(count($allPermissions) == 0)
                        <p class="text-xs text-slate-400 italic">Belum ada permission yang didaftarkan di sistem.</p>
                    @endif
                @endif
            </div>

            <div class="pt-4 border-t border-slate-100 flex justify-end gap-3">
                <button wire:click="batal" class="px-6 py-2.5 text-sm font-bold text-slate-500 hover:bg-slate-100 rounded-xl transition-all">Batal</button>
                <button wire:click="save" class="px-8 py-2.5 bg-[#002855] text-white rounded-xl text-sm font-bold shadow-lg hover:bg-[#001a38] transition-all">
                    Simpan Perubahan
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- Tabel Data --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#002855] border-b border-[#001a38] text-white">
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest">Nama Role</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest">Hak Akses (Permissions)</th>
                        <th class="px-6 py-4 text-center text-[10px] font-bold uppercase tracking-widest">User</th>
                        <th class="px-6 py-4 text-right text-[10px] font-bold uppercase tracking-widest">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($roles as $role)
                    <tr class="hover:bg-slate-50/80 transition-colors group">
                        <td class="px-6 py-4 align-top w-48">
                            <span class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-black bg-indigo-50 text-[#002855] border border-indigo-100">
                                {{ $role->name }}
                            </span>
                        </td>
                        <td class="px-6 py-4 align-top">
                            @if($role->name === 'superadmin')
                                <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded">* All Access</span>
                            @else
                                <div class="flex flex-wrap gap-1">
                                    @foreach($role->permissions as $p)
                                        <span class="text-[9px] font-bold text-slate-500 bg-slate-100 border border-slate-200 px-1.5 py-0.5 rounded">{{ $p->name }}</span>
                                    @endforeach
                                    @if($role->permissions->isEmpty())
                                        <span class="text-[10px] text-slate-300 italic">Tidak ada akses khusus</span>
                                    @endif
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 align-top text-center">
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                {{ $role->users_count }}
                            </span>
                        </td>
                        <td class="px-6 py-4 align-top text-right">
                            <div class="flex justify-end gap-2 opacity-60 group-hover:opacity-100 transition-opacity">
                                <button wire:click="edit({{ $role->id }})" class="p-2 text-[#002855] hover:bg-[#002855]/10 rounded-lg transition-colors" title="Edit Akses">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                </button>
                                @if(!in_array($role->name, ['superadmin', 'admin', 'dosen', 'mahasiswa']))
                                <button wire:click="delete({{ $role->id }})" wire:confirm="Hapus role ini?" class="p-2 text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-6 py-16 text-center text-slate-400 italic">Data tidak ditemukan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">{{ $roles->links() }}</div>
    </div>
</div>