<div>
    {{-- SEO & Header --}}
    <x-slot name="title">Monitoring Keuangan</x-slot>
    <x-slot name="header">Monitoring & Realisasi Keuangan</x-slot>

    <div class="space-y-8">
        {{-- Top Toolbar --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <p class="text-slate-500 text-sm">Analisis realisasi pembayaran, potensi pendapatan, dan pemantauan tunggakan mahasiswa.</p>
            </div>

            <div class="flex items-center gap-3">
                <button onclick="window.print()"
                    class="inline-flex items-center px-6 py-3 bg-white border border-slate-200 text-slate-600 rounded-xl font-bold text-sm hover:bg-slate-50 transition-all shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Cetak Laporan
                </button>
            </div>
        </div>

        {{-- Filters Section --}}
        <div class="bg-white p-6 shadow-sm rounded-3xl border border-slate-200 grid grid-cols-1 md:grid-cols-4 gap-6 items-end">
            <div>
                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2">Tahun Akademik</label>
                <select wire:model.live="semesterId" class="block w-full rounded-xl border-slate-200 bg-slate-50 py-3 px-4 focus:bg-white focus:border-unmaris-blue focus:ring-4 focus:ring-unmaris-blue/5 text-sm transition-all outline-none">
                    @foreach($semesters as $sem)
                    <option value="{{ $sem->id }}">{{ $sem->nama_tahun }} {{ $sem->is_active ? '(Aktif)' : '' }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2">Program Studi</label>
                <select wire:model.live="filterProdiId" class="block w-full rounded-xl border-slate-200 bg-slate-50 py-3 px-4 focus:bg-white focus:border-unmaris-blue focus:ring-4 focus:ring-unmaris-blue/5 text-sm transition-all outline-none">
                    <option value="">Semua Prodi</option>
                    @foreach($prodis as $p)
                    <option value="{{ $p->id }}">{{ $p->nama_prodi }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2">Status Pembayaran</label>
                <select wire:model.live="filterStatus" class="block w-full rounded-xl border-slate-200 bg-slate-50 py-3 px-4 focus:bg-white focus:border-unmaris-blue focus:ring-4 focus:ring-unmaris-blue/5 text-sm transition-all outline-none">
                    <option value="">Semua Status</option>
                    <option value="LUNAS">Lunas</option>
                    <option value="BELUM">Belum Lunas / Cicil</option>
                </select>
            </div>
            <div>
                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2">Cari Mahasiswa</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>
                    <input type="text" wire:model.live.debounce.500ms="search" placeholder="NIM atau Nama..." class="block w-full pl-10 rounded-xl border-slate-200 bg-slate-50 py-3 px-4 focus:bg-white focus:border-unmaris-blue focus:ring-4 focus:ring-unmaris-blue/5 text-sm transition-all outline-none">
                </div>
            </div>
        </div>

        {{-- Summary Stats Cards --}}
        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
            <!-- Card 1: Potensi -->
            <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-200 flex items-center space-x-6 group hover:border-unmaris-blue transition-all duration-300">
                <div class="w-14 h-14 bg-indigo-50 rounded-2xl flex items-center justify-center text-unmaris-blue shrink-0">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Potensi Tagihan</p>
                    <p class="text-2xl font-black text-slate-900 tabular-nums">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</p>
                    <p class="text-[11px] text-slate-400 font-bold mt-1">{{ $countLunas + $countBelum }} Mahasiswa Terdata</p>
                </div>
            </div>

            <!-- Card 2: Realisasi -->
            <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-200 flex items-center space-x-6 group hover:border-emerald-500 transition-all duration-300">
                <div class="w-14 h-14 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600 shrink-0">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Realisasi (Uang Masuk)</p>
                    <p class="text-2xl font-black text-emerald-600 tabular-nums">Rp {{ number_format($totalTerbayar, 0, ',', '.') }}</p>
                    <div class="flex items-center mt-1">
                        <span class="text-[11px] bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded-lg font-black tracking-tighter">
                            {{ $totalTagihan > 0 ? number_format(($totalTerbayar / $totalTagihan) * 100, 1) : 0 }}% Tercapai
                        </span>
                    </div>
                </div>
            </div>

            <!-- Card 3: Tunggakan -->
            <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-200 flex items-center space-x-6 group hover:border-rose-500 transition-all duration-300">
                <div class="w-14 h-14 bg-rose-50 rounded-2xl flex items-center justify-center text-rose-600 shrink-0">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Sisa Tunggakan</p>
                    <p class="text-2xl font-black text-rose-600 tabular-nums">Rp {{ number_format($totalTunggakan, 0, ',', '.') }}</p>
                    <p class="text-[11px] text-rose-400 font-bold mt-1">{{ $countBelum }} Mahasiswa Outstanding</p>
                </div>
            </div>
        </div>

        {{-- Main Data Table --}}
        <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-100">
                            <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Mahasiswa</th>
                            <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Program & Prodi</th>
                            <th class="px-8 py-5 text-right text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Tagihan</th>
                            <th class="px-8 py-5 text-right text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Realisasi</th>
                            <th class="px-8 py-5 text-right text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Sisa</th>
                            <th class="px-8 py-5 text-center text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($tagihans as $tagihan)
                        <tr class="hover:bg-slate-50/50 transition-colors group">
                            <td class="px-8 py-5">
                                <div class="text-sm font-bold text-slate-800 leading-tight">{{ $tagihan->mahasiswa->nama_lengkap }}</div>
                                <div class="text-[10px] font-mono font-bold text-indigo-500 mt-1 uppercase tracking-tighter">{{ $tagihan->mahasiswa->nim }}</div>
                            </td>
                            <td class="px-8 py-5">
                                <div class="text-[11px] font-bold text-slate-600 uppercase">{{ $tagihan->mahasiswa->prodi->nama_prodi }}</div>
                                <div class="text-[10px] text-slate-400 font-medium tracking-tight">{{ $tagihan->mahasiswa->programKelas->nama_program }}</div>
                            </td>
                            <td class="px-8 py-5 text-right font-bold text-slate-700 tabular-nums text-[13px]">
                                {{ number_format($tagihan->total_tagihan, 0, ',', '.') }}
                            </td>
                            <td class="px-8 py-5 text-right font-black text-emerald-600 tabular-nums text-[13px]">
                                {{ number_format($tagihan->total_bayar, 0, ',', '.') }}
                            </td>
                            <td class="px-8 py-5 text-right font-black text-rose-600 tabular-nums text-[13px]">
                                {{ number_format($tagihan->sisa_tagihan, 0, ',', '.') }}
                            </td>
                            <td class="px-8 py-5 text-center">
                                <span class="px-3 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-widest border
                                {{ $tagihan->status_bayar == 'LUNAS' 
                                    ? 'bg-emerald-50 text-emerald-600 border-emerald-100' 
                                    : 'bg-rose-50 text-rose-600 border-rose-100' }}">
                                    {{ $tagihan->status_bayar }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-8 py-20 text-center">
                                <div class="max-w-xs mx-auto text-slate-400">
                                    <svg class="w-16 h-16 mx-auto mb-4 opacity-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p class="font-bold text-sm italic">Data tidak ditemukan</p>
                                    <p class="text-[11px] mt-1">Pastikan proses "Generate Tagihan" sudah dilakukan di menu generator.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-8 py-5 bg-slate-50/50 border-t border-slate-100">
                {{ $tagihans->links() }}
            </div>
        </div>
    </div>


</div>