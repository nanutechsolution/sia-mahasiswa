<div class="space-y-8 animate-in fade-in duration-700 max-w-[1600px] mx-auto p-4 md:p-8">
    {{-- 1. Header Section --}}
    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6">
        <div class="space-y-2">
            <h1 class="text-3xl font-black text-[#002855] tracking-tight flex items-center gap-3">
                <div class="w-12 h-12 bg-[#002855] rounded-2xl flex items-center justify-center text-[#fcc000] shadow-xl shadow-blue-900/20">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                </div>
                Cetak Dokumen Absensi
            </h1>
            <p class="text-slate-400 font-bold text-sm ml-1 uppercase tracking-widest italic">DHMD & Jurnal Perkuliahan Kolektif</p>
        </div>
    </div>

    {{-- 2. Filter & Search Bento Box --}}
    <div class="bg-white p-3 shadow-sm rounded-[2rem] border border-slate-200 grid grid-cols-1 md:grid-cols-3 gap-3">
        <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-2xl border border-slate-100 group focus-within:border-[#002855] transition-all">
            <div class="w-11 h-11 shrink-0 rounded-[10px] bg-white text-[#002855] flex items-center justify-center font-black text-[10px] tracking-widest shadow-sm">SEM</div>
            <select wire:model.live="filterSemesterId" class="flex-1 bg-transparent border-none font-bold text-[#002855] focus:ring-0 text-sm uppercase">
                @foreach($semesters as $sem)
                    <option value="{{ $sem->id }}">{{ $sem->nama_tahun }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-2xl border border-slate-100 group focus-within:border-[#002855] transition-all">
            <div class="w-11 h-11 shrink-0 rounded-[10px] bg-white text-indigo-600 flex items-center justify-center font-black text-[10px] tracking-widest shadow-sm">PRD</div>
            <select wire:model.live="filterProdiId" class="flex-1 bg-transparent border-none font-bold text-slate-600 focus:ring-0 text-sm uppercase">
                <option value="">Semua Program Studi</option>
                @foreach($prodis as $p)
                    <option value="{{ $p->id }}">{{ $p->jenjang }} - {{ $p->nama_prodi }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex items-center gap-3 p-3 bg-white rounded-2xl border border-slate-200 group focus-within:ring-2 focus-within:ring-indigo-100 transition-all">
            <div class="w-11 h-11 shrink-0 rounded-[10px] bg-slate-50 text-slate-400 group-focus-within:text-[#002855] group-focus-within:bg-indigo-50 flex items-center justify-center transition-colors">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
            </div>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari MK / Dosen..." class="flex-1 border-none focus:ring-0 text-sm font-bold text-slate-700 placeholder:text-slate-300 placeholder:font-medium">
        </div>
    </div>

    {{-- 3. Schedule Table --}}
    <div class="bg-white rounded-[2rem] shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50/80">
                    <tr>
                        <th class="px-8 py-5 text-left text-[10px] font-black text-slate-500 uppercase tracking-widest">Mata Kuliah & Jadwal</th>
                        <th class="px-6 py-5 text-left text-[10px] font-black text-slate-500 uppercase tracking-widest">Tim Pengajar</th>
                        <th class="px-6 py-5 text-center text-[10px] font-black text-slate-500 uppercase tracking-widest">Peserta</th>
                        <th class="px-8 py-5 text-right text-[10px] font-black text-slate-500 uppercase tracking-widest">Opsi Cetak</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($jadwals as $jadwal)
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <td class="px-8 py-6">
                            <div class="flex items-start gap-4">
                                <div class="w-14 h-14 bg-indigo-50/50 rounded-2xl flex flex-col items-center justify-center text-[#002855] shrink-0 border border-indigo-100/50 group-hover:bg-[#002855] group-hover:text-white transition-colors">
                                    <span class="text-[9px] font-black uppercase tracking-widest">{{ substr($jadwal->hari, 0, 3) }}</span>
                                    <span class="text-sm font-black mt-0.5">{{ substr($jadwal->jam_mulai, 0, 5) }}</span>
                                </div>
                                <div class="space-y-1.5">
                                    <h4 class="text-sm font-black text-slate-800 uppercase tracking-tight leading-tight group-hover:text-[#002855] transition-colors">{{ $jadwal->mataKuliah->nama_mk }}</h4>
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="text-[10px] font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-md uppercase">{{ $jadwal->mataKuliah->kode_mk }}</span>
                                        <span class="text-[10px] font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-md border border-indigo-100 uppercase">R. {{ $jadwal->ruang->kode_ruang ?? 'TBA' }}</span>
                                        <span class="text-[10px] font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded-md border border-amber-100 uppercase">KLS {{ $jadwal->nama_kelas }}</span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-6">
                            <div class="flex flex-col gap-2">
                                @foreach($jadwal->dosens as $dosen)
                                <div class="flex items-center gap-2">
                                    <div class="w-1.5 h-1.5 rounded-full {{ $dosen->pivot->is_koordinator ? 'bg-amber-500 animate-pulse' : 'bg-slate-300' }}"></div>
                                    <span class="text-xs font-bold text-slate-600 tracking-tight">{{ $dosen->person->nama_lengkap }}</span>
                                    @if($dosen->pivot->is_koordinator)
                                        <span class="text-[8px] font-black bg-amber-100 text-amber-700 px-1.5 py-0.5 rounded uppercase tracking-widest ml-1">Koor</span>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-6 py-6 text-center align-middle">
                            <div class="inline-flex flex-col items-center justify-center w-12 h-12 bg-slate-50 rounded-xl border border-slate-100">
                                <span class="text-lg font-black text-[#002855]">{{ $jadwal->peserta_count }}</span>
                            </div>
                        </td>
                        <td class="px-8 py-6 text-right align-middle">
                            <div class="flex flex-col sm:flex-row justify-end gap-2">
                                @if($jadwal->peserta_count > 0)
                                    <a href="{{ route('admin.cetak.absensi', $jadwal->id) }}" target="_blank" 
                                        class="inline-flex items-center justify-center px-4 py-2 bg-[#002855] text-white rounded-lg text-[10px] font-black uppercase tracking-widest hover:bg-indigo-700 transition-colors shadow-sm">
                                        <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        DHMD
                                    </a>
                                    <a href="{{ route('admin.cetak.rekap-absensi', $jadwal->id) }}" target="_blank" 
                                        class="inline-flex items-center justify-center px-4 py-2 bg-white border border-slate-300 text-slate-700 rounded-lg text-[10px] font-black uppercase tracking-widest hover:border-[#002855] hover:text-[#002855] transition-colors shadow-sm">
                                        <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        Rekap
                                    </a>
                                @else
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-[10px] font-bold text-rose-500 bg-rose-50 border border-rose-100">
                                        Kosong
                                    </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-8 py-24 text-center">
                            <div class="flex flex-col items-center justify-center space-y-3">
                                <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center text-3xl grayscale opacity-40">📝</div>
                                <p class="text-slate-500 font-bold text-sm">Tidak ada jadwal perkuliahan yang ditemukan.</p>
                                <p class="text-slate-400 text-xs">Coba sesuaikan filter pencarian Anda.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-8 py-4 bg-slate-50/50 border-t border-slate-100">
            {{ $jadwals->links() }}
        </div>
    </div>

    {{-- Footer Info --}}
    <div class="pt-8 flex flex-col items-center gap-2 opacity-30 grayscale pointer-events-none">
        <p class="text-[10px] font-black uppercase tracking-[0.4em] text-[#002855]">SIAKAD &bull; Document Generator</p>
    </div>
</div>