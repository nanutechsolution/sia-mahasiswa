<div class="space-y-6 max-w-[1600px] mx-auto p-4 md:p-8 animate-in fade-in duration-500">
    
    {{-- Header --}}
    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6">
        <div class="space-y-2">
            <h1 class="text-3xl font-black text-[#002855] tracking-tight flex items-center gap-3">
                <div class="w-12 h-12 bg-[#002855] rounded-2xl flex items-center justify-center text-[#fcc000] shadow-xl shadow-blue-900/20">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" /></svg>
                </div>
                Ploting Kurikulum
            </h1>
            <p class="text-slate-400 font-medium text-sm ml-1">Pemetaan massal kurikulum ke data mahasiswa (Legacy & Transisi).</p>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="bg-white p-4 shadow-sm rounded-[2rem] border border-slate-200 grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 pl-1">Program Studi</label>
            <select wire:model.live="filterProdiId" class="w-full rounded-xl border-slate-200 bg-slate-50 py-3 text-sm font-bold text-[#002855] focus:ring-[#fcc000] focus:border-[#fcc000]">
                <option value="">Semua Prodi</option>
                @foreach($prodis as $p) <option value="{{ $p->id }}">{{ $p->jenjang }} - {{ $p->nama_prodi }}</option> @endforeach
            </select>
        </div>
        <div>
            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 pl-1">Angkatan</label>
            <select wire:model.live="filterAngkatan" class="w-full rounded-xl border-slate-200 bg-slate-50 py-3 text-sm font-bold text-[#002855] focus:ring-[#fcc000] focus:border-[#fcc000]">
                <option value="">Semua Angkatan</option>
                @foreach($angkatans as $akt) <option value="{{ $akt->id_tahun }}">{{ $akt->id_tahun }}</option> @endforeach
            </select>
        </div>
        <div>
            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 pl-1">Status Kurikulum</label>
            <select wire:model.live="filterStatusKurikulum" class="w-full rounded-xl border-slate-200 bg-slate-50 py-3 text-sm font-bold text-[#002855] focus:ring-[#fcc000] focus:border-[#fcc000]">
                <option value="">Semua Status</option>
                <option value="null">Belum Di-plot (Kosong)</option>
                <option value="has">Sudah Di-plot</option>
            </select>
        </div>
        <div>
            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 pl-1">Cari Nama/NIM</label>
            <div class="relative">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari..." class="w-full rounded-xl border-slate-200 bg-slate-50 py-3 pl-10 text-sm font-bold text-[#002855] focus:ring-[#fcc000] focus:border-[#fcc000]">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Bulk Action Area (Hanya muncul jika ada yang dipilih) --}}
    @if($totalSelected > 0)
    <div class="bg-[#002855] rounded-[2rem] p-6 shadow-2xl shadow-blue-900/20 flex flex-col md:flex-row items-center justify-between gap-6 animate-in slide-in-from-top-4 border border-indigo-400/20 sticky top-4 z-40">
        <div class="flex items-center gap-4 text-white">
            <div class="w-12 h-12 bg-[#fcc000] rounded-xl flex items-center justify-center text-[#002855] font-black text-xl shadow-inner">
                {{ $totalSelected }}
            </div>
            <div>
                <h3 class="font-black tracking-widest uppercase text-sm">Mahasiswa Terpilih</h3>
                <p class="text-[10px] text-indigo-200 font-medium mt-1">Siap untuk dipetakan ke kurikulum baru.</p>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row items-center gap-3 w-full md:w-auto">
            @if($filterProdiId)
                <select wire:model="targetKurikulumId" class="w-full sm:w-64 rounded-xl border-none bg-white/10 text-white py-3 px-4 text-sm font-bold focus:ring-[#fcc000] [&>option]:text-slate-800">
                    <option value="">-- Pilih Target Kurikulum --</option>
                    @foreach($targetKurikulums as $tk)
                        <option value="{{ $tk->id }}">{{ $tk->nama_kurikulum }} ({{ $tk->tahun_mulai }})</option>
                    @endforeach
                </select>
                @error('targetKurikulumId') <span class="text-rose-400 text-xs font-bold absolute -bottom-5">{{ $message }}</span> @enderror
                
                <button wire:click="terapkanKurikulum" wire:confirm="Anda yakin ingin memindahkan {{ $totalSelected }} mahasiswa ini ke kurikulum terpilih?" class="w-full sm:w-auto px-8 py-3 bg-[#fcc000] text-[#002855] rounded-xl font-black text-xs uppercase tracking-widest hover:bg-[#ffca28] hover:scale-105 transition-all shadow-lg shadow-amber-500/20 whitespace-nowrap">
                    Terapkan
                </button>
            @else
                <div class="px-6 py-3 bg-white/10 rounded-xl text-xs font-bold text-amber-300 border border-amber-400/30">
                    ⚠ Pilih filter Program Studi terlebih dahulu.
                </div>
            @endif
        </div>
    </div>
    @endif

    {{-- Main Table --}}
    <div class="bg-white rounded-[3rem] shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50/50">
                    <tr>
                        <th class="px-6 py-5 text-center w-16">
                            <input type="checkbox" wire:model.live="selectAll" class="w-5 h-5 rounded border-slate-300 text-[#002855] focus:ring-[#fcc000] cursor-pointer">
                        </th>
                        <th class="px-4 py-5 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Identitas Mahasiswa</th>
                        <th class="px-4 py-5 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Prodi & Angkatan</th>
                        <th class="px-6 py-5 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Status Kurikulum Saat Ini</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($mahasiswas as $mhs)
                    <tr class="hover:bg-slate-50/50 transition-colors group {{ in_array($mhs->id, $selectedMahasiswa) ? 'bg-indigo-50/30' : '' }}">
                        <td class="px-6 py-4 text-center align-middle">
                            <input type="checkbox" wire:model.live="selectedMahasiswa" value="{{ $mhs->id }}" class="w-5 h-5 rounded border-slate-300 text-[#002855] focus:ring-[#fcc000] cursor-pointer">
                        </td>
                        <td class="px-4 py-4">
                            <div class="text-sm font-black text-[#002855] uppercase tracking-tight">{{ $mhs->person->nama_lengkap ?? 'Unknown' }}</div>
                            <div class="text-xs font-mono font-bold text-slate-500 mt-1">{{ $mhs->nim }}</div>
                        </td>
                        <td class="px-4 py-4">
                            <div class="text-xs font-bold text-slate-700 uppercase">{{ $mhs->prodi->nama_prodi ?? '-' }}</div>
                            <div class="text-[10px] font-bold text-indigo-500 uppercase tracking-widest mt-1">Angkatan {{ $mhs->angkatan_id }}</div>
                        </td>
                        <td class="px-6 py-4">
                            @if($mhs->kurikulum_id)
                                <div class="inline-flex flex-col">
                                    <span class="px-3 py-1 bg-emerald-50 text-emerald-700 border border-emerald-100 rounded-lg text-[10px] font-black uppercase tracking-wider">
                                        {{ $mhs->kurikulum->nama_kurikulum }}
                                    </span>
                                    @if($mhs->kurikulum->tahun_mulai)
                                    <span class="text-[9px] text-slate-400 font-bold mt-1 ml-1">Edisi: {{ $mhs->kurikulum->tahun_mulai }}</span>
                                    @endif
                                </div>
                            @else
                                <span class="px-3 py-1.5 bg-rose-50 text-rose-600 border border-rose-100 rounded-lg text-[10px] font-black uppercase tracking-wider animate-pulse inline-flex items-center gap-2">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                    BELUM DI-PLOT
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-20 text-center">
                            <div class="text-4xl mb-4 grayscale opacity-20">📂</div>
                            <p class="text-xs font-black text-slate-400 uppercase tracking-[0.2em]">Tidak ada data mahasiswa ditemukan</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-8 py-5 border-t border-slate-100 bg-slate-50/80">
            {{ $mahasiswas->links() }}
        </div>
    </div>

    {{-- System Footer --}}
    <div class="pt-8 flex flex-col items-center gap-2 opacity-20 grayscale pointer-events-none">
        <p class="text-[9px] font-black uppercase tracking-[0.6em] text-[#002855]">CURRICULUM MAPPING TOOL &bull; v4.2 PRO</p>
    </div>

    {{-- SweetAlert2 Listener --}}
    @script
    <script>
        $wire.on('swal:success', data => {
            alert(data[0].text); // Replace with SweetAlert if you have it implemented in layout
        });
        $wire.on('swal:error', data => {
            alert(data[0].text); // Replace with SweetAlert if you have it implemented in layout
        });
    </script>
    @endscript
</div>