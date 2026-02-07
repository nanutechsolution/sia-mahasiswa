<div class="min-h-screen bg-slate-50 pb-32 lg:pb-12 font-sans text-slate-800">

    {{-- 1. STICKY HEADER --}}
    <header class=" top-0 z-40 bg-white/90 backdrop-blur-md border-b border-slate-200 shadow-sm transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">

                {{-- Profile Section --}}
                <div class="flex items-center gap-4 w-full md:w-auto">
                    <div class="relative shrink-0">
                        <div class="w-12 h-12 bg-[#002855] text-white rounded-full flex items-center justify-center font-bold text-lg shadow-md ring-4 ring-white">
                            {{ substr($mahasiswa->person->nama_lengkap, 0, 1) }}
                        </div>
                        <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-emerald-500 border-2 border-white rounded-full flex items-center justify-center">
                            <span class="text-[8px] text-white font-bold">✓</span>
                        </div>
                    </div>
                    <div class="min-w-0">
                        <h2 class="text-lg font-bold text-slate-900 truncate leading-tight">{{ $mahasiswa->person->nama_lengkap }}</h2>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-xs font-mono text-slate-500 bg-slate-100 px-2 py-0.5 rounded">{{ $mahasiswa->nim }}</span>
                            <span class="text-xs font-semibold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-full">Semester {{ $semesterBerjalan }}</span>
                        </div>
                    </div>
                </div>

                {{-- SKS Counter --}}
                <div class="w-full md:w-auto flex items-center justify-between md:justify-end bg-slate-50 md:bg-transparent p-3 md:p-0 rounded-xl border md:border-0 border-slate-200">
                    <span class="text-xs font-medium text-slate-500 md:hidden">Total Kredit Diambil</span>
                    <div class="text-right">
                        <div class="flex items-baseline justify-end gap-1.5">
                            <span class="text-3xl font-bold text-[#002855] tracking-tight">{{ $totalSks }}</span>
                            @if(!$isPaket && $maxSks > 0)
                            <span class="text-lg font-medium text-slate-400">/ {{ $maxSks }}</span>
                            @endif
                            <span class="text-xs font-bold text-slate-400 uppercase">SKS</span>
                        </div>
                        {{-- Progress Bar Visual --}}
                        @if($maxSks > 0)
                        <div class="w-32 h-1.5 bg-slate-200 rounded-full mt-1 ml-auto overflow-hidden hidden md:block">
                            <div class="h-full bg-[#002855] rounded-full transition-all duration-500" style="width: {{ ($totalSks / $maxSks) * 100 }}%"></div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">

        {{-- Alert Block --}}
        @if($blockKrs)
        <div class="mb-8 bg-red-50 border border-red-100 rounded-2xl p-5 flex items-start gap-4 animate-in fade-in slide-in-from-top-2">
            <div class="p-2 bg-red-100 text-red-600 rounded-lg shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <div>
                <h4 class="text-sm font-bold text-red-900">Akses KRS Terkunci</h4>
                <p class="text-sm text-red-700 mt-1 leading-relaxed">{{ $pesanBlock }}</p>
            </div>
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

            {{-- LEFT COLUMN: Selected Courses --}}
            <div class="lg:col-span-8 space-y-6">
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                        <h3 class="font-bold text-slate-700 flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            Mata Kuliah Diambil
                        </h3>
                        <span class="px-3 py-1 rounded-full text-xs font-bold {{ $statusKrs == 'DISETUJUI' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                            {{ $statusKrs }}
                        </span>
                    </div>

                    <div class="divide-y divide-slate-100">
                        @forelse($krsDiambil as $row)
                        <div class="p-5 hover:bg-slate-50 transition-colors group relative">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1 space-y-2">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="text-xs font-mono font-medium text-slate-500 bg-slate-100 px-2 py-0.5 rounded border border-slate-200">{{ $row->kode_mk_snapshot }}</span>
                                        @if($row->activity_type_snapshot != \App\Domains\Akademik\Models\KrsDetail::TYPE_REGULAR)
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-amber-100 text-amber-700 border border-amber-200">
                                            {{ $row->activity_label }}
                                        </span>
                                        @endif
                                    </div>

                                    <h4 class="text-base font-bold text-slate-800 leading-snug">{{ $row->nama_mk_snapshot }}</h4>
                                    {{-- Schedule Info --}}
                                    @if($row->jadwalKuliah)
                                    <div class="flex flex-wrap items-center gap-x-6 gap-y-2 text-sm text-slate-600 mt-2">
                                        <div class="flex items-center gap-2 min-w-[120px]">
                                            <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span class="font-medium">{{ $row->jadwalKuliah->hari }}, {{ substr($row->jadwalKuliah->jam_mulai, 0, 5) }}</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2" />
                                            </svg>
                                            <span>R.{{ $row->jadwalKuliah->ruang }} <span class="text-slate-400">({{ $row->jadwalKuliah->nama_kelas }})</span></span>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2 text-xs text-slate-500 mt-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        {{ $row->jadwalKuliah->dosen->person->nama_lengkap ?? 'Dosen Belum Ditentukan' }}
                                    </div>
                                    @else
                                    <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-amber-50 text-amber-700 text-xs font-medium rounded-lg border border-amber-100">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        Jadwal Fleksibel (Non-Kelas)
                                    </div>
                                    @endif
                                </div>

                                @if($statusKrs == 'DRAFT')
                                <button wire:click="hapusMatkul('{{ $row->id }}')"
                                    class="group/btn p-2 rounded-xl text-slate-400 hover:text-red-600 hover:bg-red-50 transition-all"
                                    title="Hapus Mata Kuliah">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                                @endif
                            </div>
                        </div>
                        @empty
                        <div class="py-12 flex flex-col items-center justify-center text-center">
                            <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-3">
                                <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                            </div>
                            <p class="text-sm font-medium text-slate-500">Belum ada mata kuliah yang dipilih.</p>
                            <p class="text-xs text-slate-400 mt-1">Silakan pilih dari daftar penawaran di sebelah kanan.</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- RIGHT COLUMN: Offers (Sticky Desktop) --}}
            <div class="lg:col-span-4 h-full">
                <div class="sticky top-28 space-y-6">

                    <div class="bg-white rounded-2xl shadow-xl shadow-slate-200/50 border border-slate-200 overflow-hidden flex flex-col max-h-[calc(100vh-140px)] {{ $statusKrs != 'DRAFT' || $blockKrs ? 'opacity-60 pointer-events-none grayscale' : '' }}">
                        <div class="p-4 bg-[#002855] text-white flex justify-between items-center shrink-0">
                            <h3 class="font-bold text-sm tracking-wide">PENAWARAN KELAS</h3>
                            <span class="text-[10px] bg-white/10 px-2 py-0.5 rounded text-white/80">Klik untuk ambil</span>
                        </div>

                        <div class="overflow-y-auto p-2 space-y-2 custom-scrollbar bg-slate-50 flex-1">
                            @forelse($jadwalTersedia as $j)
                            @php
                            $semData = \Illuminate\Support\Facades\DB::table('kurikulum_mata_kuliah')
                            ->join('master_kurikulums', 'kurikulum_mata_kuliah.kurikulum_id', '=', 'master_kurikulums.id')
                            ->where('kurikulum_mata_kuliah.mata_kuliah_id', $j->mata_kuliah_id)
                            ->where('master_kurikulums.prodi_id', $this->mahasiswa->prodi_id)
                            ->where('master_kurikulums.is_active', true)
                            ->select('kurikulum_mata_kuliah.semester_paket')
                            ->first();
                            @endphp

                            <div class="bg-white p-4 rounded-xl border border-slate-200 hover:border-indigo-300 hover:shadow-md transition-all group relative cursor-default">
                                {{-- Top Row: SKS & Sem --}}
                                <div class="flex justify-between items-start mb-2">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-50 text-indigo-700 font-bold text-xs border border-indigo-100">
                                        {{ $j->mataKuliah->sks_default }}<span class="text-[8px] font-normal ml-0.5">SKS</span>
                                    </span>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase bg-slate-100 px-2 py-0.5 rounded">
                                        SMT {{ $semData->semester_paket ?? '-' }}
                                    </span>
                                </div>

                                <h4 class="text-sm font-bold text-slate-800 leading-tight mb-3 group-hover:text-indigo-700 transition-colors">
                                    {{ $j->mataKuliah->nama_mk }}
                                </h4>

                                <div class="space-y-1.5 border-t border-slate-100 pt-3 mb-3">
                                    <div class="flex items-center gap-2 text-xs text-slate-600">
                                        <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span class="truncate">{{ $j->hari }}, {{ substr($j->jam_mulai, 0, 5) }}-{{ substr($j->jam_selesai, 0, 5) }}</span>
                                    </div>
                                    <div class="flex items-center gap-2 text-xs text-slate-600">
                                        <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2" />
                                        </svg>
                                        <span class="truncate">R.{{ $j->ruang }} (Kls {{ $j->nama_kelas }})</span>
                                    </div>
                                </div>

                                <button wire:click="ambilMatkul('{{ $j->id }}')"
                                    class="w-full py-2 bg-slate-800 text-white text-xs font-bold rounded-lg hover:bg-[#002855] hover:shadow-lg active:scale-95 transition-all flex items-center justify-center gap-2">
                                    <span>Ambil</span>
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                </button>
                            </div>
                            @empty
                            <div class="text-center py-8">
                                <p class="text-xs text-slate-400 italic">Tidak ada jadwal tersedia</p>
                            </div>
                            @endforelse
                        </div>
                    </div>

                    {{-- Action Button (Desktop) --}}
                    @if($statusKrs == 'DRAFT' && $totalSks > 0 && !$blockKrs)
                    <div class="hidden lg:block pt-2">
                        <button wire:click="ajukanKrs" class="w-full py-3.5 bg-[#fcc000] hover:bg-[#e6af00] text-[#002855] rounded-xl font-bold text-sm shadow-lg shadow-amber-500/20 hover:shadow-amber-500/30 transform hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2">
                            <span>Ajukan Rencana Studi</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
                            </svg>
                        </button>
                        <p class="text-center text-[10px] text-slate-400 mt-2">Pastikan pilihan Anda sudah final.</p>
                    </div>
                    @endif
                </div>
            </div>

        </div>
    </main>

    {{-- Mobile Sticky Action --}}
    @if($statusKrs == 'DRAFT' && $totalSks > 0 && !$blockKrs)
    <div class="lg:hidden fixed bottom-4 left-4 right-4 z-50">
        <button wire:click="ajukanKrs" class="w-full py-4 bg-[#002855] text-white rounded-2xl font-bold text-sm shadow-2xl flex items-center justify-between px-6 border border-white/10 backdrop-blur-xl">
            <span>Ajukan KRS ({{ $totalSks }} SKS)</span>
            <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </div>
        </button>
    </div>
    @endif

</div>