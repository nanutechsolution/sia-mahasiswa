<div class="space-y-8 animate-in fade-in duration-500 max-w-[1600px] mx-auto p-4 md:p-8">
    
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div class="space-y-2">
            <h1 class="text-3xl font-black text-[#002855] tracking-tight flex items-center gap-3">
                <div class="w-12 h-12 bg-[#002855] rounded-2xl flex items-center justify-center text-[#fcc000] shadow-xl shadow-blue-900/20">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" /></svg>
                </div>
                Koreksi & Refund
            </h1>
            <p class="text-slate-400 font-medium text-sm mt-1 uppercase tracking-widest italic">
                Kendali operasional penyesuaian tagihan dan pencairan deposit.
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-8">
        
        {{-- PANEL KIRI: PENCARIAN --}}
        <div class="xl:col-span-4 space-y-6">
            <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-200 relative z-20">
                <label class="block text-xs font-black text-[#002855] uppercase tracking-widest mb-3 border-l-[3px] border-[#fcc000] pl-3">Cari Mahasiswa</label>
                <div class="relative">
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Ketik NIM atau Nama..." 
                        class="block w-full rounded-2xl border-slate-200 bg-slate-50 py-4 pl-12 pr-4 text-sm font-bold focus:ring-[#002855] focus:border-[#002855] transition-all outline-none">
                    <svg class="w-5 h-5 text-slate-400 absolute left-4 top-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                
                {{-- Dropdown Hasil Pencarian --}}
                @if(!empty($searchResults))
                <div class="absolute left-0 right-0 mt-3 bg-white rounded-2xl shadow-2xl border border-slate-100 overflow-hidden z-30 mx-8 animate-in slide-in-from-top-2">
                    @foreach($searchResults as $res)
                        <button wire:click="selectMahasiswa('{{ $res->id }}')" class="w-full flex items-center gap-4 px-6 py-4 hover:bg-indigo-50 border-b border-slate-50 last:border-0 transition-colors group text-left">
                            <div class="w-10 h-10 bg-slate-100 text-[#002855] rounded-xl flex items-center justify-center font-black text-xs uppercase shadow-inner">{{ substr($res->person->nama_lengkap ?? $res->nama_lengkap, 0, 1) }}</div>
                            <div>
                                <p class="text-sm font-bold text-[#002855] group-hover:text-indigo-700 tracking-tight uppercase">{{ $res->person->nama_lengkap ?? $res->nama_lengkap }}</p>
                                <p class="text-[10px] text-slate-500 font-bold tracking-widest uppercase mt-0.5">{{ $res->nim }} • {{ $res->prodi->kode_prodi_internal }}</p>
                            </div>
                        </button>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- Profil Mahasiswa Terpilih --}}
            @if($mahasiswa)
            <div class="bg-white p-8 rounded-[3rem] shadow-lg border border-slate-200 text-center relative overflow-hidden animate-in slide-in-from-left-4">
                <div class="absolute top-0 left-0 w-full h-28 bg-[#002855]">
                    <div class="absolute top-0 right-0 p-4 opacity-10">
                        <svg class="w-24 h-24 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 14l9-5-9-5-9 5 9 5z"/><path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                    </div>
                </div>
                <div class="relative z-10 mt-4">
                    <div class="h-28 w-28 rounded-full bg-white p-1.5 mx-auto shadow-2xl">
                        <div class="h-full w-full rounded-full bg-[#fcc000] flex items-center justify-center text-[#002855] text-4xl font-black border-4 border-white">
                            {{ substr($mahasiswa->person->nama_lengkap ?? 'M', 0, 1) }}
                        </div>
                    </div>
                    <h2 class="mt-5 text-xl font-black text-[#002855] leading-tight uppercase tracking-tight">{{ $mahasiswa->person->nama_lengkap ?? '-' }}</h2>
                    <p class="text-sm font-mono font-bold text-slate-400 mt-1">{{ $mahasiswa->nim }}</p>
                    <div class="mt-4 inline-flex px-4 py-1.5 rounded-xl bg-indigo-50 text-indigo-700 text-[10px] font-black uppercase tracking-widest border border-indigo-100">
                        {{ $mahasiswa->prodi->nama_prodi }}
                    </div>
                </div>
            </div>
            @endif
        </div>

        {{-- PANEL KANAN: WORKSPACE --}}
        <div class="xl:col-span-8 space-y-8">
            @if($mahasiswa)
                
                {{-- 1. WALLET CARD (DOMPET) --}}
                <div class="bg-gradient-to-br from-[#002855] to-[#001a38] p-8 md:p-10 rounded-[3rem] shadow-2xl text-white relative overflow-hidden animate-in fade-in group">
                    <div class="absolute right-0 top-0 p-6 opacity-10 group-hover:opacity-20 transition-opacity duration-500">
                        <svg class="w-48 h-48" fill="currentColor" viewBox="0 0 20 20"><path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4s0-2 2-2z" /></svg>
                    </div>
                    
                    <div class="relative z-10 flex flex-col md:flex-row justify-between items-center gap-6">
                        <div>
                            <p class="text-xs font-bold text-[#fcc000] uppercase tracking-widest mb-2 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                Saldo Deposit Terkini
                            </p>
                            <h3 class="text-5xl font-black tracking-tighter italic">Rp {{ number_format($saldo->saldo, 0, ',', '.') }}</h3>
                            <p class="text-[11px] text-slate-300 mt-3 max-w-md leading-relaxed font-medium">
                                Saldo ini terbentuk otomatis dari kelebihan bayar atau beasiswa retroaktif. Dana ini dapat dicairkan (Refund) atau digunakan otomatis untuk semester depan.
                            </p>
                        </div>
                        @if($saldo->saldo > 0)
                        <button wire:click="openRefund" class="w-full md:w-auto px-8 py-4 bg-[#fcc000] text-[#002855] rounded-2xl font-black text-xs uppercase tracking-[0.2em] hover:bg-[#ffca28] hover:-translate-y-1 active:scale-95 transition-all shadow-xl shadow-orange-500/20 flex items-center justify-center gap-3 whitespace-nowrap">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                            Cairkan Dana
                        </button>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    {{-- 3. LIST TAGIHAN --}}
                    <div class="space-y-4">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-8 h-8 bg-indigo-100 text-indigo-600 flex items-center justify-center rounded-xl shadow-inner">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            </div>
                            <h3 class="text-xs font-black text-[#002855] uppercase tracking-[0.2em]">Daftar Tagihan</h3>
                        </div>
                        
                        <div class="space-y-4 max-h-[600px] overflow-y-auto custom-scrollbar pr-2">
                            @forelse($tagihans as $tagihan)
                            <div class="bg-white rounded-[2rem] shadow-sm border border-slate-200 overflow-hidden hover:border-[#002855]/30 transition-all hover:shadow-lg group">
                                <div class="p-6 md:p-8 space-y-6">
                                    <div>
                                        <div class="flex flex-wrap items-center gap-2 mb-3">
                                            <span class="text-[9px] font-black text-white bg-[#002855] px-2.5 py-1 rounded-md uppercase tracking-widest shadow-sm">{{ $tagihan->tahunAkademik->nama_tahun ?? 'Legacy' }}</span>
                                            <span class="text-[9px] font-mono font-bold text-slate-500 border border-slate-200 px-2.5 py-1 rounded-md">{{ $tagihan->kode_transaksi }}</span>
                                        </div>
                                        <h4 class="text-sm font-black text-slate-800 uppercase tracking-tight leading-snug group-hover:text-[#002855] transition-colors">{{ $tagihan->deskripsi }}</h4>
                                    </div>

                                    {{-- List Adjustment (Beasiswa) --}}
                                    @if($tagihan->adjustments->count() > 0)
                                    <div class="space-y-2 bg-slate-50 p-4 rounded-2xl border border-slate-100">
                                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2">Riwayat Koreksi Aktif:</p>
                                        @foreach($tagihan->adjustments as $adj)
                                        <div class="flex items-center text-xs text-slate-700 bg-white px-3 py-2 rounded-xl border border-slate-200 shadow-sm">
                                            <svg class="w-3.5 h-3.5 mr-2 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                            <span class="font-black mr-1">{{ $adj->jenis_adjustment }}:</span> 
                                            <span class="font-bold text-amber-600">- Rp {{ number_format($adj->nominal, 0, ',', '.') }}</span>
                                        </div>
                                        @endforeach
                                    </div>
                                    @endif

                                    {{-- LOGIKA HITUNG SISA VISUAL --}}
                                    @php
                                        $totalBayar = $tagihan->total_bayar;
                                        $totalKoreksi = $tagihan->adjustments->sum('nominal');
                                        $tagihanNet = max(0, $tagihan->total_tagihan - $totalKoreksi);
                                        $sisaDisplay = $tagihanNet - $totalBayar;
                                    @endphp

                                    <div class="flex items-end justify-between pt-4 border-t border-slate-100">
                                        <div>
                                            @if($sisaDisplay < 0)
                                                <p class="text-[9px] text-blue-500 font-black uppercase tracking-[0.2em] mb-1">Status: Lebih Bayar</p>
                                                <p class="text-xl font-black text-blue-600 italic">+ Rp {{ number_format(abs($sisaDisplay), 0, ',', '.') }}</p>
                                            @elseif($sisaDisplay == 0)
                                                <p class="text-[9px] text-emerald-500 font-black uppercase tracking-[0.2em] mb-1">Status Tagihan</p>
                                                <p class="text-xl font-black text-emerald-600 italic">LUNAS</p>
                                            @else
                                                <p class="text-[9px] text-slate-400 font-black uppercase tracking-[0.2em] mb-1">Sisa Kewajiban</p>
                                                <p class="text-xl font-black text-rose-600 italic">Rp {{ number_format($sisaDisplay, 0, ',', '.') }}</p>
                                            @endif
                                        </div>

                                        <button wire:click="openAdjustment('{{ $tagihan->id }}')" class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 hover:bg-[#002855] hover:text-white transition-all shadow-sm" title="Tambah Koreksi">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="p-10 text-center bg-white rounded-[2.5rem] border border-slate-200">
                                <p class="text-xs font-black text-slate-400 uppercase tracking-widest">Tidak ada tagihan</p>
                            </div>
                            @endforelse
                        </div>
                    </div>

                    {{-- 2. RIWAYAT MUTASI SALDO --}}
                    <div class="space-y-4">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-8 h-8 bg-emerald-100 text-emerald-600 flex items-center justify-center rounded-xl shadow-inner">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                            <h3 class="text-xs font-black text-[#002855] uppercase tracking-[0.2em]">Mutasi Dompet ({{ count($riwayatSaldo) }})</h3>
                        </div>

                        <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-200 overflow-hidden">
                            <div class="divide-y divide-slate-100 max-h-[600px] overflow-y-auto custom-scrollbar">
                                @forelse($riwayatSaldo as $log)
                                <div class="px-6 py-5 flex justify-between items-center hover:bg-slate-50 transition-colors">
                                    <div>
                                        <div class="text-xs font-black text-slate-700 uppercase">{{ $log->keterangan }}</div>
                                        <div class="text-[9px] text-slate-400 font-bold uppercase tracking-widest mt-1 flex items-center gap-2">
                                            <span>{{ $log->created_at->format('d M Y') }}</span>
                                            <span class="text-slate-300">&bull;</span>
                                            <span class="font-mono">{{ $log->referensi_id }}</span>
                                        </div>
                                    </div>
                                    <div class="text-sm font-black italic {{ $log->tipe == 'IN' ? 'text-emerald-500' : 'text-rose-500' }}">
                                        {{ $log->tipe == 'IN' ? '+' : '-' }} {{ number_format($log->nominal, 0, ',', '.') }}
                                    </div>
                                </div>
                                @empty
                                <div class="px-6 py-10 text-center">
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Belum ada riwayat mutasi</p>
                                </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

            @else
                <div class="flex flex-col items-center justify-center py-32 bg-white rounded-[3rem] border-2 border-dashed border-slate-200">
                    <div class="bg-slate-50 p-8 rounded-full mb-6 shadow-inner">
                        <svg class="w-16 h-16 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    </div>
                    <h3 class="text-xl font-black text-[#002855] uppercase tracking-widest">Pilih Mahasiswa</h3>
                    <p class="text-slate-400 text-xs font-bold uppercase tracking-widest mt-2">Gunakan panel kiri untuk mencari data mahasiswa.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- MODAL ADJUSTMENT (FORMAT RUPIAH) --}}
    @if($showAdjustmentModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/70 backdrop-blur-md p-4 animate-in fade-in duration-300">
        <div class="bg-white rounded-[3rem] shadow-2xl w-full max-w-lg overflow-hidden border border-white/20 animate-in zoom-in-95 duration-300">
            <div class="bg-[#002855] px-10 py-8 text-white flex justify-between items-start relative overflow-hidden">
                <div class="absolute right-0 top-0 p-6 opacity-10">
                    <svg class="w-24 h-24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                </div>
                <div class="relative z-10">
                    <h3 class="text-2xl font-black uppercase tracking-tight italic text-[#fcc000]">Koreksi Tagihan</h3>
                    <p class="text-[10px] text-white/70 mt-2 font-bold uppercase tracking-widest">Penyesuaian Beasiswa, Potongan, atau Denda.</p>
                </div>
                <button wire:click="$set('showAdjustmentModal', false)" class="relative z-10 text-white/50 hover:text-white p-2 transition-colors"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </div>
            
            <div class="p-10 space-y-6">
                {{-- Info Target dengan Perhitungan Real-time --}}
                <div class="bg-slate-50 p-5 rounded-2xl border border-slate-100">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Target Invoice</span>
                    <span class="font-black text-[#002855] text-sm uppercase">{{ $selectedTagihanForAdj->deskripsi ?? '-' }}</span>
                    
                    @php
                        $koreksiSaatIni = $selectedTagihanForAdj->adjustments->sum('nominal');
                        $nettoSaatIni = max(0, $selectedTagihanForAdj->total_tagihan - $koreksiSaatIni);
                        $sisaSaatIni = $nettoSaatIni - $selectedTagihanForAdj->total_bayar;
                    @endphp
                    
                    <div class="flex justify-between items-center border-t border-slate-200 pt-3 mt-3">
                        <span class="text-[10px] font-black uppercase tracking-widest text-slate-500">Status Saat Ini:</span>
                        @if($sisaSaatIni > 0)
                            <span class="font-black text-rose-600 text-sm italic">Kurang Rp {{ number_format($sisaSaatIni, 0, ',', '.') }}</span>
                        @elseif($sisaSaatIni < 0)
                            <span class="font-black text-blue-600 text-sm italic">Lebih Rp {{ number_format(abs($sisaSaatIni), 0, ',', '.') }}</span>
                        @else
                            <span class="font-black text-emerald-600 text-sm italic">LUNAS</span>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-[10px] font-black text-slate-400 uppercase mb-2 tracking-widest ml-1">Jenis Koreksi</label>
                        <select wire:model="adj_jenis" class="w-full rounded-2xl border-slate-200 text-sm font-bold py-4 px-5 focus:ring-2 focus:ring-[#fcc000] outline-none text-[#002855]">
                            <option value="BEASISWA">Beasiswa (Pengurang)</option>
                            <option value="POTONGAN">Potongan / Diskon (Pengurang)</option>
                            <option value="DENDA">Denda / Tambahan (Penambah)</option>
                        </select>
                        @error('adj_jenis') <span class="text-rose-500 text-[10px] font-bold mt-1 block ml-1">{{ $message }}</span> @enderror
                    </div>
                    
                    {{-- Input Rupiah Otomatis --}}
                    <div x-data="{
                        amount: '',
                        formatRupiah(value) { return value.replace(/\D/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.'); },
                        handleInput(e) {
                            let raw = e.target.value.replace(/\./g, '');
                            this.amount = this.formatRupiah(raw);
                            $wire.set('adj_nominal', raw); 
                        }
                    }" class="md:col-span-2">
                        <label class="block text-[10px] font-black text-slate-400 uppercase mb-2 tracking-widest ml-1">Nominal Koreksi (Rp)</label>
                        <div class="relative rounded-2xl shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                                <span class="text-slate-400 font-black text-lg">Rp</span>
                            </div>
                            <input type="text" 
                                x-model="amount"
                                @input="handleInput"
                                class="w-full rounded-2xl border-slate-200 text-xl font-black text-[#002855] py-4 pl-16 pr-5 focus:ring-2 focus:ring-[#fcc000] outline-none placeholder-slate-300 transition-all"
                                placeholder="0"
                            >
                        </div>
                        @error('adj_nominal') <span class="text-rose-500 text-[10px] font-bold mt-1 block ml-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-[10px] font-black text-slate-400 uppercase mb-2 tracking-widest ml-1">Keterangan / Dasar Keputusan</label>
                        <textarea wire:model="adj_keterangan" class="w-full rounded-2xl border-slate-200 text-sm font-bold py-4 px-5 focus:ring-2 focus:ring-[#fcc000] outline-none placeholder-slate-300 resize-none text-[#002855]" rows="3" placeholder="Contoh: SK Rektor No. 123/2026 ttg Beasiswa Prestasi"></textarea>
                        @error('adj_keterangan') <span class="text-rose-500 text-[10px] font-bold mt-1 block ml-1">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <div class="p-8 bg-slate-50 border-t border-slate-100 flex flex-col sm:flex-row justify-end gap-3">
                <button wire:click="$set('showAdjustmentModal', false)" class="px-8 py-4 border border-slate-200 bg-white text-slate-500 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-slate-100 transition-all w-full sm:w-auto text-center">Batal</button>
                <button wire:click="saveAdjustment" wire:loading.attr="disabled" class="px-10 py-4 bg-[#002855] text-white rounded-2xl font-black text-[10px] uppercase tracking-[0.2em] hover:bg-[#001a38] shadow-xl shadow-blue-900/20 hover:scale-105 transition-all flex items-center justify-center w-full sm:w-auto disabled:opacity-50">
                    <span wire:loading.remove wire:target="saveAdjustment">Simpan Koreksi</span>
                    <span wire:loading wire:target="saveAdjustment">Memproses...</span>
                </button>
            </div>
        </div>
    </div>
    @endif
    
    {{-- MODAL REFUND (FORMAT RUPIAH) --}}
    @if($showRefundModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/70 backdrop-blur-md p-4 animate-in fade-in duration-300">
        <div class="bg-white rounded-[3rem] shadow-2xl w-full max-w-md overflow-hidden border border-white/20 animate-in zoom-in-95 duration-300">
            <div class="bg-[#fcc000] px-10 py-8 text-[#002855] flex justify-between items-start relative overflow-hidden">
                <div class="absolute right-0 top-0 p-6 opacity-10">
                    <svg class="w-24 h-24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <div class="relative z-10">
                    <h3 class="text-2xl font-black uppercase tracking-tight italic">Pencairan Saldo</h3>
                    <p class="text-[10px] text-[#002855]/70 mt-2 font-bold uppercase tracking-widest">Pengembalian Dana (Refund) Mahasiswa.</p>
                </div>
                <button wire:click="$set('showRefundModal', false)" class="relative z-10 text-[#002855]/50 hover:text-[#002855] p-2 transition-colors"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </div>
            
            <div class="p-10 space-y-8">
                <div class="bg-indigo-50 p-6 rounded-2xl text-center border border-indigo-100">
                    <p class="text-[10px] text-indigo-500 font-black uppercase tracking-widest">Saldo Tersedia</p>
                    <p class="text-3xl font-black text-[#002855] mt-1 italic">Rp {{ number_format($saldo->saldo, 0, ',', '.') }}</p>
                </div>
                
                {{-- Input Rupiah Otomatis --}}
                <div x-data="{
                    amount: '',
                    limit: {{ $saldo->saldo }},
                    formatRupiah(value) { return value.replace(/\D/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.'); },
                    handleInput(e) {
                        let raw = e.target.value.replace(/\./g, '');
                        let num = parseInt(raw) || 0;
                        if(num > this.limit) num = this.limit; // Limit Client
                        this.amount = this.formatRupiah(num.toString());
                        $wire.set('refund_nominal', num); 
                    }
                }" class="space-y-6">
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase mb-2 tracking-widest ml-1">Nominal Dicairkan (Rp)</label>
                        <div class="relative rounded-2xl shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                                <span class="text-slate-400 font-black text-lg">Rp</span>
                            </div>
                            <input type="text" 
                                x-model="amount"
                                @input="handleInput"
                                class="w-full rounded-2xl border-slate-200 text-xl font-black text-[#002855] py-4 pl-16 pr-5 focus:ring-2 focus:ring-[#fcc000] outline-none placeholder-slate-300 transition-all"
                                placeholder="0"
                            >
                        </div>
                        @error('refund_nominal') <span class="text-rose-500 text-[10px] font-bold mt-2 block ml-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase mb-2 tracking-widest ml-1">Catatan / Bukti Transfer</label>
                        <textarea wire:model="refund_keterangan" class="w-full rounded-2xl border-slate-200 text-sm font-bold text-[#002855] py-4 px-5 focus:ring-2 focus:ring-[#fcc000] outline-none placeholder-slate-300 resize-none" rows="3" placeholder="Contoh: Transfer via BCA ke rekening a/n Budi pada tgl 12 Nov..."></textarea>
                        @error('refund_keterangan') <span class="text-rose-500 text-[10px] font-bold mt-2 block ml-1">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <div class="p-8 bg-slate-50 border-t border-slate-100 flex flex-col sm:flex-row justify-end gap-3">
                <button wire:click="$set('showRefundModal', false)" class="px-8 py-4 border border-slate-200 bg-white text-slate-500 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-slate-100 transition-all w-full sm:w-auto text-center">Batal</button>
                <button wire:click="processRefund" wire:loading.attr="disabled" class="px-10 py-4 bg-[#002855] text-white rounded-2xl font-black text-[10px] uppercase tracking-[0.2em] hover:bg-[#001a38] shadow-xl shadow-blue-900/20 hover:scale-105 transition-all flex items-center justify-center w-full sm:w-auto disabled:opacity-50">
                    <span wire:loading.remove wire:target="processRefund">Eksekusi Refund</span>
                    <span wire:loading wire:target="processRefund">Memproses...</span>
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- SweetAlert2 Listener --}}
    @script
    <script>
        $wire.on('swal:success', data => { alert(data[0].title + '\n\n' + data[0].text); });
        $wire.on('swal:error', data => { alert(data[0].title + '\n\n' + data[0].text); });
    </script>
    @endscript

    {{-- System Footer Info --}}
    <div class="pt-10 flex flex-col items-center gap-2 opacity-20 grayscale pointer-events-none border-t border-slate-100">
        <p class="text-[9px] font-black uppercase tracking-[0.6em] text-[#002855]">FINANCIAL CONTROL CENTER &bull; v4.2 PRO</p>
    </div>
    
    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    </style>
</div>