<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-unmaris-blue">Master Fakultas</h1>
            <p class="text-slate-500 text-sm mt-1">Data unit fakultas di lingkungan universitas.</p>
        </div>
        @if(!$showForm)
        <button wire:click="create" class="inline-flex items-center px-5 py-2.5 bg-unmaris-blue text-white rounded-xl font-black text-sm shadow-lg shadow-unmaris-blue-500/20 hover:bg-unmaris-yellow hover:scale-105 transition-all hover:text-unmaris-blue">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Fakultas
        </button>
        @endif
    </div>

    {{-- Alert --}}
    @if (session()->has('success'))
    <div class="bg-emerald-50 border border-emerald-100 p-4 rounded-xl text-emerald-800 text-sm flex items-center shadow-sm animate-in fade-in slide-in-from-top-2">
        <svg class="w-5 h-5 mr-3 text-emerald-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
        </svg>
        <span class="font-bold">{{ session('success') }}</span>
    </div>
    @endif
    @if (session()->has('error'))
    <div class="bg-rose-50 border border-rose-100 p-4 rounded-xl text-rose-800 text-sm flex items-center shadow-sm animate-in fade-in slide-in-from-top-2">
        <span class="font-bold">{{ session('error') }}</span>
    </div>
    @endif

    {{-- Form --}}
    @if($showForm)
    <div class="bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden animate-in zoom-in-95 duration-200">
        <div class="px-8 py-5 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
            <h3 class="text-sm font-bold text-unmaris-blue uppercase tracking-wider flex items-center gap-2">
                @if($editMode)
                <svg class="w-5 h-5 text-unmaris-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit Fakultas
                @else
                <svg class="w-5 h-5 text-unmaris-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Tambah Fakultas Baru
                @endif
            </h3>
            <button wire:click="$set('showForm', false)" class="text-slate-400 hover:text-slate-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="p-8 space-y-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-2">Kode Fakultas (Ex: FT)</label>
                    <input type="text" wire:model="kode_fakultas" class="w-full rounded-xl border-slate-200 bg-slate-50 text-slate-900 py-2.5 pl-4 text-sm font-bold focus:ring-[#002855] placeholder-slate-400" placeholder="TEK">
                    @error('kode_fakultas') <span class="text-rose-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-2">Nama Fakultas</label>
                    <input type="text" wire:model="nama_fakultas" class="w-full rounded-xl border-slate-200 bg-slate-50 text-slate-900 py-2.5 pl-4 text-sm font-bold focus:ring-[#002855] placeholder-slate-400" placeholder="Fakultas Teknik">
                    @error('nama_fakultas') <span class="text-rose-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="pt-6 border-t border-slate-100 flex justify-end gap-3">
                <button wire:click="$set('showForm', false)" class="px-6 py-2.5 text-sm font-bold text-slate-500 hover:text-slate-700 hover:bg-slate-100 rounded-xl transition-all">Batal</button>
                <button wire:click="save" class="px-8 py-2.5 bg-unmaris-blue text-white rounded-xl text-sm font-bold shadow-lg shadow-indigo-200 hover:bg-unmaris-dark hover:scale-105 transition-all">Simpan Data</button>
            </div>
        </div>
    </div>
    @endif

    {{-- Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        {{-- Search Header --}}
        <div class="p-4 bg-slate-50/50 border-b flex items-center gap-4">
            <div class="relative flex-1">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari Fakultas..." class="w-full pl-10 pr-4 py-2 rounded-xl border-slate-200 text-sm focus:ring-unmaris-blue focus:border-unmaris-blue transition-shadow outline-none font-bold text-slate-700">
                <svg class="w-4 h-4 absolute left-3 top-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-unmaris-blue border-b border-unmaris-dark text-white">
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest">Kode</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest">Nama Fakultas</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest">Dekan Aktif (HR)</th>
                        <th class="px-6 py-4 text-right text-[10px] font-bold uppercase tracking-widest">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($fakultas as $f)
                    <tr class="hover:bg-slate-50/80 transition-colors group">
                        <td class="px-6 py-4 align-top">
                            <div class="font-mono text-xs font-bold text-unmaris-blue bg-indigo-50 px-2 py-0.5 rounded w-fit">{{ $f->kode_fakultas }}</div>
                        </td>
                        <td class="px-6 py-4 align-top">
                            <div class="text-sm font-bold text-slate-800">{{ $f->nama_fakultas }}</div>
                        </td>
                        <td class="px-6 py-4 align-top">
                            <div class="text-xs font-medium text-slate-600">
                                {{ $f->dekan }}
                            </div>
                        </td>
                        <td class="px-6 py-4 align-top text-right">
                            <div class="flex justify-end gap-2 opacity-60 group-hover:opacity-100 transition-opacity">
                                <button wire:click="edit({{ $f->id }})" class="p-2 text-unmaris-blue hover:bg-unmaris-blue/10 rounded-lg transition-colors" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <button wire:click="delete({{ $f->id }})" wire:confirm="Hapus fakultas ini?" class="p-2 text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-16 text-center">
                            <p class="text-slate-400 font-medium italic">Tidak ada data fakultas.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
            {{ $fakultas->links() }}
        </div>
    </div>
</div>