<div class="space-y-8 animate-in fade-in duration-500 max-w-[1600px] mx-auto p-4 md:p-8">
    
    {{-- Header Section --}}
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
        <div class="space-y-2">
            <h1 class="text-3xl font-black text-[#002855] tracking-tight flex items-center gap-3">
                <div class="w-12 h-12 bg-[#002855] rounded-2xl flex items-center justify-center text-emerald-400 shadow-xl shadow-blue-900/20">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                </div>
                Financial Command Center
            </h1>
            <p class="text-slate-400 font-bold text-sm ml-1 uppercase tracking-widest italic italic">Pemantauan Likuiditas & Audit Piutang Universitas</p>
        </div>
        
        <button wire:click="exportLaporan" wire:loading.attr="disabled" class="group inline-flex items-center px-8 py-4 bg-emerald-600 text-white rounded-2xl font-black text-[10px] uppercase tracking-[0.2em] shadow-xl shadow-emerald-500/20 hover:bg-emerald-700 transition-all">
            <svg wire:loading.remove wire:target="exportLaporan" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
            <svg wire:loading wire:target="exportLaporan" class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
            Cetak Audit Excel
        </button>
    </div>

    {{-- EXECUTIVE DASHBOARD: Logika 5 Pilar Akuntansi --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
        {{-- Card 1: Omzet Bruto --}}
        <div class="bg-white p-7 rounded-[2.5rem] shadow-sm border border-slate-200 relative overflow-hidden group">
            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Kontrak Penagihan (Bruto)</p>
            <h3 class="text-xl font-black text-[#002855] mt-2 italic tracking-tighter">Rp {{ number_format($globalStats['bruto'], 0, ',', '.') }}</h3>
            <p class="text-[8px] font-bold text-slate-400 mt-2">Nilai awal seluruh tagihan</p>
        </div>

        {{-- Card 2: Cash In Riil (LOKER/BANK) --}}
        <div class="bg-[#002855] p-7 rounded-[2.5rem] shadow-xl shadow-blue-900/20 text-white relative border-2 border-emerald-400/20 group">
            <div class="absolute -right-4 -top-4 opacity-5 group-hover:opacity-10 transition-opacity"><svg class="w-24 h-24" fill="currentColor" viewBox="0 0 20 20"><path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4s0-2 2-2z" /></svg></div>
            <p class="text-[9px] font-black text-[#fcc000] uppercase tracking-widest">Realisasi Kas (Cash In)</p>
            <h3 class="text-xl font-black mt-2 italic text-emerald-400 tracking-tighter">Rp {{ number_format($globalStats['cash_in'], 0, ',', '.') }}</h3>
            <div class="mt-3 w-full bg-white/10 rounded-full h-1.5 overflow-hidden shadow-inner">
                <div class="bg-emerald-400 h-full transition-all duration-1000" style="width: {{ $globalStats['collection_rate'] }}%"></div>
            </div>
        </div>

        {{-- Card 3: Pelunasan Internal (BEASISWA/SALDO) --}}
        <div class="bg-white p-7 rounded-[2.5rem] shadow-sm border border-indigo-100 group">
            <p class="text-[9px] font-black text-indigo-400 uppercase tracking-widest">Pelunasan via Saldo</p>
            <h3 class="text-xl font-black text-indigo-600 mt-2 italic tracking-tighter">Rp {{ number_format($globalStats['paid_internal'], 0, ',', '.') }}</h3>
            <p class="text-[8px] font-bold text-slate-300 mt-2 uppercase italic">*Pemotongan deposit MHS</p>
        </div>

        {{-- Card 4: Piutang Bersih --}}
        <div class="bg-white p-7 rounded-[2.5rem] shadow-sm border border-slate-200">
            <p class="text-[9px] font-black text-rose-400 uppercase tracking-widest">Sisa Piutang Aktif</p>
            <h3 class="text-xl font-black text-rose-600 mt-2 italic tracking-tighter">Rp {{ number_format($globalStats['debt'], 0, ',', '.') }}</h3>
            <p class="text-[8px] font-bold text-slate-400 mt-2 uppercase">*Hutang riil yang ditunggu</p>
        </div>

        {{-- Card 5: Liability (Saldo Mengendap) --}}
        <div class="bg-blue-50 p-7 rounded-[2.5rem] shadow-sm border border-blue-100">
            <p class="text-[9px] font-black text-blue-500 uppercase tracking-widest">Total Sisa Deposit</p>
            <h3 class="text-xl font-black text-blue-700 mt-2 italic tracking-tighter">Rp {{ number_format($globalStats['deposit'], 0, ',', '.') }}</h3>
            <p class="text-[8px] font-bold text-blue-400 mt-2 uppercase italic">*Dana lebih mahasiswa</p>
        </div>
    </div>

    {{-- ADVANCED FILTERS PANEL --}}
    <div class="bg-white p-6 rounded-[2.5rem] shadow-sm border border-slate-200 grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
        <div>
            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 pl-1">Periode Penagihan</label>
            <select wire:model.live="semesterId" class="w-full rounded-2xl border-slate-200 bg-slate-50 text-xs font-bold text-[#002855] py-3.5 focus:ring-[#fcc000]">
                <option value="all">Seluruh Periode</option>
                <option value="legacy" class="text-amber-600 font-black italic">Tunggakan Historis (Legacy)</option>
                @foreach($semesters as $sem) <option value="{{ $sem->id }}">{{ $sem->nama_tahun }}</option> @endforeach
            </select>
        </div>
        <div>
            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 pl-1">Program Studi</label>
            <select wire:model.live="filterProdiId" class="w-full rounded-2xl border-slate-200 bg-slate-50 text-xs font-bold text-slate-700 py-3.5 focus:ring-[#fcc000]">
                <option value="">Seluruh Prodi</option>
                @foreach($prodis as $p) <option value="{{ $p->id }}">{{ $p->nama_prodi }}</option> @endforeach
            </select>
        </div>
        <div>
            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 pl-1">Status Neraca</label>
            <select wire:model.live="filterStatus" class="w-full rounded-2xl border-slate-200 bg-slate-50 text-xs font-bold text-slate-700 py-3.5 focus:ring-[#fcc000]">
                <option value="">Seluruh Status</option>
                <option value="LUNAS">Lunas Sempurna</option>
                <option value="CICIL">Menunggak (Sebagian)</option>
                <option value="BELUM">Belum Ada Bayar</option>
                <option value="LEBIH_BAYAR">Kelebihan (Surplus)</option>
            </select>
        </div>
        <div>
            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 pl-1">Pencarian NIM/Nama</label>
            <div class="relative">
                <input type="text" wire:model.live.debounce.500ms="search" placeholder="Cari..." class="w-full rounded-2xl border-slate-200 bg-white text-xs font-bold py-3.5 pl-11 focus:ring-[#002855] shadow-sm">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
            </div>
        </div>
    </div>

    {{-- MAIN DATA TABLE --}}
    <div class="bg-white rounded-[3rem] shadow-sm border border-slate-200 overflow-hidden relative">
        <div wire:loading.flex class="absolute inset-0 bg-white/60 z-20 items-center justify-center backdrop-blur-[2px]">
            <svg class="animate-spin h-8 w-8 text-[#002855]" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-[#002855] text-white">
                    <tr>
                        <th class="px-8 py-5 text-left text-[10px] font-bold uppercase tracking-widest whitespace-nowrap">Mahasiswa</th>
                        <th class="px-6 py-5 text-right text-[10px] font-bold uppercase tracking-widest whitespace-nowrap">Bruto</th>
                        <th class="px-6 py-5 text-right text-[10px] font-bold uppercase tracking-widest whitespace-nowrap">Koreksi</th>
                        <th class="px-6 py-5 text-right text-[10px] font-bold uppercase tracking-widest whitespace-nowrap bg-indigo-900/50">Wajib Bayar</th>
                        <th class="px-6 py-5 text-right text-[10px] font-bold uppercase tracking-widest whitespace-nowrap text-emerald-300">Total Bayar</th>
                        <th class="px-6 py-5 text-right text-[10px] font-bold uppercase tracking-widest whitespace-nowrap">Sisa Piutang</th>
                        <th class="px-6 py-5 text-center text-[10px] font-bold uppercase tracking-widest whitespace-nowrap">Status</th>
                        <th class="px-8 py-5 text-right text-[10px] font-bold uppercase tracking-widest whitespace-nowrap">Audit</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($tagihans as $tagihan)
                    @php
                        $totalKoreksi = $tagihan->adjustments->sum('nominal');
                        $tagihanAwal = $tagihan->total_tagihan;
                        $netto = max(0, $tagihanAwal - $totalKoreksi);
                        $sisa = $netto - $tagihan->total_bayar;
                        
                        if ($netto <= 0) { $statusTampil = 'LUNAS'; } 
                        elseif ($sisa <= 0 && $tagihan->total_bayar > 0) { $statusTampil = 'LUNAS'; } 
                        elseif ($sisa < 0) { $statusTampil = 'SURPLUS'; } 
                        elseif ($tagihan->total_bayar > 0) { $statusTampil = 'CICIL'; } 
                        else { $statusTampil = 'BELUM'; }
                    @endphp
                    <tr class="hover:bg-slate-50/80 transition-colors group text-xs font-medium">
                        <td class="px-8 py-5 whitespace-nowrap align-top">
                            <div class="font-black text-[#002855] uppercase tracking-tight">{{ $tagihan->mahasiswa->person->nama_lengkap ?? '-' }}</div>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="font-mono font-bold bg-slate-100 px-2 py-0.5 rounded text-slate-500 border border-slate-200 uppercase" style="font-size: 8px;">{{ $tagihan->mahasiswa->nim }}</span>
                                @if(!$tagihan->tahun_akademik_id) <span class="bg-amber-50 text-amber-600 px-1.5 py-0.5 rounded uppercase font-black" style="font-size: 8px;">Legacy</span> @endif
                            </div>
                        </td>
                        <td class="px-6 py-5 text-right text-slate-400 align-top">{{ number_format($tagihanAwal, 0, ',', '.') }}</td>
                        <td class="px-6 py-5 text-right align-top">
                            @if($totalKoreksi > 0) <span class="font-black text-indigo-500 bg-indigo-50 px-2 py-1 rounded-lg border border-indigo-100 italic">- {{ number_format($totalKoreksi, 0, ',', '.') }}</span> @else <span class="text-slate-200">-</span> @endif
                        </td>
                        <td class="px-6 py-5 text-right font-black text-[#002855] align-top bg-indigo-50/20">{{ number_format($netto, 0, ',', '.') }}</td>
                        <td class="px-6 py-5 text-right font-black text-emerald-600 align-top">{{ number_format($tagihan->total_bayar, 0, ',', '.') }}</td>
                        <td class="px-6 py-5 text-right font-black align-top {{ $sisa > 0 ? 'text-rose-600' : 'text-slate-300' }}">{{ number_format(max(0, $sisa), 0, ',', '.') }}</td>
                        <td class="px-6 py-5 text-center align-top">
                            @if($statusTampil == 'LUNAS') <span class="px-3 py-1 bg-emerald-50 text-emerald-700 rounded-full font-black uppercase tracking-widest border border-emerald-200" style="font-size: 8px;">LUNAS</span>
                            @elseif($statusTampil == 'SURPLUS') <span class="px-3 py-1 bg-blue-50 text-blue-700 rounded-full font-black uppercase tracking-widest border border-blue-200 animate-pulse" style="font-size: 8px;">SURPLUS</span>
                            @elseif($statusTampil == 'CICIL') <span class="px-3 py-1 bg-amber-50 text-amber-700 rounded-full font-black uppercase tracking-widest border border-amber-200" style="font-size: 8px;">CICIL</span>
                            @else <span class="px-3 py-1 bg-rose-50 text-rose-600 rounded-full font-black uppercase tracking-widest border border-rose-100" style="font-size: 8px;">BELUM BAYAR</span> @endif
                        </td>
                        <td class="px-8 py-5 text-right align-top">
                            <button wire:click="openDetail('{{ $tagihan->id }}')" class="p-2 bg-slate-50 text-slate-400 hover:bg-[#002855] hover:text-[#fcc000] rounded-xl transition-all border border-slate-100"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg></button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="py-24 text-center text-slate-400 italic font-bold">Tidak ada data keuangan yang ditemukan</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-8 py-5 bg-slate-50/50 border-t border-slate-100">{{ $tagihans->links() }}</div>
    </div>

    {{-- AUDIT MODAL --}}
    @if($showDetailModal && $selectedTagihan)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/70 backdrop-blur-md p-4 animate-in fade-in duration-300">
        <div class="bg-white rounded-[3rem] shadow-2xl w-full max-w-4xl overflow-hidden flex flex-col max-h-[90vh] animate-in zoom-in-95 border border-white/20">
            <div class="bg-[#002855] px-10 py-8 text-white flex justify-between items-start shrink-0 relative overflow-hidden">
                <div class="absolute right-0 top-0 p-8 opacity-10 pointer-events-none"><svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24"><path d="M12 14l9-5-9-5-9 5 9 5z"/><path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg></div>
                <div class="relative z-10">
                    <h3 class="text-2xl font-black uppercase tracking-tight italic text-[#fcc000]">Financial Audit Log</h3>
                    <p class="text-xs font-bold uppercase tracking-widest mt-2 opacity-80">{{ $selectedTagihan->mahasiswa->person->nama_lengkap }} • {{ $selectedTagihan->mahasiswa->nim }}</p>
                </div>
                <button wire:click="closeDetail" class="relative z-10 p-3 bg-white/10 hover:bg-rose-500 text-white rounded-2xl transition-all"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg></button>
            </div>
            <div class="flex-1 overflow-y-auto custom-scrollbar p-10 bg-slate-50 space-y-10">
                <div class="relative border-l-[3px] border-slate-200 ml-4 space-y-10 pb-6">
                    @foreach($auditTimeline as $log)
                    <div class="ml-10 relative group">
                        <div class="absolute -left-[54px] top-0 h-7 w-7 rounded-full border-4 border-slate-50 shadow-md flex items-center justify-center
                            {{ $log['type'] == 'BILL_CREATED' ? 'bg-slate-400' : ($log['type'] == 'ADJUSTMENT' ? 'bg-amber-500' : ($log['type'] == 'PAYMENT' ? 'bg-emerald-500' : 'bg-blue-500')) }}">
                            @if($log['type'] == 'ADJUSTMENT') <span class="text-[10px] text-white font-black">!</span> @elseif($log['type'] == 'PAYMENT') <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> @endif
                        </div>
                        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm group-hover:shadow-xl transition-all duration-300">
                            <div class="flex justify-between items-start mb-2 pr-6">
                                <div>
                                    <span class="text-[10px] font-black uppercase tracking-widest {{ $log['type'] == 'ADJUSTMENT' ? 'text-amber-600' : ($log['type'] == 'PAYMENT' ? 'text-emerald-600' : 'text-slate-600') }}">{{ $log['title'] }}</span>
                                    <p class="text-[9px] text-slate-400 font-bold uppercase">{{ $log['date']->format('d M Y H:i') }} WITA</p>
                                </div>
                                <span class="text-sm font-black {{ $log['amount'] < 0 ? 'text-emerald-600' : 'text-slate-700' }}">{{ $log['amount'] < 0 ? '+' : '' }} Rp {{ number_format(abs($log['amount']), 0, ',', '.') }}</span>
                            </div>
                            <p class="text-xs text-slate-500 leading-relaxed italic">"{{ $log['desc'] }}"</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            
            <div class="bg-slate-50 p-8 border-t border-slate-200 flex justify-end">
                <div class="bg-[#002855] px-10 py-5 rounded-2xl border border-white/10 shadow-2xl">
                    @php
                        $kModal = $selectedTagihan->adjustments->sum('nominal');
                        $nModal = max(0, $selectedTagihan->total_tagihan - $kModal);
                        $sModal = $nModal - $selectedTagihan->total_bayar;
                    @endphp
                    <p class="text-[9px] font-black text-white/40 uppercase tracking-widest mb-1">Status Neraca Final</p>
                    @if($sModal > 0)
                        <span class="block text-2xl font-black text-rose-400 italic">KURANG: Rp {{ number_format($sModal, 0, ',', '.') }}</span>
                    @elseif($sModal < 0)
                        <span class="block text-2xl font-black text-blue-300 italic">SURPLUS: Rp {{ number_format(abs($sModal), 0, ',', '.') }}</span>
                    @else
                        <span class="block text-2xl font-black text-emerald-400 italic uppercase">LUNAS SEMPURNA</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif
</div>