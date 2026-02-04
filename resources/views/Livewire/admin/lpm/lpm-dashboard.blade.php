<div class="space-y-8 animate-in fade-in duration-700">
    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-[#002855] tracking-tight uppercase">LPM Command Center</h1>
            <div class="flex items-center gap-2 mt-1">
                <span class="bg-[#002855] text-white px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest shadow-sm">
                    Siklus SPMI {{ $currentYear }}
                </span>
                <p class="text-slate-400 font-bold text-xs uppercase tracking-tighter">Universitas Stella Maris Sumba</p>
            </div>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.lpm.ami') }}" class="px-5 py-2.5 bg-white border border-slate-200 rounded-xl font-bold text-xs uppercase tracking-widest text-slate-600 hover:bg-slate-50 transition-all shadow-sm" wire:navigate>
                Monitoring Audit
            </a>
            <button class="px-5 py-2.5 bg-[#002855] text-white rounded-xl font-bold text-xs uppercase tracking-widest shadow-lg hover:bg-black transition-all">
                Laporan Komprehensif
            </button>
        </div>
    </div>

    {{-- Executive KPI Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-[2.5rem] shadow-sm border border-slate-100 group hover:border-[#002855] transition-all relative overflow-hidden">
            <div class="absolute right-0 top-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 20 20"><path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/><path fill-rule="evenodd" d="M4 5a2 2 0 012-2h12a2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/></svg>
            </div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Standar Mutu</p>
            <h3 class="text-3xl font-black text-[#002855] mt-2">{{ $stats['total_standar'] }}</h3>
            <p class="text-[10px] text-indigo-600 font-bold mt-2 italic">Penetapan (P1) Aktif</p>
        </div>

        <div class="bg-white p-6 rounded-[2.5rem] shadow-sm border border-slate-100 group hover:border-rose-500 transition-all">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Temuan AMI (Open)</p>
            <h3 class="text-3xl font-black text-rose-600 mt-2">{{ $stats['temuan_ami'] }}</h3>
            <p class="text-[10px] text-slate-400 font-bold mt-2 uppercase">Perlu Evaluasi & RTL</p>
        </div>

        <div class="bg-[#002855] p-6 rounded-[2.5rem] shadow-lg shadow-indigo-900/20 text-white relative overflow-hidden">
            <div class="absolute -right-6 -bottom-6 bg-white/5 w-32 h-32 rounded-full blur-2xl"></div>
            <p class="text-[10px] font-black text-[#fcc000] uppercase tracking-widest">Pencapaian IKU</p>
            @php 
                $avgIku = $ikuStats->avg('progress');
            @endphp
            <h3 class="text-3xl font-black mt-2">{{ round($avgIku) }}%</h3>
            <div class="mt-4 w-full bg-white/10 rounded-full h-1.5 overflow-hidden">
                <div class="bg-[#fcc000] h-full transition-all duration-1000" style="width: {{ $avgIku }}%"></div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-[2.5rem] shadow-sm border border-slate-100">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Arsip Dokumen</p>
            <h3 class="text-3xl font-black text-slate-800 mt-2">{{ $stats['dokumen_mutu'] }}</h3>
            <div class="mt-3 flex items-center gap-1">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="text-[9px] text-emerald-600 font-black uppercase tracking-tighter">Pusat Data Terpadu</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        {{-- Progress Monitor IKU --}}
        <div class="lg:col-span-8 space-y-6">
            <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-8 py-6 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
                    <div>
                        <h3 class="text-sm font-black text-[#002855] uppercase tracking-widest">Indikator Kinerja Utama (IKU)</h3>
                        <p class="text-[10px] text-slate-400 font-bold mt-0.5">Monitoring Target Tahunan Universitas</p>
                    </div>
                    <a href="{{ route('admin.lpm.iku') }}" class="text-indigo-600 font-black text-[10px] uppercase tracking-widest hover:underline" wire:navigate>Detail IKU</a>
                </div>
                <div class="p-8 space-y-8">
                    @foreach($ikuStats as $iku)
                    <div class="group">
                        <div class="flex justify-between items-end mb-3">
                            <div>
                                <h4 class="text-sm font-black text-slate-700 group-hover:text-[#002855] transition-colors">{{ $iku['label'] }}</h4>
                                <div class="flex items-center gap-3 mt-1">
                                    <span class="text-[10px] text-slate-400 font-bold uppercase tracking-tighter">Target: {{ $iku['target'] }}</span>
                                    <span class="text-[10px] text-slate-400 font-bold uppercase tracking-tighter">Aktual: {{ $iku['actual'] }}</span>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-xl font-black text-{{ $iku['color'] }}-600 tracking-tighter">{{ round($iku['progress']) }}%</span>
                                <span class="text-[8px] font-black text-{{ $iku['color'] }}-600/50 block tracking-widest uppercase">{{ $iku['status'] }}</span>
                            </div>
                        </div>
                        <div class="w-full bg-slate-100 h-2.5 rounded-full overflow-hidden shadow-inner p-0.5">
                            <div class="bg-{{ $iku['color'] }}-500 h-full rounded-full transition-all duration-1000 shadow-sm" style="width: {{ $iku['progress'] }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            
            {{-- AMI Findings Summary --}}
            <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-8 py-5 border-b border-slate-100 bg-slate-50/50">
                    <h3 class="text-xs font-black text-[#002855] uppercase tracking-widest">Peta Temuan Audit (AMI) per Prodi</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100">
                        <thead class="bg-slate-50/50">
                            <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                <th class="px-8 py-4 text-left">Program Studi</th>
                                <th class="px-4 py-4 text-center">Total Temuan</th>
                                <th class="px-4 py-4 text-center">Belum Selesai</th>
                                <th class="px-8 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 bg-white">
                            @forelse($auditSummary as $row)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-8 py-4 text-sm font-black text-slate-800">{{ $row->nama_prodi }}</td>
                                <td class="px-4 py-4 text-center">
                                    <span class="px-2 py-1 bg-slate-100 text-slate-600 rounded-lg text-xs font-bold">{{ $row->total_temuan }}</span>
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <span class="px-2 py-1 {{ $row->open_temuan > 0 ? 'bg-rose-50 text-rose-600' : 'bg-emerald-50 text-emerald-600' }} rounded-lg text-xs font-black">
                                        {{ $row->open_temuan }}
                                    </span>
                                </td>
                                <td class="px-8 py-4 text-right">
                                    <button class="text-indigo-600 font-black text-[10px] uppercase hover:underline">Review RTL</button>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="px-8 py-10 text-center text-slate-400 italic text-xs">Belum ada sesi audit aktif.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Sidebar Section --}}
        <div class="lg:col-span-4 space-y-8">
            {{-- Radar Akreditasi --}}
            <div class="bg-[#002855] rounded-[2.5rem] p-8 text-white shadow-xl relative overflow-hidden">
                <div class="absolute -right-10 -bottom-10 bg-white/5 w-48 h-48 rounded-full blur-3xl"></div>
                <div class="relative z-10">
                    <h3 class="text-sm font-black text-[#fcc000] uppercase tracking-widest mb-6">Radar Akreditasi</h3>
                    <div class="space-y-4">
                        @foreach($auditSummary as $p)
                        <div class="flex items-center justify-between p-4 bg-white/5 rounded-2xl border border-white/10 hover:bg-white/10 transition-all cursor-default">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-white/10 flex items-center justify-center text-[10px] font-black border border-white/5">TI</div>
                                <div>
                                    <p class="text-xs font-black leading-tight">{{ $p->nama_prodi }}</p>
                                    <p class="text-[9px] font-bold text-white/40 uppercase mt-0.5 tracking-tighter">Masa Berlaku: 2029</p>
                                </div>
                            </div>
                            <span class="px-2 py-1 rounded-md bg-emerald-500 text-[8px] font-black uppercase shadow-lg shadow-emerald-500/20">UNGGUL</span>
                        </div>
                        @endforeach
                    </div>
                    <button class="w-full mt-8 py-3 bg-white/10 hover:bg-white/20 border border-white/20 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all">
                        Tampilkan Peta Mutu
                    </button>
                </div>
            </div>

            {{-- Dokumen Mutu Terbaru --}}
            <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-200">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest">Dokumen Terbaru</h3>
                    <a href="{{ route('admin.lpm.dokumen') }}" class="text-[#002855] font-black text-[9px] uppercase tracking-widest" wire:navigate>Semua</a>
                </div>
                <div class="space-y-4">
                    @forelse($latestDocs as $doc)
                    <div class="flex items-center gap-4 group cursor-pointer">
                        <div class="p-3 bg-indigo-50 text-indigo-600 rounded-2xl group-hover:bg-[#002855] group-hover:text-white transition-all duration-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-black text-slate-800 truncate leading-tight group-hover:text-indigo-600">{{ $doc->nama_dokumen }}</p>
                            <p class="text-[9px] text-slate-400 font-bold uppercase mt-1 tracking-tighter">{{ $doc->jenis }} &bull; V{{ $doc->versi }}</p>
                        </div>
                    </div>
                    @empty
                    <p class="text-xs text-slate-400 italic text-center py-4">Belum ada dokumen diunggah.</p>
                    @endforelse
                </div>
            </div>

            {{-- Quick Info LPM --}}
            <div class="bg-indigo-50 rounded-[2.5rem] p-8 border border-indigo-100 relative overflow-hidden">
                <div class="absolute -right-8 -top-8 w-24 h-24 bg-indigo-200/30 rounded-full blur-xl"></div>
                <h4 class="text-xs font-black text-indigo-800 uppercase tracking-widest mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    Siklus PPEPP
                </h4>
                <div class="space-y-3">
                    <div class="flex items-center gap-3">
                        <span class="w-6 h-6 rounded-lg bg-[#002855] text-white flex items-center justify-center text-[10px] font-black">P</span>
                        <span class="text-[10px] font-bold text-slate-600">Penetapan Standar Mutu</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="w-6 h-6 rounded-lg bg-emerald-500 text-white flex items-center justify-center text-[10px] font-black">P</span>
                        <span class="text-[10px] font-bold text-slate-600">Pelaksanaan Standar</span>
                    </div>
                    <div class="flex items-center gap-3 opacity-40">
                        <span class="w-6 h-6 rounded-lg bg-slate-300 text-white flex items-center justify-center text-[10px] font-black">E</span>
                        <span class="text-[10px] font-bold text-slate-600">Evaluasi Pelaksanaan</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>