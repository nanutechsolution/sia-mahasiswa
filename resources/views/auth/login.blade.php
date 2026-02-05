<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Masuk | SIAKAD UNMARIS Enterprise</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js (Wajib untuk fitur interaktif) -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <!-- Google Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #020617;
            background-image: 
                radial-gradient(at 0% 0%, rgba(30, 58, 138, 0.4) 0px, transparent 50%),
                radial-gradient(at 100% 0%, rgba(252, 192, 0, 0.1) 0px, transparent 50%),
                radial-gradient(at 50% 100%, rgba(30, 58, 138, 0.2) 0px, transparent 50%);
            background-attachment: fixed;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        .custom-input {
            transition: all 0.2s ease-in-out;
        }

        .custom-input:focus {
            background-color: #ffffff;
            border-color: #002855;
            box-shadow: 0 0 0 4px rgba(0, 40, 85, 0.1);
        }

        .logo-float {
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-8px); }
            100% { transform: translateY(0px); }
        }

        .hp-field {
            display: none !important;
            visibility: hidden !important;
        }

        [x-cloak] { display: none !important; }

        /* Custom Scrollbar for Help Modal */
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center p-4 sm:p-6" x-data="{ helpModalOpen: false }">

<div class="w-full max-w-md login-card rounded-[2.5rem] overflow-hidden animate-in fade-in slide-in-from-bottom-4 duration-700">

    {{-- Aksen Garis Emas --}}
    <div class="h-1.5 w-full bg-gradient-to-r from-unmaris-blue via-unmaris-gold to-unmaris-blue"></div>

    <div class="px-6 sm:px-10 pt-10 pb-6 text-center">
        <div class="relative inline-block mb-6 logo-float">
            <div class="absolute inset-0 bg-unmaris-gold blur-2xl opacity-10 rounded-full"></div>
            <img src="{{ asset('logo.png') }}" alt="Logo UNMARIS" class="relative w-20 h-20 mx-auto drop-shadow-xl">
        </div>

        <h1 class="text-2xl font-black text-[#002855] tracking-tight uppercase">
            SIAKAD <span class="text-unmaris-gold">Enterprise</span>
        </h1>
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.3em] mt-1.5">
            Universitas Stella Maris Sumba
        </p>
    </div>

    <div class="px-6 sm:px-10 pb-12">
        
        {{-- Pesan Kesalahan --}}
        @if ($errors->any())
        <div class="mb-6 bg-rose-50 border border-rose-100 p-4 rounded-2xl flex items-start gap-3 animate-in shake duration-500">
            <div class="bg-rose-500 rounded-full p-1 shrink-0 mt-0.5">
                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </div>
            <p class="text-xs font-bold text-rose-700 leading-tight">
                {{ $errors->first() }}
            </p>
        </div>
        @endif

        <form method="POST" action="/login" class="space-y-5" id="loginForm" autocomplete="off">
            @csrf
            
            {{-- Honeypot --}}
            <div class="hp-field">
                <input type="text" name="website" tabindex="-1" autocomplete="off">
            </div>

            <!-- Input Username -->
            <div class="space-y-1.5">
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">
                    Username / NIM
                </label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-unmaris-blue transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <input type="text" name="username" value="{{ old('username') }}" required autofocus
                        class="custom-input w-full pl-12 pr-4 py-4 rounded-2xl bg-slate-50 border border-slate-100 text-sm font-bold text-slate-700 outline-none placeholder-slate-300 shadow-sm"
                        placeholder="Masukkan identitas Anda">
                </div>
            </div>

            <!-- Input Password -->
            <div class="space-y-1.5" x-data="{ show: false }">
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">
                    Kata Sandi
                </label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-unmaris-blue transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </div>
                    <input :type="show ? 'text' : 'password'" name="password" required
                        class="custom-input w-full pl-12 pr-12 py-4 rounded-2xl bg-slate-50 border border-slate-100 text-sm font-bold text-slate-700 outline-none placeholder-slate-300 shadow-sm"
                        placeholder="••••••••">
                    
                    <button type="button" @click="show = !show" class="absolute right-4 top-0 h-full px-2 text-slate-300 hover:text-unmaris-blue transition-colors focus:outline-none">
                        <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        <svg x-show="show" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7 1.274-4.057 5.064-7 9.542-7 1.747 0 3.332.477 4.5 1.253M21 12c-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18"/></svg>
                    </button>
                </div>
            </div>

            <div class="flex items-center justify-between px-1">
                <label class="flex items-center cursor-pointer group">
                    <input type="checkbox" name="remember" class="w-4 h-4 rounded border-slate-300 text-unmaris-blue focus:ring-unmaris-blue transition-all">
                    <span class="ml-2 text-xs font-bold text-slate-400 group-hover:text-slate-600 transition-colors">Ingat sesi</span>
                </label>
                <button type="button" @click="helpModalOpen = true" class="text-[10px] font-black text-unmaris-blue uppercase tracking-tighter hover:text-unmaris-gold transition-colors focus:outline-none">
                    Bantuan Akses?
                </button>
            </div>

            <!-- Tombol Submit -->
            <button type="submit" id="submitBtn"
                class="w-full py-4 bg-[#002855] text-white rounded-2xl font-black text-xs tracking-[0.2em] uppercase
                       hover:bg-black hover:scale-[1.02] active:scale-95 transition-all shadow-xl shadow-indigo-900/20 flex items-center justify-center gap-2">
                <span id="btnText">Masuk Ke Sistem</span>
                <svg id="btnIcon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </button>
        </form>

        {{-- Footer Keamanan --}}
        <div class="mt-10 flex items-center justify-center gap-4">
            <div class="h-px bg-slate-100 flex-1"></div>
            <div class="flex items-center gap-1.5 opacity-40">
                <svg class="w-3.5 h-3.5 text-slate-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/></svg>
                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest text-center">Enkripsi Berlapis</span>
            </div>
            <div class="h-px bg-slate-100 flex-1"></div>
        </div>
    </div>

    <div class="pb-8 text-center border-t border-slate-50 pt-4 bg-slate-50/30">
        <p class="text-[9px] font-bold text-slate-300 uppercase tracking-[0.2em]">
            &copy; 2026 UNMARIS ICT &bull; v4.2 Secure
        </p>
    </div>
