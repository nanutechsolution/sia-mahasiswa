<div class="space-y-8">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-[#002855] tracking-tight">Daftar Ulang & Peresmian NIM</h1>
            <p class="text-slate-500 text-sm mt-1">Validasi pembayaran dan generate NIM resmi mahasiswa baru sesuai format prodi.</p>
        </div>
        
        <div class="flex items-center gap-2 bg-indigo-50 text-[#002855] px-4 py-2 rounded-xl text-xs font-bold border border-indigo-100">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>Total Camaba: {{ $camabas->total() }}</span>
        </div>
    </div>

    {{-- Alert Messages --}}
    @if (session()->has('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-100 text-emerald-800 rounded-xl text-sm font-bold flex items-center shadow-sm animate-in fade-in slide-in-from-top-2">
            <svg class="w-5 h-5 mr-3 text-emerald-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="p-4 bg-rose-50 border border-rose-100 text-rose-800 rounded-xl text-sm font-bold flex items-center shadow-sm animate-in shake">
            <svg class="w-5 h-5 mr-3 text-rose-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" /></svg>
            {{ session('error') }}
        </div>
    @endif

    {{-- Filters --}}
    <div class="bg-white p-5 shadow-sm rounded-2xl border border-slate-200 grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Filter Program Studi</label>
            <div class="relative">
                <select wire:model.live="filterProdiId" class="block w-full rounded-xl border-slate-200 bg-slate-50 py-2.5 pl-4 pr-10 text-sm focus:border-[#002855] focus:ring-[#002855] font-bold text-slate-700 transition-all outline-none appearance-none cursor-pointer">
                    <option value="">Semua Prodi</option>
                    @foreach($prodis as $p)
                        <option value="{{ $p->id }}">{{ $p->nama_prodi }}</option>
                    @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-500">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                </div>
            </div>
        </div>
        <div>
            <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Cari Nama / No. Daftar</label>
            <div class="relative">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Ketik pencarian..." class="block w-full rounded-xl border-slate-200 bg-slate-50 py-2.5 pl-10 pr-4 text-sm focus:border-[#002855] focus:ring-[#002855] font-bold text-slate-700 transition-all outline-none">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center px-3 text-slate-400">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Data Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden relative">
        
        {{-- Loading Overlay --}}
        <div wire:loading.flex wire:target="generateNimResmi, search, filterProdiId, gotoPage" class="absolute inset-0 z-20 bg-white/60 backdrop-blur-[1px] items-center justify-center hidden">
             <div class="flex flex-col items-center justify-center p-4 bg-white rounded-2xl shadow-xl border border-slate-100">
                 <svg class="w-8 h-8 text-[#002855] animate-spin mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                 <span class="text-xs font-bold text-slate-500 animate-pulse">Memproses...</span>
             </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-[#002855] text-white">
                    <tr>
                        <th class="px-6 py-4 text-left text-[10px] font-bold uppercase tracking-widest">Identitas Camaba</th>
                        <th class="px-6 py-4 text-left text-[10px] font-bold uppercase tracking-widest w-1/3">Status Keuangan</th>
                        <th class="px-6 py-4 text-center text-[10px] font-bold uppercase tracking-widest">Dispensasi</th>
                        <th class="px-6 py-4 text-right text-[10px] font-bold uppercase tracking-widest">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($camabas as $mhs)
                    @php
                        // Hitung Persentase Pembayaran
                        $tagihanWajib = $mhs->tagihan->sum('total_tagihan'); // Total tagihan
                        $sudahBayar = $mhs->tagihan->sum('total_bayar');
                        
                        $minPercent = $mhs->programKelas->min_pembayaran_persen ?? 50;
                        $paidPercent = ($tagihanWajib > 0) ? round(($sudahBayar / $tagihanWajib) * 100) : 0;
                        
                        $dispensasi = $mhs->data_tambahan['bebas_keuangan'] ?? false;
                        $eligible = $dispensasi || ($paidPercent >= $minPercent);
                    @endphp
                    <tr class="hover:bg-slate-50/80 transition-colors group">
                        <td class="px-6 py-4 align-top">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center font-bold text-slate-500 text-xs">
                                    {{ substr($mhs->nama_lengkap, 0, 1) }}
                                </div>
                                <div>
                                    <div class="text-sm font-bold text-slate-800">{{ $mhs->person->nama_lengkap ?? $mhs->nama_lengkap }}</div>
                                    <div class="text-[10px] text-slate-400 mt-0.5 font-mono">No. Daftar: {{ $mhs->nim }}</div>
                                    <div class="text-[10px] font-bold text-[#002855] bg-indigo-50 px-1.5 py-0.5 rounded w-fit mt-1 border border-indigo-100">
                                        {{ $mhs->prodi->nama_prodi }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 align-middle">
                            <div class="flex flex-col gap-1.5 w-full max-w-xs">
                                <div class="flex justify-between text-[10px] font-black uppercase tracking-wide">
                                    <span class="text-slate-400">Bayar: {{ $paidPercent }}%</span>
                                    <span class="text-[#002855]">Syarat: {{ $minPercent }}%</span>
                                </div>
                                <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden shadow-inner border border-slate-200 relative">
                                    {{-- Marker Batas Minimal --}}
                                    <div class="absolute top-0 bottom-0 w-0.5 bg-slate-300 z-10" style="left: {{ $minPercent }}%"></div>
                                    <div class="h-full transition-all duration-1000 ease-out {{ $paidPercent >= $minPercent ? 'bg-emerald-500' : 'bg-amber-400' }}" style="width: {{ min($paidPercent, 100) }}%"></div>
                                </div>
                                @if(!$eligible)
                                <p class="text-[10px] text-rose-500 font-bold mt-0.5">* Belum memenuhi syarat minimal.</p>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 align-middle text-center">
                            @if($dispensasi)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[9px] font-black bg-[#fcc000] text-[#002855] border border-amber-300 shadow-sm uppercase tracking-wide">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    Aktif
                                </span>
                            @else
                                <span class="text-[10px] font-bold text-slate-300 italic">Non-Aktif</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 align-middle text-right space-x-2">
                            <button wire:click="edit('{{ $mhs->id }}')" class="text-slate-400 hover:text-amber-500 text-[10px] font-black uppercase tracking-widest transition-colors" title="Atur Dispensasi">
                                Dispensasi
                            </button>
                            
                            @if($eligible)
                                <button wire:click="generateNimResmi('{{ $mhs->id }}')" 
                                    wire:confirm="Yakin ingin meresmikan mahasiswa ini? NIM akan digenerate otomatis dan User Login akan diupdate."
                                    class="inline-flex items-center px-4 py-2 bg-[#002855] text-white text-[10px] font-black uppercase tracking-widest rounded-xl shadow-lg hover:bg-black transition-all hover:scale-105 active:scale-95">
                                    <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    Generate NIM
                                </button>
                            @else
                                <button disabled class="inline-flex items-center px-4 py-2 bg-slate-100 text-slate-400 text-[10px] font-black uppercase tracking-widest rounded-xl border border-slate-200 cursor-not-allowed" title="Lunasi pembayaran atau aktifkan dispensasi">
                                    <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                                    Locked
                                </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-6 py-16 text-center text-slate-400 italic text-sm">Tidak ada calon mahasiswa baru.</td></tr>
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
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 animate-in fade-in duration-200">
        <div class="bg-white rounded-[2rem] shadow-2xl w-full max-w-md overflow-hidden border border-white/20">
            <div class="bg-[#002855] px-6 py-5 flex items-center gap-4">
                <div class="bg-[#fcc000] p-2.5 rounded-xl text-[#002855] shadow-lg">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <div>
                    <h3 class="text-lg font-black text-white uppercase tracking-tight">Dispensasi Keuangan</h3>
                    <p class="text-[10px] text-slate-300 font-bold uppercase tracking-wider">{{ $nama_lengkap }}</p>
                </div>
            </div>
            
            <div class="p-8 space-y-6">
                <div class="bg-amber-50 p-6 rounded-2xl border border-amber-100">
                    <label class="flex items-start gap-4 cursor-pointer group">
                        <input type="checkbox" wire:model="bebas_keuangan" class="mt-1 h-6 w-6 text-[#002855] border-slate-300 rounded focus:ring-[#fcc000] bg-white transition-all group-hover:scale-110 shadow-sm cursor-pointer">
                        <div>
                            <span class="block text-sm font-black text-[#002855] uppercase tracking-tight group-hover:text-amber-700 transition-colors">Aktifkan Hak Khusus</span>
                            <span class="block text-xs text-slate-600 mt-1.5 leading-relaxed">
                                Dengan mencentang ini, sistem akan <strong>mengabaikan syarat minimal pembayaran</strong>. Mahasiswa dapat diresmikan (mendapat NIM) dan mengisi KRS.
                            </span>
                        </div>
                    </label>
                </div>
                
                <div class="flex gap-3 pt-2">
                    <button wire:click="batal" class="flex-1 py-3 text-xs font-black text-slate-400 hover:text-slate-600 uppercase tracking-widest transition-colors">Batal</button>
                    <button wire:click="save" class="flex-1 py-3 bg-[#002855] text-white rounded-xl font-black text-xs uppercase tracking-widest shadow-lg hover:bg-[#001a38] transition-all transform hover:-translate-y-0.5 active:scale-95">Simpan Perubahan</button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>