<div class="space-y-6">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Plotting PA Massal</h1>
            <p class="mt-2 text-sm text-gray-700">Tetapkan Dosen Wali untuk banyak mahasiswa sekaligus.</p>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="bg-green-50 p-4 rounded-md border border-green-200 text-sm text-green-700 font-bold flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        
        {{-- KOLOM KIRI: FILTER & AKSI --}}
        <div class="space-y-6">
            {{-- Filter Box --}}
            <div class="bg-white p-5 shadow-sm rounded-xl border border-slate-200 space-y-4">
                <h3 class="text-sm font-bold text-slate-800 uppercase tracking-widest border-b pb-2">Filter Data</h3>
                
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Program Studi</label>
                    <select wire:model.live="filterProdiId" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 sm:text-sm">
                        @foreach($prodis as $p)
                            <option value="{{ $p->id }}">{{ $p->nama_prodi }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Angkatan</label>
                    <select wire:model.live="filterAngkatan" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 sm:text-sm">
                        @foreach($angkatans as $a)
                            <option value="{{ $a->id_tahun }}">{{ $a->id_tahun }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Status PA</label>
                    <select wire:model.live="filterStatusPa" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 sm:text-sm">
                        <option value="all">Semua Mahasiswa</option>
                        <option value="belum">Belum Punya PA</option>
                        <option value="sudah">Sudah Punya PA</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Cari Nama/NIM</label>
                    <input type="text" wire:model.live.debounce.300ms="search" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 sm:text-sm" placeholder="Search...">
                </div>
            </div>

            {{-- Action Box --}}
            <div class="bg-indigo-50 p-5 shadow-sm rounded-xl border border-indigo-100 space-y-4 sticky top-6">
                <h3 class="text-sm font-bold text-indigo-900 uppercase tracking-widest border-b border-indigo-200 pb-2">Eksekusi Plotting</h3>
                
                <div>
                    <label class="block text-xs font-bold text-indigo-700 uppercase mb-1">Pilih Dosen Wali Target</label>
                    <select wire:model="targetDosenId" class="block w-full rounded-md border-indigo-300 bg-white shadow-sm focus:border-indigo-500 sm:text-sm">
                        <option value="">-- Pilih Dosen --</option>
                        @foreach($dosens as $d)
                            <option value="{{ $d->id }}">{{ $d->nama_lengkap_gelar }}</option>
                        @endforeach
                    </select>
                    @error('targetDosenId') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-between items-center text-xs text-indigo-800 font-medium">
                    <span>Terpilih: <strong>{{ count($selectedMhs) }}</strong> Mhs</span>
                    <button wire:click="resetSelection" class="text-red-500 hover:underline">Reset</button>
                </div>

                <button wire:click="simpanPloting" 
                    wire:loading.attr="disabled"
                    @if(count($selectedMhs) == 0) disabled @endif
                    class="w-full flex justify-center items-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed">
                    <span wire:loading.remove>Simpan & Terapkan</span>
                    <span wire:loading>Memproses...</span>
                </button>
                @error('selectedMhs') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>
        </div>

        {{-- KOLOM KANAN: TABEL MAHASISWA --}}
        <div class="lg:col-span-3">
            <div class="bg-white shadow overflow-hidden sm:rounded-lg border border-slate-200">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th scope="col" class="px-4 py-3 text-left w-10">
                                    <input type="checkbox" wire:model.live="selectAll" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">NIM</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Nama Mahasiswa</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Kelas</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">PA Saat Ini</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-200">
                            @forelse($mahasiswas as $mhs)
                            <tr class="hover:bg-indigo-50/30 transition-colors {{ in_array($mhs->id, $selectedMhs) ? 'bg-indigo-50' : '' }}">
                                <td class="px-4 py-3">
                                    <input type="checkbox" wire:model.live="selectedMhs" value="{{ $mhs->id }}" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm font-mono text-slate-600">
                                    {{ $mhs->nim }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm font-bold text-slate-800">
                                    {{ $mhs->nama_lengkap }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-xs text-slate-500">
                                    {{ $mhs->programKelas->nama_program }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm">
                                    @if($mhs->dosenWali)
                                        <span class="text-slate-700">{{ $mhs->dosenWali->nama_lengkap_gelar }}</span>
                                    @else
                                        <span class="text-red-400 italic text-xs">Belum Ada</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-slate-500 italic">
                                    Tidak ada data mahasiswa sesuai filter.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-4 py-3 border-t border-slate-200 bg-slate-50">
                    {{ $mahasiswas->links() }}
                </div>
            </div>
        </div>
    </div>
</div>