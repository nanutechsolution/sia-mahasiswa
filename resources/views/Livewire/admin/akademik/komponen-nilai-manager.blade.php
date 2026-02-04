<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-[#002855]">Manajemen Bobot Nilai</h1>
            <p class="text-slate-500 text-sm mt-1">Konfigurasi persentase penilaian (Aktif, Tugas, UTS, UAS) per Kurikulum.</p>
        </div>
    </div>

    {{-- Navigasi Tab --}}
    <div class="flex flex-wrap gap-2 p-1 bg-white rounded-2xl shadow-sm border border-slate-200 w-fit">
        <button wire:click="switchTab('config')" class="px-6 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest transition-all {{ $activeTab == 'config' ? 'bg-[#002855] text-white shadow-lg' : 'text-slate-500 hover:bg-slate-50' }}">
            Setting Per Kurikulum
        </button>
        <button wire:click="switchTab('master')" class="px-6 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest transition-all {{ $activeTab == 'master' ? 'bg-[#002855] text-white shadow-lg' : 'text-slate-500 hover:bg-slate-50' }}">
            Master Komponen
        </button>
    </div>

    @if (session()->has('success'))
        <div class="bg-emerald-50 border border-emerald-200 p-4 rounded-xl text-emerald-800 text-sm font-bold flex items-center shadow-sm animate-in fade-in">
            <svg class="w-5 h-5 mr-3 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        {{-- KOLOM LIST DATA --}}
        <div class="lg:col-span-8 space-y-6">
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
                {{-- Search Area --}}
                <div class="p-4 bg-slate-50/50 border-b flex items-center justify-between">
                    <div class="relative w-64">
                        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari..." class="w-full rounded-xl border-slate-200 text-sm py-2 pl-10 pr-4 focus:ring-[#002855] focus:border-[#002855]">
                        <svg class="w-4 h-4 absolute left-3 top-2.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    @if($activeTab == 'master')
                        <button wire:click="createMaster" class="bg-indigo-600 text-white px-4 py-2 rounded-xl font-bold text-xs uppercase tracking-widest shadow-md hover:bg-indigo-700 transition-all">Tambah Komponen</button>
                    @endif
                </div>

                <div class="overflow-x-auto">
                    @if($activeTab == 'master')
                        <table class="min-w-full divide-y divide-slate-100">
                            <thead class="bg-[#002855] text-white">
                                <tr>
                                    <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-widest">ID</th>
                                    <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-widest">Nama Komponen</th>
                                    <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-widest">Slug</th>
                                    <th class="px-6 py-4 text-center text-[10px] font-black uppercase tracking-widest">Status</th>
                                    <th class="px-6 py-4 text-right text-[10px] font-black uppercase tracking-widest">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @foreach($masterKomponens as $m)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4 text-sm font-bold text-slate-400">{{ $m->id }}</td>
                                    <td class="px-6 py-4 text-sm font-black text-slate-800">{{ $m->nama_komponen }}</td>
                                    <td class="px-6 py-4 text-xs font-mono text-slate-500">{{ $m->slug }}</td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="px-2 py-0.5 rounded-full text-[9px] font-black {{ $m->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                            {{ $m->is_active ? 'AKTIF' : 'NON' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <button wire:click="editMaster({{ $m->id }})" class="text-indigo-600 font-bold text-[10px] uppercase hover:underline">Edit</button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <table class="min-w-full divide-y divide-slate-100">
                            <thead class="bg-[#002855] text-white">
                                <tr>
                                    <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-widest">Kurikulum / Prodi</th>
                                    <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-widest">Komposisi Bobot</th>
                                    <th class="px-6 py-4 text-right text-[10px] font-black uppercase tracking-widest">Opsi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @foreach($kurikulums as $k)
                                @php 
                                    $currentWeights = DB::table('kurikulum_komponen_nilai as kkn')
                                        ->join('ref_komponen_nilai as rk', 'kkn.komponen_id', '=', 'rk.id')
                                        ->where('kkn.kurikulum_id', $k->id)
                                        ->select('rk.nama_komponen', 'kkn.bobot_persen')
                                        ->get();
                                @endphp
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-black text-slate-800">{{ $k->nama_kurikulum }}</div>
                                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">{{ $k->prodi->nama_prodi }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-wrap gap-1">
                                            @forelse($currentWeights as $cw)
                                                <span class="bg-indigo-50 text-indigo-700 text-[9px] font-black px-1.5 py-0.5 rounded border border-indigo-100">
                                                    {{ $cw->nama_komponen }}: {{ number_format($cw->bobot_persen, 0) }}%
                                                </span>
                                            @empty
                                                <span class="text-[10px] text-rose-400 font-bold italic">Belum Diatur (Default E)</span>
                                            @endforelse
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <button wire:click="selectKurikulum({{ $k->id }})" class="inline-flex items-center px-3 py-1 bg-white border border-[#002855] text-[#002855] rounded-lg text-[9px] font-black uppercase tracking-widest hover:bg-[#002855] hover:text-white transition-all">
                                            Atur Bobot
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
                <div class="p-4 bg-slate-50">
                    {{ $activeTab == 'master' ? $masterKomponens->links() : $kurikulums->links() }}
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: FORM EDITOR --}}
        <div class="lg:col-span-4">
            @if($showForm)
                <div class="bg-white rounded-[2rem] shadow-xl border border-slate-200 overflow-hidden sticky top-6 animate-in slide-in-from-right-4 z-10">
                    <div class="bg-[#002855] px-8 py-6 text-white flex justify-between items-center">
                        <h3 class="text-sm font-black uppercase tracking-[0.2em]">Editor Data</h3>
                        <button wire:click="resetForm" class="text-white/50 hover:text-white">&times;</button>
                    </div>

                    @if($activeTab == 'master')
                        <form wire:submit.prevent="saveMaster" class="p-8 space-y-6">
                            <div>
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Nama Komponen</label>
                                <input type="text" wire:model="nama_komponen" class="w-full rounded-xl border-slate-200 bg-slate-50 font-bold text-slate-700 py-2.5 pl-4 focus:ring-2 focus:ring-[#fcc000] outline-none">
                            </div>
                            <div class="flex items-center gap-3">
                                <input type="checkbox" wire:model="is_active" class="h-5 w-5 text-[#002855] rounded border-slate-300 focus:ring-[#fcc000]">
                                <label class="text-sm font-bold text-slate-600">Aktifkan Komponen</label>
                            </div>
                            <button type="submit" class="w-full py-3 bg-[#002855] text-white rounded-xl font-black text-xs uppercase tracking-[0.2em] shadow-lg shadow-indigo-900/20 hover:scale-105 transition-all">Simpan Master</button>
                        </form>
                    @else
                        <div class="p-8 space-y-6">
                            <div class="bg-indigo-50 p-4 rounded-2xl border border-indigo-100">
                                <p class="text-[10px] font-black text-indigo-400 uppercase mb-1">Kurikulum Target</p>
                                <p class="text-xs font-black text-indigo-900">{{ \App\Domains\Akademik\Models\Kurikulum::find($selectedKurikulumId)->nama_kurikulum ?? '' }}</p>
                            </div>

                            <div class="space-y-4">
                                <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Atur Persentase</h4>
                                <p class="text-[9px] text-slate-400 italic mb-2">*Isi 0 untuk komponen yang tidak digunakan prodi ini.</p>
                                @foreach($allMaster as $m)
                                    <div class="flex items-center justify-between gap-4" wire:key="weight-{{ $m->id }}">
                                        <div class="flex flex-col">
                                            <span class="text-xs font-bold text-slate-600">{{ $m->nama_komponen }}</span>
                                            @if(($weights[$m->id] ?? 0) == 0)
                                                <span class="text-[8px] font-black text-slate-300 uppercase tracking-tighter">Tidak Digunakan</span>
                                            @endif
                                        </div>
                                        <div class="w-24 relative">
                                            <input type="number" wire:model.live="weights.{{ $m->id }}" class="w-full text-right rounded-lg border-slate-200 bg-slate-50 text-xs font-black py-1.5 pr-6 focus:ring-1 focus:ring-[#fcc000] outline-none">
                                            <span class="absolute right-2 top-1.5 text-[10px] font-bold text-slate-400">%</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            {{-- UI UX FIX: Perhitungan Total yang Aman --}}
                            @php
                                $currentTotal = collect($weights)->map(fn($v) => is_numeric($v) ? (float)$v : 0)->sum();
                                $isValid = $currentTotal == 100;
                            @endphp

                            <div class="pt-4 border-t border-slate-100">
                                <div class="flex justify-between items-center mb-2">
                                    <div class="text-left">
                                        <p class="text-[9px] font-black text-slate-400 uppercase">Total Bobot</p>
                                        <p class="text-2xl font-black {{ $isValid ? 'text-emerald-500' : 'text-rose-500' }}">
                                            {{ $currentTotal }}%
                                        </p>
                                    </div>
                                    
                                    <button wire:click="saveWeights" 
                                        @if(!$isValid) disabled @endif
                                        class="px-8 py-3 bg-[#002855] text-white rounded-xl font-black text-xs uppercase tracking-widest shadow-lg shadow-indigo-900/20 hover:bg-black transition-all disabled:opacity-30 disabled:cursor-not-allowed">
                                        Terapkan
                                    </button>
                                </div>

                                {{-- Progress Bar untuk Visualisasi --}}
                                <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                    <div class="h-full transition-all duration-500 {{ $isValid ? 'bg-emerald-500' : ($currentTotal > 100 ? 'bg-rose-500' : 'bg-amber-400') }}" 
                                        style="width: {{ min($currentTotal, 100) }}%"></div>
                                </div>
                                
                                @if(!$isValid)
                                    <p class="text-[9px] font-bold text-rose-500 text-center mt-2 animate-pulse">
                                        {{ $currentTotal > 100 ? 'Bobot melebihi 100%! Kurangi beberapa poin.' : 'Total harus tepat 100% untuk dapat disimpan.' }}
                                    </p>
                                @endif
                            </div>
                            @error('total_weight') <p class="text-[10px] text-rose-500 font-bold text-center mt-2">{{ $message }}</p> @enderror
                        </div>
                    @endif
                </div>
            @else
                <div class="bg-indigo-50/50 rounded-[2rem] p-8 border-2 border-dashed border-indigo-100 text-center sticky top-6">
                    <div class="w-16 h-16 bg-white rounded-2xl shadow-sm flex items-center justify-center text-2xl mx-auto mb-4 text-[#002855]">⚖️</div>
                    <h3 class="text-sm font-black text-[#002855] uppercase tracking-tight">Konfigurasi Penilaian</h3>
                    <p class="text-xs text-indigo-400 mt-2 leading-relaxed">
                        Pilih kurikulum untuk mengatur komposisi nilai akhir atau gunakan master komponen untuk menambah parameter penilaian baru.
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>