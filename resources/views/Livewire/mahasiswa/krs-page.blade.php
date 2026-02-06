<div class="space-y-6 md:space-y-8 animate-in fade-in duration-700 pb-24 lg:pb-10">
    
    {{-- 1. STICKY HEADER SUMMARY --}}
    <div class=" top-0 z-40 bg-white/95 backdrop-blur-md border-b border-slate-200 -mx-4 px-4 py-4 md:static md:mx-0 md:px-10 md:py-8 md:rounded-3xl md:shadow-sm md:border md:bg-white">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center gap-4">
            
            {{-- Profile & Status --}}
            <div class="flex items-center gap-4 w-full md:w-auto">
                <div class="w-14 h-14 md:w-16 md:h-16 bg-[#002855] text-[#fcc000] rounded-2xl flex items-center justify-center text-xl md:text-2xl font-black shadow-lg uppercase shrink-0">
                    {{ substr($mahasiswa->person->nama_lengkap ?? $mahasiswa->nama_lengkap, 0, 1) }}
                </div>
                <div class="min-w-0 text-center md:text-left">
                    <h2 class="text-base md:text-xl font-black text-[#002855] uppercase truncate tracking-tight">
                        {{ $mahasiswa->person->nama_lengkap ?? $mahasiswa->nama_lengkap }}
                    </h2>
                    <div class="flex items-center justify-center md:justify-start gap-2 mt-0.5">
                        <span class="text-[10px] md:text-xs font-mono font-bold text-slate-400">{{ $mahasiswa->nim }}</span>
                        <span class="w-1 h-1 bg-slate-300 rounded-full"></span>
                        <span class="text-[10px] md:text-xs font-black text-indigo-600 uppercase tracking-widest">Semester {{ $semesterBerjalan }}</span>
                    </div>
                </div>
            </div>

            {{-- Credit & Print Action --}}
            <div class="w-full md:w-auto flex items-center justify-between md:justify-end gap-6">
                <div class="text-right">
                    <span class="block text-[8px] font-black text-slate-400 uppercase tracking-[0.2em] mb-0.5">Total Kredit</span>
                    <div class="flex items-baseline gap-1">
                        <span class="text-2xl md:text-3xl font-black text-[#002855] tabular-nums">{{ $totalSks }}</span>
                        @if(!$isPaket)
                            <span class="text-sm font-bold text-slate-300">/ {{ $maxSks }}</span>
                        @endif
                        <span class="text-[9px] font-black text-slate-400 uppercase ml-1">SKS</span>
                    </div>
                </div>

                @if($statusKrs === 'DISETUJUI')
                    <a href="{{ route('mhs.cetak.krs') }}" target="_blank" class="hidden md:flex items-center gap-2 px-5 py-2.5 bg-emerald-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-black transition-all shadow-lg shadow-emerald-600/20">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        Cetak KRS
                    </a>
                @endif
            </div>
        </div>
    </div>

    {{-- 2. ALERT MESSAGES (Inline) --}}
    @if($blockKrs)
    <div class="bg-rose-50 border border-rose-100 p-5 rounded-2xl flex items-center gap-4 animate-in shake">
        <div class="w-10 h-10 bg-rose-200 text-rose-700 rounded-xl flex items-center justify-center shrink-0 text-xl">⚠️</div>
        <div class="flex-1">
            <h4 class="text-xs font-black text-rose-900 uppercase tracking-widest">Akses Terkunci</h4>
            <p class="text-[11px] font-bold text-rose-700/80 mt-0.5 leading-tight">{{ $pesanBlock }}</p>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        {{-- 3. RENCANA STUDI (MAIN AREA) --}}
        <div class="lg:col-span-8 space-y-6">
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-8 py-5 bg-slate-50 border-b border-slate-100 flex justify-between items-center">
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-4 bg-[#002855] rounded-full"></div>
                        <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">KRS Semester Berjalan</h3>
                    </div>
                    <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest shadow-sm {{ $statusKrs == 'DISETUJUI' ? 'bg-emerald-100 text-emerald-700' : 'bg-[#002855] text-white' }}">
                        {{ $statusKrs }}
                    </span>
                </div>

                <div class="divide-y divide-slate-50">
                    @forelse($krsDiambil as $row)
                    <div class="p-6 md:p-8 flex justify-between items-start gap-6 hover:bg-slate-50/50 transition-colors group">
                        <div class="space-y-4 flex-1 min-w-0">
                            <div class="flex flex-wrap items-center gap-3">
                                <span class="bg-indigo-50 text-indigo-600 px-2.5 py-1 rounded-lg text-[10px] font-black border border-indigo-100 uppercase tracking-widest">{{ $row->kode_mk_snapshot }}</span>
                                <h4 class="text-base font-black text-slate-800 uppercase leading-tight truncate group-hover:text-indigo-700 transition-colors">{{ $row->nama_mk_snapshot }}</h4>
                            </div>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-3 gap-x-6 text-[11px] font-bold text-slate-400 uppercase tracking-tighter">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-indigo-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2.5"/></svg> 
                                    <span>{{ $row->jadwalKuliah->hari }}, {{ substr($row->jadwalKuliah->jam_mulai, 0, 5) }} WITA</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-indigo-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2" stroke-width="2.5"/></svg> 
                                    <span>R.{{ $row->jadwalKuliah->ruang }} (Kls {{ $row->jadwalKuliah->nama_kelas }})</span>
                                </div>
                                <div class="flex items-center gap-2 sm:col-span-2">
                                    <svg class="w-4 h-4 text-indigo-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" stroke-width="2.5" /></svg>
                                    <span class="truncate">{{ $row->jadwalKuliah->dosen->person->nama_lengkap ?? 'Dosen Pengampu' }}</span>
                                </div>
                            </div>
                            
                            <div class="flex items-center gap-2">
                                <span class="text-[10px] font-black text-[#002855] bg-[#fcc000] px-2 py-0.5 rounded shadow-sm">{{ $row->sks_snapshot }} SKS</span>
                                @if($row->ekuivalensi_id)
                                    <span class="text-[9px] font-black text-indigo-500 bg-indigo-50 px-2 py-0.5 rounded border border-indigo-100 uppercase italic">Penyetaraan</span>
                                @endif
                            </div>
                        </div>
                        
                        @if($statusKrs == 'DRAFT' && !$isPaket)
                            <button wire:click="confirmHapus('{{ $row->id }}')" class="p-3 text-slate-300 hover:text-rose-500 hover:bg-rose-50 rounded-2xl transition-all shadow-inner shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        @endif
                    </div>
                    @empty
                    <div class="py-24 text-center space-y-4">
                        <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto text-3xl opacity-40">📂</div>
                        <p class="text-xs font-bold text-slate-300 uppercase tracking-[0.2em]">Belum ada mata kuliah yang terpilih</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- 4. PENAWARAN (SIDEBAR AREA) --}}
        <div class="lg:col-span-4 space-y-6">
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden {{ $statusKrs != 'DRAFT' || $blockKrs ? 'opacity-40 grayscale pointer-events-none' : '' }}">
                <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Penawaran Semester Ini</h3>
                </div>
                
                <div class="max-h-[70vh] overflow-y-auto custom-scrollbar divide-y divide-slate-100">
                    @forelse($jadwalTersedia as $j)
                        @php
                            // Ambil info semester paket dari kurikulum aktif
                            $semData = \Illuminate\Support\Facades\DB::table('kurikulum_mata_kuliah')
                                ->join('master_kurikulums', 'kurikulum_mata_kuliah.kurikulum_id', '=', 'master_kurikulums.id')
                                ->where('kurikulum_mata_kuliah.mata_kuliah_id', $j->mata_kuliah_id)
                                ->where('master_kurikulums.prodi_id', $this->mahasiswa->prodi_id)
                                ->where('master_kurikulums.is_active', true)
                                ->select('kurikulum_mata_kuliah.semester_paket')
                                ->first();
                        @endphp
                        
                        <div class="p-6 hover:bg-indigo-50/20 transition-all group relative">
                            {{-- Semester Badge --}}
                            <div class="absolute top-6 right-6">
                                <span class="bg-indigo-600 text-white text-[8px] font-black px-1.5 py-0.5 rounded shadow-sm">SMT {{ $semData->semester_paket ?? '?' }}</span>
                            </div>

                            <div class="space-y-3 pr-8">
                                <h4 class="text-xs font-black text-slate-700 leading-snug uppercase group-hover:text-indigo-600 transition-colors">{{ $j->mataKuliah->nama_mk }}</h4>
                                
                                <div class="space-y-1.5">
                                    <div class="flex items-center gap-2 text-[10px] font-bold text-slate-400">
                                        <svg class="w-3.5 h-3.5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2.5"/></svg>
                                        {{ $j->hari }}, {{ substr($j->jam_mulai, 0, 5) }} - {{ substr($j->jam_selesai, 0, 5) }}
                                    </div>
                                    <div class="flex items-center gap-2 text-[10px] font-bold text-slate-400">
                                        <svg class="w-3.5 h-3.5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2" stroke-width="2.5"/></svg>
                                        R.{{ $j->ruang }} &bull; Kelas {{ $j->nama_kelas }}
                                    </div>
                                    <div class="flex items-center gap-2 text-[10px] font-bold text-indigo-500/70">
                                        <svg class="w-3.5 h-3.5 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" stroke-width="2.5" /></svg>
                                        <span class="truncate max-w-[180px]">{{ $j->dosen->person->nama_lengkap ?? 'Dosen Pengampu' }}</span>
                                    </div>
                                </div>

                                <div class="pt-2 flex items-center justify-between">
                                    <span class="text-[9px] font-black text-slate-400 tracking-widest uppercase">{{ $j->mataKuliah->sks_default }} SKS</span>
                                    <button wire:click="ambilMatkul('{{ $j->id }}')" 
                                        class="px-4 py-2 bg-[#002855] text-white rounded-xl text-[9px] font-black uppercase tracking-widest shadow-lg shadow-indigo-900/10 hover:bg-black hover:scale-105 active:scale-95 transition-all">
                                        Pilih MK
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                    <div class="p-16 text-center">
                        <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest leading-relaxed">Penawaran tidak ditemukan</p>
                    </div>
                    @endforelse
                </div>
            </div>

            {{-- Security & Info --}}
            <div class="bg-indigo-50 p-6 rounded-3xl border border-indigo-100 space-y-3">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    <span class="text-[9px] font-black text-indigo-900 uppercase tracking-widest">Proteksi Akademik</span>
                </div>
                <p class="text-[10px] font-bold text-indigo-700/80 leading-relaxed italic">
                    Data KRS dilindungi oleh mekanisme <strong>Snapshot Transaction</strong>. Perubahan jadwal atau kurikulum di masa depan tidak akan mengubah riwayat pengambilan mata kuliah yang sudah sah saat ini.
                </p>
            </div>
        </div>
    </div>

    {{-- 5. FLOATING ACTION BAR (MOBILE ONLY) --}}
    <div class="lg:hidden fixed bottom-6 left-4 right-4 z-50">
        <div class="bg-[#002855]/95 backdrop-blur-xl text-white p-4 rounded-[2rem] shadow-2xl flex items-center justify-between border border-white/10 ring-1 ring-white/20">
            <div class="pl-3">
                <p class="text-[8px] font-black uppercase text-indigo-300 tracking-widest leading-none mb-1">Status: {{ $statusKrs }}</p>
                <p class="text-xl font-black tabular-nums leading-none">{{ $totalSks }} <span class="text-[10px] font-bold opacity-40 ml-0.5 uppercase">Kredit</span></p>
            </div>
            
            @if($statusKrs == 'DRAFT' && $totalSks > 0 && !$blockKrs)
                <button wire:click="ajukanKrs" class="bg-[#fcc000] text-[#002855] px-8 py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-xl active:scale-95 transition-all">
                    AJUKAN KRS
                </button>
            @elseif($statusKrs == 'DISETUJUI')
                <a href="{{ route('mhs.cetak.krs') }}" target="_blank" class="bg-emerald-500 text-white px-8 py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-xl active:scale-95 transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    CETAK
                </a>
            @else
                <div class="px-6 py-3 bg-white/10 rounded-2xl text-[9px] font-black uppercase tracking-widest border border-white/10 shadow-inner">
                    {{ $statusKrs }}
                </div>
            @endif
        </div>
    </div>

    {{-- 6. DESKTOP ACTION BAR (FOOTER CARD) --}}
    @if($statusKrs == 'DRAFT' && $totalSks > 0 && !$blockKrs)
    <div class="hidden lg:block bg-white p-10 rounded-[2.5rem] shadow-xl border border-slate-200 animate-in slide-in-from-bottom-6 duration-700">
        <div class="flex justify-between items-center gap-10">
            <div class="max-w-xl">
                <h4 class="text-xl font-black text-[#002855] uppercase tracking-tight">Kirim Rencana Studi Ke Dosen Wali</h4>
                <p class="text-sm font-bold text-slate-400 mt-2 leading-relaxed">
                    Pastikan jadwal sudah sesuai dan tidak terjadi bentrok. Setelah diajukan, Anda tidak dapat mengubah pilihan mata kuliah hingga Dosen Wali (PA) memberikan persetujuan atau revisi.
                </p>
            </div>
            <button wire:click="ajukanKrs" class="px-12 py-5 bg-[#002855] text-white rounded-3xl font-black text-xs uppercase tracking-[0.3em] shadow-2xl shadow-indigo-900/40 hover:bg-black hover:scale-105 active:scale-95 transition-all">
                Ajukan Sekarang
            </button>
        </div>
    </div>
    @endif

    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(0, 40, 85, 0.08); border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(0, 40, 85, 0.15); }
    </style>
</div>