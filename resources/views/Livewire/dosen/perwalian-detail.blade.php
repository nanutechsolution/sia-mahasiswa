<div class="space-y-6 animate-in fade-in duration-500">

    {{-- Breadcrumb / Back --}}
    <div>
        <a href="{{ route('dosen.perwalian') }}" class="inline-flex items-center text-sm font-bold text-slate-500 hover:text-[#002855] transition-colors">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Daftar Perwalian
        </a>
    </div>

    {{-- Student Info Card --}}
    <div class="bg-white rounded-3xl shadow-lg border border-slate-200 overflow-hidden relative">
        <div class="absolute top-0 right-0 p-4 opacity-5 pointer-events-none">
            <svg class="w-40 h-40 text-[#002855]" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 14l9-5-9-5-9 5 9 5z" />
                <path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
            </svg>
        </div>
        <div class="p-8 relative z-10">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                <div>
                    <h1 class="text-2xl font-black text-[#002855] uppercase tracking-tight">{{ $krs->mahasiswa->person->nama_lengkap }}</h1>
                    <div class="flex items-center gap-3 mt-2 text-sm">
                        <span class="font-mono font-bold bg-[#002855]/10 text-[#002855] px-2 py-0.5 rounded">{{ $krs->mahasiswa->nim }}</span>
                        <span class="text-slate-400">&bull;</span>
                        <span class="text-slate-600 font-bold uppercase">{{ $krs->mahasiswa->prodi->nama_prodi }}</span>
                    </div>
                </div>

                <div class="flex items-center gap-6">
                    <div class="text-right">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Status KRS</p>
                        @if($krs->status_krs == 'DISETUJUI')
                        <span class="text-emerald-600 font-black text-lg uppercase">Disetujui</span>
                        @elseif($krs->status_krs == 'AJUKAN')
                        <span class="text-[#fcc000] font-black text-lg uppercase">Menunggu</span>
                        @else
                        <span class="text-slate-400 font-black text-lg uppercase">Draft</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- STATISTIK AKADEMIK --}}
            <div class="mt-8 pt-6 border-t border-slate-100 grid grid-cols-2 md:grid-cols-4 gap-6">
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">IPS Semester Lalu</p>
                    <p class="text-xl font-black text-slate-700">{{ number_format($riwayat->ips ?? 0, 2) }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">IPK Saat Ini</p>
                    <p class="text-xl font-black text-[#002855]">{{ number_format($riwayat->ipk ?? 0, 2) }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total SKS Lulus</p>
                    <p class="text-xl font-black text-slate-700">{{ $riwayat->sks_total ?? 0 }}</p>
                </div>
                <div class="bg-[#fcc000]/10 -my-2 p-2 rounded-lg border border-[#fcc000]/20">
                    <p class="text-[10px] font-bold text-[#002855] uppercase tracking-wider">SKS Diajukan (Skrg)</p>
                    <p class="text-2xl font-black text-[#002855]">{{ $totalSks }}</p>
                </div>
            </div>

            {{-- DETAIL NILAI SEMESTER LALU (COLLAPSIBLE) --}}
            @if(count($khsLalu) > 0)
            <div class="mt-6 border-t border-slate-100 pt-4" x-data="{ expanded: false }">
                <button @click="expanded = !expanded" class="flex items-center text-xs font-bold text-slate-500 hover:text-[#002855] transition-colors focus:outline-none">
                    <svg class="w-4 h-4 mr-1 transition-transform duration-200" :class="expanded ? 'rotate-90' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    <span>Lihat Detail Nilai Semester Lalu ({{ count($khsLalu) }} MK)</span>
                </button>

                <div x-show="expanded" x-transition class="mt-4 overflow-hidden rounded-xl border border-slate-200 bg-slate-50/50">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-100">
                            <tr>
                                <th class="px-4 py-2 text-left text-[10px] font-bold text-slate-500 uppercase tracking-wider">Mata Kuliah</th>
                                <th class="px-4 py-2 text-center text-[10px] font-bold text-slate-500 uppercase tracking-wider">SKS</th>
                                <th class="px-4 py-2 text-center text-[10px] font-bold text-slate-500 uppercase tracking-wider">Nilai</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($khsLalu as $khs)
                            <tr class="hover:bg-white transition-colors">
                                <td class="px-4 py-2 text-xs font-medium text-slate-700">
                                    {{ $khs->nama_mk }} <span class="text-[10px] text-slate-400 font-mono ml-1">{{ $khs->kode_mk }}</span>
                                </td>
                                <td class="px-4 py-2 text-center text-xs text-slate-600 font-bold">{{ $khs->sks_default }}</td>
                                <td class="px-4 py-2 text-center text-xs font-bold">
                                    <span class="{{ in_array($khs->nilai_huruf, ['A', 'A-', 'B+', 'B']) ? 'text-emerald-600' : ($khs->nilai_huruf == 'E' ? 'text-rose-600' : 'text-indigo-600') }}">
                                        {{ $khs->nilai_huruf }}
                                    </span>
                                    <span class="text-[10px] text-slate-400 font-normal ml-1">({{ $khs->nilai_indeks }})</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

        </div>
    </div>

    @if (session()->has('success'))
    <div class="bg-emerald-50 border border-emerald-100 p-4 rounded-xl text-emerald-800 text-sm flex items-center shadow-sm animate-in fade-in slide-in-from-top-2">
        <svg class="w-5 h-5 mr-3 text-emerald-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
        </svg>
        <span class="font-bold">{{ session('success') }}</span>
    </div>
    @endif

    {{-- Content Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- List Mata Kuliah --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-6 py-4 bg-slate-50 border-b border-slate-100">
                    <h3 class="font-bold text-[#002855] text-sm uppercase tracking-wide">Rencana Studi Diambil</h3>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse($krs->details as $detail)
                    <div class="p-5 flex items-start gap-4 hover:bg-slate-50/50 transition-colors">
                        <div class="flex-shrink-0 mt-1">
                            <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 text-xs font-black">
                                {{ $detail->jadwalKuliah->mataKuliah->sks_default }}
                            </span>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-sm font-bold text-slate-800">{{ $detail->jadwalKuliah->mataKuliah->nama_mk }}</h4>
                            <p class="text-xs font-mono text-slate-400 mt-0.5">{{ $detail->jadwalKuliah->mataKuliah->kode_mk }}</p>

                            <div class="flex flex-wrap gap-y-2 gap-x-4 mt-3">
                                <div class="flex items-center text-xs text-slate-500 font-medium">
                                    <svg class="w-3.5 h-3.5 mr-1.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ $detail->jadwalKuliah->hari }}, {{ \Carbon\Carbon::parse($detail->jadwalKuliah->jam_mulai)->format('H:i') }}
                                </div>
                                <div class="flex items-center text-xs text-slate-500 font-medium">
                                    <svg class="w-3.5 h-3.5 mr-1.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    R. {{ $detail->jadwalKuliah->ruang }}
                                </div>
                                <div class="flex items-center text-xs text-slate-500 font-medium">
                                    <svg class="w-3.5 h-3.5 mr-1.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    {{ $detail->jadwalKuliah->dosen->nama_lengkap_gelar ?? 'Dosen' }}
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="p-8 text-center text-slate-400 text-sm italic">
                        Tidak ada mata kuliah diambil.
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Action Sidebar --}}
        <div class="lg:col-span-1">
            <div class="bg-white p-6 rounded-2xl shadow-lg border border-indigo-100 sticky top-6">
                <h3 class="text-sm font-black text-[#002855] uppercase tracking-widest mb-4">Aksi Dosen Wali</h3>

                @if($krs->status_krs == 'AJUKAN')
                <div class="space-y-3">
                    <button wire:click="setujui"
                        wire:confirm="Setujui KRS ini? Mahasiswa akan resmi terdaftar di kelas."
                        class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold text-sm shadow-md transition-all flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        SETUJUI KRS
                    </button>

                    <button wire:click="tolak"
                        wire:confirm="Tolak dan kembalikan ke Draft? Mahasiswa harus merevisi KRS."
                        class="w-full py-3 bg-white border-2 border-rose-100 text-rose-600 hover:bg-rose-50 hover:border-rose-200 rounded-xl font-bold text-sm transition-all flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Tolak / Revisi
                    </button>
                </div>
                <p class="text-[10px] text-slate-400 mt-4 leading-relaxed text-center">
                    Periksa <strong>IPS Lalu</strong> dan <strong>IPK</strong> di atas sebelum menyetujui total SKS yang diajukan.
                </p>
                @elseif($krs->status_krs == 'DISETUJUI')
                <div class="bg-emerald-50 border border-emerald-100 p-4 rounded-xl text-center">
                    <div class="inline-flex p-2 bg-emerald-100 rounded-full text-emerald-600 mb-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <h4 class="text-sm font-bold text-emerald-800">KRS Telah Disetujui</h4>
                    <p class="text-xs text-emerald-600 mt-1">Pada {{ $krs->updated_at->format('d M Y H:i') }}</p>

                    <button wire:click="tolak" wire:confirm="Batalkan persetujuan? Status akan kembali ke Draft." class="mt-4 text-xs font-bold text-slate-400 hover:text-rose-500 underline decoration-dotted">
                        Batalkan Persetujuan
                    </button>
                </div>
                @else
                <div class="bg-slate-50 border border-slate-100 p-4 rounded-xl text-center">
                    <p class="text-sm font-bold text-slate-500">Status: DRAFT</p>
                    <p class="text-xs text-slate-400 mt-1">Mahasiswa belum mengajukan KRS ini.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>