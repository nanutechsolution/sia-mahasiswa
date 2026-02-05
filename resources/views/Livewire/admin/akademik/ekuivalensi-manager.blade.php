<div class="space-y-6 animate-in fade-in duration-500">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-[#002855] uppercase tracking-tight">Penyetaraan Mata Kuliah</h1>
            <p class="text-[11px] text-slate-500 font-bold uppercase tracking-widest mt-1">Recognition Layer &bull; Kebijakan Lintas Kurikulum</p>
        </div>
        <button wire:click="$set('showForm', true)" class="px-5 py-2.5 bg-indigo-600 text-white rounded-xl font-black text-[10px] uppercase tracking-widest shadow-lg shadow-indigo-100 hover:bg-indigo-700 transition-all">
            Tambah Pemetaan Baru
        </button>
    </div>

    {{-- Context Filter --}}
    <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-4">
        <div class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
        </div>
        <div class="flex-1">
            <label class="block text-[9px] font-black text-slate-400 uppercase">Program Studi</label>
            <select wire:model.live="filterProdiId" class="w-full md:w-64 border-none p-0 font-bold text-slate-700 focus:ring-0 cursor-pointer">
                @foreach($prodis as $p) <option value="{{ $p->id }}">{{ $p->nama_prodi }}</option> @endforeach
            </select>
        </div>
    </div>

    @if (session()->has('success'))
    <div class="p-4 bg-emerald-50 border border-emerald-100 text-emerald-800 rounded-xl text-xs font-bold animate-in slide-in-from-top-2">
        {{ session('success') }}
    </div>
    @endif

    {{-- Minimal CRUD Form --}}
    @if($showForm)
    <div class="bg-white rounded-3xl shadow-xl border border-slate-200 overflow-hidden animate-in zoom-in-95 duration-200">
        <div class="px-8 py-5 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
            <h3 class="text-xs font-black text-[#002855] uppercase tracking-widest">{{ $editMode ? 'Edit Penyetaraan' : 'Buat Penyetaraan Baru' }}</h3>
            <button wire:click="resetForm" class="text-slate-400 text-xl">&times;</button>
        </div>

        <div class="p-8 space-y-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                {{-- Source: MK di Kurikulum Lama --}}
                <div class="space-y-4">
                    <label class="block text-[10px] font-black text-indigo-600 uppercase tracking-widest">Mata Kuliah Asal (Lama)</label>
                    <div class="relative" x-data="{ open: false }">
                        <input type="text" wire:model.live="searchAsal" @focus="open = true" @click.away="open = false"
                            placeholder="{{ $selectedAsalName ?: 'Cari Kode/Nama MK Lama...' }}"
                            class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm font-bold p-3 focus:ring-indigo-500 shadow-inner">

                        @if(!empty($optionsAsal))
                        <div x-show="open" class="absolute z-50 w-full bg-white shadow-2xl rounded-xl mt-1 border border-slate-100 overflow-hidden">
                            @foreach($optionsAsal as $oa)
                            <div wire:click="selectAsal('{{ $oa->id }}', '{{ $oa->nama_mk }}', '{{ $oa->kode_mk }}')" @click="open = false"
                                class="px-4 py-3 hover:bg-indigo-50 cursor-pointer border-b border-slate-50 last:border-0 transition-colors">
                                <p class="text-xs font-bold text-[#002855]">{{ $oa->nama_mk }}</p>
                                <p class="text-[9px] text-slate-400 font-mono">{{ $oa->kode_mk }} &bull; {{ $oa->sks_default }} SKS</p>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Target: MK di Kurikulum Baru --}}
                <div class="space-y-4">
                    <label class="block text-[10px] font-black text-indigo-600 uppercase tracking-widest">Mata Kuliah Tujuan (Baru)</label>
                    <div class="relative" x-data="{ open: false }">
                        <input type="text" wire:model.live="searchTujuan" @focus="open = true" @click.away="open = false"
                            placeholder="{{ $selectedTujuanName ?: 'Cari Kode/Nama MK Baru...' }}"
                            class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm font-bold p-3 focus:ring-indigo-500 shadow-inner">

                        @if(!empty($optionsTujuan))
                        <div x-show="open" class="absolute z-50 w-full bg-white shadow-2xl rounded-xl mt-1 border border-slate-100 overflow-hidden">
                            @foreach($optionsTujuan as $ot)
                            <div wire:click="selectTujuan('{{ $ot->id }}', '{{ $ot->nama_mk }}', '{{ $ot->kode_mk }}')" @click="open = false"
                                class="px-4 py-3 hover:bg-emerald-50 cursor-pointer border-b border-slate-50 last:border-0 transition-colors">
                                <p class="text-xs font-bold text-slate-800">{{ $ot->nama_mk }}</p>
                                <p class="text-[9px] text-slate-400 font-mono">{{ $ot->kode_mk }} &bull; {{ $ot->sks_default }} SKS</p>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>

                <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="md:col-span-1">
                        <label class="block text-[10px] font-black text-slate-400 uppercase mb-1">Nomor SK Penyetaraan</label>
                        <input type="text" wire:model="nomor_sk" placeholder="Ex: SK/01/TI/2026" class="w-full rounded-xl border-slate-200 text-sm font-bold">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-[10px] font-black text-slate-400 uppercase mb-1">Keterangan / Alasan</label>
                        <input type="text" wire:model="keterangan" placeholder="Ex: Penyesuaian kurikulum 2017 ke MBKM" class="w-full rounded-xl border-slate-200 text-sm">
                    </div>
                </div>
            </div>

            <div class="pt-6 border-t border-slate-100 flex justify-end gap-3">
                <button wire:click="resetForm" class="px-6 py-2.5 text-xs font-bold text-slate-400 uppercase tracking-widest">Batal</button>
                <button wire:click="save" class="px-8 py-2.5 bg-[#002855] text-white rounded-xl font-black text-[10px] uppercase tracking-[0.2em] shadow-lg hover:scale-105 transition-all">Simpan Kebijakan</button>
            </div>
        </div>
    </div>
    @endif

    {{-- Table List --}}
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">MK Asal (Lama)</th>
                    <th class="px-6 py-4 text-center text-slate-300">
                        <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">MK Tujuan (Baru)</th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($listEkuivalensi as $item)
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="px-6 py-5">
                        <div class="text-sm font-black text-[#002855]">{{ $item->mataKuliahAsal->nama_mk }}</div>
                        <div class="text-[10px] font-mono text-slate-400 mt-1 uppercase">{{ $item->mataKuliahAsal->kode_mk }} &bull; {{ $item->mataKuliahAsal->sks_default }} SKS</div>
                    </td>
                    <td class="px-6 py-5 text-center">
                        <div class="inline-flex px-2 py-0.5 rounded bg-indigo-50 text-indigo-500 text-[8px] font-black uppercase border border-indigo-100">Setara</div>
                    </td>
                    <td class="px-6 py-5">
                        <div class="text-sm font-black text-slate-800">{{ $item->mataKuliahTujuan->nama_mk }}</div>
                        <div class="text-[10px] font-mono text-slate-400 mt-1 uppercase">{{ $item->mataKuliahTujuan->kode_mk }} &bull; {{ $item->mataKuliahTujuan->sks_default }} SKS</div>
                    </td>
                    <td class="px-6 py-5 text-right space-x-3">
                        <button wire:click="edit('{{ $item->id }}')" class="text-indigo-600 font-black text-[10px] uppercase hover:underline">Edit</button>
                        <button wire:click="delete('{{ $item->id }}')" wire:confirm="Hapus pemetaan ini?" class="text-rose-500 font-black text-[10px] uppercase hover:underline">Hapus</button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-20 text-center">
                        <div class="flex flex-col items-center opacity-20">
                            <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <p class="text-sm font-black uppercase tracking-widest">Belum ada data penyetaraan</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-100">
            {{ $listEkuivalensi->links() }}
        </div>
    </div>
</div>