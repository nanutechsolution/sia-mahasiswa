<div class="space-y-6">
    <!-- Header Back -->
    <div class="flex items-center justify-between">
        <button wire:click="backToList" class="flex items-center text-sm font-bold text-slate-500 hover:text-[#002855] transition-colors">
            <svg class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M17 10a.75.75 0 01-.75.75H5.612l4.158 3.96a.75.75 0 11-1.04 1.08l-5.5-5.25a.75.75 0 010-1.08l5.5-5.25a.75.75 0 111.04 1.08L5.612 9.25H16.25A.75.75 0 0117 10z" clip-rule="evenodd" /></svg>
            Kembali ke Daftar
        </button>
        <div class="text-right">
            <h2 class="text-xl font-black text-[#002855] uppercase tracking-tight">{{ $selectedKurikulum->nama_kurikulum }}</h2>
            <p class="text-sm font-medium text-slate-500">{{ $selectedKurikulum->prodi->nama_prodi }}</p>
        </div>
    </div>

    <!-- Form Add MK -->
    <div class="bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden animate-in zoom-in-95 duration-200">
        <div class="px-8 py-5 border-b border-slate-100 bg-slate-50 flex items-center gap-2">
            <h3 class="text-sm font-bold text-[#002855] uppercase tracking-wider flex items-center gap-2">
                <svg class="w-5 h-5 text-[#fcc000]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                Tambah Mata Kuliah ke Struktur
            </h3>
        </div>
        
        <div class="p-8 space-y-8">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-start">
                <!-- Baris 1: MK, Semester, Sifat -->
                <div class="md:col-span-5">
                    <label class="block text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2">Pilih Mata Kuliah *</label>
                    <select wire:model.live="mk_id_to_add" class="block w-full rounded-xl border-slate-200 bg-slate-50 text-slate-900 py-2.5 px-4 text-sm font-bold focus:outline-none focus:ring-2 focus:ring-[#fcc000] focus:border-transparent transition-all">
                        <option value="">-- Pilih MK dari Master --</option>
                        @foreach($availableMks as $mk)
                            <option value="{{ $mk->id }}">{{ $mk->kode_mk }} - {{ $mk->nama_mk }}</option>
                        @endforeach
                    </select>
                    @error('mk_id_to_add') <span class="text-rose-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2">Semester *</label>
                    <input type="number" wire:model.live="semester_paket_to_add" min="1" max="8" class="block w-full rounded-xl border-slate-200 bg-slate-50 text-slate-900 py-2.5 px-4 text-sm font-bold text-center focus:outline-none focus:ring-2 focus:ring-[#fcc000] focus:border-transparent transition-all">
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2">Sifat *</label>
                    <select wire:model="sifat_mk_to_add" class="block w-full rounded-xl border-slate-200 bg-slate-50 text-slate-900 py-2.5 px-4 text-sm font-bold focus:outline-none focus:ring-2 focus:ring-[#fcc000] focus:border-transparent transition-all">
                        <option value="W">Wajib</option>
                        <option value="P">Pilihan</option>
                    </select>
                </div>

                <div class="md:col-span-3 text-right pt-6">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Total Beban SKS</label>
                    <span class="text-3xl font-black text-[#002855]">
                        {{ (int)$sks_tatap_muka_to_add + (int)$sks_praktek_to_add + (int)$sks_lapangan_to_add }}
                    </span>
                    <span class="text-xs font-bold text-slate-400">SKS</span>
                </div>

                <!-- Baris 2: Rincian SKS -->
                <div class="md:col-span-6 bg-[#002855]/5 p-5 rounded-2xl border border-[#002855]/10">
                    <label class="block text-xs font-black text-[#002855] uppercase tracking-widest mb-3">Bobot SKS (Dapat Diedit)</label>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase text-center mb-1">Tatap Muka</label>
                            <input type="number" wire:model.live="sks_tatap_muka_to_add" min="0" class="block w-full rounded-xl border-slate-200 bg-white text-slate-900 py-2 text-sm font-bold text-center focus:outline-none focus:ring-2 focus:ring-[#002855] focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase text-center mb-1">Praktek</label>
                            <input type="number" wire:model.live="sks_praktek_to_add" min="0" class="block w-full rounded-xl border-slate-200 bg-white text-slate-900 py-2 text-sm font-bold text-center focus:outline-none focus:ring-2 focus:ring-[#002855] focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase text-center mb-1">Lapangan</label>
                            <input type="number" wire:model.live="sks_lapangan_to_add" min="0" class="block w-full rounded-xl border-slate-200 bg-white text-slate-900 py-2 text-sm font-bold text-center focus:outline-none focus:ring-2 focus:ring-[#002855] focus:border-transparent">
                        </div>
                    </div>
                    <p class="text-[10px] text-slate-500 mt-2 italic text-center">*Biarkan 0 untuk menggunakan SKS default dari Master MK.</p>
                </div>

                <!-- Baris 2: Prasyarat & Tombol -->
                <div class="md:col-span-6 flex flex-col gap-4">
                    <div class="flex gap-4">
                        <div class="flex-1">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Mata Kuliah Syarat (Lulus)</label>
                            <select wire:model="prasyarat_mk_id" class="block w-full rounded-xl border-slate-200 bg-slate-50 text-slate-900 py-2.5 px-4 text-sm font-bold focus:outline-none focus:ring-2 focus:ring-[#fcc000] focus:border-transparent transition-all">
                                <option value="">-- Tanpa Syarat --</option>
                                @foreach($prerequisiteOptions as $pre)
                                    <option value="{{ $pre->id }}">{{ $pre->nama_mk }} (Smt {{ $pre->pivot->semester_paket }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="w-32">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Nilai Min.</label>
                            <select wire:model="min_nilai_prasyarat_to_add" class="block w-full rounded-xl border-slate-200 bg-slate-50 text-slate-900 py-2.5 px-4 text-sm font-bold focus:outline-none focus:ring-2 focus:ring-[#fcc000] focus:border-transparent transition-all text-center">
                                @foreach($availableGrades as $grade)
                                    <option value="{{ $grade->huruf }}">{{ $grade->huruf }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <p class="text-[10px] text-slate-400 italic">*Hanya MK semester sebelumnya (1 - {{ max(1, (int)$semester_paket_to_add - 1) }})</p>
                    
                    <div class="mt-auto pt-2 text-right">
                        <button wire:click="addMk" class="inline-flex items-center justify-center px-6 py-3 bg-[#002855] text-white rounded-xl font-bold text-sm shadow-lg shadow-indigo-900/20 hover:bg-[#001a38] hover:scale-105 transition-all w-full md:w-auto">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                            Simpan MK ke Struktur
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Struktur -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-[#002855] text-white">
                    <tr>
                        <th class="px-6 py-4 text-left text-[10px] font-bold uppercase tracking-widest">Smt</th>
                        <th class="px-6 py-4 text-left text-[10px] font-bold uppercase tracking-widest">Kode</th>
                        <th class="px-6 py-4 text-left text-[10px] font-bold uppercase tracking-widest">Mata Kuliah</th>
                        <th class="px-6 py-4 text-center text-[10px] font-bold uppercase tracking-widest">SKS (T/P/L)</th>
                        <th class="px-6 py-4 text-center text-[10px] font-bold uppercase tracking-widest">Sifat</th>
                        <th class="px-6 py-4 text-left text-[10px] font-bold uppercase tracking-widest">Prasyarat</th>
                        <th class="px-6 py-4 text-right text-[10px] font-bold uppercase tracking-widest">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($selectedKurikulum->mataKuliahs as $mk)
                    <tr class="hover:bg-slate-50/80 transition-colors group">
                        <td class="px-6 py-4 whitespace-nowrap align-top">
                             <div class="font-black text-xl text-[#002855]">{{ $mk->pivot->semester_paket }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap align-top">
                             <div class="font-mono text-xs font-bold text-[#002855] bg-indigo-50 px-2 py-0.5 rounded w-fit">{{ $mk->kode_mk }}</div>
                        </td>
                        <td class="px-6 py-4 align-top">
                            <div class="text-sm font-bold text-slate-800">{{ $mk->nama_mk }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center align-top">
                            <span class="text-lg font-black text-slate-700 block">{{ $mk->pivot->sks_tatap_muka + $mk->pivot->sks_praktek + $mk->pivot->sks_lapangan }}</span>
                            <span class="text-[10px] font-bold text-slate-400 bg-slate-100 px-2 py-0.5 rounded-full">
                                T:{{ $mk->pivot->sks_tatap_muka }} P:{{ $mk->pivot->sks_praktek }} L:{{ $mk->pivot->sks_lapangan }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center align-top">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wide {{ $mk->pivot->sifat_mk == 'W' ? 'bg-[#002855]/10 text-[#002855]' : 'bg-[#fcc000]/20 text-slate-700' }}">
                                {{ $mk->pivot->sifat_mk == 'W' ? 'Wajib' : 'Pilihan' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-xs align-top">
                            @if($mk->pivot->prasyarat_mk_id)
                                @php
                                    $prasyarat = $selectedKurikulum->mataKuliahs->firstWhere('id', $mk->pivot->prasyarat_mk_id);
                                @endphp
                                @if($prasyarat)
                                    <span class="inline-flex items-center px-2 py-1 rounded-lg bg-rose-50 text-rose-700 border border-rose-100 font-bold gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                                        {{ $prasyarat->kode_mk }} (Min: {{ $mk->pivot->min_nilai_prasyarat }})
                                    </span>
                                @else
                                    <span class="text-xs text-slate-400 italic">ID: {{ $mk->pivot->prasyarat_mk_id }} (?)</span>
                                @endif
                            @else
                                <span class="text-slate-300">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right align-top">
                            <button wire:click="removeMk({{ $mk->id }})" wire:confirm="Hapus MK ini dari kurikulum?" class="p-2 text-rose-600 hover:bg-rose-50 rounded-lg transition-colors group-hover:opacity-100 opacity-60" title="Hapus">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>