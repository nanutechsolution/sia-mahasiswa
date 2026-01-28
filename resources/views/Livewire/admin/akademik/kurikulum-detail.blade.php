<div>
    {{-- SEO & Header --}}
    <x-slot name="title">Manajemen Kurikulum</x-slot>
    <x-slot name="header">{{ $selectedKurikulum ? 'Struktur Kurikulum' : 'Sistem Manajemen Kurikulum' }}</x-slot>

    <div class="space-y-8">

        @if(!$selectedKurikulum)
        {{-- VIEW: DAFTAR KURIKULUM --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 animate-in fade-in duration-500">
            <div>
                <p class="text-slate-500 text-sm">Konfigurasi struktur kurikulum akademik, pemetaan mata kuliah, dan standarisasi angkatan per program studi.</p>
            </div>
            <div class="flex items-center space-x-2 text-[10px] font-black uppercase tracking-widest text-slate-400">
                <span class="w-2 h-2 bg-unmaris-yellow rounded-full animate-pulse"></span>
                <span>Curriculum Engine Active</span>
            </div>
        </div>

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
                    <div class="md:col-span-5">
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-2">Nama Identitas Kurikulum *</label>
                        <input type="text" wire:model="nama_kurikulum" placeholder="Contoh: Kurikulum 2024 Merdeka Belajar"
                            class="block w-full rounded-2xl border-slate-200 bg-slate-50 py-4 px-5 focus:bg-white focus:border-unmaris-blue focus:ring-4 focus:ring-unmaris-blue/5 text-sm font-semibold transition-all outline-none">
                        @error('nama_kurikulum') <span class="text-rose-500 text-[11px] font-bold mt-2 block">{{ $message }}</span> @enderror
                    </div>
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

        @else
        {{-- VIEW: DETAIL STRUKTUR MATA KULIAH --}}
        <div class="space-y-8 animate-in slide-in-from-right-4 duration-500">
            <!-- Header Back & Info -->
            <div class="bg-white rounded-3xl p-6 lg:p-8 shadow-sm border border-slate-200 flex flex-col md:flex-row items-center justify-between gap-6">
                <button wire:click="backToList" class="group flex items-center px-5 py-2.5 bg-slate-50 text-slate-500 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-unmaris-blue hover:text-white transition-all shadow-sm">
                    <svg class="h-4 w-4 mr-2 transition-transform group-hover:-translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali
                </button>
                <div class="text-center md:text-right">
                    <h2 class="text-xl font-black text-unmaris-blue uppercase tracking-tight">{{ $selectedKurikulum->nama_kurikulum }}</h2>
                    <p class="text-xs font-bold text-unmaris-gold uppercase tracking-[0.2em] mt-1">{{ $selectedKurikulum->prodi->nama_prodi }}</p>
                </div>
            </div>

            <!-- Form Add MK (Quick Access Tool) -->
            <div class="bg-unmaris-blue rounded-3xl p-8 shadow-2xl border border-white/5 flex flex-col lg:flex-row gap-6 items-end">
                <div class="flex-1 w-full space-y-2">
                    <label class="block text-[10px] font-black text-white/50 uppercase tracking-[0.2em] ml-1">Pilih Mata Kuliah Master</label>
                    <select wire:model="mk_id_to_add" class="block w-full rounded-2xl border-white/10 bg-white/5 py-4 px-5 text-white text-sm font-semibold transition-all outline-none focus:bg-white focus:text-slate-800">
                        <option value="">-- Pilih Mata Kuliah --</option>
                        @foreach($availableMks as $mk)
                        <option value="{{ $mk->id }}">{{ $mk->kode_mk }} - {{ $mk->nama_mk }} ({{ $mk->sks_default }} SKS)</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-full lg:w-32 space-y-2">
                    <label class="block text-[10px] font-black text-white/50 uppercase tracking-[0.2em] ml-1">Semester</label>
                    <input type="number" wire:model="semester_paket_to_add" min="1" max="8" class="block w-full rounded-2xl border-white/10 bg-white/5 py-4 px-5 text-white text-sm font-semibold transition-all outline-none focus:bg-white focus:text-slate-800 text-center">
                </div>
                <div class="w-full lg:w-40 space-y-2">
                    <label class="block text-[10px] font-black text-white/50 uppercase tracking-[0.2em] ml-1">Sifat MK</label>
                    <select wire:model="sifat_mk_to_add" class="block w-full rounded-2xl border-white/10 bg-white/5 py-4 px-5 text-white text-sm font-semibold transition-all outline-none focus:bg-white focus:text-slate-800">
                        <option value="W">Wajib</option>
                        <option value="P">Pilihan</option>
                    </select>
                </div>
                <button wire:click="addMk" class="w-full lg:w-auto px-10 py-4 bg-unmaris-yellow text-unmaris-blue rounded-2xl text-xs font-black uppercase tracking-[0.2em] shadow-xl shadow-yellow-500/10 hover:scale-105 active:scale-95 transition-all">
                    Petakan MK
                </button>
            </div>

            <!-- Tabel Struktur Struktur -->
            <div class="bg-white rounded-[2rem] shadow-sm border border-slate-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/50 border-b border-slate-100 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                                <th class="px-8 py-5 w-20 text-center">Smt</th>
                                <th class="px-8 py-5 w-32">Kode</th>
                                <th class="px-8 py-5">Mata Kuliah</th>
                                <th class="px-8 py-5 text-center w-24">Total SKS</th>
                                <th class="px-8 py-5 text-center w-32">Sifat</th>
                                <th class="px-8 py-5 text-right">Opsi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($selectedKurikulum->mataKuliahs->sortBy('pivot.semester_paket') as $mk)
                            <tr class="hover:bg-slate-50/50 transition-colors group">
                                <td class="px-8 py-5 text-center">
                                    <span class="inline-flex items-center justify-center w-8 h-8 bg-slate-100 text-slate-600 rounded-lg font-black text-xs tabular-nums">
                                        {{ $mk->pivot->semester_paket }}
                                    </span>
                                </td>
                                <td class="px-8 py-5">
                                    <span class="text-[11px] font-mono font-bold text-indigo-500 bg-indigo-50 px-2 py-0.5 rounded uppercase tracking-tighter italic">
                                        {{ $mk->kode_mk }}
                                    </span>
                                </td>
                                <td class="px-8 py-5">
                                    <div class="text-sm font-bold text-slate-800 leading-tight uppercase group-hover:text-unmaris-blue transition-colors">
                                        {{ $mk->nama_mk }}
                                    </div>
                                </td>
                                <td class="px-8 py-5 text-center font-black text-slate-500 tabular-nums">
                                    {{ $mk->pivot->sks_tatap_muka + $mk->pivot->sks_praktek + $mk->pivot->sks_lapangan }}
                                </td>
                                <td class="px-8 py-5 text-center">
                                    <span class="px-3 py-1 rounded-xl text-[9px] font-black uppercase tracking-widest border
                                        {{ $mk->pivot->sifat_mk == 'W' ? 'bg-blue-50 text-blue-600 border-blue-100' : 'bg-amber-50 text-amber-600 border-amber-100' }}">
                                        {{ $mk->pivot->sifat_mk == 'W' ? 'Wajib' : 'Pilihan' }}
                                    </span>
                                </td>
                                <td class="px-8 py-5 text-right">
                                    <button wire:click="removeMk({{ $mk->id }})" wire:confirm="Hapus MK ini dari struktur kurikulum?"
                                        class="p-2.5 text-slate-300 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition-all opacity-0 group-hover:opacity-100">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>


</div>