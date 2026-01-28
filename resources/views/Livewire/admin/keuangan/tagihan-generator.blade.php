<div>
    {{-- SEO & Header Layout Integration --}}
    <x-slot name="title">Tagihan Generator</x-slot>
    <x-slot name="header">Generator Tagihan Massal</x-slot>

    <div class="space-y-8">
        {{-- Control Card --}}
        <div class="bg-white rounded-3xl shadow-xl border border-slate-200 overflow-hidden animate-in fade-in duration-500">
            <div class="px-8 py-6 border-b border-slate-100 bg-slate-50/50">
                <h3 class="text-sm font-black text-unmaris-blue uppercase tracking-widest">Invoicing & Revenue Engine</h3>
                <p class="text-xs text-slate-400 font-bold mt-1 uppercase tracking-tighter">Automated Billing System</p>
            </div>

            <div class="p-8 lg:p-10">
                <div class="bg-indigo-50/50 rounded-2xl p-6 border border-indigo-100 flex items-start space-x-5 mb-10">
                    <div class="p-3 bg-unmaris-blue rounded-xl shadow-lg shadow-indigo-200">
                        <svg class="w-6 h-6 text-unmaris-yellow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <span class="text-xs font-black text-unmaris-blue uppercase block mb-1 tracking-widest">Cara Kerja Generator:</span>
                        <p class="text-[12px] text-slate-500 leading-relaxed font-medium">
                            Sistem akan memindai data mahasiswa berdasarkan filter di bawah, mengecek <strong>Skema Tarif</strong> yang berlaku, dan membuat invoice otomatis untuk mahasiswa yang belum memiliki tagihan di semester target.
                        </p>
                    </div>
                </div>

                {{-- Status Notifications --}}
                @if (session()->has('success'))
                <div class="mb-8 bg-emerald-50 border border-emerald-100 p-4 rounded-2xl text-emerald-800 text-sm flex items-center animate-in slide-in-from-top-2">
                    <svg class="w-5 h-5 mr-3 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <span class="font-bold">{{ session('success') }}</span>
                </div>
                @endif

                @if (session()->has('error'))
                <div class="mb-8 bg-rose-50 border border-rose-100 p-4 rounded-2xl text-rose-800 text-sm flex items-center">
                    <svg class="w-5 h-5 mr-3 text-rose-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    <span class="font-bold">{{ session('error') }}</span>
                </div>
                @endif

                {{-- Form Filters --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-2">Target Semester Tagihan *</label>
                        <select wire:model="semesterId" class="block w-full rounded-2xl border-slate-200 bg-slate-50 py-4 px-5 focus:bg-white focus:border-unmaris-blue focus:ring-4 focus:ring-unmaris-blue/5 text-sm font-semibold transition-all outline-none">
                            @foreach($semesters as $sem)
                            <option value="{{ $sem->id }}">{{ $sem->nama_tahun }} {{ $sem->is_active ? '(Aktif Saat Ini)' : '' }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-2">Filter Angkatan Mahasiswa *</label>
                        <select wire:model="angkatanId" class="block w-full rounded-2xl border-slate-200 bg-slate-50 py-4 px-5 focus:bg-white focus:border-unmaris-blue focus:ring-4 focus:ring-unmaris-blue/5 text-sm font-semibold transition-all outline-none">
                            <option value="">-- Pilih Angkatan --</option>
                            @foreach($angkatans as $angkatan)
                            <option value="{{ $angkatan->id_tahun }}">{{ $angkatan->id_tahun }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-2">Filter Program Studi (Opsional)</label>
                        <select wire:model="prodiId" class="block w-full rounded-2xl border-slate-200 bg-slate-50 py-4 px-5 focus:bg-white focus:border-unmaris-blue focus:ring-4 focus:ring-unmaris-blue/5 text-sm font-semibold transition-all outline-none">
                            <option value="">-- Semua Program Studi --</option>
                            @foreach($prodis as $prodi)
                            <option value="{{ $prodi->id }}">{{ $prodi->nama_prodi }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Action Button --}}
                <div class="mt-12 pt-8 border-t border-slate-100 flex justify-center">
                    <button wire:click="generate"
                        wire:confirm="Sistem akan membuat tagihan baru secara massal. Lanjutkan proses?"
                        wire:loading.attr="disabled"
                        class="group relative px-16 py-4 bg-unmaris-yellow text-unmaris-blue rounded-2xl text-sm font-black shadow-2xl shadow-unmaris-yellow/20 hover:scale-105 transition-all uppercase tracking-[0.2em] disabled:opacity-50 disabled:cursor-not-allowed">
                        <span wire:loading.remove class="flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            Jalankan Generator Tagihan
                        </span>
                        <span wire:loading class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-unmaris-blue" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Proses Enqueueing...
                        </span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Hasil Report (Conditional) --}}
        @if($hasil)
        <div class="bg-white rounded-3xl shadow-xl border border-slate-200 overflow-hidden animate-in slide-in-from-bottom-4 duration-700">
            <div class="px-8 py-5 border-b border-slate-100 bg-slate-50/50">
                <h3 class="text-xs font-black text-slate-400 uppercase tracking-[0.2em]">Laporan Eksekusi Terakhir</h3>
            </div>

            <div class="p-8 lg:p-10">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
                    <div class="p-6 bg-emerald-50 rounded-2xl border border-emerald-100 text-center group hover:bg-emerald-100 transition-colors">
                        <div class="text-3xl font-black text-emerald-600 mb-1 tabular-nums">{{ $hasil['sukses'] }}</div>
                        <div class="text-[10px] text-emerald-500 font-black uppercase tracking-widest">Berhasil Dibuat</div>
                    </div>
                    <div class="p-6 bg-amber-50 rounded-2xl border border-amber-100 text-center group hover:bg-amber-100 transition-colors">
                        <div class="text-3xl font-black text-amber-600 mb-1 tabular-nums">{{ $hasil['skip'] }}</div>
                        <div class="text-[10px] text-amber-500 font-black uppercase tracking-widest">Dilewati (Sudah Ada)</div>
                    </div>
                    <div class="p-6 bg-rose-50 rounded-2xl border border-rose-100 text-center group hover:bg-rose-100 transition-colors">
                        <div class="text-3xl font-black text-rose-600 mb-1 tabular-nums">{{ count($hasil['errors']) }}</div>
                        <div class="text-[10px] text-rose-500 font-black uppercase tracking-widest">Gagal Eksekusi</div>
                    </div>
                </div>

                {{-- Detail Error List --}}
                @if(count($hasil['errors']) > 0)
                <div class="space-y-4">
                    <div class="flex items-center space-x-3 text-rose-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <h4 class="text-sm font-black uppercase tracking-widest">Log Kesalahan Sistem:</h4>
                    </div>
                    <div class="bg-slate-900 rounded-2xl p-6 overflow-hidden border border-slate-800">
                        <ul class="space-y-3 font-mono text-xs text-rose-300">
                            @foreach($hasil['errors'] as $err)
                            <li class="flex items-start">
                                <span class="mr-3 opacity-50">#</span>
                                <span>{{ $err }}</span>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    <p class="text-[11px] text-slate-400 italic font-medium">
                        *Tip: Kegagalan biasanya disebabkan karena data mahasiswa belum terhubung dengan **Skema Tarif** yang valid. Silakan periksa menu Master Tarif.
                    </p>
                </div>
                @endif
            </div>
        </div>
        @endif
    </div>


</div>