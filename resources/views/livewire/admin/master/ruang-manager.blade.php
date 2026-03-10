<div class="space-y-6 max-w-[1600px] mx-auto p-4 md:p-8 animate-in fade-in duration-500">
    
    {{-- Header --}}
    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6">
        <div class="space-y-2">
            <h1 class="text-3xl font-black text-[#002855] tracking-tight flex items-center gap-3">
                <div class="w-12 h-12 bg-[#002855] rounded-2xl flex items-center justify-center text-[#fcc000] shadow-xl shadow-blue-900/20">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                </div>
                Master Ruangan
            </h1>
            <p class="text-slate-400 font-medium text-sm ml-1 uppercase tracking-widest italic">Kelola Data Fasilitas & Kapasitas Kelas</p>
        </div>
        
        @if(!$showForm)
        <button wire:click="create" class="px-8 py-4 bg-[#fcc000] text-[#002855] rounded-2xl font-black text-xs uppercase tracking-[0.2em] hover:bg-[#ffca28] hover:-translate-y-1 transition-all shadow-xl shadow-amber-500/20 flex items-center gap-3">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
            Tambah Ruangan
        </button>
        @endif
    </div>

    {{-- Filter & Search Bar --}}
    @if(!$showForm)
    <div class="bg-white p-3 shadow-sm rounded-[2.5rem] border border-slate-200 flex items-center max-w-md">
        <div class="flex items-center w-full bg-slate-50 rounded-[1.8rem] px-4 py-3 border border-slate-100 focus-within:ring-2 focus-within:ring-indigo-100 transition-all">
            <svg class="h-5 w-5 text-slate-400 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari kode atau nama ruang..." class="flex-1 border-none text-sm font-bold text-slate-700 bg-transparent focus:ring-0 placeholder:text-slate-400 placeholder:font-medium">
        </div>
    </div>

    {{-- Main Table --}}
    <div class="bg-white rounded-[3rem] shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50/50">
                    <tr>
                        <th class="px-8 py-6 text-left text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Kode & Nama Ruang</th>
                        <th class="px-6 py-6 text-center text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Kapasitas</th>
                        <th class="px-6 py-6 text-center text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Status</th>
                        <th class="px-8 py-6 text-right text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($ruangan as $r)
                    <tr class="hover:bg-slate-50/80 transition-all group">
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-indigo-600 bg-indigo-50 border border-indigo-100 shadow-sm font-black text-xs">
                                    {{ substr($r->kode_ruang, 0, 1) }}
                                </div>
                                <div>
                                    <h4 class="text-sm font-black text-[#002855] uppercase tracking-tight">{{ $r->nama_ruang }}</h4>
                                    <p class="text-[10px] font-bold text-slate-400 mt-1 uppercase tracking-widest">{{ $r->kode_ruang }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-6 text-center align-middle">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-slate-50 border border-slate-200 rounded-lg text-xs font-black text-slate-600">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                                {{ $r->kapasitas }} <span class="text-[9px] font-bold text-slate-400 ml-1">Mhs</span>
                            </span>
                        </td>
                        <td class="px-6 py-6 text-center align-middle">
                            <button wire:click="toggleActive('{{ $r->id }}')" class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest transition-colors {{ $r->is_active ? 'bg-emerald-50 text-emerald-600 hover:bg-emerald-100' : 'bg-rose-50 text-rose-500 hover:bg-rose-100' }}">
                                {{ $r->is_active ? 'AKTIF' : 'NON-AKTIF' }}
                            </button>
                        </td>
                        <td class="px-8 py-6 text-right align-middle">
                            <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <button wire:click="edit('{{ $r->id }}')" class="p-2.5 text-indigo-600 bg-indigo-50 hover:bg-indigo-100 rounded-xl transition-colors" title="Edit"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg></button>
                                <button wire:click="delete('{{ $r->id }}')" wire:confirm="Hapus permanen ruangan ini?" class="p-2.5 text-rose-500 bg-rose-50 hover:bg-rose-100 rounded-xl transition-colors" title="Hapus"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg></button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-24 text-center">
                            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 grayscale opacity-40 text-4xl">🏢</div>
                            <p class="text-xs font-black text-slate-400 uppercase tracking-[0.2em]">Data Ruangan Belum Tersedia</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-8 py-5 border-t border-slate-100 bg-slate-50/50">
            {{ $ruangan->links() }}
        </div>
    </div>
    @endif

    {{-- CREATE / EDIT MODAL (Slide-in / Centered Layout) --}}
    @if($showForm)
    <div class="bg-white rounded-[3rem] shadow-2xl border border-slate-100 overflow-hidden animate-in slide-in-from-bottom-10 duration-500 max-w-3xl mx-auto">
        <div class="px-10 py-8 border-b border-slate-50 bg-slate-50/50 flex justify-between items-center">
            <div>
                <h3 class="text-xl font-black text-[#002855] uppercase tracking-tight italic">{{ $editMode ? 'Edit Ruangan' : 'Ruangan Baru' }}</h3>
                <p class="text-[10px] font-bold text-slate-400 mt-1 uppercase tracking-widest">Detail Fasilitas Kampus</p>
            </div>
            <button wire:click="$set('showForm', false)" class="text-slate-400 hover:text-rose-500 transition-colors bg-white p-2 rounded-full shadow-sm"><svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>

        <div class="p-10 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Kode Ruang</label>
                    <input type="text" wire:model="kode_ruang" class="w-full rounded-2xl border-slate-200 bg-slate-50 py-4 px-5 text-sm font-bold text-[#002855] uppercase focus:ring-[#fcc000] focus:border-[#fcc000]" placeholder="Misal: A.101">
                    @error('kode_ruang') <span class="text-rose-500 text-[10px] font-bold mt-1 block ml-1">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Kapasitas Maksimal (Mhs)</label>
                    <input type="number" wire:model="kapasitas" class="w-full rounded-2xl border-slate-200 bg-slate-50 py-4 px-5 text-sm font-bold text-[#002855] focus:ring-[#fcc000] focus:border-[#fcc000]">
                    @error('kapasitas') <span class="text-rose-500 text-[10px] font-bold mt-1 block ml-1">{{ $message }}</span> @enderror
                </div>
            </div>

            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Nama Lengkap Ruangan</label>
                <input type="text" wire:model="nama_ruang" class="w-full rounded-2xl border-slate-200 bg-slate-50 py-4 px-5 text-sm font-bold text-[#002855] focus:ring-[#fcc000] focus:border-[#fcc000]" placeholder="Misal: Ruang Kelas Teori A.1">
                @error('nama_ruang') <span class="text-rose-500 text-[10px] font-bold mt-1 block ml-1">{{ $message }}</span> @enderror
            </div>

            <div class="pt-4 flex items-center gap-3">
                <input type="checkbox" wire:model="is_active" id="is_active_toggle" class="w-5 h-5 rounded border-slate-300 text-[#002855] focus:ring-[#fcc000] cursor-pointer">
                <label for="is_active_toggle" class="text-sm font-bold text-slate-700 cursor-pointer select-none">Ruangan ini aktif dan dapat digunakan untuk penjadwalan.</label>
            </div>
        </div>

        <div class="px-10 py-6 bg-slate-50 border-t border-slate-100 flex justify-end gap-3">
            <button wire:click="$set('showForm', false)" class="px-8 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest hover:bg-slate-200 rounded-2xl transition-colors">Batal</button>
            <button wire:click="save" class="px-10 py-4 bg-[#002855] text-white rounded-2xl font-black text-[10px] uppercase tracking-[0.2em] shadow-xl shadow-blue-900/20 hover:-translate-y-1 transition-all">Simpan Ruangan</button>
        </div>
    </div>
    @endif

    {{-- SweetAlert2 Listener --}}
    @script
    <script>
        $wire.on('swal:success', data => { alert(data[0].text); });
        $wire.on('swal:error', data => { alert(data[0].text); });
    </script>
    @endscript

    {{-- System Footer --}}
    <div class="pt-8 flex flex-col items-center gap-2 opacity-20 grayscale pointer-events-none">
        <p class="text-[9px] font-black uppercase tracking-[0.6em] text-[#002855]">FACILITY MANAGEMENT &bull; MASTER DATA</p>
    </div>
</div>