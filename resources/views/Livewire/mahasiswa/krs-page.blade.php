<div class="space-y-8 animate-in fade-in duration-500">
    {{-- Header Profile --}}
    <div class="bg-white rounded-3xl shadow-xl border border-slate-200 overflow-hidden relative">
        <div class="absolute top-0 right-0 p-4 opacity-10 pointer-events-none">
            <svg class="w-64 h-64 text-[#002855]" fill="currentColor" viewBox="0 0 24 24"><path d="M12 14l9-5-9-5-9 5 9 5z"/><path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
        </div>
        <div class="p-6 lg:p-8 flex flex-col md:flex-row md:items-center justify-between gap-6 relative z-10">
            <div class="flex items-center space-x-5">
                <div class="w-16 h-16 bg-[#002855] text-[#fcc000] rounded-2xl flex items-center justify-center font-black text-3xl shadow-lg ring-4 ring-slate-50">
                    {{ substr($mahasiswa->person->nama_lengkap ?? $mahasiswa->nama_lengkap, 0, 1) }}
                </div>
                <div>
                    <h2 class="text-2xl font-black text-[#002855] leading-tight uppercase tracking-tight">{{ $mahasiswa->person->nama_lengkap ?? $mahasiswa->nama_lengkap }}</h2>
                    <div class="flex flex-wrap items-center gap-2 mt-1.5">
                        <span class="text-xs font-mono font-bold text-[#002855] bg-[#002855]/10 px-2 py-0.5 rounded-lg">{{ $mahasiswa->nim }}</span>
                        <span class="text-xs text-slate-500 font-bold uppercase tracking-widest border-l border-slate-300 pl-2 ml-1">{{ $mahasiswa->prodi->nama_prodi }}</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wide
                            {{ $statusKrs == 'DRAFT' ? 'bg-slate-100 text-slate-600' : 
                              ($statusKrs == 'AJUKAN' ? 'bg-[#fcc000]/20 text-amber-700' : 
                              ($statusKrs == 'DISETUJUI' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800')) }}">
                            {{ $statusKrs }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-4">
                <div class="bg-slate-50 border border-slate-100 px-5 py-2.5 rounded-2xl flex items-center shadow-inner min-w-[160px] justify-between">
                    <div class="text-right mr-4">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Jatah SKS</p>
                        <p class="text-[10px] font-bold text-[#002855]">{{ $ipsLalu > 0 ? "IPS Lalu: ".number_format($ipsLalu, 2) : "Mahasiswa Baru" }}</p>
                    </div>
                    <div class="flex items-baseline">
                        <span class="text-3xl font-black text-[#002855] tabular-nums">{{ $totalSks }}</span>
                        <span class="text-lg font-bold text-slate-300 ml-1">/ {{ $maxSks }}</span>
                    </div>
                </div>
                <a href="{{ route('mhs.cetak.krs') }}" target="_blank" class="inline-flex items-center px-5 py-3 bg-white border border-slate-200 text-slate-600 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-[#002855] hover:text-white transition-all shadow-sm group">
                    <svg class="w-5 h-5 mr-2 group-hover:text-[#fcc000] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                    PDF
                </a>
            </div>
        </div>
    </div>

    {{-- LOGS (Hasil Paket Otomatis) --}}
    @if(!empty($logs))
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm animate-in slide-in-from-top-4">
        <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex justify-between items-center">
            <h3 class="text-xs font-black text-slate-500 uppercase tracking-widest">Laporan Pengambilan Paket</h3>
            <button wire:click="$set('logs', [])" class="text-xs text-slate-400 hover:text-slate-600 font-bold">&times; Tutup</button>
        </div>
        <div class="p-4 space-y-2 max-h-60 overflow-y-auto">
            @foreach($logs as $log)
            <div class="flex items-start gap-3 p-3 rounded-xl border {{ $log['type'] == 'success' ? 'bg-emerald-50 border-emerald-100 text-emerald-700' : ($log['type'] == 'warning' ? 'bg-amber-50 border-amber-100 text-amber-700' : 'bg-rose-50 border-rose-100 text-rose-700') }}">
                <div class="flex-shrink-0 mt-0.5">
                    @if($log['type'] == 'success') <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    @else <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> @endif
                </div>
                <div class="text-xs font-bold leading-relaxed">{!! $log['msg'] !!}</div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Alert Messages & Blocking --}}
    @if($blockKrs)
    <div class="rounded-3xl bg-rose-50 p-6 border border-rose-100 shadow-sm flex items-start gap-4">
        <span class="text-2xl">⚠️</span>
        <div>
            <h3 class="text-sm font-black text-rose-800 uppercase tracking-widest">Akses KRS Terkunci</h3>
            <p class="text-sm text-rose-700 font-medium leading-relaxed mt-1">{{ $pesanBlock }}</p>
        </div>
    </div>
    @endif
    
    @if (session()->has('success'))
        <div class="rounded-2xl bg-emerald-50 p-4 border border-emerald-100 text-emerald-800 text-sm font-bold flex items-center shadow-sm">
            <svg class="w-5 h-5 mr-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg> {{ session('success') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="rounded-2xl bg-rose-50 p-4 border border-rose-100 text-rose-800 text-sm font-bold flex items-center shadow-sm">
            <svg class="w-5 h-5 mr-3 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg> {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        {{-- Area Mata Kuliah --}}
        <div class="lg:col-span-8 space-y-6">
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-8 py-5 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
                    <div>
                        <h3 class="text-xs font-black text-[#002855] uppercase tracking-[0.2em]">
                            {{ $isPaket ? 'Mata Kuliah Paket (Otomatis)' : 'Pilih Mata Kuliah' }}
                        </h3>
                        <p class="text-[10px] text-slate-400 font-bold uppercase mt-0.5">Program {{ $mahasiswa->programKelas->nama_program }}</p>
                    </div>
                    @if($isPaket)
                        <span class="bg-[#fcc000] text-[#002855] px-3 py-1 rounded-full text-[10px] font-black uppercase shadow-sm">Sistem Paket</span>
                    @else
                        <span class="bg-slate-200 text-slate-600 px-3 py-1 rounded-full text-[10px] font-black uppercase shadow-sm">SKS Mandiri</span>
                    @endif
                </div>

                <div class="divide-y divide-slate-100 {{ $statusKrs != 'DRAFT' ? 'opacity-50 pointer-events-none grayscale' : '' }} transition-all duration-300">
                    {{-- SKS MURNI --}}
                    @if(!$isPaket)
                        @forelse($this->jadwalTersedia as $jadwal)
                        @php $smtPaket = $semesterMap[$jadwal->mata_kuliah_id] ?? null; @endphp
                        <div class="px-8 py-6 flex items-center justify-between group hover:bg-slate-50 transition-colors">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3 mb-1.5">
                                    <span class="text-sm font-black text-slate-800 leading-tight group-hover:text-[#002855] transition-colors">{{ $jadwal->mataKuliah->nama_mk }}</span>
                                    <span class="px-2 py-0.5 rounded-md bg-[#002855]/10 text-[#002855] text-[9px] font-black uppercase tracking-widest">{{ $jadwal->mataKuliah->kode_mk }}</span>
                                    @if($smtPaket)
                                        <span class="px-2 py-0.5 rounded-md bg-[#fcc000]/20 text-[#002855] text-[9px] font-black uppercase tracking-widest">Smt {{ $smtPaket }}</span>
                                    @endif
                                </div>
                                <div class="flex flex-wrap items-center gap-y-2 gap-x-4">
                                    <div class="flex items-center text-[11px] font-bold text-slate-400">
                                        {{ $jadwal->hari }}, {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} • Kls {{ $jadwal->nama_kelas }}
                                    </div>
                                    <div class="flex items-center text-[11px] font-bold text-slate-400 uppercase tracking-tighter">
                                        {{ $jadwal->dosen->person->nama_lengkap ?? 'Dosen Belum Diset' }}
                                    </div>
                                    <div class="text-[11px] font-black text-[#002855] uppercase tracking-widest">{{ $jadwal->mataKuliah->sks_default }} SKS</div>
                                </div>
                            </div>
                            <div class="ml-6">
                                @if(!$blockKrs)
                                <button wire:click="ambilMatkul('{{ $jadwal->id }}')" wire:loading.attr="disabled" class="px-5 py-2.5 bg-[#002855] text-white rounded-xl text-[10px] font-black uppercase tracking-[0.15em] shadow-lg shadow-indigo-900/20 hover:scale-105 active:scale-95 hover:bg-[#001a38] transition-all disabled:opacity-50">Ambil</button>
                                @endif
                            </div>
                        </div>
                        @empty
                        <div class="px-8 py-20 text-center"><p class="text-slate-400 text-sm font-bold italic">Tidak ada jadwal tersedia.</p></div>
                        @endforelse
                    {{-- SISTEM PAKET --}}
                    @else
                        <div class="px-8 py-12 text-center bg-slate-50/50">
                            <p class="text-slate-500 font-medium text-sm">Anda menggunakan Sistem Paket. Mata kuliah dipilihkan secara otomatis.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Sidebar (Draft KRS) --}}
        <div class="lg:col-span-4 space-y-6">
            <div class="bg-[#002855] rounded-3xl shadow-2xl border border-white/10 overflow-hidden sticky top-6">
                <div class="px-8 py-6 border-b border-white/10 bg-white/5 flex justify-between items-center">
                    <div>
                        <h3 class="text-xs font-black text-[#fcc000] uppercase tracking-[0.2em]">Draft KRS</h3>
                        <p class="text-[10px] text-white/50 font-bold uppercase mt-1">Semester {{ \App\Helpers\SistemHelper::getTahunAktif()->nama_tahun ?? '-' }}</p>
                    </div>
                    <span class="text-xs font-bold text-[#002855] bg-[#fcc000] px-2.5 py-1 rounded-lg shadow-lg shadow-[#fcc000]/20">{{ $this->krsDiambil->count() }} MK</span>
                </div>

                <div class="max-h-[50vh] overflow-y-auto custom-scrollbar divide-y divide-white/10">
                    @forelse($this->krsDiambil as $detail)
                    @php $smtPaket = $semesterMap[$detail->jadwalKuliah->mata_kuliah_id] ?? null; @endphp
                    <div class="px-8 py-5 flex justify-between items-start group hover:bg-white/5 transition-colors">
                        <div class="flex-1 mr-4">
                            <p class="text-sm font-bold text-white leading-tight group-hover:text-[#fcc000] transition-colors">{{ $detail->jadwalKuliah->mataKuliah->nama_mk }}</p>
                            <div class="flex items-center gap-2 mt-1">
                                <p class="text-[10px] font-black text-white/40 uppercase tracking-widest">{{ $detail->jadwalKuliah->mataKuliah->sks_default }} SKS • {{ $detail->jadwalKuliah->nama_kelas }}</p>
                                @if($smtPaket)
                                    <span class="text-[9px] font-bold text-[#fcc000] bg-[#fcc000]/10 px-1.5 py-0.5 rounded border border-[#fcc000]/20">Smt {{ $smtPaket }}</span>
                                @endif
                            </div>
                        </div>
                        @if($statusKrs == 'DRAFT' && !$isPaket) 
                        <button wire:click="hapusMatkul({{ $detail->id }})" class="p-2 text-white/20 hover:text-rose-400 hover:bg-rose-400/10 rounded-lg transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                        @endif
                    </div>
                    @empty
                    <div class="px-8 py-12 text-center">
                        <p class="text-[11px] font-bold text-white/20 uppercase tracking-widest">Draft Kosong</p>
                    </div>
                    @endforelse
                </div>

                <div class="p-8 bg-black/20 border-t border-white/10">
                    <div class="flex justify-between items-end mb-6">
                        <span class="text-[10px] font-black text-white/50 uppercase tracking-widest">Akumulasi Kredit</span>
                        <span class="text-2xl font-black text-[#fcc000]">{{ $totalSks }} <span class="text-xs font-bold text-white/50 ml-1">SKS</span></span>
                    </div>

                    @if($statusKrs == 'DRAFT')
                        <button wire:click="ajukanKrs" wire:loading.attr="disabled"
                            wire:confirm="Ajukan KRS semester ini? Setelah diajukan, Anda tidak dapat mengubah data."
                            @if($totalSks==0 || $blockKrs) disabled @endif
                            class="w-full py-4 bg-[#fcc000] text-[#002855] rounded-2xl font-black text-xs uppercase tracking-[0.2em] shadow-xl shadow-[#fcc000]/20 hover:bg-[#e6b000] hover:scale-[1.02] active:scale-95 transition-all disabled:opacity-50 disabled:grayscale disabled:cursor-not-allowed">
                            Ajukan Rencana Studi
                        </button>
                    @elseif($statusKrs == 'AJUKAN')
                        <div class="w-full py-4 bg-[#fcc000]/10 text-[#fcc000] border border-[#fcc000]/30 rounded-2xl font-bold text-xs uppercase text-center tracking-widest">
                            Menunggu Persetujuan PA
                        </div>
                    @elseif($statusKrs == 'DISETUJUI')
                        <div class="w-full py-4 bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 rounded-2xl font-bold text-xs uppercase text-center tracking-widest">
                            KRS Disetujui (Final)
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>