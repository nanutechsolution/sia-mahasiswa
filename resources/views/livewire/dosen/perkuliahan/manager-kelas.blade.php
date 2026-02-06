<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-[#002855] tracking-tight">Jadwal Mengajar Hari Ini</h1>
            {{-- Waktu Server WITA --}}
            <p class="text-sm text-slate-500 font-medium">{{ \Carbon\Carbon::now('Asia/Makassar')->isoFormat('dddd, D MMMM Y') }}</p>
        </div>
        <div class="flex items-center gap-3 self-end md:self-auto">
            <button wire:click="$refresh" class="p-2 text-slate-400 hover:text-[#002855] bg-white rounded-lg border border-slate-200 shadow-sm transition-colors" title="Refresh Data">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
            </button>
            <div class="bg-indigo-50 text-indigo-700 px-4 py-2 rounded-xl text-xs font-bold uppercase tracking-wider border border-indigo-100">
                Semester {{ $globalTa->nama_tahun ?? 'Aktif' }}
            </div>
        </div>
    </div>

    {{-- Notifikasi Sukses --}}
    @if (session()->has('success'))
    <div class="p-4 bg-emerald-50 text-emerald-800 rounded-xl font-bold text-sm border border-emerald-100 flex items-center gap-3 animate-in fade-in slide-in-from-top-2 shadow-sm">
        <div class="bg-emerald-100 p-1.5 rounded-full"><svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
            </svg></div>
        {{ session('success') }}
    </div>
    @endif

    {{-- Notifikasi Error --}}
    @if (session()->has('error'))
    <div class="p-4 bg-rose-50 text-rose-800 rounded-xl font-bold text-sm border border-rose-100 flex items-center gap-3 animate-in fade-in slide-in-from-top-2 shadow-sm">
        <div class="bg-rose-100 p-1.5 rounded-full"><svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12" />
            </svg></div>
        {{ session('error') }}
    </div>
    @endif

    {{-- Grid Jadwal --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        @forelse($jadwalHariIni as $jadwal)
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden group hover:border-indigo-200 transition-all duration-300 flex flex-col h-full">

            {{-- Header Kartu --}}
            <div class="p-6 border-b border-slate-50 flex justify-between items-start relative bg-white z-10">
                <div class="flex-1 min-w-0">
                    <div class="flex justify-between items-start mb-2">
                        <div class="flex gap-2">
                            <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-500 text-[10px] font-bold uppercase tracking-widest border border-slate-200">{{ $jadwal->kode_mk }}</span>
                            <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-500 text-[10px] font-bold uppercase tracking-widest border border-slate-200">{{ $jadwal->nama_kelas }}</span>
                        </div>
                        <button wire:click="cetakRekap('{{ $jadwal->id }}')" class="text-slate-300 hover:text-indigo-600 transition-colors" title="Cetak Rekap Absensi Semester">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                        </button>
                    </div>

                    <h3 class="text-lg font-bold text-[#002855] leading-snug truncate">{{ $jadwal->mataKuliah->nama_mk }}</h3>

                    <div class="flex flex-wrap items-center gap-4 mt-3 text-sm text-slate-500 font-medium">
                        <div class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ substr($jadwal->jam_mulai, 0, 5) }} - {{ substr($jadwal->jam_selesai, 0, 5) }}
                        </div>
                        <div class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2" />
                            </svg>
                            {{ $jadwal->ruang }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Action Area --}}
            <div class="bg-slate-50/50 flex-1 flex flex-col justify-center">
                @if($jadwal->sesiAktif)
                {{-- STATUS: KELAS SEDANG BERLANGSUNG --}}
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="relative flex h-3 w-3">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                                </span>
                                <span class="text-sm font-black text-emerald-600">Pertemuan {{ $jadwal->sesiAktif->pertemuan_ke }} Aktif</span>
                            </div>
                            <p class="text-[10px] font-bold text-slate-400 mt-1 uppercase tracking-wider ml-5">
                                {{ $jadwal->sesiAktif->metode_validasi == 'TUGAS' ? 'Penugasan' : $jadwal->sesiAktif->metode_validasi }}
                            </p>
                        </div>

                        <button wire:click="tutupSesi('{{ $jadwal->sesiAktif->id }}')"
                            wire:confirm="Yakin ingin menutup kelas? Mahasiswa yang belum absen akan otomatis Alpha."
                            class="px-4 py-2 bg-white border border-rose-200 text-rose-600 rounded-lg text-xs font-bold shadow-sm hover:bg-rose-50 transition-all flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Tutup Kelas
                        </button>
                    </div>

                    {{-- Statistik Kehadiran --}}
                    @php
                    $hadirCount = $jadwal->sesiAktif->absensi_count ?? 0;
                    // FIX: Gunakan jumlah peserta KRS yang disetujui, bukan kuota kelas
                    $totalMhs = $jadwal->jumlah_peserta > 0 ? $jadwal->jumlah_peserta : 1;
                    $persen = ($hadirCount / $totalMhs) * 100;
                    @endphp
                    <div class="mb-6 bg-white p-4 rounded-xl border border-slate-100 shadow-sm">
                        <div class="flex justify-between items-end mb-2">
                            <span class="text-xs font-bold text-slate-500 uppercase tracking-wide">Kehadiran Real-time</span>
                            <span class="text-sm font-black text-[#002855]">{{ $hadirCount }} <span class="text-slate-400 font-medium">/ {{ $jadwal->jumlah_peserta }} Mhs</span></span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                            <div class="bg-[#002855] h-2 rounded-full transition-all duration-1000 ease-out" style="width: {{ $persen }}%"></div>
                        </div>
                    </div>

                    {{-- Token & Detail --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @if($jadwal->sesiAktif->metode_validasi == 'QR')
                        <div class="p-4 bg-[#002855] rounded-xl flex flex-col justify-center items-center text-white relative overflow-hidden group/token cursor-pointer shadow-lg shadow-indigo-900/20" onclick="navigator.clipboard.writeText('{{ $jadwal->sesiAktif->token_sesi }}'); alert('Token disalin!')">
                            <div class="absolute inset-0 bg-white/5 opacity-50 pattern-grid-lg"></div>
                            <span class="text-[9px] font-medium opacity-70 uppercase tracking-widest mb-1 relative z-10">Token Kelas</span>
                            <span class="font-mono text-3xl font-black tracking-widest relative z-10 group-hover/token:scale-110 transition-transform">{{ $jadwal->sesiAktif->token_sesi }}</span>
                        </div>
                        @elseif($jadwal->sesiAktif->metode_validasi == 'GPS')
                        <div class="p-4 bg-blue-50 border border-blue-100 rounded-xl flex flex-col justify-center items-center text-blue-800">
                            <svg class="w-8 h-8 mb-1 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span class="text-[10px] font-bold text-center uppercase tracking-wide">Validasi GPS Aktif</span>
                        </div>
                        @else
                        <div class="p-4 bg-slate-100 border border-slate-200 rounded-xl flex flex-col justify-center items-center text-slate-600">
                            <span class="text-[10px] font-bold text-center uppercase tracking-wide">Mode Bebas / Manual</span>
                        </div>
                        @endif

                        <button wire:click="bukaDetailPresensi('{{ $jadwal->sesiAktif->id }}')" class="p-4 bg-white border border-slate-200 rounded-xl flex flex-col justify-center items-center text-slate-600 hover:border-[#002855] hover:text-[#002855] hover:shadow-md transition-all group/btn">
                            <svg class="w-6 h-6 mb-2 text-slate-400 group-hover/btn:text-[#002855] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                            <span class="text-xs font-bold">Daftar Hadir</span>
                        </button>
                    </div>
                </div>
                @else
                {{-- STATUS: KELAS BELUM DIBUKA --}}
                <div class="p-6 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center border border-slate-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Status</p>
                            @if($jadwal->jumlah_peserta > 0)
                            <p class="text-sm font-bold text-slate-600">Sesi Belum Dibuka</p>
                            @else
                            <p class="text-sm font-bold text-rose-500">Belum Ada Peserta</p>
                            @endif
                        </div>
                    </div>

                    @if($jadwal->jumlah_peserta > 0)
                    <button wire:click="openModalBuka('{{ $jadwal->id }}')"
                        class="px-5 py-2.5 bg-[#002855] text-white rounded-xl text-sm font-bold shadow-lg shadow-indigo-900/20 hover:bg-[#001a38] hover:scale-105 transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Buka Kelas
                    </button>
                    @else
                    <button disabled class="px-5 py-2.5 bg-slate-100 text-slate-400 rounded-xl text-sm font-bold border border-slate-200 cursor-not-allowed flex items-center gap-2" title="Belum ada mahasiswa yang KRS-nya disetujui">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        Terkunci (0 Mhs)
                    </button>
                    @endif
                </div>
                @endif
            </div>
        </div>
        @empty
        <div class="lg:col-span-2 py-16 text-center border-2 border-dashed border-slate-200 rounded-2xl bg-slate-50/30">
            <div class="w-16 h-16 bg-white border border-slate-100 text-slate-300 rounded-full flex items-center justify-center mx-auto mb-3 shadow-sm">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            <h3 class="text-slate-900 font-bold">Tidak Ada Jadwal Hari Ini</h3>
            <p class="text-slate-500 text-sm mt-1 max-w-sm mx-auto">Anda tidak memiliki jadwal mengajar hari ini.</p>
        </div>
        @endforelse
    </div>

    {{-- MODAL BUKA KELAS (PROFESSIONAL UI) --}}
    @if($isModalOpen)
    <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-4 sm:p-6 bg-slate-900/60 backdrop-blur-sm transition-opacity"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0">

        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden flex flex-col max-h-[90vh]"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">

            {{-- Header --}}
            <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-white sticky top-0 z-10">
                <div>
                    <h3 class="font-black text-xl text-[#002855] tracking-tight">Mulai Perkuliahan</h3>
                    <p class="text-xs text-slate-500 font-medium mt-0.5">Silakan atur detail sesi pertemuan hari ini</p>
                </div>
                <button wire:click="$set('isModalOpen', false)" class="group p-2 rounded-full hover:bg-slate-100 transition-colors focus:outline-none">
                    <svg class="w-5 h-5 text-slate-400 group-hover:text-rose-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Body --}}
            <div class="p-6 space-y-6 overflow-y-auto custom-scrollbar">

                {{-- Row 1: Pertemuan & Metode --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    {{-- Input Pertemuan --}}
                    <div class="group">
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">Pertemuan Ke</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <span class="text-slate-400 font-bold text-xs">#</span>
                            </div>
                            <input type="number" wire:model="pertemuan_ke" class="pl-8 py-2.5 w-full rounded-xl border-slate-200 font-bold text-slate-800 text-sm focus:ring-2 focus:ring-[#002855] focus:border-transparent transition-all shadow-sm group-hover:border-slate-300 placeholder:font-normal" placeholder="1">
                        </div>
                    </div>

                    {{-- Input Metode --}}
                    <div class="group">
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">Metode Validasi</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <select wire:model.live="metode_validasi" class="pl-9 py-2.5 w-full rounded-xl border-slate-200 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-[#002855] focus:border-transparent shadow-sm appearance-none cursor-pointer hover:bg-slate-50 transition-all">
                                <option value="GPS">Tatap Muka (GPS)</option>
                                <option value="QR">Tatap Muka (QR Code)</option>
                                <option value="DARING">Daring / Online</option>
                                <option value="MANUAL">Manual Input</option>
                                <option value="TUGAS">Penugasan (Mandiri)</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none text-slate-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Info Box Dynamic --}}
                <div class="rounded-lg p-3 border text-xs leading-relaxed flex gap-3 
                    @if($metode_validasi == 'GPS') bg-blue-50 border-blue-100 text-blue-700 
                    @elseif($metode_validasi == 'QR') bg-purple-50 border-purple-100 text-purple-700
                    @elseif($metode_validasi == 'DARING') bg-amber-50 border-amber-100 text-amber-700
                    @elseif($metode_validasi == 'TUGAS') bg-emerald-50 border-emerald-100 text-emerald-700
                    @else bg-slate-50 border-slate-200 text-slate-600 @endif">

                    <div class="shrink-0 mt-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        @if($metode_validasi == 'GPS')
                        <strong>Validasi Lokasi:</strong> Mahasiswa wajib berada dalam radius kampus untuk melakukan check-in via aplikasi.
                        @elseif($metode_validasi == 'QR')
                        <strong>Validasi Token:</strong> Anda harus menampilkan Kode Token di proyektor. Mahasiswa menginput token untuk absen.
                        @elseif($metode_validasi == 'DARING')
                        <strong>Fleksibel:</strong> Mahasiswa dapat melakukan check-in dari mana saja. Cocok untuk kelas Zoom atau E-Learning.
                        @elseif($metode_validasi == 'TUGAS')
                        <strong>Penugasan Mandiri:</strong> Mahasiswa absen mandiri tanpa validasi lokasi. Cocok untuk tugas asinkron.
                        @else
                        <strong>Manual:</strong> Mahasiswa tidak bisa check-in mandiri. Anda harus menginput kehadiran satu per satu.
                        @endif
                    </div>
                </div>

                {{-- Input Materi --}}
                <div class="group">
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">
                        Pokok Bahasan / Materi
                        <span class="text-rose-500">*</span>
                    </label>
                    <textarea wire:model="materi_kuliah" rows="4" class="w-full rounded-xl border-slate-200 text-sm p-3 focus:ring-2 focus:ring-[#002855] focus:border-transparent shadow-sm resize-none placeholder:text-slate-400 transition-all" placeholder="Tuliskan ringkasan materi atau topik yang dibahas pada pertemuan ini..."></textarea>
                    @error('materi_kuliah')
                    <div class="flex items-center gap-1 mt-1.5 text-rose-500">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-xs font-bold">{{ $message }}</span>
                    </div>
                    @enderror
                </div>

            </div>

            {{-- Footer --}}
            <div class="px-6 py-5 bg-slate-50 border-t border-slate-100 flex justify-end gap-3 sticky bottom-0 z-10">
                <button wire:click="$set('isModalOpen', false)" class="px-5 py-2.5 text-sm font-bold text-slate-600 hover:text-slate-800 hover:bg-white border border-transparent hover:border-slate-200 rounded-xl transition-all">
                    Batal
                </button>
                <button wire:click="bukaSesi" class="px-6 py-2.5 bg-[#002855] text-white text-sm font-bold rounded-xl shadow-lg shadow-[#002855]/20 hover:bg-[#001a38] hover:shadow-[#002855]/40 hover:-translate-y-0.5 active:scale-95 transition-all flex items-center gap-2">
                    <span>Mulai Sesi</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL DETAIL PRESENSI (Daftar Hadir & Manual Input) --}}
    @if($isDetailOpen)
    <div class="fixed inset-0 z-[60] flex items-end sm:items-center justify-center sm:p-4">
        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"
            wire:click="tutupDetailPresensi"></div>

        <div class="relative bg-white w-full sm:max-w-4xl h-[90vh] sm:h-[80vh] rounded-t-3xl sm:rounded-2xl shadow-2xl flex flex-col overflow-hidden animate-in slide-in-from-bottom-10 sm:zoom-in-95 duration-300">

            {{-- Header Modal --}}
            <div class="px-6 py-4 border-b border-slate-100 bg-white z-10 flex justify-between items-center shrink-0">
                <div>
                    <h3 class="font-black text-xl text-[#002855]">Daftar Hadir Mahasiswa</h3>
                    <div class="flex items-center gap-2 text-sm text-slate-500 mt-0.5">
                        <span class="font-bold">Pertemuan {{ $detailSesi->pertemuan_ke }}</span>
                        <span>&bull;</span>
                        <span>{{ $detailSesi->jadwalKuliah->mataKuliah->nama_mk }}</span>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    {{-- TOMBOL CETAK SESI INI --}}
                    <button wire:click="cetakPresensi('{{ $detailSesi->id }}')" class="flex items-center gap-2 px-4 py-2 bg-slate-100 text-slate-600 rounded-lg text-xs font-bold hover:bg-[#002855] hover:text-white transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Cetak PDF
                    </button>

                    <button wire:click="tutupDetailPresensi" class="p-2 bg-slate-50 text-slate-400 hover:text-rose-500 hover:bg-rose-50 rounded-full transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Content Area --}}
            <div class="flex-1 overflow-y-auto custom-scrollbar p-0 bg-slate-50">
                {{-- Statistik Cepat --}}
                <div class="grid grid-cols-4 gap-1 p-2 bg-white border-b border-slate-100 sticky top-0 z-10 text-center">
                    <div class="p-2 rounded-lg bg-emerald-50 text-emerald-700">
                        <div class="text-xs font-bold uppercase">Hadir</div>
                        <div class="text-xl font-black">{{ $daftarPeserta->where('status', 'H')->count() }}</div>
                    </div>
                    <div class="p-2 rounded-lg bg-blue-50 text-blue-700">
                        <div class="text-xs font-bold uppercase">Ijin/Skt</div>
                        <div class="text-xl font-black">{{ $daftarPeserta->whereIn('status', ['I','S'])->count() }}</div>
                    </div>
                    <div class="p-2 rounded-lg bg-rose-50 text-rose-700">
                        <div class="text-xs font-bold uppercase">Alpha</div>
                        <div class="text-xl font-black">{{ $daftarPeserta->where('status', 'A')->count() }}</div>
                    </div>
                    <div class="p-2 rounded-lg bg-slate-50 text-slate-600">
                        <div class="text-xs font-bold uppercase">Total</div>
                        <div class="text-xl font-black">{{ $daftarPeserta->count() }}</div>
                    </div>
                </div>

                {{-- List Mahasiswa --}}
                <div class="divide-y divide-slate-100 bg-white">
                    @foreach($daftarPeserta as $mhs)
                    <div class="p-4 flex items-center justify-between hover:bg-slate-50 transition-colors group">
                        <div class="flex items-center gap-4 min-w-0">
                            {{-- Avatar --}}
                            <div class="relative shrink-0">
                                <div class="w-10 h-10 rounded-full bg-[#002855] text-white flex items-center justify-center font-bold text-sm">
                                    {{ substr($mhs['nama'], 0, 1) }}
                                </div>
                                @if($mhs['status'] == 'H')
                                <div class="absolute -bottom-1 -right-1 bg-emerald-500 border-2 border-white rounded-full p-0.5">
                                    <svg class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                @endif
                            </div>

                            {{-- Info --}}
                            <div class="min-w-0">
                                <h4 class="font-bold text-slate-800 text-sm truncate">{{ $mhs['nama'] }}</h4>
                                <div class="flex items-center gap-2 text-xs text-slate-500 mt-0.5">
                                    <span class="font-mono">{{ $mhs['nim'] }}</span>
                                    @if($mhs['waktu_absen'])
                                    <span class="text-emerald-600 font-bold bg-emerald-50 px-1.5 rounded flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ \Carbon\Carbon::parse($mhs['waktu_absen'])->format('H:i') }}
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Action Toggles --}}
                        <div class="flex items-center bg-slate-100 rounded-lg p-1">
                            <button wire:click="updateStatus('{{ $mhs['krs_detail_id'] }}', 'H')"
                                class="w-8 h-8 rounded-md flex items-center justify-center text-xs font-bold transition-all {{ $mhs['status'] == 'H' ? 'bg-emerald-500 text-white shadow-sm' : 'text-slate-400 hover:text-slate-600' }}">
                                H
                            </button>
                            <button wire:click="updateStatus('{{ $mhs['krs_detail_id'] }}', 'I')"
                                class="w-8 h-8 rounded-md flex items-center justify-center text-xs font-bold transition-all {{ $mhs['status'] == 'I' ? 'bg-blue-500 text-white shadow-sm' : 'text-slate-400 hover:text-slate-600' }}">
                                I
                            </button>
                            <button wire:click="updateStatus('{{ $mhs['krs_detail_id'] }}', 'S')"
                                class="w-8 h-8 rounded-md flex items-center justify-center text-xs font-bold transition-all {{ $mhs['status'] == 'S' ? 'bg-amber-500 text-white shadow-sm' : 'text-slate-400 hover:text-slate-600' }}">
                                S
                            </button>
                            <button wire:click="updateStatus('{{ $mhs['krs_detail_id'] }}', 'A')"
                                class="w-8 h-8 rounded-md flex items-center justify-center text-xs font-bold transition-all {{ $mhs['status'] == 'A' ? 'bg-rose-500 text-white shadow-sm' : 'text-slate-400 hover:text-slate-600' }}">
                                A
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Footer info --}}
            <div class="p-4 border-t border-slate-100 bg-slate-50 text-center text-xs text-slate-400">
                Data kehadiran disimpan otomatis saat tombol ditekan.
            </div>
        </div>
    </div>
    @endif
</div>