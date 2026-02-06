<div class="space-y-8">
    {{-- Header & Toolbar --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-[#002855] uppercase tracking-tight">Penyetaraan Mata Kuliah</h1>
            <p class="text-slate-500 text-sm mt-1">Konfigurasi pemetaan mata kuliah lintas kurikulum (Recognition Layer).</p>
        </div>
        
        @if(!$showForm)
        <button wire:click="create" class="inline-flex items-center px-5 py-2.5 bg-[#fcc000] text-[#002855] rounded-xl font-black text-sm shadow-lg shadow-orange-500/20 hover:bg-[#fbbf24] hover:scale-105 transition-all">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" /></svg>
            Tambah Pemetaan
        </button>
        @endif
    </div>

    {{-- Alert Messages --}}
    @if (session()->has('success'))
    <div class="p-4 bg-emerald-50 border border-emerald-100 text-emerald-800 rounded-xl text-sm font-bold flex items-center shadow-sm animate-in fade-in slide-in-from-top-2">
        <svg class="w-5 h-5 mr-3 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
        {{ session('success') }}
    </div>
    @endif

    {{-- Context Filter --}}
    @if(!$showForm)
    <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 flex flex-col md:flex-row items-start md:items-center gap-4">
        <div class="p-2 bg-indigo-50 text-[#002855] rounded-lg">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
        </div>
        <div class="flex-1 w-full">
            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Filter Program Studi</label>
            <div class="relative">
                <select wire:model.live="filterProdiId" class="block w-full md:w-1/2 rounded-xl border-slate-200 bg-slate-50 text-slate-900 py-2.5 pl-4 pr-10 text-sm font-bold focus:border-[#002855] focus:ring-[#002855] transition-shadow cursor-pointer">
                    @foreach($prodis as $p) <option value="{{ $p->id }}">{{ $p->jenjang }} - {{ $p->nama_prodi }}</option> @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-500 md:right-1/2">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Form Section --}}
    @if($showForm)
    <div class="bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden animate-in zoom-in-95 duration-200">
        <div class="px-8 py-5 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
            <h3 class="text-sm font-bold text-[#002855] uppercase tracking-wider flex items-center gap-2">
                @if($editMode) Edit Penyetaraan @else Buat Penyetaraan Baru @endif
            </h3>
            <button wire:click="batal" class="text-slate-400 hover:text-slate-600 transition-colors">&times;</button>
        </div>

        <div class="p-8 space-y-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-start relative">
                
                {{-- Arrow Decoration (Desktop) --}}
                <div class="hidden md:block absolute top-8 left-1/2 transform -translate-x-1/2 z-0 text-slate-200">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" /></svg>
                </div>

                {{-- Left: Mata Kuliah Asal --}}
                <div class="bg-rose-50/50 p-6 rounded-2xl border border-rose-100 relative z-10">
                    <div class="flex items-center gap-2 mb-4">
                        <span class="bg-rose-100 text-rose-700 px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider">Sumber</span>
                        <h4 class="text-sm font-bold text-slate-700">Mata Kuliah Lama</h4>
                    </div>
                    
                    <div class="relative" x-data="{ open: false }">
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Cari Matkul</label>
                        
                        <div class="relative">
                            <input type="text" wire:model.live="searchAsal" 
                                @focus="open = true" @click.away="open = false"
                                placeholder="{{ $selectedAsalName ?: 'Ketik Nama / Kode MK...' }}"
                                class="block w-full rounded-xl border-slate-300 bg-white p-3 pl-10 text-sm font-bold focus:border-rose-500 focus:ring-rose-500 shadow-sm placeholder:font-normal placeholder:text-slate-400">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                        </div>

                        {{-- Dropdown Results --}}
                        @if(!empty($optionsAsal))
                        <div x-show="open" class="absolute z-50 w-full bg-white shadow-2xl rounded-xl mt-2 border border-slate-100 max-h-60 overflow-y-auto">
                            @foreach($optionsAsal as $oa)
                            <div wire:click="selectAsal('{{ $oa->id }}', '{{ $oa->nama_mk }}', '{{ $oa->kode_mk }}')" @click="open = false"
                                class="px-4 py-3 hover:bg-rose-50 cursor-pointer border-b border-slate-50 last:border-0 transition-colors group">
                                <p class="text-xs font-bold text-slate-800 group-hover:text-rose-700">{{ $oa->nama_mk }}</p>
                                <p class="text-[10px] text-slate-500 font-mono mt-0.5">{{ $oa->kode_mk }} &bull; {{ $oa->sks_default }} SKS</p>
                            </div>
                            @endforeach
                        </div>
                        @endif

                        {{-- Selected Preview --}}
                        @if($selectedAsalName && !$searchAsal)
                        <div class="mt-3 p-3 bg-white border border-rose-200 rounded-xl flex items-center gap-3">
                            <div class="bg-rose-100 p-2 rounded-lg text-rose-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg></div>
                            <div>
                                <p class="text-[10px] text-slate-400 uppercase font-bold">Terpilih:</p>
                                <p class="text-xs font-bold text-slate-800">{{ $selectedAsalName }}</p>
                            </div>
                        </div>
                        @endif
                        @error('mk_asal_id') <span class="text-rose-500 text-[10px] font-bold mt-2 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                {{-- Right: Mata Kuliah Tujuan --}}
                <div class="bg-emerald-50/50 p-6 rounded-2xl border border-emerald-100 relative z-10">
                    <div class="flex items-center gap-2 mb-4">
                        <span class="bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider">Target</span>
                        <h4 class="text-sm font-bold text-slate-700">Mata Kuliah Baru</h4>
                    </div>

                    <div class="relative" x-data="{ open: false }">
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Cari Matkul</label>
                        
                        <div class="relative">
                            <input type="text" wire:model.live="searchTujuan" 
                                @focus="open = true" @click.away="open = false"
                                placeholder="{{ $selectedTujuanName ?: 'Ketik Nama / Kode MK...' }}"
                                class="block w-full rounded-xl border-slate-300 bg-white p-3 pl-10 text-sm font-bold focus:border-emerald-500 focus:ring-emerald-500 shadow-sm placeholder:font-normal placeholder:text-slate-400">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                        </div>

                        {{-- Dropdown Results --}}
                        @if(!empty($optionsTujuan))
                        <div x-show="open" class="absolute z-50 w-full bg-white shadow-2xl rounded-xl mt-2 border border-slate-100 overflow-hidden">
                            @foreach($optionsTujuan as $ot)
                            <div wire:click="selectTujuan('{{ $ot->id }}', '{{ $ot->nama_mk }}', '{{ $ot->kode_mk }}')" @click="open = false"
                                class="px-4 py-3 hover:bg-emerald-50 cursor-pointer border-b border-slate-50 last:border-0 transition-colors group">
                                <p class="text-xs font-bold text-slate-800 group-hover:text-emerald-700">{{ $ot->nama_mk }}</p>
                                <p class="text-[10px] text-slate-500 font-mono mt-0.5">{{ $ot->kode_mk }} &bull; {{ $ot->sks_default }} SKS</p>
                            </div>
                            @endforeach
                        </div>
                        @endif

                         {{-- Selected Preview --}}
                         @if($selectedTujuanName && !$searchTujuan)
                         <div class="mt-3 p-3 bg-white border border-emerald-200 rounded-xl flex items-center gap-3">
                             <div class="bg-emerald-100 p-2 rounded-lg text-emerald-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg></div>
                             <div>
                                 <p class="text-[10px] text-slate-400 uppercase font-bold">Terpilih:</p>
                                 <p class="text-xs font-bold text-slate-800">{{ $selectedTujuanName }}</p>
                             </div>
                         </div>
                         @endif
                        @error('mk_tujuan_id') <span class="text-rose-500 text-[10px] font-bold mt-2 block">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-4 border-t border-slate-100">
                <div class="md:col-span-1">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Nomor SK</label>
                    <input type="text" wire:model="nomor_sk" placeholder="Ex: SK/01/TI/2026" class="block w-full rounded-xl border-slate-300 bg-white p-2.5 text-sm font-bold focus:border-[#002855] focus:ring-[#002855] shadow-sm">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Keterangan / Alasan</label>
                    <input type="text" wire:model="keterangan" placeholder="Ex: Perubahan kurikulum 2024..." class="block w-full rounded-xl border-slate-300 bg-white p-2.5 text-sm focus:border-[#002855] focus:ring-[#002855] shadow-sm">
                </div>
            </div>

            <div class="pt-4 flex justify-end gap-3">
                <button wire:click="batal" class="px-6 py-2.5 text-sm font-bold text-slate-500 hover:text-slate-700 transition-colors">Batalkan</button>
                <button wire:click="save" class="px-8 py-2.5 bg-[#002855] text-white rounded-xl text-sm font-bold shadow-lg hover:bg-[#001a38] hover:scale-105 transition-all flex items-center">
                    <span wire:loading.remove wire:target="save">Simpan Data</span>
                    <span wire:loading wire:target="save">Menyimpan...</span>
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- Table List --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden relative">
        
        {{-- Loading Overlay --}}
        <div wire:loading.flex wire:target="delete, edit, filterProdiId, gotoPage" class="absolute inset-0 z-20 bg-white/60 backdrop-blur-[1px] items-center justify-center hidden">
             <div class="flex flex-col items-center justify-center p-4 bg-white rounded-2xl shadow-xl border border-slate-100">
                 <svg class="w-8 h-8 text-[#002855] animate-spin mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                 <span class="text-xs font-bold text-slate-500 animate-pulse">Memuat...</span>
             </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#002855] border-b border-[#001a38] text-white">
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest w-5/12">MK Asal (Lama)</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-center w-1/12"></th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest w-5/12">MK Tujuan (Baru)</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-right w-1/12">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($listEkuivalensi as $item)
                    <tr class="hover:bg-slate-50/80 transition-colors group">
                        <td class="px-6 py-4 align-middle">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center font-bold text-xs">A</div>
                                <div>
                                    <div class="text-sm font-black text-slate-800">{{ $item->mataKuliahAsal->nama_mk }}</div>
                                    <div class="text-[10px] font-mono text-slate-400 mt-0.5 uppercase">{{ $item->mataKuliahAsal->kode_mk }} &bull; {{ $item->mataKuliahAsal->sks_default }} SKS</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center align-middle">
                            <div class="inline-flex p-1.5 rounded-full bg-slate-100 text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                            </div>
                        </td>
                        <td class="px-6 py-4 align-middle">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-xs">B</div>
                                <div>
                                    <div class="text-sm font-black text-[#002855]">{{ $item->mataKuliahTujuan->nama_mk }}</div>
                                    <div class="text-[10px] font-mono text-slate-400 mt-0.5 uppercase">{{ $item->mataKuliahTujuan->kode_mk }} &bull; {{ $item->mataKuliahTujuan->sks_default }} SKS</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right align-middle">
                            <div class="flex justify-end gap-2 opacity-60 group-hover:opacity-100 transition-opacity">
                                <button wire:click="edit('{{ $item->id }}')" class="p-2 text-[#002855] hover:bg-[#002855]/10 rounded-lg transition-colors border border-transparent hover:border-[#002855]/20" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                </button>
                                <button wire:click="delete('{{ $item->id }}')" wire:confirm="Hapus pemetaan ini?" class="p-2 text-rose-600 hover:bg-rose-50 rounded-lg transition-colors border border-transparent hover:border-rose-200" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="bg-slate-50 p-4 rounded-full mb-3 border border-slate-100">
                                    <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                </div>
                                <p class="text-slate-500 font-medium italic">Belum ada data penyetaraan.</p>
                                <p class="text-xs text-slate-400 mt-1">Tambahkan pemetaan mata kuliah untuk kurikulum baru.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
            {{ $listEkuivalensi->links() }}
        </div>
    </div>
</div>