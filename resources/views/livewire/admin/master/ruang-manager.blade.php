<div class="space-y-6 animate-in fade-in duration-500">

    {{-- 1. Header Section --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-[#002855] rounded-2xl flex items-center justify-center text-[#fcc000] shadow-lg shadow-blue-900/20">
                <x-heroicon-o-home-modern class="w-7 h-7" />
            </div>
            <div>
                <h1 class="text-2xl font-black text-[#002855] tracking-tight uppercase">Master Ruangan</h1>
                <p class="text-slate-500 text-sm font-medium">Kelola data fasilitas, kapasitas, dan lokasi kelas perkuliahan.</p>
            </div>
        </div>
        @if(!$showForm)
        <button wire:click="create" class="inline-flex items-center px-6 py-3 bg-[#fcc000] text-[#002855] rounded-xl font-black text-xs uppercase tracking-widest shadow-lg shadow-amber-500/20 hover:bg-[#fbbf24] hover:scale-105 transition-all">
            <x-heroicon-s-plus class="w-5 h-5 mr-2" />
            Tambah Ruangan
        </button>
        @endif
    </div>

    @if($showForm)
    <div class="bg-white rounded-3xl shadow-2xl border border-slate-200 overflow-hidden animate-in zoom-in-95 duration-200">
        <div class="px-8 py-5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
            <h3 class="text-sm font-bold text-[#002855] uppercase tracking-wider flex items-center gap-2">
                <x-heroicon-o-pencil-square class="w-5 h-5 text-[#fcc000]" />
                {{ $editMode ? 'Edit Data Ruangan' : 'Registrasi Ruangan Baru' }}
            </h3>
            <button wire:click="resetForm" @click="$wire.showForm = false" class="text-slate-400 hover:text-rose-500 transition-colors bg-white p-1.5 rounded-full shadow-sm">
                <x-heroicon-s-x-mark class="w-5 h-5" />
            </button>
        </div>

        <div class="p-8 space-y-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                {{-- Kode Ruangan --}}
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Kode Ruangan *</label>
                    <input type="text" wire:model="kode_ruang" class="block w-full rounded-xl border-slate-300 bg-slate-50 p-4 focus:border-[#002855] focus:ring-[#002855] text-sm font-bold uppercase placeholder-slate-300" placeholder="Contoh: A.101">
                    @error('kode_ruang') <span class="text-rose-500 text-[10px] font-bold mt-1 block ml-1 uppercase">{{ $message }}</span> @enderror
                </div>

                {{-- Kapasitas --}}
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Kapasitas Mahasiswa *</label>
                    <div class="relative">
                        <input type="number" wire:model="kapasitas" class="block w-full rounded-xl border-slate-300 bg-slate-50 p-4 focus:border-[#002855] focus:ring-[#002855] text-sm font-bold placeholder-slate-300" placeholder="40">
                        <div class="absolute inset-y-0 right-4 flex items-center pointer-events-none text-slate-400 text-xs font-bold uppercase">Orang</div>
                    </div>
                    @error('kapasitas') <span class="text-rose-500 text-[10px] font-bold mt-1 block ml-1 uppercase">{{ $message }}</span> @enderror
                </div>

                {{-- Nama Ruangan --}}
                <div class="col-span-1 md:col-span-2 space-y-2">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Nama Lengkap Ruangan *</label>
                    <input type="text" wire:model="nama_ruang" class="block w-full rounded-xl border-slate-300 bg-slate-50 p-4 focus:border-[#002855] focus:ring-[#002855] text-sm font-bold placeholder-slate-300" placeholder="Contoh: Ruang Kelas Teori Gedung A Lantai 1">
                    @error('nama_ruang') <span class="text-rose-500 text-[10px] font-bold mt-1 block ml-1 uppercase">{{ $message }}</span> @enderror
                </div>

                {{-- Status --}}
                <div class="col-span-1 md:col-span-2">
                    <label class="flex items-center group cursor-pointer">
                        <div class="relative">
                            <input type="checkbox" wire:model="is_active" class="sr-only">
                            <div class="block bg-slate-200 w-14 h-8 rounded-full transition-colors group-hover:bg-slate-300" :class="$wire.is_active ? '!bg-emerald-500' : ''"></div>
                            <div class="dot absolute left-1 top-1 bg-white w-6 h-6 rounded-full transition-transform" :class="$wire.is_active ? 'translate-x-6' : ''"></div>
                        </div>
                        <div class="ml-4">
                            <span class="block text-sm font-bold text-slate-700">Status Aktif</span>
                            <span class="block text-[10px] text-slate-400 uppercase font-bold tracking-tight">Ruangan dapat dipilih dalam pembuatan jadwal kuliah</span>
                        </div>
                    </label>
                </div>
            </div>

            <div class="pt-8 border-t border-slate-100 flex flex-col md:flex-row justify-end gap-3">
                <button wire:click="batal" class="px-8 py-3 text-xs font-black text-slate-500 hover:text-slate-700 uppercase tracking-widest transition-colors">
                    Batalkan
                </button>
                <button wire:click="save" class="px-12 py-3 bg-[#002855] text-white rounded-xl text-xs font-black uppercase tracking-widest shadow-xl shadow-blue-900/20 hover:bg-[#001a38] transition-all flex items-center justify-center min-w-[160px]">
                    <span wire:loading.remove wire:target="save">Simpan Perubahan</span>
                    <span wire:loading wire:target="save" class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Memproses...
                    </span>
                </button>
            </div>
        </div>
    </div>
    @endif
    <div class="w-full">
     <livewire:ruang-table />
    </div>

</div>