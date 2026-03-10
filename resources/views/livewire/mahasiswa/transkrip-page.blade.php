<div class="space-y-8 animate-in fade-in duration-700 pb-12">
    {{-- SEO & Header Layout --}}
    <x-slot name="title">Transkrip Nilai &bull; {{ $mahasiswa->nim }}</x-slot>

    {{-- 1. PROFILE HEADER CARD --}}
    <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-200 overflow-hidden relative">
        <div class="absolute top-0 right-0 p-8 opacity-[0.03] pointer-events-none">
            <svg class="w-64 h-64 text-[#002855]" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 14l9-5-9-5-9 5 9 5z" />
                <path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
            </svg>
        </div>
        
        <div class="p-8 lg:p-12 flex flex-col md:flex-row md:items-center justify-between gap-8 relative z-10">
            <div class="flex items-center gap-8">
                <div class="w-24 h-24 bg-[#002855] text-[#fcc000] rounded-[2rem] flex items-center justify-center text-4xl font-black shadow-2xl shadow-blue-900/20 uppercase shrink-0">
                    {{ substr($mahasiswa->person->nama_lengkap, 0, 1) }}
                </div>
                <div class="space-y-2">
                    <h2 class="text-3xl font-black text-[#002855] leading-tight uppercase tracking-tight italic">
                        {{ $mahasiswa->person->nama_lengkap }}
                    </h2>
                    <div class="flex flex-wrap items-center gap-3">
                        <span class="text-xs font-mono font-bold text-slate-500 bg-slate-100 px-3 py-1 rounded-xl border border-slate-200 shadow-sm">{{ $mahasiswa->nim }}</span>
                        <div class="w-1.5 h-1.5 rounded-full bg-slate-200"></div>
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ $mahasiswa->prodi->nama_prodi }}</span>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-4">
                {{-- IPK Main Badge --}}
                <div class="bg-[#002855] px-8 py-4 rounded-[1.8rem] flex flex-col items-center justify-center shadow-xl shadow-blue-900/20 min-w-[160px]">
                    <p class="text-[9px] font-black text-indigo-300 uppercase tracking-[0.2em] mb-1">IP Kumulatif</p>
                    <div class="text-4xl font-black text-[#fcc000] tabular-nums tracking-tighter italic">
                        {{ number_format($ipk, 2) }}
                    </div>
                </div>

                @if($totalSks > 0)
                <a href="{{ route('mhs.cetak.transkrip') }}" target="_blank"
                    class="h-14 flex items-center px-8 bg-white border border-slate-200 text-[#002855] rounded-2xl font-black text-[10px] uppercase tracking-[0.2em] hover:bg-[#002855] hover:text-white hover:-translate-y-1 transition-all shadow-sm group">
                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Cetak Transkrip
                </a>
                @endif
            </div>
        </div>
    </div>

    {{-- 2. QUICK STATS GRID --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-200 flex flex-col justify-center">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2">Total Kredit Lulus</p>
            <h3 class="text-4xl font-black text-[#002855] italic">{{ $totalSks }} <span class="text-xs not-italic text-slate-300 ml-1 font-bold uppercase tracking-widest">SKS</span></h3>
        </div>

        <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-200 flex flex-col justify-center">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2">Total Bobot Mutu</p>
            <h3 class="text-4xl font-black text-slate-800 italic">{{ number_format($totalMutu, 2) }}</h3>
        </div>

        <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-200 flex flex-col justify-center">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2">Masa Studi Aktif</p>
            <h3 class="text-2xl font-black text-emerald-600 uppercase tracking-tighter italic">Semester {{ count($transkripGrouped) }}</h3>
        </div>
    </div>

    {{-- 3. SEMESTER-WISE LISTING --}}
    <div class="space-y-12 pt-4">
        @forelse($transkripGrouped as $semesterName => $mataKuliahs)
        <div class="space-y-4">
            <div class="flex items-center gap-4 px-4">
                <div class="h-px bg-slate-200 flex-1"></div>
                <h3 class="text-[11px] font-black text-[#002855] uppercase tracking-[0.4em] bg-slate-50 px-6 py-2 rounded-full border border-slate-200">
                    {{ $semesterName }}
                </h3>
                <div class="h-px bg-slate-200 flex-1"></div>
            </div>

            <div class="bg-white shadow-sm rounded-[2.5rem] border border-slate-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100">
                        <thead class="bg-slate-50/50">
                            <tr>
                                <th class="px-10 py-6 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Mata Kuliah</th>
                                <th class="px-6 py-6 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest w-24">SKS</th>
                                <th class="px-6 py-6 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest w-24">Grade</th>
                                <th class="px-6 py-6 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest w-24">Indeks</th>
                                <th class="px-10 py-6 text-right text-[10px] font-black text-[#002855] uppercase tracking-widest w-32">Mutu</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach($mataKuliahs as $item)
                            <tr class="hover:bg-slate-50/80 transition-all group">
                                <td class="px-10 py-6">
                                    <div class="flex items-center gap-4">
                                        <span class="px-2 py-1 bg-indigo-50 text-indigo-600 text-[9px] font-black rounded border border-indigo-100 uppercase">{{ $item->mataKuliah->kode_mk ?? '-' }}</span>
                                        <span class="text-sm font-black text-slate-700 uppercase tracking-tight group-hover:text-[#002855] transition-colors leading-tight">
                                            {{ $item->mataKuliah->nama_mk ?? '-' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-6 text-center font-black text-slate-400 text-sm italic">{{ $item->sks_diakui }}</td>
                                <td class="px-6 py-6 text-center">
                                    <span class="text-lg font-black {{ (float)$item->nilai_indeks_final >= 2 ? 'text-emerald-500' : 'text-rose-500' }}">
                                        {{ $item->nilai_huruf_final }}
                                    </span>
                                </td>
                                <td class="px-6 py-6 text-center text-xs font-bold text-slate-400">{{ number_format($item->nilai_indeks_final, 2) }}</td>
                                <td class="px-10 py-6 text-right font-black text-[#002855] text-sm italic bg-slate-50/30">
                                    {{ number_format($item->sks_diakui * $item->nilai_indeks_final, 2) }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-slate-50/50">
                            <tr>
                                <td class="px-10 py-5 text-right text-[9px] font-black text-slate-400 uppercase tracking-widest">Pencapaian Semester:</td>
                                <td class="px-6 py-5 text-center text-sm font-black text-[#002855] italic">{{ $mataKuliahs->sum('sks_diakui') }}</td>
                                <td colspan="2"></td>
                                <td class="px-10 py-5 text-right text-sm font-black text-[#002855] italic">
                                    {{ number_format($mataKuliahs->sum(fn($i) => $i->sks_diakui * $i->nilai_indeks_final), 2) }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        @empty
        <div class="py-32 text-center bg-white rounded-[3rem] border-2 border-dashed border-slate-100">
            <div class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6 shadow-inner text-5xl grayscale opacity-20">📂</div>
            <h3 class="text-sm font-black text-slate-400 uppercase tracking-[0.3em]">Belum Ada Riwayat Akademik</h3>
            <p class="text-slate-300 text-[10px] font-bold mt-2 uppercase tracking-widest">Nilai Anda akan muncul setelah dipublikasikan oleh dosen pengampu.</p>
        </div>
        @endforelse
    </div>

    {{-- 4. FOOTER PORTAL --}}
    <div class="pt-10 flex flex-col items-center gap-2 opacity-20 grayscale pointer-events-none border-t border-slate-100">
        <p class="text-[9px] font-black uppercase tracking-[0.6em] text-[#002855]">OFFICIAL ACADEMIC TRANSCRIPT PORTAL &bull; v4.2 PRO</p>
    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar { height: 4px; width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(0, 40, 85, 0.1); border-radius: 10px; }
    </style>
</div>