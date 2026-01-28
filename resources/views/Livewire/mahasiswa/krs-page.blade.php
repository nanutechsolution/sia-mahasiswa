<div>
    {{-- SEO & Header Layout --}}
    <x-slot name="title">KRS Online - UNMARIS</x-slot>
    <x-slot name="header">Pengisian KRS Online</x-slot>

    <div class="space-y-8">
        {{-- Profile Header Card --}}
        <div class="bg-white rounded-3xl shadow-xl border border-slate-200 overflow-hidden animate-in fade-in duration-500">
            <div class="p-6 lg:p-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="flex items-center space-x-5">
                    <div class="w-16 h-16 bg-indigo-600 text-white rounded-2xl flex items-center justify-center font-black text-2xl shadow-lg ring-4 ring-slate-50">
                        {{ substr($mahasiswa->nama_lengkap, 0, 1) }}
                    </div>
                    <div>
                        <h2 class="text-xl font-black text-slate-900 leading-tight uppercase tracking-tight">{{ $mahasiswa->nama_lengkap }}</h2>
                        <div class="flex flex-wrap items-center gap-2 mt-1.5">
                            <span class="text-xs font-mono font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-lg border border-indigo-100">{{ $mahasiswa->nim }}</span>
                            <span class="text-xs text-slate-400 font-bold uppercase tracking-widest">• {{ $mahasiswa->prodi->nama_prodi }}</span>
                            
                            {{-- Status Badge --}}
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wide
                                {{ $statusKrs == 'DRAFT' ? 'bg-gray-100 text-gray-600' : 
                                  ($statusKrs == 'AJUKAN' ? 'bg-yellow-100 text-yellow-700' : 
                                  ($statusKrs == 'DISETUJUI' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700')) }}">
                                Status: {{ $statusKrs }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-4">
                    {{-- SKS Counter Badge --}}
                    <div class="bg-slate-50 border border-slate-100 px-6 py-3 rounded-2xl flex items-center shadow-inner min-w-[180px] justify-between">
                        <div class="text-right mr-4">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Jatah SKS</p>
                            @if($ipsLalu > 0)
                                <p class="text-[10px] font-bold text-indigo-500">IPS Lalu: {{ number_format($ipsLalu, 2) }}</p>
                            @else
                                <p class="text-[10px] font-bold text-slate-400">Semester 1</p>
                            @endif
                        </div>
                        <div class="flex items-baseline">
                            <span class="text-3xl font-black text-indigo-600 tabular-nums">{{ $totalSks }}</span>
                            <span class="text-lg font-bold text-slate-300 ml-1">/ {{ $maxSks }}</span>
                        </div>
                    </div>

                    <a href="{{ route('mhs.cetak.krs') }}" target="_blank"
                        class="inline-flex items-center px-6 py-4 bg-white border border-slate-200 text-slate-600 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-slate-50 hover:text-indigo-600 transition-all shadow-sm">
                        <svg class="w-5 h-5 mr-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        PDF
                    </a>
                </div>
            </div>
        </div>

        {{-- Block Access Alert --}}
        @if($blockKrs)
        <div class="rounded-3xl bg-rose-50 p-6 border border-rose-100 animate-pulse-slow">
            <div class="flex items-start">
                <div class="flex-shrink-0 p-2 bg-rose-100 rounded-xl text-rose-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-black text-rose-800 uppercase tracking-widest">Akses KRS Terkunci</h3>
                    <div class="mt-1 text-[13px] text-rose-700 font-medium leading-relaxed">
                        {{ $pesanBlock }}
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Flash Messages --}}
        @if (session()->has('success'))
            <div class="rounded-2xl bg-green-50 p-4 border border-green-100 text-green-800 text-sm font-bold flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                {{ session('success') }}
            </div>
        @endif
        @if (session()->has('error'))
            <div class="rounded-2xl bg-rose-50 p-4 border border-rose-100 text-rose-800 text-sm font-bold flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            {{-- Available Schedules --}}
            <div class="lg:col-span-8 space-y-6">
                <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="px-8 py-5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                        <div>
                            <h3 class="text-xs font-black text-indigo-600 uppercase tracking-[0.2em]">Penawaran Mata Kuliah</h3>
                            <p class="text-[10px] text-slate-400 font-bold uppercase mt-0.5">Sesuai Kurikulum Program {{ $mahasiswa->programKelas->nama_program }}</p>
                        </div>
                    </div>

                    <div class="divide-y divide-slate-100 {{ $statusKrs != 'DRAFT' ? 'opacity-50 pointer-events-none' : '' }}">
                        @forelse($this->jadwalTersedia as $jadwal)
                        @php 
                            // Ambil data semester dari map yang dikirim backend
                            $smtPaket = $semesterMap[$jadwal->mata_kuliah_id] ?? null; 
                        @endphp
                        <div class="px-8 py-6 flex items-center justify-between group hover:bg-slate-50/50 transition-colors">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3 mb-1.5">
                                    {{-- Nama MK --}}
                                    <span class="text-sm font-black text-slate-800 leading-tight group-hover:text-indigo-600 transition-colors">{{ $jadwal->mataKuliah->nama_mk }}</span>
                                    
                                    {{-- Kode MK Badge --}}
                                    <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 text-[9px] font-black uppercase tracking-widest">{{ $jadwal->mataKuliah->kode_mk }}</span>

                                    {{-- BADGE SEMESTER (BARU) --}}
                                    @if($smtPaket)
                                        <span class="px-2 py-0.5 rounded-md bg-sky-100 text-sky-700 text-[9px] font-black uppercase tracking-widest border border-sky-200">
                                            SMT {{ $smtPaket }}
                                        </span>
                                    @endif
                                </div>
                                
                                <div class="flex flex-wrap items-center gap-y-2 gap-x-4">
                                    {{-- Waktu --}}
                                    <div class="flex items-center text-[11px] font-bold text-slate-400">
                                        <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ $jadwal->hari }}, {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} • Kls {{ $jadwal->nama_kelas }}
                                    </div>
                                    {{-- Dosen --}}
                                    <div class="flex items-center text-[11px] font-bold text-slate-400 uppercase tracking-tighter">
                                        <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        {{ optional($jadwal->dosen)->nama_lengkap_gelar ?? 'Dosen Belum Diset' }}
                                    </div>
                                    {{-- SKS --}}
                                    <div class="text-[11px] font-black text-indigo-600 uppercase tracking-widest">{{ $jadwal->mataKuliah->sks_default }} SKS</div>
                                </div>
                            </div>

                            {{-- Tombol Ambil --}}
                            <div class="ml-6">
                                @if(!$blockKrs)
                                <button wire:click="ambilMatkul('{{ $jadwal->id }}')"
                                    wire:loading.attr="disabled"
                                    class="px-5 py-2.5 bg-indigo-600 text-white rounded-xl text-[10px] font-black uppercase tracking-[0.15em] shadow-lg shadow-indigo-100 hover:scale-105 active:scale-95 transition-all disabled:opacity-50">
                                    Ambil
                                </button>
                                @else
                                <div class="px-4 py-2 bg-slate-100 text-slate-400 rounded-xl text-[10px] font-black uppercase tracking-widest border border-slate-200">
                                    Locked
                                </div>
                                @endif
                            </div>
                        </div>
                        @empty
                        <div class="px-8 py-20 text-center">
                            <p class="text-slate-400 text-sm font-bold italic">Tidak ada jadwal tersedia (atau semua sudah diambil).</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- KRS Draft Sidebar (Kanan) --}}
            <div class="lg:col-span-4 space-y-6">
                <div class="bg-[#0f172a] rounded-3xl shadow-2xl border border-white/5 overflow-hidden sticky top-6">
                    <div class="px-8 py-6 border-b border-white/5 bg-white/5 flex justify-between items-center">
                        <div>
                            <h3 class="text-xs font-black text-yellow-400 uppercase tracking-[0.2em]">Draft KRS</h3>
                            <p class="text-[10px] text-white/40 font-bold uppercase mt-1">Semester {{ \App\Helpers\SistemHelper::getTahunAktif()->nama_tahun ?? '-' }}</p>
                        </div>
                        <span class="text-xs font-bold text-white bg-indigo-600 px-2 py-1 rounded">{{ $this->krsDiambil->count() }} MK</span>
                    </div>

                    <div class="max-h-[50vh] overflow-y-auto custom-scrollbar divide-y divide-white/5">
                        @forelse($this->krsDiambil as $detail)
                        @php 
                            // Tampilkan semester paket juga di sidebar kanan
                            $smtPaket = $semesterMap[$detail->jadwalKuliah->mata_kuliah_id] ?? null; 
                        @endphp
                        <div class="px-8 py-5 flex justify-between items-start group hover:bg-white/5 transition-colors">
                            <div class="flex-1 mr-4">
                                <p class="text-sm font-bold text-white leading-tight group-hover:text-yellow-400 transition-colors">{{ $detail->jadwalKuliah->mataKuliah->nama_mk }}</p>
                                <div class="flex items-center gap-2 mt-1">
                                    <p class="text-[10px] font-black text-white/30 uppercase tracking-widest">{{ $detail->jadwalKuliah->mataKuliah->sks_default }} SKS • {{ $detail->jadwalKuliah->nama_kelas }}</p>
                                    @if($smtPaket)
                                        <span class="text-[9px] font-bold text-sky-400 bg-sky-900/30 px-1.5 py-0.5 rounded border border-sky-500/20">Smt {{ $smtPaket }}</span>
                                    @endif
                                </div>
                            </div>
                            @if($statusKrs == 'DRAFT')
                            <button wire:click="hapusMatkul({{ $detail->id }})"
                                class="p-2 text-white/20 hover:text-rose-400 hover:bg-rose-400/10 rounded-lg transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                            @endif
                        </div>
                        @empty
                        <div class="px-8 py-12 text-center">
                            <svg class="w-12 h-12 text-white/10 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-[11px] font-bold text-white/20 uppercase tracking-widest">Draft Kosong</p>
                        </div>
                        @endforelse
                    </div>

                    {{-- Footer Sidebar: Action Button --}}
                    <div class="p-8 bg-black/20 border-t border-white/5">
                        <div class="flex justify-between items-end mb-6">
                            <span class="text-[10px] font-black text-white/40 uppercase tracking-widest">Akumulasi Kredit</span>
                            <span class="text-2xl font-black text-yellow-400">{{ $totalSks }} <span class="text-xs font-bold text-white/40 ml-1">SKS</span></span>
                        </div>

                        <!-- Logic Tombol Action -->
                        @if($statusKrs == 'DRAFT')
                            <button type="button" wire:click="ajukanKrs" wire:loading.attr="disabled"
                                wire:confirm="Yakin ajukan KRS? Setelah diajukan, Anda tidak dapat mengubah data kecuali ditolak oleh Dosen Wali."
                                @if($totalSks==0 || $blockKrs) disabled @endif
                                class="w-full py-4 bg-yellow-400 text-slate-900 rounded-2xl font-black text-xs uppercase tracking-[0.2em] shadow-xl shadow-yellow-500/10 hover:scale-[1.02] active:scale-95 transition-all disabled:opacity-50 disabled:grayscale disabled:cursor-not-allowed">
                                Ajukan Rencana Studi
                            </button>
                            <p class="text-[9px] text-white/30 font-bold uppercase text-center mt-4 tracking-tighter leading-relaxed">
                                Draft akan dikirimkan ke dosen wali <br>untuk verifikasi akademik.
                            </p>
                        @elseif($statusKrs == 'AJUKAN')
                            <div class="w-full py-4 bg-yellow-900/30 text-yellow-500 border border-yellow-500/30 rounded-2xl font-bold text-xs uppercase text-center tracking-widest">
                                Menunggu Persetujuan PA
                            </div>
                        @elseif($statusKrs == 'DISETUJUI')
                            <div class="w-full py-4 bg-green-900/30 text-green-500 border border-green-500/30 rounded-2xl font-bold text-xs uppercase text-center tracking-widest">
                                KRS Disetujui (Final)
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>