<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-[#002855]">Koreksi & Saldo Mahasiswa</h1>
            <p class="text-slate-500 text-sm mt-1">Kelola beasiswa susulan, potongan biaya, dan refund saldo lebih bayar.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        {{-- PANEL KIRI: PENCARIAN (Sama seperti sebelumnya) --}}
        <div class="lg:col-span-4 space-y-6">
            <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-200 relative z-20">
                <label class="block text-xs font-black text-[#002855] uppercase tracking-widest mb-2">Cari Mahasiswa</label>
                <div class="relative">
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Ketik NIM atau Nama..." 
                        class="block w-full rounded-xl border-slate-200 bg-slate-50 py-3 pl-10 pr-4 text-sm font-bold focus:ring-[#002855] focus:border-[#002855]">
                    <svg class="w-5 h-5 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                @if(!empty($searchResults))
                <div class="absolute left-0 right-0 mt-2 bg-white rounded-xl shadow-xl border border-slate-100 overflow-hidden z-30 mx-6">
                    @foreach($searchResults as $res)
                        <button wire:click="selectMahasiswa('{{ $res->id }}')" class="w-full text-left px-4 py-3 hover:bg-slate-50 border-b border-slate-50 last:border-0 transition-colors">
                            <p class="text-sm font-bold text-[#002855]">{{ $res->person->nama_lengkap ?? $res->nama_lengkap }}</p>
                            <p class="text-xs text-slate-500 font-mono">{{ $res->nim }} • {{ $res->prodi->kode_prodi_internal }}</p>
                        </button>
                    @endforeach
                </div>
                @endif
            </div>

            @if($mahasiswa)
            <div class="bg-white p-8 rounded-[2.5rem] shadow-lg border border-slate-200 text-center relative overflow-hidden animate-in slide-in-from-left-4">
                <div class="absolute top-0 left-0 w-full h-24 bg-[#002855]"></div>
                <div class="relative z-10 -mt-4">
                    <div class="h-24 w-24 rounded-full bg-white p-1 mx-auto shadow-xl">
                        <div class="h-full w-full rounded-full bg-[#fcc000] flex items-center justify-center text-[#002855] text-3xl font-black">
                            {{ substr($mahasiswa->person->nama_lengkap ?? 'M', 0, 1) }}
                        </div>
                    </div>
                    <h2 class="mt-4 text-lg font-black text-[#002855]">{{ $mahasiswa->person->nama_lengkap ?? '-' }}</h2>
                    <p class="text-sm font-mono font-bold text-slate-400">{{ $mahasiswa->nim }}</p>
                    <div class="mt-4 inline-flex px-3 py-1 rounded-full bg-indigo-50 text-indigo-700 text-xs font-bold uppercase tracking-wider border border-indigo-100">
                        {{ $mahasiswa->prodi->nama_prodi }}
                    </div>
                </div>
            </div>
            @endif
        </div>

        {{-- PANEL KANAN: WORKSPACE --}}
        <div class="lg:col-span-8 space-y-6">
            @if($mahasiswa)
                
                {{-- 1. WALLET CARD --}}
                <div class="bg-gradient-to-r from-[#002855] to-[#001a38] p-8 rounded-[2rem] shadow-xl text-white relative overflow-hidden animate-in fade-in">
                    <div class="absolute right-0 top-0 p-6 opacity-10">
                        <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 20 20"><path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4s0-2 2-2z" /></svg>
                    </div>
                    <div class="relative z-10 flex justify-between items-center">
                        <div>
                            <p class="text-xs font-bold text-[#fcc000] uppercase tracking-widest mb-1">Saldo Deposit (Lebih Bayar)</p>
                            <h3 class="text-4xl font-black tracking-tight">Rp {{ number_format($saldo->saldo, 0, ',', '.') }}</h3>
                            <p class="text-[10px] text-slate-300 mt-2 max-w-md">Saldo ini terbentuk otomatis dari kelebihan bayar atau beasiswa retroaktif.</p>
                        </div>
                        @if($saldo->saldo > 0)
                        <button wire:click="openRefund" class="px-6 py-3 bg-[#fcc000] text-[#002855] rounded-xl font-black text-xs uppercase tracking-widest hover:scale-105 transition-transform shadow-lg shadow-orange-500/20">
                            Proses Refund
                        </button>
                        @endif
                    </div>
                </div>

                {{-- 2. RIWAYAT MUTASI SALDO (BARU) --}}
                @if(count($riwayatSaldo) > 0)
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="px-6 py-4 bg-slate-50 border-b border-slate-100">
                        <h3 class="text-xs font-black text-[#002855] uppercase tracking-widest">Riwayat Mutasi Dompet</h3>
                    </div>
                    <div class="divide-y divide-slate-100">
                        @foreach($riwayatSaldo as $log)
                        <div class="px-6 py-3 flex justify-between items-center text-sm">
                            <div>
                                <div class="font-bold text-slate-700">{{ $log->keterangan }}</div>
                                <div class="text-[10px] text-slate-400 font-mono">{{ $log->created_at->format('d M Y H:i') }} • Ref: {{ $log->referensi_id }}</div>
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
                    <h3 class="text-sm font-black text-[#002855] uppercase tracking-widest pl-2 border-l-4 border-[#fcc000]">Riwayat Tagihan & Koreksi</h3>
                    
                    @foreach($tagihans as $tagihan)
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden hover:border-indigo-300 transition-colors">
                        <div class="p-6 flex flex-col md:flex-row justify-between gap-6">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="text-[10px] font-bold text-white bg-[#002855] px-2 py-0.5 rounded">{{ $tagihan->tahunAkademik->nama_tahun }}</span>
                                    <span class="text-[10px] font-mono text-slate-400">{{ $tagihan->kode_transaksi }}</span>
                                </div>
                                <h4 class="text-lg font-bold text-slate-800">{{ $tagihan->deskripsi }}</h4>
                                
                                {{-- List Adjustment --}}
                                @if($tagihan->adjustments->count() > 0)
                                <div class="mt-3 space-y-1">
                                    @foreach($tagihan->adjustments as $adj)
                                    <div class="flex items-center text-xs text-slate-500 bg-slate-50 px-2 py-1 rounded w-fit">
                                        <svg class="w-3 h-3 mr-1 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                        <span class="font-bold mr-1">{{ $adj->jenis_adjustment }}:</span> 
                                        <span>Rp {{ number_format($adj->nominal, 0, ',', '.') }}</span>
                                        <span class="ml-1 text-slate-400 italic">({{ $adj->keterangan }})</span>
                                    </div>
                                    @endforeach
                                </div>
                                @endif
                            </div>

                            <div class="text-right">
                                <p class="text-xs text-slate-400 font-bold uppercase">Sisa Tagihan</p>
                                <p class="text-2xl font-black {{ $tagihan->sisa_tagihan > 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                                    Rp {{ number_format($tagihan->sisa_tagihan, 0, ',', '.') }}
                                </p>
                                <button wire:click="openAdjustment('{{ $tagihan->id }}')" class="mt-3 text-xs font-bold text-indigo-600 hover:underline hover:text-indigo-800">
                                    + Tambah Koreksi / Beasiswa
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

    {{-- MODAL ADJUSTMENT (Sama seperti sebelumnya) --}}
    @if($showAdjustmentModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 animate-in fade-in">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden border border-white/20">
            <div class="bg-indigo-600 px-8 py-5 text-white">
                <h3 class="text-lg font-black uppercase tracking-widest">Koreksi Tagihan</h3>
            </div>
            <div class="p-8 space-y-6">
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-1">Jenis Koreksi</label>
                    <select wire:model="adj_jenis" class="w-full rounded-xl border-slate-200 text-sm font-bold">
                        <option value="BEASISWA">Beasiswa (Kurangi Tagihan)</option>
                        <option value="POTONGAN">Potongan / Diskon (Kurangi Tagihan)</option>
                        <option value="DENDA">Denda / Charge (Tambah Tagihan)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-1">Nominal (Rp)</label>
                    <input type="number" wire:model="adj_nominal" class="w-full rounded-xl border-slate-200 text-lg font-black text-slate-800 py-3">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-1">Keterangan / SK</label>
                    <textarea wire:model="adj_keterangan" class="w-full rounded-xl border-slate-200 text-sm" rows="2" placeholder="Contoh: SK Beasiswa No. 123"></textarea>
                </div>
                
                <div class="flex gap-3 pt-2">
                    <button wire:click="$set('showAdjustmentModal', false)" class="flex-1 py-3 border border-slate-200 text-slate-500 rounded-xl font-bold text-xs uppercase hover:bg-slate-50">Batal</button>
                    <button wire:click="saveAdjustment" class="flex-1 py-3 bg-indigo-600 text-white rounded-xl font-bold text-xs uppercase hover:bg-indigo-700 shadow-lg">Simpan</button>
                </div>
            </div>
        </div>
    </div>
    @endif
    
    {{-- MODAL REFUND (Sama seperti sebelumnya) --}}
    @if($showRefundModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 animate-in fade-in">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden border border-white/20">
            <div class="bg-[#fcc000] px-8 py-5 text-[#002855]">
                <h3 class="text-lg font-black uppercase tracking-widest">Pencairan Saldo (Refund)</h3>
            </div>
            <div class="p-8 space-y-6">
                <div class="bg-indigo-50 p-4 rounded-xl text-center">
                    <p class="text-xs text-indigo-500 font-bold uppercase">Saldo Tersedia</p>
                    <p class="text-2xl font-black text-[#002855]">Rp {{ number_format($saldo->saldo, 0, ',', '.') }}</p>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-1">Nominal Dicairkan (Rp)</label>
                    <input type="number" wire:model="refund_nominal" class="w-full rounded-xl border-slate-200 text-lg font-black text-slate-800 py-3" max="{{ $saldo->saldo }}">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-1">Catatan Transfer</label>
                    <textarea wire:model="refund_keterangan" class="w-full rounded-xl border-slate-200 text-sm" rows="2" placeholder="Contoh: Transfer ke Rekening BCA Mahasiswa"></textarea>
                </div>
                
                <div class="flex gap-3 pt-2">
                    <button wire:click="$set('showRefundModal', false)" class="flex-1 py-3 border border-slate-200 text-slate-500 rounded-xl font-bold text-xs uppercase hover:bg-slate-50">Batal</button>
                    <button wire:click="processRefund" class="flex-1 py-3 bg-[#002855] text-white rounded-xl font-bold text-xs uppercase hover:bg-[#001a38] shadow-lg">Cairkan Dana</button>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>