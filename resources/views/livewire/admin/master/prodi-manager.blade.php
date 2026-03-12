<div class="space-y-8">
    {{-- Header & Toolbar --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-[#002855]">Master Program Studi</h1>
            <p class="text-slate-500 text-sm mt-1">Kelola jurusan, jenjang pendidikan, dan parameter akademik prodi.</p>
        </div>
        @if(!$showForm)
        <button wire:click="create" class="inline-flex items-center px-5 py-2.5 bg-[#fcc000] text-[#002855] rounded-xl font-black text-sm shadow-lg shadow-orange-500/20 hover:bg-[#fbbf24] hover:scale-105 transition-all">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Prodi
        </button>
        @endif
    </div>
    {{-- Form Section --}}
    @if($showForm)
    <div class="bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden animate-in zoom-in-95 duration-200">
        <div class="px-8 py-5 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
            <h3 class="text-sm font-bold text-[#002855] uppercase tracking-wider flex items-center gap-2">
                @if($editMode) Edit Program Studi @else Tambah Prodi Baru @endif
            </h3>
            <button wire:click="batal" class="text-slate-400 hover:text-slate-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="p-8 space-y-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

                {{-- Fakultas --}}
                <div class="col-span-2">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Fakultas Induk *</label>
                    <div class="relative">
                        <select wire:model="fakultas_id" class="block w-full rounded-lg border-slate-300 bg-slate-50 p-2.5 focus:border-[#002855] focus:ring-[#002855] text-sm font-bold text-slate-700 appearance-none">
                            <option value="">-- Pilih Fakultas --</option>
                            @foreach($fakultas_list as $f)
                            <option value="{{ $f->id }}">{{ $f->nama_fakultas }}</option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-500">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>
                    @error('fakultas_id') <span class="text-rose-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- Nama Prodi --}}
                <div class="col-span-2">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Nama Program Studi *</label>
                    <input type="text" wire:model="nama_prodi" class="block w-full rounded-lg border-slate-300 bg-slate-50 p-2.5 focus:border-[#002855] focus:ring-[#002855] text-sm font-bold placeholder-slate-400" placeholder="Contoh: Teknik Informatika">
                    @error('nama_prodi') <span class="text-rose-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- Jenjang --}}
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Jenjang *</label>
                    <div class="relative">
                        <select wire:model="jenjang" class="block w-full rounded-lg border-slate-300 bg-slate-50 p-2.5 focus:border-[#002855] focus:ring-[#002855] text-sm font-bold text-slate-700 appearance-none">
                            <option value="D3">D3</option>
                            <option value="D4">D4</option>
                            <option value="S1">S1</option>
                            <option value="S2">S2</option>
                            <option value="S3">S3</option>
                            <option value="PROFESI">PROFESI</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-500">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>
                    @error('jenjang') <span class="text-rose-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- Kode Internal --}}
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Kode Internal *</label>
                    <input type="text" wire:model="kode_prodi_internal" class="block w-full rounded-lg border-slate-300 bg-slate-50 p-2.5 focus:border-[#002855] focus:ring-[#002855] text-sm font-bold uppercase" placeholder="TI">
                    @error('kode_prodi_internal') <span class="text-rose-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- Kode Dikti --}}
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Kode Dikti</label>
                    <input type="text" wire:model="kode_prodi_dikti" class="block w-full rounded-lg border-slate-300 bg-slate-50 p-2.5 focus:border-[#002855] focus:ring-[#002855] text-sm font-bold" placeholder="55201">
                </div>

                {{-- Gelar --}}
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Gelar Lulusan</label>
                    <input type="text" wire:model="gelar_lulusan" class="block w-full rounded-lg border-slate-300 bg-slate-50 p-2.5 focus:border-[#002855] focus:ring-[#002855] text-sm font-bold" placeholder="S.Kom">
                </div>

                {{-- Kaprodi --}}
                <div class="col-span-2">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Nama Kaprodi</label>
                    <input type="text" wire:model="kaprodi" class="block w-full rounded-lg border-slate-300 bg-slate-50 p-2.5 focus:border-[#002855] focus:ring-[#002855] text-sm font-bold" placeholder="Nama lengkap Kaprodi">
                </div>
            </div>

            {{-- Format NIM --}}
            <div class="bg-indigo-50/50 p-5 rounded-2xl border border-indigo-100">
                <label class="block text-[10px] font-black text-indigo-700 uppercase tracking-widest mb-2">Format Auto-Generate NIM</label>
                <div class="flex flex-col md:flex-row gap-4 items-start">
                    <div class="flex-1 w-full">
                        <input type="text" wire:model="format_nim" class="block w-full rounded-lg border-indigo-200 bg-white text-indigo-900 p-2.5 text-sm font-mono font-bold focus:ring-indigo-500 focus:border-indigo-500" placeholder="{THN}{KODE}{NO:3}">
                        <p class="text-[10px] text-indigo-500/80 mt-2 font-medium">
                            Token: <span class="bg-white px-1 rounded border border-indigo-100">{THN}</span> (2 Digit Thn), <span class="bg-white px-1 rounded border border-indigo-100">{TAHUN}</span> (4 Digit), <span class="bg-white px-1 rounded border border-indigo-100">{KODE}</span> (Kode Dikti), <span class="bg-white px-1 rounded border border-indigo-100">{INTERNAL}</span>, <span class="bg-white px-1 rounded border border-indigo-100">{NO:3}</span> (Urutan).
                        </p>
                    </div>
                </div>
            </div>

            {{-- Settings --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-slate-100">
                <label class="flex items-center p-4 bg-amber-50 rounded-xl border border-amber-100 cursor-pointer hover:bg-amber-100 transition-colors">
                    <input type="checkbox" wire:model="is_paket" class="w-5 h-5 text-amber-500 rounded border-gray-300 focus:ring-amber-500">
                    <div class="ml-3">
                        <span class="block text-sm font-bold text-slate-800">Sistem Paket (KRS Otomatis)</span>
                        <span class="block text-[10px] text-slate-500 mt-0.5">Mahasiswa baru otomatis ambil MK paket.</span>
                    </div>
                </label>
                <label class="flex items-center p-4 bg-slate-50 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-100 transition-colors">
                    <input type="checkbox" wire:model="is_active" class="w-5 h-5 text-[#002855] rounded border-gray-300 focus:ring-[#002855]">
                    <span class="ml-3 text-sm font-bold text-slate-800">Status Prodi Aktif</span>
                </label>
            </div>

            <div class="pt-6 border-t border-slate-100 flex justify-end gap-3">
                <button wire:click="batal" class="px-6 py-2.5 text-sm font-bold text-slate-500 hover:text-slate-700 transition-colors rounded-xl">Batalkan</button>
                <button wire:click="save" class="px-8 py-2.5 bg-[#002855] text-white rounded-xl text-sm font-bold shadow-lg shadow-indigo-900/20 hover:bg-[#001a38] hover:scale-105 transition-all flex items-center">
                    <span wire:loading.remove wire:target="save">Simpan Data</span>
                    <span wire:loading wire:target="save">Menyimpan...</span>
                </button>
            </div>
        </div>
    </div>
    @endif
    <div class="w-full">
        <livewire:prodi-table />
    </div>
</div>