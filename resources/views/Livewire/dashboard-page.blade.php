<div class="space-y-6 md:space-y-8 animate-in fade-in duration-700 pb-12">
    
    {{-- 1. CLEAN HERO GREETING --}}
    <div class="relative overflow-hidden rounded-3xl bg-white border border-slate-200 shadow-sm p-6 md:p-10">
        <div class="absolute top-0 right-0 p-8 opacity-[0.03] pointer-events-none">
            <svg class="w-40 h-40 text-[#002855]" fill="currentColor" viewBox="0 0 24 24"><path d="M12 14l9-5-9-5-9 5 9 5z"/><path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
        </div>
        
        <div class="relative flex flex-col md:flex-row items-center md:items-start justify-between gap-6">
            <div class="flex flex-col md:flex-row items-center gap-5">
                <div class="w-16 h-16 md:w-20 md:h-20 bg-[#002855] text-[#fcc000] rounded-2xl flex items-center justify-center text-2xl md:text-3xl font-black shadow-lg uppercase shrink-0">
                    {{ substr($user->name, 0, 1) }}
                </div>
                <div class="text-center md:text-left">
                    <h1 class="text-2xl md:text-3xl font-black text-[#002855] tracking-tight">
                        {{ $greeting }}, <span class="text-slate-500 font-bold">{{ explode(' ', $user->name)[0] }}</span>
                    </h1>
                    <div class="flex flex-wrap justify-center md:justify-start items-center gap-2 mt-1">
                        <span class="text-[10px] font-black bg-indigo-50 text-indigo-600 px-2 py-0.5 rounded-lg border border-indigo-100 uppercase tracking-widest">{{ $role }}</span>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">{{ $taAktif->nama_tahun ?? 'SIAKAD UNMARIS' }}</span>
                    </div>
                </div>
            </div>
            
            <div class="hidden md:flex flex-col items-end">
                <p class="text-slate-800 font-black text-lg">{{ Carbon\Carbon::now()->isoFormat('D MMMM Y') }}</p>
                <p class="text-slate-400 text-[10px] font-black uppercase tracking-[0.2em]">{{ Carbon\Carbon::now()->isoFormat('dddd') }}</p>
            </div>
        </div>
    </div>

    {{-- 2. DYNAMIC CONTENT BASED ON ROLE --}}
    
    @if($role === 'mahasiswa')
        {{-- MAHASISWA MINIMALIST VIEW --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 md:gap-8">
            
            {{-- Alerts & Warnings (Top Priority for Student) --}}
            @if($stats['edom_pending'] > 0 || $stats['finance']['debt'] > 0)
            <div class="lg:col-span-12 grid grid-cols-1 md:grid-cols-2 gap-4 animate-in slide-in-from-top-4">
                @if($stats['edom_pending'] > 0)
                <a href="{{ route('mhs.khs') }}" class="flex items-center gap-4 p-4 bg-amber-50 border border-amber-100 rounded-2xl group hover:bg-amber-100 transition-all" wire:navigate>
                    <div class="w-10 h-10 bg-amber-200 text-amber-700 rounded-xl flex items-center justify-center shrink-0">⚖️</div>
                    <div class="flex-1">
                        <h4 class="text-xs font-black text-amber-900 uppercase">Evaluasi Dosen (EDOM)</h4>
                        <p class="text-[10px] font-bold text-amber-700/70">{{ $stats['edom_pending'] }} Mata Kuliah menunggu evaluasi Anda.</p>
                    </div>
                    <svg class="w-4 h-4 text-amber-400 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                </a>
                @endif
                @if($stats['finance']['debt'] > 0)
                <a href="{{ route('mhs.keuangan') }}" class="flex items-center gap-4 p-4 bg-rose-50 border border-rose-100 rounded-2xl group hover:bg-rose-100 transition-all" wire:navigate>
                    <div class="w-10 h-10 bg-rose-200 text-rose-700 rounded-xl flex items-center justify-center shrink-0">💳</div>
                    <div class="flex-1">
                        <h4 class="text-xs font-black text-rose-900 uppercase">Tunggakan Pembayaran</h4>
                        <p class="text-[10px] font-bold text-rose-700/70">Terdapat sisa tagihan sebesar Rp {{ number_format($stats['finance']['debt'], 0, ',', '.') }}.</p>
                    </div>
                    <svg class="w-4 h-4 text-rose-400 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                </a>
                @endif
            </div>
            @endif

            {{-- Academic Stats Card --}}
            <div class="lg:col-span-4 space-y-6">
                <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-200 flex flex-col justify-between h-full">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Capaian Akademik</p>
                            <h3 class="text-4xl font-black text-[#002855] tracking-tighter">{{ number_format($stats['academic']['ipk'], 2) }} <span class="text-xs text-slate-300 font-bold uppercase">IPK</span></h3>
                        </div>
                        <div class="p-3 bg-indigo-50 text-indigo-600 rounded-2xl font-black text-sm">
                            {{ $stats['academic']['sks_total'] }} <span class="text-[10px] opacity-50">SKS</span>
                        </div>
                    </div>
                    
                    <div class="mt-8 grid grid-cols-3 gap-2">
                        <a href="{{ route('mhs.krs') }}" class="flex flex-col items-center p-3 rounded-2xl hover:bg-slate-50 transition-all border border-transparent hover:border-slate-100 group" wire:navigate>
                            <span class="text-xl mb-1 group-hover:scale-110 transition-transform">📝</span>
                            <span class="text-[9px] font-black text-slate-500 uppercase">KRS</span>
                        </a>
                        <a href="{{ route('mhs.khs') }}" class="flex flex-col items-center p-3 rounded-2xl hover:bg-slate-50 transition-all border border-transparent hover:border-slate-100 group" wire:navigate>
                            <span class="text-xl mb-1 group-hover:scale-110 transition-transform">📊</span>
                            <span class="text-[9px] font-black text-slate-500 uppercase">Nilai</span>
                        </a>
                        <a href="{{ route('mhs.transkrip') }}" class="flex flex-col items-center p-3 rounded-2xl hover:bg-slate-50 transition-all border border-transparent hover:border-slate-100 group" wire:navigate>
                            <span class="text-xl mb-1 group-hover:scale-110 transition-transform">📜</span>
                            <span class="text-[9px] font-black text-slate-500 uppercase">Log</span>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Finance & Schedule Grid --}}
            <div class="lg:col-span-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Financial Overview --}}
                <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-200 flex flex-col">
                    <div class="flex justify-between items-center mb-6">
                        <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Realisasi Keuangan</h4>
                        <span class="px-2 py-0.5 rounded-lg text-[9px] font-black uppercase {{ $stats['finance']['status_smt'] == 'LUNAS' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                            {{ $stats['finance']['status_smt'] }}
                        </span>
                    </div>
                    <div class="flex-1 space-y-4">
                        <div class="flex justify-baseline items-baseline gap-2">
                            <h3 class="text-2xl font-black text-[#002855]">Rp {{ number_format($stats['finance']['total_paid'], 0, ',', '.') }}</h3>
                            <span class="text-[10px] font-bold text-slate-300 uppercase">Terbayar</span>
                        </div>
                        <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden">
                            @php $pct = $stats['finance']['total_bill'] > 0 ? ($stats['finance']['total_paid'] / $stats['finance']['total_bill'] * 100) : 100; @endphp
                            <div class="bg-indigo-600 h-full transition-all duration-1000" style="width: {{ min($pct, 100) }}%"></div>
                        </div>
                        <div class="flex justify-between text-[10px] font-bold">
                            <span class="text-slate-400 uppercase">Sisa Tunggakan:</span>
                            <span class="text-rose-600 font-black">Rp {{ number_format($stats['finance']['debt'], 0, ',', '.') }}</span>
                        </div>
                    </div>
                    <a href="{{ route('mhs.keuangan') }}" class="mt-6 w-full py-3 bg-slate-50 hover:bg-indigo-50 text-[#002855] rounded-xl text-[9px] font-black text-center uppercase tracking-widest transition-all" wire:navigate>
                        Manajemen Tagihan &rarr;
                    </a>
                </div>

                {{-- Today's Schedule --}}
                <div class="bg-white rounded-[2rem] shadow-sm border border-slate-200 overflow-hidden flex flex-col">
                    <div class="px-6 py-4 bg-slate-50/50 border-b border-slate-100 flex justify-between items-center">
                        <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Jadwal Hari Ini</h4>
                        <span class="text-[9px] font-black text-indigo-600 uppercase">{{ count($scheduleToday) }} Sesi</span>
                    </div>
                    <div class="flex-1 overflow-y-auto max-h-[180px] custom-scrollbar divide-y divide-slate-50">
                        @forelse($scheduleToday as $item)
                        <div class="px-6 py-3 flex gap-4 hover:bg-slate-50 transition-colors">
                            <div class="text-center w-10 shrink-0">
                                <p class="text-xs font-black text-slate-700 leading-none">{{ \Carbon\Carbon::parse($item->jadwalKuliah->jam_mulai)->format('H:i') }}</p>
                            </div>
                            <div class="flex-1 border-l-2 border-indigo-100 pl-3">
                                <h5 class="text-[11px] font-black text-slate-800 leading-tight truncate uppercase">{{ $item->jadwalKuliah->mataKuliah->nama_mk }}</h5>
                                <p class="text-[9px] text-slate-400 font-bold uppercase mt-0.5 tracking-tighter">R.{{ $item->jadwalKuliah->ruang }}</p>
                            </div>
                        </div>
                        @empty
                        <div class="py-10 text-center">
                            <p class="text-[10px] font-bold text-slate-300 uppercase tracking-widest">Tidak ada kelas</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

    @elseif($role === 'dosen')
        {{-- DOSEN MINIMALIST VIEW --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 md:gap-8">
            <div class="lg:col-span-4 space-y-6">
                <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-200">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-6">Indikator Kinerja</p>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-indigo-50 p-6 rounded-3xl border border-indigo-100 text-center group">
                            <p class="text-3xl font-black text-[#002855] group-hover:scale-110 transition-transform">{{ $stats['teaching']['total_kelas'] }}</p>
                            <p class="text-[9px] font-black text-indigo-400 uppercase mt-1">Kelas Aktif</p>
                        </div>
                        <div class="bg-slate-50 p-6 rounded-3xl border border-slate-100 text-center">
                            <p class="text-3xl font-black text-slate-700">{{ $stats['mentorship']['total_anak_wali'] }}</p>
                            <p class="text-[9px] font-black text-slate-400 uppercase mt-1">Mhs Wali</p>
                        </div>
                    </div>
                </div>

                @if($stats['mentorship']['krs_pending'] > 0)
                <a href="{{ route('dosen.perwalian') }}" class="block bg-amber-50 p-6 rounded-[2.5rem] border border-amber-200 group transition-all" wire:navigate>
                    <div class="flex items-center gap-4">
                        <div class="h-10 w-10 bg-white rounded-xl flex items-center justify-center text-xl shadow-sm group-hover:rotate-12 transition-transform">⏳</div>
                        <div>
                            <h4 class="text-xs font-black text-amber-900 uppercase">Persetujuan KRS</h4>
                            <p class="text-[10px] font-bold text-amber-700 mt-0.5">{{ $stats['mentorship']['krs_pending'] }} Mhs menunggu ACC</p>
                        </div>
                    </div>
                </a>
                @endif
            </div>

            <div class="lg:col-span-8 bg-white rounded-[2.5rem] shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-8 py-5 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
                    <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Jadwal Mengajar Hari Ini</h3>
                </div>
                <div class="divide-y divide-slate-50">
                    @forelse($scheduleToday as $j)
                    <div class="px-8 py-5 flex items-center justify-between hover:bg-slate-50 transition-colors">
                        <div class="flex items-center gap-6">
                            <div class="text-center w-12">
                                <p class="text-sm font-black text-[#002855]">{{ \Carbon\Carbon::parse($j->jam_mulai)->format('H:i') }}</p>
                            </div>
                            <div class="border-l-2 border-indigo-100 pl-6">
                                <h4 class="text-sm font-black text-slate-800 leading-tight uppercase">{{ $j->mataKuliah->nama_mk }}</h4>
                                <p class="text-[9px] font-bold text-slate-400 mt-1 uppercase">Kls {{ $j->nama_kelas }} &bull; R.{{ $j->ruang }}</p>
                            </div>
                        </div>
                        <a href="{{ route('dosen.nilai', $j->id) }}" class="px-5 py-2 bg-indigo-50 text-indigo-700 hover:bg-[#002855] hover:text-white rounded-xl text-[9px] font-black uppercase tracking-widest transition-all" wire:navigate>Input Nilai</a>
                    </div>
                    @empty
                    <div class="py-20 text-center">
                        <p class="text-[10px] font-bold text-slate-300 uppercase tracking-widest italic">Tidak ada jadwal hari ini</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

    @else
        {{-- ADMIN MINIMALIST VIEW --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-200 group">
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Mhs Aktif</p>
                <h3 class="text-4xl font-black text-[#002855] tracking-tighter">{{ $stats['system']['mhs_aktif'] }}</h3>
                <div class="mt-4 h-1 w-8 bg-[#fcc000] rounded-full group-hover:w-full transition-all duration-500"></div>
            </div>
            
            <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-200">
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Verifikasi Bayar</p>
                <h3 class="text-4xl font-black {{ $stats['system']['pembayaran_pending'] > 0 ? 'text-rose-500' : 'text-slate-700' }} tracking-tighter">{{ $stats['system']['pembayaran_pending'] }}</h3>
                <p class="text-[9px] font-bold text-slate-300 uppercase mt-2">Menunggu Persetujuan</p>
            </div>

            <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-200">
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Persetujuan KRS</p>
                <h3 class="text-4xl font-black text-[#002855] tracking-tighter">{{ $stats['system']['krs_diajukan'] }}</h3>
                <p class="text-[9px] font-bold text-slate-300 uppercase mt-2">Dalam Review PA</p>
            </div>

            <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-200">
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Progres Nilai</p>
                <h3 class="text-4xl font-black text-[#002855] tracking-tighter">{{ $stats['system']['nilai_unpublished'] }}</h3>
                <p class="text-[9px] font-bold text-slate-300 uppercase mt-2">Belum Dipublikasi</p>
            </div>
        </div>

        <div class="bg-indigo-900 rounded-[2.5rem] p-10 text-white shadow-xl relative overflow-hidden">
            <div class="absolute right-0 top-0 p-8 opacity-10"><svg class="w-48 h-48" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg></div>
            <div class="relative z-10 max-w-lg">
                <h2 class="text-2xl font-black uppercase tracking-tight">Quality Assurance Audit</h2>
                <p class="mt-4 text-indigo-200 text-sm leading-relaxed font-medium">Lakukan pemantauan berkala terhadap siklus akademik untuk menjamin ketersediaan data akreditasi yang valid.</p>
                <div class="mt-8 flex gap-3 flex-wrap">
                    <a href="{{ route('admin.lpm.dashboard') }}" class="px-8 py-3 bg-[#fcc000] text-[#002855] rounded-xl font-black text-[10px] uppercase tracking-widest hover:scale-105 transition-all shadow-lg" wire:navigate>LPM Radar</a>
                    <button class="px-8 py-3 bg-white/10 border border-white/20 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-white/20 transition-all">Export Data Audit</button>
                </div>
            </div>
        </div>
    @endif

    {{-- 3. MINIMAL FOOTER --}}
    <div class="pt-8 flex flex-col items-center gap-2 opacity-30 grayscale pointer-events-none">
        <p class="text-[9px] font-black uppercase tracking-[0.4em] text-[#002855]">UNMARIS Digital Portal &bull; v4.2</p>
    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(0, 40, 85, 0.05); border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(0, 40, 85, 0.1); }
    </style>
</div>