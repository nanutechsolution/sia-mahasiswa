<div>
    {{-- SEO & Header --}}
    <x-slot name="title">Jadwal Mengajar - UNMARIS</x-slot>
    <x-slot name="header">Jadwal Perkuliahan Dosen</x-slot>

    <div class="space-y-8">
        {{-- Intro Section --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <p class="text-slate-500 text-sm font-medium">Manajemen beban mengajar dan akses cepat penginputan nilai mahasiswa.</p>
            </div>
            <div class="inline-flex items-center px-4 py-2 bg-emerald-50 border border-emerald-100 rounded-xl">
                <span class="w-2 h-2 bg-emerald-500 rounded-full mr-2 animate-pulse"></span>
                <span class="text-[10px] font-black text-emerald-700 uppercase tracking-widest">
                    {{ $taAktif->nama_tahun ?? 'Semester Non-Aktif' }}
                </span>
            </div>
        </div>

        @if(count($jadwals) > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($jadwals as $jadwal)
            <div class="group bg-white rounded-[2rem] shadow-sm border border-slate-200 overflow-hidden hover:shadow-2xl hover:shadow-indigo-100 hover:border-unmaris-blue/30 transition-all duration-500 flex flex-col">
                {{-- Card Header --}}
                <div class="p-6 lg:p-8 flex-1">
                    <div class="flex justify-between items-start mb-6">
                        <div class="px-3 py-1 bg-unmaris-blue/5 border border-unmaris-blue/10 rounded-lg">
                            <span class="text-[10px] font-black text-unmaris-blue uppercase tracking-widest">Kelas {{ $jadwal->nama_kelas }}</span>
                        </div>
                        <div class="text-right">
                            <span class="text-[11px] font-black text-unmaris-gold uppercase tracking-[0.2em]">{{ $jadwal->hari }}</span>
                        </div>
                    </div>

                    <h3 class="text-lg font-black text-slate-800 leading-tight group-hover:text-unmaris-blue transition-colors">
                        {{ $jadwal->mataKuliah->nama_mk }}
                    </h3>
                    <p class="text-[11px] font-bold text-slate-400 uppercase mt-1 tracking-tighter">{{ $jadwal->mataKuliah->kode_mk }} • {{ $jadwal->mataKuliah->sks_default }} SKS</p>

                    <div class="mt-8 space-y-3">
                        <div class="flex items-center text-sm font-semibold text-slate-600">
                            <div class="w-8 h-8 rounded-lg bg-slate-50 flex items-center justify-center mr-3 group-hover:bg-unmaris-blue group-hover:text-white transition-colors">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <span class="tabular-nums">{{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }} WIB</span>
                        </div>
                        <div class="flex items-center text-sm font-semibold text-slate-600">
                            <div class="w-8 h-8 rounded-lg bg-slate-50 flex items-center justify-center mr-3 group-hover:bg-unmaris-blue group-hover:text-white transition-colors">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <span>Ruang {{ $jadwal->ruang }}</span>
                        </div>
                    </div>

                    @if($jadwal->programKelasAllow)
                    <div class="mt-6 pt-6 border-t border-slate-100">
                        <span class="inline-flex items-center text-[9px] font-black text-slate-400 uppercase tracking-widest">
                            <svg class="w-3 h-3 mr-1 text-unmaris-gold" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                                <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd" />
                            </svg>
                            Khusus {{ $jadwal->programKelasAllow->nama_program }}
                        </span>
                    </div>
                    @endif
                </div>

                {{-- Action Area --}}
                <div class="px-6 py-5 bg-slate-50 border-t border-slate-100">
                    <a href="{{ route('dosen.nilai', $jadwal->id) }}"
                        class="group/btn relative flex items-center justify-center w-full py-3.5 bg-unmaris-blue text-white rounded-2xl text-[11px] font-black uppercase tracking-[0.2em] shadow-xl shadow-indigo-900/10 hover:bg-unmaris-yellow hover:text-unmaris-blue transition-all duration-300">
                        Input Nilai & Presensi
                        <svg class="w-4 h-4 ml-2 transition-transform group-hover/btn:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="bg-white rounded-[3rem] shadow-sm border border-slate-200 py-24 text-center">
            <div class="max-w-xs mx-auto">
                <div class="w-20 h-20 bg-slate-50 rounded-3xl flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest">Data Tidak Ditemukan</h3>
                <p class="mt-2 text-xs font-medium text-slate-400 leading-relaxed">Anda belum terdaftar memiliki jadwal mengajar pada semester yang aktif saat ini.</p>
            </div>
        </div>
        @endif
    </div>


</div>