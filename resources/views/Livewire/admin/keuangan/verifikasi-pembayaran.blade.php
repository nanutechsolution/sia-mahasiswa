<div>
{{-- SEO & Header Layout Integration --}}
<x-slot name="title">Verifikasi Pembayaran</x-slot>
<x-slot name="header">Verifikasi Pembayaran Masuk</x-slot>

<div class="space-y-8">
    {{-- Top Toolbar --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <p class="text-slate-500 text-sm font-medium">Validasi bukti transfer mahasiswa untuk pembaharuan saldo tagihan secara otomatis.</p>
        </div>
        
        <div class="flex items-center space-x-2 text-[10px] font-black uppercase tracking-widest text-slate-400">
            <span class="w-2 h-2 bg-unmaris-yellow rounded-full animate-pulse"></span>
            <span>Real-time Verification Pool</span>
        </div>
    </div>

    {{-- Success Notification --}}
    @if (session()->has('success'))
        <div class="bg-emerald-50 border border-emerald-100 p-4 rounded-2xl text-emerald-800 text-sm flex items-center animate-in fade-in slide-in-from-top-2 duration-300 shadow-sm">
            <svg class="w-5 h-5 mr-3 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            <span class="font-bold">{{ session('success') }}</span>
        </div>
    @endif

    {{-- Main Table Section --}}
    <div class="bg-white rounded-3xl shadow-xl border border-slate-200 overflow-hidden animate-in fade-in duration-500">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                        <th class="px-8 py-5">Mahasiswa</th>
                        <th class="px-8 py-5">Nominal & Metode</th>
                        <th class="px-8 py-5">Dokumen Bukti</th>
                        <th class="px-8 py-5">Waktu Transaksi</th>
                        <th class="px-8 py-5 text-right">Aksi Validasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($pembayarans as $bayar)
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        {{-- Mahasiswa --}}
                        <td class="px-8 py-6">
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0 w-11 h-11 bg-unmaris-blue text-unmaris-yellow rounded-2xl flex items-center justify-center font-black text-sm shadow-sm">
                                    {{ substr($bayar->tagihan->mahasiswa->nama_lengkap, 0, 1) }}
                                </div>
                                <div>
                                    <div class="text-sm font-black text-slate-800 leading-tight">{{ $bayar->tagihan->mahasiswa->nama_lengkap }}</div>
                                    <div class="text-[10px] font-mono font-bold text-indigo-500 mt-1 uppercase tracking-tighter">{{ $bayar->tagihan->mahasiswa->nim }}</div>
                                    <div class="text-[10px] text-slate-400 font-bold mt-0.5">{{ $bayar->tagihan->deskripsi }}</div>
                                </div>
                            </div>
                        </td>

                        {{-- Nominal --}}
                        <td class="px-8 py-6">
                            <div class="text-[15px] font-black text-unmaris-blue tabular-nums">
                                Rp {{ number_format($bayar->nominal_bayar, 0, ',', '.') }}
                            </div>
                            <div class="inline-flex items-center mt-1 px-2 py-0.5 rounded-lg bg-slate-100 border border-slate-200 text-[9px] font-black text-slate-500 uppercase tracking-widest">
                                {{ $bayar->metode_pembayaran }}
                            </div>
                        </td>

                        {{-- Bukti --}}
                        <td class="px-8 py-6">
                            @if($bayar->bukti_bayar_path)
                                <a href="{{ asset('storage/'.$bayar->bukti_bayar_path) }}" target="_blank" 
                                   class="inline-flex items-center text-[11px] font-black text-unmaris-blue uppercase tracking-widest hover:text-unmaris-gold transition-colors group/link">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <span class="border-b-2 border-transparent group-hover/link:border-unmaris-gold transition-all">Lihat Bukti</span>
                                </a>
                            @else
                                <span class="text-[10px] font-bold text-rose-400 uppercase italic tracking-tighter">Lampiran Kosong</span>
                            @endif
                        </td>

                        {{-- Tanggal --}}
                        <td class="px-8 py-6 whitespace-nowrap text-sm text-slate-500">
                            <div class="text-[12px] font-bold text-slate-700">{{ $bayar->tanggal_bayar->format('d M Y') }}</div>
                            <div class="text-[10px] font-mono text-slate-400 mt-1">{{ $bayar->tanggal_bayar->format('H:i') }} WIB</div>
                        </td>

                        {{-- Aksi --}}
                        <td class="px-8 py-6 text-right">
                            <div class="flex justify-end space-x-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                <button wire:click="approve('{{ $bayar->id }}')" 
                                        wire:confirm="Yakin validasi pembayaran ini? Saldo tagihan akan bertambah."
                                        class="inline-flex items-center px-4 py-2 bg-emerald-50 text-emerald-600 rounded-xl text-[10px] font-black uppercase tracking-widest border border-emerald-100 hover:bg-emerald-600 hover:text-white transition-all shadow-sm">
                                    Terima
                                </button>
                                
                                <button wire:click="reject('{{ $bayar->id }}')"
                                        wire:confirm="Yakin tolak pembayaran ini?"
                                        class="inline-flex items-center px-4 py-2 bg-rose-50 text-rose-600 rounded-xl text-[10px] font-black uppercase tracking-widest border border-rose-100 hover:bg-rose-600 hover:text-white transition-all shadow-sm">
                                    Tolak
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-8 py-24 text-center">
                            <div class="max-w-xs mx-auto text-slate-400">
                                <div class="w-16 h-16 bg-slate-50 rounded-3xl flex items-center justify-center mx-auto mb-4 border border-slate-100">
                                    <svg class="w-8 h-8 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <p class="font-black text-sm uppercase tracking-widest">Antrian Kosong</p>
                                <p class="text-[11px] mt-1 font-medium">Saat ini tidak ada pembayaran yang menunggu verifikasi.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>


</div>