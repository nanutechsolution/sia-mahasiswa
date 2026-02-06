<div wire:poll.30s="cekJadwalBerlangsung" class="w-full max-w-xl mx-auto">
    
    {{-- Notifikasi Toast (Floating) --}}
    {{-- PERBAIKAN: Tambah wire:key acak dan auto-clear ke backend --}}
    @if($notifMessage)
    <div wire:key="toast-{{ Str::random(10) }}" 
         x-data="{ show: true }" 
         x-show="show" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         x-init="setTimeout(() => { show = false; setTimeout(() => $wire.set('notifMessage', null), 300) }, 5000)" 
         class="fixed top-24 right-4 left-4 z-50 md:left-auto md:w-96">
         
        <div class="rounded-2xl p-4 shadow-2xl flex items-start gap-3 border border-white/20 backdrop-blur-md
            {{ $notifType === 'error' ? 'bg-rose-500/95 text-white shadow-rose-900/20' : '' }}
            {{ $notifType === 'success' ? 'bg-emerald-500/95 text-white shadow-emerald-900/20' : '' }}
            {{ $notifType === 'warning' ? 'bg-amber-500/95 text-white shadow-amber-900/20' : '' }}
            {{ $notifType === 'info' ? 'bg-blue-500/95 text-white shadow-blue-900/20' : '' }}">
            
            <div class="shrink-0 mt-0.5">
                @if($notifType === 'error') 
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                @elseif($notifType === 'success') 
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                @else 
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                @endif
            </div>
            <div class="flex-1">
                <p class="font-bold text-sm">{{ ucfirst($notifType) }}</p>
                <p class="text-xs opacity-90 leading-relaxed">{{ $notifMessage }}</p>
            </div>
            <button @click="show = false" class="opacity-70 hover:opacity-100"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
    </div>
    @endif

    @if($jadwalAktif && $sesiAktif)
        @php
            $isDaring = \Illuminate\Support\Str::contains(strtoupper($jadwalAktif->ruang ?? ''), ['DARING', 'ONLINE', 'ZOOM']);
            $isMandiri = \Illuminate\Support\Str::contains(strtoupper($jadwalAktif->ruang ?? ''), ['MANDIRI']);
            
            if ($isDaring) {
                $bgClass = 'bg-gradient-to-br from-indigo-600 to-purple-800';
                $accentClass = 'text-indigo-200';
                $iconClass = 'text-indigo-300';
            } elseif ($isMandiri) {
                $bgClass = 'bg-gradient-to-br from-orange-500 to-red-600';
                $accentClass = 'text-orange-200';
                $iconClass = 'text-orange-200';
            } else {
                $bgClass = 'bg-gradient-to-br from-[#002855] to-[#001a38]';
                $accentClass = 'text-slate-300';
                $iconClass = 'text-[#fcc000]';
            }
        @endphp

        <div class="relative overflow-hidden rounded-[2rem] {{ $bgClass }} text-white shadow-2xl shadow-slate-200 ring-1 ring-white/10">
            
            {{-- Background Effects --}}
            <div class="absolute -right-20 -top-20 h-80 w-80 rounded-full bg-white/10 blur-3xl mix-blend-overlay"></div>
            <div class="absolute -left-20 -bottom-20 h-60 w-60 rounded-full bg-white/5 blur-3xl mix-blend-overlay"></div>

            <div class="relative z-10 p-6 sm:p-8">
                
                {{-- Header Status --}}
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-2 px-3 py-1 rounded-full bg-black/20 backdrop-blur-md border border-white/10">
                        <span class="relative flex h-2.5 w-2.5">
                          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                          <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                        </span>
                        <span class="text-[10px] font-black uppercase tracking-widest text-white/90">Sesi Aktif</span>
                    </div>
                    
                    @if($waktuAbsen)
                        <div class="flex items-center gap-1.5 text-xs font-bold text-emerald-300">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Terverifikasi</span>
                        </div>
                    @endif
                </div>

                {{-- Course Info --}}
                <div class="space-y-1 mb-8">
                    <h2 class="text-2xl sm:text-3xl font-black leading-tight tracking-tight text-white">
                        {{ $jadwalAktif->mataKuliah->nama_mk }}
                    </h2>
                    <div class="flex items-center gap-2 {{ $accentClass }} text-sm font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <span>{{ $jadwalAktif->dosen->person->nama_lengkap ?? 'Dosen Pengampu' }}</span>
                    </div>
                </div>

                {{-- Metadata Grid --}}
                <div class="grid grid-cols-2 gap-3 mb-8">
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-3 border border-white/5">
                        <p class="text-[10px] uppercase tracking-wider text-white/60 font-bold mb-1">Waktu</p>
                        <div class="flex items-center gap-2 font-bold text-sm">
                            <svg class="w-4 h-4 {{ $iconClass }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            {{ substr($jadwalAktif->jam_mulai, 0, 5) }} - {{ substr($jadwalAktif->jam_selesai, 0, 5) }}
                        </div>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-3 border border-white/5">
                        <p class="text-[10px] uppercase tracking-wider text-white/60 font-bold mb-1">Ruang</p>
                        <div class="flex items-center gap-2 font-bold text-sm">
                            <svg class="w-4 h-4 {{ $iconClass }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2"/></svg>
                            {{ $jadwalAktif->ruang }}
                        </div>
                    </div>
                    @if($sesiAktif->materi_kuliah)
                    <div class="col-span-2 bg-white/10 backdrop-blur-sm rounded-xl p-3 border border-white/5">
                        <p class="text-[10px] uppercase tracking-wider text-white/60 font-bold mb-1">Materi / Topik</p>
                        <p class="text-xs leading-relaxed text-white/90 line-clamp-2">
                            {{ $sesiAktif->materi_kuliah }}
                        </p>
                    </div>
                    @endif
                </div>

                {{-- ACTION AREA --}}
                <div x-data="geoHandler()" class="relative">
                    @if($sudahAbsen)
                        <div class="w-full bg-emerald-500/20 border border-emerald-500/50 rounded-2xl p-4 flex items-center justify-between backdrop-blur-md">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-full bg-emerald-500 flex items-center justify-center shadow-lg shadow-emerald-500/30">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <div>
                                    <h4 class="font-bold text-white text-sm">Absensi Berhasil</h4>
                                    <p class="text-xs text-emerald-200">Tercatat pukul {{ $waktuAbsen }} WIB</p>
                                </div>
                            </div>
                        </div>
                    @else
                        {{-- Tombol Check-In dengan Proteksi Double Click --}}
                        <button 
                            @click="getLocation()"
                            :disabled="isLocating" 
                            :class="{'opacity-75 cursor-wait scale-[0.98] bg-slate-100': isLocating, 'hover:scale-[1.02] hover:bg-[#fcc000] bg-white': !isLocating}"
                            class="group relative w-full overflow-hidden rounded-2xl p-1 text-left shadow-xl transition-all disabled:opacity-70 disabled:cursor-not-allowed">
                            
                            <div class="relative z-10 flex items-center gap-4 p-2">
                                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-[#002855] text-white transition-all"
                                     :class="{'bg-slate-300': isLocating}">
                                    
                                    {{-- Icon Default --}}
                                    <svg x-show="!isLocating" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    
                                    {{-- Icon Loading --}}
                                    <svg x-show="isLocating" class="h-6 w-6 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-[10px] font-bold uppercase text-slate-400 group-hover:text-[#002855]/60" x-text="isLocating ? 'Mohon Tunggu...' : 'Ketuk untuk Absen'"></p>
                                    <h3 class="text-base font-black text-[#002855]" x-text="isLocating ? 'Sedang Memproses...' : 'Konfirmasi Kehadiran'"></h3>
                                </div>
                                <div class="pr-2 text-slate-300 group-hover:text-[#002855]" x-show="!isLocating">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </div>
                            </div>
                            
                            {{-- Progress Bar effect on loading --}}
                            <div x-show="isLocating" class="absolute bottom-0 left-0 h-1 w-full bg-slate-200">
                                <div class="h-full bg-[#fcc000] animate-[shimmer_2s_infinite]"></div>
                            </div>
                        </button>

                        {{-- Status Text JS --}}
                        <div x-show="isLocating" x-transition class="mt-3 flex justify-center items-center gap-2 text-xs font-medium text-white/70">
                            <svg class="w-3 h-3 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            <span x-text="statusMsg"></span>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    @else
        <div class="p-8 text-center border-2 border-dashed border-slate-200 rounded-3xl bg-slate-50/50">
            <div class="mx-auto w-16 h-16 bg-slate-200 rounded-full flex items-center justify-center text-slate-400 mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <h3 class="text-slate-800 font-bold text-lg">Tidak Ada Kelas Aktif</h3>
            <p class="text-slate-500 text-sm mt-1 max-w-xs mx-auto">
                Belum ada sesi perkuliahan yang dibuka oleh dosen saat ini. Silakan tunggu jadwal berikutnya.
            </p>
        </div>
    @endif

    {{-- JAVASCRIPT GEOLOCATION --}}
    <script>
        function geoHandler() {
            return {
                statusMsg: '',
                isLocating: false, // State untuk kunci tombol
                
                getLocation() {
                    if (this.isLocating) return; // Cegah double click

                    if (!navigator.geolocation) {
                        alert("Browser Anda tidak mendukung GPS.");
                        return;
                    }

                    this.isLocating = true;
                    this.statusMsg = 'Sedang mencari titik koordinat GPS...';

                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                            this.statusMsg = 'Mengirim data lokasi ke server...';
                            @this.set('latitude', position.coords.latitude);
                            @this.set('longitude', position.coords.longitude);
                            @this.set('accuracy', position.coords.accuracy);
                            
                            // Panggil checkIn dan tunggu selesai baru buka kunci
                            @this.call('checkIn').then(() => {
                                this.statusMsg = '';
                                this.isLocating = false;
                            });
                        },
                        (error) => {
                            let msg = "Gagal mengambil lokasi.";
                            switch(error.code) {
                                case error.PERMISSION_DENIED: msg = "Izin lokasi ditolak. Harap aktifkan GPS."; break;
                                case error.POSITION_UNAVAILABLE: msg = "Sinyal lokasi tidak ditemukan."; break;
                                case error.TIMEOUT: msg = "Waktu habis (Timeout). Coba lagi di area terbuka."; break;
                            }
                            alert(msg);
                            this.statusMsg = '';
                            this.isLocating = false; // Buka kunci jika error
                        },
                        { 
                            enableHighAccuracy: true, 
                            timeout: 10000, 
                            maximumAge: 0 
                        }
                    );
                }
            }
        }
    </script>
</div>