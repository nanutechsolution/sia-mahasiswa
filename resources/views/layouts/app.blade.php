<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'SIAKAD UNMARIS' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'unmaris-blue': '#002855',
                        /* Biru Tua Kampus */
                        'unmaris-dark': '#001a38',
                        /* Biru Lebih Gelap (Hover) */
                        'unmaris-gold': '#fcc000',
                        /* Kuning Emas Logo */
                        'unmaris-yellow': '#fbbf24',
                        /* Kuning Aksen */
                    }
                }
            }
        }
    </script>
    <style>
        [x-cloak] {
            display: none !important;
        }

        /* Custom Scrollbar for Sidebar */
        .sidebar-scroll::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar-scroll::-webkit-scrollbar-track {
            background: #001a38;
        }

        .sidebar-scroll::-webkit-scrollbar-thumb {
            background: #1e40af;
            border-radius: 4px;
        }

        .sidebar-scroll::-webkit-scrollbar-thumb:hover {
            background: #fcc000;
        }

        .nav-active {
            background-color: #fcc000;
            color: #002855;
            font-weight: 800;
            box-shadow: 0 4px 6px -1px rgba(252, 192, 0, 0.3);
        }

        .nav-inactive {
            color: #cbd5e1;
        }

        .nav-inactive:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: #ffffff;
        }
    </style>
    @livewireStyles
</head>

