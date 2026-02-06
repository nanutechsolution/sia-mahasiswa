<div class="space-y-6">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-black text-[#002855]">Daftar Ulang & Peresmian NIM</h1>
            <p class="mt-2 text-sm text-slate-500">Proses migrasi dari Calon Mahasiswa (PMB) menjadi Mahasiswa Aktif.</p>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white p-5 shadow-sm rounded-2xl border border-slate-200 grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Program Studi</label>
            <select wire:model.live="filterProdiId" class="block w-full rounded-xl border-slate-200 bg-slate-50 py-3 pl-4 pr-10 text-sm font-bold text-slate-700 focus:ring-[#002855] transition-all outline-none appearance-none">
                <option value="">Semua Prodi</option>
                @foreach($prodis as $p)
                    <option value="{{ $p->id }}">{{ $p->nama_prodi }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Cari Nama / No. Daftar</label>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari..." class="block w-full rounded-xl border-slate-200 bg-slate-50 py-3 px-4 text-sm font-bold focus:ring-[#002855] outline-none">
        </div>
    </div>

    @if (session()->has('success'))
        <div class="bg-emerald-50 border border-emerald-100 p-4 rounded-xl text-emerald-800 text-sm flex items-center shadow-sm animate-in fade-in slide-in-from-top-2">
            <svg class="w-5 h-5 mr-3 text-emerald-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
            <span class="font-bold">{{ session('success') }}</span>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-rose-50 border border-rose-100 p-4 rounded-xl text-rose-800 text-sm flex items-center shadow-sm animate-in shake">
            <svg class="w-5 h-5 mr-3 text-rose-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" /></svg>
            <span class="font-bold">{{ session('error') }}</span>
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-[#002855] text-white">
                    <tr>
                        <th class="px-6 py-4 text-left text-[10px] font-bold uppercase tracking-widest">Mahasiswa</th>
                        <th class="px-6 py-4 text-left text-[10px] font-bold uppercase tracking-widest">Kewajiban Keuangan</th>
                        <th class="px-6 py-4 text-center text-[10px] font-bold uppercase tracking-widest">Dispensasi</th>
                        <th class="px-6 py-4 text-right text-[10px] font-bold uppercase tracking-widest">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($camabas as $mhs)
                    @php
                        $tagihan = $mhs->tagihan->first();
                        $minPercent = $mhs->programKelas->min_pembayaran_persen ?? 50;
                        $paidPercent = ($tagihan && $tagihan->total_tagihan > 0) ? round(($tagihan->total_bayar / $tagihan->total_tagihan) * 100) : 0;
                        $dispensasi = $mhs->data_tambahan['bebas_keuangan'] ?? false;
                        $eligible = $dispensasi || ($paidPercent >= $minPercent);
                    @endphp
                    <tr class="hover:bg-slate-50/80 transition-colors group">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-bold text-slate-800">{{ $mhs->nama_lengkap }}</div>
                            <div class="text-[10px] font-mono text-slate-400 mt-1 uppercase">{{ $mhs->nim }} &bull; {{ $mhs->prodi->nama_prodi }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col gap-1.5 w-48">
                                <div class="flex justify-between text-[9px] font-black uppercase">
                                    <span class="text-slate-400">Bayar: {{ $paidPercent }}%</span>
                                    <span class="text-[#002855]">Syarat: {{ $minPercent }}%</span>
                                </div>
                                <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden shadow-inner">
                                    <div class="h-full transition-all duration-1000 {{ $paidPercent >= $minPercent ? 'bg-emerald-500' : 'bg-amber-400' }}" style="width: {{ min($paidPercent, 100) }}%"></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($dispensasi)
                                <span class="px-2 py-0.5 rounded text-[9px] font-black bg-[#fcc000] text-[#002855] border border-unmaris-gold shadow-sm">AKTIF</span>
                            @else
                                <span class="text-[9px] font-bold text-slate-300 italic">Tidak Ada</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right space-x-2">
                            <button wire:click="edit('{{ $mhs->id }}')" class="text-[#002855] hover:text-[#fcc000] text-[10px] font-black uppercase tracking-widest">Dispensasi</button>
                            
                            @if($eligible)
                                <button wire:click="generateNimResmi('{{ $mhs->id }}')" 
                                    wire:confirm="Generate NIM resmi untuk mahasiswa ini?"
                                    class="inline-flex items-center px-3 py-1.5 bg-[#002855] text-white text-[10px] font-black uppercase rounded-lg shadow-lg hover:bg-black transition-all">
                                    <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    Generate NIM
                                </button>
                            @else
                                <span class="inline-flex items-center px-3 py-1.5 bg-slate-100 text-slate-400 text-[10px] font-black uppercase rounded-lg border border-slate-200 cursor-not-allowed" title="Pembayaran Belum Mencapai Target">
                                    🔒 Locked
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-6 py-16 text-center text-slate-400 italic text-sm">Tidak ada data camaba.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
            {{ $camabas->links() }}
        </div>
    </div>

    <!-- Modal Dispensasi -->
    @if($showForm)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 animate-in fade-in">
        <div class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-md overflow-hidden border border-white/20">
            <div class="bg-[#002855] px-6 py-5 flex items-center gap-4">
                <div class="bg-[#fcc000] p-2 rounded-xl text-[#002855] shadow-lg">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <div>
                    <h3 class="text-lg font-black text-white uppercase tracking-tight">Dispensasi Keuangan</h3>
                    <p class="text-[10px] text-slate-300 font-bold uppercase">{{ $nama_lengkap }}</p>
                </div>
            </div>
            <div class="p-8 space-y-6">
                <div class="bg-amber-50 p-6 rounded-2xl border border-amber-100">
                    <label class="flex items-start gap-4 cursor-pointer group">
                        <input type="checkbox" wire:model="bebas_keuangan" class="mt-1 h-6 w-6 text-[#002855] border-slate-300 rounded focus:ring-[#fcc000] bg-white transition-all group-hover:scale-110">
                        <div>
                            <span class="block text-sm font-black text-[#002855] uppercase tracking-tight">Aktifkan Dispensasi</span>
                            <span class="block text-xs text-slate-500 mt-1 leading-relaxed italic">
                                Mengizinkan peresmian NIM tanpa harus melunasi pembayaran minimal tagihan daftar ulang.
                            </span>
                        </div>
                    </label>
                </div>
                <div class="flex gap-3">
                    <button wire:click="batal" class="flex-1 py-3 text-xs font-black text-slate-400 uppercase tracking-widest">Batal</button>
                    <button wire:click="save" class="flex-1 py-3 bg-[#002855] text-white rounded-xl font-black text-xs uppercase tracking-widest shadow-xl hover:bg-black transition-all">Simpan Perubahan</button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>