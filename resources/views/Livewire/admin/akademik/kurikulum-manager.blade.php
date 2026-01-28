<div>
    {{-- SEO & Header --}}
    <x-slot name="title">Manajemen Kurikulum</x-slot>
    <x-slot name="header">Sistem Manajemen Kurikulum</x-slot>

    <div class="space-y-8">
        {{-- Top Toolbar --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <p class="text-slate-500 text-sm">Konfigurasi struktur kurikulum akademik, pemetaan mata kuliah, dan standarisasi angkatan per program studi.</p>
            </div>

            <div class="flex items-center space-x-2 text-[10px] font-black uppercase tracking-widest text-slate-400">
                <span class="w-2 h-2 bg-unmaris-yellow rounded-full animate-pulse"></span>
                <span>Curriculum Engine Active</span>
            </div>
        </div>

        {{-- Feedback Notification --}}
        @if (session()->has('success'))
        <div class="bg-emerald-50 border border-emerald-100 p-4 rounded-2xl text-emerald-800 text-sm flex items-center animate-in fade-in duration-300 shadow-sm">
            <svg class="w-5 h-5 mr-3 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            <span class="font-bold">{{ session('success') }}</span>
        </div>
        @endif

        {{-- Form: Setup Kurikulum Baru --}}
        <div class="bg-white rounded-[2.5rem] shadow-xl border border-slate-200 overflow-hidden animate-in slide-in-from-top-4 duration-500">
            <div class="px-8 py-6 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-black text-unmaris-blue uppercase tracking-widest">Setup Kurikulum Utama</h3>
                    <p class="text-[10px] text-slate-400 font-bold uppercase mt-1">Definisi Struktur Akademik Baru</p>
                </div>
                <span class="px-3 py-1 bg-unmaris-yellow/20 text-unmaris-gold text-[10px] font-black rounded-lg uppercase tracking-widest border border-unmaris-yellow/30">Master Configuration</span>
            </div>

            <div class="p-8 lg:p-10 space-y-8">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-8">
                    {{-- Program Studi --}}
                    <div class="md:col-span-4">
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-2">Program Studi Pengampu *</label>
                        <select wire:model="prodi_id" class="block w-full rounded-2xl border-slate-200 bg-slate-50 py-4 px-5 focus:bg-white focus:border-unmaris-blue focus:ring-4 focus:ring-unmaris-blue/5 text-sm font-semibold transition-all outline-none">
                            <option value="">Pilih Program Studi</option>
                            @foreach($prodis as $p)
                            <option value="{{ $p->id }}">{{ $p->nama_prodi }}</option>
                            @endforeach
                        </select>
                        @error('prodi_id') <span class="text-rose-500 text-[11px] font-bold mt-2 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- Nama Kurikulum --}}
                    <div class="md:col-span-5">
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-2">Nama Identitas Kurikulum *</label>
                        <input type="text" wire:model="nama_kurikulum" placeholder="Contoh: Kurikulum 2024 Merdeka Belajar"
                            class="block w-full rounded-2xl border-slate-200 bg-slate-50 py-4 px-5 focus:bg-white focus:border-unmaris-blue focus:ring-4 focus:ring-unmaris-blue/5 text-sm font-semibold transition-all outline-none">
                        @error('nama_kurikulum') <span class="text-rose-500 text-[11px] font-bold mt-2 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- Tahun Mulai --}}
                    <div class="md:col-span-3">
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-2">Tahun Mulai Berlaku *</label>
                        <input type="number" wire:model="tahun_mulai" placeholder="2024"
                            class="block w-full rounded-2xl border-slate-200 bg-slate-50 py-4 px-5 focus:bg-white focus:border-unmaris-blue focus:ring-4 focus:ring-unmaris-blue/5 text-sm font-semibold transition-all outline-none tabular-nums">
                        @error('tahun_mulai') <span class="text-rose-500 text-[11px] font-bold mt-2 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="pt-6 border-t border-slate-100 flex justify-end">
                    <button wire:click="saveHeader"
                        class="group relative px-12 py-4 bg-unmaris-blue text-white rounded-2xl text-xs font-black uppercase tracking-[0.2em] shadow-2xl shadow-indigo-200 hover:scale-105 transition-all overflow-hidden">
                        <span class="relative z-10">Simpan Kurikulum Baru</span>
                        <div class="absolute inset-0 bg-gradient-to-r from-unmaris-blue to-indigo-600 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    </button>
                </div>
            </div>
        </div>

        {{-- List: Grid Kurikulum --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($kurikulums as $k)
            <div class="bg-white rounded-[2rem] shadow-sm border border-slate-200 overflow-hidden group hover:shadow-xl hover:border-unmaris-blue/30 transition-all duration-500 flex flex-col">
                <div class="p-8 flex-1">
                    <div class="flex items-center justify-between mb-6">
                        <span class="px-3 py-1 bg-unmaris-blue/5 text-unmaris-blue text-[9px] font-black uppercase tracking-widest rounded-lg border border-unmaris-blue/10">
                            {{ $k->prodi->nama_prodi }}
                        </span>

                        <button wire:click="toggleActive({{ $k->id }})"
                            class="inline-flex items-center px-2.5 py-1 rounded-full text-[9px] font-black uppercase tracking-tighter transition-all
                            {{ $k->is_active ? 'bg-emerald-100 text-emerald-600 border border-emerald-200' : 'bg-slate-100 text-slate-400 border border-slate-200' }}">
                            <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $k->is_active ? 'bg-emerald-500 animate-pulse' : 'bg-slate-300' }}"></span>
                            {{ $k->is_active ? 'Aktif' : 'Non-Aktif' }}
                        </button>
                    </div>

                    <h3 class="text-lg font-black text-slate-800 leading-tight mb-2 group-hover:text-unmaris-blue transition-colors">
                        {{ $k->nama_kurikulum }}
                    </h3>
                    <div class="flex items-center text-xs font-bold text-slate-400 uppercase tracking-widest">
                        <svg class="w-4 h-4 mr-1.5 text-unmaris-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z" />
                        </svg>
                        Mulai Angkatan {{ $k->tahun_mulai }}
                    </div>
                </div>

                <div class="px-6 py-5 bg-slate-50/80 border-t border-slate-100">
                    <button wire:click="manage({{ $k->id }})"
                        class="flex items-center justify-center w-full py-3.5 bg-white border border-slate-200 text-slate-600 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-unmaris-blue hover:text-white hover:border-unmaris-blue transition-all shadow-sm">
                        Struktur & Mata Kuliah
                        <svg class="w-3.5 h-3.5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </button>
                </div>
            </div>
            @endforeach
        </div>
    </div>


</div>