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
    <div class="bg-white p-2 shadow-sm rounded-[2.5rem] border border-slate-200 grid grid-cols-1 md:grid-cols-3 gap-2">
        <div class="flex items-center gap-4 p-4 bg-slate-50 rounded-[1.8rem] border border-slate-100 group focus-within:border-[#002855] transition-all">
            <div class="w-10 h-10 rounded-xl bg-white text-[#002855] flex items-center justify-center font-black text-[10px] shadow-sm">SEM</div>
            <select wire:model.live="filterSemesterId" class="flex-1 bg-transparent border-none font-black text-[#002855] focus:ring-0 text-sm uppercase">
                @foreach($semesters as $sem)
                    <option value="{{ $sem->id }}">{{ $sem->nama_tahun }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex items-center gap-4 p-4 bg-slate-50 rounded-[1.8rem] border border-slate-100 group focus-within:border-[#002855] transition-all">
            <div class="w-10 h-10 rounded-xl bg-white text-indigo-600 flex items-center justify-center font-black text-[10px] shadow-sm">PRD</div>
            <select wire:model.live="filterProdiId" class="flex-1 bg-transparent border-none font-black text-slate-600 focus:ring-0 text-sm uppercase">
                @foreach($prodis as $p)
                    <option value="{{ $p->id }}">{{ $p->jenjang }} - {{ $p->nama_prodi }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex items-center gap-4 p-4 bg-white rounded-[1.8rem] border border-slate-200 group focus-within:ring-4 focus-within:ring-indigo-50 transition-all">
            <div class="text-slate-400 group-focus-within:text-[#002855] transition-colors pl-2">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
            </div>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="CARI MK / DOSEN..." class="flex-1 border-none focus:ring-0 text-xs font-black uppercase tracking-widest text-slate-700 placeholder:text-slate-300">
        </div>
    </div>

    {{-- 3. Schedule Table --}}
    <div class="bg-white rounded-[3rem] shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50/50">
                    <tr>
                        <th class="px-10 py-6 text-left text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Mata Kuliah & Jadwal</th>
                        <th class="px-10 py-6 text-left text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Tim Pengajar</th>
                        <th class="px-6 py-6 text-center text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Status Peserta</th>
                        <th class="px-10 py-6 text-right text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Opsi Cetak</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($jadwals as $jadwal)
                    <tr class="hover:bg-slate-50/80 transition-all group">
                        <td class="px-10 py-8">
                            <div class="flex items-start gap-5">
                                <div class="w-14 h-14 bg-[#002855]/5 rounded-2xl flex flex-col items-center justify-center text-[#002855] shrink-0 border border-blue-900/5">
                                    <span class="text-[9px] font-black uppercase leading-none">{{ substr($jadwal->hari, 0, 3) }}</span>
                                    <span class="text-sm font-black mt-1">{{ substr($jadwal->jam_mulai, 0, 5) }}</span>
                                </div>
                                <div class="space-y-1">
                                    <h4 class="text-sm font-black text-[#002855] uppercase tracking-tight leading-tight group-hover:text-indigo-600 transition-colors">{{ $jadwal->mataKuliah->nama_mk }}</h4>
                                    <div class="flex items-center gap-3">
                                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ $jadwal->mataKuliah->kode_mk }}</span>
                                        <div class="w-1 h-1 rounded-full bg-slate-200"></div>
                                        <span class="text-[10px] font-black text-indigo-400 uppercase tracking-widest">R.{{ $jadwal->ruang->kode_ruang ?? 'TBA' }}</span>
                                        <div class="w-1 h-1 rounded-full bg-slate-200"></div>
                                        <span class="text-[10px] font-black text-amber-500 uppercase tracking-widest">KLS {{ $jadwal->nama_kelas }}</span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-10 py-8">
                            <div class="flex flex-col gap-2">
                                @foreach($jadwal->dosens as $dosen)
                                <div class="flex items-center gap-3">
                                    <div class="w-2 h-2 rounded-full {{ $dosen->pivot->is_koordinator ? 'bg-[#fcc000]' : 'bg-slate-200' }}"></div>
                                    <span class="text-[11px] font-bold text-slate-600 uppercase tracking-tighter">{{ $dosen->person->nama_lengkap }}</span>
                                    @if($dosen->pivot->is_koordinator)
                                        <span class="text-[7px] font-black bg-amber-50 text-amber-600 border border-amber-100 px-1.5 py-0.5 rounded uppercase tracking-widest">Koor</span>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-6 py-8 text-center">
                            <div class="inline-flex flex-col items-center">
                                <span class="text-xl font-black text-[#002855] italic">{{ $jadwal->peserta_count }}</span>
                                <span class="text-[8px] font-black text-slate-400 uppercase tracking-[0.2em]">Mahasiswa Aktif</span>
                            </div>
                        </td>
                        <td class="px-10 py-8 text-right">
                            <div class="flex flex-col sm:flex-row justify-end gap-2">
                                @if($jadwal->peserta_count > 0)
                                    {{-- Tombol DHMD --}}
                                    <a href="{{ route('admin.cetak.absensi', $jadwal->id) }}" target="_blank" 
                                        class="inline-flex items-center justify-center px-5 py-2.5 bg-slate-900 text-white rounded-xl text-[9px] font-black uppercase tracking-[0.15em] hover:bg-indigo-600 transition-all shadow-lg shadow-slate-900/10">
                                        DHMD PDF
                                    </a>
                                    {{-- Tombol Rekap (Reuse Dosen Method) --}}
                                    <a href="{{ route('admin.cetak.rekap-absensi', $jadwal->id) }}" target="_blank" 
                                        class="inline-flex items-center justify-center px-5 py-2.5 bg-white border border-slate-200 text-slate-600 rounded-xl text-[9px] font-black uppercase tracking-[0.15em] hover:border-[#002855] hover:text-[#002855] transition-all shadow-sm">
                                        REKAP SEMESTER
                                    </a>
                                @else
                                    <span class="text-[9px] font-black text-rose-400 uppercase tracking-widest bg-rose-50 px-4 py-2 rounded-xl border border-rose-100 italic">Peserta 0</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-10 py-32 text-center">
                            <div class="flex flex-col items-center justify-center space-y-4">
                                <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center text-4xl grayscale opacity-20">📂</div>
                                <p class="text-slate-400 font-black uppercase tracking-[0.3em] text-xs">Data perkuliahan tidak ditemukan</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-10 py-8 bg-slate-50/50 border-t border-slate-100">
            {{ $jadwals->links() }}
        </div>
    </div>

    {{-- Footer Info --}}
    <div class="pt-10 flex flex-col items-center gap-2 opacity-20 grayscale pointer-events-none border-t border-slate-100">
        <p class="text-[9px] font-black uppercase tracking-[0.6em] text-[#002855]">OFFICIAL DOCUMENT MANAGER &bull; v4.2 PRO</p>
    </div>
</div>