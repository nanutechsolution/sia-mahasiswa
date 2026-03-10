<div class="space-y-8 animate-in fade-in duration-500 pb-12">
    {{-- 1. HEADER SECTION --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div class="space-y-2">
            <h1 class="text-3xl font-black text-[#002855] tracking-tight flex items-center gap-3">
                <div class="w-12 h-12 bg-[#002855] rounded-2xl flex items-center justify-center text-white shadow-xl shadow-blue-900/20">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                </div>
                Jadwal Mengajar
            </h1>
            <div class="flex items-center gap-3 ml-1">
                <span class="text-slate-400 text-[10px] font-black uppercase tracking-[0.2em]">Tahun Akademik:</span>
                <span class="bg-indigo-50 text-indigo-700 px-3 py-1 rounded-xl text-[10px] font-black uppercase tracking-wider border border-indigo-100">
                    {{ $taAktif->nama_tahun ?? 'Tidak Aktif' }}
                </span>
            </div>
        </div>
    </div>

    {{-- 2. JADWAL GRID --}}
    @if(count($jadwals) > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($jadwals as $jadwal)
            <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-200 overflow-hidden hover:shadow-2xl hover:-translate-y-1 transition-all duration-500 group flex flex-col relative">
                
                {{-- Team Teaching Badge --}}
                @if($jadwal->dosens->count() > 1)
                <div class="absolute top-6 right-6 z-10">
                    <div class="bg-amber-100 text-amber-700 px-2 py-1 rounded-lg text-[8px] font-black uppercase tracking-widest border border-amber-200">Team Teaching</div>
                </div>
                @endif

                {{-- Card Header: Time & Day --}}
                <div class="px-8 py-8 bg-slate-50/50 border-b border-slate-100">
                    <div class="flex items-center gap-3 mb-4">
                        <span class="px-4 py-1.5 rounded-xl bg-[#fcc000] text-[#002855] text-[10px] font-black uppercase tracking-widest shadow-lg shadow-amber-500/20">
                            {{ $jadwal->hari }}
                        </span>
                        <div class="h-1 w-1 bg-slate-300 rounded-full"></div>
                        <span class="text-xs font-black text-slate-500 tracking-tighter">
                            {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}
                        </span>
                    </div>

                    <h3 class="text-xl font-black text-[#002855] leading-[1.1] uppercase tracking-tight group-hover:text-indigo-600 transition-colors">
                        {{ $jadwal->mataKuliah->nama_mk }}
                    </h3>
                    <div class="flex items-center gap-3 mt-4">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ $jadwal->mataKuliah->kode_mk }}</span>
                        <span class="w-1 h-1 bg-slate-200 rounded-full"></span>
                        <span class="text-[10px] font-black text-indigo-400 uppercase tracking-widest">{{ $jadwal->mataKuliah->sks_default }} SKS</span>
                    </div>
                </div>

                {{-- Card Body: Info --}}
                <div class="p-8 flex-1 space-y-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest">Ruangan</p>
                            <p class="text-sm font-black text-[#002855] uppercase tracking-tight">
                                R. {{ $jadwal->ruang->kode_ruang ?? 'TBA' }}
                            </p>
                        </div>
                        <div class="space-y-1 text-right">
                            <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest">Kelas</p>
                            <p class="text-sm font-black text-slate-700 uppercase tracking-tight">{{ $jadwal->nama_kelas }}</p>
                        </div>
                    </div>

                    {{-- Multi-Dosen Team --}}
                    <div class="pt-6 border-t border-slate-50">
                        <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest mb-3">Partner Tim:</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach($jadwal->dosens as $tim)
                                <div class="flex items-center gap-2 px-3 py-1.5 bg-slate-50 rounded-xl border border-slate-100 {{ $tim->id === $dosen->id ? 'ring-1 ring-indigo-200' : '' }}">
                                    <div class="w-1.5 h-1.5 rounded-full {{ $tim->pivot->is_koordinator ? 'bg-amber-400 animate-pulse' : 'bg-slate-300' }}"></div>
                                    <span class="text-[10px] font-bold text-slate-600 truncate max-w-[120px] uppercase">{{ $tim->person->nama_lengkap }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    @if($jadwal->programKelasAllow)
                    <div class="mt-4 p-3 bg-amber-50/50 rounded-2xl border border-amber-100/50 flex items-center justify-center gap-2">
                        <span class="text-[9px] font-black text-amber-600 uppercase tracking-[0.1em]">Khusus {{ $jadwal->programKelasAllow->nama_program }}</span>
                    </div>
                    @endif
                </div>

                {{-- Action Footer --}}
                <div class="px-8 py-6 bg-slate-50/30 border-t border-slate-50 mt-auto">
                    <a href="{{ route('dosen.nilai', $jadwal->id) }}" 
                        class="flex items-center justify-center w-full py-4 bg-[#002855] text-white rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] hover:bg-indigo-600 hover:-translate-y-1 transition-all shadow-xl shadow-blue-900/10 active:scale-95 group/btn">
                        <svg class="w-4 h-4 mr-2 group-hover/btn:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                        KELOLA NILAI & ABSENSI
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    @else
        <div class="flex flex-col items-center justify-center py-32 bg-white rounded-[3rem] border-2 border-dashed border-slate-100">
            <div class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mb-6 shadow-inner">
                <svg class="w-10 h-10 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
            </div>
            <h3 class="text-sm font-black text-slate-400 uppercase tracking-[0.3em]">Jadwal Belum Tersedia</h3>
            <p class="text-slate-300 text-xs font-bold mt-2 uppercase tracking-widest">Anda tidak memiliki beban mengajar di semester ini.</p>
        </div>
    @endif

    {{-- 3. FOOTER INFO --}}
    <div class="pt-10 flex flex-col items-center gap-2 opacity-20 grayscale pointer-events-none border-t border-slate-100">
        <p class="text-[9px] font-black uppercase tracking-[0.6em] text-[#002855]">SI-AKADEMIK ENTEPRISE v4.2</p>
    </div>
</div>