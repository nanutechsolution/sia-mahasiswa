<div class="space-y-8 animate-in fade-in duration-500 pb-12 max-w-7xl mx-auto px-4 sm:px-6">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mt-4">
        <div>
            <h1 class="text-3xl font-black text-[#002855] tracking-tight">Keuangan Anda</h1>
            <p class="text-slate-500 font-medium mt-1">Pantau tagihan, beasiswa, saldo dompet, dan konfirmasi pembayaran.</p>
        </div>
    </div>

    {{-- Alert Notification --}}
    @if (session()->has('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 6000)" x-transition class="p-5 bg-emerald-50 border border-emerald-100 text-emerald-800 rounded-2xl shadow-lg shadow-emerald-500/10 flex items-start gap-4">
        <div class="bg-emerald-500 text-white rounded-full p-1.5 shrink-0 mt-0.5">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
            </svg>
        </div>
        <div>
            <h4 class="font-black text-sm uppercase tracking-widest text-emerald-900">Pembayaran Sukses</h4>
            <p class="text-xs mt-1 font-medium">{{ session('success') }}</p>
        </div>
        <button @click="show = false" class="ml-auto text-emerald-400 hover:text-emerald-700"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg></button>
    </div>
    @endif
    @if (session()->has('error'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 6000)" x-transition class="p-5 bg-rose-50 border border-rose-100 text-rose-800 rounded-2xl shadow-lg shadow-rose-500/10 flex items-start gap-4">
        <div class="bg-rose-500 text-white rounded-full p-1.5 shrink-0 mt-0.5">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </div>
        <div>
            <h4 class="font-black text-sm uppercase tracking-widest text-rose-900">Pembayaran Gagal</h4>
            <p class="text-xs mt-1 font-medium">{{ session('error') }}</p>
        </div>
        <button @click="show = false" class="ml-auto text-rose-400 hover:text-rose-700"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg></button>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

        {{-- KOLOM KIRI: SALDO & LIST TAGIHAN --}}
        <div class="lg:col-span-7 xl:col-span-8 space-y-8">

            {{-- 1. INFO SALDO DEPOSIT (Gaya Dompet Digital) --}}
            @if($saldo && $saldo->saldo > 0)
            <div class="bg-gradient-to-br from-[#002855] to-indigo-900 p-8 rounded-[2.5rem] shadow-xl shadow-blue-900/20 text-white relative overflow-hidden group">
                <div class="absolute -right-6 -top-6 p-4 opacity-10 group-hover:scale-110 transition-transform duration-700">
                    <svg class="w-48 h-48" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4s0-2 2-2z" />
                    </svg>
                </div>
                <div class="relative z-10 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6">
                    <div>
                        <p class="text-[10px] font-black text-[#fcc000] uppercase tracking-[0.2em] mb-1 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Saldo Dompet Kampus
                        </p>
                        <h3 class="text-4xl font-black tracking-tighter mt-1 mb-2">Rp {{ number_format($saldo->saldo, 0, ',', '.') }}</h3>
                        <p class="text-xs text-indigo-200 max-w-md leading-relaxed">
                            Dana kelebihan bayar ini bisa digunakan untuk <strong>membayar tagihan secara instan</strong> dengan mengklik opsi pada formulir pembayaran di samping.
                        </p>
                    </div>
                </div>
            </div>
            @endif

            {{-- 2. LIST TAGIHAN --}}
            <div class="space-y-5">
                <h3 class="text-sm font-black text-[#002855] uppercase tracking-widest flex items-center gap-3">
                    <div class="w-8 h-8 bg-[#fcc000] text-[#002855] rounded-xl flex items-center justify-center shadow-inner">🧾</div>
                    Daftar Tagihan Anda
                </h3>

                @forelse($tagihans as $tagihan)
                {{-- Kalkulasi Real-time untuk Tampilan Masing-Masing Card --}}
                @php
                $totalKoreksi = $tagihan->adjustments->sum('nominal');
                $tagihanBersih = max(0, $tagihan->total_tagihan - $totalKoreksi);

                $totalVALID = $tagihan->pembayarans->where('status_verifikasi', 'VALID')->sum('nominal_bayar');
                $totalPending = $tagihan->pembayarans->where('status_verifikasi', 'PENDING')->sum('nominal_bayar');

                // Sisa Kewajiban
                $sisaHitungan = max(0, $tagihanBersih - $totalVALID - $totalPending);
                $lunas = $sisaHitungan <= 0;

                    // Hitung Surplus (Jika Beasiswa> Tagihan)
                    $totalHak = $totalKoreksi + $totalVALID;
                    $totalSurplus = max(0, $totalHak - $tagihan->total_tagihan);

                    $isSelected = $tagihanIdSelected == $tagihan->id;
                    @endphp

                    <div wire:click="pilihTagihan('{{ $tagihan->id }}')"
                        class="bg-white rounded-[2rem] shadow-sm border transition-all cursor-pointer group relative overflow-hidden
                    {{ $isSelected ? 'border-[#002855] ring-4 ring-indigo-50 scale-[1.02]' : 'border-slate-200 hover:border-indigo-300 hover:shadow-md' }}">

                        {{-- Aksen Biru jika Terpilih --}}
                        @if($isSelected)
                        <div class="absolute left-0 top-0 bottom-0 w-2 bg-[#002855]"></div>
                        @endif

                        <div class="p-6 md:p-8">
                            <div class="flex flex-col md:flex-row justify-between gap-6">

                                {{-- Info Kiri --}}
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-3">
                                        @if($lunas)
                                        <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest bg-emerald-100 text-emerald-700 flex items-center gap-1 shadow-sm">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                            LUNAS
                                        </span>
                                        @elseif($totalVALID > 0 || $totalPending > 0)
                                        <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest bg-amber-100 text-amber-700 shadow-sm">DICICIL</span>
                                        @else
                                        <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest bg-rose-100 text-rose-700 shadow-sm animate-pulse">BELUM BAYAR</span>
                                        @endif

                                        <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest border border-slate-200 px-2 py-0.5 rounded-lg">
                                            {{ $tagihan->tahunAkademik->nama_tahun ?? 'Legacy / Lama' }}
                                        </span>
                                    </div>

                                    <h3 class="text-lg md:text-xl font-black text-[#002855] leading-tight mb-1 group-hover:text-indigo-700 transition-colors">{{ $tagihan->deskripsi }}</h3>
                                    <p class="text-[10px] font-mono text-slate-400">INV: {{ $tagihan->kode_transaksi }}</p>

                                    {{-- Rincian Ala Struk --}}
                                    <div class="mt-5 bg-slate-50 p-5 rounded-2xl border border-slate-100 text-xs">
                                        @php
                                        $rincian = is_string($tagihan->rincian_item) ? json_decode($tagihan->rincian_item, true) : ($tagihan->rincian_item ?? []);
                                        @endphp

                                        @if(is_array($rincian) && count($rincian) > 0)
                                        @foreach($rincian as $key => $item)
                                        <div class="flex justify-between text-slate-600 mb-2">
                                            @if(is_array($item))
                                            <span>{{ $item['nama'] ?? 'Biaya' }}</span>
                                            <span class="font-bold">Rp {{ number_format($item['nominal'] ?? 0, 0, ',', '.') }}</span>
                                            @else
                                            <span>{{ $key }}</span>
                                            <span class="font-bold">Rp {{ number_format($item, 0, ',', '.') }}</span>
                                            @endif
                                        </div>
                                        @endforeach
                                        @endif

                                        @foreach($tagihan->adjustments as $adj)
                                        <div class="flex justify-between items-center text-emerald-600 bg-emerald-50 px-3 py-1.5 rounded-lg border border-emerald-100 my-2">
                                            <span class="font-bold flex items-center text-[10px] uppercase tracking-widest">
                                                <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                                </svg>
                                                {{ $adj->jenis_adjustment }}
                                            </span>
                                            <span class="font-black">- Rp {{ number_format($adj->nominal, 0, ',', '.') }}</span>
                                        </div>
                                        @endforeach

                                        <div class="border-t border-slate-200 border-dashed pt-3 mt-3 flex justify-between items-center">
                                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Tagihan Bersih</span>
                                            <span class="text-sm font-black text-[#002855]">Rp {{ number_format($tagihanBersih, 0, ',', '.') }}</span>
                                        </div>
                                    </div>

                                    {{-- Pesan Ramah Jika Ada Uang Lebih --}}
                                    @if($totalSurplus > 0)
                                    <div class="mt-3 p-4 bg-blue-50 border border-blue-100 rounded-2xl flex items-start gap-3">
                                        <div class="text-blue-500 mt-0.5">🎉</div>
                                        <div>
                                            <h4 class="text-[10px] font-black text-blue-800 uppercase tracking-widest">Beasiswa Melimpah!</h4>
                                            <p class="text-xs text-blue-700 font-medium leading-snug mt-0.5">Sisa potongan sebesar <strong class="font-black">Rp {{ number_format($totalSurplus, 0, ',', '.') }}</strong> telah kami pindahkan dengan aman ke <strong>Saldo Dompet</strong> Anda.</p>
                                        </div>
                                    </div>
                                    @endif
                                </div>

                                {{-- Kanan: Call to Action --}}
                                <div class="md:w-56 flex flex-col justify-between items-end md:items-stretch md:text-right border-t md:border-t-0 md:border-l border-slate-100 pt-6 md:pt-0 md:pl-6">
                                    <div class="w-full text-right">
                                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Sisa Yang Harus Dibayar</p>
                                        <p class="text-3xl font-black {{ $sisaHitungan > 0 ? 'text-rose-600' : 'text-slate-300' }} tracking-tighter">
                                            Rp {{ number_format($sisaHitungan, 0, ',', '.') }}
                                        </p>

                                        @if($totalPending > 0)
                                        <div class="inline-flex items-center gap-1.5 mt-2 bg-amber-50 px-2 py-1 rounded border border-amber-100">
                                            <svg class="w-3 h-3 text-amber-500 animate-spin" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            <p class="text-[9px] text-amber-700 font-bold uppercase tracking-wider">Menunggu ACC: Rp {{ number_format($totalPending, 0, ',', '.') }}</p>
                                        </div>
                                        @endif
                                    </div>

                                    @if($sisaHitungan > 0)
                                    <div class="mt-6 w-full text-center md:text-right">
                                        <span class="inline-flex w-full md:w-auto justify-center py-3.5 px-6 bg-[#002855] text-white rounded-xl text-[10px] font-black uppercase tracking-widest group-hover:bg-[#fcc000] group-hover:text-[#002855] transition-all shadow-lg shadow-blue-900/20">
                                            {{ $isSelected ? 'SEDANG DIPILIH ↓' : 'KLIK UNTUK BAYAR' }}
                                        </span>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-24 bg-white rounded-[3rem] border border-slate-200 border-dashed">
                        <div class="text-6xl mb-4 grayscale opacity-20">📭</div>
                        <p class="text-slate-500 font-black uppercase tracking-[0.2em] text-sm">Tidak ada tagihan aktif.</p>
                    </div>
                    @endforelse
            </div>
        </div>

        {{-- KOLOM KANAN: AREA KONFIRMASI (STICKY) --}}
        <div class="lg:col-span-5 xl:col-span-4 mt-8 lg:mt-0">
            @if($tagihanIdSelected && $selectedTagihanInfo)
            <div class="bg-white rounded-[2.5rem] shadow-2xl border border-slate-200 overflow-hidden sticky top-24 animate-in slide-in-from-right-8 duration-500">

                {{-- Header Form --}}
                <div class="bg-[#002855] px-8 py-6 flex items-start justify-between relative overflow-hidden">
                    <div class="absolute right-0 top-0 opacity-10 p-2"><svg class="w-24 h-24 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg></div>
                    <div class="relative z-10">
                        <h3 class="text-sm font-black text-[#fcc000] uppercase tracking-widest">Selesaikan Pembayaran</h3>
                        <p class="text-[10px] text-indigo-200 font-medium mt-1">Sisa tagihan: Rp {{ number_format($sisaTagihanSaatIni, 0, ',', '.') }}</p>
                    </div>
                    <button wire:click="batalPilih" class="relative z-10 text-white/50 hover:text-white bg-white/10 p-1.5 rounded-full transition-colors">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                @if($sisaTagihanSaatIni > 0)
                <div class="p-6 space-y-6">

                    {{-- FITUR BARU: BAYAR INSTAN PAKAI SALDO DOMPET --}}
                    @if($saldo && $saldo->saldo > 0)
                    <div class="bg-indigo-50 border border-indigo-100 rounded-2xl p-5 relative overflow-hidden group">
                        <div class="absolute -right-4 -bottom-4 text-indigo-200 opacity-50 group-hover:scale-110 transition-transform">
                            <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4s0-2 2-2z" />
                            </svg>
                        </div>
                        <div class="relative z-10">
                            <h4 class="text-[10px] font-black text-indigo-800 uppercase tracking-widest mb-2">Gunakan Saldo Dompet</h4>
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs text-indigo-600 font-medium">Tersedia:</p>
                                    <p class="text-lg font-black text-[#002855] italic tracking-tighter">Rp {{ number_format($saldo->saldo, 0, ',', '.') }}</p>
                                </div>
                                <button wire:click="bayarPakaiSaldo" wire:confirm="Sistem akan menarik Rp {{ number_format(min($saldo->saldo, $sisaTagihanSaatIni), 0, ',', '.') }} dari dompet Anda. Lanjutkan?" class="px-5 py-3 bg-indigo-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-[#002855] transition-all shadow-lg hover:-translate-y-1">
                                    Bayar Instan
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="relative flex items-center py-2">
                        <div class="flex-grow border-t border-slate-200"></div>
                        <span class="flex-shrink-0 mx-4 text-slate-400 text-[10px] font-bold uppercase tracking-widest">Atau Transfer Manual</span>
                        <div class="flex-grow border-t border-slate-200"></div>
                    </div>
                    @endif

                    {{-- Form Konfirmasi Transfer Bank --}}
                    <form wire:submit.prevent="simpanPembayaran" class="space-y-6">

                        {{-- Info Rekening --}}
                        <div class="flex items-center justify-between gap-4 bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
                            <img src="https://upload.wikimedia.org/wikipedia/id/thumb/5/55/BNI_logo.svg/1200px-BNI_logo.svg.png" alt="BNI" class="h-5 object-contain">
                            <div class="text-right">
                                <p class="text-sm font-black text-[#002855] tracking-widest">1234567890</p>
                                <p class="text-[8px] font-bold text-slate-400 uppercase tracking-widest">A.N. Kampus UNMARIS</p>
                            </div>
                        </div>

                        <div class="space-y-5 pt-2">
                            {{-- Input Nominal (Alpine) --}}
                            <div x-data="{
                                rawAmount: @entangle('nominalBayar'), 
                                maxLimit: {{ $sisaTagihanSaatIni }},
                                displayAmount: '',
                                showWarning: false,
                                
                                init() {
                                    if(this.rawAmount) this.displayAmount = this.formatNumber(this.rawAmount);
                                    this.$watch('rawAmount', val => {
                                        if(!val) this.displayAmount = '';
                                        else this.displayAmount = this.formatNumber(val);
                                    });
                                },
                                formatNumber(val) {
                                    return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                                },
                                onType(e) {
                                    let inputVal = e.target.value.replace(/\D/g, '');
                                    let num = parseInt(inputVal) || 0;
                                    
                                    if (num > this.maxLimit) {
                                        this.showWarning = true;
                                        this.rawAmount = inputVal;
                                    } else {
                                        this.showWarning = false;
                                        this.rawAmount = inputVal;
                                    }
                                }
                            }">
                                <div class="flex justify-between items-end mb-2">
                                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest pl-1">Nominal Transfer (Rp)</label>
                                    <button type="button" @click="rawAmount = maxLimit; showWarning = false;" class="text-[9px] font-black bg-slate-100 text-slate-600 px-2 py-1 rounded hover:bg-[#002855] hover:text-white transition-colors uppercase tracking-widest">Lunasi</button>
                                </div>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <span class="text-slate-400 font-black text-sm">Rp</span>
                                    </div>
                                    <input type="text"
                                        x-model="displayAmount"
                                        @input="onType"
                                        class="block w-full rounded-xl border-slate-300 bg-slate-50 text-[#002855] py-3.5 pl-12 pr-4 text-lg font-black focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#fcc000] focus:border-transparent transition-all shadow-sm"
                                        :class="showWarning ? 'border-rose-400 ring-2 ring-rose-100' : ''"
                                        placeholder="0">
                                </div>
                                <p x-show="showWarning" x-transition class="text-rose-500 text-[9px] font-bold mt-1.5 flex items-center gap-1 uppercase tracking-wider">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    Melebihi sisa hutang (Maks: Rp {{ number_format($sisaTagihanSaatIni, 0, ',', '.') }})
                                </p>
                                @error('nominalBayar') <span class="text-rose-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2 pl-1">Tgl. Struk Bukti</label>
                                <input type="date" wire:model="tglBayar" class="w-full rounded-xl border-slate-300 bg-slate-50 text-slate-800 py-3 px-4 font-bold focus:bg-white focus:ring-[#fcc000] shadow-sm text-sm cursor-pointer">
                            </div>

                            <div>
                                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2 pl-1">Upload Bukti (JPG/PDF)</label>
                                <label class="flex flex-col items-center justify-center w-full h-24 border-2 border-slate-300 border-dashed rounded-2xl cursor-pointer bg-slate-50 hover:bg-slate-100 transition-all group overflow-hidden relative">
                                    @if($fileBukti)
                                    <div class="absolute inset-0 flex items-center justify-center bg-emerald-50">
                                        <div class="text-center p-4">
                                            <div class="w-8 h-8 bg-emerald-500 text-white rounded-full flex items-center justify-center mx-auto mb-2 shadow-md">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                                </svg>
                                            </div>
                                            <p class="text-[9px] text-emerald-700 font-bold truncate max-w-[200px]">{{ $fileBukti->getClientOriginalName() }}</p>
                                        </div>
                                    </div>
                                    @else
                                    <div class="flex flex-col items-center justify-center py-4">
                                        <div class="w-8 h-8 bg-white text-slate-400 rounded-full flex items-center justify-center mb-2 shadow-sm group-hover:bg-[#002855] group-hover:text-[#fcc000] transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                            </svg>
                                        </div>
                                        <p class="text-[10px] font-bold text-slate-600">Pilih File (Maks 2MB)</p>
                                    </div>
                                    @endif
                                    <input type="file" wire:model="fileBukti" class="hidden" accept="image/*,application/pdf">
                                </label>
                                <div wire:loading wire:target="fileBukti" class="text-[10px] text-indigo-600 font-bold mt-2 flex items-center justify-center animate-pulse">
                                    <svg class="animate-spin -ml-1 mr-2 h-3 w-3 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Mengunggah file...
                                </div>
                                @error('fileBukti') <span class="text-rose-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="pt-2">
                            <button type="submit" wire:loading.attr="disabled"
                                class="w-full py-4 bg-[#fcc000] text-[#002855] rounded-2xl font-black text-xs uppercase tracking-[0.2em] shadow-xl shadow-amber-500/20 hover:bg-[#ffca28] hover:-translate-y-1 active:scale-95 transition-all disabled:opacity-50 flex items-center justify-center gap-3">
                                <span wire:loading.remove wire:target="simpanPembayaran">Kirim Konfirmasi</span>
                                <span wire:loading wire:target="simpanPembayaran">Memproses...</span>
                            </button>
                        </div>
                    </form>
                </div>
                @else
                <div class="p-12 text-center bg-emerald-50">
                    <div class="w-20 h-20 bg-emerald-100 text-emerald-500 rounded-full flex items-center justify-center mx-auto mb-6 text-4xl shadow-inner">🎉</div>
                    <h3 class="text-xl font-black text-emerald-800 uppercase tracking-tight">Tagihan Selesai</h3>
                    <p class="text-sm font-medium text-emerald-700/80 mt-2 leading-relaxed">Tidak ada kewajiban pembayaran yang tersisa untuk tagihan ini. Hebat!</p>
                </div>
                @endif
            </div>
            @else
            {{-- State Kosong (Belum pilih tagihan) --}}
            <div class="bg-indigo-50/50 rounded-[3rem] p-12 border-2 border-dashed border-indigo-100 text-center sticky top-24">
                <div class="w-20 h-20 bg-white rounded-full shadow-md flex items-center justify-center text-4xl mx-auto mb-6 text-[#002855]">💳</div>
                <h3 class="text-lg font-black text-[#002855] uppercase tracking-tight">Pilih Tagihan</h3>
                <p class="text-xs font-medium text-indigo-400 mt-3 leading-relaxed max-w-xs mx-auto">
                    Klik tagihan yang belum lunas di sebelah kiri untuk melihat opsi pembayaran dan menggunakan Saldo Dompet.
                </p>
            </div>
            @endif
        </div>
    </div> {{-- <-- Penutup Grid --}}

    {{-- 3. RIWAYAT PEMBAYARAN & KOREKSI TABLE --}}
    <div class="mt-8 space-y-4 pt-8 border-t border-slate-200">
        <h3 class="text-sm font-black text-[#002855] uppercase tracking-widest flex items-center gap-3">
            <div class="w-8 h-8 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center shadow-inner">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            Histori Transaksi
        </h3>

        <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50/80">
                        <tr>
                            <th class="px-8 py-5 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Tanggal Konfirmasi</th>
                            <th class="px-6 py-5 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Keterangan / Tagihan</th>
                            <th class="px-6 py-5 text-right text-[10px] font-black text-slate-400 uppercase tracking-widest">Nominal</th>
                            <th class="px-8 py-5 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest">Status Admin</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($riwayatPembayaran as $log)
                        <tr class="hover:bg-slate-50/50 transition-colors group">
                            <td class="px-8 py-5 whitespace-nowrap">
                                <p class="text-sm font-black text-slate-700">{{ \Carbon\Carbon::parse($log->tanggal)->isoFormat('D MMMM Y') }}</p>
                                <p class="text-[9px] font-bold text-slate-400 mt-0.5 uppercase tracking-widest">Via {{ $log->metode }}</p>
                            </td>
                            <td class="px-6 py-5">
                                <p class="text-xs font-bold {{ $log->type == 'ADJUSTMENT' ? 'text-amber-600' : 'text-slate-600' }} leading-tight">{{ $log->keterangan }}</p>
                                <p class="text-[9px] font-mono text-slate-400 mt-1">REF: {{ $log->referensi }}</p>
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap text-right">
                                <p class="text-sm font-black {{ $log->type == 'ADJUSTMENT' ? 'text-amber-600' : 'text-[#002855]' }}">
                                    {{ $log->type == 'ADJUSTMENT' ? '-' : '' }} Rp {{ number_format($log->nominal, 0, ',', '.') }}
                                </p>
                            </td>
                            <td class="px-8 py-5 whitespace-nowrap text-center">
                                @if($log->status == 'VALID')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-[9px] font-black {{ $log->type == 'ADJUSTMENT' ? 'bg-amber-50 text-amber-600 border border-amber-100' : 'bg-emerald-50 text-emerald-600 border border-emerald-100' }} uppercase tracking-widest">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                    {{ $log->type == 'ADJUSTMENT' ? 'Diterapkan' : 'Diterima' }}
                                </span>
                                @elseif($log->status == 'REJECTED')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-[9px] font-black bg-rose-50 text-rose-600 uppercase tracking-widest border border-rose-100 cursor-help" title="Cek Catatan Admin">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                    </svg>
                                    Ditolak
                                </span>
                                @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-[9px] font-black bg-slate-100 text-slate-500 uppercase tracking-widest border border-slate-200 animate-pulse">
                                    <svg class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Diproses
                                </span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-20 text-center">
                                <div class="text-4xl mb-4 grayscale opacity-20">📜</div>
                                <p class="text-xs text-slate-400 font-bold uppercase tracking-widest">Belum ada riwayat transaksi.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>