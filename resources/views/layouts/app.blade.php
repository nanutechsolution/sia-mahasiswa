<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'SIAKAD UNMARIS' }}</title>
    <!-- icon -->
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet">
</head>

<body class="bg-slate-100 font-sans antialiased text-slate-800" x-data="{ sidebarOpen: false }">
    <div class="flex h-screen overflow-hidden">
        {{-- MOBILE SIDEBAR BACKDROP --}}
        <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 z-40 bg-slate-900/80 backdrop-blur-sm md:hidden" x-cloak x-transition></div>
        {{-- SIDEBAR --}}
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-50 w-72 bg-unmaris-blue text-white shadow-2xl transition-transform duration-300 ease-in-out md:static md:translate-x-0 flex flex-col h-full border-r border-unmaris-dark">
            <div class="h-20 flex items-center px-6 bg-unmaris-dark border-b border-white/5 relative z-10 shadow-md flex-shrink-0">
                <div class="flex items-center gap-3">
                    <div class="bg-white p-1.5 rounded-xl shadow-lg">
                        <img src="{{ asset('logo.png') }}" alt="UNMARIS" class="w-8 h-8 object-contain">
                    </div>
                    <div class="flex flex-col">
                        <span class="text-lg font-black tracking-wide leading-none text-white">UNMARIS</span>
                        <span class="text-[9px] font-bold text-unmaris-gold uppercase tracking-[0.2em] mt-0.5">Siakad v2.0</span>
                    </div>
                </div>
                <button @click="sidebarOpen = false" class="md:hidden absolute right-4 text-slate-400 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            @php
            $user = Auth::user();
            $role = $user->role;

            // Grup rute untuk status 'active' pada Master Data
            $masterRoutes = [
            'admin.master.*', 'admin.matakuliah', 'admin.kurikulum*',
            'admin.skala-nilai', 'admin.aturan-sks', 'admin.akademik.ekuivalensi-mk',
            'admin.perbaikan-nilai'
            ];
            $isMasterActive = request()->routeIs($masterRoutes);

            /**
            * MASTER DATA SUBMENU
            */
            $masterSubmenus = [
            ['route' => 'admin.master.fakultas', 'label' => 'Fakultas'],
            ['route' => 'admin.master.prodi', 'label' => 'Program Studi'],
            ['route' => 'admin.master.ruang', 'label' => 'Data Ruangan'],
            ['route' => 'admin.master.program-kelas', 'label' => 'Program Kelas'],
            ['route' => 'admin.matakuliah', 'label' => 'Mata Kuliah'],
            ['route' => 'admin.kurikulum', 'label' => 'Kurikulum', 'pattern' => 'admin.kurikulum*'],
            ['route' => 'admin.skala-nilai', 'label' => 'Skala Nilai'],
            ['route' => 'admin.aturan-sks', 'label' => 'Aturan SKS'],
            ['route' => 'admin.akademik.ekuivalensi-mk', 'label' => 'Ekuivalensi MK'],
            ['route' => 'admin.perbaikan-nilai', 'label' => 'Perbaikan Nilai'],
            ];

            /**
            * DEFINISI SEKSI ADMIN & STAFF
            */
            $adminSections = [
            [
            'title' => 'Administrasi Akademik',
            'permission' => 'akses_modul_akademik',
            'menus' => [
            ['label' => 'Tahun Akademik', 'route' => 'admin.semester', 'icon' => 'calendar-days'],
            ['label' => 'Komponen Nilai', 'route' => 'admin.komponen-nilai', 'icon' => 'chart-bar-square'],
            // Master Data disisipkan secara manual di loop bawah
            ['label' => 'Jadwal Kuliah', 'route' => 'admin.jadwal', 'icon' => 'clock'],
            ['label' => 'Jadwal Ujian', 'route' => 'admin.jadwal-ujian', 'icon' => 'document-check'],
            ['label' => 'Cetak Absensi', 'route' => 'admin.cetak.absensi.manager', 'icon' => 'printer', 'pattern' => 'admin.cetak.absensi*'],
            ]
            ],
            [
            'title' => 'Operasional',
            'permission' => 'akses_modul_akademik',
            'menus' => [
            ['label' => 'Plotting PA', 'route' => 'admin.ploting-pa', 'icon' => 'user-group'],
            ['label' => 'Ploting Kurikulum', 'route' => 'admin.ploting.kurikulum', 'icon' => 'academic-cap'],
            ['label' => 'Mutasi Mahasiswa', 'route' => 'admin.akademik.mutasi', 'icon' => 'arrows-right-left'],
            ['label' => 'Import Nilai Historis', 'route' => 'admin.akademik.import-nilai', 'icon' => 'arrow-up-tray'],
            ['label' => 'HR & Pejabat', 'route' => 'admin.hr.manager', 'icon' => 'identification'],
            ]
            ],
            [
            'title' => 'Manajemen User',
            'permission' => 'akses_modul_akademik',
            'menus' => [
            ['label' => 'PMB & Daftar Ulang', 'route' => 'admin.camaba', 'icon' => 'user-plus'],
            ['label' => 'Data Mahasiswa', 'route' => 'admin.mahasiswa', 'icon' => 'users'],
            ['label' => 'Data Dosen', 'route' => 'admin.dosen', 'icon' => 'briefcase'],
            ]
            ],
            [
            'title' => 'Administrasi Keuangan',
            'permission' => 'akses_modul_keuangan',
            'menus' => [
            ['label' => 'Verifikasi Bayar', 'route' => 'admin.keuangan', 'icon' => 'check-badge'],
            ['label' => 'Komponen Biaya', 'route' => 'admin.keuangan.komponen', 'icon' => 'tag'],
            ['label' => 'Skema Tarif', 'route' => 'admin.keuangan.skema', 'icon' => 'calculator'],
            ['label' => 'Generator Tagihan', 'route' => 'admin.tagihan-generator', 'icon' => 'bolt'],
            ['label' => 'Tagihan Manual', 'route' => 'admin.keuangan.manual', 'icon' => 'pencil-square'],
            ['label' => 'Import Tagihan', 'route' => 'admin.keuangan.import-tagihan', 'icon' => 'document-arrow-up'],
            ['label' => 'Koreksi & Saldo', 'route' => 'admin.keuangan.adjustment', 'icon' => 'adjustments-horizontal'],
            ['label' => 'Laporan & Audit', 'route' => 'admin.keuangan.laporan', 'icon' => 'document-chart-bar'],
            ]
            ],
            [
            'title' => 'Penjaminan Mutu (SPMI)',
            'permission' => 'akses_modul_lpm',
            'role_exception' => 'lpm',
            'menus' => [
            ['label' => 'Command Center', 'route' => 'admin.lpm.dashboard', 'icon' => 'shield-check'],
            ['label' => 'Standar Mutu', 'route' => 'admin.lpm.standar', 'icon' => 'clipboard-document-check', 'pattern' => 'admin.lpm.standar*'],
            ['label' => 'Audit (AMI)', 'route' => 'admin.lpm.ami', 'icon' => 'eye', 'pattern' => 'admin.lpm.ami*'],
            ['label' => 'Dokumen Mutu', 'route' => 'admin.lpm.dokumen', 'icon' => 'folder-open', 'pattern' => 'admin.lpm.dokumen*'],
            ]
            ],
            [
            'title' => 'Monev & Akreditasi',
            'permission' => 'akses_modul_lpm',
            'role_exception' => 'lpm',
            'menus' => [
            ['label' => 'Evaluasi Dosen', 'route' => 'admin.lpm.edom.index', 'icon' => 'star', 'pattern' => 'admin.lpm.edom.index*'],
            ['label' => 'IKU & Akreditasi', 'route' => '#', 'icon' => 'presentation-chart-line'],
            ['label' => 'Kuesioner', 'route' => 'admin.lpm.edom.setup', 'icon' => 'list-bullet', 'pattern' => 'admin.lpm.edom.setup*'],
            ['label' => 'Indikator', 'route' => 'admin.lpm.indikator', 'icon' => 'variable', 'pattern' => 'admin.lpm.indikator*'],
            ]
            ],
            [
            'title' => 'System & IT',
            'permission' => 'superadmin',
            'menus' => [
            ['label' => 'User Management', 'route' => 'admin.users', 'icon' => 'cog-6-tooth'],
            ['label' => 'Audit Logs', 'route' => 'admin.audit', 'icon' => 'command-line'],
            ['label' => 'Roles & Akses', 'route' => 'admin.roles', 'icon' => 'lock-closed'],
            ]
            ]
            ];
            @endphp

            <nav class="flex-1 overflow-y-auto px-4 py-4 space-y-7 sidebar-scroll">

                {{-- 1. Dashboard --}}
                <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" icon="squares-2x2">
                    Dashboard
                </x-nav-link>

                {{-- 2. Render Admin & Staff Sections --}}
                @if(in_array($role, ['superadmin', 'admin', 'bara', 'bauk', 'lpm']))
                @foreach($adminSections as $section)
                @if($user->can($section['permission']) || $role === 'superadmin' || ($role === ($section['role_exception'] ?? '')))
                <div class="space-y-1">
                    <p class="px-4 text-[10px] font-bold text-slate-500 uppercase tracking-[0.2em] mb-3 mt-2">{{ $section['title'] }}</p>

                    @foreach($section['menus'] as $index => $menu)

                    {{-- Dropdown Master Data (Hanya di Administrasi Akademik setelah Komponen Nilai) --}}
                    @if($section['title'] === 'Administrasi Akademik' && $index === 2)
                    <x-nav-dropdown title="Master Data" :active="$isMasterActive" icon="archive-box">
                        @foreach($masterSubmenus as $sub)
                        <a href="{{ route($sub['route']) }}"
                            class="block py-2 text-xs {{ request()->routeIs($sub['pattern'] ?? $sub['route']) ? 'text-unmaris-gold font-bold' : 'text-slate-400 hover:text-white' }}"
                            wire:navigate>
                            {{ $sub['label'] }}
                        </a>
                        @endforeach
                    </x-nav-dropdown>
                    @endif

                    <x-nav-link :href="($menu['route'] !== '#' ? route($menu['route']) : '#')"
                        :active="request()->routeIs($menu['pattern'] ?? $menu['route'])"
                        :icon="$menu['icon']">
                        {{ $menu['label'] }}
                    </x-nav-link>
                    @endforeach
                </div>
                @endif
                @endforeach
                @endif

                {{-- 3. Menu Mahasiswa --}}
                @if($role === 'mahasiswa')
                <div class="space-y-6">
                    <div class="space-y-1">
                        <p class="px-4 text-[10px] font-bold text-slate-500 uppercase tracking-[0.2em] mb-3 mt-2">Akademik</p>
                        <x-nav-link :href="route('mhs.krs')" :active="request()->routeIs('mhs.krs')" icon="book-open">KRS Online</x-nav-link>
                        <x-nav-link :href="route('mhs.absensi.scan')" :active="request()->routeIs('mhs.absensi.scan')" icon="qr-code">Absensi</x-nav-link>
                        <x-nav-link :href="route('mhs.khs')" :active="request()->routeIs('mhs.khs')" icon="document-text">Hasil Studi</x-nav-link>
                        <x-nav-link :href="route('mhs.transkrip')" :active="request()->routeIs('mhs.transkrip')" icon="academic-cap">Transkrip Nilai</x-nav-link>
                    </div>
                    <div class="space-y-1">
                        <p class="px-4 text-[10px] font-bold text-slate-500 uppercase tracking-[0.2em] mb-3">Keuangan & Akun</p>
                        <x-nav-link :href="route('mhs.keuangan')" :active="request()->routeIs('mhs.keuangan')" icon="credit-card">Riwayat Keuangan</x-nav-link>
                        <x-nav-link :href="route('mhs.profile')" :active="request()->routeIs('mhs.profile')" icon="user-circle">Profil & Password</x-nav-link>
                    </div>
                </div>
                @endif

                {{-- 4. Menu Dosen --}}
                @if($role === 'dosen')
                <div class="space-y-6">
                    <div class="space-y-1">
                        <p class="px-4 text-[10px] font-bold text-slate-500 uppercase tracking-[0.2em] mb-3 mt-2">Aktivitas Mengajar</p>
                        <x-nav-link :href="route('dosen.jadwal')" :active="request()->routeIs('dosen.jadwal')" icon="calendar-days">Jadwal & Presensi</x-nav-link>
                        <x-nav-link :href="route('dosen.manager-kelas')" :active="request()->routeIs('dosen.manager-kelas')" icon="clipboard-document-list">Presensi</x-nav-link>
                    </div>
                    <div class="space-y-1">
                        <p class="px-4 text-[10px] font-bold text-slate-500 uppercase tracking-[0.2em] mb-3">Lainnya</p>
                        <x-nav-link :href="route('dosen.perwalian')" :active="request()->routeIs('dosen.perwalian*')" icon="users">Perwalian (PA)</x-nav-link>
                        <x-nav-link :href="route('dosen.profile')" :active="request()->routeIs('dosen.profile')" icon="user-circle">Profil & Password</x-nav-link>
                    </div>
                </div>
                @endif

            </nav>

            {{-- LOGOUT --}}
            <div class="p-4 border-t border-white/5 bg-unmaris-dark">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center px-4 py-3 bg-white/5 hover:bg-rose-600 text-slate-300 hover:text-white border border-slate-700 hover:border-rose-500 rounded-xl transition-all shadow-md group">
                        <svg class="w-5 h-5 mr-2 group-hover:rotate-180 transition-transform duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        <span class="font-bold text-xs uppercase tracking-wider">Keluar Aplikasi</span>
                    </button>
                </form>
            </div>

        </aside>

        {{-- MAIN CONTENT --}}
        <div class="flex-1 flex flex-col h-screen overflow-hidden relative">
            {{-- Top Header --}}
            <header class="flex items-center justify-between px-6 py-4 bg-white border-b border-slate-200 z-30 shadow-sm relative">
                {{-- TAMPILAN SEMESTER AKTIF --}}
                <div class="hidden md:flex flex-col items-end mr-6 border-r border-slate-200 pr-6 h-8 justify-center">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Semester Aktif</span>
                    <span class="text-sm font-black text-unmaris-blue leading-none mt-0.5">
                        {{ $globalTa->nama_tahun ?? 'Tidak Ada Aktif' }}
                    </span>
                </div>
                <div class="flex items-center gap-4">
                    <button @click="sidebarOpen = true" class="text-slate-500 hover:text-unmaris-blue focus:outline-none md:hidden">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                    <div class="flex items-center gap-3 md:hidden">
                        <img src="{{ asset('logo.png') }}" alt="Logo" class="w-8 h-8 object-contain">
                        <span class="font-black text-unmaris-blue tracking-wider">UNMARIS</span>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <button class="relative p-2 text-slate-400 hover:text-unmaris-blue transition-colors rounded-full hover:bg-slate-50">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <span class="absolute top-1.5 right-1.5 h-2.5 w-2.5 rounded-full bg-red-500 border-2 border-white"></span>
                    </button>

                    <div class="flex items-center gap-3 pl-4 border-l border-slate-200">
                        <div class="text-right hidden sm:block">
                            <div class="text-sm font-bold text-slate-700 leading-tight">{{ Auth::user()->name }}</div>
                            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">{{ Auth::user()->role }}</div>
                        </div>
                        <div class="relative group">
                            <div class="h-10 w-10 rounded-full bg-unmaris-gold text-unmaris-blue flex items-center justify-center text-sm font-black shadow-md ring-2 ring-white cursor-pointer hover:ring-unmaris-blue transition-all overflow-hidden">
                                @if (Auth::user()->person && Auth::user()->person->photo_path)
                                {{-- Jika ada foto, tampilkan gambarnya --}}
                                <img src="{{ Storage::url(Auth::user()->person->photo_path) }}"
                                    alt="Profil"
                                    class="h-full w-full object-cover">
                                @else
                                {{-- Jika tidak ada, tampilkan inisial nama --}}
                                {{ substr(Auth::user()->name ?? 'G', 0, 1) }}
                                @endif
                            </div>
                            <span class="absolute bottom-0 right-0 h-3 w-3 rounded-full bg-emerald-500 border-2 border-white"></span>
                            <div class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-xl py-2 invisible group-hover:visible opacity-0 group-hover:opacity-100 transition-all z-50 border border-slate-100">
                                @php
                                $profileRoute = Auth::user()->role == 'mahasiswa' ? route('mhs.profile') : (Auth::user()->role == 'dosen' ? route('dosen.profile') : '#');
                                @endphp
                                <a href="{{ $profileRoute }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-unmaris-blue font-bold" wire:navigate>Profil Saya</a>
                                <div class="border-t border-slate-50 my-1"></div>
                                <form action="{{ route('logout') }}" method="POST">@csrf<button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 font-bold">Keluar</button></form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-slate-100 p-4 md:p-8 scroll-smooth">
                <div class="max-w-7xl mx-auto pb-10">{{ $slot }}</div>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>

</html>