<div class="space-y-6">
    {{-- Header & Toolbar --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-unmaris-blue">Pengaturan Skala Nilai</h1>
            <p class="text-slate-500 text-sm mt-1">Atur bobot indeks (A-E), rentang nilai angka, dan status kelulusan prasyarat.</p>
        </div>

        @if(!$showForm)
        <button wire:click="create"
            class="inline-flex items-center px-5 py-2.5 bg-[#fcc000] text-unmaris-blue rounded-xl font-black text-sm shadow-lg shadow-orange-500/20 hover:bg-[#fbbf24] hover:scale-105 transition-all">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Skala Baru
        </button>
        @endif
    </div>

    {{-- Alert Messages --}}
    @if (session()->has('success'))
        <div class="bg-emerald-50 border border-emerald-100 p-4 rounded-xl text-emerald-800 text-sm flex items-center shadow-sm animate-in fade-in slide-in-from-top-2">
            <svg class="w-5 h-5 mr-3 text-emerald-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            <span class="font-bold">{{ session('success') }}</span>
        </div>
    @endif

    {{-- Form Section --}}
    @if($showForm)
    <div class="bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden animate-in zoom-in-95 duration-200">
        <div class="px-8 py-5 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
            <h3 class="text-sm font-bold text-unmaris-blue uppercase tracking-wider flex items-center gap-2">
                @if($selectedId)
                    <svg class="w-5 h-5 text-[#fcc000]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                    Edit Skala Nilai
                @else
                    <svg class="w-5 h-5 text-unmaris-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    Input Skala Baru
                @endif
            </h3>
            <button wire:click="$set('showForm', false)" class="text-slate-400 hover:text-slate-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>
        
        <div class="p-8 space-y-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 bg-slate-50 p-6 rounded-xl border border-slate-100 items-start">
                
                {{-- Huruf --}}
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-2">Huruf Mutu *</label>
                    <input type="text" wire:model="huruf" placeholder="A" class="block w-full rounded-lg border-slate-300 focus:border-unmaris-blue focus:ring-unmaris-blue text-lg font-black text-center uppercase" maxlength="2">
                    @error('huruf') <span class="text-rose-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>
                
                {{-- Bobot --}}
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-2">Bobot Indeks *</label>
                    <input type="number" step="0.01" wire:model="bobot_indeks" placeholder="4.00" class="block w-full rounded-lg border-slate-300 focus:border-unmaris-blue focus:ring-unmaris-blue text-lg font-bold text-center">
                    @error('bobot_indeks') <span class="text-rose-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- Rentang Min --}}
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-2">Nilai Angka (Min) *</label>
                    <input type="number" step="0.01" wire:model="nilai_min" placeholder="80.00" class="block w-full rounded-lg border-slate-300 focus:border-unmaris-blue focus:ring-unmaris-blue text-sm text-center">
                    @error('nilai_min') <span class="text-rose-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- Rentang Max --}}
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-2">Nilai Angka (Max) *</label>
                    <input type="number" step="0.01" wire:model="nilai_max" placeholder="100.00" class="block w-full rounded-lg border-slate-300 focus:border-unmaris-blue focus:ring-unmaris-blue text-sm text-center">
                    @error('nilai_max') <span class="text-rose-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- Status Lulus --}}
                <div class="flex items-center h-full pt-6">
                    <label class="flex items-center cursor-pointer p-3 bg-white border border-slate-200 rounded-lg w-full hover:bg-indigo-50 transition-colors">
                        <input type="checkbox" wire:model="is_lulus" class="rounded border-slate-300 text-unmaris-blue shadow-sm focus:border-unmaris-blue focus:ring focus:ring-unmaris-blue focus:ring-opacity-50 h-5 w-5">
                        <span class="ml-3 text-xs font-bold text-unmaris-blue uppercase">Dianggap Lulus?</span>
                    </label>
                </div>
            </div>

            <div class="pt-2 border-t border-slate-100 flex justify-end gap-3">
                <button wire:click="$set('showForm', false)" class="px-6 py-2.5 text-sm font-bold text-slate-500 hover:text-slate-700 hover:bg-slate-100 rounded-xl transition-all">Batalkan</button>
                <button wire:click="save" class="px-8 py-2.5 bg-unmaris-blue text-white rounded-xl text-sm font-bold shadow-lg hover:bg-[#001a38] hover:scale-105 transition-all">Simpan Skala</button>
            </div>
        </div>
    </div>
    @endif

    {{-- Tabel Data --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-unmaris-blue border-b border-[#001a38] text-white">
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest w-20 text-center">Huruf Mutu</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-center">Bobot Indeks</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-center">Rentang Nilai (Angka)</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-center">Status Kelulusan</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($skala as $item)
                    <tr class="hover:bg-slate-50/80 transition-colors group">
                        <td class="px-6 py-4 text-center">
                            <span class="text-xl font-black text-unmaris-blue bg-indigo-50 px-3 py-1 rounded-lg border border-indigo-100">{{ $item->huruf }}</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="text-sm font-bold text-slate-700">{{ number_format($item->bobot_indeks, 2) }}</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="inline-flex items-center text-xs font-mono font-bold text-slate-500 bg-slate-100 px-3 py-1 rounded-full">
                                {{ number_format($item->nilai_min, 2) }} 
                                <svg class="w-3 h-3 mx-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
                                {{ number_format($item->nilai_max, 2) }}
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($item->is_lulus)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wide bg-emerald-100 text-emerald-700 border border-emerald-200">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                                    Lulus
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wide bg-rose-100 text-rose-700 border border-rose-200">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12" /></svg>
                                    Tidak Lulus
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2 opacity-60 group-hover:opacity-100 transition-opacity">
                                <button wire:click="edit({{ $item->id }})" class="p-2 text-unmaris-blue hover:bg-unmaris-blue/10 rounded-lg transition-colors" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                </button>
                                <button wire:click="delete({{ $item->id }})" wire:confirm="Hapus skala nilai ini?" class="p-2 text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="bg-slate-50 p-4 rounded-full mb-3">
                                    <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                                </div>
                                <p class="text-slate-500 font-medium italic">Belum ada data skala nilai.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>