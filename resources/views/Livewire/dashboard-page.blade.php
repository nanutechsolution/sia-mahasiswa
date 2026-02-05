<div class="space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700">

    {{-- 1. WELCOME HERO SECTION --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-unmaris-blue to-indigo-900 shadow-xl">
        <div class="absolute top-0 right-0 -mr-16 -mt-16 h-64 w-64 rounded-full bg-white/5 blur-3xl"></div>
        <div class="absolute bottom-0 left-0 -ml-16 -mb-16 h-64 w-64 rounded-full bg-unmaris-gold/10 blur-3xl"></div>

        <div class="relative px-8 py-10 sm:px-12 sm:py-12 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
            <div>
                <div class="inline-flex items-center rounded-full bg-white/10 px-3 py-1 text-xs font-semibold text-indigo-100 backdrop-blur-sm border border-white/10 mb-4">
                    <span>{{ $taAktif->nama_tahun ?? 'Semester Belum Aktif' }}</span>
                </div>
                <h1 class="text-3xl font-black text-white tracking-tight sm:text-4xl">
                    {{ $greeting }}, {{ explode(' ', $user->name)[0] }}! 👋
                </h1>
                <p class="mt-2 text-indigo-100 text-sm md:text-base max-w-xl leading-relaxed">
                    Selamat datang di Sistem Informasi Akademik Terpadu UNMARIS.
                </p>
            </div>
            <div class="hidden md:block">
                <div class="h-16 w-16 bg-white/10 rounded-2xl flex items-center justify-center backdrop-blur-md border border-white/20 text-3xl">
                    🎓
                </div>
            </div>
        </div>
    </div>

    {{-- Announcements (Error Profil) --}}
    @foreach($announcements as $ann)
    <div class="p-4 mb-4 text-sm text-red-800 rounded-2xl bg-red-50 border border-red-100 flex items-center" role="alert">
        <svg class="flex-shrink-0 inline w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
        </svg>
        <div>
            <span class="font-bold">{{ $ann['title'] }}:</span> {{ $ann['message'] }}
        </div>
    </div>
    @endforeach

    {{-- A. DASHBOARD MAHASISWA --}}
    @if($role == 'mahasiswa')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Statistik Cards --}}
        <div class="lg:col-span-3 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- IPK Card -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-4">
                <div class="p-3 bg-indigo-50 rounded-xl text-indigo-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">IPK Saat Ini</p>
                    <p class="text-2xl font-black text-slate-800">{{ number_format($stats['ipk'] ?? 0, 2) }}</p>
                </div>
            </div>

            <!-- SKS Card -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-4">
                <div class="p-3 bg-emerald-50 rounded-xl text-emerald-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total SKS</p>
                    <p class="text-2xl font-black text-slate-800">{{ $stats['sks_total'] ?? 0 }}</p>
                </div>
            </div>

            <!-- Keuangan Card -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-4 col-span-1 sm:col-span-2">
                <div class="p-3 {{ ($stats['status_bayar'] ?? '') == 'LUNAS' ? 'bg-green-50 text-green-600' : 'bg-rose-50 text-rose-600' }} rounded-xl">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Status Keuangan</p>
                    <div class="flex justify-between items-end">
                        <p class="text-xl font-black text-slate-800">{{ $stats['status_bayar'] ?? '-' }}</p>
                        <p class="text-xs font-medium {{ ($stats['sisa_tagihan'] ?? 0) > 0 ? 'text-rose-500' : 'text-green-500' }}">
                            Sisa: Rp {{ number_format($stats['sisa_tagihan'] ?? 0, 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Jadwal Hari Ini --}}
        <div class="lg:col-span-2 bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <h3 class="font-bold text-slate-800">Jadwal Kuliah Hari Ini</h3>
                <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-2 py-1 rounded">{{ count($scheduleToday) }} Kelas</span>
            </div>
            <div class="divide-y divide-slate-50">
                @forelse($scheduleToday as $detail)
                <div class="px-6 py-4 flex items-center group hover:bg-slate-50 transition-colors">
                    <div class="w-16 text-center mr-4">
                        <span class="block text-sm font-black text-slate-800">{{ \Carbon\Carbon::parse($detail->jadwalKuliah->jam_mulai)->format('H:i') }}</span>
                        <span class="block text-[10px] font-bold text-slate-400 uppercase">WITA</span>
                    </div>
                    <div class="flex-1 border-l-2 border-slate-100 pl-4">
                        <h4 class="text-sm font-bold text-indigo-900 group-hover:text-indigo-600 transition-colors">
                            {{ $detail->jadwalKuliah->mataKuliah->nama_mk }}
                        </h4>
                        <p class="text-xs text-slate-500 mt-1">
                            R. {{ $detail->jadwalKuliah->ruang }} • {{ $detail->jadwalKuliah->dosen->nama_lengkap_gelar ?? 'Dosen' }}
                        </p>
                    </div>
                    <div>
                        <span class="px-2 py-1 bg-slate-100 text-slate-500 text-[10px] font-bold uppercase rounded">
                            Kls {{ $detail->jadwalKuliah->nama_kelas }}
                        </span>
                    </div>
                </div>
                @empty
                <div class="px-6 py-12 text-center">
                    <p class="text-slate-400 text-sm italic">Tidak ada jadwal kuliah hari ini. Istirahat yang cukup! ☕</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Quick Links --}}
        <div class="bg-indigo-900 rounded-3xl shadow-xl p-6 text-white flex flex-col justify-between relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full blur-2xl -mr-10 -mt-10"></div>
            <div class="relative z-10">
                <h3 class="font-bold text-lg mb-1">Akses Cepat</h3>
                <p class="text-indigo-200 text-xs mb-6">Pintasan menu sering digunakan.</p>

                <div class="space-y-3">
                    <a href="{{ route('mhs.krs') }}" class="flex items-center p-3 bg-white/10 rounded-xl hover:bg-white/20 transition-all border border-white/5">
                        <span class="text-xl mr-3">📝</span>
                        <span class="text-sm font-bold">Isi KRS Online</span>
                    </a>
                    <a href="{{ route('mhs.khs') }}" class="flex items-center p-3 bg-white/10 rounded-xl hover:bg-white/20 transition-all border border-white/5">
                        <span class="text-xl mr-3">📊</span>
                        <span class="text-sm font-bold">Lihat Nilai (KHS)</span>
                    </a>
                    <a href="{{ route('mhs.keuangan') }}" class="flex items-center p-3 bg-white/10 rounded-xl hover:bg-white/20 transition-all border border-white/5">
                        <span class="text-xl mr-3">💳</span>
                        <span class="text-sm font-bold">Bayar Tagihan</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- B. DASHBOARD DOSEN --}}
    @elseif($role == 'dosen')
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        {{-- Stats --}}
        <div class="lg:col-span-4 grid grid-cols-1 sm:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                <p class="text-xs font-bold text-slate-400 uppercase">Kelas Semester Ini</p>
                <p class="text-3xl font-black text-indigo-600 mt-2">{{ $stats['kelas_ajar'] ?? 0 }}</p>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                <p class="text-xs font-bold text-slate-400 uppercase">Mahasiswa Bimbingan</p>
                <p class="text-3xl font-black text-emerald-600 mt-2">{{ $stats['mhs_wali'] ?? 0 }}</p>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 relative overflow-hidden">
                @if(($stats['krs_perlu_acc'] ?? 0) > 0)
                <div class="absolute top-0 right-0 w-3 h-3 bg-rose-500 rounded-full animate-ping mt-4 mr-4"></div>
                @endif
                <p class="text-xs font-bold text-slate-400 uppercase">KRS Menunggu Approval</p>
                <p class="text-3xl font-black text-rose-600 mt-2">{{ $stats['krs_perlu_acc'] ?? 0 }}</p>
                @if(($stats['krs_perlu_acc'] ?? 0) > 0)
                <a href="{{ route('dosen.perwalian') }}" class="absolute bottom-4 right-4 text-xs font-bold text-indigo-600 hover:underline">Review Sekarang &rarr;</a>
                @endif
            </div>
        </div>

        {{-- Jadwal Mengajar --}}
        <div class="lg:col-span-3 bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50">
                <h3 class="font-bold text-slate-800">Jadwal Mengajar Hari Ini</h3>
            </div>
            <div class="divide-y divide-slate-50">
                @forelse($scheduleToday as $jadwal)
                <div class="px-6 py-4 flex items-center justify-between group hover:bg-slate-50">
                    <div class="flex items-center gap-4">
                        <div class="text-center w-14">
                            <span class="block text-sm font-black text-slate-800">{{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }}</span>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-indigo-900">{{ $jadwal->mataKuliah->nama_mk }}</h4>
                            <p class="text-xs text-slate-500">Kelas {{ $jadwal->nama_kelas }} • R. {{ $jadwal->ruang }}</p>
                        </div>
                    </div>
                    <a href="{{ route('dosen.nilai', $jadwal->id) }}" class="px-4 py-2 bg-indigo-50 text-indigo-700 text-xs font-bold rounded-lg hover:bg-indigo-100 transition-colors">
                        Input Nilai
                    </a>
                </div>
                @empty
                <div class="px-6 py-12 text-center text-slate-400 italic text-sm">Tidak ada jadwal mengajar hari ini.</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- C. DASHBOARD ADMIN --}}
    @else
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
            <p class="text-xs font-bold text-slate-400 uppercase">Mahasiswa Aktif</p>
            <p class="text-2xl font-black text-slate-800 mt-2">{{ $stats['total_mhs_aktif'] ?? 0 }}</p>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
            <p class="text-xs font-bold text-slate-400 uppercase">Pembayaran Pending</p>
            <p class="text-2xl font-black text-amber-500 mt-2">{{ $stats['pembayaran_pending'] ?? 0 }}</p>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
            <p class="text-xs font-bold text-slate-400 uppercase">Kelas Terbuka</p>
            <p class="text-2xl font-black text-indigo-600 mt-2">{{ $stats['kelas_aktif'] ?? 0 }}</p>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
            <p class="text-xs font-bold text-slate-400 uppercase">User Online</p>
            <p class="text-2xl font-black text-emerald-500 mt-2">{{ $stats['user_online'] ?? 0 }}</p>
        </div>
    </div>

    {{-- Todo List Admin --}}
    @if(count($todoList) > 0)
    <div class="mt-8">
        <h3 class="text-lg font-bold text-slate-800 mb-4">Perlu Tindakan Segera</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($todoList as $todo)
            <a href="{{ route($todo['route']) }}" class="flex items-center p-4 bg-white border border-slate-200 rounded-xl shadow-sm hover:shadow-md transition-shadow group">
                <div class="h-10 w-10 rounded-full {{ $todo['color'] }} flex items-center justify-center font-bold text-sm mr-4">
                    {{ $todo['count'] }}
                </div>
                <div>
                    <h4 class="text-sm font-bold text-slate-800 group-hover:text-indigo-600 transition-colors">{{ $todo['title'] }}</h4>
                    <p class="text-xs text-slate-500">Klik untuk memproses</p>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif
    @endif
</div>