<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-[#002855]">Cetak Absensi Perkuliahan</h1>
            <p class="text-slate-500 text-sm mt-1">Cetak daftar hadir mahasiswa (DHMD) dan jurnal kelas untuk dosen.</p>
        </div>
    </div>

    <!-- Filter & Search -->
    <div class="bg-white p-5 shadow-sm rounded-2xl border border-slate-200 grid grid-cols-1 md:grid-cols-3 gap-6">
        <div>
            <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Periode Semester</label>
            <div class="relative">
                <select wire:model.live="filterSemesterId" class="block w-full rounded-xl border-slate-200 bg-white text-slate-900 py-3 pl-4 pr-10 text-sm focus:border-[#002855] focus:ring-[#002855] transition-shadow outline-none font-bold">
                    @foreach($semesters as $sem)
                        <option value="{{ $sem->id }}">{{ $sem->nama_tahun }} {{ $sem->is_active ? '(Aktif)' : '' }}</option>
                    @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-500">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                </div>
            </div>
        </div>
        <div>
            <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Program Studi</label>
            <div class="relative">
                <select wire:model.live="filterProdiId" class="block w-full rounded-xl border-slate-200 bg-white text-slate-900 py-3 pl-4 pr-10 text-sm focus:border-[#002855] focus:ring-[#002855] transition-shadow outline-none font-bold">
                    @foreach($prodis as $p)
                        <option value="{{ $p->id }}">{{ $p->jenjang }} - {{ $p->nama_prodi }}</option>
                    @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-500">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                </div>
            </div>
        </div>
        <div>
            <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Cari Jadwal</label>
            <div class="relative">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Mata Kuliah / Dosen..." class="block w-full rounded-xl border-slate-200 bg-white py-3 pl-10 pr-4 text-sm focus:border-[#002855] focus:ring-[#002855] transition-shadow outline-none font-bold text-slate-700">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center px-3 text-slate-400">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Jadwal -->
    <div class="bg-white shadow-sm rounded-2xl border border-slate-200 overflow-hidden">
        <div class="px-6 py-3 border-b border-slate-100 bg-slate-50/80 flex items-center justify-between">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Total Kelas: {{ $jadwals->total() }}</span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-[#002855] text-white">
                    <tr>
                        <th class="px-6 py-4 text-left text-[10px] font-bold uppercase tracking-widest">Mata Kuliah</th>
                        <th class="px-6 py-4 text-left text-[10px] font-bold uppercase tracking-widest">Kelas / Dosen</th>
                        <th class="px-6 py-4 text-center text-[10px] font-bold uppercase tracking-widest">Peserta (Valid)</th>
                        <th class="px-6 py-4 text-right text-[10px] font-bold uppercase tracking-widest">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($jadwals as $jadwal)
                    <tr class="hover:bg-slate-50/80 transition-colors group">
                        <td class="px-6 py-4 align-top">
                            <div class="text-sm font-bold text-slate-900">{{ $jadwal->mataKuliah->nama_mk }}</div>
                            <div class="flex items-center gap-2 mt-1.5">
                                <span class="px-2 py-0.5 bg-[#002855]/10 text-[#002855] text-[10px] font-bold uppercase rounded border border-[#002855]/20">{{ $jadwal->mataKuliah->kode_mk }}</span>
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ $jadwal->mataKuliah->sks_default }} SKS</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 align-top">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="px-2 py-0.5 bg-slate-100 text-slate-600 text-[10px] font-bold uppercase rounded border border-slate-200">Kls {{ $jadwal->nama_kelas }}</span>
                            </div>
                            <div class="text-xs font-bold text-indigo-700">
                                {{ $jadwal->dosen->nama_lengkap_gelar }}
                            </div>
                            <div class="text-[10px] text-slate-400 mt-0.5">
                                {{ $jadwal->hari }}, {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} (R. {{ $jadwal->ruang }})
                            </div>
                        </td>
                        <td class="px-6 py-4 align-top text-center">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold {{ $jadwal->peserta_count > 0 ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-rose-50 text-rose-700 border border-rose-100' }}">
                                {{ $jadwal->peserta_count }} Mahasiswa
                            </span>
                        </td>
                        <td class="px-6 py-4 align-top text-right">
                            @if($jadwal->peserta_count > 0)
                                <a href="{{ route('admin.cetak.absensi', $jadwal->id) }}" target="_blank" 
                                    class="inline-flex items-center px-4 py-2 bg-white border border-[#002855] text-[#002855] hover:bg-[#002855] hover:text-white rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-sm">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                                    Cetak Absen
                                </a>
                            @else
                                <span class="text-[10px] font-bold text-slate-400 italic bg-slate-50 px-2 py-1 rounded border border-slate-100">Belum ada peserta</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="bg-slate-50 p-4 rounded-full mb-3">
                                    <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                </div>
                                <p class="text-slate-500 font-medium">Tidak ada jadwal kuliah ditemukan.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
            {{ $jadwals->links() }}
        </div>
    </div>
</div>