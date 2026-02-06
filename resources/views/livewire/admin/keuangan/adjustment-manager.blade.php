<div class="space-y-6 animate-in fade-in duration-500">
    
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-black text-[#002855] tracking-tight">Koreksi & Refund</h1>
            <p class="text-slate-500 font-medium text-sm mt-1">
                Panel kendali operasional keuangan untuk penyesuaian tagihan dan pencairan deposit.
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        {{-- PANEL KIRI: PENCARIAN --}}
        <div class="lg:col-span-4 space-y-6">
            <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-200 relative z-20">
                <label class="block text-xs font-black text-[#002855] uppercase tracking-widest mb-2">Cari Mahasiswa</label>
                <div class="relative">
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Ketik NIM atau Nama..." 
                        class="block w-full rounded-xl border-slate-200 bg-slate-50 py-3 pl-10 pr-4 text-sm font-bold focus:ring-[#002855] focus:border-[#002855] transition-all outline-none">
                    <svg class="w-5 h-5 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                
                {{-- Dropdown Hasil Pencarian --}}
                @if(!empty($searchResults))
                <div class="absolute left-0 right-0 mt-2 bg-white rounded-xl shadow-xl border border-slate-100 overflow-hidden z-30 mx-6">
                    @foreach($searchResults as $res)
                        <button wire:click="selectMahasiswa('{{ $res->id }}')" class="w-full text-left px-4 py-3 hover:bg-indigo-50 border-b border-slate-50 last:border-0 transition-colors group">
                            <p class="text-sm font-bold text-[#002855] group-hover:text-indigo-700">{{ $res->person->nama_lengkap ?? $res->nama_lengkap }}</p>
                            <p class="text-[10px] text-slate-500 font-mono">{{ $res->nim }} • {{ $res->prodi->kode_prodi_internal }}</p>
                        </button>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- Profil Mahasiswa Terpilih --}}
            @if($mahasiswa)
            <div class="bg-white p-8 rounded-[2.5rem] shadow-lg border border-slate-200 text-center relative overflow-hidden animate-in slide-in-from-left-4">
                <div class="absolute top-0 left-0 w-full h-24 bg-[#002855]"></div>
                <div class="relative z-10 -mt-4">
                    <div class="h-24 w-24 rounded-full bg-white p-1 mx-auto shadow-xl">
                        <div class="h-full w-full rounded-full bg-[#fcc000] flex items-center justify-center text-[#002855] text-3xl font-black border-4 border-white">
                            {{ substr($mahasiswa->person->nama_lengkap ?? 'M', 0, 1) }}
                        </div>
                    </div>
                    <h2 class="mt-4 text-lg font-black text-[#002855] leading-tight">{{ $mahasiswa->person->nama_lengkap ?? '-' }}</h2>
                    <p class="text-sm font-mono font-bold text-slate-400 mt-1">{{ $mahasiswa->nim }}</p>
                    <div class="mt-4 inline-flex px-3 py-1 rounded-full bg-indigo-50 text-indigo-700 text-[10px] font-black uppercase tracking-wider border border-indigo-100">
                        {{ $mahasiswa->prodi->nama_prodi }}
                    </div>
                </div>
            </div>
            @endif
        </div>

        {{-- PANEL KANAN: WORKSPACE --}}
        <div class="lg:col-span-8 space-y-6">
            @if($mahasiswa)
                
                {{-- 1. WALLET CARD (DOMPET) --}}
                <div class="bg-gradient-to-r from-[#002855] to-[#001a38] p-8 rounded-[2rem] shadow-xl text-white relative overflow-hidden animate-in fade-in group">
                    <div class="absolute right-0 top-0 p-6 opacity-10 group-hover:opacity-20 transition-opacity duration-500">
                        <svg class="w-40 h-40" fill="currentColor" viewBox="0 0 20 20"><path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4s0-2 2-2z" /></svg>
                    </div>
                    
                    <div class="relative z-10 flex flex-col md:flex-row justify-between items-center gap-6">
                        <div>
                            <p class="text-xs font-bold text-[#fcc000] uppercase tracking-widest mb-1 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                Saldo Deposit (Lebih Bayar)
                            </p>
                            <h3 class="text-4xl font-black tracking-tight">Rp {{ number_format($saldo->saldo, 0, ',', '.') }}</h3>
                            <p class="text-[10px] text-slate-300 mt-2 max-w-md leading-relaxed">
                                Saldo ini terbentuk otomatis dari kelebihan bayar atau beasiswa retroaktif. Dana ini dapat dicairkan (Refund) atau digunakan untuk semester depan.
                            </p>
                        </div>
                        @if($saldo->saldo > 0)
                        <button wire:click="openRefund" class="px-8 py-3 bg-[#fcc000] text-[#002855] rounded-xl font-black text-xs uppercase tracking-widest hover:bg-[#e6b000] hover:scale-105 transition-all shadow-lg shadow-orange-500/20 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                            Cairkan Dana
                        </button>
                        @endif
                    </div>
                </div>

                {{-- 2. RIWAYAT MUTASI SALDO --}}
                @if(count($riwayatSaldo) > 0)
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex justify-between items-center">
                        <h3 class="text-xs font-black text-[#002855] uppercase tracking-widest">Riwayat Mutasi Dompet</h3>
                        <span class="text-[10px] font-bold text-slate-400">{{ count($riwayatSaldo) }} Transaksi</span>
                    </div>
                    <div class="divide-y divide-slate-100 max-h-60 overflow-y-auto custom-scrollbar">
                        @foreach($riwayatSaldo as $log)
                        <div class="px-6 py-3 flex justify-between items-center text-sm hover:bg-slate-50 transition-colors">
                            <div>
                                <div class="font-bold text-slate-700">{{ $log->keterangan }}</div>
                                <div class="text-[10px] text-slate-400 font-mono mt-0.5 flex items-center gap-2">
                                    <span>{{ $log->created_at->format('d M Y H:i') }}</span>
                                    <span>&bull;</span>
                                    <span>REF: {{ $log->referensi_id }}</span>
                                </div>
                            </div>
                            <div class="font-black {{ $log->tipe == 'IN' ? 'text-emerald-600' : 'text-rose-600' }}">
                                {{ $log->tipe == 'IN' ? '+' : '-' }} Rp {{ number_format($log->nominal, 0, ',', '.') }}
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- 3. LIST TAGIHAN --}}
                <div class="space-y-4">
                    <div class="flex items-center gap-2 pl-2 border-l-4 border-[#fcc000]">
                        <h3 class="text-sm font-black text-[#002855] uppercase tracking-widest">Daftar Tagihan Semester</h3>
                    </div>
                    
                    @foreach($tagihans as $tagihan)
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden hover:border-indigo-300 transition-all hover:shadow-md group">
                        <div class="p-6 flex flex-col md:flex-row justify-between gap-6">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="text-[10px] font-bold text-white bg-[#002855] px-2 py-0.5 rounded shadow-sm">{{ $tagihan->tahunAkademik->nama_tahun }}</span>
                                    <span class="text-[10px] font-mono text-slate-400 border border-slate-200 px-1.5 py-0.5 rounded">{{ $tagihan->kode_transaksi }}</span>
                                </div>
                                <h4 class="text-lg font-bold text-slate-800 group-hover:text-indigo-700 transition-colors">{{ $tagihan->deskripsi }}</h4>
                                
                                {{-- List Adjustment (Beasiswa) --}}
                                @if($tagihan->adjustments->count() > 0)
                                <div class="mt-3 space-y-1">
                                    @foreach($tagihan->adjustments as $adj)
                                    <div class="flex items-center text-xs text-slate-600 bg-amber-50 px-2 py-1 rounded w-fit border border-amber-100">
                                        <svg class="w-3 h-3 mr-1.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                        <span class="font-bold mr-1">{{ $adj->jenis_adjustment }}:</span> 
                                        <span>Rp {{ number_format($adj->nominal, 0, ',', '.') }}</span>
                                        <span class="ml-1 text-slate-400 italic">({{ $adj->keterangan }})</span>
                                    </div>
                                    @endforeach
                                </div>
                                @endif
                            </div>

                            {{-- LOGIKA HITUNG SISA VISUAL --}}
                            @php
                                $totalBayar = $tagihan->total_bayar;
                                $totalKoreksi = $tagihan->adjustments->sum('nominal');
                                $tagihanNet = max(0, $tagihan->total_tagihan - $totalKoreksi);
                                $sisaDisplay = $tagihanNet - $totalBayar;
                            @endphp

                            <div class="text-right">
                                @if($sisaDisplay < 0)
                                    <p class="text-xs text-blue-500 font-bold uppercase tracking-wider">Lebih Bayar / Deposit</p>
                                    <p class="text-2xl font-black text-blue-600">
                                        + Rp {{ number_format(abs($sisaDisplay), 0, ',', '.') }}
                                    </p>
                                @elseif($sisaDisplay == 0)
                                    <p class="text-xs text-emerald-500 font-bold uppercase tracking-wider">Status</p>
                                    <p class="text-2xl font-black text-emerald-600">LUNAS</p>
                                @else
                                    <p class="text-xs text-slate-400 font-bold uppercase tracking-wider">Sisa Tagihan</p>
                                    <p class="text-2xl font-black text-rose-600">
                                        Rp {{ number_format($sisaDisplay, 0, ',', '.') }}
                                    </p>
                                @endif

                                <button wire:click="openAdjustment('{{ $tagihan->id }}')" class="mt-3 inline-flex items-center text-xs font-bold text-indigo-600 bg-indigo-50 px-3 py-1.5 rounded-lg hover:bg-indigo-100 transition-colors">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                                    Tambah Koreksi
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

            @else
                <div class="flex flex-col items-center justify-center py-20 bg-white rounded-[2.5rem] border-2 border-dashed border-slate-200">
                    <div class="bg-slate-50 p-6 rounded-full mb-4">
                        <svg class="w-12 h-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-600">Pilih Mahasiswa</h3>
                    <p class="text-slate-400 text-sm mt-1">Cari dan pilih mahasiswa di panel kiri untuk mengelola keuangannya.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- MODAL ADJUSTMENT (FORMAT RUPIAH) --}}
    @if($showAdjustmentModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 animate-in fade-in duration-200">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden border border-white/20">
            <div class="bg-[#002855] px-8 py-5 text-white flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-black uppercase tracking-widest leading-none">Koreksi Tagihan</h3>
                    <p class="text-xs text-white/60 mt-1">Tambahkan Beasiswa, Potongan, atau Denda.</p>
                </div>
                <button wire:click="$set('showAdjustmentModal', false)" class="text-white/50 hover:text-white text-2xl font-bold">&times;</button>
            </div>
            
            <div class="p-8 space-y-6">
                {{-- Info Target dengan Perhitungan Real-time --}}
                <div class="bg-slate-50 p-3 rounded-xl border border-slate-100 text-xs text-slate-600 mb-2">
                    <span class="font-bold text-[#002855] block mb-1">Target: {{ $selectedTagihanForAdj->deskripsi ?? '-' }}</span>
                    
                    @php
                        $koreksiSaatIni = $selectedTagihanForAdj->adjustments->sum('nominal');
                        $nettoSaatIni = max(0, $selectedTagihanForAdj->total_tagihan - $koreksiSaatIni);
                        $sisaSaatIni = $nettoSaatIni - $selectedTagihanForAdj->total_bayar;
                    @endphp
                    
                    <div class="flex justify-between items-center border-t border-slate-200 pt-2 mt-2">
                        <span class="font-bold text-[#002855]">Status Saat Ini:</span>
                        @if($sisaSaatIni > 0)
                            <span class="font-black text-rose-600 text-sm">Kurang Rp {{ number_format($sisaSaatIni, 0, ',', '.') }}</span>
                        @elseif($sisaSaatIni < 0)
                            <span class="font-black text-blue-600 text-sm">Lebih Rp {{ number_format(abs($sisaSaatIni), 0, ',', '.') }}</span>
                        @else
                            <span class="font-black text-emerald-600 text-sm">LUNAS</span>
                        @endif
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-1 tracking-widest">Jenis Koreksi</label>
                    <select wire:model="adj_jenis" class="w-full rounded-xl border-slate-200 text-sm font-bold py-2.5 px-4 focus:ring-2 focus:ring-[#fcc000] outline-none text-slate-700">
                        <option value="BEASISWA">Beasiswa (Kurangi Tagihan)</option>
                        <option value="POTONGAN">Potongan / Diskon (Kurangi Tagihan)</option>
                        <option value="DENDA">Denda / Charge (Tambah Tagihan)</option>
                    </select>
                    @error('adj_jenis') <span class="text-rose-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
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
                }">
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-1 tracking-widest">Nominal (Rp)</label>
                    <div class="relative rounded-xl shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <span class="text-slate-400 font-black text-sm">Rp</span>
                        </div>
                        <input type="text" 
                            x-model="amount"
                            @input="handleInput"
                            class="w-full rounded-xl border-slate-200 text-lg font-black text-slate-800 py-3 pl-12 pr-4 focus:ring-2 focus:ring-[#fcc000] outline-none placeholder-slate-300"
                            placeholder="0"
                        >
                    </div>
                    @error('adj_nominal') <span class="text-rose-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-1 tracking-widest">Keterangan / No SK</label>
                    <textarea wire:model="adj_keterangan" class="w-full rounded-xl border-slate-200 text-sm font-medium py-3 px-4 focus:ring-2 focus:ring-[#fcc000] outline-none placeholder-slate-300" rows="2" placeholder="Contoh: SK Rektor No. 123/2025"></textarea>
                    @error('adj_keterangan') <span class="text-rose-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>
                
                <div class="flex gap-3 pt-2 border-t border-slate-100">
                    <button wire:click="$set('showAdjustmentModal', false)" class="flex-1 py-3 border border-slate-200 text-slate-500 rounded-xl font-bold text-xs uppercase hover:bg-slate-50 transition-colors">Batal</button>
                    <button wire:click="saveAdjustment" wire:loading.attr="disabled" class="flex-1 py-3 bg-[#002855] text-white rounded-xl font-bold text-xs uppercase hover:bg-[#001a38] shadow-lg transition-colors flex items-center justify-center disabled:opacity-50">
                        <span wire:loading.remove wire:target="saveAdjustment">Simpan Koreksi</span>
                        <span wire:loading wire:target="saveAdjustment">Menyimpan...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
    
    {{-- MODAL REFUND (FORMAT RUPIAH) --}}
    @if($showRefundModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 animate-in fade-in duration-200">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden border border-white/20">
            <div class="bg-[#fcc000] px-8 py-5 text-[#002855] flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-black uppercase tracking-widest leading-none">Pencairan Saldo (Refund)</h3>
                    <p class="text-xs text-[#002855]/70 mt-1 font-bold">Transfer balik ke rekening mahasiswa.</p>
                </div>
                <button wire:click="$set('showRefundModal', false)" class="text-[#002855]/50 hover:text-[#002855] text-2xl font-bold">&times;</button>
            </div>
            
            <div class="p-8 space-y-6">
                <div class="bg-indigo-50 p-4 rounded-xl text-center border border-indigo-100">
                    <p class="text-[10px] text-indigo-500 font-bold uppercase tracking-widest">Saldo Tersedia</p>
                    <p class="text-3xl font-black text-[#002855] mt-1">Rp {{ number_format($saldo->saldo, 0, ',', '.') }}</p>
                </div>
                
                {{-- Input Rupiah Otomatis --}}
                <div x-data="{
                    amount: '',
                    limit: {{ $saldo->saldo }},
                    formatRupiah(value) { return value.replace(/\D/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.'); },
                    handleInput(e) {
                        let raw = e.target.value.replace(/\./g, '');
                        if(parseInt(raw) > this.limit) raw = this.limit; // Limit Client
                        this.amount = this.formatRupiah(raw);
                        $wire.set('refund_nominal', raw); 
                    }
                }">
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-1 tracking-widest">Nominal Dicairkan (Rp)</label>
                    <div class="relative rounded-xl shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <span class="text-slate-400 font-black text-sm">Rp</span>
                        </div>
                        <input type="text" 
                            x-model="amount"
                            @input="handleInput"
                            class="w-full rounded-xl border-slate-200 text-lg font-black text-slate-800 py-3 pl-12 pr-4 focus:ring-2 focus:ring-[#002855] outline-none placeholder-slate-300"
                            placeholder="0"
                        >
                    </div>
                    @error('refund_nominal') <span class="text-rose-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-1 tracking-widest">Catatan Transfer</label>
                    <textarea wire:model="refund_keterangan" class="w-full rounded-xl border-slate-200 text-sm font-medium py-3 px-4 focus:ring-2 focus:ring-[#002855] outline-none" rows="2" placeholder="Contoh: Transfer ke Rekening BCA Mahasiswa"></textarea>
                    @error('refund_keterangan') <span class="text-rose-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>
                
                <div class="flex gap-3 pt-2 border-t border-slate-100">
                    <button wire:click="$set('showRefundModal', false)" class="flex-1 py-3 border border-slate-200 text-slate-500 rounded-xl font-bold text-xs uppercase hover:bg-slate-50 transition-colors">Batal</button>
                    <button wire:click="processRefund" wire:loading.attr="disabled" class="flex-1 py-3 bg-[#002855] text-white rounded-xl font-bold text-xs uppercase hover:bg-[#001a38] shadow-lg transition-colors flex items-center justify-center disabled:opacity-50">
                        <span wire:loading.remove wire:target="processRefund">Cairkan Dana</span>
                        <span wire:loading wire:target="processRefund">Memproses...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>