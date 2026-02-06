<div class="space-y-6 md:space-y-8 animate-in fade-in duration-700 pb-12">
    
    {{-- 1. HEADER SECTION --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-6 md:p-8 rounded-[2rem] border border-slate-200 shadow-sm relative overflow-hidden">
        <div class="absolute top-0 right-0 p-8 opacity-[0.03] pointer-events-none">
            <svg class="w-32 h-32 text-[#002855]" fill="currentColor" viewBox="0 0 24 24"><path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
        </div>
        <div class="relative z-10">
            <h1 class="text-2xl md:text-3xl font-black text-[#002855] uppercase tracking-tight">Perwalian Akademik</h1>
            <div class="flex items-center gap-2 mt-1">
                <span class="text-slate-400 text-xs font-bold uppercase tracking-widest">Periode Aktif:</span>
                <span class="bg-indigo-50 text-indigo-700 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-tighter border border-indigo-100 shadow-sm">
                    {{ $taAktifNama }}
                </span>
            </div>
        </div>
        <div class="hidden md:block text-right relative z-10">
            <p class="text-slate-400 text-[10px] font-black uppercase tracking-widest leading-none mb-1">Dosen Pembimbing</p>
            <p class="text-slate-700 font-bold text-sm">{{ $dosen->nama_lengkap_gelar }}</p>
        </div>
    </div>

    {{-- 2. STATS CARDS --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 md:gap-6">
        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Mahasiswa</p>
            <div class="flex items-baseline gap-2">
                <h3 class="text-3xl font-black text-[#002855]">{{ $stats['total'] }}</h3>
                <span class="text-xs text-slate-300 font-bold uppercase">Orang</span>
            </div>
        </div>
        
        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm relative overflow-hidden group">
            @if($stats['pending'] > 0)
                <div class="absolute -right-2 -top-2 w-16 h-16 bg-amber-50 rounded-full blur-xl group-hover:bg-amber-100 transition-colors"></div>
            @endif
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Menunggu Persetujuan</p>
            <div class="flex items-center justify-between relative z-10">
                <h3 class="text-3xl font-black {{ $stats['pending'] > 0 ? 'text-amber-500 animate-pulse' : 'text-slate-300' }}">
                    {{ $stats['pending'] }}
                </h3>
                @if($stats['pending'] > 0)
                    <span class="px-2 py-0.5 bg-amber-100 text-amber-700 text-[8px] font-black rounded uppercase">Perlu ACC</span>
                @endif
            </div>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Selesai Validasi</p>
            <div class="flex items-baseline gap-2">
                <h3 class="text-3xl font-black text-emerald-600">{{ $stats['approved'] }}</h3>
                <div class="flex-1 h-1.5 bg-slate-100 rounded-full overflow-hidden ml-4">
                    @php $progress = $stats['total'] > 0 ? ($stats['approved'] / $stats['total']) * 100 : 0; @endphp
                    <div class="h-full bg-emerald-500 rounded-full transition-all duration-1000" style="width: {{ $progress }}%"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- 3. FILTER BAR --}}
    <div class="bg-white p-4 md:p-6 rounded-[2rem] border border-slate-200 shadow-sm grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
        <div class="md:col-span-2">
            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Cari Mahasiswa</label>
            <div class="relative group">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Masukkan Nama atau NIM..." class="w-full rounded-xl border-slate-200 bg-slate-50 py-2.5 pl-10 pr-4 text-sm font-bold focus:ring-[#002855] focus:border-[#002855] transition-all">
                <svg class="w-4 h-4 absolute left-3.5 top-3 text-slate-300 group-focus-within:text-[#002855] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
        </div>
        
        <div>
            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Status KRS</label>
            <select wire:model.live="filterStatus" class="w-full rounded-xl border-slate-200 bg-white text-sm font-bold py-2.5 focus:ring-[#002855] cursor-pointer">
                <option value="all">Semua Status</option>
                <option value="AJUKAN">Menunggu Persetujuan</option>
                <option value="DISETUJUI">Sudah Disetujui</option>
                <option value="DRAFT">Masih Draft</option>
                <option value="BELUM_ISI">Belum Mengisi</option>
            </select>
        </div>

        <div>
            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Program Kelas</label>
            <select wire:model.live="filterProgramId" class="w-full rounded-xl border-slate-200 bg-white text-sm font-bold py-2.5 focus:ring-[#002855] cursor-pointer">
                <option value="">Semua Program</option>
                @foreach($programKelas as $pk)
                    <option value="{{ $pk->id }}">{{ $pk->nama_program }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- 4. LIST MAHASISWA --}}
    <div class="bg-white rounded-[2rem] border border-slate-200 shadow-sm overflow-hidden animate-in fade-in">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50/80">
                    <tr>
                        <th class="px-6 py-4 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Identitas Mahasiswa</th>
                        <th class="px-6 py-4 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Program & Prodi</th>
                        <th class="px-6 py-4 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest">Status KRS</th>
                        <th class="px-6 py-4 text-right text-[10px] font-black text-slate-400 uppercase tracking-widest w-40">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($mahasiswas as $mhs)
                    @php 
                        $krs = $mhs->krs->first(); 
                        $status = $krs ? $krs->status_krs : 'BELUM_ISI';
                        
                        $statusStyles = match($status) {
                            'AJUKAN' => 'bg-amber-50 text-amber-700 border-amber-200 animate-pulse',
                            'DISETUJUI' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                            'DRAFT' => 'bg-slate-100 text-slate-500 border-slate-200',
                            default => 'bg-rose-50 text-rose-400 border-rose-100'
                        };
                        
                        $statusLabel = match($status) {
                            'AJUKAN' => 'MENUNGGU ACC',
                            'DISETUJUI' => 'DISETUJUI',
                            'DRAFT' => 'DRAFT',
                            default => 'BELUM ISI'
                        };
                    @endphp
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center font-black text-[#002855] text-xs uppercase shadow-inner group-hover:bg-[#002855] group-hover:text-white transition-all">
                                    {{ substr($mhs->person->nama_lengkap ?? $mhs->nama_lengkap, 0, 1) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-black text-slate-800 uppercase tracking-tight truncate">{{ $mhs->person->nama_lengkap ?? $mhs->nama_lengkap }}</p>
                                    <p class="text-[10px] font-mono font-bold text-slate-400 mt-0.5 tracking-widest">{{ $mhs->nim }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <div class="text-[10px] font-black text-indigo-600 uppercase tracking-widest leading-none mb-1">
                                {{ $mhs->prodi->nama_prodi }}
                            </div>
                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter border border-slate-100 px-1.5 py-0.5 rounded">
                                {{ $mhs->programKelas->nama_program }}
                            </span>
                        </td>
                        <td class="px-6 py-5 text-center">
                            <span class="px-3 py-1 inline-flex text-[9px] font-black uppercase rounded-full border {{ $statusStyles }}">
                                {{ $statusLabel }}
                            </span>
                        </td>
                        <td class="px-6 py-5 text-right">
                            @if($krs)
                                <a href="{{ route('dosen.perwalian.detail', $krs->id) }}" 
                                   class="inline-flex items-center px-5 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-sm
                                   {{ $status == 'AJUKAN' ? 'bg-[#002855] text-white hover:bg-black' : 'bg-white border border-slate-200 text-slate-500 hover:bg-slate-50' }}"
                                   wire:navigate>
                                    {{ $status == 'AJUKAN' ? 'Review & ACC' : 'Buka Detail' }}
                                </a>
                            @else
                                <span class="text-[9px] font-black text-slate-300 uppercase tracking-widest italic">N/A</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-20 text-center">
                            <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 grayscale opacity-40">👥</div>
                            <h4 class="text-sm font-black text-slate-400 uppercase tracking-widest leading-relaxed">Data mahasiswa tidak ditemukan</h4>
                            <p class="text-[10px] text-slate-300 font-bold mt-1">Coba sesuaikan filter atau kata kunci pencarian Anda.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-6 bg-slate-50/50 border-t border-slate-100">
            {{ $mahasiswas->links() }}
        </div>
    </div>

    {{-- 5. MINIMAL FOOTER --}}
    <div class="flex flex-col items-center gap-2 opacity-30 pointer-events-none">
        <p class="text-[9px] font-black uppercase tracking-[0.4em] text-[#002855]">UNMARIS Perwalian System &bull; Integrated Solution</p>
    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(0, 40, 85, 0.05); border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(0, 40, 85, 0.1); }
    </style>
</div>