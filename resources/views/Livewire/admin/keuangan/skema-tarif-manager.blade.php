<div>
    {{-- SEO & Header --}}
    <x-slot name="title">Manajemen Skema Tarif</x-slot>
    <x-slot name="header">Konfigurasi Skema Biaya</x-slot>

    <div class="space-y-8">
        {{-- Top Toolbar --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <p class="text-slate-500 text-sm">Kelola paket standarisasi biaya kuliah berdasarkan kombinasi Angkatan, Program Studi, dan Kelas.</p>
            </div>

            <button wire:click="create"
                class="inline-flex items-center px-6 py-3 bg-unmaris-yellow text-unmaris-blue rounded-xl font-bold text-sm shadow-lg shadow-unmaris-yellow/20 hover:scale-105 transition-all">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" />
                </svg>
                Buat Skema Baru
            </button>
        </div>

        @if (session()->has('success'))
        <div class="bg-emerald-50 border border-emerald-100 p-4 rounded-xl text-emerald-800 text-sm flex items-center animate-in fade-in duration-300 shadow-sm">
            <svg class="w-5 h-5 mr-3 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            <span class="font-bold">{{ session('success') }}</span>
        </div>
        @endif

        {{-- Grid Skema --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($skemas as $skema)
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden flex flex-col group hover:shadow-xl hover:shadow-slate-200/50 transition-all duration-300">
                <div class="p-6 lg:p-8 flex-1">
                    {{-- Badge & Label --}}
                    <div class="flex items-center justify-between mb-5">
                        <span class="px-3 py-1 bg-unmaris-blue/5 text-unmaris-blue text-[10px] font-black uppercase tracking-widest rounded-lg border border-unmaris-blue/10">
                            Angkatan {{ $skema->angkatan_id }}
                        </span>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">ID: {{ $skema->id }}</span>
                    </div>

                    <h3 class="text-lg font-black text-slate-800 mb-1 leading-tight group-hover:text-unmaris-blue transition-colors">
                        {{ $skema->nama_skema }}
                    </h3>
                    <p class="text-[11px] font-bold text-unmaris-gold uppercase tracking-widest">
                        Program {{ $skema->programKelas->nama_program ?? 'Reguler' }}
                    </p>

                    {{-- Rincian Biaya Singkat --}}
                    <div class="mt-8 space-y-3">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] block mb-2">Komponen Utama</label>
                        @foreach($skema->details->take(3) as $det)
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-slate-500 font-medium truncate w-40">{{ $det->komponenBiaya->nama_komponen ?? '-' }}</span>
                            <span class="font-bold text-slate-700 tabular-nums">Rp {{ number_format($det->nominal, 0, ',', '.') }}</span>
                        </div>
                        @endforeach

                        @if($skema->details->count() > 3)
                        <div class="flex items-center justify-center pt-2">
                            <span class="px-3 py-1 bg-slate-50 text-slate-400 text-[9px] font-black uppercase rounded-full border border-slate-100">
                                + {{ $skema->details->count() - 3 }} Komponen Lainnya
                            </span>
                        </div>
                        @endif
                    </div>

                    {{-- Total Section --}}
                    <div class="mt-8 pt-6 border-t border-dashed border-slate-200 flex justify-between items-end">
                        <div>
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block">Total Tagihan</span>
                            <span class="text-xl font-black text-unmaris-blue tabular-nums">
                                Rp {{ number_format($skema->details->sum('nominal'), 0, ',', '.') }}
                            </span>
                        </div>
                        <div class="w-10 h-10 bg-emerald-50 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="bg-slate-50/80 px-6 py-4 border-t border-slate-100 flex justify-between items-center">
                    <button wire:click="delete({{ $skema->id }})" wire:confirm="Hapus skema tarif ini secara permanen?"
                        class="text-xs font-bold text-rose-400 hover:text-rose-600 transition-colors uppercase tracking-tighter">
                        Hapus
                    </button>
                    <button wire:click="edit({{ $skema->id }})"
                        class="px-5 py-2 bg-white border border-slate-200 rounded-xl text-xs font-bold text-unmaris-blue hover:bg-unmaris-blue hover:text-white transition-all shadow-sm">
                        Edit Rincian
                    </button>
                </div>
            </div>
            @empty
            <div class="md:col-span-3 py-20 text-center">
                <div class="max-w-xs mx-auto text-slate-400">
                    <svg class="w-16 h-16 mx-auto mb-4 opacity-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <p class="font-bold text-sm italic">Belum ada skema tarif yang dibuat</p>
                    <p class="text-[11px] mt-1 text-slate-400">Gunakan tombol diatas untuk mulai membuat paket biaya.</p>
                </div>
            </div>
            @endforelse
        </div>

        <div class="pt-6">
            {{ $skemas->links() }}
        </div>
    </div>
</div>