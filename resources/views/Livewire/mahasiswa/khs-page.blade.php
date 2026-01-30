<div class="space-y-8 animate-in fade-in duration-500">
    {{-- SEO & Header Layout --}}
    <x-slot name="title">KHS Online - UNMARIS</x-slot>
    <x-slot name="header">Kartu Hasil Studi</x-slot>

    {{-- Profile Header Card (Consistent with KRS) --}}
    <div class="bg-white rounded-3xl shadow-xl border border-slate-200 overflow-hidden relative">
        <div class="absolute top-0 right-0 p-4 opacity-10 pointer-events-none">
            {{-- Decorative Icon --}}
            <svg class="w-64 h-64 text-[#002855]" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 14l9-5-9-5-9 5 9 5z" />
                <path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
            </svg>
        </div>
        <div class="p-6 lg:p-8 flex flex-col md:flex-row md:items-center justify-between gap-6 relative z-10">
            <div class="flex items-center space-x-5">
                <div class="w-16 h-16 bg-[#002855] text-[#fcc000] rounded-2xl flex items-center justify-center font-black text-3xl shadow-lg ring-4 ring-slate-50">
                    {{ substr($mahasiswa->person->nama_lengkap ?? $mahasiswa->nama_lengkap ?? 'M', 0, 1) }}
                </div>
                <div>
                    <h2 class="text-2xl font-black text-[#002855] leading-tight uppercase tracking-tight">
                        {{ $mahasiswa->person->nama_lengkap ?? $mahasiswa->nama_lengkap ?? 'Mahasiswa' }}
                    </h2>
                    <div class="flex flex-wrap items-center gap-2 mt-1.5">
                        <span class="text-xs font-mono font-bold text-[#002855] bg-[#002855]/10 px-2 py-0.5 rounded-lg">{{ $mahasiswa->nim }}</span>
                        <span class="text-xs text-slate-500 font-bold uppercase tracking-widest border-l border-slate-300 pl-2 ml-1">{{ $mahasiswa->prodi->nama_prodi }}</span>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-4">
                {{-- IPK/IPS Badge --}}
                <div class="bg-slate-50 border border-slate-100 px-5 py-2.5 rounded-2xl flex items-center shadow-inner min-w-[140px] justify-between">
                    <div class="text-right mr-4">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">IP Semester</p>
                        <p class="text-[10px] font-bold text-slate-400">IPS</p>
                    </div>
                    <div class="text-3xl font-black text-[#002855] tabular-nums">
                        {{ number_format($riwayat->ips ?? 0, 2) }}
                    </div>
                </div>

                @if(count($details) > 0)
                <a href="{{ route('mhs.cetak.khs') }}" target="_blank"
                    class="inline-flex items-center px-5 py-3 bg-white border border-slate-200 text-slate-600 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-[#002855] hover:text-white transition-all shadow-sm group">
                    <svg class="w-5 h-5 mr-2 group-hover:text-[#fcc000] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Cetak PDF
                </a>
                @endif
            </div>
        </div>
    </div>

    {{-- Ringkasan Statistik --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-[#002855] p-6 rounded-2xl shadow-lg shadow-indigo-900/20 text-white relative overflow-hidden">
            <div class="absolute top-0 right-0 w-24 h-24 bg-[#fcc000] rounded-full blur-2xl -mr-6 -mt-6 opacity-20"></div>
            <p class="text-xs font-bold uppercase opacity-60 tracking-widest">SKS Diambil</p>
            <p class="text-4xl font-black mt-2">{{ $details->sum('sks_default') }} <span class="text-sm font-bold opacity-50">SKS</span></p>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Total Mutu (SKS x Nilai)</p>
            @php
            $totalMutu = $details->sum(fn($d) => $d->sks_default * $d->nilai_indeks);
            @endphp
            <p class="text-3xl font-black text-slate-800 mt-2">{{ number_format($totalMutu, 2) }}</p>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Status Akademik</p>
            <div class="mt-2 flex items-center">
                @if(($riwayat->status_kuliah ?? 'N') == 'A')
                <span class="w-3 h-3 bg-emerald-500 rounded-full mr-2 animate-pulse"></span>
                <span class="text-xl font-black text-emerald-600 uppercase">Aktif</span>
                @else
                <span class="w-3 h-3 bg-slate-400 rounded-full mr-2"></span>
                <span class="text-xl font-black text-slate-500 uppercase">Non-Aktif</span>
                @endif
            </div>
        </div>
    </div>

    {{-- Tabel Nilai --}}
    <div class="bg-white shadow-sm rounded-2xl border border-slate-200 overflow-hidden">
        <div class="px-8 py-5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
            <h3 class="text-xs font-black text-[#002855] uppercase tracking-[0.2em]">Rincian Hasil Studi</h3>
            <span class="text-xs font-bold text-[#002855] bg-[#fcc000]/20 px-2.5 py-1 rounded-lg">{{ count($details) }} Mata Kuliah</span>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-[#002855] text-white">
                    <tr>
                        <th class="px-6 py-4 text-left text-[10px] font-bold uppercase tracking-widest">Mata Kuliah</th>
                        <th class="px-4 py-4 text-center text-[10px] font-bold uppercase tracking-widest">SKS</th>
                        <th class="px-4 py-4 text-center text-[10px] font-bold uppercase tracking-widest">Angka</th>
                        <th class="px-4 py-4 text-center text-[10px] font-bold uppercase tracking-widest">Huruf</th>
                        <th class="px-4 py-4 text-center text-[10px] font-bold uppercase tracking-widest">Indeks</th>
                        <th class="px-4 py-4 text-center text-[10px] font-bold uppercase tracking-widest">Mutu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($details as $row)
                    <tr class="hover:bg-slate-50/80 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-3">
                                <span class="px-2 py-0.5 rounded-md bg-[#002855]/10 text-[#002855] text-[9px] font-black uppercase tracking-widest">{{ $row->jadwalKuliah->mataKuliah->kode_mk }}</span>
                                <span class="text-sm font-bold text-slate-800">{{ $row->jadwalKuliah->mataKuliah->nama_mk }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-4 text-center">
                            <span class="text-sm font-medium text-slate-600 bg-slate-100 px-2 py-1 rounded">{{ $row->jadwalKuliah->mataKuliah->sks_default }}</span>
                        </td>
                        <td class="px-4 py-4 text-center">
                            <span class="text-sm font-bold text-slate-700">{{ number_format($row->nilai_angka, 2) }}</span>
                        </td>
                        <td class="px-4 py-4 text-center">
                            @php
                            $gradeColor = match($row->nilai_huruf) {
                            'A' => 'text-emerald-600 bg-emerald-50',
                            'A-' => 'text-emerald-600 bg-emerald-50',
                            'B+', 'B', 'B-' => 'text-indigo-600 bg-indigo-50',
                            'C+', 'C' => 'text-amber-600 bg-amber-50',
                            'D' => 'text-orange-600 bg-orange-50',
                            'E' => 'text-rose-600 bg-rose-50',
                            default => 'text-slate-600 bg-slate-50'
                            };
                            @endphp
                            <span class="text-base font-black px-3 py-1 rounded-lg {{ $gradeColor }}">
                                {{ $row->nilai_huruf }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-center text-sm font-bold text-slate-700">{{ number_format($row->nilai_indeks, 2) }}</td>
                        <td class="px-4 py-4 text-center text-sm font-black text-[#002855]">{{ number_format($row->jadwalKuliah->mataKuliah->sks_default * $row->nilai_indeks, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="bg-slate-50 p-4 rounded-full mb-3">
                                    <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <p class="text-slate-500 font-medium italic">Belum ada nilai yang dipublikasikan untuk semester ini.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>