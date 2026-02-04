<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'SIAKAD UNMARIS' }}</title>

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

            <nav class="flex-1 overflow-y-auto px-4 py-4 space-y-1 sidebar-scroll">
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" wire:navigate>
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                    </svg>
                    Dashboard
                </a>

                {{-- AREA ADMIN / STAFF (PBAC Check) --}}
                @if(in_array(Auth::user()->role, ['superadmin', 'admin', 'bara', 'bauk', 'lpm']))

                {{-- MENU BARA / AKADEMIK (Permission: akses_modul_akademik) --}}
                @if(Auth::user()->can('akses_modul_akademik') || Auth::user()->role == 'superadmin')
                <div class="nav-group-title">Administrasi Akademik</div>

                <a href="{{ route('admin.semester') }}" class="nav-link {{ request()->routeIs('admin.semester') ? 'active' : '' }}" wire:navigate>
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z"></path>
                    </svg>
                    Tahun Akademik
                </a>

                <a href="{{ route('admin.komponen-nilai') }}" class="nav-link {{ request()->routeIs('admin.komponen-nilai') ? 'active' : '' }}" wire:navigate>
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    Komponen Nilai
                </a>

                {{-- Submenu Master Data --}}
                @php
                $masterRoutes = ['admin.master.fakultas', 'admin.master.prodi', 'admin.master.program-kelas', 'admin.matakuliah', 'admin.kurikulum*', 'admin.skala-nilai', 'admin.aturan-sks'];
                $isMasterActive = request()->routeIs($masterRoutes);
                @endphp

                <div x-data="{ open: {{ $isMasterActive ? 'true' : 'false' }} }">
                    <button @click="open = !open" class="nav-link w-full justify-between {{ $isMasterActive ? 'text-white font-bold' : '' }}">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3 {{ $isMasterActive ? 'text-unmaris-gold' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                            Master Data
                        </div>
                        <svg class="w-4 h-4 transition-transform duration-200 {{ $isMasterActive ? 'text-unmaris-gold' : '' }}" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div x-show="open" class="pl-9 space-y-1 mt-1 border-l-2 border-white/10 ml-5" x-cloak x-transition>
                        <a href="{{ route('admin.master.fakultas') }}" class="nav-link text-xs py-2 {{ request()->routeIs('admin.master.fakultas') ? 'text-unmaris-gold font-bold bg-white/5' : 'text-slate-400' }}" wire:navigate>Fakultas</a>
                        <a href="{{ route('admin.master.prodi') }}" class="nav-link text-xs py-2 {{ request()->routeIs('admin.master.prodi') ? 'text-unmaris-gold font-bold bg-white/5' : 'text-slate-400' }}" wire:navigate>Program Studi</a>
                        <a href="{{ route('admin.master.program-kelas') }}" class="nav-link text-xs py-2 {{ request()->routeIs('admin.master.program-kelas') ? 'text-unmaris-gold font-bold bg-white/5' : 'text-slate-400' }}" wire:navigate>Program Kelas</a>
                        <a href="{{ route('admin.matakuliah') }}" class="nav-link text-xs py-2 {{ request()->routeIs('admin.matakuliah') ? 'text-unmaris-gold font-bold bg-white/5' : 'text-slate-400' }}" wire:navigate>Mata Kuliah</a>
                        <a href="{{ route('admin.kurikulum') }}" class="nav-link text-xs py-2 {{ request()->routeIs('admin.kurikulum*') ? 'text-unmaris-gold font-bold bg-white/5' : 'text-slate-400' }}" wire:navigate>Kurikulum</a>
                        <a href="{{ route('admin.skala-nilai') }}" class="nav-link text-xs py-2 {{ request()->routeIs('admin.skala-nilai') ? 'text-unmaris-gold font-bold bg-white/5' : 'text-slate-400' }}" wire:navigate>Skala Nilai</a>
                        <a href="{{ route('admin.aturan-sks') }}" class="nav-link text-xs py-2 {{ request()->routeIs('admin.aturan-sks') ? 'text-unmaris-gold font-bold bg-white/5' : 'text-slate-400' }}" wire:navigate>Aturan SKS</a>
                    </div>
                </div>

                <a href="{{ route('admin.jadwal') }}" class="nav-link {{ request()->routeIs('admin.jadwal') ? 'active' : '' }}" wire:navigate>
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Jadwal Kuliah
                </a>
                <a href="{{ route('admin.cetak.absensi.manager') }}" class="nav-link {{ request()->routeIs('admin.cetak.absensi*') ? 'active' : '' }}" wire:navigate>
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    Cetak Absensi
                </a>

                <div class="nav-group-title">Operasional</div>
                <a href="{{ route('admin.ploting-pa') }}" class="nav-link {{ request()->routeIs('admin.ploting-pa') ? 'active' : '' }}" wire:navigate>
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    Plotting PA
                </a>
                <a href="{{ route('admin.akademik.mutasi') }}" class="nav-link {{ request()->routeIs('admin.akademik.mutasi') ? 'active' : '' }}" wire:navigate>
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                    </svg>
                    Mutasi Mahasiswa
                </a>
                <a href="{{ route('admin.hr.manager') }}" class="nav-link {{ request()->routeIs('admin.hr.manager') ? 'active' : '' }}" wire:navigate>
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    HR & Pejabat
                </a>

                <div class="nav-group-title">Manajemen User</div>
                <a href="{{ route('admin.camaba') }}" class="nav-link {{ request()->routeIs('admin.camaba') ? 'active' : '' }}" wire:navigate>
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                    </svg>
                    PMB & Daftar Ulang
                </a>
                <a href="{{ route('admin.mahasiswa') }}" class="nav-link {{ request()->routeIs('admin.mahasiswa') ? 'active' : '' }}" wire:navigate>
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    Data Mahasiswa
                </a>
                <a href="{{ route('admin.dosen') }}" class="nav-link {{ request()->routeIs('admin.dosen') ? 'active' : '' }}" wire:navigate>
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    Data Dosen
                </a>
                @endif

                {{-- MENU BAUK / KEUANGAN (Permission: akses_modul_keuangan) --}}
                @if(Auth::user()->can('akses_modul_keuangan') || Auth::user()->role == 'superadmin')
                <div class="nav-group-title">Administrasi Keuangan</div>
                <a href="{{ route('admin.keuangan') }}" class="nav-link {{ request()->routeIs('admin.keuangan') ? 'active' : '' }}" wire:navigate>
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Verifikasi Bayar
                </a>
                <a href="{{ route('admin.keuangan.komponen') }}" class="nav-link {{ request()->routeIs('admin.keuangan.komponen') ? 'active' : '' }}" wire:navigate>
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                    </svg>
                    Komponen Biaya
                </a>
                <a href="{{ route('admin.keuangan.skema') }}" class="nav-link {{ request()->routeIs('admin.keuangan.skema') ? 'active' : '' }}" wire:navigate>
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                    Skema Tarif
                </a>
                <a href="{{ route('admin.tagihan-generator') }}" class="nav-link {{ request()->routeIs('admin.tagihan-generator') ? 'active' : '' }}" wire:navigate>
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    Generator Tagihan
                </a>
                <a href="{{ route('admin.keuangan.manual') }}" class="nav-link {{ request()->routeIs('admin.keuangan.manual') ? 'active' : '' }}" wire:navigate>
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Tagihan Manual
                </a>
                <a href="{{ route('admin.keuangan.adjustment') }}" class="nav-link {{ request()->routeIs('admin.keuangan.adjustment') ? 'active' : '' }}" wire:navigate>
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    Koreksi & Saldo
                </a>
                <a href="{{ route('admin.keuangan.laporan') }}" class="nav-link {{ request()->routeIs('admin.keuangan.laporan') ? 'active' : '' }}" wire:navigate>
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    Laporan & Audit
                </a>
                @endif


                {{-- MENU LPM / PENJAMINAN MUTU (Permission: akses_modul_lpm) --}}
                @if(Auth::user()->can('akses_modul_lpm') || Auth::user()->role == 'superadmin' || Auth::user()->role == 'lpm')
                <div class="nav-group-title">Penjaminan Mutu (SPMI)</div>

                <a href="{{ route('admin.lpm.dashboard') }}" class="nav-link {{ request()->routeIs('admin.lpm.dashboard') ? 'active' : '' }}" wire:navigate>
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                    Command Center
                </a>

                {{-- Section: Siklus PPEPP --}}
                <a href="{{route('admin.lpm.standar')}}" class="nav-link {{ request()->routeIs('admin.lpm.standar*') ? 'active' : '' }}" wire:navigate>
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                    Standar Mutu
                </a>

                <a href="{{route('admin.lpm.ami')}}" class="nav-link {{ request()->routeIs('admin.lpm.ami*') ? 'active' : '' }}" wire:navigate>
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    Audit (AMI)
                </a>

                <a href="{{route('admin.lpm.dokumen')}}" class="nav-link {{ request()->routeIs('admin.lpm.dokumen*') ? 'active' : '' }}" wire:navigate>
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z"></path>
                    </svg>
                    Dokumen Mutu
                </a>

                {{-- Section: Evaluasi Kinerja --}}
                <div class="nav-group-title">Monev & Akreditasi</div>
                <a href="{{route('admin.lpm.edom.index')}}" class="nav-link {{ request()->routeIs('admin.lpm.edom.index*') ? 'active' : '' }}" wire:navigate>
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Evaluasi Dosen (EDOM)
                </a>

                <a href="{{route('admin.lpm.iku')}}" class="nav-link {{ request()->routeIs('admin.lpm.iku*') ? 'active' : '' }}" wire:navigate>
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    IKU & Akreditasi
                </a>
                <a href="{{route('admin.lpm.edom.setup')}}" class="nav-link {{ request()->routeIs('admin.lpm.edom.setup*') ? 'active' : '' }}" wire:navigate>
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    Kuesioner
                </a>
                <a href="{{route('admin.lpm.indikator')}}" class="nav-link {{ request()->routeIs('admin.lpm.indikator*') ? 'active' : '' }}" wire:navigate>
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    Indikator
                </a>
                @endif
                {{-- MENU SYSTEM (SUPERADMIN ONLY) --}}
                @if(Auth::user()->role == 'superadmin')
                <div class="nav-group-title">System & IT</div>
                <a href="{{ route('admin.users') }}" class="nav-link {{ request()->routeIs('admin.users') ? 'active' : '' }}" wire:navigate>
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    User Management
                </a>
                <a href="{{ route('admin.audit') }}" class="nav-link {{ request()->routeIs('admin.audit') ? 'active' : '' }}" wire:navigate>
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                    Audit Logs
                </a>
                <a href="{{ route('admin.roles') }}" class="nav-link {{ request()->routeIs('admin.roles') ? 'active' : '' }}" wire:navigate>
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                    Roles & Akses
                </a>
                @endif
                @endif

                {{-- ================= MENU MAHASISWA ================= --}}
                @if(Auth::user()->role == 'mahasiswa')
                <div class="nav-group-title">Akademik</div>
                <a href="{{ route('mhs.krs') }}" class="nav-link {{ request()->routeIs('mhs.krs') ? 'active' : '' }}" wire:navigate>
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                    KRS Online
                </a>
                <a href="{{ route('mhs.khs') }}" class="nav-link {{ request()->routeIs('mhs.khs') ? 'active' : '' }}" wire:navigate>
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Kartu Hasil Studi
                </a>
                <a href="{{ route('mhs.transkrip') }}" class="nav-link {{ request()->routeIs('mhs.transkrip') ? 'active' : '' }}" wire:navigate>
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                    Transkrip Nilai
                </a>

                <div class="nav-group-title">Keuangan</div>
                <a href="{{ route('mhs.keuangan') }}" class="nav-link {{ request()->routeIs('mhs.keuangan') ? 'active' : '' }}" wire:navigate>
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                    </svg>
                    Riwayat Keuangan
                </a>

                <div class="nav-group-title">Akun</div>
                <a href="{{ route('mhs.profile') }}" class="nav-link {{ request()->routeIs('mhs.profile') ? 'active' : '' }}" wire:navigate>
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Profil & Password
                </a>
                @endif

                {{-- ================= MENU DOSEN ================= --}}
                @if(Auth::user()->role == 'dosen')
                <div class="nav-group-title">Aktivitas Mengajar</div>
                <a href="{{ route('dosen.jadwal') }}" class="nav-link {{ request()->routeIs('dosen.jadwal') ? 'active' : '' }}" wire:navigate>
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    Jadwal & Presensi
                </a>

                <div class="nav-group-title">Pembimbingan</div>
                <a href="{{ route('dosen.perwalian') }}" class="nav-link {{ request()->routeIs('dosen.perwalian*') ? 'active' : '' }}" wire:navigate>
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    Perwalian (PA)
                </a>

                <div class="nav-group-title">Akun</div>
                <a href="{{ route('dosen.profile') }}" class="nav-link {{ request()->routeIs('dosen.profile') ? 'active' : '' }}" wire:navigate>
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Profil & Password
                </a>
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
                            <div class="h-10 w-10 rounded-full bg-unmaris-gold text-unmaris-blue flex items-center justify-center text-sm font-black shadow-md ring-2 ring-white cursor-pointer hover:ring-unmaris-blue transition-all">
                                {{ substr(Auth::user()->name ?? 'G', 0, 1) }}
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
    @livewireScripts
</body>

</html>