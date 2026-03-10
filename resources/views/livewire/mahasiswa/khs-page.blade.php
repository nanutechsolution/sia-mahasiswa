<div class="space-y-8 animate-in fade-in duration-700 pb-12">
    <x-slot name="title">KHS Online &bull; {{ $mahasiswa->nim }}</x-slot>

    {{-- 1. TOP SELECTOR & PROFILE --}}
    <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-200 overflow-visible relative z-30">
        <div class="p-8 flex flex-col xl:flex-row xl:items-center justify-between gap-8">
            <div class="flex items-center gap-6">
                <div class="w-20 h-20 bg-[#002855] text-[#fcc000] rounded-3xl flex items-center justify-center text-4xl font-black shadow-2xl shadow-blue-900/20 uppercase">
                    {{ substr($mahasiswa->person->nama_lengkap, 0, 1) }}
                </div>
                <div>
                    <h2 class="text-2xl font-black text-[#002855] uppercase tracking-tight italic">{{ $mahasiswa->person->nama_lengkap }}</h2>
                    <div class="flex items-center gap-3 mt-2">
                        <span class="text-xs font-mono font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-lg border border-slate-200">{{ $mahasiswa->nim }}</span>
                        <div class="w-1 h-1 rounded-full bg-slate-300"></div>
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ $mahasiswa->prodi->nama_prodi }}</span>
                    </div>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-4">
                <div class="space-y-1">
                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Periode Semester</label>
                    <select wire:model.live="tahunAkademikId" class="w-full sm:w-64 bg-slate-50 border-slate-200 rounded-2xl py-3 px-5 text-sm font-black text-[#002855] focus:ring-[#fcc000] shadow-sm cursor-pointer transition-all hover:bg-white">
                        @forelse($listSemester as $sem)
                        <option value="{{ $sem->id }}">{{ $sem->nama_tahun }}</option>
                        @empty
                        <option value="">Belum Ada Data</option>
                        @endforelse
                    </select>
                </div>

                @if($krs )
                <div class="pt-5">
                    <a href="{{ route('mhs.cetak.khs', ['ta' => $tahunAkademikId]) }}" target="_blank" class="flex items-center justify-center px-6 py-3.5 bg-[#002855] text-white rounded-2xl font-black text-[10px] uppercase tracking-[0.2em] hover:bg-black transition-all shadow-xl shadow-blue-900/20 group">
                        <svg class="w-4 h-4 mr-2 group-hover:animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Cetak KHS
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- 2. ACADEMIC SUMMARY --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 relative z-10">
        <div class="bg-white p-8 rounded-[2.5rem] border border-slate-200 shadow-sm flex flex-col justify-center text-center sm:text-left">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">IP Semester (IPS)</p>
            <h3 class="text-4xl font-black text-[#002855] italic tracking-tighter">{{ number_format($riwayat->ips ?? 0, 2) }}</h3>
        </div>
        <div class="bg-indigo-900 p-8 rounded-[2.5rem] shadow-xl shadow-blue-900/20 flex flex-col justify-center text-center sm:text-left">
            <p class="text-[10px] font-black text-indigo-300 uppercase tracking-widest mb-1">IP Kumulatif (IPK)</p>
            <h3 class="text-4xl font-black text-[#fcc000] italic tracking-tighter">{{ number_format($ipkKumulatif, 2) }}</h3>
        </div>
        <div class="bg-white p-8 rounded-[2.5rem] border border-slate-200 shadow-sm flex flex-col justify-center text-center sm:text-left">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Total SKS Lulus</p>
            <h3 class="text-4xl font-black text-slate-700 italic tracking-tighter">{{ $totalSksLulus }} <span class="text-xs not-italic text-slate-300 ml-1">SKS</span></h3>
        </div>
    </div>

    {{-- 3. GATEKEEPER & TABLE --}}
    @if(!$krs)
    <div class="bg-white rounded-[3rem] p-20 text-center border border-slate-200 shadow-sm">
        <div class="text-6xl mb-6 grayscale opacity-20">📂</div>
        <h3 class="text-xl font-black text-slate-400 uppercase tracking-[0.3em]">Data KRS Belum Ditemukan</h3>
    </div>
    <!-- @elseif(!$isEdomComplete)
    <div class="bg-white rounded-[3rem] shadow-2xl border-4 border-dashed border-amber-200 p-12 text-center animate-in zoom-in-95 duration-500">
        <div class="w-24 h-24 bg-amber-50 text-[#fcc000] rounded-full flex items-center justify-center mx-auto text-5xl mb-8 shadow-inner">🔒</div>
        <h3 class="text-2xl font-black text-[#002855] uppercase tracking-tight italic">Hasil Studi Terkunci</h3>
        <p class="text-slate-500 mt-4 max-w-lg mx-auto font-medium leading-relaxed">
            Anda memiliki <span class="font-black text-rose-600 underline">{{ count($unfilledCourses) }}</span> mata kuliah yang belum dievaluasi. Selesaikan pengisian kuesioner EDOM untuk melihat nilai akhir semester ini.
        </p>

        <div class="mt-10 grid grid-cols-1 md:grid-cols-2 gap-4 max-w-3xl mx-auto">
            @foreach($unfilledCourses as $unfilled)
            <div class="flex items-center justify-between p-5 bg-slate-50 rounded-3xl border border-slate-200 hover:border-[#002855] transition-all group">
                <div class="text-left">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ $unfilled->kode_mk_snapshot }}</p>
                    <h4 class="text-xs font-black text-[#002855] uppercase truncate max-w-[200px]">{{ $unfilled->nama_mk_snapshot }}</h4>
                </div>
                <a href="{{ route('mhs.survei-edom', $unfilled->id) }}" class="px-5 py-2 bg-[#002855] text-white text-[9px] font-black uppercase rounded-xl hover:scale-105 transition-all shadow-lg shadow-blue-900/10 whitespace-nowrap">
                    Isi Survei
                </a>
            </div>
            @endforeach
        </div>
    </div> -->
    @else
    {{-- KHS TABLE VIEW --}}
    <div class="bg-white rounded-[3rem] shadow-sm border border-slate-200 overflow-hidden relative">
        <div class="px-10 py-6 bg-slate-50/50 border-b border-slate-100">
            <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.4em]">Transcript Record Semester</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-[#002855]">
                    <tr>
                        <th class="px-10 py-6 text-left text-[10px] font-black text-white uppercase tracking-widest">Mata Kuliah</th>
                        <th class="px-6 py-6 text-center text-[10px] font-black text-white uppercase tracking-widest w-24">SKS</th>
                        <th class="px-6 py-6 text-center text-[10px] font-black text-white uppercase tracking-widest w-24">Grade</th>
                        <th class="px-6 py-6 text-center text-[10px] font-black text-white uppercase tracking-widest w-24">Indeks</th>
                        <th class="px-10 py-6 text-right text-[10px] font-black text-white uppercase tracking-widest w-32">Mutu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($details as $row)
                    <tr class="hover:bg-slate-50/80 transition-all group">
                        <td class="px-10 py-6">
                            <div class="flex items-center gap-4">
                                <span class="px-2 py-1 bg-indigo-50 text-indigo-600 text-[9px] font-black rounded border border-indigo-100 uppercase">{{ $row->kode_mk_snapshot }}</span>
                                <span class="text-sm font-black text-slate-700 uppercase tracking-tight group-hover:text-[#002855] transition-colors">{{ $row->nama_mk_snapshot }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-6 text-center font-black text-slate-400 italic text-sm">{{ $row->sks_snapshot }}</td>
                        <td class="px-6 py-6 text-center">
                            <span class="text-lg font-black {{ (float)$row->nilai_indeks >= 2 ? 'text-emerald-500' : 'text-rose-500' }}">
                                {{ $row->nilai_huruf ?: '-' }}
                            </span>
                        </td>
                        <td class="px-6 py-6 text-center text-xs font-bold text-slate-400">{{ number_format($row->nilai_indeks, 2) }}</td>
                        <td class="px-10 py-6 text-right font-black text-[#002855] text-sm italic bg-slate-50/30">
                            {{ number_format($row->sks_snapshot * $row->nilai_indeks, 2) }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-20 text-center">
                            <p class="text-[10px] font-black text-slate-300 uppercase tracking-widest">Belum ada nilai yang dipublikasikan oleh Dosen.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if(count($details) > 0)
                <tfoot class="bg-slate-50/50">
                    <tr class="font-black">
                        <td class="px-10 py-6 text-right text-[10px] uppercase tracking-widest text-slate-400">Total Kredit & Bobot Mutu</td>
                        <td class="px-6 py-6 text-center text-sm text-[#002855] italic">{{ $details->sum('sks_snapshot') }}</td>
                        <td colspan="2"></td>
                        <td class="px-10 py-6 text-right text-sm text-[#002855] italic">
                            {{ number_format($details->sum(fn($d) => $d->sks_snapshot * $d->nilai_indeks), 2) }}
                        </td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

    {{-- Signature Footer --}}
    <div class="flex flex-col md:flex-row justify-between items-start gap-12 px-8 pt-10 border-t border-slate-100 opacity-60">
        <div class="space-y-1">
            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Dicetak Oleh Sistem pada:</p>
            <p class="text-[10px] font-bold text-slate-600">{{ now()->isoFormat('D MMMM Y, HH:mm') }} WITA</p>
        </div>

        @if($kaProdi)
        <div class="text-right space-y-1 ml-auto">
            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-12">Ketua Program Studi,</p>
            <p class="text-sm font-black text-[#002855] uppercase underline decoration-2 decoration-[#fcc000] underline-offset-4">{{ $kaProdi->nama }}</p>
            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">{{ $kaProdi->identitas }}</p>
        </div>
        @endif
    </div>
    @endif

    <div class="pt-10 flex flex-col items-center gap-2 opacity-20 grayscale pointer-events-none border-t border-slate-50">
        <p class="text-[9px] font-black uppercase tracking-[0.6em] text-[#002855]">OFFICIAL ACADEMIC TRANSCRIPT PORTAL &bull; v4.2</p>
    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar {
            height: 4px;
            width: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(0, 40, 85, 0.1);
            border-radius: 10px;
        }
    </style>
</div>