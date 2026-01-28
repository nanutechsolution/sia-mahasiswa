<div>
    {{-- SEO & Header Layout --}}
    <x-slot name="title">KHS - UNMARIS</x-slot>
    <x-slot name="header">Kartu Hasil Studi (KHS)</x-slot>

    <div class="space-y-8">
        {{-- Profile Header Card --}}
        <div class="bg-white rounded-3xl shadow-xl border border-slate-200 overflow-hidden animate-in fade-in duration-500">
            <div class="p-6 lg:p-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="flex items-center space-x-5">
                    <div class="w-16 h-16 bg-unmaris-blue text-unmaris-yellow rounded-2xl flex items-center justify-center font-black text-2xl shadow-lg ring-4 ring-slate-50">
                        {{ substr($mahasiswa->nama_lengkap, 0, 1) }}
                    </div>
                    <div>
                        <h2 class="text-xl font-black text-slate-900 leading-tight uppercase tracking-tight">{{ $mahasiswa->nama_lengkap }}</h2>
                        <div class="flex flex-wrap items-center gap-2 mt-1.5">
                            <span class="text-xs font-mono font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-lg border border-indigo-100">{{ $mahasiswa->nim }}</span>
                            <span class="text-xs text-slate-400 font-bold uppercase tracking-widest">• {{ $mahasiswa->prodi->nama_prodi }}</span>
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-4">
                    <div class="bg-slate-50 border border-slate-100 px-6 py-3 rounded-2xl flex items-center shadow-inner">
                        <div class="text-right">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Periode Aktif</p>
                            <p class="text-xs font-bold text-unmaris-blue uppercase tracking-tighter">Semester Ganjil 2024/2025</p>
                        </div>
                    </div>
                    <button onclick="window.print()" class="p-4 bg-white border border-slate-200 text-slate-600 rounded-2xl hover:bg-slate-50 hover:text-unmaris-blue transition-all shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- Grade Table Section --}}
        <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-100 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                            <th class="px-8 py-5">Kode MK</th>
                            <th class="px-8 py-5">Mata Kuliah</th>
                            <th class="px-8 py-5 text-center">SKS</th>
                            <th class="px-8 py-5 text-center">Nilai Huruf</th>
                            <th class="px-8 py-5 text-center">Indeks</th>
                            <th class="px-8 py-5 text-center">SKS x Bobot</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($details as $mk)
                        <tr class="hover:bg-slate-50/50 transition-colors group">
                            <td class="px-8 py-5 font-mono text-[11px] font-bold text-indigo-500 uppercase tracking-tighter">
                                {{ $mk->jadwalKuliah->mataKuliah->kode_mk }}
                            </td>
                            <td class="px-8 py-5">
                                <div class="text-sm font-bold text-slate-800 leading-tight uppercase group-hover:text-unmaris-blue transition-colors">
                                    {{ $mk->jadwalKuliah->mataKuliah->nama_mk }}
                                </div>
                            </td>
                            <td class="px-8 py-5 text-center text-sm font-bold text-slate-500 tabular-nums">
                                {{ $mk->jadwalKuliah->mataKuliah->sks_default }}
                            </td>
                            <td class="px-8 py-5 text-center">
                                <span class="px-3 py-1 rounded-lg text-[11px] font-black uppercase tracking-widest border
                                @switch($mk->nilai_huruf)
                                    @case('A') bg-emerald-50 text-emerald-600 border-emerald-100 @break
                                    @case('B') bg-blue-50 text-blue-600 border-blue-100 @break
                                    @case('C') bg-amber-50 text-amber-600 border-amber-100 @break
                                    @case('D') bg-orange-50 text-orange-600 border-orange-100 @break
                                    @case('E') bg-rose-50 text-rose-600 border-rose-100 @break
                                    @default bg-slate-50 text-slate-400 border-slate-100
                                @endswitch">
                                    {{ $mk->nilai_huruf ?? '-' }}
                                </span>
                            </td>
                            <td class="px-8 py-5 text-center text-sm font-bold text-slate-500 tabular-nums">
                                {{ number_format($mk->nilai_indeks, 2) }}
                            </td>
                            <td class="px-8 py-5 text-center text-sm font-black text-slate-900 tabular-nums">
                                {{ number_format($mk->jadwalKuliah->mataKuliah->sks_default * $mk->nilai_indeks, 2) }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-8 py-20 text-center">
                                <div class="max-w-xs mx-auto text-slate-400">
                                    <svg class="w-12 h-12 mx-auto mb-4 opacity-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <p class="font-bold text-xs italic uppercase tracking-widest leading-loose">Belum ada nilai yang dipublish oleh dosen pengampu.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Statistics Summary --}}
        @if($riwayat)
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-200 text-center group hover:border-unmaris-blue transition-all duration-300">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Total Beban Kredit</p>
                <div class="text-3xl font-black text-slate-800 group-hover:scale-110 transition-transform">{{ $riwayat->sks_semester }} <span class="text-xs text-slate-400 font-bold ml-1">SKS</span></div>
            </div>
            <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-200 text-center group hover:border-indigo-600 transition-all duration-300">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">IP Semester (IPS)</p>
                <div class="text-4xl font-black text-indigo-600 tabular-nums group-hover:scale-110 transition-transform">{{ number_format($riwayat->ips, 2) }}</div>
            </div>
            <div class="bg-[#0f172a] p-8 rounded-3xl shadow-xl border border-white/5 text-center group hover:shadow-indigo-900/20 transition-all duration-300">
                <p class="text-[10px] font-black text-white/30 uppercase tracking-widest mb-2">IP Kumulatif (IPK)</p>
                <div class="text-4xl font-black text-unmaris-yellow tabular-nums group-hover:scale-110 transition-transform">{{ number_format($riwayat->ipk, 2) }}</div>
            </div>
        </div>
        @endif
    </div>


</div>