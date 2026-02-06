<div class="space-y-6 md:space-y-8 animate-in fade-in duration-700 pb-12">

    {{-- 1. HEADER SECTON --}}
    <div class="bg-white rounded-[2rem] border border-slate-200 shadow-sm p-6 md:p-8 flex flex-col md:flex-row justify-between items-center gap-6">
        <div class="flex items-center gap-5 w-full md:w-auto">
            <div class="w-14 h-14 bg-[#002855] text-[#fcc000] rounded-2xl flex items-center justify-center text-2xl shadow-lg shrink-0">
                📚
            </div>
            <div>
                <h1 class="text-xl md:text-2xl font-black text-[#002855] uppercase tracking-tight">Katalog Mata Kuliah</h1>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">Manajemen Kurikulum & Beban SKS</p>
            </div>
        </div>

        @if(!$showForm)
        <button wire:click="create" class="w-full md:w-auto px-8 py-3.5 bg-[#002855] text-white rounded-2xl font-black text-[10px] uppercase tracking-[0.2em] shadow-xl shadow-indigo-900/20 hover:bg-black hover:scale-105 active:scale-95 transition-all">
            + Tambah Mata Kuliah
        </button>
        @endif
    </div>

    {{-- Alert Notifikasi --}}
    @if (session()->has('success'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show" x-transition class="p-4 bg-emerald-50 border border-emerald-100 text-emerald-700 rounded-2xl text-xs font-bold flex items-center shadow-sm">
            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        {{-- 2. FORM EDITOR (CONDITIONAL) --}}
        @if($showForm)
        <div class="lg:col-span-12">
            <div class="bg-white rounded-[2.5rem] shadow-2xl border border-slate-200 overflow-hidden animate-in zoom-in-95 duration-300">
                <div class="px-8 py-6 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-[10px] font-black text-[#002855] uppercase tracking-[0.2em]">
                        {{ $editMode ? 'Perbarui Data Mata Kuliah' : 'Input Mata Kuliah Baru' }}
                    </h3>
                    <button wire:click="batal" class="text-slate-400 hover:text-rose-500 transition-colors text-3xl font-light leading-none">&times;</button>
                </div>

                <form wire:submit.prevent="save" class="p-8 md:p-12 space-y-10">
                    
                    {{-- A. Identitas Mata Kuliah --}}
                    <div class="grid grid-cols-1 md:grid-cols-6 gap-8">
                        <div class="md:col-span-2 space-y-2">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Kode Mata Kuliah *</label>
                            <input type="text" wire:model="kode_mk" placeholder="Cth: TI-101" 
                                class="w-full rounded-2xl border-slate-200 bg-slate-50 py-4 px-6 font-black text-[#002855] focus:ring-2 focus:ring-[#fcc000] focus:border-[#fcc000] uppercase transition-all @error('kode_mk') !border-rose-400 !bg-rose-50 !text-rose-900 @enderror">
                            @error('kode_mk') <span class="text-[9px] text-rose-500 font-bold uppercase tracking-wide flex items-center gap-1 mt-1">⚠ {{ $message }}</span> @enderror
                        </div>

                        <div class="md:col-span-4 space-y-2">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Nama Lengkap Mata Kuliah *</label>
                            <input type="text" wire:model="nama_mk" placeholder="Cth: Algoritma dan Pemrograman" 
                                class="w-full rounded-2xl border-slate-200 bg-slate-50 py-4 px-6 font-bold text-slate-700 focus:ring-2 focus:ring-[#fcc000] focus:border-[#fcc000] transition-all @error('nama_mk') !border-rose-400 !bg-rose-50 !text-rose-900 @enderror">
                            @error('nama_mk') <span class="text-[9px] text-rose-500 font-bold uppercase tracking-wide flex items-center gap-1 mt-1">⚠ {{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- B. Policy Driven Choice (Activity Type) --}}
                    <div class="bg-indigo-50/50 p-6 md:p-8 rounded-[2rem] border border-indigo-100 space-y-6">
                        <label class="block text-[10px] font-black text-indigo-600 uppercase tracking-[0.2em] text-center">
                            Jenis Aktivitas (Menentukan Perhitungan SKS)
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                            @foreach($types as $key => $type)
                            <label class="relative cursor-pointer group h-full">
                                <input type="radio" wire:model.live="activity_type" value="{{ $key }}" class="peer sr-only">
                                <div class="p-5 bg-white border-2 border-transparent rounded-2xl hover:border-indigo-200 peer-checked:border-[#002855] peer-checked:bg-white peer-checked:ring-4 peer-checked:ring-indigo-50/50 transition-all h-full flex flex-col items-center justify-center text-center shadow-sm">
                                    <span class="text-3xl mb-3 grayscale group-hover:grayscale-0 peer-checked:grayscale-0 transition-all">{{ $type['icon'] }}</span>
                                    <span class="font-black text-xs text-slate-600 peer-checked:text-[#002855] mb-1">{{ $type['label'] }}</span>
                                    <span class="text-[9px] text-slate-400 leading-tight">{{ $type['desc'] }}</span>
                                </div>
                                {{-- Checkmark Indicator --}}
                                <div class="absolute top-3 right-3 w-4 h-4 rounded-full bg-[#002855] text-white flex items-center justify-center opacity-0 peer-checked:opacity-100 transition-opacity scale-50 peer-checked:scale-100">
                                    <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7"/></svg>
                                </div>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- C. Logic SKS --}}
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-start">
                        @php
                            $isManual = $activity_type === 'THESIS';
                            $isZero   = $activity_type === 'CONTINUATION';
                            $disableComponent = $isManual || $isZero;
                        @endphp

                        {{-- Komponen SKS --}}
                        <div class="md:col-span-8 grid grid-cols-3 gap-4">
                            <div class="space-y-2">
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Tatap Muka</label>
                                <input type="number" min="0" wire:model.live="sks_tatap_muka" @if($disableComponent) disabled @endif
                                    class="w-full rounded-2xl border-slate-200 bg-white py-4 text-center text-lg font-black text-slate-700 focus:ring-[#fcc000] disabled:bg-slate-100 disabled:text-slate-400 transition-colors">
                            </div>
                            <div class="space-y-2">
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Praktek</label>
                                <input type="number" min="0" wire:model.live="sks_praktek" @if($disableComponent) disabled @endif
                                    class="w-full rounded-2xl border-slate-200 bg-white py-4 text-center text-lg font-black text-slate-700 focus:ring-[#fcc000] disabled:bg-slate-100 disabled:text-slate-400 transition-colors">
                            </div>
                            <div class="space-y-2">
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Lapangan</label>
                                <input type="number" min="0" wire:model.live="sks_lapangan" @if($disableComponent) disabled @endif
                                    class="w-full rounded-2xl border-slate-200 bg-white py-4 text-center text-lg font-black text-slate-700 focus:ring-[#fcc000] disabled:bg-slate-100 disabled:text-slate-400 transition-colors">
                            </div>
                        </div>

                        {{-- Total SKS --}}
                        <div class="md:col-span-4">
                            <div class="bg-[#fcc000] p-6 rounded-[2rem] flex flex-col items-center justify-center shadow-lg shadow-orange-500/20 relative overflow-hidden group {{ $isManual ? 'cursor-text ring-4 ring-orange-200' : 'cursor-default' }}">
                                <p class="text-[10px] font-black text-[#002855] uppercase tracking-[0.2em] mb-1 z-10">Total SKS</p>
                                
                                <input type="number" wire:model="sks_default" @if(!$isManual) readonly @endif
                                    class="w-full bg-transparent border-none text-center text-5xl font-black text-[#002855] tracking-tighter p-0 focus:ring-0 z-10">
                                
                                <span class="text-[9px] font-bold text-[#002855] opacity-60 uppercase z-10 mt-1">
                                    @if($isManual) (Input Manual) @elseif($isZero) (Wajib 0 SKS) @else (Auto Kalkulasi) @endif
                                </span>

                                {{-- Background Pattern --}}
                                <div class="absolute -right-4 -bottom-4 text-8xl opacity-10 rotate-12 pointer-events-none select-none">🧮</div>
                            </div>
                            @error('sks_default') <span class="text-[9px] text-rose-500 font-bold uppercase text-center block mt-2">⚠ {{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- D. Administrative Info --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-4">
                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Program Studi Pemilik</label>
                            <select wire:model="prodi_id" class="w-full rounded-2xl border-slate-200 bg-slate-50 py-4 px-6 font-bold text-slate-700 focus:ring-[#fcc000] cursor-pointer">
                                @foreach($prodis as $p) <option value="{{ $p->id }}">{{ $p->nama_prodi }}</option> @endforeach
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Jenis MK (Pelaporan Feeder)</label>
                            <select wire:model="jenis_mk" class="w-full rounded-2xl border-slate-200 bg-slate-50 py-4 px-6 font-bold text-slate-700 focus:ring-[#fcc000] cursor-pointer">
                                <option value="A">Wajib Nasional</option>
                                <option value="B">Wajib Program Studi</option>
                                <option value="C">Mata Kuliah Pilihan</option>
                                <option value="D">Tugas Akhir / Skripsi</option>
                            </select>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="pt-10 border-t border-slate-100 flex flex-col md:flex-row justify-end items-center gap-4">
                        <button type="button" wire:click="batal" class="w-full md:w-auto px-10 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-rose-500 transition-colors">Batalkan Setup</button>
                        
                        <button type="submit" wire:loading.attr="disabled" class="w-full md:w-auto px-16 py-4 bg-[#002855] text-white rounded-2xl font-black text-xs uppercase tracking-[0.3em] shadow-2xl shadow-indigo-900/40 hover:scale-105 active:scale-95 disabled:opacity-70 disabled:scale-100 transition-all flex items-center justify-center gap-2">
                            <span wire:loading.remove>{{ $editMode ? 'Simpan Perubahan' : 'Terbitkan Mata Kuliah' }}</span>
                            <span wire:loading>Menyimpan...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endif

        {{-- 3. FILTER BAR & TABLE (VISIBLE IF NOT EDITING) --}}
        @if(!$showForm)
        <div class="lg:col-span-12 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="md:col-span-2 relative group">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari Nama atau Kode MK..." class="w-full rounded-2xl border-slate-200 bg-white py-4 pl-12 pr-4 text-sm font-bold focus:ring-[#002855] transition-all shadow-sm">
                <svg class="w-5 h-5 absolute left-4 top-4 text-slate-300 group-focus-within:text-[#002855] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2.5" /></svg>
            </div>
            <div class="relative">
                <select wire:model.live="filterProdiId" class="w-full rounded-2xl border-slate-200 bg-white py-4 px-6 text-sm font-bold text-[#002855] focus:ring-[#002855] shadow-sm appearance-none cursor-pointer">
                    <option value="">Semua Program Studi</option>
                    @foreach($prodis as $p) <option value="{{ $p->id }}">{{ $p->nama_prodi }}</option> @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-4 flex items-center text-slate-300">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="3"/></svg>
                </div>
            </div>
        </div>

        <div class="lg:col-span-12">
            <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-200 overflow-hidden relative">
                <div wire:loading.flex wire:target="filterProdiId, delete" class="absolute inset-0 bg-white/60 z-10 items-center justify-center backdrop-blur-[1px]">
                    <svg class="animate-spin h-8 w-8 text-[#002855]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100">
                        <thead class="bg-slate-50/50">
                            <tr>
                                <th class="px-8 py-5 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Mata Kuliah</th>
                                <th class="px-6 py-5 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest">Kredit SKS</th>
                                <th class="px-6 py-5 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest">Aktivitas</th>
                                <th class="px-6 py-5 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Prodi</th>
                                <th class="px-8 py-5 text-right text-[10px] font-black text-slate-400 uppercase tracking-widest">Opsi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 bg-white">
                            @forelse($mks as $mk)
                            <tr class="hover:bg-slate-50/80 transition-colors group">
                                <td class="px-8 py-5">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-xl bg-indigo-50 text-[#002855] flex items-center justify-center font-black text-[10px] shadow-inner uppercase shrink-0">
                                            {{ substr($mk->nama_mk, 0, 1) }}
                                        </div>
                                        <div class="min-w-0">
                                            <h4 class="text-sm font-black text-slate-700 uppercase truncate group-hover:text-indigo-600 transition-colors">{{ $mk->nama_mk }}</h4>
                                            <p class="text-[10px] font-mono font-bold text-slate-400 tracking-widest mt-0.5 uppercase">{{ $mk->kode_mk }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-5 text-center">
                                    <span class="text-lg font-black text-[#002855] tracking-tighter">{{ $mk->sks_default }}</span>
                                    <div class="text-[8px] font-black text-slate-300 uppercase mt-1">
                                        T:{{ $mk->sks_tatap_muka }} P:{{ $mk->sks_praktek }} L:{{ $mk->sks_lapangan }}
                                    </div>
                                </td>
                                <td class="px-6 py-5 text-center">
                                    @php
                                        $style = match($mk->activity_type) {
                                            'THESIS' => 'bg-amber-100 text-amber-700 border-amber-200',
                                            'MBKM' => 'bg-sky-100 text-sky-700 border-sky-200',
                                            'CONTINUATION' => 'bg-purple-100 text-purple-700 border-purple-200',
                                            default => 'bg-slate-100 text-slate-500 border-slate-200'
                                        };
                                        // Cari label dari array types di controller (optional fallback)
                                        $label = $mk->activity_type ?? 'REGULAR';
                                    @endphp
                                    <span class="px-2.5 py-1 rounded-lg text-[9px] font-black uppercase tracking-tighter border {{ $style }}">
                                        {{ $label }}
                                    </span>
                                </td>
                                <td class="px-6 py-5">
                                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-tighter">{{ $mk->prodi->nama_prodi }}</span>
                                </td>
                                <td class="px-8 py-5 text-right whitespace-nowrap space-x-1">
                                    <button wire:click="edit('{{ $mk->id }}')" class="px-4 py-2 text-indigo-600 hover:bg-indigo-50 hover:text-indigo-800 rounded-xl transition-all font-black text-[10px] uppercase tracking-widest inline-flex items-center gap-2">
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                        Edit
                                    </button>
                                    <button wire:click="delete('{{ $mk->id }}')" wire:confirm="Hapus mata kuliah ini secara permanen?" class="px-4 py-2 text-rose-500 hover:bg-rose-50 hover:text-rose-700 rounded-xl transition-all font-black text-[10px] uppercase tracking-widest inline-flex items-center gap-2">
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        Hapus
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="py-20 text-center text-slate-400 font-bold uppercase tracking-[0.2em] italic">Katalog masih kosong</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-8 bg-slate-50/50 border-t border-slate-100">
                    {{ $mks->links() }}
                </div>
            </div>
        </div>
        @endif
    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(0, 40, 85, 0.08); border-radius: 10px; }
    </style>
</div>