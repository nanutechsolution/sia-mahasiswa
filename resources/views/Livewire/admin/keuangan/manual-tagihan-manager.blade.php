<div class="max-w-4xl mx-auto space-y-6">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Buat Tagihan Manual</h1>
            <p class="mt-2 text-sm text-gray-700">Untuk tagihan khusus (Denda, Ganti Rugi, Biaya Susulan) perorangan.</p>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="bg-green-50 p-4 rounded-md border border-green-200 text-sm text-green-700 font-bold flex items-center animate-in fade-in">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white shadow rounded-lg border border-gray-200 overflow-hidden">
        <div class="p-6 space-y-6">
            
            {{-- LANGKAH 1: PILIH MAHASISWA --}}
            <div>
                <label class="block text-sm font-bold text-gray-700 uppercase mb-2">1. Cari Mahasiswa</label>
                
                @if(!$selectedMhs)
                    <div class="relative">
                        <input type="text" wire:model.live.debounce.300ms="searchMhs" 
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm pl-10" 
                            placeholder="Ketik NIM atau Nama Mahasiswa...">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        
                        {{-- Dropdown Hasil Pencarian --}}
                        @if(!empty($searchResults))
                            <div class="absolute z-10 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm">
                                @foreach($searchResults as $mhs)
                                    <div wire:click="selectMhs('{{ $mhs->id }}')" class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-indigo-50">
                                        <span class="font-semibold block truncate">{{ $mhs->nama_lengkap }}</span>
                                        <span class="text-gray-500 text-xs">{{ $mhs->nim }} - {{ $mhs->prodi->nama_prodi }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @elseif(strlen($searchMhs) >= 3)
                            <div class="absolute z-10 mt-1 w-full bg-white shadow-lg rounded-md py-2 px-3 text-sm text-gray-500 italic border">
                                Tidak ditemukan mahasiswa dengan kata kunci tersebut.
                            </div>
                        @endif
                    </div>
                @else
                    {{-- Kartu Mahasiswa Terpilih --}}
                    <div class="bg-indigo-50 border border-indigo-100 rounded-lg p-4 flex justify-between items-center">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10 bg-indigo-200 rounded-full flex items-center justify-center text-indigo-700 font-bold">
                                {{ substr($selectedMhs->nama_lengkap, 0, 1) }}
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-bold text-indigo-900">{{ $selectedMhs->nama_lengkap }}</div>
                                <div class="text-xs text-indigo-700">{{ $selectedMhs->nim }} • {{ $selectedMhs->prodi->nama_prodi }}</div>
                            </div>
                        </div>
                        <button wire:click="resetSelection" class="text-red-500 hover:text-red-700 text-sm font-bold px-3 py-1 bg-white rounded border border-red-200 hover:bg-red-50">
                            Ganti Mahasiswa
                        </button>
                    </div>
                @endif
                @error('selectedMhs') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            {{-- LANGKAH 2: DETAIL TAGIHAN --}}
            <div class="{{ !$selectedMhs ? 'opacity-50 pointer-events-none' : '' }} space-y-6">
                <div class="border-t border-gray-200 pt-6">
                    <label class="block text-sm font-bold text-gray-700 uppercase mb-4">2. Detail Tagihan</label>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Untuk Semester</label>
                            <select wire:model="semesterId" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 sm:text-sm">
                                @foreach($semesters as $sem)
                                    <option value="{{ $sem->id }}">{{ $sem->nama_tahun }} {{ $sem->is_active ? '(Aktif)' : '' }}</option>
                                @endforeach
                            </select>
                            @error('semesterId') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Jenis Biaya</label>
                            <select wire:model.live="komponenId" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 sm:text-sm">
                                <option value="">-- Pilih Komponen --</option>
                                @foreach($komponens as $k)
                                    <option value="{{ $k->id }}">{{ $k->nama_komponen }} ({{ $k->tipe_biaya }})</option>
                                @endforeach
                            </select>
                            @error('komponenId') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Deskripsi / Keterangan Tagihan</label>
                            <input type="text" wire:model="deskripsi" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 sm:text-sm" placeholder="Contoh: Denda Keterlambatan Pengembalian Alat Lab">
                            @error('deskripsi') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Nominal Tagihan (Rp)</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">Rp</span>
                                </div>
                                <input type="number" wire:model="nominal" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md" placeholder="0">
                            </div>
                            @error('nominal') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <div class="flex justify-end pt-4 border-t border-gray-200">
                    <button wire:click="simpanTagihan" wire:loading.attr="disabled" class="inline-flex justify-center py-2 px-6 border border-transparent shadow-sm text-sm font-bold rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50">
                        <span wire:loading.remove>Buat Tagihan</span>
                        <span wire:loading>Memproses...</span>
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>