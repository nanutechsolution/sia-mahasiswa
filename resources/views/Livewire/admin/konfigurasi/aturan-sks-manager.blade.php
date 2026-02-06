<div class="space-y-6">
    
    {{-- Header & Toolbar --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-[#002855]">Aturan Beban SKS</h1>
            <p class="text-slate-500 text-sm mt-1">Konfigurasi batas maksimal pengambilan SKS berdasarkan Indeks Prestasi (IP) mahasiswa.</p>
        </div>
        
        @if(!$showForm)
        <button wire:click="create" class="inline-flex items-center px-5 py-2.5 bg-[#fcc000] text-[#002855] rounded-xl font-black text-sm shadow-lg shadow-orange-500/20 hover:bg-[#fbbf24] hover:scale-105 transition-all">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" /></svg>
            Tambah Aturan
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

    {{-- Form Section --}}
    @if($showForm)
    <div class="bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden animate-in zoom-in-95 duration-200">
        <div class="px-8 py-5 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
            <h3 class="text-sm font-bold text-[#002855] uppercase tracking-wider flex items-center gap-2">
                @if($editMode) Edit Aturan SKS @else Buat Aturan Baru @endif
            </h3>
            <button wire:click="batal" class="text-slate-400 hover:text-slate-600 transition-colors">&times;</button>
        </div>

        <div class="p-8">
            <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    {{-- Input IPS Min --}}
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">IPS Minimal ( > )</label>
                        <div class="relative">
                            <input type="number" step="0.01" wire:model="min_ips" class="block w-full rounded-lg border-slate-300 focus:border-[#002855] focus:ring-[#002855] text-sm font-bold text-center p-2.5" placeholder="0.00">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <span class="text-slate-400 text-xs">IP</span>
                            </div>
                        </div>
                        @error('min_ips') <span class="text-rose-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    
                    {{-- Input IPS Max --}}
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">IPS Maksimal ( <= )</label>
                        <div class="relative">
                            <input type="number" step="0.01" wire:model="max_ips" class="block w-full rounded-lg border-slate-300 focus:border-[#002855] focus:ring-[#002855] text-sm font-bold text-center p-2.5" placeholder="4.00">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <span class="text-slate-400 text-xs">IP</span>
                            </div>
                        </div>
                        @error('max_ips') <span class="text-rose-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- Input SKS --}}
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Jatah SKS Maksimal</label>
                        <div class="relative">
                            <input type="number" wire:model="max_sks" class="block w-full rounded-lg border-slate-300 bg-white text-[#002855] text-lg font-black text-center p-2.5 focus:border-[#fcc000] focus:ring-[#fcc000]" placeholder="24">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <span class="text-slate-400 text-xs font-bold">SKS</span>
                            </div>
                        </div>
                        @error('max_sks') <span class="text-rose-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <div class="pt-2 border-t border-slate-100 flex justify-end gap-3">
                <button wire:click="batal" class="px-6 py-2.5 text-sm font-bold text-slate-500 hover:text-slate-700 transition-colors">Batalkan</button>
                <button wire:click="save" class="px-8 py-2.5 bg-[#002855] text-white rounded-xl text-sm font-bold shadow-lg hover:bg-[#001a38] hover:scale-105 transition-all flex items-center">
                    <span wire:loading.remove wire:target="save">Simpan Aturan</span>
                    <span wire:loading wire:target="save">Menyimpan...</span>
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- Tabel Data --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden relative">
        
        {{-- Loading Overlay --}}
        <div wire:loading.flex wire:target="delete, edit" class="absolute inset-0 z-20 bg-white/60 backdrop-blur-[1px] items-center justify-center hidden">
             <div class="flex flex-col items-center justify-center p-4 bg-white rounded-2xl shadow-xl border border-slate-100">
                 <svg class="w-8 h-8 text-[#002855] animate-spin mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                 <span class="text-xs font-bold text-slate-500 animate-pulse">Memproses...</span>
             </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#002855] border-b border-[#001a38] text-white">
                        <th class="px-6 py-4 text-center text-[10px] font-bold uppercase tracking-widest w-1/3">Range IPS (Indeks Prestasi)</th>
                        <th class="px-6 py-4 text-center text-[10px] font-bold uppercase tracking-widest w-1/3">Batas Maksimal SKS</th>
                        <th class="px-6 py-4 text-right text-[10px] font-bold uppercase tracking-widest w-1/3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($aturan as $item)
                    <tr class="hover:bg-slate-50/80 transition-colors group">
                        <td class="px-6 py-4 text-center align-middle">
                            <div class="inline-flex items-center text-sm font-bold text-slate-700 bg-white border border-slate-200 px-4 py-2 rounded-lg shadow-sm">
                                <span class="text-slate-400 mr-2 text-xs">IP</span>
                                {{ number_format($item->min_ips, 2) }} 
                                <span class="mx-2 text-slate-300">➜</span>
                                {{ number_format($item->max_ips, 2) }}
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center align-middle">
                            <span class="inline-flex items-center justify-center w-16 h-8 text-sm font-black text-[#002855] bg-indigo-50 rounded-lg border border-indigo-100">
                                {{ $item->max_sks }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right align-middle">
                            <div class="flex justify-end gap-2 opacity-60 group-hover:opacity-100 transition-opacity">
                                <button wire:click="edit({{ $item->id }})" class="p-2 text-[#002855] hover:bg-[#002855]/10 rounded-lg transition-colors border border-transparent hover:border-[#002855]/20" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                </button>
                                <button wire:click="delete({{ $item->id }})" wire:confirm="Hapus aturan ini?" class="p-2 text-rose-600 hover:bg-rose-50 rounded-lg transition-colors border border-transparent hover:border-rose-200" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="bg-slate-50 p-4 rounded-full mb-3 border border-slate-100">
                                    <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                </div>
                                <p class="text-slate-500 font-medium italic">Belum ada aturan SKS dibuat.</p>
                                <p class="text-xs text-slate-400 mt-1">Tambahkan aturan baru untuk menentukan jatah SKS mahasiswa.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>