<div class="space-y-6 md:space-y-8 animate-in fade-in duration-700 pb-12 max-w-[1600px] mx-auto px-4 sm:px-6 md:px-8">

    {{-- 1. HEADER SECTION --}}
    <div class="bg-white rounded-[2rem] border border-slate-200 shadow-sm p-6 md:p-8 flex flex-col md:flex-row justify-between items-center gap-6">
        <div class="flex items-center gap-5 w-full md:w-auto">
            <div class="w-14 h-14 bg-[#002855] text-[#fcc000] rounded-2xl flex items-center justify-center text-2xl shadow-lg shadow-blue-900/20 shrink-0">
                📚
            </div>
            <div>
                <h1 class="text-xl md:text-2xl font-black text-[#002855] uppercase tracking-tight">Katalog Mata Kuliah</h1>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">Manajemen Kurikulum & Beban SKS</p>
            </div>
        </div>

        @if(!$showForm)
        <button wire:click="create" class="w-full md:w-auto px-8 py-3.5 bg-[#002855] text-white rounded-2xl font-black text-[10px] uppercase tracking-[0.2em] shadow-xl shadow-indigo-900/20 hover:-translate-y-1 active:scale-95 transition-all">
            + Tambah Mata Kuliah
        </button>
        @endif
    </div>

    {{-- Alert Messages --}}
    <div class="space-y-3">
        @if (session()->has('success'))
            <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show" x-transition class="p-4 bg-emerald-50 border border-emerald-100 text-emerald-800 rounded-2xl text-xs font-bold flex items-center shadow-sm">
                <svg class="w-5 h-5 mr-3 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                {{ session('success') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 6000)" x-show="show" x-transition class="p-4 bg-rose-50 border border-rose-100 text-rose-800 rounded-2xl text-xs font-bold flex items-center shadow-sm">
                <svg class="w-5 h-5 mr-3 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                {{ session('error') }}
            </div>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        {{-- 2. FORM EDITOR (CONDITIONAL) --}}
        @if($showForm)
        <div class="lg:col-span-12">
            <div class="bg-white rounded-[2.5rem] shadow-2xl border border-slate-200 overflow-hidden animate-in slide-in-from-bottom-8 duration-500">
                <div class="px-8 py-6 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-[10px] font-black text-[#002855] uppercase tracking-[0.2em] flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-[#fcc000]"></span>
                        {{ $editMode ? 'Perbarui Data Mata Kuliah' : 'Input Mata Kuliah Baru' }}
                    </h3>
                    <button wire:click="batal" class="text-slate-400 hover:text-rose-500 bg-white shadow-sm p-2 rounded-full transition-colors flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form wire:submit.prevent="save" class="p-8 md:p-12 space-y-10">
                    
                    {{-- A. Identitas Mata Kuliah --}}
                    <div class="grid grid-cols-1 md:grid-cols-6 gap-8">
                        <div class="md:col-span-2 space-y-2">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest pl-1">Kode Mata Kuliah *</label>
                            <input type="text" wire:model="kode_mk" placeholder="Cth: TI-101" 
                                class="w-full rounded-2xl border-slate-200 bg-slate-50 py-4 px-5 font-black text-[#002855] focus:ring-2 focus:ring-[#fcc000] focus:border-[#fcc000] uppercase transition-all @error('kode_mk') border-rose-400 bg-rose-50 text-rose-900 @enderror">
                            @error('kode_mk') <span class="text-[10px] text-rose-500 font-bold mt-1 block pl-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="md:col-span-4 space-y-2">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest pl-1">Nama Lengkap Mata Kuliah *</label>
                            <input type="text" wire:model="nama_mk" placeholder="Cth: Algoritma dan Pemrograman" 
                                class="w-full rounded-2xl border-slate-200 bg-slate-50 py-4 px-5 font-bold text-[#002855] focus:ring-2 focus:ring-[#fcc000] focus:border-[#fcc000] transition-all @error('nama_mk') border-rose-400 bg-rose-50 text-rose-900 @enderror">
                            @error('nama_mk') <span class="text-[10px] text-rose-500 font-bold mt-1 block pl-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- B. Policy Driven Choice (Activity Type) --}}
                    <div class="bg-indigo-50/50 p-6 md:p-8 rounded-[2rem] border border-indigo-100/50 space-y-6">
                        <label class="block text-[10px] font-black text-indigo-600 uppercase tracking-[0.2em] text-center">
                            Jenis Aktivitas (Menentukan Metode SKS)
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                            @foreach($types as $key => $type)
                            <label class="relative cursor-pointer group h-full">
                                <input type="radio" wire:model.live="activity_type" value="{{ $key }}" class="peer sr-only">
                                <div class="p-5 bg-white border-2 border-transparent rounded-2xl hover:border-indigo-200 peer-checked:border-[#002855] peer-checked:bg-white peer-checked:shadow-md transition-all h-full flex flex-col items-center justify-center text-center">
                                    <span class="text-3xl mb-3 grayscale group-hover:grayscale-0 peer-checked:grayscale-0 transition-all">{{ $type['icon'] }}</span>
                                    <span class="font-black text-xs text-slate-600 peer-checked:text-[#002855] mb-1">{{ $type['label'] }}</span>
                                    <span class="text-[9px] font-bold text-slate-400 leading-tight">{{ $type['desc'] }}</span>
                                </div>
                                <div class="absolute top-3 right-3 w-4 h-4 rounded-full bg-[#002855] text-white flex items-center justify-center opacity-0 peer-checked:opacity-100 transition-opacity scale-50 peer-checked:scale-100 shadow-sm">
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
                                    class="w-full rounded-2xl border-slate-200 bg-white py-4 text-center text-xl font-black text-[#002855] focus:ring-[#fcc000] disabled:bg-slate-50 disabled:text-slate-300 transition-colors shadow-sm">
                            </div>
                            <div class="space-y-2">
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Praktek</label>
                                <input type="number" min="0" wire:model.live="sks_praktek" @if($disableComponent) disabled @endif
                                    class="w-full rounded-2xl border-slate-200 bg-white py-4 text-center text-xl font-black text-[#002855] focus:ring-[#fcc000] disabled:bg-slate-50 disabled:text-slate-300 transition-colors shadow-sm">
                            </div>
                            <div class="space-y-2">
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Lapangan</label>
                                <input type="number" min="0" wire:model.live="sks_lapangan" @if($disableComponent) disabled @endif
                                    class="w-full rounded-2xl border-slate-200 bg-white py-4 text-center text-xl font-black text-[#002855] focus:ring-[#fcc000] disabled:bg-slate-50 disabled:text-slate-300 transition-colors shadow-sm">
                            </div>
                        </div>

                        {{-- Total SKS --}}
                        <div class="md:col-span-4">
                            <div class="bg-[#fcc000] p-6 rounded-[2rem] flex flex-col items-center justify-center shadow-lg shadow-amber-500/20 relative overflow-hidden group {{ $isManual ? 'cursor-text ring-4 ring-amber-200' : 'cursor-default' }}">
                                <p class="text-[10px] font-black text-[#002855] uppercase tracking-[0.2em] mb-1 z-10">Total SKS</p>
                                
                                <input type="number" wire:model="sks_default" @if(!$isManual) readonly @endif
                                    class="w-full bg-transparent border-none text-center text-5xl font-black text-[#002855] tracking-tighter p-0 focus:ring-0 z-10">
                                
                                <span class="text-[9px] font-bold text-[#002855] opacity-60 uppercase z-10 mt-1 tracking-widest">
                                    @if($isManual) (Input Manual) @elseif($isZero) (Wajib 0 SKS) @else (Kalkulasi Otomatis) @endif
                                </span>

                                <div class="absolute -right-4 -bottom-4 text-8xl opacity-10 rotate-12 pointer-events-none select-none">🧮</div>
                            </div>
                            @error('sks_default') <span class="text-[10px] text-rose-500 font-bold uppercase text-center block mt-2 tracking-widest">⚠ {{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- D. Administrative Info --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-4">
                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest pl-1">Program Studi Pemilik</label>
                            <select wire:model="prodi_id" class="w-full rounded-2xl border-slate-200 bg-slate-50 py-4 px-5 font-bold text-[#002855] focus:ring-[#fcc000] cursor-pointer">
                                @foreach($prodis as $p) <option value="{{ $p->id }}">{{ $p->jenjang }} - {{ $p->nama_prodi }}</option> @endforeach
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest pl-1">Jenis MK (Pelaporan Feeder)</label>
                            <select wire:model="jenis_mk" class="w-full rounded-2xl border-slate-200 bg-slate-50 py-4 px-5 font-bold text-[#002855] focus:ring-[#fcc000] cursor-pointer">
                                <option value="A">Wajib Nasional</option>
                                <option value="B">Wajib Program Studi</option>
                                <option value="C">Mata Kuliah Pilihan</option>
                                <option value="D">Tugas Akhir / Skripsi</option>
                            </select>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="pt-8 border-t border-slate-100 flex flex-col md:flex-row justify-end items-center gap-4">
                        <button type="button" wire:click="batal" class="w-full md:w-auto px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-rose-500 hover:bg-rose-50 rounded-xl transition-all">Batalkan Setup</button>
                        
                        <button type="submit" wire:loading.attr="disabled" class="w-full md:w-auto px-12 py-4 bg-[#002855] text-white rounded-xl font-black text-[10px] uppercase tracking-[0.2em] shadow-xl shadow-indigo-900/20 hover:-translate-y-1 active:scale-95 disabled:opacity-70 disabled:hover:translate-y-0 transition-all flex items-center justify-center gap-3">
                            <span wire:loading.remove>{{ $editMode ? 'Simpan Perubahan' : 'Terbitkan Mata Kuliah' }}</span>
                            <span wire:loading>Menyimpan Data...</span>
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
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari Nama atau Kode MK..." class="w-full rounded-2xl border-slate-200 bg-white py-4 pl-12 pr-4 text-sm font-bold focus:ring-2 focus:ring-indigo-100 transition-all shadow-sm text-slate-700">
                <svg class="w-5 h-5 absolute left-4 top-4 text-slate-400 group-focus-within:text-[#002855] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2.5" /></svg>
            </div>
            <div class="relative">
                <select wire:model.live="filterProdiId" class="w-full rounded-2xl border-slate-200 bg-white py-4 px-6 text-sm font-bold text-[#002855] focus:ring-2 focus:ring-indigo-100 shadow-sm appearance-none cursor-pointer">
                    <option value="">Semua Program Studi</option>
                    @foreach($prodis as $p) <option value="{{ $p->id }}">{{ $p->nama_prodi }}</option> @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-4 flex items-center text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="3"/></svg>
                </div>
            </div>
        </div>

        <div class="lg:col-span-12">
            <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-200 overflow-hidden relative">
                <div wire:loading.flex wire:target="filterProdiId, delete, search, gotoPage, previousPage, nextPage" class="absolute inset-0 bg-white/60 z-10 items-center justify-center backdrop-blur-[2px]">
                    <div class="bg-white p-4 rounded-xl shadow-xl flex flex-col items-center border border-slate-100">
                        <svg class="animate-spin h-8 w-8 text-[#002855] mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest animate-pulse">Memuat...</span>
                    </div>
                </div>

                <div class="overflow-x-auto custom-scrollbar">
                    <table class="min-w-full divide-y divide-slate-100">
                        <thead class="bg-slate-50/80">
                            <tr>
                                <th class="px-8 py-5 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest whitespace-nowrap">Mata Kuliah</th>
                                <th class="px-6 py-5 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest whitespace-nowrap">Kredit SKS</th>
                                <th class="px-6 py-5 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest whitespace-nowrap">Aktivitas</th>
                                <th class="px-6 py-5 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest whitespace-nowrap">Prodi</th>
                                <th class="px-8 py-5 text-right text-[10px] font-black text-slate-400 uppercase tracking-widest whitespace-nowrap">Opsi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 bg-white">
                            @forelse($mks as $mk)
                            <tr class="hover:bg-slate-50/50 transition-colors group">
                                <td class="px-8 py-5">
                                    <div class="flex items-center gap-4">
                                        <div class="w-11 h-11 rounded-[10px] bg-indigo-50 text-[#002855] flex items-center justify-center font-black text-sm shadow-sm uppercase shrink-0 border border-indigo-100">
                                            {{ substr($mk->nama_mk, 0, 1) }}
                                        </div>
                                        <div class="min-w-0">
                                            <h4 class="text-sm font-black text-slate-700 uppercase truncate group-hover:text-[#002855] transition-colors">{{ $mk->nama_mk }}</h4>
                                            <p class="text-[10px] font-bold text-slate-400 tracking-widest mt-0.5 uppercase">{{ $mk->kode_mk }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-5 text-center">
                                    <span class="inline-flex flex-col items-center justify-center">
                                        <span class="text-lg font-black text-[#002855] tracking-tighter">{{ $mk->sks_default }}</span>
                                        <span class="text-[8px] font-black text-slate-400 uppercase mt-0.5 bg-slate-100 px-1.5 py-0.5 rounded">
                                            T:{{ $mk->sks_tatap_muka }} P:{{ $mk->sks_praktek }} L:{{ $mk->sks_lapangan }}
                                        </span>
                                    </span>
                                </td>
                                <td class="px-6 py-5 text-center align-middle">
                                    @php
                                        $style = match($mk->activity_type) {
                                            'THESIS' => 'bg-amber-50 text-amber-600 border-amber-200',
                                            'MBKM' => 'bg-sky-50 text-sky-600 border-sky-200',
                                            'CONTINUATION' => 'bg-purple-50 text-purple-600 border-purple-200',
                                            default => 'bg-slate-50 text-slate-600 border-slate-200'
                                        };
                                        $label = $mk->activity_type ?? 'REGULAR';
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest border {{ $style }}">
                                        {{ $label }}
                                    </span>
                                </td>
                                <td class="px-6 py-5 align-middle">
                                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-tight">{{ $mk->prodi->nama_prodi ?? '-' }}</span>
                                </td>
                                <td class="px-8 py-5 text-right whitespace-nowrap align-middle">
                                    <div class="flex items-center justify-end gap-2 opacity-100 md:opacity-0 group-hover:opacity-100 transition-opacity">
                                        <button wire:click="edit('{{ $mk->id }}')" class="p-2.5 text-indigo-600 bg-indigo-50 hover:bg-indigo-100 hover:text-indigo-800 rounded-xl transition-colors" title="Edit">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                        </button>
                                        <button wire:click="delete('{{ $mk->id }}')" wire:confirm="Yakin ingin menghapus mata kuliah ini? Aksi ini tidak dapat dibatalkan." class="p-2.5 text-rose-500 bg-rose-50 hover:bg-rose-100 hover:text-rose-700 rounded-xl transition-colors" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="py-24 text-center">
                                    <div class="flex flex-col items-center justify-center opacity-40">
                                        <svg class="w-16 h-16 text-slate-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                                        <p class="text-xs font-black text-slate-500 uppercase tracking-[0.2em]">Katalog Mata Kuliah Kosong</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-8 py-5 bg-slate-50/80 border-t border-slate-100">
                    {{ $mks->links() }}
                </div>
            </div>
        </div>
        @endif
    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
</div>