<body class="bg-slate-100 font-sans antialiased">

    <div class="min-h-screen flex">
        <!-- SIDEBAR -->
        <aside class="w-72 bg-unmaris-blue text-white flex flex-col fixed h-full shadow-2xl z-50 transition-all duration-300">
            <!-- Branding / Logo -->
            <div class="h-24 flex items-center px-6 border-b border-white/10 bg-unmaris-dark shadow-md z-10">
                <div class="flex items-center gap-4">
                    {{-- Logo Image --}}
                    <div class="bg-white p-1 rounded-full shadow-lg shadow-white/10">
                        <!-- Pastikan file logo.png ada di folder public -->
                        <img src="{{ asset('logo.png') }}" alt="Logo UNMARIS" class="w-10 h-10 object-contain">
                    </div>
                    <div>
                        <h1 class="text-xl font-black tracking-wider text-white leading-none">UNMARIS</h1>
                        <p class="text-[10px] text-unmaris-gold font-bold uppercase tracking-[0.2em] mt-1">SIAKAD v1.0</p>
                    </div>
                </div>
            </div>

            <!-- User Info Mini -->
            <div class="p-5 border-b border-white/10 bg-gradient-to-b from-unmaris-blue to-unmaris-dark">
                <div class="flex items-center space-x-3 bg-white/5 p-3 rounded-xl border border-white/5">
                    <div class="w-10 h-10 rounded-full bg-unmaris-gold text-unmaris-blue flex items-center justify-center text-lg font-black shadow-lg ring-2 ring-white/20">
                        {{ substr(Auth::user()->name ?? 'G', 0, 1) }}
                    </div>
                    <div class="overflow-hidden">
                        <div class="text-sm font-bold truncate w-40 text-white">{{ Auth::user()->name ?? 'Guest' }}</div>
                        <div class="flex items-center mt-0.5">
                            <div class="h-1.5 w-1.5 rounded-full bg-green-400 mr-1.5 animate-pulse"></div>
                            <div class="text-[10px] text-slate-300 font-bold uppercase tracking-wider">
                                {{ Auth::user()->role ?? 'Visitor' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-4 py-6 space-y-1.5 overflow-y-auto sidebar-scroll">

                {{-- ================= MENU ADMIN ================= --}}
                @if(Auth::user()->role == 'admin' || Auth::user()->role == 'superadmin' || Auth::user()->role == 'baak' || Auth::user()->role == 'keuangan')

                @if(Auth::user()->role == 'admin' || Auth::user()->role == 'superadmin' || Auth::user()->role == 'baak')
                {{-- GROUP: KONFIGURASI --}}
                <div class="pb-4">
                    <p class="px-3 text-[10px] font-black text-unmaris-gold/70 uppercase tracking-widest mb-2 ml-1">Konfigurasi</p>
                    <a href="{{ route('admin.semester') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition-all {{ request()->routeIs('admin.semester') ? 'nav-active' : 'nav-inactive' }}">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('admin.semester') ? 'text-unmaris-blue' : 'text-slate-400 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Tahun Akademik
                    </a>
                </div>

                {{-- GROUP: MASTER DATA --}}
                <div class="pb-4">
                    <p class="px-3 text-[10px] font-black text-unmaris-gold/70 uppercase tracking-widest mb-2 ml-1">Master Data</p>

                    <a href="{{ route('admin.master.fakultas') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition-all {{ request()->routeIs('admin.master.fakultas') ? 'nav-active' : 'nav-inactive' }}">
                        <span class="w-5 h-5 mr-3 flex items-center justify-center text-[10px] font-black border-2 border-current rounded-md {{ request()->routeIs('admin.master.fakultas') ? 'border-unmaris-blue' : 'border-slate-500 group-hover:border-white' }}">F</span>
                        Fakultas
                    </a>
                    <a href="{{ route('admin.master.prodi') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition-all {{ request()->routeIs('admin.master.prodi') ? 'nav-active' : 'nav-inactive' }}">
                        <span class="w-5 h-5 mr-3 flex items-center justify-center text-[10px] font-black border-2 border-current rounded-md {{ request()->routeIs('admin.master.prodi') ? 'border-unmaris-blue' : 'border-slate-500 group-hover:border-white' }}">P</span>
                        Program Studi
                    </a>
                    <a href="{{ route('admin.matakuliah') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition-all {{ request()->routeIs('admin.matakuliah') ? 'nav-active' : 'nav-inactive' }}">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('admin.matakuliah') ? 'text-unmaris-blue' : 'text-slate-400 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                        Mata Kuliah
                    </a>
                    <a href="{{ route('admin.kurikulum') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition-all {{ request()->routeIs('admin.kurikulum') ? 'nav-active' : 'nav-inactive' }}">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('admin.kurikulum') ? 'text-unmaris-blue' : 'text-slate-400 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        Kurikulum
                    </a>
                    <a href="{{ route('admin.jadwal') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition-all {{ request()->routeIs('admin.jadwal') ? 'nav-active' : 'nav-inactive' }}">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('admin.jadwal') ? 'text-unmaris-blue' : 'text-slate-400 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Jadwal Kuliah
                    </a>
                </div>

                {{-- GROUP: PENGGUNA --}}
                <div class="pb-4">
                    <p class="px-3 text-[10px] font-black text-unmaris-gold/70 uppercase tracking-widest mb-2 ml-1">Pengguna</p>

                    <a href="{{ route('admin.mahasiswa') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition-all {{ request()->routeIs('admin.mahasiswa') ? 'nav-active' : 'nav-inactive' }}">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('admin.mahasiswa') ? 'text-unmaris-blue' : 'text-slate-400 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        Data Mahasiswa
                    </a>
                    <a href="{{ route('admin.dosen') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition-all {{ request()->routeIs('admin.dosen') ? 'nav-active' : 'nav-inactive' }}">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('admin.dosen') ? 'text-unmaris-blue' : 'text-slate-400 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        Data Dosen
                    </a>
                    <a href="{{ route('admin.camaba') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition-all {{ request()->routeIs('admin.camaba') ? 'nav-active' : 'nav-inactive' }}">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('admin.camaba') ? 'text-unmaris-blue' : 'text-slate-400 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                        PMB & Daftar Ulang
                    </a>
                </div>
                @endif

                @if(Auth::user()->role == 'admin' || Auth::user()->role == 'superadmin' || Auth::user()->role == 'keuangan')
                {{-- GROUP: KEUANGAN --}}
                <div class="pb-4">
                    <p class="px-3 text-[10px] font-black text-unmaris-gold/70 uppercase tracking-widest mb-2 ml-1">Keuangan</p>

                    <a href="{{ route('admin.keuangan') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition-all {{ request()->routeIs('admin.keuangan') ? 'nav-active' : 'nav-inactive' }}">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('admin.keuangan') ? 'text-unmaris-blue' : 'text-slate-400 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Verifikasi Bayar
                    </a>
                    <a href="{{ route('admin.keuangan.komponen') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition-all {{ request()->routeIs('admin.keuangan.komponen') ? 'nav-active' : 'nav-inactive' }}">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('admin.keuangan.komponen') ? 'text-unmaris-blue' : 'text-slate-400 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                        Komponen Biaya
                    </a>
                    <a href="{{ route('admin.keuangan.skema') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition-all {{ request()->routeIs('admin.keuangan.skema') ? 'nav-active' : 'nav-inactive' }}">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('admin.keuangan.skema') ? 'text-unmaris-blue' : 'text-slate-400 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                        Skema Tarif
                    </a>
                    <a href="{{ route('admin.tagihan-generator') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition-all {{ request()->routeIs('admin.tagihan-generator') ? 'nav-active' : 'nav-inactive' }}">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('admin.tagihan-generator') ? 'text-unmaris-blue' : 'text-slate-400 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        Generator Tagihan
                    </a>
                    <a href="{{ route('admin.keuangan.laporan') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition-all {{ request()->routeIs('admin.keuangan.laporan') ? 'nav-active' : 'nav-inactive' }}">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('admin.keuangan.laporan') ? 'text-unmaris-blue' : 'text-slate-400 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        Laporan / Monitoring
                    </a>
                </div>
                @endif

                @if(Auth::user()->role == 'superadmin')
                {{-- GROUP: SYSTEM --}}
                <div class="pb-4">
                    <p class="px-3 text-[10px] font-black text-unmaris-gold/70 uppercase tracking-widest mb-2 ml-1">System & IT</p>

                    <a href="{{ route('admin.users') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition-all {{ request()->routeIs('admin.users') ? 'nav-active' : 'nav-inactive' }}">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('admin.users') ? 'text-unmaris-blue' : 'text-slate-400 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        User Management
                    </a>
                    <a href="{{ route('admin.audit') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition-all {{ request()->routeIs('admin.audit') ? 'nav-active' : 'nav-inactive' }}">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('admin.audit') ? 'text-unmaris-blue' : 'text-slate-400 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                        Audit Logs
                    </a>
                    <a href="{{ route('admin.roles') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition-all {{ request()->routeIs('admin.roles') ? 'nav-active' : 'nav-inactive' }}">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('admin.roles') ? 'text-unmaris-blue' : 'text-slate-400 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        Roles & Akses
                    </a>
                </div>
                @endif
                @endif

                {{-- ================= MENU MAHASISWA ================= --}}
                @if(Auth::user()->role == 'mahasiswa')
                <div class="pb-4">
                    <p class="px-3 text-[10px] font-black text-unmaris-gold/70 uppercase tracking-widest mb-2 ml-1">Akademik</p>

                    <a href="{{ route('mhs.krs') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition-all {{ request()->routeIs('mhs.krs') ? 'nav-active' : 'nav-inactive' }}">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('mhs.krs') ? 'text-unmaris-blue' : 'text-slate-400 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                        KRS Online
                    </a>
                    <a href="{{ route('mhs.khs') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition-all {{ request()->routeIs('mhs.khs') ? 'nav-active' : 'nav-inactive' }}">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('mhs.khs') ? 'text-unmaris-blue' : 'text-slate-400 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Kartu Hasil Studi
                    </a>
                    <a href="{{ route('mhs.transkrip') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition-all {{ request()->routeIs('mhs.transkrip') ? 'nav-active' : 'nav-inactive' }}">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('mhs.transkrip') ? 'text-unmaris-blue' : 'text-slate-400 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        Transkrip Nilai
                    </a>
                </div>

                <div class="pb-4 border-t border-white/10 pt-4">
                    <p class="px-3 text-[10px] font-black text-unmaris-gold/70 uppercase tracking-widest mb-2 ml-1">Tagihan</p>
                    <a href="{{ route('mhs.keuangan') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition-all {{ request()->routeIs('mhs.keuangan') ? 'nav-active' : 'nav-inactive' }}">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('mhs.keuangan') ? 'text-unmaris-blue' : 'text-slate-400 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                        Riwayat Keuangan
                    </a>
                </div>
                @endif

                {{-- ================= MENU DOSEN ================= --}}
                @if(Auth::user()->role == 'dosen')
                <div class="pb-4">
                    <p class="px-3 text-[10px] font-black text-unmaris-gold/70 uppercase tracking-widest mb-2 ml-1">Perkuliahan</p>

                    <a href="{{ route('dosen.jadwal') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition-all {{ request()->routeIs('dosen.jadwal') ? 'nav-active' : 'nav-inactive' }}">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('dosen.jadwal') ? 'text-unmaris-blue' : 'text-slate-400 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z" />
                        </svg>
                        Jadwal Mengajar
                    </a>
                    <a href="{{ route('dosen.perwalian') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition-all {{ request()->routeIs('dosen.perwalian*') ? 'nav-active' : 'nav-inactive' }}">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('dosen.perwalian*') ? 'text-unmaris-blue' : 'text-slate-400 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        Perwalian (PA)
                    </a>
                </div>
                @endif

            </nav>

            <!-- Logout -->
            <div class="p-4 border-t border-white/10 bg-unmaris-dark">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center px-4 py-3 border border-slate-700 rounded-xl shadow-lg bg-white/5 hover:bg-red-600 hover:border-red-600 hover:text-white text-sm font-bold text-slate-300 transition-all focus:outline-none">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Keluar Aplikasi
                    </button>
                </form>
            </div>
        </aside>

        <!-- MAIN CONTENT -->
        <main class="flex-1 ml-72 p-8 bg-slate-100 min-h-screen">
            <div class="max-w-7xl mx-auto animate-in fade-in slide-in-from-bottom-2 duration-500">
                {{ $slot }}
            </div>
        </main>
    </div>

    @livewireScripts
</body>

</html>