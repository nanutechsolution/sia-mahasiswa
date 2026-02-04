<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-[#002855]">Master Indikator Mutu</h1>
            <p class="text-slate-500 text-sm mt-1">Definisikan indikator kinerja (IKU/IKT) untuk setiap standar mutu.</p>
        </div>
        @if(!$showForm)
        <button wire:click="create" class="inline-flex items-center px-5 py-2.5 bg-[#002855] text-white rounded-xl font-bold text-sm shadow-lg hover:bg-black transition-all">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" /></svg>
            Tambah Indikator
        </button>
        @endif
    </div>

    @if (session()->has('success'))
        <div class="bg-emerald-50 border border-emerald-200 p-4 rounded-xl text-emerald-800 text-sm font-bold flex items-center shadow-sm animate-in fade-in">
            <svg class="w-5 h-5 mr-3 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-rose-50 border border-rose-200 p-4 rounded-xl text-rose-800 text-sm font-bold flex items-center shadow-sm animate-in fade-in">
            <svg class="w-5 h-5 mr-3 text-rose-500" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
            {{ session('error') }}
        </div>
    @endif

    {{-- Form Section --}}
    @if($showForm)
    <div class="bg-white rounded-3xl shadow-xl border border-slate-200 overflow-hidden animate-in zoom-in-95 duration-200">
        <div class="px-8 py-5 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
            <h3 class="text-sm font-bold text-[#002855] uppercase tracking-wider">
                {{ $editMode ? 'Edit Indikator' : 'Buat Indikator Baru' }}
            </h3>
            <button wire:click="batal" class="text-slate-400 hover:text-slate-600">&times;</button>
        </div>
        
        <form wire:submit.prevent="save" class="p-8 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Pilih Standar Induk</label>
                    <select wire:model="standar_id" class="w-full rounded-xl border-slate-200 bg-slate-50 text-slate-900 py-2.5 pl-4 text-sm font-bold focus:ring-[#002855]">
                        <option value="">-- Pilih Standar Mutu --</option>
                        @foreach($standars as $s)
                            <option value="{{ $s->id }}">[{{ $s->kode_standar }}] {{ $s->nama_standar }}</option>
                        @endforeach
                    </select>
                    @error('standar_id') <span class="text-rose-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Nama Indikator</label>
                    <input type="text" wire:model="nama_indikator" placeholder="Contoh: Rata-rata IPK Lulusan" class="w-full rounded-xl border-slate-200 bg-slate-50 text-slate-900 py-2.5 pl-4 text-sm font-bold focus:ring-[#002855]">
                    @error('nama_indikator') <span class="text-rose-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Bobot Kontribusi (%)</label>
                    <input type="number" step="0.01" wire:model="bobot" class="w-full rounded-xl border-slate-200 bg-slate-50 text-slate-900 py-2.5 pl-4 text-sm font-bold focus:ring-[#002855]">
                    @error('bobot') <span class="text-rose-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Sumber Data (SIAKAD Mapping)</label>
                    <input type="text" wire:model="sumber_data_siakad" placeholder="Contoh: krs_detail_nilai" class="w-full rounded-xl border-slate-200 bg-slate-50 text-slate-900 py-2.5 pl-4 text-sm font-bold focus:ring-[#002855]">
                    <p class="text-[9px] text-slate-400 mt-1 italic">*Digunakan untuk otomatisasi penarikan data capaian.</p>
                </div>
            </div>

            <div class="pt-6 border-t border-slate-100 flex justify-end gap-3">
                <button type="button" wire:click="batal" class="px-6 py-2.5 text-sm font-bold text-slate-500 hover:bg-slate-100 rounded-xl transition-all">Batal</button>
                <button type="submit" class="px-8 py-2.5 bg-[#002855] text-white rounded-xl text-sm font-bold shadow-lg hover:bg-black transition-all">
                    Simpan Indikator
                </button>
            </div>
        </form>
    </div>
    @endif

    {{-- Data Table --}}
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-4 bg-slate-50/50 border-b flex items-center gap-4">
            <div class="relative flex-1">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari Indikator..." class="w-full pl-10 pr-4 py-2 rounded-xl border-slate-200 text-sm focus:ring-[#002855] transition-shadow outline-none font-bold text-slate-700">
                <svg class="w-4 h-4 absolute left-3 top-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-[#002855] text-white">
                    <tr>
                        <th class="px-6 py-4 text-left text-[10px] font-bold uppercase tracking-widest w-32">Standar</th>
                        <th class="px-6 py-4 text-left text-[10px] font-bold uppercase tracking-widest">Indikator</th>
                        <th class="px-6 py-4 text-center text-[10px] font-bold uppercase tracking-widest">Bobot</th>
                        <th class="px-6 py-4 text-left text-[10px] font-bold uppercase tracking-widest">Sumber Data</th>
                        <th class="px-6 py-4 text-right text-[10px] font-bold uppercase tracking-widest">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 bg-white">
                    @forelse($indikators as $i)
                    <tr class="hover:bg-slate-50/80 transition-colors group">
                        <td class="px-6 py-4 align-top">
                            <span class="font-mono text-[10px] font-black text-[#002855] bg-indigo-50 px-2 py-0.5 rounded border border-indigo-100">{{ $i->kode_standar }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-bold text-slate-800">{{ $i->nama_indikator }}</div>
                            <div class="text-[10px] text-slate-400 mt-0.5 font-medium">{{ $i->nama_standar }}</div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-2 py-1 rounded bg-slate-100 text-[#002855] text-xs font-black">{{ $i->bobot }}%</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-[10px] font-mono font-bold text-slate-500 uppercase">{{ $i->sumber_data_siakad ?: 'Input Manual' }}</span>
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <button wire:click="edit({{ $i->id }})" class="text-[#002855] hover:text-indigo-600 font-bold text-[10px] uppercase transition-colors">Edit</button>
                            <button wire:click="delete({{ $i->id }})" wire:confirm="Hapus indikator ini?" class="text-rose-500 hover:text-rose-700 font-bold text-[10px] uppercase transition-colors">Hapus</button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-6 py-16 text-center text-slate-400 italic">Data tidak ditemukan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
            {{ $indikators->links() }}
        </div>
    </div>
</div>