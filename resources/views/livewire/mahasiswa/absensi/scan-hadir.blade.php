<div wire:poll.30s="cekJadwalBerlangsung" class="w-full max-w-xl mx-auto space-y-6 animate-in fade-in duration-700">
    
    {{-- Global Notifications --}}
    @if($notifMessage)
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" 
         class="fixed top-24 right-4 left-4 z-50 md:left-auto md:w-96 animate-in slide-in-from-right">
        <div class="rounded-2xl p-4 shadow-2xl flex items-start gap-4 border backdrop-blur-xl 
            {{ $notifType === 'error' ? 'bg-rose-500/90 text-white border-rose-400/50' : '' }}
            {{ $notifType === 'success' ? 'bg-emerald-500/90 text-white border-emerald-400/50' : '' }}
            {{ $notifType === 'warning' ? 'bg-amber-500/90 text-white border-amber-400/50' : '' }}
            {{ $notifType === 'info' ? 'bg-blue-500/90 text-white border-blue-400/50' : '' }}">
            <div class="shrink-0 pt-0.5">
                @if($notifType === 'error') <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                @elseif($notifType === 'success') <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                @else <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                @endif
            </div>
            <div class="flex-1">
                <p class="font-black text-xs uppercase tracking-widest">{{ $notifType }}</p>
                <p class="text-sm font-medium opacity-90 leading-snug">{{ $notifMessage }}</p>
            </div>
            <button @click="show = false" class="opacity-50 hover:opacity-100 transition-opacity"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
    </div>
    @endif

    {{-- KARTU UTAMA SESSION (Berubah tergantung Kuliah atau Ujian) --}}
    @if($jadwalAktif && $scanMode)
        <div class="relative overflow-hidden rounded-[2.5rem] {{ $scanMode === 'UJIAN' ? 'bg-[#1e1b4b]' : 'bg-[#002855]' }} text-white shadow-2xl border border-white/5 transition-colors duration-500">
            {{-- Aksen Latar Belakang --}}
            <div class="absolute -right-20 -top-20 h-64 w-64 rounded-full {{ $scanMode === 'UJIAN' ? 'bg-amber-500/20' : 'bg-blue-500/20' }} blur-3xl"></div>
            
            <div class="relative z-10 p-8">
                {{-- Status Bar --}}
                <div class="flex items-center justify-between mb-8">
                    <div class="px-3 py-1 bg-white/10 rounded-full border border-white/10 flex items-center gap-2">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full {{ $scanMode === 'UJIAN' ? 'bg-amber-400' : 'bg-emerald-400' }} opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 {{ $scanMode === 'UJIAN' ? 'bg-amber-500' : 'bg-emerald-500' }}"></span>
                        </span>
                        
                        @if($scanMode === 'KULIAH')
                            <span class="text-[9px] font-black uppercase tracking-[0.2em] text-white/80">Pertemuan {{ $sesiAktif->pertemuan_ke }} Aktif</span>
                        @elseif($scanMode === 'UJIAN')
                            <span class="text-[9px] font-black uppercase tracking-[0.2em] text-[#fcc000]">Ujian {{ $ujianAktif->jenis_ujian }} Aktif</span>
                        @endif
                    </div>
                    @if($sudahAbsen)
                    <div class="text-[10px] font-black text-emerald-400 bg-emerald-500/10 px-3 py-1 rounded-lg border border-emerald-500/20 uppercase tracking-widest italic">Hadir • {{ $waktuAbsen }}</div>
                    @endif
                </div>

                {{-- Course Detail --}}
                <div class="space-y-2 mb-10">
                    <h2 class="text-3xl font-black leading-none uppercase tracking-tighter italic">{{ $jadwalAktif->mataKuliah->nama_mk }}</h2>
                    <div class="flex flex-wrap items-center gap-x-4 gap-y-2 pt-2">
                        <div class="flex items-center gap-2 text-slate-300 text-xs font-bold uppercase tracking-wide">
                            <div class="w-6 h-6 rounded-full bg-white/10 flex items-center justify-center text-[10px] text-white">👨‍🏫</div>
                            {{ $jadwalAktif->dosens->where('pivot.is_koordinator', true)->first()->person->nama_lengkap ?? 'Dosen Pengampu' }}
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-10">
                    <div class="bg-white/5 rounded-2xl p-4 border border-white/5 backdrop-blur-sm">
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Lokasi Sesi</p>
                        @if($scanMode === 'KULIAH')
                            <p class="text-sm font-black uppercase tracking-tight">{{ $jadwalAktif->ruang->kode_ruang ?? 'ONLINE' }}</p>
                        @else
                            <p class="text-sm font-black uppercase tracking-tight text-[#fcc000]">{{ $ujianAktif->ruang->kode_ruang ?? 'ONLINE' }}</p>
                        @endif
                    </div>
                    <div class="bg-white/5 rounded-2xl p-4 border border-white/5 backdrop-blur-sm">
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Batas Waktu</p>
                        @if($scanMode === 'KULIAH')
                            <p class="text-sm font-black uppercase tracking-tight">{{ substr($jadwalAktif->jam_selesai, 0, 5) }} WITA</p>
                        @else
                            <p class="text-sm font-black uppercase tracking-tight text-[#fcc000]">{{ substr($ujianAktif->jam_selesai, 0, 5) }} WITA</p>
                        @endif
                    </div>
                </div>

                {{-- SCANNER / INPUT AREA --}}
                <div x-data="absensiHandler()" class="space-y-6">
                    @if(!$sudahAbsen)
                        {{-- Metode QR/TOKEN hanya untuk Kuliah --}}
                        @if($scanMode === 'KULIAH' && $sesiAktif->metode_validasi === 'QR')
                        <div class="space-y-3">
                            <input type="text" wire:model="inputToken" maxlength="6"
                                class="w-full text-center uppercase text-4xl font-black tracking-[0.4em] py-5 rounded-2xl border-2 border-white/20 bg-white/5 text-[#fcc000] focus:border-[#fcc000] transition-all outline-none placeholder:text-white/10" 
                                placeholder="******">
                            <p class="text-[9px] text-center text-slate-400 font-bold uppercase tracking-widest">Masukkan Kode Token Dari Dosen</p>
                        </div>
                        @endif

                        {{-- Button Action --}}
                        <button @click="doCheckIn()" 
                            :disabled="processing"
                            class="group relative w-full h-20 overflow-hidden rounded-2xl bg-white text-[#002855] shadow-2xl transition-all hover:scale-[1.02] active:scale-95 disabled:opacity-50 disabled:grayscale">
                            
                            <div class="relative z-10 flex items-center justify-center gap-4">
                                <div x-show="!processing" class="flex items-center gap-4">
                                    <span class="text-sm font-black uppercase tracking-[0.3em]">{{ $scanMode === 'UJIAN' ? 'Absen Ujian' : 'Konfirmasi Hadir' }}</span>
                                    <svg class="w-6 h-6 group-hover:translate-x-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                                </div>
                                <div x-show="processing" class="flex items-center gap-3">
                                    <svg class="w-6 h-6 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    <span class="text-sm font-black uppercase tracking-widest" x-text="statusMsg"></span>
                                </div>
                            </div>
                        </button>

                        <div class="flex items-center justify-center gap-4 pt-4 border-t border-white/5 opacity-50">
                            @if($scanMode === 'UJIAN')
                                <div class="flex items-center gap-1.5">
                                    <div class="w-1.5 h-1.5 rounded-full bg-[#fcc000]"></div>
                                    <span class="text-[8px] font-black uppercase tracking-widest text-[#fcc000]">Strict GPS Verification Required</span>
                                </div>
                            @else
                                <div class="flex items-center gap-1.5">
                                    <div class="w-1.5 h-1.5 rounded-full {{ $sesiAktif->metode_validasi == 'GPS' ? 'bg-emerald-400' : 'bg-slate-500' }}"></div>
                                    <span class="text-[8px] font-black uppercase tracking-widest">GPS Check</span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <div class="w-1.5 h-1.5 rounded-full {{ $sesiAktif->metode_validasi == 'QR' ? 'bg-[#fcc000]' : 'bg-slate-500' }}"></div>
                                    <span class="text-[8px] font-black uppercase tracking-widest">Token Auth</span>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="p-6 bg-emerald-500/10 border border-emerald-500/30 rounded-3xl flex flex-col items-center justify-center text-center space-y-3">
                            <div class="w-12 h-12 bg-emerald-500 rounded-2xl flex items-center justify-center shadow-lg shadow-emerald-500/40">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <div>
                                <h4 class="text-lg font-black uppercase tracking-tight">Presensi Tercatat</h4>
                                <p class="text-[10px] font-bold text-emerald-400/70 uppercase tracking-widest mt-1 italic">Sistem Verifikasi Selesai</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @else
        {{-- EMPTY STATE --}}
        <div class="py-24 px-10 text-center border-4 border-dashed border-slate-200 rounded-[3rem] bg-slate-50/50 flex flex-col items-center justify-center space-y-6">
            <div class="w-24 h-24 bg-white rounded-full flex items-center justify-center shadow-xl text-5xl grayscale opacity-30">⏳</div>
            <div>
                <h3 class="text-xl font-black text-slate-800 uppercase tracking-tight italic">Menunggu Jadwal Dibuka</h3>
                <p class="text-slate-400 text-xs font-medium mt-2 leading-relaxed max-w-xs mx-auto uppercase tracking-widest">
                    Belum ada sesi perkuliahan atau ujian aktif untuk jadwal Anda hari ini.
                </p>
            </div>
            <div class="pt-4">
                <span class="inline-flex items-center px-4 py-2 rounded-full bg-white border border-slate-200 shadow-sm text-[10px] font-black text-indigo-600 uppercase tracking-[0.2em] animate-pulse">
                    Monitoring Sesi...
                </span>
            </div>
        </div>
    @endif

    {{-- HISTORY LOG --}}
    @if(count($riwayatAbsensi) > 0)
    <div class="bg-white rounded-[2.5rem] border border-slate-200 p-8 shadow-sm">
        <div class="flex items-center justify-between mb-8">
            <h3 class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em]">Riwayat Kuliah Terakhir</h3>
            <button wire:click="cetakRekapan" class="px-4 py-2 bg-slate-50 text-slate-600 hover:bg-[#002855] hover:text-white rounded-xl text-[9px] font-black uppercase tracking-widest border border-slate-100 transition-all">Download Log</button>
        </div>

        <div class="space-y-4">
            @foreach($riwayatAbsensi as $row)
            <div class="flex items-center justify-between p-4 rounded-2xl bg-slate-50/50 border border-slate-100 group hover:bg-white hover:border-[#002855]/20 transition-all">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center font-black text-[10px] uppercase shadow-inner
                        {{ $row->status_kehadiran == 'H' ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }}">
                        {{ $row->status_kehadiran }}
                    </div>
                    <div>
                        <p class="text-xs font-black text-slate-700 uppercase tracking-tight italic">Pertemuan {{ $row->sesi->pertemuan_ke }}</p>
                        <p class="text-[9px] font-bold text-slate-400 uppercase">{{ $row->waktu_check_in ? $row->waktu_check_in->timezone('Asia/Makassar')->isoFormat('D MMM Y • HH:mm') : 'Manual Update' }}</p>
                    </div>
                </div>
                <div class="text-right">
                    <span class="text-[8px] font-black text-slate-300 uppercase tracking-widest border border-slate-200 px-2 py-0.5 rounded-lg group-hover:border-[#002855]/20 group-hover:text-[#002855] transition-all">
                        {{ $row->bukti_validasi['method'] ?? 'SYS' }}
                    </span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <script>
        function absensiHandler() {
            return {
                processing: false,
                statusMsg: 'Memproses...',
                
                async doCheckIn() {
                    this.processing = true;
                    this.statusMsg = 'Mengunci Sesi...';

                    // Cek Metode: Ujian selalu memaksa GPS. Jika Kuliah, cek metodenya.
                    let method = '{{ $scanMode === "KULIAH" ? ($sesiAktif->metode_validasi ?? "GPS") : "GPS" }}';

                    if (method === 'GPS') {
                        this.statusMsg = 'Mencari Koordinat...';
                        if (!navigator.geolocation) {
                            alert("Perangkat Anda tidak mendukung GPS.");
                            this.processing = false;
                            return;
                        }

                        navigator.geolocation.getCurrentPosition(
                            (pos) => {
                                this.statusMsg = 'Memverifikasi Lokasi...';
                                @this.set('latitude', pos.coords.latitude);
                                @this.set('longitude', pos.coords.longitude);
                                @this.set('accuracy', pos.coords.accuracy);
                                
                                @this.call('checkIn').then(() => {
                                    this.processing = false;
                                });
                            },
                            (err) => {
                                alert("Gagal mendapatkan lokasi. Harap aktifkan/berikan izin GPS pada browser Anda.");
                                this.processing = false;
                            },
                            { enableHighAccuracy: true, timeout: 10000 }
                        );
                    } else {
                        // Metode QR atau DARING (Tanpa GPS)
                        this.statusMsg = 'Validasi Data...';
                        @this.call('checkIn').then(() => {
                            this.processing = false;
                        });
                    }
                }
            }
        }
    </script>
</div>