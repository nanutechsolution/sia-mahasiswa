<div class="space-y-8 animate-in fade-in duration-500">
    {{-- Header --}}
    <div>
        <h1 class="text-2xl font-black text-[#002855]">Informasi Keuangan</h1>
        <p class="text-slate-500 text-sm mt-1">Pantau riwayat tagihan dan lakukan konfirmasi pembayaran mandiri.</p>
    </div>

    @if (session()->has('success'))
        <div class="bg-emerald-50 border border-emerald-100 p-4 rounded-2xl text-emerald-800 text-sm flex items-center shadow-sm">
            <svg class="w-5 h-5 mr-3 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
            <span class="font-bold">{{ session('success') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- List Tagihan --}}
        <div class="lg:col-span-2 space-y-6">
            @forelse($tagihans as $tagihan)
                <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden hover:shadow-md transition-all">
                    <div class="p-6 md:p-8 flex flex-col md:flex-row justify-between gap-6">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-widest {{ $tagihan->status_bayar == 'LUNAS' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                    {{ $tagihan->status_bayar }}
                                </span>
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                                    Semester {{ $tagihan->tahunAkademik->nama_tahun ?? '-' }}
                                </span>
                            </div>
                            <h3 class="text-lg font-black text-[#002855] leading-tight mb-2">{{ $tagihan->deskripsi }}</h3>
                            
                            {{-- Item Rincian --}}
                            <div class="space-y-1 mt-4">
                                @foreach($tagihan->rincian_item ?? [] as $item)
                                    <div class="flex justify-between text-xs text-slate-500 italic">
                                        <span>{{ $item['nama'] }}</span>
                                        <span>Rp {{ number_format($item['nominal'], 0, ',', '.') }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="md:w-48 text-right flex flex-col justify-between">
                            <div>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Tagihan</p>
                                <p class="text-xl font-black text-slate-800">Rp {{ number_format($tagihan->total_tagihan, 0, ',', '.') }}</p>
                            </div>
                            
                            @if($tagihan->status_bayar != 'LUNAS')
                                <button wire:click="pilihTagihan('{{ $tagihan->id }}')" 
                                    class="mt-4 px-4 py-2.5 bg-[#002855] text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-[#001a38] transition-all shadow-lg shadow-indigo-900/20">
                                    Konfirmasi Bayar
                                </button>
                            @else
                                <div class="mt-4 flex items-center justify-end text-emerald-600 font-bold text-xs">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                                    Terbayar Lunas
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Riwayat Pembayaran Internal --}}
                    @if($tagihan->pembayarans->count() > 0)
                        <div class="bg-slate-50 px-8 py-4 border-t border-slate-100">
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2">Riwayat Setoran</p>
                            <div class="space-y-2">
                                @foreach($tagihan->pembayarans as $pembayaran)
                                    <div class="flex justify-between items-center text-xs">
                                        <span class="text-slate-500 font-medium">
                                            {{ \Carbon\Carbon::parse($pembayaran->tanggal_bayar)->format('d M Y') }} 
                                            <span class="mx-1 text-slate-300">|</span> 
                                            Rp {{ number_format($pembayaran->nominal_bayar, 0, ',', '.') }}
                                        </span>
                                        <span class="font-bold uppercase text-[9px] {{ $pembayaran->status_verifikasi == 'VERIFIED' ? 'text-emerald-600' : 'text-amber-500' }}">
                                            {{ $pembayaran->status_verifikasi }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @empty
                <div class="text-center py-20 bg-white rounded-3xl border border-slate-200 border-dashed">
                    <p class="text-slate-400 font-medium italic">Belum ada tagihan yang diterbitkan.</p>
                </div>
            @endforelse
        </div>

        {{-- Sidebar Form --}}
        <div class="lg:col-span-1">
            @if($tagihanIdSelected)
                <div class="bg-white rounded-3xl shadow-xl border border-slate-200 overflow-hidden sticky top-6 animate-in slide-in-from-right-4">
                    <div class="bg-[#002855] px-6 py-5 flex items-center justify-between">
                        <h3 class="text-xs font-black text-[#fcc000] uppercase tracking-widest">Konfirmasi Bayar</h3>
                        <button wire:click="$set('tagihanIdSelected', null)" class="text-white/50 hover:text-white">&times;</button>
                    </div>
                    
                    <form wire:submit.prevent="simpanPembayaran" class="p-6 space-y-5">
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Nominal Transfer (Rp)</label>
                            <input type="number" wire:model="nominalBayar" 
                                class="w-full rounded-xl border-slate-200 bg-slate-50 text-slate-900 py-2 pl-4 font-black focus:outline-none focus:ring-2 focus:ring-[#fcc000] transition-all">
                            @error('nominalBayar') <span class="text-rose-500 text-[10px] font-bold mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Tanggal Transfer</label>
                            <input type="date" wire:model="tglBayar" 
                                class="w-full rounded-xl border-slate-200 bg-slate-50 text-slate-900 py-2 pl-4 font-bold focus:outline-none focus:ring-2 focus:ring-[#fcc000] transition-all">
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Upload Bukti (JPG/PDF)</label>
                            <div class="relative">
                                <input type="file" wire:model="fileBukti" class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:bg-[#002855] file:text-white hover:file:bg-[#001a38] cursor-pointer">
                            </div>
                            @error('fileBukti') <span class="text-rose-500 text-[10px] font-bold mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="pt-4">
                            <button type="submit" wire:loading.attr="disabled"
                                class="w-full py-4 bg-[#fcc000] text-[#002855] rounded-2xl font-black text-xs uppercase tracking-widest shadow-xl shadow-orange-500/20 hover:scale-[1.02] active:scale-95 transition-all">
                                <span wire:loading.remove>Kirim Konfirmasi</span>
                                <span wire:loading>Memproses...</span>
                            </button>
                        </div>
                    </form>
                </div>
            @else
                <div class="bg-indigo-50/50 rounded-3xl p-8 border-2 border-dashed border-indigo-100 text-center">
                    <div class="w-16 h-16 bg-white rounded-2xl shadow-sm flex items-center justify-center text-2xl mx-auto mb-4">💳</div>
                    <h3 class="text-sm font-black text-indigo-900 uppercase tracking-tight">Proses Pembayaran</h3>
                    <p class="text-xs text-indigo-400 mt-2 leading-relaxed">Klik tombol "Konfirmasi Bayar" pada tagihan yang ingin Anda laporkan.</p>
                </div>
            @endif
        </div>
    </div>
</div>