</div>

{{-- MODAL BANTUAN AKSES (FIXED CLOSE BUTTON) --}}
<div x-show="helpModalOpen" 
     x-cloak 
     class="fixed inset-0 z-[999] flex items-center justify-center p-4 sm:p-6 bg-slate-900/60 backdrop-blur-sm transition-opacity"
     x-transition:enter="ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">
    
    <div class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-lg overflow-hidden border border-white/20 transform transition-all"
         @click.away="helpModalOpen = false"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95 translate-y-4"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 translate-y-4">
        
        <div class="bg-[#002855] px-8 py-6 text-white flex justify-between items-center relative">
            <div class="absolute right-0 top-0 p-8 opacity-10">
                <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
            </div>
            <div class="relative z-10 text-left">
                <h3 class="text-xl font-black uppercase tracking-widest leading-none">Pusat Bantuan</h3>
                <p class="text-[10px] font-bold uppercase text-unmaris-gold mt-2">Panduan akses & kendala login</p>
            </div>
            {{-- Tombol Close (Atas) --}}
            <button type="button" @click.stop="helpModalOpen = false" class="text-white/50 hover:text-white transition-colors focus:outline-none shrink-0 ml-4 relative z-50">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <div class="p-6 sm:p-8 space-y-5 bg-white">
            <div class="space-y-4 max-h-[40vh] overflow-y-auto custom-scrollbar pr-2">
                <div class="flex gap-4 p-4 rounded-2xl bg-slate-50 border border-slate-100 hover:border-indigo-100 transition-all text-left">
                    <div class="w-10 h-10 shrink-0 bg-indigo-100 text-indigo-600 rounded-xl flex items-center justify-center font-black text-xs uppercase">01</div>
                    <div>
                        <h4 class="text-sm font-black text-[#002855] uppercase tracking-tight">Lupa Kata Sandi?</h4>
                        <p class="text-[11px] text-slate-500 mt-1 leading-relaxed">Silakan hubungi <b>Unit ICT</b> untuk reset password dengan membawa identitas resmi atau Kartu Mahasiswa.</p>
                    </div>
                </div>

                <div class="flex gap-4 p-4 rounded-2xl bg-slate-50 border border-slate-100 hover:border-indigo-100 transition-all text-left">
                    <div class="w-10 h-10 shrink-0 bg-indigo-100 text-indigo-600 rounded-xl flex items-center justify-center font-black text-xs uppercase">02</div>
                    <div>
                        <h4 class="text-sm font-black text-[#002855] uppercase tracking-tight">Akun Terkunci?</h4>
                        <p class="text-[11px] text-slate-500 mt-1 leading-relaxed">Sistem akan mengunci akses selama 5 menit jika gagal login sebanyak 5 kali berturut-turut demi keamanan.</p>
                    </div>
                </div>
            </div>

            <div class="pt-6 border-t border-slate-100 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <a href="https://wa.me/628123456789" target="_blank" class="flex items-center justify-center gap-3 px-6 py-4 bg-[#25D366] text-white rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-lg shadow-green-100 hover:scale-[1.02] transition-all">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.414 0 .004 5.411 0 12.05c0 2.123.553 4.197 1.603 6.04L0 24l6.117-1.605a11.815 11.815 0 005.93 1.58h.005c6.632 0 12.042-5.411 12.046-12.05a11.812 11.812 0 00-3.593-8.514z"/></svg>
                    WhatsApp ICT
                </a>
                <button type="button" @click="helpModalOpen = false" class="flex items-center justify-center gap-3 px-6 py-4 bg-slate-100 text-slate-600 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-slate-200 transition-all border border-slate-200">
                    Selesai & Tutup
                </button>
            </div>
        </div>

        <div class="px-8 py-4 bg-slate-50 border-t border-slate-100 text-center">
            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Waktu Layanan: Senin - Jumat (08.00 - 16.00 WITA)</p>
        </div>
    </div>
</div>

<script>
    const form = document.getElementById('loginForm');
    const btn = document.getElementById('submitBtn');
    const btnText = document.getElementById('btnText');
    const btnIcon = document.getElementById('btnIcon');

    form.addEventListener('submit', function() {
        // Jeda kecil untuk UX agar klik terasa
        setTimeout(() => {
            btn.disabled = true;
            btn.classList.add('opacity-70', 'cursor-wait');
            btnText.innerText = 'Memverifikasi...';
            btnIcon.innerHTML = `
                <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            `;
        }, 10);
    });
</script>

</body>
</html>