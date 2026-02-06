<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-10 space-y-8 animate-in fade-in duration-700 pb-20">
    
    {{-- 1. HEADER RINGKASAN --}}
    <div class="bg-white rounded-[2.5rem] shadow-2xl shadow-indigo-900/5 border border-slate-100 p-6 sm:p-10 flex flex-col lg:flex-row justify-between items-center gap-8 relative overflow-hidden">
        <div class="flex items-center gap-6 w-full lg:w-auto relative z-10">
            <div class="w-16 h-16 sm:w-20 sm:h-20 bg-[#002855] text-[#fcc000] rounded-3xl flex items-center justify-center font-black text-2xl sm:text-3xl shadow-xl ring-4 ring-indigo-50 shrink-0 uppercase">
                {{ substr($mahasiswa->person->nama_lengkap ?? $mahasiswa->nama_lengkap, 0, 1) }}
            </div>
            <div class="min-w-0">
                <h2 class="text-xl sm:text-2xl font-black text-[#002855] uppercase truncate">{{ $mahasiswa->person->nama_lengkap }}</h2>
                <div class="flex items-center gap-2 mt-1">
                    <span class="px-2 py-0.5 bg-indigo-50 text-[#002855] text-[10px] font-mono font-black rounded-lg border border-indigo-100 uppercase">{{ $mahasiswa->nim }}</span>
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-l border-slate-200 pl-2">Smt {{ $semesterBerjalan }}</span>
                </div>
            </div>
        </div>

        {{-- SKS Meter --}}
        <div class="w-full lg:w-auto flex items-center justify-between lg:justify-end gap-8 bg-slate-50 p-6 rounded-[2rem] border border-slate-100 shadow-inner">
            <div class="text-right border-r border-slate-200 pr-8">
                <span class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1 leading-none">Sistem</span>
                <span class="text-[11px] font-black {{ $isPaket ? 'text-indigo-600' : 'text-emerald-600' }} uppercase">
                    {{ $isPaket ? 'Paket Kurikulum' : 'SKS Mandiri' }}
                </span>
            </div>
            <div class="flex items-baseline">
                <span class="text-4xl sm:text-5xl font-black text-[#002855] tabular-nums">{{ $totalSks }}</span>
                @if(!$isPaket) <span class="text-xl font-bold text-slate-300 ml-1">/ {{ $maxSks }}</span> @endif
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Kredit</span>
            </div>
        </div>
    </div>

    {{-- 2. PANEL BLOKIR (ADMINISTRATIVE & FINANCE) --}}
    @if($blockKrs)
    <div class="grid grid-cols-1 gap-6 animate-in slide-in-from-top-4">
        <div class="bg-white rounded-[2.5rem] border-2 border-rose-100 shadow-2xl shadow-rose-900/10 p-8 sm:p-12 text-center space-y-8 relative overflow-hidden">
            <div class="absolute -right-10 -top-10 w-40 h-40 bg-rose-50 rounded-full blur-3xl"></div>
            
            <div class="w-24 h-24 bg-rose-50 text-rose-600 rounded-[2rem] flex items-center justify-center mx-auto text-5xl shadow-inner border border-rose-100">
                @if($reasonType == 'NIM') 🆔 @elseif($reasonType == 'FINANCE') 💳 @else ⚠️ @endif
            </div>

            <div class="max-w-2xl mx-auto space-y-4">
                <h3 class="text-2xl font-black text-rose-900 uppercase tracking-tight">Pengisian KRS Terkunci</h3>
                <p class="text-slate-600 font-medium leading-relaxed italic">{{ $pesanBlock }}</p>
            </div>

            {{-- Visualisasi Progress Bayar (Jika Masalah Keuangan) --}}
            @if($reasonType == 'FINANCE')
            <div class="max-w-md mx-auto space-y-3 bg-slate-50 p-6 rounded-3xl border border-slate-200">
                <div class="flex justify-between text-[10px] font-black uppercase tracking-widest">
                    <span class="text-slate-400">Progres Pembayaran</span>
                    <span class="text-[#002855]">{{ $paidPercentage }}% / {{ $minPercentage }}%</span>
                </div>
                <div class="w-full bg-slate-200 h-3 rounded-full overflow-hidden shadow-inner">
                    <div class="bg-indigo-600 h-full transition-all duration-1000" style="width: {{ min($paidPercentage, 100) }}%"></div>
                </div>
                <p class="text-[10px] text-slate-400 font-bold uppercase pt-2">*Mohon lunasi tagihan atau ajukan dispensasi ke Keuangan.</p>
            </div>
            @endif

            <div class="pt-4">
                <a href="{{ route('dashboard') }}" class="px-10 py-4 bg-slate-800 text-white rounded-2xl font-black text-xs uppercase tracking-[0.2em] shadow-xl hover:bg-black transition-all">Kembali Ke Beranda</a>
            </div>
        </div>
    </div>
    @else

    {{-- 3. WORKSPACE KRS (Hanya Tampil Jika Tidak Diblokir) --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        {{-- DAFTAR PENAWARAN --}}
        <div class="lg:col-span-8 space-y-6">
            <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-10 py-6 border-b border-slate-50 bg-slate-50/50 flex justify-between items-center">
                    <h3 class="text-xs font-black text-[#002855] uppercase tracking-widest">Penawaran Mata Kuliah</h3>
                </div>

                <div class="divide-y divide-slate-50 {{ $statusKrs != 'DRAFT' ? 'opacity-40 pointer-events-none' : '' }}">
                    @forelse($jadwalTersedia as $j)
                        @php
                            $semData = DB::table('kurikulum_mata_kuliah')
                                ->join('master_kurikulums', 'kurikulum_mata_kuliah.kurikulum_id', '=', 'master_kurikulums.id')
                                ->where('kurikulum_mata_kuliah.mata_kuliah_id', $j->mata_kuliah_id)
                                ->where('master_kurikulums.prodi_id', $this->mahasiswa->prodi_id)
                                ->where('master_kurikulums.is_active', true)
                                ->select('kurikulum_mata_kuliah.semester_paket')
                                ->first();
                        @endphp
                        <div class="px-10 py-8 flex flex-col sm:flex-row sm:items-center justify-between gap-6 group hover:bg-indigo-50/30 transition-all">
                            <div class="flex-1 space-y-4">
                                <div class="flex items-center gap-3">
                                    <span class="px-2 py-0.5 bg-[#002855] text-[#fcc000] text-[9px] font-black uppercase rounded shadow-sm">Smt {{ $semData->semester_paket ?? '?' }}</span>
                                    <h4 class="text-base font-black text-slate-800 leading-tight uppercase group-hover:text-indigo-700">{{ $j->mataKuliah->nama_mk }}</h4>
                                </div>
                                <div class="flex flex-wrap items-center gap-x-6 gap-y-2 text-[11px] font-bold text-slate-400">
                                    <span class="flex items-center uppercase"><svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>{{ $j->hari }}, {{ substr($j->jam_mulai,0,5) }}</span>
                                    <span class="flex items-center uppercase"><svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>R.{{ $j->ruang }} ({{ $j->nama_kelas }})</span>
                                    <span class="text-indigo-600 uppercase tracking-widest">{{ $j->mataKuliah->sks_default }} SKS</span>
                                </div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase truncate"><span class="w-5 h-5 rounded-full bg-slate-100 flex items-center justify-center mr-2 text-[8px] inline-flex">👤</span>{{ $j->dosen->person->nama_lengkap ?? 'Dosen Pengampu' }}</p>
                            </div>
                            <button wire:click="ambilMatkul('{{ $j->id }}')" class="w-full sm:w-auto px-10 py-3.5 bg-[#002855] text-white rounded-2xl font-black text-[11px] uppercase tracking-widest shadow-xl hover:scale-105 active:scale-95 transition-all">Ambil</button>
                        </div>
                    @empty
                        <div class="py-24 text-center text-slate-400 italic text-sm">Penawaran kelas belum tersedia.</div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- SIDEBAR DRAF --}}
        <div class="lg:col-span-4 space-y-6">
            <div class="bg-[#002855] rounded-[2.5rem] shadow-2xl shadow-indigo-900/20 overflow-hidden lg:sticky lg:top-6">
                <div class="px-8 py-8 border-b border-white/10 bg-white/5 flex justify-between items-center text-white">
                    <div>
                        <h3 class="text-xs font-black text-[#fcc000] uppercase tracking-widest">Rencana Studi</h3>
                        <p class="text-[10px] opacity-40 font-bold uppercase mt-1 leading-none">Draf Dipilih</p>
                    </div>
                    <span class="text-xs font-black bg-[#fcc000] text-[#002855] px-3 py-1 rounded-xl shadow-lg">{{ count($krsDiambil) }} MK</span>
                </div>

                <div class="max-h-[50vh] overflow-y-auto custom-scrollbar divide-y divide-white/5 bg-black/10">
                    @forelse($krsDiambil as $detail)
                        <div class="px-8 py-6 flex justify-between items-start group hover:bg-white/5 transition-all text-white">
                            <div class="flex-1 min-w-0 pr-4 space-y-2">
                                <p class="text-[11px] font-black uppercase tracking-tight leading-snug">{{ $detail->nama_mk_snapshot }}</p>
                                <div class="flex items-center gap-2">
                                    <span class="text-[8px] font-black text-[#fcc000] border border-[#fcc000]/30 px-1.5 py-0.5 rounded uppercase">{{ $detail->kode_mk_snapshot }}</span>
                                    <span class="text-[9px] font-bold opacity-40 uppercase">{{ $detail->sks_snapshot }} SKS</span>
                                </div>
                            </div>
                            @if($statusKrs == 'DRAFT' && !$isPaket)
                                <button wire:click="hapusMatkul('{{ $detail->id }}')" class="p-2 text-white/20 hover:text-rose-400 transition-all focus:outline-none shrink-0"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg></button>
                            @endif
                        </div>
                    @empty
                        <div class="px-10 py-20 text-center text-white/20 uppercase tracking-widest font-black text-[10px]">Keranjang Kosong</div>
                    @endforelse
                </div>

                <div class="p-10 bg-black/20 border-t border-white/10 space-y-8">
                    <div class="flex justify-between items-end text-white">
                        <div>
                            <span class="text-[9px] font-black opacity-30 uppercase tracking-widest block mb-1">Total Kredit</span>
                            <span class="text-3xl font-black text-[#fcc000] tabular-nums leading-none">{{ $totalSks }}</span>
                        </div>
                        <span class="px-3 py-1 bg-white/10 rounded-lg text-[10px] font-black uppercase border border-white/10 tracking-widest">{{ $statusKrs }}</span>
                    </div>

                    @if($statusKrs == 'DRAFT')
                        <button wire:click="ajukanKrs" wire:confirm="Kirim rencana studi ini ke Dosen Wali?" @if($totalSks == 0) disabled @endif
                            class="w-full py-5 bg-[#fcc000] text-[#002855] rounded-3xl font-black text-[11px] uppercase tracking-[0.4em] shadow-2xl shadow-orange-500/20 hover:scale-[1.02] active:scale-95 transition-all disabled:opacity-30 disabled:grayscale">
                            Ajukan Rencana Studi
                        </button>
                    @endif
                </div>
            </div>

            <div class="bg-indigo-50 p-8 rounded-[2.5rem] border border-indigo-100 relative overflow-hidden hidden lg:block">
                <div class="absolute -right-6 -bottom-6 opacity-10"><svg class="w-32 h-32 text-[#002855]" fill="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg></div>
                <h4 class="text-[11px] font-black text-indigo-900 uppercase tracking-widest mb-3 relative z-10 flex items-center gap-2"><span class="w-1.5 h-4 bg-[#fcc000] rounded-full"></span> Keamanan Data</h4>
                <p class="text-[10px] text-indigo-700/70 leading-relaxed font-bold relative z-10">Data pengambilan matakuliah dilindungi oleh mekanisme <strong>Snapshot Transaction</strong> untuk menjamin validitas transkrip nilai seumur hidup.</p>
            </div>
        </div>
    </div>
    @endif

    <style>.custom-scrollbar::-webkit-scrollbar{width:4px}.custom-scrollbar::-webkit-scrollbar-track{background:transparent}.custom-scrollbar::-webkit-scrollbar-thumb{background:rgba(255,255,255,0.1);border-radius:10px}</style>
</div>