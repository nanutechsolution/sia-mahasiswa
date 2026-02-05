<div class="space-y-8 animate-in fade-in duration-500">
    
    {{-- TITLE & TOOLS --}}
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
        <div>
            <h1 class="text-3xl font-black text-[#002855] tracking-tight">Financial Command Center</h1>
            <p class="text-slate-500 font-medium text-sm mt-1">
                Laporan real-time, analisis performa prodi, manajemen tunggakan, dan audit refund.
            </p>
        </div>
        <div>
            <button wire:click="exportLaporan" wire:loading.attr="disabled" class="group inline-flex items-center rounded-xl bg-emerald-600 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-emerald-600/20 hover:bg-emerald-700 hover:scale-105 transition-all">
                <svg wire:loading.remove wire:target="exportLaporan" class="w-5 h-5 mr-2 group-hover:animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <svg wire:loading wire:target="exportLaporan" class="w-5 h-5 mr-2 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                Unduh Data Audit (CSV)
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
                </p>
            </div>
        </div>

        <!-- 4. Anomali -->
        <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 flex flex-col justify-between relative overflow-hidden">
            <div>
                <p class="text-xs font-black text-blue-500 uppercase tracking-widest">Saldo Lebih / Refund</p>
                @if($globalStats['anomali_count'] > 0)
                    <h3 class="text-2xl font-black text-blue-600 mt-2 tracking-tight">{{ $globalStats['anomali_count'] }} Akun</h3>
                    <div class="mt-2 text-xs text-slate-500 font-medium">Potensi kasus Beasiswa/Lebih Bayar.</div>
                @else
                    <div class="mt-2 flex items-center text-emerald-600 gap-2">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="font-bold text-sm">Data Bersih</span>
                    </div>
                @endif
            </div>
            <button wire:click="$set('filterStatus', 'LEBIH_BAYAR')" class="mt-4 w-full py-2 bg-blue-50 text-blue-700 text-[10px] font-bold uppercase rounded-lg hover:bg-blue-100 transition-colors">
                Audit Data Refund
            </button>
        </div>
    </div>

    {{-- Filter Panel (Advanced) --}}
    <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-200 grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
        <div class="md:col-span-1">
            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Periode</label>
            <select wire:model.live="semesterId" class="w-full rounded-xl border-slate-200 bg-slate-50 text-xs font-bold py-2.5 focus:ring-[#002855]">
                @foreach($semesters as $sem) <option value="{{ $sem->id }}">{{ $sem->nama_tahun }} {{ $sem->is_active ? '(Aktif)' : '' }}</option> @endforeach
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
                <option value="CICIL">Menunggak</option>
                <option value="LEBIH_BAYAR">Anomali (Lebih Bayar)</option>
            </select>
        </div>
        <div class="md:col-span-2">
            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Pencarian Cepat</label>
            <input type="text" wire:model.live.debounce.500ms="search" placeholder="Cari Nama Mahasiswa atau NIM..." class="w-full rounded-xl border-slate-200 bg-slate-50 text-xs font-bold py-2.5 pl-4 focus:ring-[#002855]">
        </div>
    </div>

    {{-- Main Table --}}
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-[#002855] text-white">
                    <tr>
                        <th class="px-6 py-4 text-left text-[10px] font-bold uppercase tracking-widest">Identitas</th>
                        <th class="px-6 py-4 text-left text-[10px] font-bold uppercase tracking-widest">Akademik</th>
                        <th class="px-6 py-4 text-right text-[10px] font-bold uppercase tracking-widest">Tagihan Awal</th>
                        <th class="px-6 py-4 text-right text-[10px] font-bold uppercase tracking-widest">Koreksi</th>
                        <th class="px-6 py-4 text-right text-[10px] font-bold uppercase tracking-widest">Netto</th>
                        <th class="px-6 py-4 text-right text-[10px] font-bold uppercase tracking-widest">Terbayar</th>
                        <th class="px-6 py-4 text-right text-[10px] font-bold uppercase tracking-widest">Sisa</th>
                        <th class="px-6 py-4 text-center text-[10px] font-bold uppercase tracking-widest">Status</th>
                        <th class="px-6 py-4 text-right text-[10px] font-bold uppercase tracking-widest">Detail</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($tagihans as $tagihan)
                    {{-- [ON-THE-FLY CALCULATION] Agar tampilan konsisten 100% --}}
                    @php
                        $koreksi = $tagihan->adjustments->sum('nominal');
                        $tagihanAwal = $tagihan->total_tagihan; // Base
                        $netto = max(0, $tagihanAwal - $koreksi);
                        $sisaReal = max(0, $netto - $tagihan->total_bayar);
                        
                        // Status override untuk visual
                        $statusTampil = $tagihan->status_bayar;
                        if($sisaReal <= 0 && $tagihan->total_bayar > 0) $statusTampil = 'LUNAS';
                    @endphp

                    <tr class="hover:bg-slate-50/80 transition-colors group">
                        <td class="px-6 py-4 whitespace-nowrap align-top">
                            <div class="text-sm font-black text-slate-800">{{ $tagihan->mahasiswa->person->nama_lengkap ?? '-' }}</div>
                            <div class="text-[10px] font-mono text-slate-400 mt-0.5">{{ $tagihan->mahasiswa->nim }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap align-top">
                            <div class="text-xs font-bold text-slate-600 uppercase">{{ $tagihan->mahasiswa->prodi->kode_prodi_internal }}</div>
                            <span class="inline-flex mt-1 text-[9px] font-bold text-[#002855] bg-indigo-50 px-1.5 py-0.5 rounded">{{ $tagihan->mahasiswa->programKelas->nama_program }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-slate-500 font-medium align-top">
                            {{ number_format($tagihanAwal, 0, ',', '.') }}
                        </td>
                        
                        {{-- Kolom Koreksi --}}
                        <td class="px-6 py-4 whitespace-nowrap text-right align-top">
                            @if($koreksi > 0)
                                <span class="text-xs font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded cursor-help" title="Beasiswa/Potongan">
                                    - {{ number_format($koreksi, 0, ',', '.') }}
                                </span>
                            @else
                                <span class="text-xs text-slate-300">-</span>
                            @endif
                        </td>
                        
                        {{-- Kolom Netto (Wajib Bayar) --}}
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-slate-800 font-bold align-top bg-slate-50/50">
                            {{ number_format($netto, 0, ',', '.') }}
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-emerald-600 font-bold align-top">
                            {{ number_format($tagihan->total_bayar, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm {{ $sisaReal > 0 ? 'text-rose-600' : 'text-slate-400' }} font-black align-top">
                            {{ number_format($sisaReal, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center align-top">
                            @if($statusTampil == 'LUNAS')
                                <span class="px-2.5 py-0.5 inline-flex text-[9px] font-black uppercase rounded-full bg-emerald-100 text-emerald-700">LUNAS</span>
                            @elseif($tagihan->total_bayar > $netto)
                                <span class="px-2.5 py-0.5 rounded-full text-[9px] font-black bg-blue-100 text-blue-700 uppercase animate-pulse">LEBIH</span>
                            @elseif($tagihan->total_bayar > 0)
                                <span class="px-2.5 py-0.5 inline-flex text-[9px] font-black uppercase rounded-full bg-amber-100 text-amber-700">CICIL</span>
                            @else
                                <span class="px-2.5 py-0.5 inline-flex text-[9px] font-black uppercase rounded-full bg-rose-100 text-rose-700">BELUM</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right align-top">
                            <button wire:click="openDetail('{{ $tagihan->id }}')" class="text-[#002855] hover:text-[#fcc000] text-[10px] font-black uppercase hover:underline flex items-center justify-end gap-1 ml-auto">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                AUDIT
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="px-6 py-20 text-center text-slate-400 italic">Data tidak ditemukan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">{{ $tagihans->links() }}</div>
    </div>

    {{-- MODAL AUDIT TRAIL (Timeline Style) --}}
    @if($showDetailModal && $selectedTagihan)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 animate-in fade-in duration-200">
        <div class="bg-white rounded-[2rem] shadow-2xl w-full max-w-3xl overflow-hidden border border-white/20 flex flex-col max-h-[90vh]">
            
            {{-- Modal Header --}}
            <div class="bg-[#002855] px-8 py-6 text-white flex justify-between items-start shrink-0">
                <div>
                    <h3 class="text-xl font-black uppercase tracking-widest leading-none">Audit Trail Keuangan</h3>
                    <div class="flex items-center gap-2 mt-2 opacity-80">
                        <span class="text-xs font-bold">{{ $selectedTagihan->mahasiswa->person->nama_lengkap }}</span>
                        <span class="text-xs">&bull;</span>
                        <span class="text-xs font-mono bg-white/20 px-1.5 py-0.5 rounded">{{ $selectedTagihan->mahasiswa->nim }}</span>
                    </div>
                </div>
                <button wire:click="closeDetail" class="text-white/50 hover:text-white transition-colors">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            
            <div class="flex-1 overflow-y-auto custom-scrollbar p-8 bg-slate-50">
                
                <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-6 border-b border-slate-200 pb-2 flex justify-between">
                    <span>Kronologi Transaksi</span>
                    <span>Waktu Server (WITA)</span>
                </h4>

                {{-- TIMELINE VISUALIZATION --}}
                <div class="relative border-l-2 border-slate-200 ml-4 space-y-8 pb-4">
                    @foreach($auditTimeline as $log)
                    <div class="ml-8 relative group">
                        {{-- Dot Indicator --}}
                        <div class="absolute -left-[41px] top-0 h-6 w-6 rounded-full border-4 border-white shadow-sm flex items-center justify-center
                            {{ $log['type'] == 'BILL_CREATED' ? 'bg-slate-500' : 
                              ($log['type'] == 'ADJUSTMENT' ? 'bg-[#fcc000]' : 
                              ($log['type'] == 'PAYMENT' ? 'bg-emerald-500' : 'bg-blue-500')) }}">
                            @if($log['type'] == 'ADJUSTMENT') <span class="text-[8px] text-[#002855] font-black">!</span>
                            @elseif($log['type'] == 'PAYMENT') <span class="text-[8px] text-white font-black">✓</span>
                            @endif
                        </div>
                        
                        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm group-hover:shadow-md group-hover:border-indigo-100 transition-all">
                            <div class="flex justify-between items-start mb-2">
                                <div class="flex flex-col">
                                    <span class="text-[10px] font-black uppercase tracking-widest 
                                        {{ $log['type'] == 'ADJUSTMENT' ? 'text-amber-600' : 
                                          ($log['type'] == 'PAYMENT' ? 'text-emerald-600' : 
                                          ($log['type'] == 'WALLET_IN' ? 'text-blue-600' : 'text-slate-600')) }}">
                                        {{ $log['title'] }}
                                    </span>
                                    <span class="text-[10px] text-slate-400 font-medium mt-0.5">
                                        {{ $log['date']->format('d M Y') }} <span class="mx-1 text-slate-300">|</span> {{ $log['date']->format('H:i:s') }}
                                    </span>
                                </div>
                                @if($log['type'] != 'BILL_CREATED')
                                    <span class="text-sm font-black {{ $log['amount'] < 0 ? 'text-amber-600' : ($log['amount'] == 0 ? 'text-blue-600' : 'text-emerald-600') }}">
                                        {{ $log['amount'] < 0 ? '-' : ($log['amount'] > 0 ? '+' : '') }} Rp {{ number_format(abs($log['amount']), 0, ',', '.') }}
                                    </span>
                                @else
                                    <span class="text-sm font-black text-slate-600">
                                        Tagihan Awal: Rp {{ number_format($log['amount'], 0, ',', '.') }}
                                    </span>
                                @endif
                            </div>
                            
                            <div class="bg-slate-50 rounded-lg p-3 border border-slate-100 mt-2">
                                <div class="flex items-start gap-2 mb-2">
                                    <svg class="w-4 h-4 text-slate-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    <div class="text-xs text-slate-600 leading-relaxed">
                                        {{ $log['desc'] }}
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 pt-2 border-t border-slate-200">
                                    <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wide">
                                        PIC / Aktor: <span class="text-[#002855]">{{ $log['user'] }}</span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- Status Akhir --}}
                <div class="mt-8 bg-[#002855] p-5 rounded-2xl text-white shadow-lg flex justify-between items-center">
                    <div>
                        <p class="text-[10px] font-bold text-[#fcc000] uppercase tracking-widest">Status Keuangan Akhir</p>
                        <p class="text-xs opacity-70 mt-1">Berdasarkan seluruh transaksi di atas.</p>
                    </div>
                    <div class="text-right">
                        @php
                            // Perhitungan Manual untuk Modal (Sinkron dengan tabel utama)
                            $koreksiModal = $selectedTagihan->adjustments->sum('nominal');
                            $nettoModal = max(0, $selectedTagihan->total_tagihan - $koreksiModal);
                            $sisaModal = max(0, $nettoModal - $selectedTagihan->total_bayar);
                        @endphp
                        
                        @if($sisaModal > 0)
                            <span class="block text-2xl font-black text-rose-400">KURANG BAYAR</span>
                            <span class="text-sm font-bold">Rp {{ number_format($sisaModal, 0, ',', '.') }}</span>
                        @elseif($selectedTagihan->total_bayar > $nettoModal)
                            <span class="block text-2xl font-black text-blue-300">LEBIH BAYAR (SALDO)</span>
                        @else
                            <span class="block text-2xl font-black text-emerald-400">LUNAS</span>
                            <span class="text-sm font-bold">Rp 0</span>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="p-6 border-t border-slate-200 bg-white shrink-0 flex justify-end">
                <button wire:click="closeDetail" class="px-8 py-2.5 bg-slate-100 text-slate-600 rounded-xl text-xs font-black uppercase hover:bg-slate-200 transition-all border border-slate-300">
                    Tutup Laporan
                </button>
            </div>
        </div>
    </div>
    @endif
</div>