<div class="space-y-6 animate-in fade-in duration-500">
    
    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="space-y-2">
            <h1 class="text-3xl font-black text-[#002855] tracking-tight flex items-center gap-3">
                <div class="w-12 h-12 bg-[#002855] rounded-2xl flex items-center justify-center text-[#fcc000] shadow-xl shadow-blue-900/20">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                Verifikasi Pembayaran
            </h1>
            <p class="text-slate-400 font-bold text-sm ml-1 uppercase tracking-widest italic">Pusat Validasi Struk Transfer & Konfirmasi Deposit Mahasiswa</p>
        </div>
        
        <div class="flex items-center gap-3 bg-white px-6 py-3 rounded-2xl border border-slate-200 shadow-sm">
            <span class="w-3 h-3 bg-amber-500 rounded-full animate-ping"></span>
            <span class="text-[10px] font-black text-[#002855] uppercase tracking-widest">{{ $pembayarans->count() }} Antrean Pending</span>
        </div>
    </div>

    {{-- Main Table --}}
    <div class="bg-white  rounded-2xl shadow-xl border border-slate-200 overflow-hidden relative">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-[#002855] text-white">
                    <tr>
                        <th class="px-8 py-5 text-left text-[10px] font-bold uppercase tracking-[0.2em]">Mahasiswa & Tagihan</th>
                        <th class="px-6 py-5 text-right text-[10px] font-bold uppercase tracking-[0.2em]">Nominal Transfer</th>
                        <th class="px-6 py-5 text-center text-[10px] font-bold uppercase tracking-[0.2em]">Waktu Konfirmasi</th>
                        <th class="px-6 py-5 text-center text-[10px] font-bold uppercase tracking-[0.2em]">Preview Struk</th>
                        <th class="px-8 py-5 text-right text-[10px] font-bold uppercase tracking-[0.2em]">Opsi Validasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($pembayarans as $bayar)
                    <tr class="hover:bg-slate-50/80 transition-colors group">
                        <td class="px-8 py-6 whitespace-nowrap">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-slate-100 text-[#002855] rounded-2xl flex items-center justify-center font-black text-sm shadow-inner uppercase italic border-2 border-white">
                                    {{ substr($bayar->tagihan->mahasiswa->person->nama_lengkap ?? 'M', 0, 1) }}
                                </div>
                                <div>
                                    <div class="text-sm font-black text-[#002855] uppercase tracking-tight">{{ $bayar->tagihan->mahasiswa->person->nama_lengkap ?? '-' }}</div>
                                    <div class="text-[10px] font-mono font-bold text-slate-400 mt-1 uppercase">{{ $bayar->tagihan->mahasiswa->nim }} &bull; {{ $bayar->tagihan->deskripsi }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-6 text-right whitespace-nowrap">
                            <p class="text-lg font-black text-[#002855] italic tracking-tighter">Rp {{ number_format($bayar->nominal_bayar, 0, ',', '.') }}</p>
                            <p class="text-[9px] font-bold text-indigo-400 uppercase tracking-widest mt-1">Metode: {{ str_replace('_', ' ', $bayar->metode_pembayaran) }}</p>
                        </td>
                        <td class="px-6 py-6 text-center whitespace-nowrap">
                            <p class="text-xs font-bold text-slate-600">{{ $bayar->created_at->format('d M Y') }}</p>
                            <p class="text-[10px] text-slate-400 font-mono mt-1">{{ $bayar->created_at->format('H:i') }} WITA</p>
                        </td>
                        <td class="px-6 py-6 text-center whitespace-nowrap">
                            <button wire:click="openPreview('{{ $bayar->id }}')" class="px-4 py-2 bg-indigo-50 text-indigo-600 rounded-xl text-[9px] font-black uppercase tracking-widest hover:bg-[#002855] hover:text-white transition-all shadow-sm">
                                Buka Dokumen
                            </button>
                        </td>
                        <td class="px-8 py-6 text-right whitespace-nowrap">
                            <div class="flex justify-end gap-2">
                                <button wire:click="approve('{{ $bayar->id }}')" wire:confirm="Sah-kan pembayaran ini?" class="px-5 py-2.5 bg-emerald-500 text-white rounded-xl text-[9px] font-black uppercase tracking-widest shadow-lg shadow-emerald-500/20 hover:bg-emerald-600 transition-all">Terima</button>
                                <button wire:click="openPreview('{{ $bayar->id }}')" class="px-5 py-2.5 bg-rose-50 text-rose-500 rounded-xl text-[9px] font-black uppercase tracking-widest hover:bg-rose-500 hover:text-white transition-all">Tolak</button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-32 text-center">
                            <div class="flex flex-col items-center justify-center opacity-30 grayscale pointer-events-none">
                                <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center text-4xl mb-6 shadow-inner italic">☕</div>
                                <p class="text-xs font-black text-slate-500 uppercase tracking-[0.4em]">Antrean Bersih</p>
                                <p class="text-[10px] text-slate-400 mt-2 font-medium tracking-widest uppercase">Semua pembayaran telah divalidasi</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- MODAL PREVIEW & REJECT ACTION --}}
    @if($showPreviewModal && $selectedPayment)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/80 backdrop-blur-md p-4 animate-in fade-in duration-300">
        <div class="bg-white rounded-[3rem] shadow-2xl w-full max-w-5xl overflow-hidden flex flex-col md:flex-row h-[85vh] animate-in zoom-in-95 border border-white/20">
            
            {{-- Bagian Kiri: Dokumen --}}
            <div class="md:w-3/5 bg-slate-100 flex flex-col p-4 relative overflow-hidden">
                <div class="bg-white/80 backdrop-blur rounded-2xl p-4 mb-4 flex justify-between items-center z-10">
                    <h4 class="text-xs font-black text-[#002855] uppercase tracking-widest">Dokumen Bukti Transfer</h4>
                    <span class="text-[9px] font-mono font-bold text-slate-400 italic">PATH: {{ $selectedPayment->bukti_bayar_path }}</span>
                </div>
                
                <div class="flex-1 rounded-3xl overflow-hidden border-4 border-white shadow-2xl relative bg-slate-200">
                    @if(str_ends_with($selectedPayment->bukti_bayar_path, '.pdf'))
                        <iframe src="{{ asset('storage/'.$selectedPayment->bukti_bayar_path) }}" class="w-full h-full border-none"></iframe>
                    @else
                        <img src="{{ asset('storage/'.$selectedPayment->bukti_bayar_path) }}" class="w-full h-full object-contain">
                    @endif
                </div>
            </div>

            {{-- Bagian Kanan: Decision Center --}}
            <div class="md:w-2/5 flex flex-col p-10 bg-white">
                <div class="flex justify-between items-start mb-10">
                    <div>
                        <h3 class="text-2xl font-black text-[#002855] uppercase tracking-tight italic">Decision Panel</h3>
                        <p class="text-xs font-bold text-slate-400 mt-1 uppercase tracking-widest">Validasi Kelayakan Transaksi</p>
                    </div>
                    <button wire:click="$set('showPreviewModal', false)" class="p-3 bg-slate-50 text-slate-400 hover:bg-rose-500 hover:text-white rounded-2xl transition-all"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>

                <div class="space-y-6 flex-1 overflow-y-auto custom-scrollbar pr-2">
                    {{-- Summary Box --}}
                    <div class="bg-slate-50 p-6 rounded-3xl border border-slate-100 space-y-4">
                        <div class="flex justify-between items-center border-b border-slate-200 pb-3">
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Pengirim</span>
                            <span class="text-xs font-black text-[#002855] uppercase">{{ $selectedPayment->tagihan->mahasiswa->person->nama_lengkap }}</span>
                        </div>
                        <div class="flex justify-between items-center border-b border-slate-200 pb-3">
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Nominal</span>
                            <span class="text-lg font-black text-[#002855] italic tracking-tighter">Rp {{ number_format($selectedPayment->nominal_bayar, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Tanggal Bayar</span>
                            <span class="text-xs font-bold text-slate-600">{{ $selectedPayment->tanggal_bayar->format('d M Y, H:i') }}</span>
                        </div>
                    </div>

                    {{-- Form Penolakan --}}
                    <div class="space-y-3 pt-4">
                        <label class="block text-[10px] font-black text-rose-500 uppercase tracking-widest ml-1">Alasan Penolakan (Jika Ditolak)</label>
                        <textarea wire:model="catatanReject" 
                            class="w-full rounded-2xl border-slate-200 bg-slate-50 py-4 px-5 text-sm font-medium focus:ring-rose-500 focus:border-rose-500 transition-all outline-none resize-none"
                            rows="4" placeholder="Misal: Bukti transfer terpotong, Nama tidak sesuai, atau nominal tidak cocok..."></textarea>
                        @error('catatanReject') <span class="text-[9px] font-bold text-rose-600 ml-1 uppercase">{{ $message }}</span> @enderror
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="pt-10 flex gap-4">
                    <button wire:click="reject('{{ $selectedPayment->id }}')" 
                        class="flex-1 py-4 bg-rose-50 text-rose-600 rounded-2xl font-black text-[10px] uppercase tracking-[0.2em] hover:bg-rose-500 hover:text-white transition-all shadow-xl shadow-rose-500/10">
                        Tolak Pembayaran
                    </button>
                    <button wire:click="approve('{{ $selectedPayment->id }}')" 
                        class="flex-[2] py-4 bg-[#002855] text-[#fcc000] rounded-2xl font-black text-[10px] uppercase tracking-[0.2em] shadow-2xl shadow-blue-900/30 hover:scale-105 active:scale-95 transition-all">
                        Sah & Update Saldo
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- SweetAlert2 Listener --}}
    @script
    <script>
        $wire.on('swal:success', data => { alert(data[0].text); });
        $wire.on('swal:error', data => { alert(data[0].text); });
        $wire.on('swal:info', data => { alert(data[0].text); });
    </script>
    @endscript

    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    </style>
</div>