<div class="space-y-6">
    {{-- Header & Toolbar --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-unmaris-blue">Master Mata Kuliah</h1>
            <p class="text-slate-500 text-sm mt-1">Database seluruh mata kuliah per Program Studi.</p>
        </div>
        
        @if(!$showForm)
        <div class="flex flex-wrap gap-2">
            <button wire:click="openImport" type="button" 
                class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 shadow-sm hover:bg-slate-50 transition-all">
                <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                </svg>
                Import CSV
            </button>
            
            <button wire:click="create" type="button" 
                class="inline-flex items-center justify-center px-5 py-2.5 bg-unmaris-gold text-unmaris-blue rounded-xl font-black text-sm shadow-lg shadow-orange-500/20 hover:bg-unmaris-yellow hover:scale-105 transition-all">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" />
                </svg>
                Tambah MK
            </button>
        </div>
        @endif
    </div>

    {{-- Filters --}}
    <div class="bg-white p-5 shadow-sm rounded-2xl border border-slate-200 grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Filter Program Studi</label>
            <div class="relative">
                <select wire:model.live="filterProdiId" class="block w-full rounded-xl border-slate-200 bg-slate-50 text-slate-900 py-2 pl-4 pr-10 text-sm font-bold focus:outline-none focus:ring-2 focus:ring-unmaris-yellow focus:border-transparent transition-all appearance-none">
                    @foreach($prodis as $p)
                        <option value="{{ $p->id }}">{{ $p->jenjang }} - {{ $p->nama_prodi }}</option>
                    @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-500">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                </div>
            </div>
        </div>
        <div>
            <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Cari Data</label>
            <div class="relative">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Kode atau Nama MK..." class="block w-full rounded-xl border-slate-200 bg-slate-50 text-slate-900 py-2 pl-10 pr-4 text-sm font-bold placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-unmaris-yellow focus:border-transparent transition-all">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center px-3 text-slate-400">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Feedback Messages --}}
    @if (session()->has('success'))
        <div class="bg-emerald-50 border border-emerald-100 p-4 rounded-xl text-emerald-800 text-sm flex items-center shadow-sm animate-in fade-in slide-in-from-top-2">
            <svg class="w-5 h-5 mr-3 text-emerald-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
            <span class="font-bold">{{ session('success') }}</span>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="bg-rose-50 border border-rose-100 p-4 rounded-xl text-rose-800 text-sm flex items-center shadow-sm animate-in fade-in slide-in-from-top-2">
            <span class="font-bold">{{ session('error') }}</span>
        </div>
    @endif

    {{-- Import Modal --}}
    @if($showImportModal)
    <div class="fixed inset-0 z-[999] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" aria-hidden="true" wire:click="batal"></div>
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="relative inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-white/20">
                <div class="bg-unmaris-blue px-8 py-6 text-white flex justify-between items-center">
                    <h3 class="text-lg font-black uppercase tracking-widest leading-none">Import Mata Kuliah</h3>
                    <button wire:click="batal" class="text-white/80 hover:text-white">&times;</button>
                </div>
                <div class="p-8 space-y-6">
                    <div class="bg-slate-50 p-4 rounded-xl border border-slate-100 text-sm text-slate-600">
                        <p class="font-bold mb-2 text-unmaris-blue">Format Wajib CSV:</p>
                        <code class="block bg-white p-2 rounded border border-slate-200 text-xs text-indigo-600 font-mono mb-4 break-all">
                            Kode MK, Nama MK, Total SKS, SKS Teori, SKS Praktek, Jenis(A/B/C)
                        </code>
                        <button wire:click="downloadTemplate" type="button" class="mt-3 text-xs font-bold text-unmaris-blue hover:underline flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                            Download Template CSV
                        </button>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase mb-2">Upload File</label>
                        <input type="file" wire:model="fileImport" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-unmaris-blue file:text-white hover:file:bg-unmaris-dark transition-colors cursor-pointer">
                        <div wire:loading wire:target="fileImport" class="text-xs text-indigo-600 mt-2 font-bold animate-pulse">Sedang mengupload...</div>
                        @error('fileImport') <span class="text-rose-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="pt-4 flex justify-end gap-3">
                        <button wire:click="batal" type="button" class="px-6 py-2 text-sm font-bold text-slate-400 hover:text-slate-600">Batal</button>
                        <button wire:click="processImport" wire:loading.attr="disabled" type="button" class="bg-emerald-600 text-white px-6 py-2 rounded-xl font-bold text-sm shadow-md hover:bg-emerald-700 transition-all flex items-center disabled:opacity-50">
                            <span wire:loading.remove wire:target="processImport">Mulai Import</span>
                            <span wire:loading wire:target="processImport">Memproses...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Form Input (Manual) --}}
    @if($showForm)
    <div class="bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden animate-in zoom-in-95 duration-200">
        <div class="px-8 py-5 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
            <h3 class="text-sm font-bold text-unmaris-blue uppercase tracking-wider flex items-center gap-2">
                @if($editMode) Edit Data Mata Kuliah @else Tambah Mata Kuliah Baru @endif
            </h3>
            <button wire:click="batal" class="text-slate-400 hover:text-slate-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>
        
        <div class="p-8 space-y-8">
            <div class="grid grid-cols-1 md:grid-cols-6 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-1">Kode MK</label>
                    <input type="text" wire:model="kode_mk" class="block w-full rounded-xl border-slate-200 bg-slate-50 text-slate-900 py-2 pl-4 pr-4 text-sm font-bold uppercase placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-unmaris-yellow focus:border-transparent transition-all" placeholder="TI-101">
                    @error('kode_mk') <span class="text-rose-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="md:col-span-4">
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-1">Nama Mata Kuliah</label>
                    <input type="text" wire:model="nama_mk" class="block w-full rounded-xl border-slate-200 bg-slate-50 text-slate-900 py-2 pl-4 pr-4 text-sm font-bold placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-unmaris-yellow focus:border-transparent transition-all" placeholder="Algoritma Pemrograman">
                    @error('nama_mk') <span class="text-rose-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Rincian SKS (UPDATED DESIGN) -->
                <div class="md:col-span-6 bg-unmaris-blue/5 p-6 rounded-2xl border border-unmaris-blue/10">
                    <label class="block text-xs font-black text-unmaris-blue uppercase tracking-widest mb-4">Rincian Beban SKS (Kredit)</label>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                        {{-- Kolom Input SKS Menggunakan bg-slate-50 agar kontras dengan card putih --}}
                        <div class="bg-white p-2 rounded-xl border border-slate-200 shadow-sm focus-within:ring-2 focus-within:ring-unmaris-yellow focus-within:border-transparent transition-all">
                            <label class="block text-[10px] font-bold text-slate-400 uppercase text-center mb-1">Tatap Muka</label>
                            <input type="number" wire:model.live="sks_tatap_muka" class="block w-full border-0 p-0 text-center text-2xl font-black text-slate-900 focus:ring-0 bg-transparent placeholder-slate-200" placeholder="0">
                        </div>
                        <div class="bg-white p-2 rounded-xl border border-slate-200 shadow-sm focus-within:ring-2 focus-within:ring-unmaris-yellow focus-within:border-transparent transition-all">
                            <label class="block text-[10px] font-bold text-slate-400 uppercase text-center mb-1">Praktek</label>
                            <input type="number" wire:model.live="sks_praktek" class="block w-full border-0 p-0 text-center text-2xl font-black text-slate-900 focus:ring-0 bg-transparent placeholder-slate-200" placeholder="0">
                        </div>
                        <div class="bg-white p-2 rounded-xl border border-slate-200 shadow-sm focus-within:ring-2 focus-within:ring-unmaris-yellow focus-within:border-transparent transition-all">
                            <label class="block text-[10px] font-bold text-slate-400 uppercase text-center mb-1">Lapangan</label>
                            <input type="number" wire:model.live="sks_lapangan" class="block w-full border-0 p-0 text-center text-2xl font-black text-slate-900 focus:ring-0 bg-transparent placeholder-slate-200" placeholder="0">
                        </div>
                        <div class="bg-unmaris-blue p-2 rounded-xl border border-unmaris-blue shadow-md flex flex-col justify-center">
                            <label class="block text-[10px] font-bold text-unmaris-gold uppercase text-center mb-1">Total SKS</label>
                            <input type="text" wire:model="sks_default" readonly class="block w-full text-center bg-transparent border-0 p-0 text-3xl font-black text-white focus:ring-0 cursor-default">
                        </div>
                    </div>
                    @error('sks_default') <span class="text-rose-500 text-xs font-bold mt-3 block text-center">{{ $message }}</span> @enderror
                </div>

                <div class="md:col-span-3">
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-1">Jenis MK (Feeder)</label>
                    <select wire:model="jenis_mk" class="block w-full rounded-xl border-slate-200 bg-slate-50 text-slate-900 py-2 pl-4 pr-10 text-sm font-bold focus:outline-none focus:ring-2 focus:ring-unmaris-yellow focus:border-transparent transition-all">
                        <option value="A">A - Wajib Nasional</option>
                        <option value="B">B - Wajib Prodi</option>
                        <option value="C">C - Pilihan</option>
                        <option value="D">D - Tugas Akhir/Skripsi</option>
                    </select>
                </div>

                <div class="md:col-span-3">
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-1">Pemilik Prodi</label>
                    <select wire:model="prodi_id" class="block w-full rounded-xl border-slate-200 bg-slate-50 text-slate-900 py-2 pl-4 pr-10 text-sm font-bold focus:outline-none focus:ring-2 focus:ring-unmaris-yellow focus:border-transparent transition-all">
                        @foreach($prodis as $p)
                            <option value="{{ $p->id }}">{{ $p->nama_prodi }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="pt-6 border-t border-slate-100 flex justify-end gap-3">
                <button wire:click="batal" type="button" class="px-6 py-2.5 text-sm font-bold text-slate-500 hover:text-slate-600 transition-colors">Batal</button>
                <button wire:click="save" type="button" class="px-8 py-2.5 bg-unmaris-blue text-white rounded-xl text-sm font-bold shadow-lg shadow-indigo-200 hover:bg-unmaris-dark hover:scale-105 transition-all">Simpan Data</button>
            </div>
        </div>
    </div>
    @endif

    <!-- Tabel Data -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-3 border-b border-slate-100 bg-slate-50/80 flex items-center justify-between">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Total Data: {{ $mks->total() }}</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-unmaris-blue border-b border-unmaris-dark text-white">
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest">Kode MK</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest">Nama Mata Kuliah</th>
                        <th class="px-6 py-4 text-center text-[10px] font-bold uppercase tracking-widest">Bobot SKS</th>
                        <th class="px-6 py-4 text-center text-[10px] font-bold uppercase tracking-widest">Jenis</th>
                        <th class="px-6 py-4 text-right text-[10px] font-bold uppercase tracking-widest">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($mks as $mk)
                    <tr class="hover:bg-slate-50/80 transition-colors group">
                        <td class="px-6 py-4 align-top">
                            <div class="font-mono text-xs font-bold text-unmaris-blue bg-indigo-50 px-2 py-0.5 rounded w-fit">{{ $mk->kode_mk }}</div>
                        </td>
                        <td class="px-6 py-4 align-top">
                            <div class="text-sm font-bold text-slate-800">{{ $mk->nama_mk }}</div>
                        </td>
                        <td class="px-6 py-4 align-top text-center">
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full text-sm font-black bg-emerald-50 text-emerald-700 border border-emerald-100">
                                {{ $mk->sks_default }}
                            </span>
                        </td>
                        <td class="px-6 py-4 align-top text-center">
                            <span class="text-xs font-bold text-slate-500">{{ $mk->jenis_mk }}</span>
                        </td>
                        <td class="px-6 py-4 align-top text-right">
                            <div class="flex justify-end gap-2 opacity-60 group-hover:opacity-100 transition-opacity">
                                <button wire:click="edit('{{ $mk->id }}')" class="p-2 text-unmaris-blue hover:bg-unmaris-blue/10 rounded-lg transition-colors" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                </button>
                                <button wire:click="delete('{{ $mk->id }}')" wire:confirm="Hapus MK ini?" class="p-2 text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center">
                            <p class="text-slate-400 font-medium italic">Tidak ada data mata kuliah.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
            {{ $mks->links() }}
        </div>
    </div>
</div>