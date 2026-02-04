<div class="space-y-8 animate-in fade-in duration-700">
    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-[#002855] tracking-tight">LPM Command Center</h1>
            <p class="text-slate-500 font-medium text-sm mt-1 uppercase tracking-widest">
                Sistem Penjaminan Mutu Internal (SPMI) PPEPP
            </p>
        </div>
        <div class="flex gap-3">
            <button class="px-5 py-2.5 bg-white border border-slate-200 rounded-xl font-bold text-xs uppercase tracking-widest text-slate-600 hover:bg-slate-50 transition-all shadow-sm">
                Unduh Laporan AMI
            </button>
            <button class="px-5 py-2.5 bg-[#002855] text-white rounded-xl font-bold text-xs uppercase tracking-widest shadow-lg hover:bg-black transition-all">
                Siklus Baru (PPEPP)
            </button>
        </div>
    </div>

    {{-- Executive Quick Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 group hover:border-[#002855] transition-all">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Dokumen Mutu</p>
            <h3 class="text-3xl font-black text-[#002855] mt-2">{{ $stats['dokumen_mutu'] }}</h3>
            <p class="text-[10px] text-emerald-600 font-bold mt-2">Pusat Data Terpadu</p>
        </div>
        <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 group hover:border-[#fcc000] transition-all">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Temuan AMI Aktif</p>
            <h3 class="text-3xl font-black text-rose-600 mt-2">{{ $stats['temuan_open'] }}</h3>
            <p class="text-[10px] text-slate-400 font-bold mt-2">Perlu Tindak Lanjut</p>
        </div>
        <div class="bg-[#002855] p-6 rounded-[2rem] shadow-lg shadow-indigo-900/20 text-white relative overflow-hidden">
            <div class="absolute right-0 top-0 p-4 opacity-10">
                <svg class="w-20 h-20" fill="currentColor" viewBox="0 0 20 20"><path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/><path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/></svg>
            </div>
            <p class="text-[10px] font-black text-[#fcc000] uppercase tracking-widest">IKU Tercapai</p>
            <h3 class="text-3xl font-black mt-2">88%</h3>
            <div class="mt-4 w-full bg-white/10 rounded-full h-1.5 overflow-hidden">
                <div class="bg-[#fcc000] h-full" style="width: 88%"></div>
            </div>
        </div>
        <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Kepuasan Mhs (EDOM)</p>
            <h3 class="text-3xl font-black text-slate-800 mt-2">3.82<span class="text-sm font-bold text-slate-400">/4.0</span></h3>
            <p class="text-[10px] text-emerald-600 font-bold mt-2">Kategori: Sangat Baik</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        {{-- Radar IKU/IKT --}}
        <div class="lg:col-span-8 space-y-6">
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-8 py-5 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
                    <h3 class="text-xs font-black text-[#002855] uppercase tracking-widest">Pencapaian Indikator Kinerja Utama (IKU)</h3>
                    <span class="text-[10px] font-bold text-slate-400 italic">Data Real-time SIAKAD</span>
                </div>
                <div class="p-8 space-y-8">
                    @foreach($ikuStats as $iku)
                    <div class="relative">
                        <div class="flex justify-between items-end mb-2">
                            <div>
                                <h4 class="text-sm font-bold text-slate-800">{{ $iku['label'] }}</h4>
                                <p class="text-[10px] text-slate-400 font-bold uppercase">Target: {{ $iku['target'] }}</p>
                            </div>
                            <div class="text-right">
                                <span class="text-lg font-black text-{{ $iku['color'] }}-600">{{ $iku['actual'] }}</span>
                                <span class="text-[10px] font-black text-{{ $iku['color'] }}-600/50 block">{{ $iku['status'] }}</span>
                            </div>
                        </div>
                        <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                            <div class="bg-{{ $iku['color'] }}-500 h-full transition-all duration-1000 shadow-sm" style="width: {{ min(($iku['actual']/$iku['target'])*100, 100) }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Sidebar: Status Akreditasi --}}
        <div class="lg:col-span-4 space-y-6">
            <div class="bg-[#002855] rounded-[2.5rem] p-8 text-white shadow-xl relative overflow-hidden">
                <div class="absolute -right-10 -bottom-10 bg-white/5 w-40 h-40 rounded-full blur-3xl"></div>
                <h3 class="text-sm font-black text-[#fcc000] uppercase tracking-widest mb-6">Radar Akreditasi</h3>
                <div class="space-y-4 relative z-10">
                    @foreach($auditSummary as $p)
                    <div class="flex items-center justify-between p-3 bg-white/5 rounded-2xl border border-white/10 group hover:bg-white/10 transition-all">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center text-xs font-black">{{ $p->jenjang }}</div>
                            <span class="text-xs font-bold">{{ $p->nama_prodi }}</span>
                        </div>
                        <span class="px-2 py-0.5 rounded bg-emerald-500 text-[9px] font-black">UNGGUL</span>
                    </div>
                    @endforeach
                </div>
                <button class="w-full mt-8 py-3 bg-white/10 hover:bg-white/20 border border-white/20 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all">
                    Lihat Peta Mutu Lengkap
                </button>
            </div>

            {{-- Recent Documents --}}
            <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-200">
                <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-4">Dokumen Mutu Terbaru</h3>
                <div class="space-y-3">
                    <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl">
                        <div class="p-2 bg-indigo-100 text-indigo-600 rounded-lg"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"/></svg></div>
                        <div>
                            <p class="text-xs font-bold text-slate-800">RPS Standard v2.0</p>
                            <p class="text-[9px] text-slate-400">Update: 02 Feb 2026</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>