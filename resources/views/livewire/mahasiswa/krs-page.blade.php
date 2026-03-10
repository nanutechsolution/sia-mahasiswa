<div class="min-h-screen bg-slate-50 pb-32 lg:pb-12 font-sans text-slate-800">
    {{-- Error/Success Notification --}}
    <div class="fixed top-24 right-4 z-50 flex flex-col gap-2 max-w-sm">
        @if (session()->has('success'))
        <div class="p-4 bg-emerald-500 text-white rounded-2xl shadow-xl animate-in slide-in-from-right duration-300 flex items-center gap-3">
            <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p class="text-sm font-bold">{{ session('success') }}</p>
        </div>
        @endif
        @if (session()->has('error'))
        <div class="p-4 bg-rose-500 text-white rounded-2xl shadow-xl animate-in slide-in-from-right duration-300 flex items-center gap-3">
            <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <p class="text-sm font-bold">{{ session('error') }}</p>
        </div>
        @endif
    </div>

    {{-- STICKY HEADER --}}
    <header class="sticky top-0 z-40 bg-white/90 backdrop-blur-md border-b border-slate-200 shadow-sm transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                {{-- Profile Section --}}
                <div class="flex items-center gap-4 w-full md:w-auto">
                    <div class="relative shrink-0">
                        <div class="w-12 h-12 bg-[#002855] text-white rounded-full flex items-center justify-center font-bold text-lg shadow-md ring-4 ring-white">
                            {{ substr($mahasiswa->person->nama_lengkap, 0, 1) }}
                        </div>
                    </div>
                    <div class="min-w-0">
                        <h2 class="text-lg font-bold text-slate-900 truncate leading-tight">{{ $mahasiswa->person->nama_lengkap }}</h2>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-xs font-mono text-slate-500 bg-slate-100 px-2 py-0.5 rounded">{{ $mahasiswa->nim }}</span>
                            <span class="text-xs font-semibold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-full">Smt {{ $semesterBerjalan }} &bull; {{ $mahasiswa->prodi->nama_prodi }}</span>
                        </div>
                    </div>
                </div>

                {{-- SKS Counter --}}
                <div class="w-full md:w-auto flex items-center justify-between md:justify-end bg-slate-50 md:bg-transparent p-3 md:p-0 rounded-xl border md:border-0 border-slate-200">
                    <div class="text-right">
                        <div class="flex items-baseline justify-end gap-1.5">
                            <span class="text-3xl font-bold text-[#002855] tracking-tight">{{ $totalSks }}</span>
                            @if(!$isPaket)
                            <span class="text-lg font-medium text-slate-400">/ {{ $maxSks }}</span>
                            @endif
                            <span class="text-xs font-bold text-slate-400 uppercase ml-1">Kredit Diambil</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
        @if($blockKrs)
        <div class="mb-8 bg-rose-50 border border-rose-100 rounded-[2rem] p-8 flex flex-col md:flex-row items-center gap-6 animate-in zoom-in-95 duration-300">
            <div class="w-16 h-16 bg-rose-100 text-rose-600 rounded-full flex items-center justify-center shrink-0">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            </div>
            <div class="text-center md:text-left">
                <h4 class="text-lg font-black text-rose-900 uppercase tracking-tight">KRS Terkunci (Administrative Hold)</h4>
                <p class="text-rose-700 mt-2 font-medium leading-relaxed">{{ $pesanBlock }}</p>
            </div>
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            {{-- LEFT COLUMN: Selected Courses --}}
            <div class="lg:col-span-8 space-y-6">
                <div class="bg-white rounded-[2rem] shadow-sm border border-slate-200 overflow-hidden">
                    <div class="px-8 py-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/30">
                        <h3 class="font-black text-[#002855] uppercase tracking-wider text-sm flex items-center gap-3">
                            <div class="w-2 h-2 bg-indigo-500 rounded-full animate-pulse"></div>
                            Daftar Rencana Studi
                        </h3>
                        <span class="px-4 py-1.5 rounded-full text-[10px] font-black tracking-[0.1em] uppercase shadow-sm {{ $statusKrs == 'DISETUJUI' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-800 text-white' }}">
                            STATUS: {{ $statusKrs }}
                        </span>
                    </div>

                    <div class="divide-y divide-slate-100">
                        @forelse($krsDiambil as $row)
                        <div class="p-8 hover:bg-slate-50/80 transition-all group relative">
                            <div class="flex items-start justify-between gap-6">
                                <div class="flex-1 space-y-4">
                                    <div class="flex items-center gap-3 flex-wrap">
                                        <span class="text-[10px] font-black text-[#002855] bg-indigo-50 border border-indigo-100 px-3 py-1 rounded-lg uppercase tracking-wider">{{ $row->kode_mk_snapshot }}</span>
                                        <span class="px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider bg-slate-100 text-slate-500">
                                            {{ $row->sks_snapshot }} SKS
                                        </span>
                                    </div>

                                    <div>
                                        <h4 class="text-xl font-black text-slate-900 leading-none uppercase tracking-tight">{{ $row->nama_mk_snapshot }}</h4>
                                        
                                        @if($row->jadwalKuliah)
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                                            <div class="flex items-center gap-3 text-sm font-bold text-slate-500">
                                                <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                {{ $row->jadwalKuliah->hari }}, {{ substr($row->jadwalKuliah->jam_mulai, 0, 5) }} - {{ substr($row->jadwalKuliah->jam_selesai, 0, 5) }}
                                            </div>
                                            <div class="flex items-center gap-3 text-sm font-bold text-slate-500">
                                                <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2"/></svg>
                                                Ruang {{ $row->jadwalKuliah->ruang->kode_ruang ?? 'TBA' }} ({{ $row->jadwalKuliah->nama_kelas }})
                                            </div>
                                        </div>

                                        <div class="mt-4 pt-4 border-t border-slate-50">
                                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Team Pengampu:</p>
                                            <div class="flex flex-wrap gap-2">
                                                @foreach($row->jadwalKuliah->dosens as $d)
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-white border border-slate-200 rounded-full text-[10px] font-bold text-slate-600 uppercase">
                                                    <div class="w-1.5 h-1.5 rounded-full {{ $d->pivot->is_koordinator ? 'bg-amber-400' : 'bg-slate-300' }}"></div>
                                                    {{ $d->person->nama_lengkap }}
                                                </span>
                                                @endforeach
                                            </div>
                                        </div>
                                        @else
                                        <div class="mt-4 inline-flex items-center px-4 py-2 bg-amber-50 text-amber-700 text-[10px] font-black rounded-xl uppercase tracking-widest border border-amber-100">
                                            Jadwal Fleksibel (Mandiri)
                                        </div>
                                        @endif
                                    </div>
                                </div>

                                @if($statusKrs == 'DRAFT')
                                <button wire:click="hapusMatkul('{{ $row->id }}')" wire:confirm="Hapus MK ini?" class="p-4 rounded-2xl text-slate-300 hover:text-rose-500 hover:bg-rose-50 transition-all opacity-0 group-hover:opacity-100">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                                @endif
                            </div>
                        </div>
                        @empty
                        <div class="py-24 text-center">
                            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6">
                                <svg class="w-10 h-10 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            </div>
                            <h4 class="text-slate-400 font-bold uppercase tracking-widest text-xs">Belum ada mata kuliah yang dipilih</h4>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- RIGHT COLUMN: Offers --}}
            <div class="lg:col-span-4 space-y-6">
                <div class="sticky top-28 space-y-6">
                    <div class="bg-white rounded-[2rem] shadow-2xl shadow-slate-200/50 border border-slate-200 overflow-hidden flex flex-col max-h-[calc(100vh-160px)] {{ $statusKrs != 'DRAFT' || $blockKrs ? 'opacity-40 grayscale pointer-events-none' : '' }}">
                        <div class="px-6 py-5 bg-[#002855] text-white flex justify-between items-center shrink-0">
                            <h3 class="font-black text-xs tracking-[0.2em] uppercase">Penawaran Semester</h3>
                            <div class="px-2 py-1 bg-white/10 rounded-lg text-[9px] font-bold">GANJIL/GENAP</div>
                        </div>

                        <div class="overflow-y-auto custom-scrollbar bg-slate-50/50 p-4 space-y-4">
                            @forelse($jadwalTersedia as $j)
                            <div class="bg-white p-6 rounded-3xl border border-slate-100 hover:border-indigo-500 hover:shadow-lg transition-all group cursor-default">
                                <div class="flex justify-between items-start mb-4">
                                    <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-700 flex flex-col items-center justify-center font-black text-xs">
                                        {{ $j->mataKuliah->sks_default }}
                                        <span class="text-[7px] font-bold uppercase -mt-1 text-indigo-400">SKS</span>
                                    </div>
                                    <span class="text-[9px] font-black text-slate-400 bg-slate-100 px-3 py-1 rounded-full uppercase tracking-widest">{{ $j->nama_kelas }}</span>
                                </div>

                                <h4 class="text-sm font-black text-slate-800 leading-tight uppercase group-hover:text-indigo-600 transition-colors">
                                    {{ $j->mataKuliah->nama_mk }}
                                </h4>

                                <div class="space-y-2 mt-4 pt-4 border-t border-slate-50">
                                    <div class="flex items-center gap-3 text-[10px] font-bold text-slate-500 uppercase tracking-tighter">
                                        <svg class="w-3.5 h-3.5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        {{ $j->hari }}, {{ substr($j->jam_mulai,0,5) }}
                                    </div>
                                    <div class="flex items-center gap-3 text-[10px] font-bold text-slate-500 uppercase tracking-tighter">
                                        <svg class="w-3.5 h-3.5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2"/></svg>
                                        R. {{ $j->ruang->kode_ruang ?? 'TBA' }}
                                    </div>
                                </div>

                                <button wire:click="ambilMatkul('{{ $j->id }}')" 
                                    class="w-full mt-6 py-4 bg-slate-900 text-white rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] shadow-lg shadow-slate-900/10 hover:bg-indigo-600 hover:-translate-y-1 active:scale-95 transition-all">
                                    AMBIL MATA KULIAH
                                </button>
                            </div>
                            @empty
                            <div class="text-center py-12">
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Tidak ada jadwal tersedia</p>
                            </div>
                            @endforelse
                        </div>
                    </div>

                    @if($statusKrs == 'DRAFT' && $totalSks > 0 && !$blockKrs)
                    <div class="pt-4 animate-in slide-in-from-bottom-4 duration-500">
                        <button wire:click="ajukanKrs" class="w-full py-5 bg-[#fcc000] text-[#002855] rounded-3xl font-black text-sm uppercase tracking-[0.2em] shadow-xl shadow-amber-500/30 hover:-translate-y-1 active:scale-95 transition-all flex items-center justify-center gap-4 group">
                            <span>Ajukan Sekarang</span>
                            <svg class="w-5 h-5 group-hover:translate-x-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/></svg>
                        </button>
                        <p class="text-center text-[9px] text-slate-400 font-bold mt-4 uppercase tracking-[0.1em]">Pastikan rencana studi Anda sudah final.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </main>
</div>