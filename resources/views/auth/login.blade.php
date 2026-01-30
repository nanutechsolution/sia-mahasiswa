<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | SIAKAD UNMARIS</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Google Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Instrument Sans', sans-serif;
            background: radial-gradient(circle at top, #1e3a8a, #020617);
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(12px);
        }

        .input-focus:focus {
            border-color: #1d4ed8;
            box-shadow: 0 0 0 4px rgba(29, 78, 216, 0.15);
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center px-4 sm:px-6">

<div class="w-full max-w-sm sm:max-w-md glass-card rounded-3xl shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-500">

    <!-- HEADER -->
    <div class="px-8 py-10 text-center border-b border-slate-100 bg-slate-50">
        <img src="{{ asset('logo.png') }}" alt="Logo UNMARIS"
             class="w-16 h-16 sm:w-20 sm:h-20 mx-auto mb-4">

        <h1 class="text-lg sm:text-xl font-extrabold text-unmaris-blue uppercase tracking-wide">
            SIAKAD Enterprise
        </h1>

        <p class="text-[10px] sm:text-xs font-semibold text-slate-400 tracking-widest mt-1">
            UNIVERSITAS STELLA MARIS SUMBA
        </p>
    </div>

    <!-- CONTENT -->
    <div class="p-6 sm:p-8">

        @if ($errors->any())
        <div class="mb-6 bg-rose-50 border border-rose-200 p-4 rounded-2xl flex items-start gap-3">
            <svg class="w-5 h-5 text-rose-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0z" clip-rule="evenodd"/>
            </svg>
            <p class="text-sm font-semibold text-rose-600">
                {{ $errors->first() }}
            </p>
        </div>
        @endif

        <form method="POST" action="/login" class="space-y-5">
            @csrf

            <!-- USERNAME -->
            <div>
                <label class="block text-[11px] font-extrabold text-slate-500 uppercase tracking-widest mb-2">
                    Username / NIM
                </label>
                <input type="text" name="username" value="{{ old('username') }}" required autofocus
                       class="w-full px-5 py-3.5 rounded-2xl bg-slate-50 text-sm font-semibold outline-none input-focus"
                       placeholder="Masukkan ID Anda">
            </div>

            <!-- PASSWORD -->
            <div>
                <label class="block text-[11px] font-extrabold text-slate-500 uppercase tracking-widest mb-2">
                    Kata Sandi
                </label>
                <input type="password" name="password" required
                       class="w-full px-5 py-3.5 rounded-2xl bg-slate-50 text-sm font-semibold outline-none input-focus"
                       placeholder="••••••••">
            </div>

            <!-- BUTTON -->
            <button type="submit"
                    class="w-full py-3.5 bg-unmaris-blue text-white rounded-2xl font-extrabold text-sm tracking-widest
                           hover:scale-[1.02] active:scale-95 transition-all shadow-xl">
                MASUK SISTEM
            </button>
        </form>

        <!-- DEMO INFO -->
        <div class="mt-8 p-4 bg-slate-50 rounded-2xl border border-slate-100 text-[11px] text-slate-600">
            <p class="font-extrabold text-slate-400 tracking-widest mb-3 uppercase">
                Akses Simulasi
            </p>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <span class="block text-[9px] font-bold text-unmaris-blue">Mahasiswa</span>
                    2401001 / password
                </div>
                <div>
                    <span class="block text-[9px] font-bold text-unmaris-blue">Admin</span>
                    admin_keuangan / pw
                </div>
            </div>
        </div>
    </div>

    <!-- FOOTER -->
    <div class="py-4 bg-slate-50 text-center">
        <p class="text-[10px] font-semibold text-slate-400">
            © {{ date('Y') }} UNMARIS ICT Division — v4.0 Enterprise
        </p>
    </div>
</div>

</body>
</html>
