<div class="space-y-8 animate-in fade-in duration-500">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-[#002855]">Jadwal Mengajar</h1>
            <div class="flex items-center gap-2 mt-1">
                <span class="text-slate-500 text-sm">Semester:</span>
                <span class="bg-[#002855]/10 text-[#002855] px-2 py-0.5 rounded text-xs font-bold uppercase">
                    {{ $taAktif->nama_tahun ?? 'Tidak Aktif' }}
                </span>
            </div>
        </div>
    </div>

    {{-- Grid Jadwal --}}
    @if(count($jadwals) > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($jadwals as $jadwal)
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden hover:shadow-xl transition-all duration-300 group flex flex-col">
                {{-- Card Header --}}
                <div class="px-6 py-5 bg-slate-50 border-b border-slate-100 flex justify-between items-start">
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <span class="px-2.5 py-1 rounded-lg bg-[#fcc000] text-[#002855] text-[10px] font-black uppercase tracking-widest shadow-sm">
                                {{ $jadwal->hari }}
                            </span>
                            <span class="text-xs font-bold text-slate-500 flex items-center">
                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}
                            </span>
                        </div>
                        <h3 class="text-lg font-black text-[#002855] leading-tight group-hover:text-indigo-600 transition-colors">
                            {{ $jadwal->mataKuliah->nama_mk }}
                        </h3>
                        <p class="text-[10px] font-mono font-bold text-slate-400 mt-1 uppercase tracking-wider">
                            {{ $jadwal->mataKuliah->kode_mk }} • {{ $jadwal->mataKuliah->sks_default }} SKS
                        </p>
                    </div>
                </div>

                {{-- Card Body --}}
                <div class="p-6 flex-1">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                                </div>
                                <div>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Ruangan</p>
                                    <p class="text-sm font-bold text-slate-700">{{ $jadwal->ruang }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 text-right">
                                <div>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Kelas</p>
                                    <p class="text-sm font-bold text-slate-700">{{ $jadwal->nama_kelas }}</p>
                                </div>
                                <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                                </div>
                            </div>
                        </div>

                        @if($jadwal->programKelasAllow)
                        <div class="flex items-center justify-center p-2 bg-amber-50 rounded-xl border border-amber-100">
                            <span class="text-[10px] font-bold text-amber-700 uppercase tracking-wide">
                                Khusus Program {{ $jadwal->programKelasAllow->nama_program }}
                            </span>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Card Footer --}}
                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100">
                    <a href="{{ route('dosen.nilai', $jadwal->id) }}" 
                        class="flex items-center justify-center w-full py-3 bg-[#002855] text-white rounded-xl text-xs font-black uppercase tracking-widest hover:bg-[#001a38] hover:scale-[1.02] transition-all shadow-lg shadow-indigo-900/20 group-hover:shadow-indigo-900/30">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                        Input Nilai & Presensi
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    @else
        <div class="flex flex-col items-center justify-center py-20 bg-white rounded-3xl border-2 border-dashed border-slate-200">
            <div class="bg-slate-50 p-6 rounded-full mb-4">
                <svg class="w-16 h-16 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z" /></svg>
            </div>
            <h3 class="text-lg font-bold text-slate-600">Belum Ada Jadwal</h3>
            <p class="text-slate-400 text-sm mt-1">Anda tidak memiliki jadwal mengajar di semester aktif ini.</p>
        </div>
    @endif
</div>