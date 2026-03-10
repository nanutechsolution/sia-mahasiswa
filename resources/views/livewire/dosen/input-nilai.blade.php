<div class="space-y-8 animate-in fade-in duration-500 pb-12">
    
    {{-- 1. HEADER & INFO KELAS --}}
    <div class="bg-white p-8 md:p-10 rounded-[2.5rem] shadow-sm border border-slate-200 relative overflow-hidden">
        <div class="absolute top-0 right-0 p-8 opacity-[0.03] pointer-events-none">
            <svg class="w-40 h-40 text-[#002855]" fill="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        </div>

        <div class="relative flex flex-col lg:flex-row justify-between items-start lg:items-center gap-8">
            <div class="flex items-center gap-6">
                <div class="w-20 h-20 bg-[#002855] text-[#fcc000] rounded-3xl flex items-center justify-center text-4xl font-black shadow-2xl shadow-blue-900/20">
                    {{ substr($jadwal->mataKuliah->nama_mk, 0, 1) }}
                </div>
                <div class="space-y-1">
                    <h1 class="text-2xl md:text-3xl font-black text-[#002855] uppercase tracking-tight italic">{{ $jadwal->mataKuliah->nama_mk }}</h1>
                    <div class="flex flex-wrap items-center gap-3">
                        <span class="px-3 py-1 bg-slate-100 text-slate-500 rounded-lg text-[10px] font-black uppercase tracking-widest">{{ $jadwal->nama_kelas }}</span>
                        <div class="w-1.5 h-1.5 rounded-full bg-slate-200"></div>
                        <span class="text-xs font-bold text-indigo-600 uppercase tracking-widest">{{ $jadwal->tahunAkademik->nama_tahun }}</span>
                        <div class="w-1.5 h-1.5 rounded-full bg-slate-200"></div>
                        <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-tighter {{ $isLocked ? 'bg-rose-50 text-rose-600 border border-rose-100' : 'bg-emerald-50 text-emerald-600 border border-emerald-100' }}">
                            {{ $isLocked ? 'Status: Terkunci' : 'Status: Open' }}
                        </span>
                    </div>
                </div>
            </div>
            
            <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">
                @if(!$isLocked)
                <button wire:click="publishAll" wire:confirm="PERINGATAN: Mempublikasikan nilai akan mengunci inputan dan menampilkan nilai di KHS Mahasiswa secara permanen. Lanjutkan?" 
                    class="px-8 py-4 bg-[#002855] text-white rounded-2xl font-black text-xs uppercase tracking-[0.2em] hover:bg-black transition-all shadow-xl shadow-blue-900/20 flex items-center justify-center gap-3 group">
                    <svg class="w-5 h-5 group-hover:animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                    Publish Ke KHS
                </button>
                @endif
                <a href="{{ route('dosen.jadwal') }}" class="px-8 py-4 bg-slate-100 text-slate-500 rounded-2xl font-black text-xs uppercase tracking-[0.2em] hover:bg-slate-200 text-center transition-all" wire:navigate>Batal</a>
            </div>
        </div>

        {{-- Team Info --}}
        <div class="mt-8 pt-8 border-t border-slate-50 flex flex-wrap items-center gap-6">
            <div class="space-y-1">
                <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest leading-none">Ruangan</p>
                <p class="text-sm font-black text-slate-600 uppercase tracking-tighter italic">R. {{ $jadwal->ruang->kode_ruang ?? 'TBA' }}</p>
            </div>
            <div class="h-8 w-px bg-slate-100 hidden sm:block"></div>
            <div class="space-y-1">
                <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest leading-none">Tim Pengajar</p>
                <div class="flex flex-wrap gap-2">
                    @foreach($jadwal->dosens as $d)
                    <span class="text-[10px] font-bold text-slate-500 bg-slate-50 px-2 py-0.5 rounded border border-slate-100 uppercase italic">{{ $d->person->nama_lengkap }}</span>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    @if (session()->has('global_success'))
        <div class="bg-emerald-500 p-5 rounded-[1.5rem] text-white text-sm font-black uppercase tracking-widest flex items-center shadow-xl shadow-emerald-500/20 animate-in slide-in-from-top-4 duration-500">
            <svg class="w-6 h-6 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
            {{ session('global_success') }}
        </div>
    @endif

    {{-- 2. DYNAMIC GRADE TABLE --}}
    <div class="bg-white rounded-[3rem] shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50/50">
                    <tr>
                        <th rowspan="2" class="px-10 py-6 text-left text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] border-r border-slate-100">Mahasiswa</th>
                        <th colspan="{{ count($komponenBobot) }}" class="px-6 py-4 text-center text-[10px] font-black text-indigo-400 uppercase tracking-[0.3em] border-b border-slate-100">Komponen Penilaian Kurikulum</th>
                        <th rowspan="2" class="px-6 py-6 text-center text-[10px] font-black text-[#002855] uppercase tracking-[0.2em] bg-slate-50 border-x border-slate-100">Nilai Akhir</th>
                        <th rowspan="2" class="px-6 py-6 text-center text-[10px] font-black text-[#002855] uppercase tracking-[0.2em] bg-slate-50 border-r border-slate-100">Grade</th>
                        <th rowspan="2" class="px-10 py-6 text-right text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Opsi</th>
                    </tr>
                    <tr class="bg-slate-50/30">
                        @foreach($komponenBobot as $kb)
                        <th class="px-4 py-4 text-center">
                            <div class="text-[9px] font-black text-slate-500 uppercase">{{ $kb->nama_komponen }}</div>
                            <div class="text-[8px] font-bold text-indigo-300 mt-1 uppercase">{{ number_format($kb->bobot_persen, 0) }}%</div>
                        </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 bg-white">
                    @forelse($pesertaKelas as $mhs)
                    <tr class="hover:bg-slate-50/80 transition-all group">
                        <td class="px-10 py-5 border-r border-slate-50">
                            <div class="text-sm font-black text-slate-800 uppercase tracking-tight">{{ $mhs->krs->mahasiswa->person->nama_lengkap }}</div>
                            <div class="text-[10px] font-mono font-bold text-slate-400 mt-1 tracking-widest uppercase italic">{{ $mhs->krs->mahasiswa->nim }}</div>
                        </td>

                        {{-- Input Grid --}}
                        @foreach($komponenBobot as $kb)
                        <td class="px-3 py-5">
                            <input type="number" 
                                wire:model.defer="inputNilai.{{ $mhs->id }}.{{ $kb->id }}"
                                class="w-20 mx-auto block text-center rounded-2xl border-slate-200 bg-white text-xs font-black py-3 focus:ring-4 focus:ring-[#fcc000]/20 focus:border-[#fcc000] outline-none transition-all shadow-sm {{ $mhs->is_published || $isLocked ? 'bg-slate-50 opacity-50 cursor-not-allowed shadow-none' : '' }}"
                                {{ $mhs->is_published || $isLocked ? 'disabled' : '' }}
                            >
                        </td>
                        @endforeach

                        <td class="px-6 py-5 text-center bg-slate-50/30 border-x border-slate-50">
                            <span class="text-sm font-black text-slate-800 italic">{{ number_format($mhs->nilai_angka, 2) }}</span>
                        </td>
                        <td class="px-6 py-5 text-center bg-slate-50/30 border-r border-slate-50">
                            <span class="text-xl font-black {{ $mhs->nilai_indeks >= 2 ? 'text-emerald-600' : 'text-rose-600' }}">
                                {{ $mhs->nilai_huruf ?? '??' }}
                            </span>
                        </td>

                        <td class="px-10 py-5 text-right">
                            @if(!$mhs->is_published && !$isLocked)
                                <div class="flex items-center justify-end gap-4">
                                    @if(session()->has('ok-'.$mhs->id))
                                        <span class="text-[9px] font-black text-emerald-500 uppercase tracking-widest animate-pulse italic">SAVED!</span>
                                    @endif
                                    <button wire:click="saveLine('{{ $mhs->id }}')" class="p-3 bg-indigo-600 text-white rounded-2xl shadow-xl shadow-indigo-900/10 hover:scale-110 active:scale-95 transition-all">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                                    </button>
                                </div>
                            @elseif($mhs->is_published)
                                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-emerald-50 text-emerald-600 rounded-xl border border-emerald-100">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                                    <span class="text-[9px] font-black uppercase tracking-widest">PUBLISHED</span>
                                </div>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ count($komponenBobot) + 4 }}" class="py-32 text-center">
                            <div class="text-4xl mb-4 grayscale opacity-20">👥</div>
                            <p class="text-slate-300 font-black uppercase tracking-[0.3em] text-xs">Belum ada mahasiswa yang tervalidasi di kelas ini</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- FOOTER VERSION --}}
    <div class="pt-10 flex flex-col items-center gap-2 opacity-20 grayscale pointer-events-none border-t border-slate-100">
        <p class="text-[9px] font-black uppercase tracking-[0.6em] text-[#002855]">UNMARIS Enterprise Digital Environment &bull; v4.2 PRO</p>
    </div>
</div>