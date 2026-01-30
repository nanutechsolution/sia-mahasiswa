<div class="space-y-6">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-black text-[#002855]">Monitoring Keuangan</h1>
            <p class="mt-2 text-sm text-slate-500">Laporan realisasi pembayaran mahasiswa per semester.</p>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
            <button onclick="window.print()" class="inline-flex items-center rounded-xl border border-slate-300 bg-white px-5 py-2.5 text-sm font-bold text-slate-700 shadow-sm hover:bg-slate-50 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                Cetak Laporan
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white p-5 shadow-sm rounded-2xl border border-slate-200 grid grid-cols-1 md:grid-cols-4 gap-6">
        <div>
            <label class="block text-xs font-bold text-[#002855] uppercase tracking-widest mb-2">Tahun Akademik</label>
            <div class="relative">
                <select wire:model.live="semesterId" class="block w-full rounded-xl border-slate-200 bg-white text-slate-900 py-3 pl-4 pr-10 text-sm focus:border-[#002855] focus:ring-[#002855] transition-shadow outline-none appearance-none font-bold">
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
            <label class="block text-xs font-bold text-[#002855] uppercase tracking-widest mb-2">Program Studi</label>
            <div class="relative">
                <select wire:model.live="filterProdiId" class="block w-full rounded-xl border-slate-200 bg-white text-slate-900 py-3 pl-4 pr-10 text-sm focus:border-[#002855] focus:ring-[#002855] transition-shadow outline-none appearance-none font-bold">
                    <option value="">Semua Prodi</option>
                    @foreach($prodis as $p)
                        <option value="{{ $p->id }}">{{ $p->nama_prodi }}</option>
                    @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-500">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                </div>
            </div>
        </div>
        <div>
            <label class="block text-xs font-bold text-[#002855] uppercase tracking-widest mb-2">Status Bayar</label>
            <div class="relative">
                <select wire:model.live="filterStatus" class="block w-full rounded-xl border-slate-200 bg-white text-slate-900 py-3 pl-4 pr-10 text-sm focus:border-[#002855] focus:ring-[#002855] transition-shadow outline-none appearance-none font-bold">
                    <option value="">Semua Status</option>
                    <option value="LUNAS">Lunas</option>
                    <option value="BELUM">Belum Lunas / Cicil</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-500">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                </div>
            </div>
        </div>
        <div>
            <label class="block text-xs font-bold text-[#002855] uppercase tracking-widest mb-2">Cari Mahasiswa</label>
            <div class="relative">
                <input type="text" wire:model.live.debounce.500ms="search" placeholder="NIM / Nama..." class="block w-full rounded-xl border-slate-200 bg-white py-3 pl-10 pr-4 text-sm focus:border-[#002855] focus:ring-[#002855] transition-shadow outline-none font-bold text-slate-700">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center px-3 text-slate-400">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
        <!-- Card 1 -->
        <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-slate-200">
            <div class="px-6 py-6 flex items-center gap-4">
                <div class="p-3 bg-indigo-50 rounded-xl text-indigo-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <div>
                    <dt class="text-xs font-bold text-slate-400 uppercase tracking-wider">Potensi Tagihan</dt>
                    <dd class="mt-1 text-2xl font-black text-slate-900">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</dd>
                    <p class="text-[10px] text-slate-400 font-bold mt-1">Dari {{ $countLunas + $countBelum }} Mahasiswa</p>
                </div>
            </div>
        </div>
        <!-- Card 2 -->
        <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-slate-200">
            <div class="px-6 py-6 flex items-center gap-4">
                <div class="p-3 bg-emerald-50 rounded-xl text-emerald-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <div>
                    <dt class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Terbayar</dt>
                    <dd class="mt-1 text-2xl font-black text-emerald-600">Rp {{ number_format($totalTerbayar, 0, ',', '.') }}</dd>
                    <p class="text-[10px] text-emerald-600 font-black mt-1 bg-emerald-50 px-2 py-0.5 rounded inline-block">
                        {{ $totalTagihan > 0 ? number_format(($totalTerbayar / $totalTagihan) * 100, 1) : 0 }}% Tercapai
                    </p>
                </div>
            </div>
        </div>
        <!-- Card 3 -->
        <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-slate-200">
            <div class="px-6 py-6 flex items-center gap-4">
                <div class="p-3 bg-rose-50 rounded-xl text-rose-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                </div>
                <div>
                    <dt class="text-xs font-bold text-slate-400 uppercase tracking-wider">Sisa Tunggakan</dt>
                    <dd class="mt-1 text-2xl font-black text-rose-600">Rp {{ number_format($totalTunggakan, 0, ',', '.') }}</dd>
                    <p class="text-[10px] text-rose-500 font-bold mt-1">{{ $countBelum }} Mhs Belum Lunas</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white shadow-sm rounded-2xl border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-[#002855] text-white">
                    <tr>
                        <th class="px-6 py-4 text-left text-[10px] font-bold uppercase tracking-widest">Mahasiswa</th>
                        <th class="px-6 py-4 text-left text-[10px] font-bold uppercase tracking-widest">Info</th>
                        <th class="px-6 py-4 text-right text-[10px] font-bold uppercase tracking-widest">Tagihan</th>
                        <th class="px-6 py-4 text-right text-[10px] font-bold uppercase tracking-widest">Terbayar</th>
                        <th class="px-6 py-4 text-right text-[10px] font-bold uppercase tracking-widest">Sisa</th>
                        <th class="px-6 py-4 text-center text-[10px] font-bold uppercase tracking-widest">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($tagihans as $tagihan)
                    <tr class="hover:bg-slate-50/80 transition-colors group">
                        <td class="px-6 py-4 whitespace-nowrap align-top">
                            <div class="text-sm font-black text-slate-800">{{ $tagihan->mahasiswa->person->nama_lengkap ?? $tagihan->mahasiswa->nama_lengkap }}</div>
                            <div class="text-[10px] font-mono text-slate-400 mt-0.5">{{ $tagihan->mahasiswa->nim }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap align-top">
                            <div class="text-xs font-bold text-slate-600 uppercase">{{ $tagihan->mahasiswa->prodi->nama_prodi }}</div>
                            <div class="text-[10px] font-bold text-[#002855] mt-1 bg-indigo-50 px-2 py-0.5 rounded w-fit">{{ $tagihan->mahasiswa->programKelas->nama_program }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-slate-600 font-medium align-top">
                            {{ number_format($tagihan->total_tagihan, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-emerald-600 font-bold align-top">
                            {{ number_format($tagihan->total_bayar, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-rose-600 font-black align-top">
                            {{ number_format($tagihan->sisa_tagihan, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center align-top">
                            <span class="px-2.5 py-0.5 inline-flex text-[10px] font-black uppercase rounded-full tracking-wide
                                {{ $tagihan->status_bayar == 'LUNAS' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                {{ $tagihan->status_bayar }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="bg-slate-50 p-4 rounded-full mb-3">
                                    <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                                </div>
                                <p class="text-slate-500 font-medium">Tidak ada data tagihan.</p>
                                <p class="text-xs text-slate-400 mt-1">Pastikan sudah melakukan "Generate Tagihan" di menu Generator.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
            {{ $tagihans->links() }}
        </div>
    </div>
</div>