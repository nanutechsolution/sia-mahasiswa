<div class="space-y-6">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Master Mata Kuliah</h1>
            <p class="mt-2 text-sm text-gray-700">Database seluruh mata kuliah per Program Studi.</p>
        </div>
        <div class="mt-4 sm:mt-0 flex gap-3">
            {{-- Tombol Import & Tambah hanya muncul jika tidak sedang buka form input --}}
            @if(!$showForm)
                <button wire:click="openImport" type="button" 
                    class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-bold text-gray-700 shadow-sm hover:bg-gray-50 transition-colors">
                    {{-- Ikon Cloud Upload --}}
                    <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                    Import CSV
                </button>
                
                <button wire:click="create" type="button" 
                    class="inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-bold text-white shadow-sm hover:bg-indigo-700 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Manual
                </button>
            @endif
        </div>
    </div>

    <!-- Filter -->
    <div class="bg-white p-4 shadow sm:rounded-lg border border-gray-200 grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-xs font-medium text-gray-700 uppercase">Filter Program Studi</label>
            <select wire:model.live="filterProdiId" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 sm:text-sm">
                @foreach($prodis as $p)
                    <option value="{{ $p->id }}">{{ $p->jenjang }} - {{ $p->nama_prodi }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 uppercase">Cari MK</label>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Kode atau Nama MK..." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 sm:text-sm">
        </div>
    </div>

    <!-- Alert -->
    @if (session()->has('success'))
        <div class="bg-green-50 p-4 rounded-md border border-green-200 text-sm text-green-700 font-medium animate-pulse">
            {{ session('success') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="bg-red-50 p-4 rounded-md border border-red-200 text-sm text-red-700 font-medium">
            {{ session('error') }}
        </div>
    @endif

    <!-- Import Modal (Popup) - FIXED STRUCTURE -->
    @if($showImportModal)
    <div class="relative z-[999]" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <!-- Backdrop (Layar Abu-abu) -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

        <!-- Container Scroll -->
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                
                <!-- Panel Modal (Kotak Putih) -->
                <div class="relative transform overflow-hidden rounded-lg bg-white px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6">
                    
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" /></svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Import Mata Kuliah (CSV)</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500 mb-2">
                                    Pastikan file CSV memiliki format urutan kolom:<br>
                                    <strong>Kode MK, Nama MK, Total SKS, SKS Teori, SKS Praktek, Jenis(A/B/C)</strong>
                                </p>
                                
                                <button wire:click="downloadTemplate" type="button" class="mb-4 inline-flex items-center text-xs font-bold text-indigo-600 hover:text-indigo-800 underline">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                    Download Format CSV (Template)
                                </button>
                                
                                <div class="bg-gray-50 p-3 rounded text-xs font-mono text-gray-600 mb-4 border border-gray-200">
                                    Contoh Isi File:<br>
                                    TI101,Algoritma Pemrograman,3,2,1,A<br>
                                    TI102,Basis Data,3,3,0,A
                                </div>

                                <input type="file" wire:model="fileImport" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                
                                <div wire:loading wire:target="fileImport" class="text-xs text-indigo-600 mt-1 font-bold">
                                    Sedang mengupload file...
                                </div>

                                @error('fileImport') <span class="text-red-500 text-xs font-bold block mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                        <button wire:click="processImport" wire:loading.attr="disabled" type="button" 
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-wait">
                            <span wire:loading.remove wire:target="processImport">Mulai Import</span>
                            <span wire:loading wire:target="processImport">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                Memproses...
                            </span>
                        </button>
                        <button wire:click="batal" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm">
                            Batal
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Form Input (Manual) -->
    @if($showForm)
    <div class="bg-indigo-50 shadow sm:rounded-lg p-6 border border-indigo-100">
        <h3 class="text-lg font-medium leading-6 text-indigo-900 mb-4">
            {{ $editMode ? 'Edit Mata Kuliah' : 'Tambah Mata Kuliah Baru' }}
        </h3>
        
        <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700">Kode MK</label>
                <input type="text" wire:model="kode_mk" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 sm:text-sm uppercase">
                @error('kode_mk') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div class="sm:col-span-4">
                <label class="block text-sm font-medium text-gray-700">Nama Mata Kuliah</label>
                <input type="text" wire:model="nama_mk" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 sm:text-sm">
                @error('nama_mk') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <!-- Rincian SKS -->
            <div class="sm:col-span-6 bg-white p-4 rounded-md border border-gray-200">
                <label class="block text-xs font-bold text-gray-500 uppercase mb-3">Bobot SKS (Kredit)</label>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tatap Muka</label>
                        <input type="number" wire:model.live="sks_tatap_muka" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Praktek</label>
                        <input type="number" wire:model.live="sks_praktek" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Lapangan</label>
                        <input type="number" wire:model.live="sks_lapangan" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-indigo-700">Total SKS</label>
                        <input type="number" wire:model="sks_default" readonly class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm text-indigo-700 font-bold sm:text-sm cursor-not-allowed">
                        @error('sks_default') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>
                <p class="text-[10px] text-gray-400 mt-2">*Total SKS dihitung otomatis.</p>
            </div>

            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700">Jenis MK (Feeder)</label>
                <select wire:model="jenis_mk" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 sm:text-sm">
                    <option value="A">A - Wajib Nasional</option>
                    <option value="B">B - Wajib Prodi</option>
                    <option value="C">C - Pilihan</option>
                    <option value="D">D - Tugas Akhir/Skripsi</option>
                </select>
            </div>

            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700">Pemilik Prodi</label>
                <select wire:model="prodi_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 sm:text-sm">
                    @foreach($prodis as $p)
                        <option value="{{ $p->id }}">{{ $p->nama_prodi }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="mt-5 flex justify-end space-x-2">
            <button wire:click="batal" type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                Batal
            </button>
            <button wire:click="save" type="button" class="px-4 py-2 text-sm font-bold text-white bg-indigo-600 rounded-md hover:bg-indigo-700">
                Simpan
            </button>
        </div>
    </div>
    @endif

    <!-- Tabel Data -->
    <div class="flex flex-col">
        <div class="-my-2 -mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="inline-block min-w-full py-2 align-middle md:px-6 lg:px-8">
                <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-300">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Kode MK</th>
                                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Nama Mata Kuliah</th>
                                <th class="px-3 py-3.5 text-center text-sm font-semibold text-gray-900">SKS</th>
                                <th class="px-3 py-3.5 text-center text-sm font-semibold text-gray-900">Jenis</th>
                                <th class="relative py-3.5 pl-3 pr-4 sm:pr-6">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse($mks as $mk)
                            <tr>
                                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-indigo-600 sm:pl-6">
                                    {{ $mk->kode_mk }}
                                </td>
                                <td class="px-3 py-4 text-sm text-gray-900">
                                    {{ $mk->nama_mk }}
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-center">
                                    <span class="inline-flex rounded-full bg-gray-100 px-2 text-xs font-semibold leading-5 text-gray-800">
                                        {{ $mk->sks_default }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-center text-gray-500">
                                    {{ $mk->jenis_mk }}
                                </td>
                                <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6 space-x-2">
                                    <button wire:click="edit('{{ $mk->id }}')" class="text-indigo-600 hover:text-indigo-900">Edit</button>
                                    <button wire:click="delete('{{ $mk->id }}')" wire:confirm="Hapus MK ini?" class="text-red-600 hover:text-red-900">Hapus</button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-gray-500 italic">
                                    Tidak ada data mata kuliah.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="p-4 border-t border-gray-200">
                        {{ $mks->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>