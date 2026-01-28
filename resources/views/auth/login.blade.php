<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SIAKAD UNMARIS Enterprise</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Google Fonts: Instrument Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Instrument Sans', sans-serif;
            background: linear-gradient(135deg, #1d1d72 0%, #0f172a 100%);
        }

        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }

        .input-focus:focus {
            border-color: #1d1d72;
            box-shadow: 0 0 0 4px rgba(29, 29, 114, 0.1);
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md login-card rounded-3xl shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-500">
        <!-- Brand Header -->
        <div class="bg-slate-50 px-8 py-10 text-center border-b border-slate-100">
            <img src="{{ asset('images/logo.png') }}" alt="Logo UNMARIS" class="w-20 h-20 mx-auto mb-4 drop-shadow-sm">
            <h1 class="text-xl font-black text-unmaris-blue tracking-tight uppercase">SIAKAD Enterprise</h1>
            <p class="text-xs font-bold text-slate-400 mt-1 tracking-widest">UNIVERSITAS STELLA MARIS SUMBA</p>
        </div>

        <div class="p-8 lg:p-10">
            @if ($errors->any())
            <div class="mb-6 bg-rose-50 border border-rose-100 p-4 rounded-2xl flex items-center animate-shake">
                <svg class="w-5 h-5 text-rose-500 mr-3 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
                <span class="text-sm font-bold text-rose-600 leading-tight">{{ $errors->first() }}</span>
            </div>
            @endif

            <form method="POST" action="/login" class="space-y-6">
                @csrf
                <div>
                    <label class="block text-[11px] font-black text-slate-500 uppercase tracking-widest mb-2">Username / NIM</label>
                    <div class="relative group">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400 group-focus-within:text-unmaris-blue transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </span>
                        <input type="text" name="username" value="{{ old('username') }}"
                            class="block w-full pl-12 pr-4 py-4 bg-slate-50 border-transparent rounded-2xl text-sm font-semibold transition-all outline-none input-focus placeholder:text-slate-300"
                            placeholder="Masukkan ID Anda" required autofocus>
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-black text-slate-500 uppercase tracking-widest mb-2">Kata Sandi</label>
                    <div class="relative group">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400 group-focus-within:text-unmaris-blue transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m0 0v3m0-3h3m-3 0H9m12-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </span>
                        <input type="password" name="password"
                            class="block w-full pl-12 pr-4 py-4 bg-slate-50 border-transparent rounded-2xl text-sm font-semibold transition-all outline-none input-focus placeholder:text-slate-300"
                            placeholder="••••••••" required>
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit"
                        class="w-full py-4 bg-unmaris-blue text-white rounded-2xl font-black text-sm shadow-xl shadow-indigo-900/20 hover:scale-[1.02] active:scale-95 transition-all uppercase tracking-widest">
                        Masuk Sekarang
                    </button>
                </div>
            </form>

            <!-- Hint Login (Hidden on actual production) -->
            <div class="mt-10 p-5 bg-slate-50 rounded-2xl border border-slate-100">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-3 flex items-center">
                    <svg class="w-3 h-3 mr-2 text-unmaris-gold" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" />
                    </svg>
                    Akses Simulasi
                </p>
                <div class="grid grid-cols-2 gap-y-2 gap-x-4 text-[11px] font-bold text-slate-500">
                    <div class="flex flex-col">
                        <span class="text-[9px] text-unmaris-blue">Mahasiswa</span>
                        <span>2401001 / password</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-[9px] text-unmaris-blue">Admin</span>
                        <span>admin_keuangan / pw</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="p-6 bg-slate-50 text-center">
            <p class="text-[10px] font-bold text-slate-400">
                &copy; {{ date('Y') }} UNMARIS ICT Division. V.4.0-PRO
            </p>
        </div>
    </div>
</body>

</html>