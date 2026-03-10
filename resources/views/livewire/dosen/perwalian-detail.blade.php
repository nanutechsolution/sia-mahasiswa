<div class="space-y-6 animate-in fade-in duration-500 pb-12">
    {{-- Breadcrumb --}}
    <div>
        <a href="{{ route('dosen.perwalian') }}" class="inline-flex items-center text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-[#002855] transition-colors group" wire:navigate>
            <svg class="w-4 h-4 mr-2 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7" /></svg>
            Kembali ke Daftar Perwalian
        </a>
    </div>

    {{-- Student Identity Card --}}
    <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-200 overflow-hidden relative">
        <div class="absolute top-0 right-0 p-8 opacity-[0.03] pointer-events-none">
            <svg class="w-48 h-48 text-[#002855]" fill="currentColor" viewBox="0 0 24 24"><path d="M12 14l9-5-9-5-9 5 9 5z"/><path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
        </div>
        
        <div class="p-8 md:p-12 relative z-10">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-8">
                <div class="flex items-center gap-6">
                    <div class="w-16 h-16 bg-[#002855] text-[#fcc000] rounded-2xl flex items-center justify-center text-2xl font-black shadow-xl shadow-blue-900/20 uppercase">
                        {{ substr($krs->mahasiswa->person->nama_lengkap, 0, 1) }}
                    </div>
                    <div>
                        <h1 class="text-2xl font-black text-[#002855] uppercase tracking-tight leading-none">{{ $krs->mahasiswa->person->nama_lengkap }}</h1>
                        <p class="text-sm font-bold text-slate-400 mt-2 flex items-center gap-2">
                            <span class="font-mono bg-slate-100 px-2 py-0.5 rounded text-slate-600">{{ $krs->mahasiswa->nim }}</span>
                            <span>&bull;</span>
                            <span class="uppercase tracking-wider text-xs">{{ $krs->mahasiswa->prodi->nama_prodi }}</span>
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-4 bg-slate-50 p-4 rounded-[1.5rem] border border-slate-100">
                    <div class="text-right">
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Status Pengajuan</p>
                        <span class="text-sm font-black uppercase tracking-tighter {{ $krs->status_krs == 'DISETUJUI' ? 'text-emerald-600' : 'text-amber-500' }}">
                            {{ $krs->status_krs }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Academic Track Record --}}
            <div class="mt-12 grid grid-cols-2 md:grid-cols-4 gap-8 border-t border-slate-50 pt-10">
                <div class="space-y-1">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">IPK Saat Ini</p>
                    <p class="text-3xl font-black text-[#002855] italic">{{ number_format($riwayat->ipk ?? 0, 2) }}</p>
                </div>
                <div class="space-y-1">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">SKS Lulus</p>
                    <p class="text-3xl font-black text-slate-700 italic">{{ $riwayat->sks_total ?? 0 }}</p>
                </div>
                <div class="space-y-1">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">IPS Lalu</p>
                    <p class="text-3xl font-black text-slate-700 italic">{{ number_format($riwayat->ips ?? 0, 2) }}</p>
                </div>
                <div class="bg-[#fcc000] p-4 rounded-2xl shadow-lg shadow-amber-500/20 text-[#002855]">
                    <p class="text-[9px] font-black uppercase tracking-widest opacity-60">SKS Diajukan</p>
                    <p class="text-3xl font-black italic">{{ $totalSks }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Selected Courses Table --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-[2rem] shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-8 py-6 bg-slate-50/50 border-b border-slate-100">
                    <h3 class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em]">Rincian Mata Kuliah yang Diambil</h3>
                </div>
                <div class="divide-y divide-slate-50">
                    @foreach($krs->details as $detail)
                    <div class="p-8 hover:bg-slate-50/80 transition-all group">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-3">
                                    <span class="px-2 py-0.5 bg-indigo-50 text-indigo-600 rounded text-[9px] font-black uppercase tracking-widest border border-indigo-100">{{ $detail->kode_mk_snapshot }}</span>
                                    <span class="text-[10px] font-black text-slate-400 uppercase">{{ $detail->sks_snapshot }} SKS</span>
                                </div>
                                <h4 class="text-lg font-black text-slate-800 uppercase tracking-tight">{{ $detail->nama_mk_snapshot }}</h4>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                                    <div class="flex items-center gap-3 text-xs font-bold text-slate-500">
                                        <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-indigo-500">📅</div>
                                        <div>
                                            <p class="text-[8px] font-black text-slate-300 uppercase">Jadwal</p>
                                            {{ $detail->jadwalKuliah->hari ?? '-' }}, {{ substr($detail->jadwalKuliah->jam_mulai ?? '', 0, 5) }}
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3 text-xs font-bold text-slate-500">
                                        <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-indigo-500">🏛️</div>
                                        <div>
                                            <p class="text-[8px] font-black text-slate-300 uppercase">Lokasi</p>
                                            R. {{ $detail->jadwalKuliah->ruang->kode_ruang ?? 'N/A' }} ({{ $detail->jadwalKuliah->nama_kelas ?? '-' }})
                                        </div>
                                    </div>
                                </div>
                                
                                {{-- Team Teaching Display --}}
                                <div class="mt-6 pt-4 border-t border-slate-50">
                                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2">Tim Pengampu:</p>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($detail->jadwalKuliah->dosens as $d)
                                        <span class="inline-flex items-center gap-2 px-3 py-1 bg-white border border-slate-200 rounded-full text-[10px] font-bold text-slate-600 uppercase">
                                            <div class="w-1.5 h-1.5 rounded-full {{ $d->pivot->is_koordinator ? 'bg-amber-400 animate-pulse' : 'bg-slate-300' }}"></div>
                                            {{ $d->person->nama_lengkap }}
                                        </span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Action Card --}}
        <div class="space-y-6">
            <div class="bg-[#002855] p-8 rounded-[2.5rem] shadow-2xl shadow-blue-900/30 text-white sticky top-28">
                <h3 class="text-xs font-black uppercase tracking-[0.2em] mb-8 text-[#fcc000]">Keputusan Perwalian</h3>

                @if($krs->status_krs == 'AJUKAN')
                <div class="space-y-4">
                    <button wire:click="setujui" wire:confirm="Setujui rencana studi mahasiswa ini?" 
                        class="w-full py-5 bg-emerald-500 hover:bg-emerald-600 text-white rounded-2xl font-black text-xs uppercase tracking-[0.2em] shadow-lg shadow-emerald-900/20 transition-all hover:-translate-y-1">
                        SETUJUI KRS
                    </button>
                    <button wire:click="tolak" wire:confirm="Kembalikan ke mahasiswa untuk revisi?"
                        class="w-full py-5 bg-white/10 hover:bg-rose-500 text-white rounded-2xl font-black text-xs uppercase tracking-[0.2em] border border-white/20 transition-all">
                        TOLAK / REVISI
                    </button>
                </div>
                @elseif($krs->status_krs == 'DISETUJUI')
                <div class="text-center space-y-4">
                    <div class="w-16 h-16 bg-emerald-500/20 text-emerald-400 rounded-full flex items-center justify-center mx-auto text-3xl">✓</div>
                    <div>
                        <h4 class="font-black uppercase tracking-widest text-sm">Akses Terkunci</h4>
                        <p class="text-[10px] font-bold text-slate-400 mt-2 uppercase">KRS telah disetujui dan dikunci sistem.</p>
                    </div>
                    <button wire:click="tolak" class="text-[10px] font-black uppercase text-rose-400 hover:text-rose-300 underline tracking-widest mt-6">Batalkan & Edit</button>
                </div>
                @endif

                <div class="mt-10 pt-10 border-t border-white/10">
                    <p class="text-[9px] font-bold text-indigo-300 leading-relaxed uppercase">Catatan: Pastikan beban SKS mahasiswa tidak mengganggu progres kelulusan atau melebihi limit jatah berdasarkan IPK.</p>
                </div>
            </div>
        </div>
    </div>
</div>