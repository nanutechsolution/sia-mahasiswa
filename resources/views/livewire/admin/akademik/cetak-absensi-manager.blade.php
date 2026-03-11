<div class="space-y-6 animate-in fade-in duration-500">
    {{-- 1. Header Section --}}
    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-[#002855] rounded-2xl flex items-center justify-center text-[#fcc000] shadow-lg shadow-blue-900/20">
                <x-heroicon-o-printer class="w-7 h-7" />
            </div>
            <div>
                <h1 class="text-2xl font-black text-[#002855] tracking-tight uppercase">Cetak Dokumen Absensi</h1>
                <p class="text-slate-500 text-sm font-medium italic">DHMD & Jurnal Perkuliahan Kolektif Semester Aktif</p>
            </div>
        </div>
    </div>

    {{-- 2. Filter & Search Bento Box --}}
    <div class="bg-white p-3 shadow-sm rounded-3xl border border-slate-200 grid grid-cols-1 md:grid-cols-3 gap-3">
        {{-- Filter Semester --}}
        <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-2xl border border-slate-100 group focus-within:border-[#002855] transition-all">
            <div class="w-10 h-10 shrink-0 rounded-xl bg-white text-[#002855] flex items-center justify-center font-black text-[10px] tracking-widest shadow-sm">SEM</div>
            <select wire:model.live="filterSemesterId" class="flex-1 bg-transparent border-none font-bold text-[#002855] focus:ring-0 text-sm uppercase cursor-pointer">
                @foreach($semesters as $sem)
                    <option value="{{ $sem->id }}">{{ $sem->nama_tahun }}</option>
                @endforeach
            </select>
        </div>

        {{-- Filter Prodi --}}
        <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-2xl border border-slate-100 group focus-within:border-[#002855] transition-all">
            <div class="w-10 h-10 shrink-0 rounded-xl bg-white text-indigo-600 flex items-center justify-center font-black text-[10px] tracking-widest shadow-sm">PRD</div>
            <select wire:model.live="filterProdiId" class="flex-1 bg-transparent border-none font-bold text-slate-600 focus:ring-0 text-sm uppercase cursor-pointer">
                <option value="">Semua Program Studi</option>
                @foreach($prodis as $p)
                    <option value="{{ $p->id }}">{{ $p->jenjang }} - {{ $p->nama_prodi }}</option>
                @endforeach
            </select>
        </div>

        {{-- Search Input --}}
        <div class="flex items-center gap-3 p-3 bg-white rounded-2xl border border-slate-200 group focus-within:ring-2 focus-within:ring-indigo-100 transition-all">
            <div class="w-10 h-10 shrink-0 rounded-xl bg-slate-50 text-slate-400 group-focus-within:text-[#002855] group-focus-within:bg-indigo-50 flex items-center justify-center transition-colors">
                <x-heroicon-o-magnifying-glass class="h-5 w-5" />
            </div>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari MK / Dosen..." class="flex-1 border-none focus:ring-0 text-sm font-bold text-slate-700 placeholder:text-slate-300 placeholder:font-medium">
        </div>
    </div>

    {{-- 3. Schedule Table --}}
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden relative">
        {{-- Loading Overlay --}}
        <div wire:loading.flex wire:target="filterSemesterId, filterProdiId, search, gotoPage, nextPage, previousPage" class="absolute inset-0 z-10 bg-white/60 backdrop-blur-[1px] items-center justify-center">
            <div class="p-4 bg-white rounded-2xl shadow-xl border border-slate-100 flex flex-col items-center">
                <svg class="w-8 h-8 text-[#002855] animate-spin mb-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Memproses...</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-[#002855] text-white">
                    <tr>
                        <th class="px-8 py-5 text-[10px] font-bold uppercase tracking-widest">Mata Kuliah & Jadwal</th>
                        <th class="px-6 py-5 text-[10px] font-bold uppercase tracking-widest">Tim Pengajar</th>
                        <th class="px-6 py-5 text-center text-[10px] font-bold uppercase tracking-widest">Peserta</th>
                        <th class="px-8 py-5 text-right text-[10px] font-bold uppercase tracking-widest w-64">Opsi Cetak</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($jadwals as $jadwal)
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        {{-- MK & Waktu --}}
                        <td class="px-8 py-6">
                            <div class="flex items-start gap-4">
                                <div class="w-14 h-14 bg-indigo-50 text-[#002855] rounded-2xl flex flex-col items-center justify-center shrink-0 border border-indigo-100 group-hover:bg-[#002855] group-hover:text-white transition-all shadow-sm">
                                    <span class="text-[9px] font-black uppercase tracking-widest leading-none">{{ substr($jadwal->hari, 0, 3) }}</span>
                                    <span class="text-xs font-black mt-1.5">{{ substr($jadwal->jam_mulai, 0, 5) }}</span>
                                </div>
                                <div class="space-y-2">
                                    <h4 class="text-sm font-black text-slate-800 uppercase tracking-tight leading-tight group-hover:text-[#002855] transition-colors line-clamp-2">{{ $jadwal->mataKuliah->nama_mk }}</h4>
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="text-[9px] font-black text-slate-500 bg-slate-100 px-2 py-0.5 rounded shadow-xs border border-slate-200 uppercase">{{ $jadwal->mataKuliah->kode_mk }}</span>
                                        <span class="text-[9px] font-black text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded shadow-xs border border-indigo-100 uppercase">R. {{ $jadwal->ruang->kode_ruang ?? 'TBA' }}</span>
                                        <span class="text-[9px] font-black text-amber-700 bg-amber-50 px-2 py-0.5 rounded shadow-xs border border-amber-100 uppercase">KLS {{ $jadwal->nama_kelas }}</span>
                                    </div>
                                </div>
                            </div>
                        </td>

                        {{-- Tim Dosen --}}
                        <td class="px-6 py-6">
                            <div class="flex flex-col gap-2">
                                @foreach($jadwal->dosens as $dosen)
                                <div class="flex items-center gap-2">
                                    <div class="w-1.5 h-1.5 rounded-full {{ $dosen->pivot->is_koordinator ? 'bg-amber-400 animate-pulse' : 'bg-slate-300' }}"></div>
                                    <span class="text-[11px] font-bold text-slate-600 uppercase tracking-tight group-hover:text-slate-800 transition-colors">{{ $dosen->person->nama_lengkap }}</span>
                                    @if($dosen->pivot->is_koordinator)
                                        <span class="text-[7px] font-black bg-amber-100 text-amber-700 px-1.5 py-0.5 rounded uppercase tracking-tighter shadow-xs">KOOR</span>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                        </td>

                        {{-- Peserta Count --}}
                        <td class="px-6 py-6 text-center align-middle">
                            <div class="inline-flex flex-col items-center justify-center w-12 h-12 bg-white rounded-2xl border border-slate-200 shadow-sm group-hover:scale-110 transition-transform">
                                <span class="text-lg font-black text-[#002855] leading-none">{{ $jadwal->peserta_count }}</span>
                                <span class="text-[7px] font-black text-slate-400 uppercase mt-1">MHS</span>
                            </div>
                        </td>

                        {{-- Print Actions --}}
                        <td class="px-8 py-6 text-right align-middle">
                            <div class="flex flex-col sm:flex-row justify-end gap-2 opacity-0 group-hover:opacity-100 transition-all transform translate-x-2 group-hover:translate-x-0">
                                @if($jadwal->peserta_count > 0)
                                    <a href="{{ route('admin.cetak.absensi', $jadwal->id) }}" target="_blank" 
                                        class="inline-flex items-center justify-center px-5 py-2.5 bg-[#002855] text-[#fcc000] rounded-xl text-[10px] font-black uppercase tracking-[0.2em] hover:bg-indigo-900 transition-all shadow-lg shadow-blue-900/10">
                                        <x-heroicon-s-printer class="w-3.5 h-3.5 mr-2" />
                                        DHMD
                                    </a>
                                    <a href="{{ route('admin.cetak.rekap-absensi', $jadwal->id) }}" target="_blank" 
                                        class="inline-flex items-center justify-center px-5 py-2.5 bg-white border-2 border-slate-200 text-slate-700 rounded-xl text-[10px] font-black uppercase tracking-[0.2em] hover:border-[#002855] hover:text-[#002855] transition-all shadow-sm">
                                        <x-heroicon-s-document-chart-bar class="w-3.5 h-3.5 mr-2" />
                                        Rekap
                                    </a>
                                @else
                                    <span class="inline-flex items-center px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest text-rose-500 bg-rose-50 border border-rose-100 shadow-xs">
                                        <x-heroicon-s-no-symbol class="w-3.5 h-3.5 mr-2" />
                                        Kosong
                                    </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-8 py-24 text-center">
                            <div class="flex flex-col items-center justify-center space-y-4 opacity-30 grayscale">
                                <x-heroicon-o-document-magnifying-glass class="w-20 h-20 text-slate-400" />
                                <div class="space-y-1">
                                    <p class="text-slate-500 font-black text-sm uppercase tracking-widest">Jadwal Perkuliahan Nihil</p>
                                    <p class="text-slate-400 text-[10px] font-bold uppercase tracking-tighter">Silakan periksa filter semester atau prodi Anda.</p>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($jadwals->hasPages())
        <div class="px-8 py-5 bg-slate-50/50 border-t border-slate-100">
            {{ $jadwals->links() }}
        </div>
        @endif
    </div>

    {{-- System Footer --}}
    <div class="flex flex-col items-center justify-center gap-3 opacity-20 grayscale select-none pointer-events-none py-6 transition-opacity hover:opacity-50">
        <div class="flex items-center gap-4">
            <div class="h-px bg-slate-300 w-16"></div>
            <p class="text-[9px] font-black uppercase tracking-[0.6em] text-[#002855]">Document Generation Engine &bull; UNMARIS</p>
            <div class="h-px bg-slate-300 w-16"></div>
        </div>
        <p class="text-[8px] font-bold text-slate-400 uppercase tracking-widest italic">SIAKAD Intelligence System v4.0</p>
    </div>
</div>