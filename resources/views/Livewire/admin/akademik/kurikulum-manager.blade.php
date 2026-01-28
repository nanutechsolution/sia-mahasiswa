<div class="space-y-6">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Manajemen Kurikulum</h1>
            <p class="mt-2 text-sm text-gray-700">Daftar kurikulum per Program Studi (Standar Feeder).</p>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="bg-green-50 p-4 rounded-md border border-green-200 text-sm text-green-700 font-medium">
            {{ session('success') }}
        </div>
    @endif

    <!-- Form Tambah Kurikulum -->
    <div class="bg-white shadow sm:rounded-lg p-6 border border-gray-200">
        <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Buat Kurikulum Baru</h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="col-span-1">
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Program Studi</label>
                <select wire:model="prodi_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 sm:text-sm">
                    <option value="">Pilih Prodi</option>
                    @foreach($prodis as $p)
                        <option value="{{ $p->id }}">{{ $p->nama_prodi }}</option>
                    @endforeach
                </select>
                @error('prodi_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div class="col-span-2">
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Nama Kurikulum</label>
                <input type="text" wire:model="nama_kurikulum" placeholder="Contoh: Kurikulum 2024 Merdeka" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 sm:text-sm">
                @error('nama_kurikulum') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div class="col-span-1">
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Tahun Mulai</label>
                <input type="number" wire:model="tahun_mulai" placeholder="2024" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 sm:text-sm">
                @error('tahun_mulai') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            
            <!-- FEEDER FIELDS -->
            <div class="col-span-1">
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Min SKS Lulus</label>
                <input type="number" wire:model="jumlah_sks_lulus" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 sm:text-sm">
            </div>
            <div class="col-span-1">
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">ID Semester (Feeder)</label>
                <input type="text" wire:model="id_semester_mulai" placeholder="Cth: 20241" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 sm:text-sm">
            </div>
             <div class="col-span-2">
                <p class="text-xs text-gray-500 mt-6 italic">*Jumlah SKS Wajib & Pilihan akan dihitung otomatis saat Anda menambahkan mata kuliah.</p>
            </div>
        </div>
        <div class="mt-4 text-right">
            <button wire:click="saveHeader" class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm font-bold shadow-sm hover:bg-indigo-700">
                Simpan Kurikulum
            </button>
        </div>
    </div>

    <!-- List Kurikulum -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach($kurikulums as $k)
        <div class="bg-white overflow-hidden shadow rounded-lg border {{ $k->is_active ? 'border-green-200' : 'border-gray-200' }}">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex items-center justify-between">
                    <span class="inline-flex rounded-md bg-indigo-50 px-2 py-1 text-xs font-medium text-indigo-700 ring-1 ring-inset ring-indigo-700/10">
                        {{ $k->prodi->nama_prodi }}
                    </span>
                    <button wire:click="toggleActive({{ $k->id }})" class="text-xs font-bold {{ $k->is_active ? 'text-green-600' : 'text-gray-400' }}">
                        {{ $k->is_active ? 'AKTIF' : 'NON-AKTIF' }}
                    </button>
                </div>
                <h3 class="mt-3 text-lg font-semibold text-gray-900">{{ $k->nama_kurikulum }}</h3>
                <div class="flex justify-between items-center mt-2 text-xs text-gray-500">
                    <span>Mulai: {{ $k->tahun_mulai }} ({{ $k->id_semester_mulai }})</span>
                    <span>Min Lulus: {{ $k->jumlah_sks_lulus }} SKS</span>
                </div>
                <div class="mt-2 text-xs text-gray-600 bg-gray-50 p-2 rounded">
                   Wajib: <b>{{ $k->jumlah_sks_wajib }}</b> | Pilihan: <b>{{ $k->jumlah_sks_pilihan }}</b>
                </div>
                
                <div class="mt-4">
                    <button wire:click="manage({{ $k->id }})" class="w-full rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                        Atur Mata Kuliah & Struktur
                    </button>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>