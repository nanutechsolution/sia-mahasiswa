<div class="space-y-8 animate-in fade-in duration-500">
    
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-[#002855]">Perwalian Akademik</h1>
            <div class="flex items-center gap-2 mt-1">
                <span class="text-slate-500 text-sm">Semester:</span>
                <span class="bg-[#002855]/10 text-[#002855] px-2 py-0.5 rounded text-xs font-bold uppercase">
                    {{ $taAktifNama }}
                </span>
            </div>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Bimbingan</p>
            <p class="text-3xl font-black text-[#002855] mt-1">{{ $totalMhs }}</p>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 relative overflow-hidden">
            @if($menungguAcc > 0)
                <span class="absolute top-4 right-4 flex h-3 w-3">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#fcc000] opacity-75"></span>
                  <span class="relative inline-flex rounded-full h-3 w-3 bg-[#fcc000]"></span>
                </span>
            @endif
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Menunggu Persetujuan</p>
            <p class="text-3xl font-black text-[#fcc000] mt-1">{{ $menungguAcc }}</p>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Sudah Disetujui</p>
            <p class="text-3xl font-black text-emerald-600 mt-1">{{ $sudahAcc }}</p>
        </div>
    </div>

    {{-- Tabel Mahasiswa --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
            <h3 class="text-sm font-bold text-[#002855]">Daftar Mahasiswa Bimbingan</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-[#002855] text-white">
                    <tr>
                        <th class="px-6 py-4 text-left text-[10px] font-bold uppercase tracking-widest">Mahasiswa</th>
                        <th class="px-6 py-4 text-left text-[10px] font-bold uppercase tracking-widest">Program</th>
                        <th class="px-6 py-4 text-center text-[10px] font-bold uppercase tracking-widest">Status KRS</th>
                        <th class="px-6 py-4 text-right text-[10px] font-bold uppercase tracking-widest">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($mahasiswas as $mhs)
                    @php 
                        $krs = $mhs->krs->first(); 
                        $status = $krs ? $krs->status_krs : 'BELUM ISI';
                        
                        $statusColor = match($status) {
                            'AJUKAN' => 'bg-[#fcc000]/20 text-[#002855] border-[#fcc000]/30',
                            'DISETUJUI' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                            'DRAFT' => 'bg-slate-100 text-slate-500 border-slate-200',
                            default => 'bg-slate-50 text-slate-400 border-slate-100'
                        };
                    @endphp
                    <tr class="hover:bg-slate-50/80 transition-colors group">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-black text-slate-800">{{ $mhs->person->nama_lengkap ?? $mhs->nama_lengkap }}</div>
                            <div class="text-[10px] font-mono text-slate-400 mt-0.5">{{ $mhs->nim }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-xs text-slate-600 font-bold">
                            {{ $mhs->programKelas->nama_program }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span class="px-3 py-1 inline-flex text-[10px] font-black uppercase rounded-full border {{ $statusColor }}">
                                {{ $status == 'AJUKAN' ? 'MENUNGGU ACC' : $status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            @if($krs)
                                <a href="{{ route('dosen.perwalian.detail', $krs->id) }}" 
                                   class="inline-flex items-center px-4 py-2 bg-white border {{ $status == 'AJUKAN' ? 'border-[#fcc000] text-[#002855]' : 'border-slate-200 text-slate-500' }} rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-50 transition-all shadow-sm">
                                    {{ $status == 'AJUKAN' ? 'Review & ACC' : 'Lihat Detail' }}
                                </a>
                            @else
                                <span class="text-[10px] text-slate-300 italic font-bold">Belum Ada KRS</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-slate-400 italic text-sm">
                            Tidak ada mahasiswa bimbingan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>