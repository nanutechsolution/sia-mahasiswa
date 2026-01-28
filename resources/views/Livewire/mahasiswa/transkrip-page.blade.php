<div>
    {{-- SEO & Header Layout --}}
    <x-slot name="title">Transkrip Nilai - UNMARIS</x-slot>
    <x-slot name="header">Transkrip Nilai Akademik</x-slot>

    <div class="space-y-8">
        {{-- Profile & Cumulative Summary Card --}}
        <div class="bg-white rounded-[2.5rem] shadow-xl border border-slate-200 overflow-hidden animate-in fade-in duration-500">
            <div class="p-8 lg:p-12 flex flex-col lg:flex-row lg:items-center justify-between gap-8">
                <div class="flex items-center space-x-6">
                    <div class="w-20 h-20 bg-unmaris-blue text-unmaris-yellow rounded-[2rem] flex items-center justify-center font-black text-3xl shadow-lg ring-8 ring-slate-50">
                        {{ substr($mahasiswa->nama_lengkap, 0, 1) }}
                    </div>
                    <div>
                        <h2 class="text-2xl font-black text-slate-900 leading-tight uppercase tracking-tight">{{ $mahasiswa->nama_lengkap }}</h2>
                        <div class="flex flex-wrap items-center gap-3 mt-2">
                            <span class="text-xs font-mono font-bold text-indigo-600 bg-indigo-50 px-3 py-1 rounded-xl border border-indigo-100">{{ $mahasiswa->nim }}</span>
                            <span class="text-xs text-slate-400 font-bold uppercase tracking-widest">• {{ $mahasiswa->prodi->nama_prodi }} ({{ $mahasiswa->prodi->jenjang }})</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-6 bg-slate-50 p-6 rounded-[2rem] border border-slate-100 shadow-inner">
                    <div class="text-right border-r border-slate-200 pr-6">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-2">Total SKS Lulus</p>
                        <p class="text-2xl font-black text-slate-700">{{ $totalSks }} <span class="text-xs text-slate-400">Kredit</span></p>
                    </div>
                    <div class="text-center px-4">
                        <p class="text-[10px] font-black text-unmaris-blue uppercase tracking-widest leading-none mb-2">IPK Kumulatif</p>
                        <div class="text-5xl font-black text-unmaris-blue tabular-nums tracking-tighter">
                            {{ number_format($ipk, 2) }}
                        </div>
                    </div>
                    <div class="pl-2">
                        <a href="{{ route('mhs.cetak.transkrip') }}" target="_blank"
                            class="flex items-center justify-center w-14 h-14 bg-white border border-slate-200 text-slate-600 rounded-2xl hover:bg-unmaris-blue hover:text-white transition-all shadow-sm group">
                            <svg class="w-6 h-6 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Semester Iteration --}}
        <div class="space-y-10">
            @forelse($transkripGrouped as $semesterName => $mataKuliahs)
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden group hover:border-unmaris-blue/30 transition-all">
                <div class="px-8 py-5 border-b border-slate-100 bg-slate-50/50 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-2 h-6 bg-unmaris-yellow rounded-full"></div>
                        <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest">{{ $semesterName }}</h3>
                    </div>
                    <div class="flex items-center gap-4">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">SKS Smt: <span class="text-slate-700">{{ $mataKuliahs->sum('sks_default') }}</span></span>
                        <span class="w-1 h-1 bg-slate-300 rounded-full"></span>
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">IPS: <span class="text-indigo-600">{{ number_format($mataKuliahs->avg('nilai_indeks'), 2) }}</span></span>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-white text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] border-b border-slate-50">
                                <th class="px-8 py-4 w-32">Kode MK</th>
                                <th class="px-8 py-4">Mata Kuliah</th>
                                <th class="px-8 py-4 text-center">SKS</th>
                                <th class="px-8 py-4 text-center">Nilai</th>
                                <th class="px-8 py-4 text-center">Bobot</th>
                                <th class="px-8 py-4 text-center">Mutu</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach($mataKuliahs as $mk)
                            <tr class="hover:bg-slate-50/30 transition-colors">
                                <td class="px-8 py-4 font-mono text-[11px] font-bold text-slate-400 uppercase tracking-tighter italic">
                                    {{ $mk->kode_mk }}
                                </td>
                                <td class="px-8 py-4">
                                    <div class="text-[13px] font-bold text-slate-700 leading-tight uppercase">
                                        {{ $mk->nama_mk }}
                                    </div>
                                </td>
                                <td class="px-8 py-4 text-center text-sm font-bold text-slate-500 tabular-nums">
                                    {{ $mk->sks_default }}
                                </td>
                                <td class="px-8 py-4 text-center">
                                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-black border
                                    @switch($mk->nilai_huruf)
                                        @case('A') bg-emerald-50 text-emerald-600 border-emerald-100 @break
                                        @case('B') bg-blue-50 text-blue-600 border-blue-100 @break
                                        @case('C') bg-amber-50 text-amber-600 border-amber-100 @break
                                        @case('E') bg-rose-50 text-rose-600 border-rose-100 @break
                                        @default bg-slate-50 text-slate-400 border-slate-100
                                    @endswitch">
                                        {{ $mk->nilai_huruf }}
                                    </span>
                                </td>
                                <td class="px-8 py-4 text-center text-sm font-bold text-slate-400 tabular-nums">
                                    {{ number_format($mk->nilai_indeks, 2) }}
                                </td>
                                <td class="px-8 py-4 text-center text-sm font-black text-slate-800 tabular-nums">
                                    {{ number_format($mk->sks_default * $mk->nilai_indeks, 2) }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @empty
            <div class="bg-white rounded-[3rem] shadow-sm border border-slate-200 border-dashed py-24 text-center">
                <div class="max-w-xs mx-auto">
                    <div class="w-20 h-20 bg-slate-50 rounded-3xl flex items-center justify-center mx-auto mb-6">
                        <svg class="w-10 h-10 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest">Belum Ada Rekaman Nilai</h3>
                    <p class="mt-2 text-xs font-medium text-slate-400 leading-relaxed uppercase tracking-tighter">Transkrip kumulatif akan muncul secara otomatis setelah nilai semester dipublikasikan.</p>
                </div>
            </div>
            @endforelse
        </div>
    </div>


</div>