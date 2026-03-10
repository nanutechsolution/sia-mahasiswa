<div class="space-y-8 animate-in fade-in duration-500">
    <x-slot name="title">KHS Online - UNMARIS</x-slot>

    {{-- Profile Header & Dropdown --}}
    <div class="bg-white rounded-3xl shadow-xl border border-slate-200 overflow-visible relative z-20">
        <div class="p-6 lg:p-8 flex flex-col xl:flex-row xl:items-center justify-between gap-6 relative">
            
            {{-- Biodata Singkat --}}
             <div class="flex items-center space-x-5">
                <div class="w-16 h-16 bg-[#002855] text-[#fcc000] rounded-2xl flex items-center justify-center font-black text-3xl shadow-lg ring-4 ring-slate-50 flex-shrink-0">
                    {{ substr($mahasiswa->person->nama_lengkap, 0, 1) }}
                </div>
                <div>
                     <h2 class="text-2xl font-black text-[#002855] uppercase tracking-tight">{{ $mahasiswa->person->nama_lengkap }}</h2>
                    <div class="flex flex-wrap items-center gap-2 mt-1.5">
                        <span class="text-xs font-mono font-bold text-[#002855] bg-[#002855]/10 px-2 py-0.5 rounded-lg">{{ $mahasiswa->nim }}</span>
                        <span class="text-xs text-slate-500 font-bold uppercase tracking-widest border-l border-slate-300 pl-2 ml-1">{{ $mahasiswa->prodi->nama_prodi }}</span>
                    </div>
                </div>
            </div>

            {{-- Area Navigasi & Aksi --}}
            <div class="flex flex-col sm:flex-row items-end sm:items-center gap-4">
                
                {{-- Dropdown Pilih Semester --}}
                <div class="flex flex-col w-full sm:w-auto">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1 sm:text-right">Pilih Semester</label>
                    <select wire:model.live="tahunAkademikId" class="bg-slate-50 border border-slate-200 text-[#002855] text-sm font-bold rounded-xl px-4 py-2.5 focus:ring-[#002855] focus:border-[#002855] cursor-pointer min-w-[200px] shadow-sm">
                        @if(count($listSemester) === 0)
                            <option value="">-- Belum ada riwayat KRS --</option>
                        @endif
                        @foreach($listSemester as $sem)
                            <option value="{{ $sem->id }}">{{ $sem->nama_tahun }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Tampil jika EDOM dan KRS Lengkap --}}
                @if($krs && $isEdomComplete)
                 <div class="flex items-center gap-4 w-full sm:w-auto mt-2 sm:mt-0">
                     <div class="bg-slate-50 border border-slate-100 px-5 py-2.5 rounded-xl flex items-center shadow-inner justify-between flex-1 sm:flex-none">
                         <div class="text-right mr-4">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-0.5">IP Semester</p>
                         </div>
                         <div class="text-2xl font-black text-[#002855] tabular-nums">
                             {{ number_format($riwayat->ips ?? 0, 2) }}
                         </div>
                     </div>
                     {{-- Tambahkan parameter query `ta` agar controller bisa mencetak KHS semester spesifik --}}
                     <a href="{{ route('mhs.cetak.khs', ['ta' => $tahunAkademikId]) }}" target="_blank" class="px-5 py-3 bg-white border border-slate-200 text-slate-600 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-[#002855] hover:text-[#fcc000] transition-all shadow-sm whitespace-nowrap">
                        Cetak KHS
                    </a>
                 </div>
                 @endif
            </div>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-100 text-emerald-800 rounded-2xl text-sm font-bold flex items-center shadow-sm relative z-10">
            <svg class="w-5 h-5 mr-3 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- KONDISI 0: TIDAK ADA KRS SAMA SEKALI --}}
    @if(!$krs && $tahunAkademikId)
        <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-12 text-center relative z-10">
            <div class="w-20 h-20 bg-slate-50 text-slate-400 rounded-full flex items-center justify-center mx-auto text-4xl mb-4">📄</div>
            <h3 class="text-xl font-black text-slate-700 uppercase">KRS Tidak Ditemukan</h3>
            <p class="text-slate-500 text-sm mt-2">Anda belum memiliki KRS atau data belum divalidasi pada semester yang dipilih.</p>
        </div>

    {{-- KONDISI 1: JIKA EDOM BELUM LENGKAP --}}
    @elseif(!$isEdomComplete && count($unfilledCourses) > 0)
        <div class="bg-white rounded-3xl shadow-xl border-2 border-dashed border-[#fcc000]/50 p-12 text-center space-y-6 relative z-10">
            <div class="w-24 h-24 bg-amber-50 text-[#fcc000] rounded-full flex items-center justify-center mx-auto text-5xl">🔒</div>
            <div class="max-w-md mx-auto">
                <h3 class="text-2xl font-black text-[#002855] uppercase tracking-tight">KHS Masih Terkunci</h3>
                <p class="text-slate-500 text-sm mt-2 leading-relaxed">
                    Terdapat <span class="font-black text-rose-600">{{ count($unfilledCourses) }}</span> mata kuliah yang belum Anda evaluasi. Mohon selesaikan pengisian EDOM terlebih dahulu.
                </p>
            </div>
            
            <div class="max-w-lg mx-auto space-y-3">
                @foreach($unfilledCourses as $unfilled)
                <div class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl border border-slate-200">
                    <div class="text-left">
                        <p class="text-xs font-black text-slate-800 uppercase">{{ $unfilled->nama_mk }}</p>
                        <p class="text-[10px] font-mono text-slate-400 mt-0.5">{{ $unfilled->kode_mk }}</p>
                    </div>
                    <a href="{{ route('mhs.survei-edom', $unfilled->id) }}" class="px-5 py-2 bg-[#002855] text-white text-[10px] font-black uppercase rounded-xl hover:bg-black transition-all">
                        Isi Survei
                    </a>
                </div>
                @endforeach
            </div>
        </div>

    {{-- KONDISI 2: TAMPIL NILAI LENGKAP --}}
    @elseif($krs)
        <div class="bg-white shadow-sm rounded-2xl border border-slate-200 overflow-hidden relative z-10">
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                <h3 class="text-xs font-black text-[#002855] uppercase tracking-[0.2em]">Hasil Studi Semester</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-[#002855] text-white">
                        <tr>
                            <th class="px-6 py-4 text-left text-[10px] font-bold uppercase tracking-widest">Mata Kuliah</th>
                            <th class="px-4 py-4 text-center text-[10px] font-bold uppercase tracking-widest w-20">SKS</th>
                            <th class="px-4 py-4 text-center text-[10px] font-bold uppercase tracking-widest w-20">Grade</th>
                            <th class="px-4 py-4 text-center text-[10px] font-bold uppercase tracking-widest w-24">Mutu</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 bg-white">
                        @forelse($details as $row)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <span class="px-2 py-0.5 rounded-md bg-[#002855]/10 text-[#002855] text-[9px] font-black uppercase tracking-widest">{{ $row->kode_mk }}</span>
                                    <span class="text-sm font-bold text-slate-800">{{ $row->nama_mk }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-4 text-center text-sm font-black text-slate-600 bg-slate-50/30">{{ $row->sks_default }}</td>
                            <td class="px-4 py-4 text-center">
                                <span class="text-base font-black px-3 py-1 rounded-lg {{ $row->nilai_indeks >= 2 ? 'text-emerald-600 bg-emerald-50' : 'text-rose-600 bg-rose-50' }}">
                                    {{ $row->nilai_huruf ?: '-' }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-center text-sm font-black text-[#002855]">
                                {{ number_format($row->sks_default * $row->nilai_indeks, 2) }}
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="px-6 py-16 text-center text-slate-400 italic">Belum ada nilai yang dipublikasikan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>