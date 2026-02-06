<div class="space-y-8 animate-in fade-in duration-700">
    
    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-3xl font-black text-[#002855] tracking-tight uppercase">Monitoring EDOM</h2>
            <div class="flex items-center gap-2 mt-1">
                <span class="text-slate-500 text-sm font-bold">Periode Analisis:</span>
                <span class="bg-[#002855] text-white px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest shadow-sm">
                    {{ $taAktif->nama_tahun ?? 'Semester Tidak Aktif' }}
                </span>
            </div>
        </div>
        <div class="flex gap-2">
            <button class="px-5 py-2.5 bg-white border border-slate-200 rounded-2xl font-bold text-xs uppercase tracking-widest text-slate-600 hover:bg-slate-50 transition-all shadow-sm">
                Cetak Rekap Nasional
            </button>
            <a href="{{ route('admin.lpm.edom.setup') }}" class="px-5 py-2.5 bg-[#fcc000] text-[#002855] rounded-2xl font-black text-xs uppercase tracking-widest shadow-lg hover:bg-[#fbbf24] transition-all" wire:navigate>
                Pengaturan Instrumen
            </a>
        </div>
    </div>

    {{-- Big Stats Row --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-[2.5rem] shadow-sm border border-slate-100 relative overflow-hidden group">
            <div class="absolute right-0 top-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 20 20"><path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/><path fill-rule="evenodd" d="M4 5a2 2 0 012-2h12a2 2 0 012 2v11a2 2 0 01-2 2H4a2 2 0 01-2-2V5z" clip-rule="evenodd"/></svg>
            </div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Partisipasi</p>
            <h3 class="text-3xl font-black text-[#002855] mt-2">{{ number_format($stats['total_responden']) }}</h3>
            <p class="text-[10px] text-slate-400 mt-1">Dari {{ number_format($stats['total_kewajiban']) }} pengisian MK</p>
        </div>

        <div class="bg-white p-6 rounded-[2.5rem] shadow-sm border border-slate-100">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Rata-rata Universitas</p>
            <div class="flex items-baseline gap-1 mt-2">
                <h3 class="text-3xl font-black text-emerald-600">{{ $stats['rata_rata_univ'] }}</h3>
                <span class="text-xs font-bold text-slate-300">/ 4.00</span>
            </div>
            <div class="mt-3 flex items-center gap-1">
                <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-600 text-[9px] font-black uppercase tracking-tighter">Kategori: Sangat Baik</span>
            </div>
        </div>

        <div class="bg-[#002855] p-6 rounded-[2.5rem] shadow-xl text-white relative overflow-hidden">
            <div class="absolute -right-4 -bottom-4 bg-white/5 w-24 h-24 rounded-full blur-2xl"></div>
            <p class="text-[10px] font-black text-[#fcc000] uppercase tracking-widest">Tingkat Respon</p>
            <h3 class="text-3xl font-black mt-2">{{ round($stats['partisipasi_persen']) }}%</h3>
            <div class="mt-4 w-full bg-white/10 rounded-full h-1.5 overflow-hidden">
                <div class="bg-[#fcc000] h-full" style="width: {{ $stats['partisipasi_persen'] }}%"></div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-[2.5rem] shadow-sm border border-slate-100 flex flex-col justify-center items-center text-center">
             <div class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center mb-2">
                 <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
             </div>
             <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Status Data</p>
             <h4 class="text-xs font-black text-slate-800 uppercase mt-1">Terverifikasi (Real-Time)</h4>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        {{-- Ranking Dosen --}}
        <div class="lg:col-span-8 space-y-6">
            <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-8 py-6 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
                    <h3 class="text-xs font-black text-[#002855] uppercase tracking-[0.2em]">10 Dosen dengan Indeks Tertinggi</h3>
                    <svg class="w-5 h-5 text-[#fcc000]" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse($topPerformers as $index => $dosen)
                    <div class="px-8 py-5 flex items-center justify-between group hover:bg-slate-50 transition-all">
                        <div class="flex items-center gap-5">
                            <span class="w-6 text-xs font-black text-slate-300">#{{ $index + 1 }}</span>
                            <div class="w-12 h-12 rounded-2xl bg-indigo-50 flex items-center justify-center text-[#002855] font-black text-lg group-hover:bg-[#002855] group-hover:text-white transition-all shadow-sm">
                                {{ substr($dosen->nama_lengkap, 0, 1) }}
                            </div>
                            <div>
                                <h4 class="text-sm font-black text-slate-800 leading-tight">{{ $dosen->nama_lengkap }}</h4>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Prodi {{ $dosen->prodi }}</span>
                                    <span class="text-slate-200">|</span>
                                    <span class="text-[9px] font-bold text-indigo-500 uppercase">{{ $dosen->jumlah_mhs }} Responden</span>
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-xl font-black text-emerald-600 tracking-tighter">{{ number_format($dosen->skor_rata, 2) }}</p>
                            <p class="text-[8px] font-black text-slate-300 uppercase tracking-[0.2em]">Skor Akhir</p>
                        </div>
                    </div>
                    @empty
                    <div class="p-20 text-center text-slate-400 italic text-sm">Belum ada data evaluasi yang masuk.</div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Statistik Prodi Sidebar --}}
        <div class="lg:col-span-4 space-y-6">
            <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-200 p-8">
                <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-6 border-b pb-4">Statistik Per Prodi</h3>
                <div class="space-y-6">
                    @foreach($prodiStats as $ps)
                    <div>
                        <div class="flex justify-between items-end mb-2">
                            <div>
                                <h5 class="text-xs font-black text-[#002855] leading-tight">{{ $ps->nama_prodi }}</h5>
                                <p class="text-[9px] font-bold text-slate-400 uppercase">{{ $ps->jenjang }} &bull; {{ $ps->total_filled }} Respon</p>
                            </div>
                            <span class="text-sm font-black text-indigo-600">{{ number_format($ps->skor, 2) }}</span>
                        </div>
                        <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden">
                            <div class="bg-indigo-500 h-full transition-all duration-1000" style="width: {{ ($ps->skor / 4) * 100 }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
                
                <button class="w-full mt-8 py-3 bg-slate-50 hover:bg-slate-100 text-[10px] font-black text-[#002855] uppercase tracking-widest rounded-2xl transition-all border border-slate-100">
                    Lihat Peta Mutu Lengkap
                </button>
            </div>

            {{-- Info Card --}}
            <div class="bg-amber-50 rounded-[2.5rem] p-8 border border-amber-100 relative overflow-hidden">
                <div class="absolute -right-8 -top-8 w-24 h-24 bg-amber-200/30 rounded-full blur-xl"></div>
                <h4 class="text-xs font-black text-amber-800 uppercase tracking-widest mb-3 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Info Akreditasi
                </h4>
                <p class="text-[11px] text-amber-700/80 leading-relaxed font-medium">
                    Data EDOM di atas merupakan elemen kunci dalam **Kriteria 4 (SDM)** dan **Kriteria 6 (Pendidikan)** pada Instrumen Akreditasi 9 Kriteria.
                </p>
            </div>
        </div>
    </div>
</div>