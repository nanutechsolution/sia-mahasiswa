<div class="space-y-8 animate-in fade-in duration-700 pb-12">
    
    {{-- 1. HEADER SECTION --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div class="space-y-2">
            <h1 class="text-3xl font-black text-[#002855] tracking-tight flex items-center gap-3">
                <div class="w-12 h-12 bg-[#002855] rounded-2xl flex items-center justify-center text-[#fcc000] shadow-xl shadow-blue-900/20">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
                EDOM Analytics
            </h1>
            <div class="flex items-center gap-3 ml-1">
                <span class="text-slate-400 text-[10px] font-black uppercase tracking-[0.2em]">Periode Analisis:</span>
                <span class="bg-indigo-50 text-indigo-700 px-3 py-1 rounded-xl text-[10px] font-black uppercase tracking-wider border border-indigo-100">
                    {{ $taAktif->nama_tahun ?? 'Semester Tidak Aktif' }}
                </span>
            </div>
        </div>
        <div class="flex gap-3">
            <button class="px-6 py-4 bg-white border border-slate-200 rounded-2xl font-black text-[10px] uppercase tracking-widest text-slate-500 hover:bg-slate-50 transition-all shadow-sm">
                Cetak Rekap Nasional
            </button>
            <a href="{{ route('admin.lpm.edom.setup') }}" class="px-8 py-4 bg-[#fcc000] text-[#002855] rounded-2xl font-black text-[10px] uppercase tracking-[0.2em] shadow-xl shadow-amber-500/30 hover:bg-[#ffca28] hover:-translate-y-1 transition-all" wire:navigate>
                Pengaturan Instrumen
            </a>
        </div>
    </div>

    {{-- 2. BIG STATS BENTO BOX --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-200 flex flex-col justify-center">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2">Total Partisipasi</p>
            <h3 class="text-4xl font-black text-[#002855] italic tracking-tighter">{{ number_format($stats['total_responden']) }}</h3>
            <p class="text-[9px] font-bold text-slate-300 mt-2 uppercase tracking-widest">Dari {{ number_format($stats['total_kewajiban']) }} Entitas MK</p>
        </div>

        <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-200 flex flex-col justify-center">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2">Rata-rata Universitas</p>
            <div class="flex items-baseline gap-2">
                <h3 class="text-4xl font-black text-emerald-600 italic tracking-tighter">{{ $stats['rata_rata_univ'] }}</h3>
                <span class="text-sm font-black text-slate-200 italic">/ 4.00</span>
            </div>
            <div class="mt-4">
                <span class="px-3 py-1 bg-emerald-50 text-emerald-600 rounded-lg text-[8px] font-black uppercase tracking-widest border border-emerald-100 italic">
                    {{ $stats['kategori_univ'] }}
                </span>
            </div>
        </div>

        <div class="bg-[#002855] p-8 rounded-[2.5rem] shadow-xl shadow-blue-900/20 text-white relative overflow-hidden flex flex-col justify-center">
            <div class="absolute -right-4 -bottom-4 bg-white/5 w-32 h-32 rounded-full blur-2xl"></div>
            <p class="text-[10px] font-black text-[#fcc000] uppercase tracking-[0.2em] mb-2">Tingkat Respon</p>
            <h3 class="text-4xl font-black italic tracking-tighter">{{ round($stats['partisipasi_persen']) }}%</h3>
            <div class="mt-4 w-full bg-white/10 rounded-full h-2 overflow-hidden shadow-inner">
                <div class="bg-[#fcc000] h-full transition-all duration-1000 shadow-[0_0_10px_rgba(252,192,0,0.5)]" style="width: {{ $stats['partisipasi_persen'] }}%"></div>
            </div>
        </div>

        <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-200 flex flex-col items-center justify-center text-center">
            <div class="w-14 h-14 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center mb-3 shadow-inner">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Integritas Data</p>
            <h4 class="text-xs font-black text-slate-800 uppercase mt-1 italic">Verified Real-Time</h4>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 pt-4">
        
        {{-- 3. RANKING DOSEN --}}
        <div class="lg:col-span-8 space-y-6">
            <div class="bg-white rounded-[3rem] shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-10 py-8 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
                    <h3 class="text-[11px] font-black text-[#002855] uppercase tracking-[0.4em]">Indeks Kinerja Dosen Tertinggi</h3>
                    <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-[#fcc000] shadow-sm border border-slate-100">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    </div>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse($topPerformers as $index => $dosen)
                    <div class="px-10 py-6 flex items-center justify-between group hover:bg-slate-50 transition-all duration-300">
                        <div class="flex items-center gap-8">
                            <span class="w-8 text-xs font-black text-slate-200 group-hover:text-[#002855] transition-colors italic">#{{ $index + 1 }}</span>
                            <div class="w-14 h-14 rounded-2xl bg-indigo-50 flex items-center justify-center text-[#002855] font-black text-xl group-hover:bg-[#002855] group-hover:text-white transition-all shadow-sm uppercase shrink-0">
                                {{ substr($dosen->nama_lengkap, 0, 1) }}
                            </div>
                            <div class="space-y-1">
                                <h4 class="text-sm font-black text-slate-800 leading-tight uppercase group-hover:text-indigo-600 transition-colors">{{ $dosen->nama_lengkap }}</h4>
                                <div class="flex items-center gap-3">
                                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">{{ $dosen->prodi }}</span>
                                    <div class="w-1 h-1 bg-slate-200 rounded-full"></div>
                                    <span class="text-[9px] font-black text-indigo-500 uppercase tracking-widest italic">{{ $dosen->jumlah_mhs }} Responden</span>
                                </div>
                            </div>
                        </div>
                        <div class="text-right flex flex-col items-end">
                            <p class="text-2xl font-black text-emerald-600 tracking-tighter italic">{{ number_format($dosen->skor_rata, 2) }}</p>
                            <p class="text-[8px] font-black text-slate-300 uppercase tracking-[0.2em] mt-1">Final Score</p>
                        </div>
                    </div>
                    @empty
                    <div class="p-32 text-center text-slate-300 font-bold uppercase tracking-[0.4em] text-xs">Awaiting evaluation data...</div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- 4. STATS PER PRODI SIDEBAR --}}
        <div class="lg:col-span-4 space-y-8">
            <div class="bg-white rounded-[3rem] shadow-sm border border-slate-200 p-10">
                <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.4em] mb-10 border-b border-slate-50 pb-6">Kualitas Per Prodi</h3>
                <div class="space-y-8">
                    @foreach($prodiStats as $ps)
                    <div class="group">
                        <div class="flex justify-between items-end mb-3">
                            <div class="space-y-1">
                                <h5 class="text-xs font-black text-[#002855] leading-tight uppercase group-hover:text-indigo-600 transition-colors">{{ $ps->nama_prodi }}</h5>
                                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter">{{ $ps->jenjang }} &bull; {{ $ps->total_filled }} Respon</p>
                            </div>
                            <span class="text-sm font-black text-indigo-600 italic tracking-tighter">{{ number_format($ps->skor, 2) }}</span>
                        </div>
                        <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden shadow-inner">
                            <div class="bg-gradient-to-r from-indigo-500 to-[#002855] h-full transition-all duration-1000 group-hover:shadow-[0_0_10px_rgba(79,70,229,0.4)]" 
                                style="width: {{ ($ps->skor / 4) * 100 }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
                
                <button class="w-full mt-12 py-5 bg-slate-50 hover:bg-[#002855] hover:text-white text-[9px] font-black text-[#002855] uppercase tracking-[0.2em] rounded-2xl transition-all border border-slate-100 shadow-sm">
                    Lihat Peta Mutu Lengkap
                </button>
            </div>

            {{-- 5. ACCREDITATION INFO BOX --}}
            <div class="bg-[#fcc000] rounded-[3rem] p-10 shadow-2xl shadow-amber-500/20 text-[#002855] relative overflow-hidden group">
                <div class="absolute -right-10 -top-10 w-40 h-40 bg-white/20 rounded-full blur-3xl group-hover:scale-110 transition-transform"></div>
                <h4 class="text-xs font-black uppercase tracking-[0.2em] mb-4 flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Aset Akreditasi
                </h4>
                <p class="text-[11px] leading-relaxed font-bold uppercase tracking-wider opacity-80 italic">
                    Data EDOM terintegrasi langsung sebagai bukti rill **Kriteria 4 (SDM)** dan **Kriteria 6 (Pendidikan)** pada Instrumen BAN-PT / LAM.
                </p>
            </div>
        </div>
    </div>

    {{-- 6. SYSTEM FOOTER --}}
    <div class="pt-10 flex flex-col items-center gap-2 opacity-20 grayscale pointer-events-none border-t border-slate-100">
        <p class="text-[9px] font-black uppercase tracking-[0.6em] text-[#002855]">LPM MONITORING SUITE &bull; v4.2 PRO</p>
    </div>
</div>