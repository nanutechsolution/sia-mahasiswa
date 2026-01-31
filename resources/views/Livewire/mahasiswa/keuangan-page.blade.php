<div class="space-y-8 animate-in fade-in duration-500">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-[#002855]">Keuangan & Pembayaran</h1>
            <p class="text-slate-500 text-sm mt-1">Pantau tagihan, beasiswa, saldo deposit, dan upload bukti bayar.</p>
        </div>
    </div>

    @if (session()->has('success'))
    <div class="p-4 bg-emerald-50 border border-emerald-100 text-emerald-800 rounded-2xl text-sm font-bold flex items-center shadow-sm">
        <svg class="w-5 h-5 mr-3 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
        </svg>
        {{ session('success') }}
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

        {{-- KOLOM KIRI: SALDO & LIST TAGIHAN --}}
        <div class="lg:col-span-8 space-y-8">

            {{-- 1. INFO SALDO DEPOSIT (JIKA ADA) --}}
            @if($saldo->saldo > 0)
            <div class="bg-gradient-to-r from-[#002855] to-[#001a38] p-6 rounded-3xl shadow-lg text-white relative overflow-hidden">
                <div class="absolute right-0 top-0 p-4 opacity-10">
                    <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4s0-2 2-2z" />
                    </svg>
                </div>
                <div class="relative z-10 flex justify-between items-center">
                    <div>
                        <p class="text-[10px] font-black text-[#fcc000] uppercase tracking-widest mb-1">Saldo Deposit Anda</p>
                        <h3 class="text-3xl font-black tracking-tight">Rp {{ number_format($saldo->saldo, 0, ',', '.') }}</h3>
                        <p class="text-[10px] text-slate-300 mt-2 max-w-md">
                            Saldo ini berasal dari kelebihan bayar atau beasiswa susulan. Anda dapat mengajukan refund ke bagian keuangan atau membiarkannya untuk memotong tagihan semester depan.
                        </p>
                    </div>
                </div>
            </div>
            @endif

            {{-- 2. LIST TAGIHAN --}}
            <div class="space-y-4">
                <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest ml-2">Riwayat Tagihan</h3>

                @forelse($tagihans as $tagihan)
                {{-- Kalkulasi Real-time untuk Tampilan --}}
                @php
                $totalKoreksi = $tagihan->adjustments->sum('nominal');
                $tagihanBersih = max(0, $tagihan->total_tagihan - $totalKoreksi);

                $totalVALID = $tagihan->pembayarans->where('status_verifikasi', 'VALID')->sum('nominal_bayar');
                $totalPending = $tagihan->pembayarans->where('status_verifikasi', 'PENDING')->sum('nominal_bayar');

                // Hitung Sisa dengan mengurangi Pending juga
                $sisaHitungan = max(0, $tagihanBersih - $totalVALID - $totalPending);
                $lunas = $sisaHitungan <= 0;
                    @endphp

                    <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden hover:border-[#002855]/30 transition-all group cursor-pointer {{ $tagihanIdSelected == $tagihan->id ? 'ring-2 ring-[#002855]' : '' }}"
                    wire:click="pilihTagihan('{{ $tagihan->id }}')">
                    <div class="p-6">
                        <div class="flex flex-col md:flex-row justify-between gap-6">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-widest {{ $lunas ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                        {{ $lunas ? 'LUNAS' : 'BELUM LUNAS' }}
                                    </span>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                                        {{ $tagihan->tahunAkademik->nama_tahun ?? 'Semester Lama' }}
                                    </span>
                                </div>
                                <h3 class="text-lg font-black text-[#002855] leading-tight mb-2">{{ $tagihan->deskripsi }}</h3>

                                {{-- Rincian Biaya --}}
                                <div class="mt-4 space-y-2 bg-slate-50 p-4 rounded-xl border border-slate-100 text-xs">
                                    @foreach($tagihan->rincian_item ?? [] as $item)
                                    <div class="flex justify-between text-slate-600">
                                        <span>{{ $item['nama'] }}</span>
                                        <span class="font-bold">Rp {{ number_format($item['nominal'], 0, ',', '.') }}</span>
                                    </div>
                                    @endforeach

                                    @foreach($tagihan->adjustments as $adj)
                                    <div class="flex justify-between text-amber-600 bg-amber-50 px-2 py-1 rounded border border-amber-100">
                                        <span class="font-bold flex items-center">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                            </svg>
                                            {{ $adj->jenis_adjustment }}
                                        </span>
                                        <span class="font-bold">- Rp {{ number_format($adj->nominal, 0, ',', '.') }}</span>
                                    </div>
                                    @endforeach

                                    <div class="border-t border-slate-200 pt-2 mt-2 flex justify-between font-black text-[#002855]">
                                        <span>TAGIHAN BERSIH</span>
                                        <span>Rp {{ number_format($tagihanBersih, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="md:w-48 text-right flex flex-col justify-between">
                                <div>
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Sisa Bayar</p>
                                    <p class="text-xl font-black {{ $sisaHitungan > 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                                        Rp {{ number_format($sisaHitungan, 0, ',', '.') }}
                                    </p>
                                    @if($totalPending > 0)
                                    <p class="text-[10px] text-amber-500 font-bold mt-1">
                                        (Rp {{ number_format($totalPending, 0, ',', '.') }} dlm proses)
                                    </p>
                                    @endif
                                </div>

                                @if($sisaHitungan > 0)
                                <button wire:click="pilihTagihan('{{ $tagihan->id }}')"
                                    class="mt-4 w-full py-2.5 bg-[#002855] text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-[#001a38] transition-all shadow-lg shadow-indigo-900/20">
                                    Bayar
                                </button>
                                @else
                                <div class="mt-4 flex items-center justify-end text-emerald-600 font-bold text-xs bg-emerald-50 px-3 py-1.5 rounded-lg border border-emerald-100">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                    Lunas
                                </div>
                                @endif
                                @if($tagihanIdSelected == $tagihan->id)
                                <span class="text-[10px] font-bold text-[#002855] mt-2 block animate-pulse text-center">Sedang Dipilih</span>
                                @endif
                            </div>
                        </div>
                    </div>
            </div>
            @empty
            <div class="text-center py-20 bg-white rounded-3xl border border-slate-200 border-dashed">
                <p class="text-slate-400 font-medium italic">Belum ada tagihan yang diterbitkan.</p>
            </div>
            @endforelse
        </div>

        {{-- 3. RIWAYAT PEMBAYARAN (BARU) --}}
        <div class="space-y-4 pt-4 border-t border-slate-200/50">
            <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest ml-2">Histori Transaksi</h3>

            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Tanggal</th>
                                <th class="px-6 py-4 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Keterangan</th>
                                <th class="px-6 py-4 text-right text-[10px] font-black text-slate-400 uppercase tracking-widest">Nominal</th>
                                <th class="px-6 py-4 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @forelse($riwayatPembayaran as $bayar)
                            <tr class="hover:bg-slate-50/50 transition-colors group">
                                <td class="px-6 py-4 whitespace-nowrap text-xs font-medium text-slate-500">
                                    {{ \Carbon\Carbon::parse($bayar->tanggal_bayar)->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4 text-xs text-slate-600">
                                    Pembayaran untuk <span class="font-bold text-slate-800">{{ $bayar->tagihan->kode_transaksi }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-xs font-black text-[#002855]">
                                    Rp {{ number_format($bayar->nominal_bayar, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if($bayar->status_verifikasi == 'VALID')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[9px] font-black bg-emerald-100 text-emerald-700 uppercase tracking-wide border border-emerald-200">
                                        Berhasil
                                    </span>
                                    @elseif($bayar->status_verifikasi == 'REJECTED')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[9px] font-black bg-rose-100 text-rose-700 uppercase tracking-wide border border-rose-200">
                                        Ditolak
                                    </span>
                                    @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[9px] font-black bg-amber-100 text-amber-700 uppercase tracking-wide border border-amber-200 animate-pulse">
                                        Proses
                                    </span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-xs text-slate-400 italic">Belum ada riwayat transaksi.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- KOLOM KANAN: FORM KONFIRMASI --}}
    <div class="lg:col-span-4">
        @if($tagihanIdSelected && $selectedTagihanInfo)

        <div class="bg-white rounded-3xl shadow-xl border border-slate-200 overflow-hidden sticky top-6 animate-in slide-in-from-right-4 z-30">

            {{-- Header Form --}}
            <div class="bg-[#002855] px-6 py-5 flex items-center justify-between">
                <div>
                    <h3 class="text-xs font-black text-[#fcc000] uppercase tracking-widest">Konfirmasi Pembayaran</h3>
                    <p class="text-[10px] text-white/60 font-mono mt-0.5">{{ $selectedTagihanInfo->kode_transaksi }}</p>
                </div>
                <button wire:click="$set('tagihanIdSelected', null)" class="text-white/50 hover:text-white">&times;</button>
            </div>

            {{-- RINCIAN PERHITUNGAN (NEW) --}}
            <div class="px-6 py-5 bg-slate-50 border-b border-slate-100 space-y-3">
                <div class="flex justify-between text-xs text-slate-500">
                    <span>Tagihan Awal</span>
                    <span class="font-mono">Rp {{ number_format($detailHitungan['tagihan_awal'], 0, ',', '.') }}</span>
                </div>

                @if($detailHitungan['total_koreksi'] > 0)
                <div class="flex justify-between text-xs text-amber-600 font-bold bg-amber-50 px-2 py-1 rounded border border-amber-100">
                    <span>(-) Koreksi/Beasiswa</span>
                    <span class="font-mono">Rp {{ number_format($detailHitungan['total_koreksi'], 0, ',', '.') }}</span>
                </div>
                @endif
                <div class="flex justify-between text-xs text-emerald-600 font-bold">
                    <span>(-) Sudah Diverifikasi</span>
                    <span class="font-mono">Rp {{ number_format($detailHitungan['total_valid'], 0, ',', '.') }}</span>
                </div>

                @if($detailHitungan['total_pending'] > 0)
                <div class="flex justify-between text-xs text-indigo-500 italic border-b border-dashed border-indigo-200 pb-1">
                    <span>(-) Sedang Diproses</span>
                    <span class="font-mono">Rp {{ number_format($detailHitungan['total_pending'], 0, ',', '.') }}</span>
                </div>
                @endif

                <div class="pt-2 flex justify-between items-center border-t border-slate-200">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Sisa Kewajiban</p>
                    <p class="text-xl font-black text-rose-600 tracking-tight">
                        Rp {{ number_format($sisaTagihanSaatIni, 0, ',', '.') }}
                    </p>
                </div>
            </div>

            @if($sisaTagihanSaatIni > 0)
            <form wire:submit.prevent="simpanPembayaran" class="p-6 space-y-6">

                {{-- INPUT NOMINAL DENGAN PROTEKSI LIMIT (ALPINE JS) --}}
                <div x-data="{
                            amount: @entangle('nominalBayar'), 
                            limit: {{ $sisaTagihanSaatIni ?? 0 }},
                            isOver: false,
                            formatRupiah(value) {
                                if(!value) return '';
                                return value.replace(/\D/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                            },
                            handleInput(e) {
                                let raw = e.target.value.replace(/\./g, '');
                                let num = parseInt(raw);
                                
                                if (num > this.limit) {
                                    this.isOver = true;
                                    raw = this.limit; // Auto-Clamp ke Maksimal
                                    setTimeout(() => this.isOver = false, 1000); // Reset alert visual
                                }
                                this.amount = raw;
                            }
                        }">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 flex justify-between">
                        <span>Nominal Transfer (Rp)</span>
                        <span x-show="isOver" x-transition class="text-rose-600 font-bold animate-pulse">Maksimal Rp {{ number_format($sisaTagihanSaatIni, 0, ',', '.') }}</span>
                    </label>

                    <div class="relative rounded-xl shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <span class="text-slate-400 font-black text-sm">Rp</span>
                        </div>
                        <input type="text"
                            :value="formatRupiah(amount)"
                            @input="handleInput"
                            class="block w-full rounded-xl border-slate-200 bg-white text-[#002855] py-3 pl-12 pr-4 text-xl font-black focus:outline-none focus:ring-2 focus:ring-[#fcc000] focus:border-transparent transition-all placeholder-slate-300"
                            :class="isOver ? 'ring-2 ring-rose-500' : 'focus:ring-[#fcc000]'"
                            placeholder="Ketik nominal...">
                    </div>

                    {{-- EDUKASI & VALIDASI VISUAL --}}
                    @if($nominalBayar)
                    @if($nominalBayar < $sisaTagihanSaatIni)
                        <p class="text-[10px] text-amber-600 font-bold mt-1.5 flex items-center bg-amber-50 px-2 py-1 rounded w-fit">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Pembayaran Sebagian (Cicil)
                        </p>
                        @elseif($nominalBayar == $sisaTagihanSaatIni)
                        <p class="text-[10px] text-emerald-600 font-bold mt-1.5 flex items-center bg-emerald-50 px-2 py-1 rounded w-fit">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Pelunasan Penuh
                        </p>
                        @endif
                        @else
                        <p class="text-[10px] text-slate-400 mt-1.5 italic">*Masukkan jumlah sesuai bukti transfer.</p>
                        @endif

                        @error('nominalBayar') <span class="text-rose-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Tanggal Transfer</label>
                    <input type="date" wire:model="tglBayar"
                        class="w-full rounded-xl border-slate-200 bg-white text-slate-900 py-2.5 pl-4 font-bold focus:outline-none focus:ring-2 focus:ring-[#fcc000] transition-all text-sm">
                </div>

                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Upload Bukti (JPG/PDF)</label>
                    <label class="flex flex-col items-center justify-center w-full h-24 border-2 border-slate-200 border-dashed rounded-xl cursor-pointer bg-slate-50 hover:bg-slate-100 transition-all group">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                            <svg class="w-6 h-6 mb-2 text-slate-400 group-hover:text-[#002855]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                            <p class="text-[10px] text-slate-500"><span class="font-bold text-[#002855]">Klik upload</span> atau drag file</p>
                        </div>
                        <input type="file" wire:model="fileBukti" class="hidden" accept="image/*,application/pdf">
                    </label>
                    <div wire:loading wire:target="fileBukti" class="text-[10px] text-[#002855] font-bold mt-1 animate-pulse">Sedang mengupload...</div>
                    @if($fileBukti)
                    <p class="text-[10px] text-emerald-600 font-bold mt-1 truncate">File siap: {{ $fileBukti->getClientOriginalName() }}</p>
                    @endif
                    @error('fileBukti') <span class="text-rose-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="pt-2">
                    <button type="submit" wire:loading.attr="disabled"
                        class="w-full py-3.5 bg-[#fcc000] text-[#002855] rounded-xl font-black text-xs uppercase tracking-widest shadow-xl shadow-orange-500/20 hover:scale-[1.02] active:scale-95 transition-all disabled:opacity-50 disabled:grayscale">
                        <span wire:loading.remove>Kirim Konfirmasi</span>
                        <span wire:loading>Mengirim Data...</span>
                    </button>
                </div>
            </form>
            @else
            <div class="p-8 text-center bg-slate-50">
                <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-4 text-3xl">🎉</div>
                <h3 class="text-sm font-black text-emerald-700 uppercase">Tagihan Lunas</h3>
                <p class="text-xs text-emerald-600 mt-1">Tidak ada kewajiban pembayaran yang tersisa. Terima kasih!</p>
                @if($detailHitungan['total_pending'] > 0)
                <p class="text-[9px] text-slate-400 mt-2 italic">(Menunggu verifikasi admin untuk pembayaran terakhir)</p>
                @endif
            </div>
            @endif
        </div>
        @else
        <div class="bg-indigo-50/50 rounded-3xl p-8 border-2 border-dashed border-indigo-100 text-center sticky top-6">
            <div class="w-16 h-16 bg-white rounded-2xl shadow-sm flex items-center justify-center text-2xl mx-auto mb-4 text-[#002855]">💳</div>
            <h3 class="text-sm font-black text-[#002855] uppercase tracking-tight">Proses Pembayaran</h3>
            <p class="text-xs text-indigo-400 mt-2 leading-relaxed">
                Silakan transfer ke rekening kampus, lalu klik tombol <strong>"Bayar"</strong> pada tagihan untuk mengunggah bukti transfer.
            </p>
        </div>
        @endif
    </div>
</div>
</div>