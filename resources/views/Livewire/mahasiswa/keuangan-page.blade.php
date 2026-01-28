<div>
    {{-- SEO & Header Layout --}}
    <x-slot name="title">Riwayat Keuangan - UNMARIS</x-slot>
    <x-slot name="header">Monitoring & Pembayaran Tagihan</x-slot>

    <div class="space-y-8">
        {{-- Intro Section --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <p class="text-slate-500 text-sm font-medium">Pantau status kewajiban administrasi Anda dan lakukan konfirmasi pembayaran secara mandiri.</p>
            </div>

            <div class="flex items-center space-x-2 text-[10px] font-black uppercase tracking-widest text-slate-400">
                <span class="w-2 h-2 bg-unmaris-yellow rounded-full animate-pulse"></span>
                <span>Financial Monitor Active</span>
            </div>
        </div>

        {{-- Success Notification --}}
        @if (session()->has('success'))
        <div class="bg-emerald-50 border border-emerald-100 p-5 rounded-3xl text-emerald-800 text-sm flex items-center animate-in fade-in slide-in-from-top-2 duration-300 shadow-sm">
            <svg class="w-6 h-6 mr-3 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            <span class="font-bold">{{ session('success') }}</span>
        </div>
        @endif

        {{-- List Tagihan --}}
        <div class="space-y-10">
            @foreach($tagihans as $tagihan)
            <div class="bg-white rounded-[2.5rem] shadow-xl border border-slate-200 overflow-hidden animate-in fade-in duration-700">
                {{-- Card Header --}}
                <div class="px-8 py-8 lg:px-10 bg-slate-50/50 border-b border-slate-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                    <div>
                        <div class="flex items-center space-x-3 mb-2">
                            <span class="px-3 py-1 bg-unmaris-blue text-unmaris-yellow text-[10px] font-black uppercase tracking-widest rounded-lg shadow-sm">
                                {{ $tagihan->status_bayar }}
                            </span>
                            <span class="text-[11px] font-mono font-bold text-slate-400 tracking-tighter">{{ $tagihan->kode_transaksi }}</span>
                        </div>
                        <h3 class="text-xl font-black text-slate-900 tracking-tight">{{ $tagihan->deskripsi }}</h3>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">Periode Akademik: {{ $tagihan->tahunAkademik->nama_tahun ?? '-' }}</p>
                    </div>
                    <div class="text-left md:text-right">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Tagihan</p>
                        <p class="text-3xl font-black text-unmaris-blue tabular-nums">Rp {{ number_format($tagihan->total_tagihan, 0, ',', '.') }}</p>
                    </div>
                </div>

                <div class="p-8 lg:p-10 space-y-10">
                    {{-- Progress Bar --}}
                    <div class="bg-slate-50 rounded-3xl p-6 border border-slate-100">
                        <div class="flex justify-between items-end mb-4">
                            <div>
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Realisasi Pembayaran</span>
                                <span class="text-lg font-black text-emerald-600 tabular-nums">Rp {{ number_format($tagihan->total_bayar, 0, ',', '.') }}</span>
                            </div>
                            <div class="text-right">
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Sisa Tunggakan</span>
                                <span class="text-lg font-black text-rose-500 tabular-nums">Rp {{ number_format($tagihan->sisa_tagihan, 0, ',', '.') }}</span>
                            </div>
                        </div>
                        @php $persen = ($tagihan->total_tagihan > 0) ? ($tagihan->total_bayar / $tagihan->total_tagihan) * 100 : 0; @endphp
                        <div class="relative w-full bg-slate-200 rounded-full h-4 overflow-hidden shadow-inner">
                            <div class="absolute inset-y-0 left-0 bg-gradient-to-r from-unmaris-blue to-indigo-500 rounded-full transition-all duration-1000 ease-out" style="width: {{ $persen }}%">
                                <div class="absolute inset-0 bg-white/20 animate-pulse"></div>
                            </div>
                        </div>
                        <div class="mt-3 text-right">
                            <span class="text-[11px] font-black text-unmaris-blue uppercase tracking-widest">{{ number_format($persen, 1) }}% Tercapai</span>
                        </div>
                    </div>

                    {{-- Transaction History --}}
                    <div class="space-y-4">
                        <h4 class="text-xs font-black text-slate-400 uppercase tracking-[0.2em] flex items-center">
                            <svg class="w-4 h-4 mr-2 text-unmaris-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Log Transaksi Pembayaran
                        </h4>
                        <div class="bg-white rounded-2xl border border-slate-100 overflow-hidden shadow-sm">
                            <table class="w-full text-left">
                                <tbody class="divide-y divide-slate-50">
                                    @forelse($tagihan->pembayarans as $bayar)
                                    <tr class="hover:bg-slate-50/50 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="text-[13px] font-black text-slate-800 tabular-nums">Rp {{ number_format($bayar->nominal_bayar, 0, ',', '.') }}</div>
                                            <div class="text-[10px] font-bold text-slate-400 uppercase mt-0.5">{{ \Carbon\Carbon::parse($bayar->tanggal_bayar)->format('d F Y') }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            @if($bayar->status_verifikasi == 'PENDING')
                                            <span class="inline-flex items-center px-3 py-1 rounded-lg bg-amber-50 text-amber-600 text-[10px] font-black uppercase tracking-widest border border-amber-100">
                                                Verifikasi
                                            </span>
                                            @elseif($bayar->status_verifikasi == 'VALID')
                                            <span class="inline-flex items-center px-3 py-1 rounded-lg bg-emerald-50 text-emerald-600 text-[10px] font-black uppercase tracking-widest border border-emerald-100">
                                                Valid
                                            </span>
                                            @else
                                            <span class="inline-flex items-center px-3 py-1 rounded-lg bg-rose-50 text-rose-600 text-[10px] font-black uppercase tracking-widest border border-rose-100">
                                                Ditolak
                                            </span>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td class="px-6 py-8 text-center text-slate-400 text-xs font-bold italic uppercase tracking-widest">Belum ada aktivitas transaksi</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Payment Action / Form --}}
                    @if($tagihan->status_bayar != 'LUNAS')
                    <div class="pt-6 border-t border-slate-100">
                        @if($tagihanIdSelected == $tagihan->id)
                        <div class="bg-indigo-50/50 rounded-3xl p-8 border border-indigo-100 animate-in zoom-in-95 duration-300">
                            <form wire:submit.prevent="simpanPembayaran" class="space-y-8">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                                    <div>
                                        <label class="block text-[11px] font-black text-slate-500 uppercase tracking-widest mb-2">Nominal Transfer (IDR)</label>
                                        <input type="number" wire:model="nominalBayar"
                                            class="block w-full rounded-2xl border-slate-200 bg-white py-4 px-5 focus:ring-4 focus:ring-unmaris-blue/5 focus:border-unmaris-blue text-sm font-black text-unmaris-blue outline-none transition-all shadow-sm">
                                        @error('nominalBayar') <span class="text-rose-500 text-[10px] font-bold mt-2 block">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label class="block text-[11px] font-black text-slate-500 uppercase tracking-widest mb-2">Tanggal Transaksi</label>
                                        <input type="date" wire:model="tglBayar"
                                            class="block w-full rounded-2xl border-slate-200 bg-white py-4 px-5 focus:ring-4 focus:ring-unmaris-blue/5 focus:border-unmaris-blue text-sm font-bold text-slate-700 outline-none transition-all shadow-sm">
                                    </div>

                                    <div>
                                        <label class="block text-[11px] font-black text-slate-500 uppercase tracking-widest mb-2">Bukti Bayar (Image/PDF)</label>
                                        <input type="file" wire:model="fileBukti"
                                            class="block w-full text-xs text-slate-500 file:mr-4 file:py-3.5 file:px-6 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:uppercase file:tracking-widest file:bg-unmaris-blue file:text-white hover:file:bg-indigo-800 transition-all cursor-pointer">
                                        @error('fileBukti') <span class="text-rose-500 text-[10px] font-bold mt-2 block">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <div class="flex flex-col md:flex-row items-center justify-end gap-4">
                                    <button type="button" wire:click="$set('tagihanIdSelected', null)" class="text-xs font-black text-slate-400 hover:text-slate-600 uppercase tracking-widest transition-colors">Batalkan</button>
                                    <button type="submit" wire.loading.attr="disabled" wire:target="simpanPembayaran"
                                        class="inline-flex items-center px-10 py-4 bg-emerald-600 text-white rounded-2xl text-xs font-black uppercase tracking-[0.2em] shadow-xl shadow-emerald-200 hover:scale-105 active:scale-95 transition-all">
                                        <svg wire:loading wire:target=" simpanPembayaran" class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        Kirim Bukti Pembayaran
                                    </button>
                                </div>
                            </form>
                        </div>
                        @else
                        <div class="flex justify-center md:justify-end">
                            <button wire:click="pilihTagihan('{{ $tagihan->id }}')"
                                class="group relative inline-flex items-center px-12 py-4 bg-unmaris-blue text-white rounded-2xl text-xs font-black uppercase tracking-[0.2em] shadow-2xl shadow-indigo-200 hover:scale-105 transition-all overflow-hidden">
                                <span class="relative z-10 flex items-center">
                                    <svg class="w-4 h-4 mr-2.5 transition-transform group-hover:rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                    </svg>
                                    Konfirmasi Pembayaran
                                </span>
                                <div class="absolute inset-0 bg-gradient-to-r from-unmaris-blue to-indigo-600 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            </button>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>


</div>