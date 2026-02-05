<div class="min-h-screen bg-[#f8fafc] pb-32 lg:pb-12 animate-in fade-in duration-700">
    
    {{-- 1. PREMIUM STICKY HEADER (Mobile Optimized) --}}
    <div class="sticky top-0 z-40 bg-white/80 backdrop-blur-md border-b border-slate-200 px-4 sm:px-6 lg:px-8 py-4 lg:py-6">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center gap-4">
            
            {{-- User Context --}}
            <div class="flex items-center gap-4 w-full md:w-auto">
                <div class="relative shrink-0">
                    <div class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-tr from-[#002855] to-indigo-800 text-[#fcc000] rounded-2xl flex items-center justify-center font-black text-xl shadow-lg uppercase ring-2 ring-white">
                        {{ substr($mahasiswa->person->nama_lengkap ?? $mahasiswa->nama_lengkap, 0, 1) }}
                    </div>
                </div>
                <div class="min-w-0">
                    <h2 class="text-sm sm:text-lg font-black text-[#002855] truncate uppercase tracking-tight">
                        {{ $mahasiswa->person->nama_lengkap ?? $mahasiswa->nama_lengkap }}
                    </h2>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest flex items-center gap-2">
                        <span>{{ $mahasiswa->nim }}</span>
                        <span class="w-1 h-1 bg-slate-300 rounded-full"></span>
                        <span class="text-indigo-600">Semester {{ $semesterBerjalan }}</span>
                    </p>
                </div>
            </div>

            {{-- Credit Progress Tracker & Print Action --}}
            <div class="w-full md:w-auto flex items-center justify-between md:justify-end gap-4 sm:gap-6 bg-slate-50 md:bg-transparent p-3 md:p-0 rounded-2xl border border-slate-100 md:border-none">
                <div class="flex items-center gap-4 sm:gap-6">
                    <div class="text-left md:text-right">
                        <span class="block text-[8px] font-black text-slate-400 uppercase tracking-[0.2em] mb-0.5">Sistem Pengisian</span>
                        <span class="px-2 py-0.5 rounded-md text-[9px] font-black {{ $isPaket ? 'bg-indigo-600 text-white' : 'bg-emerald-100 text-emerald-700 border border-emerald-200' }} uppercase">
                            {{ $isPaket ? 'PAKET' : 'MANDIRI' }}
                        </span>
                    </div>
                    <div class="flex items-baseline gap-1.5 border-r border-slate-200 pr-4 sm:pr-6">
                        <span class="text-3xl font-black text-[#002855] tabular-nums">{{ $totalSks }}</span>
                        @if(!$isPaket)
                            <span class="text-sm font-bold text-slate-300">/ {{ $maxSks }}</span>
                        @endif
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1">Kredit SKS</span>
                    </div>
                </div>

                {{-- SHORTCUT CETAK (Hanya Muncul Jika Sudah Approve) --}}
                @if($statusKrs == 'DISETUJUI')
                <a href="{{ route('mhs.cetak.krs') }}" target="_blank" class="hidden sm:flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 text-[#002855] rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-[#002855] hover:text-white transition-all shadow-sm group">
                    <svg class="w-4 h-4 text-indigo-500 group-hover:text-[#fcc000] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                    Cetak
                </a>
                @endif
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6 sm:mt-10">
        
        {{-- 2. NOTIFIKASI VALIDASI --}}
        @if($blockKrs)
        <div class="mb-8 bg-white border-l-4 border-rose-500 p-5 rounded-2xl shadow-sm animate-in shake duration-500">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 bg-rose-50 text-rose-600 rounded-xl flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <div class="min-w-0">
                    <h3 class="text-xs font-black text-rose-900 uppercase">Akses Terkunci</h3>
                    <p class="text-rose-600/80 text-[11px] font-bold mt-0.5 leading-tight">{{ $pesanBlock }}</p>
                </div>
            </div>
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            {{-- 3. DAFTAR MATA KULIAH (MAIN AREA) --}}
            <div class="lg:col-span-8 space-y-4">
                <div class="flex items-center justify-between px-2 mb-4">
                    <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em]">Penawaran Akademik Aktif</h3>
                </div>

                <div class="grid grid-cols-1 gap-4 {{ $statusKrs != 'DRAFT' || $blockKrs ? 'opacity-40 grayscale pointer-events-none' : '' }}">
                    @forelse($jadwalTersedia as $j)
                        @php
                            $semData = DB::table('kurikulum_mata_kuliah')
                                ->join('master_kurikulums', 'kurikulum_mata_kuliah.kurikulum_id', '=', 'master_kurikulums.id')
                                ->where('kurikulum_mata_kuliah.mata_kuliah_id', $j->mata_kuliah_id)
                                ->where('master_kurikulums.prodi_id', $this->mahasiswa->prodi_id)
                                ->where('master_kurikulums.is_active', true)
                                ->select('kurikulum_mata_kuliah.semester_paket')
                                ->first();
                            
                            $isEquivalent = DB::table('akademik_ekuivalensi')
                                ->where('prodi_id', $this->mahasiswa->prodi_id)
                                ->where('mk_tujuan_id', $j->mata_kuliah_id)
                                ->where('is_active', true)->exists();
                        @endphp
                        
                        <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 flex flex-col sm:flex-row sm:items-center justify-between gap-6 hover:border-indigo-200 hover:shadow-xl hover:shadow-indigo-900/5 transition-all duration-300">
                            <div class="flex-1 min-w-0 space-y-4">
                                <div class="flex flex-wrap items-center gap-3">
                                    <div class="px-2.5 py-1 bg-[#002855] text-[#fcc000] text-[9px] font-black uppercase tracking-widest rounded-lg">
                                        Semester {{ $semData->semester_paket ?? '?' }}
                                    </div>
                                    <h4 class="text-sm sm:text-base font-black text-slate-800 leading-tight uppercase group-hover:text-indigo-700 transition-colors truncate">
                                        {{ $j->mataKuliah->nama_mk }}
                                    </h4>
                                    @if($isEquivalent)
                                        <span class="px-2 py-0.5 bg-amber-500 text-white text-[8px] font-black uppercase rounded shadow-sm">Penyetaraan</span>
                                    @endif
                                </div>

                                <div class="flex flex-wrap items-center gap-x-6 gap-y-2">
                                    <div class="flex items-center text-[10px] font-bold text-slate-400 uppercase tracking-tighter">
                                        <svg class="w-3.5 h-3.5 mr-2 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        {{ $j->hari }}, {{ substr($j->jam_mulai, 0, 5) }}
                                    </div>
                                    <div class="flex items-center text-[10px] font-bold text-slate-400 uppercase tracking-tighter">
                                        <svg class="w-3.5 h-3.5 mr-2 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                        R.{{ $j->ruang }} ({{ $j->nama_kelas }})
                                    </div>
                                    <div class="flex items-center text-[10px] font-black text-[#002855] uppercase tracking-widest">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#fcc000] mr-2"></span>
                                        {{ $j->mataKuliah->sks_default }} Kredit
                                    </div>
                                </div>

                                <div class="flex items-center text-[10px] font-bold text-slate-400 uppercase truncate">
                                    <span class="w-5 h-5 rounded-full bg-slate-100 flex items-center justify-center mr-2 text-[8px] text-[#002855]">👤</span>
                                    {{ $j->dosen->person->nama_lengkap ?? 'Dosen Belum Ditentukan' }}
                                </div>
                            </div>

                            <button wire:click="ambilMatkul('{{ $j->id }}')" 
                                class="w-full sm:w-auto px-10 py-3.5 bg-[#002855] text-white rounded-2xl font-black text-[10px] uppercase tracking-[0.3em] shadow-xl shadow-indigo-900/10 hover:bg-black active:scale-95 transition-all">
                                Ambil
                            </button>
                        </div>
                    @empty
                        <div class="py-24 text-center bg-white rounded-[3rem] border border-slate-200 border-dashed">
                            <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl grayscale opacity-30">📅</div>
                            <h4 class="text-slate-500 font-black uppercase text-[10px] tracking-[0.2em]">Penawaran Belum Tersedia</h4>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- 4. DRAFT SIDEBAR (RESULT AREA) --}}
            <div class="lg:col-span-4 space-y-6">
                <div class="bg-[#002855] rounded-[2.5rem] shadow-2xl shadow-indigo-900/20 overflow-hidden lg:sticky lg:top-28">
                    {{-- Header Draft --}}
                    <div class="px-8 py-6 border-b border-white/10 bg-white/5 flex justify-between items-center">
                        <div>
                            <h3 class="text-[10px] font-black text-[#fcc000] uppercase tracking-[0.3em]">Rencana Studi</h3>
                            <p class="text-[9px] text-white/40 font-bold uppercase mt-1">Draf Dipilih</p>
                        </div>
                        <div class="h-9 w-9 rounded-xl bg-white/10 text-white flex items-center justify-center font-black text-xs border border-white/10">
                            {{ count($krsDiambil) }}
                        </div>
                    </div>

                    {{-- List Items --}}
                    <div class="max-h-[45vh] overflow-y-auto custom-scrollbar divide-y divide-white/5 bg-black/10">
                        @forelse($krsDiambil as $detail)
                            @php
                                // Ambil info semester paket dari kurikulum mahasiswa untuk draf
                                $semDraf = DB::table('kurikulum_mata_kuliah')
                                    ->join('master_kurikulums', 'kurikulum_mata_kuliah.kurikulum_id', '=', 'master_kurikulums.id')
                                    ->where('kurikulum_mata_kuliah.mata_kuliah_id', $detail->jadwalKuliah->mata_kuliah_id)
                                    ->where('master_kurikulums.prodi_id', $this->mahasiswa->prodi_id)
                                    ->where('master_kurikulums.is_active', true)
                                    ->value('semester_paket');
                            @endphp
                            <div class="px-8 py-5 flex justify-between items-start group hover:bg-white/5 transition-all">
                                <div class="flex-1 min-w-0 pr-4 space-y-2">
                                    <p class="text-[11px] font-black text-white uppercase tracking-tight leading-snug">{{ $detail->nama_mk_snapshot }}</p>
                                    
                                    <div class="flex flex-wrap items-center gap-1.5">
                                        <span class="text-[8px] font-black text-[#fcc000] border border-[#fcc000]/30 px-1.5 py-0.5 rounded uppercase">{{ $detail->kode_mk_snapshot }}</span>
                                        <span class="text-[8px] font-black bg-white/10 text-white px-1.5 py-0.5 rounded uppercase">Smt {{ $semDraf ?? '?' }}</span>
                                        <span class="text-[9px] font-bold text-white/40 uppercase">{{ $detail->sks_snapshot }} SKS</span>
                                    </div>

                                    <div class="space-y-1">
                                        <div class="flex items-center text-[9px] font-bold text-white/30 uppercase tracking-tighter">
                                            <svg class="w-3 h-3 mr-1.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                            <span class="truncate">{{ $detail->jadwalKuliah->dosen->person->nama_lengkap ?? 'Dosen Belum Ditentukan' }}</span>
                                        </div>
                                        <div class="flex items-center text-[9px] font-bold text-white/30 uppercase tracking-tighter">
                                            <svg class="w-3 h-3 mr-1.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                            {{ $detail->jadwalKuliah->hari }}, {{ substr($detail->jadwalKuliah->jam_mulai, 0, 5) }} WITA
                                        </div>
                                    </div>
                                </div>

                                @if($statusKrs == 'DRAFT' && !$isPaket)
                                    <button wire:click="hapusMatkul('{{ $detail->id }}')" class="w-8 h-8 flex items-center justify-center rounded-xl text-white/20 hover:text-rose-400 hover:bg-rose-400/10 transition-all focus:outline-none shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                @endif
                            </div>
                        @empty
                            <div class="px-10 py-16 text-center">
                                <p class="text-[9px] font-black text-white/20 uppercase tracking-widest leading-relaxed italic">Keranjang KRS Kosong</p>
                            </div>
                        @endforelse
                    </div>

                    {{-- Summary & Submission --}}
                    <div class="p-8 bg-black/20 border-t border-white/10 space-y-6">
                        <div class="flex justify-between items-end">
                            <div class="space-y-1">
                                <span class="text-[9px] font-black text-white/30 uppercase tracking-[0.3em] block">Total SKS</span>
                                <span class="text-3xl font-black text-[#fcc000] tabular-nums leading-none">{{ $totalSks }}</span>
                            </div>
                            <div class="text-right">
                                <span class="px-2.5 py-1 bg-white/10 rounded-lg text-[10px] font-black text-white uppercase border border-white/10 tracking-widest">{{ $statusKrs }}</span>
                            </div>
                        </div>

                        @if($statusKrs == 'DRAFT')
                            <button wire:click="ajukanKrs" wire:confirm="Kirim rencana studi ini ke Dosen Wali?" 
                                @if($totalSks == 0 || $blockKrs) disabled @endif
                                class="w-full py-4 bg-[#fcc000] text-[#002855] rounded-2xl font-black text-[11px] uppercase tracking-[0.4em] shadow-xl hover:scale-[1.02] active:scale-95 transition-all disabled:opacity-30 disabled:grayscale">
                                Ajukan Rencana Studi
                            </button>
                        @elseif($statusKrs == 'DISETUJUI')
                            {{-- TOMBOL CETAK SIDEBAR --}}
                            <a href="{{ route('mhs.cetak.krs') }}" target="_blank" class="w-full py-4 bg-emerald-600 text-white rounded-2xl font-black text-[11px] uppercase tracking-[0.4em] shadow-xl hover:bg-emerald-700 transition-all flex items-center justify-center gap-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                                Cetak Kartu
                            </a>
                        @else
                            <div class="w-full py-4 bg-white/5 border border-white/20 rounded-2xl flex flex-col items-center justify-center gap-2">
                                <span class="text-white text-[11px] font-black uppercase tracking-[0.3em]">KRS Menunggu</span>
                                <p class="text-[8px] text-white/30 font-bold uppercase tracking-widest leading-none italic">Validasi Dosen Wali</p>
                            </div>
                        @endif

                        {{-- [IMPROVED UX] Subtle Security Info --}}
                        <div class="flex items-center gap-2 pt-2 opacity-30 justify-center">
                            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                            <span class="text-[8px] font-bold text-white uppercase tracking-widest">Snapshot Protected System</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- FIXED ACTION BAR (MOBILE ONLY) --}}
    <div class="lg:hidden fixed bottom-4 left-4 right-4 z-50">
        <div class="bg-[#002855] text-white p-4 rounded-[2rem] shadow-2xl flex items-center justify-between border border-white/10 ring-1 ring-white/20">
            <div class="pl-4">
                <p class="text-[8px] font-black uppercase text-[#fcc000] tracking-widest mb-1 leading-none">Total Kredit</p>
                <p class="text-2xl font-black leading-none">{{ $totalSks }}</p>
            </div>
            @if($statusKrs == 'DRAFT' && $totalSks > 0)
                <button wire:click="ajukanKrs" class="bg-[#fcc000] text-[#002855] px-8 py-3.5 rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-xl active:scale-95 transition-all">
                    Kirim KRS
                </button>
            @elseif($statusKrs == 'DISETUJUI')
                {{-- TOMBOL CETAK MOBILE --}}
                <a href="{{ route('mhs.cetak.krs') }}" target="_blank" class="bg-emerald-500 text-white px-8 py-3.5 rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-xl active:scale-95 transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                    Cetak
                </a>
            @else
                <div class="px-6 py-3 bg-white/10 rounded-2xl text-[9px] font-black uppercase tracking-widest border border-white/10">
                    {{ $statusKrs }}
                </div>
            @endif
        </div>
    </div>

    {{-- Global Styles --}}
    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.1); border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(252, 192, 0, 0.2); }
    </style>
</div>