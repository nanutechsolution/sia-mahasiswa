<div class="space-y-8">
    
    {{-- Header & Toolbar --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-[#002855]">Skala Nilai</h1>
            <p class="text-slate-500 text-sm mt-1">Pengaturan bobot indeks, konversi nilai angka ke huruf, dan status kelulusan.</p>
        </div>

        @if(!$showForm)
        <button wire:click="create" class="inline-flex items-center px-5 py-2.5 bg-[#fcc000] text-[#002855] rounded-xl font-black text-sm shadow-lg shadow-orange-500/20 hover:bg-[#fbbf24] hover:scale-105 transition-all">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" /></svg>
            Tambah Skala
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
                @if($editMode) Edit Skala Nilai @else Input Skala Baru @endif
            </h3>
            <button wire:click="batal" class="text-slate-400 hover:text-slate-600 transition-colors">&times;</button>
        </div>

        <div class="p-8 space-y-6">
            <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100">
                <div class="grid grid-cols-1 md:grid-cols-5 gap-6 items-start">
                    
                    {{-- Huruf --}}
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Huruf Mutu *</label>
                        {{-- JS Validation: Hanya Huruf, +, - --}}
                        <input type="text" wire:model="huruf" 
                            oninput="this.value = this.value.toUpperCase().replace(/[^A-Z\+\-]/g, '')"
                            placeholder="A" class="block w-full rounded-lg border-slate-300 focus:border-[#002855] focus:ring-[#002855] text-xl font-black text-center uppercase p-2.5" maxlength="2">
                        @error('huruf') <span class="text-rose-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    
                    {{-- Bobot --}}
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Bobot Indeks *</label>
                        <input type="number" step="0.01" wire:model="bobot_indeks" placeholder="4.00" class="block w-full rounded-lg border-slate-300 focus:border-[#002855] focus:ring-[#002855] text-lg font-bold text-center p-2.5">
                        @error('bobot_indeks') <span class="text-rose-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- Rentang Min --}}
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Nilai Min ( > ) *</label>
                        <input type="number" step="0.01" wire:model="nilai_min" placeholder="80.00" class="block w-full rounded-lg border-slate-300 focus:border-[#002855] focus:ring-[#002855] text-sm font-bold text-center p-2.5">
                        @error('nilai_min') <span class="text-rose-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- Rentang Max --}}
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Nilai Max ( <= ) *</label>
                        <input type="number" step="0.01" wire:model="nilai_max" placeholder="100.00" class="block w-full rounded-lg border-slate-300 focus:border-[#002855] focus:ring-[#002855] text-sm font-bold text-center p-2.5">
                        @error('nilai_max') <span class="text-rose-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- Status Lulus --}}
                    <div class="pt-6 h-full flex items-center">
                        <label class="flex items-center justify-between cursor-pointer p-3 border rounded-xl w-full transition-all shadow-sm group {{ $is_lulus ? 'bg-emerald-50 border-emerald-200' : 'bg-white border-slate-200 hover:border-[#002855]' }}">
                            <div class="flex items-center">
                                <input type="checkbox" wire:model.live="is_lulus" class="rounded border-slate-300 text-[#002855] h-5 w-5 focus:ring-[#fcc000] cursor-pointer">
                                <span class="ml-3 text-xs font-bold uppercase {{ $is_lulus ? 'text-emerald-700' : 'text-slate-700 group-hover:text-[#002855]' }}">
                                    {{ $is_lulus ? 'Status: Lulus' : 'Status: Tidak Lulus' }}
                                </span>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex justify-end gap-3">
                <button wire:click="batal" class="px-6 py-2.5 text-sm font-bold text-slate-500 hover:text-slate-700 transition-colors">Batalkan</button>
                <button wire:click="save" class="px-8 py-2.5 bg-[#002855] text-white rounded-xl text-sm font-bold shadow-lg hover:bg-[#001a38] hover:scale-105 transition-all flex items-center">
                    <span wire:loading.remove wire:target="save">Simpan Perubahan</span>
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
                        <th class="px-6 py-4 text-center text-[10px] font-bold uppercase tracking-widest w-24">Huruf</th>
                        <th class="px-6 py-4 text-center text-[10px] font-bold uppercase tracking-widest w-24">Bobot</th>
                        <th class="px-6 py-4 text-center text-[10px] font-bold uppercase tracking-widest">Rentang Nilai (0-100)</th>
                        <th class="px-6 py-4 text-center text-[10px] font-bold uppercase tracking-widest">Status</th>
                        <th class="px-6 py-4 text-right text-[10px] font-bold uppercase tracking-widest">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($skala as $item)
                    <tr class="hover:bg-slate-50/80 transition-colors group">
                        <td class="px-6 py-4 text-center align-middle">
                            <span class="text-lg font-black text-[#002855] bg-indigo-50 px-3 py-1 rounded-lg border border-indigo-100">{{ $item->huruf }}</span>
                        </td>
                        <td class="px-6 py-4 text-center align-middle">
                            <span class="text-sm font-bold text-slate-700">{{ number_format($item->bobot_indeks, 2) }}</span>
                        </td>
                        <td class="px-6 py-4 text-center align-middle">
                            <div class="inline-flex items-center text-xs font-mono font-bold text-slate-600 bg-white border border-slate-200 px-4 py-1.5 rounded-full shadow-sm">
                                {{ number_format($item->nilai_min, 2) }} 
                                <span class="mx-2 text-slate-300">➜</span>
                                {{ number_format($item->nilai_max, 2) }}
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center align-middle">
                            @if($item->is_lulus)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wide bg-emerald-100 text-emerald-700 border border-emerald-200">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                                    Lulus
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wide bg-rose-100 text-rose-700 border border-rose-200">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12" /></svg>
                                    Gagal
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right align-middle">
                            <div class="flex justify-end gap-2 opacity-60 group-hover:opacity-100 transition-opacity">
                                <button wire:click="edit({{ $item->id }})" class="p-2 text-[#002855] hover:bg-[#002855]/10 rounded-lg transition-colors border border-transparent hover:border-[#002855]/20" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                </button>
                                <button wire:click="delete({{ $item->id }})" wire:confirm="Hapus skala nilai ini?" class="p-2 text-rose-600 hover:bg-rose-50 rounded-lg transition-colors border border-transparent hover:border-rose-200" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="bg-slate-50 p-4 rounded-full mb-3 border border-slate-100">
                                    <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                                </div>
                                <p class="text-slate-500 font-medium italic">Belum ada data skala nilai.</p>
                                <p class="text-xs text-slate-400 mt-1">Silakan tambahkan skala nilai baru untuk penilaian.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>