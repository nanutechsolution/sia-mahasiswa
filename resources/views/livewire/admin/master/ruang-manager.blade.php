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

    {{-- 2. Alert Messages --}}
    @if (session()->has('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-100 text-emerald-800 rounded-2xl text-sm font-bold flex items-center shadow-sm animate-in slide-in-from-top-2">
            <x-heroicon-s-check-circle class="w-5 h-5 mr-3 text-emerald-500" />
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="p-4 bg-rose-50 border border-rose-100 text-rose-800 rounded-2xl text-sm font-bold flex items-center shadow-sm animate-in slide-in-from-top-2">
            <x-heroicon-s-x-circle class="w-5 h-5 mr-3 text-rose-500" />
            {{ session('error') }}
        </div>
    @endif

    {{-- 3. Form Section (Create/Edit) --}}
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
                        <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        Memproses...
                    </span>
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- 4. Table Section --}}
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden relative">
        {{-- Loading Overlay --}}
        <div wire:loading.flex wire:target="search, gotoPage, nextPage, previousPage" class="absolute inset-0 z-10 bg-white/60 backdrop-blur-[1px] items-center justify-center">
            <div class="p-4 bg-white rounded-2xl shadow-xl border border-slate-100 flex flex-col items-center">
                <svg class="w-8 h-8 text-[#002855] animate-spin mb-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Sinkronisasi...</span>
            </div>
        </div>

        <div class="p-5 bg-slate-50/50 border-b border-slate-100 flex flex-col md:flex-row gap-4 items-center justify-between">
            <div class="relative w-full max-w-sm">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari kode atau nama ruangan..." class="w-full pl-12 pr-4 py-3 rounded-xl border-slate-200 text-sm focus:ring-[#002855] focus:border-[#002855] font-bold text-slate-700 outline-none shadow-sm placeholder-slate-400">
                <x-heroicon-o-magnifying-glass class="w-5 h-5 absolute left-4 top-3.5 text-slate-400" />
            </div>
            
            <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                Total: {{ $ruangan->total() }} Data Ruangan
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#002855] text-white">
                        <th class="px-8 py-5 text-[10px] font-bold uppercase tracking-widest">Identitas Ruang</th>
                        <th class="px-6 py-5 text-[10px] font-bold uppercase tracking-widest text-center">Kapasitas</th>
                        <th class="px-6 py-5 text-[10px] font-bold uppercase tracking-widest text-center">Status</th>
                        <th class="px-8 py-5 text-[10px] font-bold uppercase tracking-widest text-right">Manajemen</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-bold">
                    @forelse($ruangan as $r)
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-indigo-50 text-[#002855] rounded-2xl flex items-center justify-center font-black text-sm border border-indigo-100 shadow-sm transition-transform group-hover:scale-110">
                                    {{ substr($r->kode_ruang, 0, 1) }}
                                </div>
                                <div>
                                    <div class="text-sm font-black text-slate-800 uppercase tracking-tight">{{ $r->nama_ruang }}</div>
                                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1 flex items-center gap-1.5">
                                        <x-heroicon-s-hashtag class="w-3 h-3" />
                                        {{ $r->kode_ruang }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5 text-center">
                            <span class="inline-flex items-center gap-1.5 px-4 py-1.5 bg-white border border-slate-200 text-slate-700 rounded-xl text-xs font-black shadow-sm">
                                <x-heroicon-s-users class="w-4 h-4 text-[#fcc000]" />
                                {{ $r->kapasitas }} <span class="text-[10px] text-slate-400">MHS</span>
                            </span>
                        </td>
                        <td class="px-6 py-5 text-center">
                            <button wire:click="toggleActive('{{ $r->id }}')" class="inline-flex items-center px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest transition-all shadow-sm hover:shadow-md {{ $r->is_active ? 'bg-emerald-50 text-emerald-600 border border-emerald-200' : 'bg-rose-50 text-rose-600 border border-rose-200' }}">
                                <div class="w-1.5 h-1.5 rounded-full mr-2 {{ $r->is_active ? 'bg-emerald-500 animate-pulse' : 'bg-rose-500' }}"></div>
                                {{ $r->is_active ? 'Aktif' : 'Non-Aktif' }}
                            </button>
                        </td>
                        <td class="px-8 py-5 text-right">
                            <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-all transform translate-x-2 group-hover:translate-x-0">
                                <button wire:click="edit('{{ $r->id }}')" class="p-2.5 text-[#002855] bg-indigo-50 hover:bg-[#002855] hover:text-white rounded-xl transition-all shadow-sm" title="Edit Data">
                                    <x-heroicon-s-pencil class="w-4 h-4" />
                                </button>
                                <button wire:click="delete('{{ $r->id }}')" wire:confirm="PERINGATAN: Menghapus ruangan akan berdampak pada jadwal kuliah yang sudah ada. Lanjutkan?" class="p-2.5 text-rose-600 bg-rose-50 hover:bg-rose-600 hover:text-white rounded-xl transition-all shadow-sm" title="Hapus Ruangan">
                                    <x-heroicon-s-trash class="w-4 h-4" />
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center justify-center opacity-30 grayscale">
                                <x-heroicon-o-archive-box-x-mark class="w-16 h-16 text-slate-400 mb-4" />
                                <p class="text-sm font-black uppercase tracking-widest text-slate-500">Data Ruangan Kosong</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($ruangan->hasPages())
        <div class="px-8 py-6 border-t border-slate-100 bg-slate-50/50">
            {{ $ruangan->links() }}
        </div>
        @endif
    </div>

    {{-- System Info Footer --}}
    <div class="flex items-center justify-center gap-3 opacity-20 grayscale select-none pointer-events-none py-4">
        <div class="h-px bg-slate-300 w-12"></div>
        <p class="text-[9px] font-black uppercase tracking-[0.4em] text-[#002855]">Ref. Ruang Management System</p>
        <div class="h-px bg-slate-300 w-12"></div>
    </div>

    {{-- SweetAlert2 Listener --}}
    @script
    <script>
        $wire.on('swal:success', data => { 
            // Implementasi library modal pilihan Anda di sini (misal SweetAlert2)
            console.log('Success:', data[0].text); 
        });
        $wire.on('swal:error', data => { 
            console.error('Error:', data[0].text); 
        });
    </script>
    @endscript
</div>