<div class="space-y-8 animate-in fade-in duration-700 pb-12">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div class="space-y-2">
            <h1 class="text-3xl font-black text-[#002855] tracking-tight flex items-center gap-3">
                <div class="w-12 h-12 bg-[#002855] rounded-2xl flex items-center justify-center text-white shadow-xl shadow-blue-900/20">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                Live Class Manager
            </h1>
            <p class="text-slate-400 font-bold text-sm ml-1 uppercase tracking-widest">{{ \Carbon\Carbon::now('Asia/Makassar')->isoFormat('dddd, D MMMM Y') }} &bull; WITA</p>
        </div>
        <div class="flex items-center gap-3">
            <button wire:click="$refresh" class="p-4 bg-white border border-slate-200 text-slate-400 hover:text-[#002855] rounded-2xl shadow-sm transition-all hover:rotate-180 duration-500">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
            </button>
            <div class="bg-indigo-50 text-indigo-700 px-6 py-4 rounded-[1.5rem] text-[10px] font-black uppercase tracking-[0.2em] border border-indigo-100 shadow-sm">
                Semester {{ $globalTa->nama_tahun ?? 'Aktif' }}
            </div>
        </div>
    </div>

    {{-- Grid Jadwal Hari Ini --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
        @forelse($jadwalHariIni as $jadwal)
        <div class="bg-white rounded-[2.5rem] border border-slate-200 shadow-sm overflow-hidden group hover:shadow-2xl hover:border-indigo-100 transition-all duration-500 flex flex-col relative">
            
            {{-- Top Info Bar --}}
            <div class="px-8 py-6 border-b border-slate-50 flex justify-between items-center bg-white z-10">
                <div class="flex items-center gap-3">
                    <span class="px-3 py-1 bg-slate-100 text-slate-500 text-[9px] font-black rounded-lg uppercase tracking-widest">{{ $jadwal->mataKuliah->kode_mk }}</span>
                    <span class="px-3 py-1 bg-amber-50 text-amber-600 text-[9px] font-black rounded-lg uppercase tracking-widest">Kls {{ $jadwal->nama_kelas }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <button wire:click="cetakRekap('{{ $jadwal->id }}')" class="p-2 text-slate-300 hover:text-indigo-600 transition-colors" title="Rekap Absensi">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                    </button>
                </div>
            </div>

            {{-- Main Content --}}
            <div class="p-8 space-y-6 flex-1">
                <div>
                    <h3 class="text-xl font-black text-[#002855] uppercase tracking-tight leading-tight mb-2 italic">{{ $jadwal->mataKuliah->nama_mk }}</h3>
                    <div class="flex items-center gap-4 text-xs font-bold text-slate-400">
                        <span class="flex items-center gap-1.5"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>{{ substr($jadwal->jam_mulai,0,5) }} - {{ substr($jadwal->jam_selesai,0,5) }}</span>
                        <span class="flex items-center gap-1.5"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2"/></svg>R. {{ $jadwal->ruang->kode_ruang ?? 'TBA' }}</span>
                    </div>
                </div>

                {{-- Status Logic --}}
                <div class="pt-6 border-t border-slate-50">
                    @if($jadwal->sesiAktif)
                        <div class="bg-emerald-50 rounded-3xl p-6 border border-emerald-100 space-y-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <span class="relative flex h-3 w-3">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                                    </span>
                                    <span class="text-xs font-black text-emerald-700 uppercase tracking-widest">Pertemuan {{ $jadwal->sesiAktif->pertemuan_ke }} Sedang Aktif</span>
                                </div>
                                <button wire:click="tutupSesi('{{ $jadwal->sesiAktif->id }}')" wire:confirm="Tutup kelas sekarang?" class="px-4 py-2 bg-white text-rose-600 rounded-xl text-[10px] font-black uppercase tracking-widest shadow-sm hover:bg-rose-50 transition-all">Tutup Sesi</button>
                            </div>

                            {{-- Stats --}}
                            <div class="space-y-2">
                                <div class="flex justify-between items-end">
                                    <span class="text-[10px] font-black text-emerald-600/70 uppercase">Kehadiran Mahasiswa</span>
                                    <span class="text-sm font-black text-emerald-800 italic">{{ $jadwal->sesiAktif->absensi_count }} / {{ $jadwal->jumlah_peserta }} <span class="text-[10px] not-italic opacity-50">Mhs</span></span>
                                </div>
                                <div class="w-full bg-emerald-200/50 h-2 rounded-full overflow-hidden">
                                    @php $pct = $jadwal->jumlah_peserta > 0 ? ($jadwal->sesiAktif->absensi_count / $jadwal->jumlah_peserta * 100) : 0; @endphp
                                    <div class="bg-emerald-600 h-full transition-all duration-1000" style="width: {{ $pct }}%"></div>
                                </div>
                            </div>

                            <div class="flex gap-2 pt-2">
                                <button wire:click="bukaDetailPresensi('{{ $jadwal->sesiAktif->id }}')" class="flex-1 py-3 bg-[#002855] text-white rounded-2xl text-[9px] font-black uppercase tracking-widest hover:scale-[1.02] transition-all shadow-lg shadow-blue-900/10">Lihat Daftar Hadir</button>
                                @if($jadwal->sesiAktif->metode_validasi == 'QR')
                                    <div class="px-4 py-3 bg-white text-[#002855] rounded-2xl font-mono text-lg font-black flex items-center justify-center border border-emerald-100 shadow-sm">{{ $jadwal->sesiAktif->token_sesi }}</div>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center py-8 text-center bg-slate-50/50 rounded-3xl border border-dashed border-slate-200">
                            <p class="text-[10px] font-black text-slate-300 uppercase tracking-widest mb-4">Kelas Belum Dimulai</p>
                            @if($jadwal->jumlah_peserta > 0)
                                <button wire:click="openModalBuka('{{ $jadwal->id }}')" class="px-8 py-3 bg-[#002855] text-white rounded-[1.2rem] text-[10px] font-black uppercase tracking-[0.2em] shadow-xl shadow-blue-900/20 hover:-translate-y-1 active:scale-95 transition-all">Buka Kelas Sekarang</button>
                            @else
                                <div class="px-6 py-2 bg-rose-50 text-rose-500 rounded-xl text-[9px] font-black uppercase tracking-widest border border-rose-100 italic">0 Peserta Tervalidasi</div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            {{-- Team Teaching Info --}}
            <div class="px-8 py-4 bg-slate-50/30 flex items-center gap-3">
                <p class="text-[8px] font-black text-slate-300 uppercase tracking-widest">Tim:</p>
                <div class="flex -space-x-2">
                    @foreach($jadwal->dosens as $d)
                        <div class="w-6 h-6 rounded-full bg-white border border-slate-200 flex items-center justify-center text-[8px] font-black text-[#002855] uppercase shadow-sm" title="{{ $d->person->nama_lengkap }}">
                            {{ substr($d->person->nama_lengkap, 0, 1) }}
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @empty
        <div class="xl:col-span-2 py-32 text-center bg-white rounded-[3rem] border-2 border-dashed border-slate-100">
            <div class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6 shadow-inner text-4xl grayscale opacity-20">☕</div>
            <h3 class="text-sm font-black text-slate-400 uppercase tracking-[0.3em]">Tidak Ada Jadwal Mengajar Hari Ini</h3>
            <p class="text-slate-300 text-[10px] font-bold mt-2 uppercase tracking-widest">Nikmati waktu istirahat Anda.</p>
        </div>
        @endforelse
    </div>

    {{-- MODAL BUKA KELAS --}}
    @if($isModalOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-6 bg-slate-900/60 backdrop-blur-sm animate-in fade-in duration-300">
        <div class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-lg overflow-hidden flex flex-col animate-in zoom-in-95 duration-300">
            <div class="p-8 border-b border-slate-50 bg-slate-50/50">
                <h3 class="text-2xl font-black text-[#002855] uppercase tracking-tight italic">Persiapan Sesi</h3>
                <p class="text-[10px] font-bold text-slate-400 mt-1 uppercase tracking-widest">Konfigurasi Pertemuan Perkuliahan</p>
            </div>
            
            <div class="p-8 space-y-8">
                <div class="grid grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Pertemuan Ke-</label>
                        <input type="number" wire:model="pertemuan_ke" class="w-full rounded-2xl border-slate-200 bg-slate-50 p-4 font-black text-[#002855] text-center text-xl">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Metode Absen</label>
                        <select wire:model.live="metode_validasi" class="w-full rounded-2xl border-slate-200 bg-slate-50 p-4 font-black text-slate-700 uppercase text-[10px] tracking-widest">
                            <option value="GPS">📍 GPS Validated</option>
                            <option value="QR">🔑 Token / QR</option>
                            <option value="DARING">🌐 Online / Zoom</option>
                            <option value="MANUAL">✍️ Manual Only</option>
                        </select>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Pokok Bahasan / Materi</label>
                    <textarea wire:model="materi_kuliah" rows="4" class="w-full rounded-[1.5rem] border-slate-200 bg-slate-50 p-5 text-sm font-medium focus:ring-[#002855] transition-all resize-none" placeholder="Masukkan ringkasan materi hari ini..."></textarea>
                    @error('materi_kuliah') <span class="text-[10px] text-rose-500 font-bold uppercase tracking-widest ml-1">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="p-8 bg-slate-50 border-t border-slate-100 flex justify-end gap-4">
                <button wire:click="$set('isModalOpen', false)" class="px-8 py-4 bg-white text-slate-400 rounded-2xl text-[10px] font-black uppercase tracking-widest border border-slate-200 hover:bg-slate-100 transition-all">Batal</button>
                <button wire:click="bukaSesi" class="px-10 py-4 bg-[#002855] text-white rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] shadow-xl shadow-blue-900/20 hover:scale-105 active:scale-95 transition-all">Buka Kelas</button>
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL DETAIL DAFTAR HADIR --}}
    @if($isDetailOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 md:p-10 bg-slate-900/80 backdrop-blur-md animate-in fade-in duration-300">
        <div class="bg-white w-full max-w-5xl h-[90vh] rounded-[3rem] shadow-2xl flex flex-col overflow-hidden animate-in slide-in-from-bottom-10 duration-500">
            <div class="p-8 md:p-10 bg-[#002855] text-white flex flex-col md:flex-row justify-between items-center gap-6 shrink-0 relative">
                <div class="absolute top-0 right-0 p-10 opacity-10"><svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg></div>
                
                <div class="relative z-10">
                    <h3 class="text-3xl font-black uppercase tracking-tight italic">Log Presensi Kehadiran</h3>
                    <div class="flex items-center gap-3 mt-3">
                        <span class="px-3 py-1 bg-white/10 rounded-lg text-[10px] font-black uppercase tracking-[0.2em] border border-white/10">Pertemuan {{ $detailSesi->pertemuan_ke }}</span>
                        <span class="text-indigo-200 text-sm font-bold">{{ $detailSesi->jadwalKuliah->mataKuliah->nama_mk }}</span>
                    </div>
                </div>

                <div class="flex gap-3 relative z-10">
                    <button wire:click="cetakPresensi('{{ $detailSesi->id }}')" class="px-6 py-3 bg-[#fcc000] text-[#002855] rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-xl shadow-amber-500/20 hover:scale-105 transition-all">Cetak PDF</button>
                    <button wire:click="tutupDetailPresensi" class="p-3 bg-white/10 text-white rounded-2xl hover:bg-white/20 transition-all"><svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M6 18L18 6M6 6l12 12" /></svg></button>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto custom-scrollbar p-8 bg-slate-50">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    @foreach(['H' => 'Hadir', 'I' => 'Izin', 'S' => 'Sakit', 'A' => 'Alpha'] as $key => $label)
                    <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 flex items-center justify-between">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ $label }}</span>
                        <span class="text-2xl font-black {{ $key == 'H' ? 'text-emerald-500' : ($key == 'A' ? 'text-rose-500' : 'text-indigo-500') }}">{{ collect($daftarPeserta)->where('status', $key)->count() }}</span>
                    </div>
                    @endforeach
                </div>

                <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
                    <table class="min-w-full divide-y divide-slate-100">
                        <thead class="bg-slate-50/50">
                            <tr>
                                <th class="px-8 py-5 text-left text-[9px] font-black text-slate-400 uppercase tracking-widest">Identitas Mahasiswa</th>
                                <th class="px-6 py-5 text-center text-[9px] font-black text-slate-400 uppercase tracking-widest">Check-in</th>
                                <th class="px-8 py-5 text-right text-[9px] font-black text-slate-400 uppercase tracking-widest">Update Manual</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach($daftarPeserta as $mhs)
                            <tr class="hover:bg-slate-50 transition-all group">
                                <td class="px-8 py-5 flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center font-black text-[#002855] text-xs uppercase shadow-inner">{{ substr($mhs['nama'], 0, 1) }}</div>
                                    <div>
                                        <p class="text-sm font-black text-slate-800 uppercase tracking-tighter">{{ $mhs['nama'] }}</p>
                                        <p class="text-[10px] font-mono font-bold text-slate-400 mt-0.5">{{ $mhs['nim'] }}</p>
                                    </div>
                                </td>
                                <td class="px-6 py-5 text-center">
                                    <span class="text-[10px] font-black {{ $mhs['waktu'] != '-' ? 'text-emerald-500' : 'text-slate-300 italic' }}">{{ $mhs['waktu'] }}</span>
                                </td>
                                <td class="px-8 py-5">
                                    <div class="flex items-center justify-end gap-1.5">
                                        @foreach(['H', 'I', 'S', 'A'] as $st)
                                        <button wire:click="updateStatusManual('{{ $mhs['krs_detail_id'] }}', '{{ $st }}')" 
                                            class="w-8 h-8 rounded-lg text-[10px] font-black transition-all {{ $mhs['status'] == $st ? 
                                                ($st == 'H' ? 'bg-emerald-500 text-white shadow-lg' : ($st == 'A' ? 'bg-rose-500 text-white shadow-lg' : 'bg-indigo-500 text-white shadow-lg')) 
                                                : 'bg-slate-100 text-slate-400 hover:bg-slate-200' }}">
                                            {{ $st }}
                                        </button>
                                        @endforeach
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif

    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(0, 40, 85, 0.1); border-radius: 10px; }
    </style>
</div>