<div class="space-y-6 md:space-y-8 animate-in fade-in duration-700 pb-12">

    {{-- 1. HERO GREETING --}}
    <div class="relative overflow-hidden rounded-[2.5rem] bg-white border border-slate-200 shadow-sm p-6 md:p-10">
        <div class="absolute top-0 right-0 p-10 opacity-[0.03] pointer-events-none">
            <svg class="w-48 h-48 text-[#002855]" fill="currentColor" viewBox="0 0 24 24"><path d="M12 14l9-5-9-5-9 5 9 5z"/><path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
        </div>

        <div class="relative flex flex-col md:flex-row items-center md:items-start justify-between gap-6">
            <div class="flex flex-col md:flex-row items-center gap-6">
                <div class="w-20 h-20 bg-[#002855] text-[#fcc000] rounded-[2rem] flex items-center justify-center text-3xl font-black shadow-2xl shadow-blue-900/20 uppercase shrink-0">
                    {{ substr($user->name, 0, 1) }}
                </div>
                <div class="text-center md:text-left space-y-1">
                    <h1 class="text-2xl md:text-4xl font-black text-[#002855] tracking-tight italic">
                        {{ $greeting }}, <span class="text-slate-400 not-italic font-bold">{{ explode(' ', $user->name)[0] }}</span>
                    </h1>
                    <div class="flex flex-wrap justify-center md:justify-start items-center gap-3 mt-2">
                        <span class="px-3 py-1 bg-indigo-50 text-indigo-600 text-[10px] font-black uppercase tracking-widest rounded-xl border border-indigo-100">{{ $role }}</span>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ $taAktif->nama_tahun ?? 'SIAKAD v4' }}</span>
                    </div>
                </div>
            </div>

            <div class="hidden md:flex flex-col items-end">
                <p class="text-slate-800 font-black text-xl tracking-tighter">{{ Carbon\Carbon::now()->isoFormat('D MMMM Y') }}</p>
                <p class="text-[#fcc000] text-[10px] font-black uppercase tracking-[0.3em]">{{ Carbon\Carbon::now()->isoFormat('dddd') }}</p>
            </div>
        </div>
    </div>

    {{-- 2. ROLE BASED CONTENT --}}

    @if($role === 'mahasiswa')
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        {{-- Alerts --}}
        @if($stats['edom_pending'] > 0 || $stats['finance']['debt'] > 0)
        <div class="lg:col-span-12 grid grid-cols-1 md:grid-cols-2 gap-6">
            @if($stats['edom_pending'] > 0)
            <a href="{{ route('mhs.khs') }}" class="flex items-center gap-5 p-6 bg-amber-50 border border-amber-100 rounded-[2rem] group hover:bg-amber-100 transition-all shadow-sm" wire:navigate>
                <div class="w-12 h-12 bg-amber-200 text-amber-700 rounded-2xl flex items-center justify-center text-xl shrink-0 shadow-inner">📋</div>
                <div class="flex-1">
                    <h4 class="text-xs font-black text-amber-900 uppercase tracking-wider">Evaluasi Dosen (EDOM)</h4>
                    <p class="text-[10px] font-bold text-amber-700/70 mt-1 uppercase">{{ $stats['edom_pending'] }} Mata Kuliah menunggu feedback Anda.</p>
                </div>
                <svg class="w-5 h-5 text-amber-400 group-hover:translate-x-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"/></svg>
            </a>
            @endif
            @if($stats['finance']['debt'] > 0)
            <a href="{{ route('mhs.keuangan') }}" class="flex items-center gap-5 p-6 bg-rose-50 border border-rose-100 rounded-[2rem] group hover:bg-rose-100 transition-all shadow-sm" wire:navigate>
                <div class="w-12 h-12 bg-rose-200 text-rose-700 rounded-2xl flex items-center justify-center text-xl shrink-0 shadow-inner">💳</div>
                <div class="flex-1">
                    <h4 class="text-xs font-black text-rose-900 uppercase tracking-wider">Tunggakan Tagihan</h4>
                    <p class="text-[10px] font-bold text-rose-700/70 mt-1 uppercase">Sisa: Rp {{ number_format($stats['finance']['debt'], 0, ',', '.') }}</p>
                </div>
                <svg class="w-5 h-5 text-rose-400 group-hover:translate-x-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"/></svg>
            </a>
            @endif
        </div>
        @endif

        {{-- Surveys --}}
        @if(count($activeSurveys) > 0)
        <div class="lg:col-span-12 space-y-4">
            <div class="flex items-center gap-2 px-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Survei Fasilitas & Layanan</h3>
            </div>
            <div class="flex gap-4 overflow-x-auto pb-4 custom-scrollbar snap-x">
                @foreach($activeSurveys as $survey)
                <a href="{{ route('sso.siaset.survei', $survey['id']) }}" target="_blank" class="min-w-[320px] bg-white p-6 rounded-[2rem] border border-slate-200 hover:border-[#002855] transition-all group shadow-sm flex flex-col justify-between">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-slate-50 text-2xl flex items-center justify-center rounded-2xl group-hover:bg-[#002855] transition-colors">📊</div>
                        <div>
                            <h4 class="text-xs font-black text-[#002855] uppercase tracking-wide line-clamp-1">{{ $survey['title'] }}</h4>
                            <p class="text-[10px] text-slate-400 mt-1 font-bold">Batas Waktu: {{ Carbon\Carbon::now()->addDays(2)->isoFormat('D MMM') }}</p>
                        </div>
                    </div>
                    <div class="mt-6 pt-4 border-t border-slate-50 text-right">
                        <span class="text-[9px] font-black text-indigo-600 uppercase tracking-widest group-hover:text-[#fcc000]">Mulai Partisipasi &rarr;</span>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Stats Cards --}}
        <div class="lg:col-span-4 space-y-6">
            <div class="bg-white p-8 rounded-[3rem] shadow-sm border border-slate-200 relative overflow-hidden h-full flex flex-col justify-between">
                <div class="relative z-10">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2">Indeks Prestasi Kumulatif</p>
                    <h3 class="text-6xl font-black text-[#002855] tracking-tighter italic">
                        {{ number_format($stats['academic']['ipk'], 2) }}
                    </h3>
                    <div class="flex items-center gap-3 mt-4">
                        <span class="px-3 py-1 bg-emerald-50 text-emerald-600 rounded-lg text-[10px] font-black uppercase tracking-widest">LULUS: {{ $stats['academic']['sks_total'] }} SKS</span>
                    </div>
                </div>
                
                <div class="grid grid-cols-3 gap-3 mt-10">
                    <a href="{{ route('mhs.krs') }}" class="p-4 bg-slate-50 rounded-2xl flex flex-col items-center hover:bg-[#002855] group transition-all" wire:navigate>
                        <span class="text-xl mb-1 group-hover:scale-125 transition-transform">📄</span>
                        <span class="text-[8px] font-black text-slate-500 uppercase group-hover:text-[#fcc000]">KRS</span>
                    </a>
                    <a href="{{ route('mhs.khs') }}" class="p-4 bg-slate-50 rounded-2xl flex flex-col items-center hover:bg-[#002855] group transition-all" wire:navigate>
                        <span class="text-xl mb-1 group-hover:scale-125 transition-transform">📊</span>
                        <span class="text-[8px] font-black text-slate-500 uppercase group-hover:text-[#fcc000]">KHS</span>
                    </a>
                    <a href="{{ route('mhs.transkrip') }}" class="p-4 bg-slate-50 rounded-2xl flex flex-col items-center hover:bg-[#002855] group transition-all" wire:navigate>
                        <span class="text-xl mb-1 group-hover:scale-125 transition-transform">🎓</span>
                        <span class="text-[8px] font-black text-slate-500 uppercase group-hover:text-[#fcc000]">SKPI</span>
                    </a>
                </div>
            </div>
        </div>

        <div class="lg:col-span-8 grid grid-cols-1 md:grid-cols-2 gap-8">
            {{-- Financial --}}
            <div class="bg-white p-8 rounded-[3rem] shadow-sm border border-slate-200 flex flex-col">
                <div class="flex justify-between items-start mb-8">
                    <div>
                        <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Kesehatan Finansial</h4>
                        <p class="text-2xl font-black text-[#002855] mt-1 italic">Rp {{ number_format($stats['finance']['total_paid'], 0, ',', '.') }}</p>
                    </div>
                    <div class="px-3 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-widest shadow-sm {{ $stats['finance']['status_smt'] == 'LUNAS' ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : 'bg-rose-50 text-rose-600 border border-rose-100' }}">
                        {{ $stats['finance']['status_smt'] }}
                    </div>
                </div>
                
                <div class="space-y-4 flex-1">
                    <div class="w-full bg-slate-100 h-2.5 rounded-full overflow-hidden">
                        @php $pct = $stats['finance']['total_bill'] > 0 ? ($stats['finance']['total_paid'] / $stats['finance']['total_bill'] * 100) : 100; @endphp
                        <div class="bg-[#002855] h-full transition-all duration-1000 ease-out" style="width: {{ min($pct, 100) }}%"></div>
                    </div>
                    <div class="flex justify-between text-[9px] font-black uppercase tracking-widest">
                        <span class="text-slate-400">Total Tagihan:</span>
                        <span class="text-slate-800">Rp {{ number_format($stats['finance']['total_bill'], 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-[9px] font-black uppercase tracking-widest bg-rose-50 p-3 rounded-xl border border-rose-100">
                        <span class="text-rose-400 italic">Sisa Kewajiban:</span>
                        <span class="text-rose-600">Rp {{ number_format($stats['finance']['debt'], 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            {{-- Schedule --}}
            <div class="bg-white rounded-[3rem] shadow-sm border border-slate-200 overflow-hidden flex flex-col">
                <div class="px-8 py-6 bg-slate-50/50 border-b border-slate-100 flex justify-between items-center">
                    <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                        <div class="w-1.5 h-1.5 bg-indigo-500 rounded-full animate-pulse"></div>
                        Agenda Perkuliahan Hari Ini
                    </h4>
                </div>
                <div class="flex-1 overflow-y-auto max-h-[250px] custom-scrollbar divide-y divide-slate-50">
                    @forelse($scheduleToday as $item)
                    <div class="px-8 py-5 flex items-start gap-6 hover:bg-slate-50 transition-colors group">
                        <div class="text-center w-12 shrink-0">
                            <p class="text-xs font-black text-[#002855] leading-none group-hover:scale-110 transition-transform">{{ \Carbon\Carbon::parse($item->jadwalKuliah->jam_mulai)->format('H:i') }}</p>
                            <p class="text-[8px] font-bold text-slate-300 uppercase mt-1">WITA</p>
                        </div>
                        <div class="flex-1 border-l-2 border-indigo-100 pl-6">
                            <h5 class="text-xs font-black text-slate-800 uppercase tracking-tight leading-tight line-clamp-1">{{ $item->jadwalKuliah->mataKuliah->nama_mk }}</h5>
                            <div class="flex items-center gap-3 mt-2">
                                <span class="text-[9px] font-black text-indigo-400 uppercase tracking-tighter">R.{{ $item->jadwalKuliah->ruang->kode_ruang ?? 'TBA' }}</span>
                                <span class="text-[9px] font-bold text-slate-300 uppercase border border-slate-200 px-1.5 rounded-lg">KLS {{ $item->jadwalKuliah->nama_kelas }}</span>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="py-20 text-center space-y-3">
                        <span class="text-3xl grayscale opacity-30 block">☕</span>
                        <p class="text-[10px] font-black text-slate-300 uppercase tracking-widest italic">Tidak ada kelas hari ini</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    @elseif($role === 'dosen')
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        {{-- Lecturer Stats --}}
        <div class="lg:col-span-4 space-y-6">
            <div class="bg-white p-8 rounded-[3rem] shadow-sm border border-slate-200">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-6">Kinerja Pengajaran</p>
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-indigo-50 p-8 rounded-[2rem] border border-indigo-100 text-center group hover:-translate-y-1 transition-all">
                        <p class="text-4xl font-black text-[#002855] tracking-tighter group-hover:scale-110 transition-transform italic">{{ $stats['teaching']['total_kelas'] }}</p>
                        <p class="text-[9px] font-black text-indigo-400 uppercase mt-2 tracking-widest">Kelas</p>
                    </div>
                    <div class="bg-slate-50 p-8 rounded-[2rem] border border-slate-100 text-center group hover:-translate-y-1 transition-all">
                        <p class="text-4xl font-black text-slate-700 tracking-tighter italic">{{ $stats['mentorship']['total_anak_wali'] }}</p>
                        <p class="text-[9px] font-black text-slate-400 uppercase mt-2 tracking-widest">Wali</p>
                    </div>
                </div>
            </div>

            @if($stats['mentorship']['krs_pending'] > 0)
            <a href="{{ route('dosen.perwalian') }}" class="block bg-amber-50 p-8 rounded-[3rem] border border-amber-200 group transition-all shadow-xl shadow-amber-900/5" wire:navigate>
                <div class="flex items-center gap-6">
                    <div class="h-14 w-14 bg-white rounded-2xl flex items-center justify-center text-3xl shadow-sm group-hover:rotate-12 transition-transform shadow-amber-200">⏳</div>
                    <div class="flex-1">
                        <h4 class="text-sm font-black text-amber-900 uppercase tracking-widest">Verifikasi KRS</h4>
                        <p class="text-[11px] font-bold text-amber-700 mt-1 uppercase">{{ $stats['mentorship']['krs_pending'] }} Mahasiswa sedang menunggu persetujuan Anda.</p>
                    </div>
                </div>
            </a>
            @endif
        </div>

        {{-- Lecturer Schedule --}}
        <div class="lg:col-span-8 bg-white rounded-[3rem] shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-10 py-6 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
                <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em]">Agenda Mengajar Hari Ini</h3>
                <span class="px-3 py-1 bg-white border border-slate-200 rounded-xl text-[9px] font-black text-[#002855] uppercase tracking-tighter">{{ count($scheduleToday) }} Sesi</span>
            </div>
            <div class="divide-y divide-slate-50">
                @forelse($scheduleToday as $j)
                <div class="px-10 py-6 flex items-center justify-between hover:bg-slate-50 transition-all group">
                    <div class="flex items-center gap-8">
                        <div class="text-center w-14">
                            <p class="text-lg font-black text-[#002855] italic">{{ \Carbon\Carbon::parse($j->jam_mulai)->format('H:i') }}</p>
                            <p class="text-[8px] font-black text-slate-300 uppercase tracking-widest">WITA</p>
                        </div>
                        <div class="border-l-2 border-indigo-100 pl-8 space-y-1">
                            <h4 class="text-sm font-black text-slate-800 leading-tight uppercase tracking-tight group-hover:text-indigo-600 transition-colors">{{ $j->mataKuliah->nama_mk }}</h4>
                            <div class="flex items-center gap-3">
                                <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Ruang {{ $j->ruang->kode_ruang ?? 'TBA' }}</span>
                                <span class="w-1 h-1 bg-slate-200 rounded-full"></span>
                                <span class="text-[9px] font-black text-indigo-400 uppercase tracking-widest">Kelas {{ $j->nama_kelas }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col gap-2">
                        <a href="{{ route('dosen.nilai', $j->id) }}" class="px-6 py-2.5 bg-[#002855] text-white hover:bg-indigo-600 rounded-2xl text-[9px] font-black uppercase tracking-[0.2em] transition-all shadow-lg shadow-blue-900/10" wire:navigate>Input Nilai</a>
                        @if($j->dosens->count() > 1)
                        <div class="flex items-center gap-1 justify-end">
                            <span class="text-[8px] font-black text-amber-500 uppercase tracking-widest">Team Teaching</span>
                        </div>
                        @endif
                    </div>
                </div>
                @empty
                <div class="py-24 text-center space-y-4">
                    <span class="text-4xl grayscale opacity-20 block">📅</span>
                    <p class="text-[10px] font-black text-slate-300 uppercase tracking-widest italic">Tidak ada agenda mengajar hari ini</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
    @endif

    {{-- FOOTER VERSION --}}
    <div class="pt-10 flex flex-col items-center gap-2 opacity-30 grayscale pointer-events-none border-t border-slate-100">
        <p class="text-[9px] font-black uppercase tracking-[0.6em] text-[#002855]">UNMARIS Enterprise Digital Environment &bull; v4.2 PRO</p>
    </div>

    {{-- Floating Notifications --}}
    @if(count($notifications) > 0)
    <div x-data="{ show: true }" x-show="show" x-transition class="fixed bottom-8 right-8 z-50 flex flex-col gap-4">
        @foreach($notifications as $notif)
        <div class="bg-[#002855] text-white px-6 py-5 rounded-[2rem] shadow-[0_20px_50px_rgba(0,40,85,0.3)] flex items-start gap-5 border border-indigo-400/20 max-w-sm w-[90vw] sm:w-auto animate-in slide-in-from-bottom-5">
            <div class="w-12 h-12 bg-[#fcc000] text-[#002855] rounded-2xl flex items-center justify-center text-xl shrink-0 shadow-xl shadow-amber-500/20">🔔</div>
            <div class="flex-1 space-y-1">
                <h4 class="text-[11px] font-black text-[#fcc000] uppercase tracking-[0.1em]">{{ $notif['title'] }}</h4>
                <p class="text-[10px] text-indigo-100 leading-relaxed font-bold">{{ $notif['message'] }}</p>
            </div>
            <button @click="show = false" class="text-indigo-300 hover:text-white transition-colors mt-1"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        @endforeach
    </div>
    @endif

    <style>
        .custom-scrollbar::-webkit-scrollbar { height: 4px; width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(0, 40, 85, 0.1); border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(0, 40, 85, 0.2); }
    </style>
</div>