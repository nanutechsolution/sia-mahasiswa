<div class="space-y-8 animate-in fade-in duration-500">
    
    {{-- TITLE & TOOLS --}}
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
        <div>
            <h1 class="text-3xl font-black text-[#002855] tracking-tight">Financial Command Center</h1>
            <p class="text-slate-500 font-medium text-sm mt-1">
                Laporan real-time, analisis performa prodi, manajemen tunggakan, dan audit refund.
            </p>
        </div>
        <div class="flex gap-3">
            <button wire:click="exportLaporan" wire:loading.attr="disabled" class="group inline-flex items-center rounded-xl bg-emerald-600 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-emerald-600/20 hover:bg-emerald-700 hover:scale-105 transition-all">
                <svg wire:loading.remove wire:target="exportLaporan" class="w-5 h-5 mr-2 group-hover:animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <svg wire:loading wire:target="exportLaporan" class="w-5 h-5 mr-2 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                Unduh Laporan Lengkap (CSV)
            </button>
        </div>
    </div>

    {{-- EXECUTIVE SUMMARY CARDS --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- 1. Tagihan (Potensi) -->
        <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 flex flex-col justify-between relative overflow-hidden group">
            <div class="absolute right-0 top-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity transform group-hover:scale-110">
                <svg class="w-24 h-24 text-indigo-600" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/></svg>
            </div>
            <div>
                <p class="text-xs font-black text-slate-400 uppercase tracking-widest">Total Tagihan (AR)</p>
                <h3 class="text-2xl font-black text-[#002855] mt-2 tracking-tight">Rp {{ number_format($globalStats['bill'], 0, ',', '.') }}</h3>
            </div>
            <div class="mt-4 flex items-center gap-2">
                <span class="bg-indigo-50 text-indigo-700 text-[10px] font-bold px-2 py-1 rounded-lg">{{ $globalStats['students'] }} Mahasiswa</span>
                <span class="text-[10px] text-slate-400 font-medium">Terdaftar</span>
            </div>
        </div>

        <!-- 2. Realisasi (Cash In) -->
        <div class="bg-[#002855] p-6 rounded-[2rem] shadow-lg shadow-indigo-900/20 text-white relative overflow-hidden group">
            <div class="absolute -right-6 -top-6 bg-[#fcc000] w-24 h-24 rounded-full opacity-20 blur-xl group-hover:opacity-30 transition-opacity"></div>
            <div>
                <p class="text-xs font-black text-[#fcc000] uppercase tracking-widest">Realisasi (Cash In)</p>
                <h3 class="text-2xl font-black mt-2 tracking-tight">Rp {{ number_format($globalStats['paid'], 0, ',', '.') }}</h3>
            </div>
            <div class="mt-4 w-full bg-white/10 rounded-full h-1.5 overflow-hidden">
                <div class="bg-[#fcc000] h-1.5 rounded-full transition-all duration-1000" style="width: {{ $globalStats['rate'] }}%"></div>
            </div>
            <p class="text-[10px] font-bold text-slate-300 mt-2 flex justify-between">
                <span>Collection Rate</span>
                <span class="text-white">{{ number_format($globalStats['rate'], 2) }}%</span>
            </p>
        </div>

        <!-- 3. Tunggakan (Outstanding) -->
        <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 flex flex-col justify-between group">
            <div>
                <p class="text-xs font-black text-rose-400 uppercase tracking-widest">Sisa Tunggakan</p>
                <h3 class="text-2xl font-black text-rose-600 mt-2 tracking-tight">Rp {{ number_format($globalStats['debt'], 0, ',', '.') }}</h3>
            </div>
            <div class="mt-4">
                <p class="text-xs text-slate-500 font-medium leading-tight">
                    <span class="font-black text-rose-600">{{ $globalStats['students'] - $globalStats['lunas_count'] }}</span> mahasiswa belum lunas.
                    Perlu tindakan penagihan segera.
                </p>
            </div>
        </div>

        <!-- 4. Deposit & Refund (Scholarship Case) -->
        <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 flex flex-col justify-between relative overflow-hidden">
            <div>
                <p class="text-xs font-black text-blue-500 uppercase tracking-widest">Saldo Lebih / Refund</p>
                @if($globalStats['anomali_count'] > 0)
                    <h3 class="text-2xl font-black text-blue-600 mt-2 tracking-tight">{{ $globalStats['anomali_count'] }} Mhs</h3>
                    <div class="mt-2 text-xs text-slate-500 font-medium leading-relaxed">
                        Terdeteksi kelebihan bayar.
                        <span class="block text-blue-600 font-bold mt-1">Potensi kasus Beasiswa Susulan / Dobel Transfer.</span>
                    </div>
                @else
                    <div class="mt-2 flex items-center text-emerald-600 gap-2">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="font-bold text-sm">Data Bersih</span>
                    </div>
                    <p class="text-xs text-slate-400 mt-1">Tidak ada anomali saldo lebih.</p>
                @endif
            </div>
            <button wire:click="$set('filterStatus', 'LEBIH_BAYAR')" class="mt-4 w-full py-2 bg-blue-50 text-blue-700 text-[10px] font-bold uppercase rounded-lg hover:bg-blue-100 transition-colors flex items-center justify-center gap-2">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                Audit Data Refund
            </button>
        </div>
    </div>

    {{-- ANALYTICS CHART SECTION (CSS ONLY) --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Performa Prodi -->
        <div class="lg:col-span-2 bg-white rounded-3xl shadow-sm border border-slate-200 p-6">
            <h4 class="text-sm font-black text-[#002855] uppercase tracking-widest mb-6 flex items-center gap-2">
                <svg class="w-4 h-4 text-[#fcc000]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                Realisasi Pembayaran Per Prodi
            </h4>
            <div class="space-y-4 max-h-60 overflow-y-auto pr-2 custom-scrollbar">
                @foreach($this->prodiPerformance as $perf)
                <div>
                    <div class="flex justify-between text-xs mb-1 font-bold">
                        <span class="text-slate-700">{{ $perf->nama_prodi }}</span>
                        <span class="{{ $perf->persen >= 80 ? 'text-emerald-600' : ($perf->persen >= 50 ? 'text-amber-600' : 'text-rose-600') }}">
                            {{ number_format($perf->persen, 1) }}%
                        </span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                        <div class="h-full rounded-full {{ $perf->persen >= 80 ? 'bg-emerald-500' : ($perf->persen >= 50 ? 'bg-amber-400' : 'bg-rose-500') }}" style="width: {{ $perf->persen }}%"></div>
                    </div>
                    <div class="flex justify-between text-[9px] text-slate-400 mt-0.5 font-mono">
                        <span>Target: {{ number_format($perf->target/1000000, 0) }} Jt</span>
                        <span>Masuk: {{ number_format($perf->realisasi/1000000, 0) }} Jt</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Tren Cashflow Bulanan -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-6">
            <h4 class="text-sm font-black text-[#002855] uppercase tracking-widest mb-6">Tren Pemasukan (5 Bln)</h4>
            <div class="flex items-end justify-between h-40 gap-2">
                @php $maxVal = $this->monthlyTrend->max('total') ?: 1; @endphp
                @foreach($this->monthlyTrend as $trend)
                    <div class="flex flex-col items-center gap-2 flex-1 group">
                        <div class="w-full bg-indigo-50 rounded-t-lg relative group-hover:bg-[#fcc000] transition-colors" 
                             style="height: {{ ($trend->total / $maxVal) * 100 }}%">
                             {{-- Tooltip --}}
                             <div class="absolute -top-8 left-1/2 -translate-x-1/2 bg-slate-800 text-white text-[9px] px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">
                                 Rp {{ number_format($trend->total/1000000, 1) }} Jt
                             </div>
                        </div>
                        <span class="text-[9px] font-bold text-slate-400 uppercase">{{ $trend->bulan_label }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- FILTER CONTROLS --}}
    <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-200 grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
        <div class="md:col-span-1">
            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Tahun Akademik</label>
            <select wire:model.live="semesterId" class="w-full rounded-xl border-slate-200 bg-slate-50 text-xs font-bold py-2.5 focus:ring-[#002855]">
                @foreach($semesters as $sem)
                    <option value="{{ $sem->id }}">{{ $sem->nama_tahun }} {{ $sem->is_active ? '(Aktif)' : '' }}</option>
                @endforeach
            </select>
        </div>
        <div class="md:col-span-1">
            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Prodi</label>
            <select wire:model.live="filterProdiId" class="w-full rounded-xl border-slate-200 bg-slate-50 text-xs font-bold py-2.5 focus:ring-[#002855]">
                <option value="">Semua Prodi</option>
                @foreach($prodis as $p) <option value="{{ $p->id }}">{{ $p->nama_prodi }}</option> @endforeach
            </select>
        </div>
        <div class="md:col-span-1">
            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Status</label>
            <select wire:model.live="filterStatus" class="w-full rounded-xl border-slate-200 bg-slate-50 text-xs font-bold py-2.5 focus:ring-[#002855]">
                <option value="">Semua Status</option>
                <option value="LUNAS">Lunas</option>
                <option value="CICIL">Menunggak (Cicil)</option>
                <option value="BELUM">Belum Bayar (Nol)</option>
                <option value="LEBIH_BAYAR">Lebih Bayar (Anomali)</option>
            </select>
        </div>
        <div class="md:col-span-2">
            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Pencarian</label>
            <input type="text" wire:model.live.debounce.500ms="search" placeholder="Nama / NIM..." class="w-full rounded-xl border-slate-200 bg-slate-50 text-xs font-bold py-2.5 pl-4 focus:ring-[#002855]">
        </div>
    </div>

    {{-- DATA TABLE --}}
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-[#002855] text-white">
                    <tr>
                        <th class="px-6 py-4 text-left text-[10px] font-bold uppercase tracking-widest">Identitas Mahasiswa</th>
                        <th class="px-6 py-4 text-left text-[10px] font-bold uppercase tracking-widest">Info Akademik</th>
                        <th class="px-6 py-4 text-right text-[10px] font-bold uppercase tracking-widest">Tagihan</th>
                        <th class="px-6 py-4 text-right text-[10px] font-bold uppercase tracking-widest">Terbayar</th>
                        <th class="px-6 py-4 text-right text-[10px] font-bold uppercase tracking-widest">Sisa</th>
                        <th class="px-6 py-4 text-center text-[10px] font-bold uppercase tracking-widest">Status</th>
                        <th class="px-6 py-4 text-right text-[10px] font-bold uppercase tracking-widest">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($tagihans as $tagihan)
                    <tr class="hover:bg-slate-50/80 transition-colors group">
                        <td class="px-6 py-4 whitespace-nowrap align-top">
                            <div class="text-sm font-black text-slate-800">{{ $tagihan->mahasiswa->person->nama_lengkap ?? '-' }}</div>
                            <div class="text-[10px] font-mono text-slate-400 mt-0.5">{{ $tagihan->mahasiswa->nim }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap align-top">
                            <div class="text-xs font-bold text-slate-600 uppercase">{{ $tagihan->mahasiswa->prodi->nama_prodi }}</div>
                            <div class="flex gap-1 mt-1">
                                <span class="text-[9px] font-bold text-[#002855] bg-indigo-50 px-1.5 py-0.5 rounded">{{ $tagihan->mahasiswa->programKelas->nama_program }}</span>
                                <span class="text-[9px] font-bold text-slate-500 bg-slate-100 px-1.5 py-0.5 rounded">Angkatan {{ $tagihan->mahasiswa->angkatan_id }}</span>
                            </div>
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
                            @if($tagihan->status_bayar == 'LUNAS')
                                <span class="px-2.5 py-0.5 inline-flex text-[9px] font-black uppercase rounded-full bg-emerald-100 text-emerald-700">LUNAS</span>
                            @elseif($tagihan->total_bayar > $tagihan->total_tagihan)
                                <span class="px-2.5 py-0.5 rounded-full text-[9px] font-black bg-amber-100 text-amber-700 uppercase animate-pulse">LEBIH BAYAR</span>
                            @elseif($tagihan->total_bayar > 0)
                                <span class="px-2.5 py-0.5 inline-flex text-[9px] font-black uppercase rounded-full bg-blue-100 text-blue-700">CICIL</span>
                            @else
                                <span class="px-2.5 py-0.5 inline-flex text-[9px] font-black uppercase rounded-full bg-rose-100 text-rose-700">BELUM</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right align-top">
                            <button wire:click="openDetail('{{ $tagihan->id }}')" class="text-indigo-600 hover:text-indigo-800 text-[10px] font-black uppercase hover:underline">Detail</button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-16 text-center">
                            <p class="text-slate-400 font-medium italic">Tidak ada data tagihan sesuai filter.</p>
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

    {{-- MODAL DETAIL --}}
    @if($showDetailModal && $selectedTagihan)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 animate-in fade-in">
        <div class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-2xl overflow-hidden border border-white/20">
            <div class="bg-[#002855] px-8 py-6 text-white flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-black uppercase tracking-widest leading-none">Rincian Keuangan</h3>
                    <p class="text-[10px] font-bold uppercase opacity-60 mt-1">{{ $selectedTagihan->mahasiswa->person->nama_lengkap }} ({{ $selectedTagihan->mahasiswa->nim }})</p>
                </div>
                <button wire:click="closeDetail" class="text-white/70 hover:text-white text-2xl font-bold">&times;</button>
            </div>
            
            <div class="p-8 space-y-6 max-h-[70vh] overflow-y-auto custom-scrollbar">
                {{-- Detail Item --}}
                <div class="bg-slate-50 p-5 rounded-2xl border border-slate-100">
                    <h4 class="text-xs font-black text-[#002855] uppercase tracking-widest mb-3 border-b border-slate-200 pb-2">Komponen Biaya</h4>
                    <div class="space-y-2">
                        @foreach($selectedTagihan->rincian_item ?? [] as $item)
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-600 font-medium">{{ $item['nama'] }}</span>
                            <span class="font-bold text-slate-800">Rp {{ number_format($item['nominal'], 0, ',', '.') }}</span>
                        </div>
                        @endforeach
                        <div class="flex justify-between text-sm pt-2 border-t border-slate-200 mt-2">
                            <span class="font-black text-[#002855]">TOTAL TAGIHAN</span>
                            <span class="font-black text-[#002855]">Rp {{ number_format($selectedTagihan->total_tagihan, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                {{-- Histori Pembayaran --}}
                <div>
                    <h4 class="text-xs font-black text-[#002855] uppercase tracking-widest mb-3">Histori Pembayaran</h4>
                    <div class="space-y-3">
                        @forelse($selectedTagihan->pembayarans as $bayar)
                        <div class="flex items-center justify-between p-4 bg-white border border-slate-200 rounded-xl">
                            <div>
                                <p class="text-xs font-bold text-slate-500">{{ \Carbon\Carbon::parse($bayar->tanggal_bayar)->format('d F Y') }}</p>
                                <p class="text-[10px] text-slate-400 uppercase tracking-wider">{{ $bayar->metode_pembayaran }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-black text-emerald-600">+ Rp {{ number_format($bayar->nominal_bayar, 0, ',', '.') }}</p>
                                <span class="text-[9px] font-bold uppercase px-2 py-0.5 rounded {{ $bayar->status_verifikasi == 'VERIFIED' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                    {{ $bayar->status_verifikasi }}
                                </span>
                            </div>
                        </div>
                        @empty
                        <p class="text-center text-xs text-slate-400 italic">Belum ada pembayaran masuk.</p>
                        @endforelse
                    </div>
                </div>
            </div>
            <div class="p-6 border-t border-slate-100 bg-slate-50 text-right">
                <button wire:click="closeDetail" class="px-6 py-2 bg-white border border-slate-300 text-slate-600 rounded-xl text-xs font-black uppercase hover:bg-slate-100 transition-all">Tutup</button>
            </div>
        </div>
    </div>
    @endif
</div>