<div class="space-y-6">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-black text-[#002855]">Plotting PA Massal</h1>
            <p class="mt-2 text-sm text-slate-500">Tetapkan Dosen Wali untuk banyak mahasiswa sekaligus.</p>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="bg-emerald-50 border border-emerald-100 p-4 rounded-xl text-emerald-800 text-sm flex items-center shadow-sm animate-in fade-in slide-in-from-top-2">
            <svg class="w-5 h-5 mr-3 text-emerald-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            <span class="font-bold">{{ session('success') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        
        {{-- KOLOM KIRI: FILTER & AKSI --}}
        <div class="space-y-6">
            {{-- Filter Box --}}
            <div class="bg-white p-5 shadow-sm rounded-2xl border border-slate-200 space-y-4">
                <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest border-b pb-3 mb-2">Filter Data</h3>
                
                <div>
                    <label class="block text-[11px] font-bold text-[#002855] uppercase tracking-widest mb-2">Program Studi</label>
                    <div class="relative">
                        <select wire:model.live="filterProdiId" class="block w-full rounded-xl border-slate-200 bg-slate-50 py-3 pl-4 pr-10 text-sm focus:border-[#002855] focus:ring-[#002855] transition-shadow outline-none appearance-none font-bold text-slate-700">
                            @foreach($prodis as $p)
                                <option value="{{ $p->id }}">{{ $p->nama_prodi }}</option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-500">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-[#002855] uppercase tracking-widest mb-2">Angkatan</label>
                    <div class="relative">
                        <select wire:model.live="filterAngkatan" class="block w-full rounded-xl border-slate-200 bg-slate-50 py-3 pl-4 pr-10 text-sm focus:border-[#002855] focus:ring-[#002855] transition-shadow outline-none appearance-none font-bold text-slate-700">
                            @foreach($angkatans as $a)
                                <option value="{{ $a->id_tahun }}">{{ $a->id_tahun }}</option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-500">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-[#002855] uppercase tracking-widest mb-2">Status PA</label>
                    <div class="relative">
                        <select wire:model.live="filterStatusPa" class="block w-full rounded-xl border-slate-200 bg-slate-50 py-3 pl-4 pr-10 text-sm focus:border-[#002855] focus:ring-[#002855] transition-shadow outline-none appearance-none font-bold text-slate-700">
                            <option value="all">Semua Mahasiswa</option>
                            <option value="belum">Belum Punya PA</option>
                            <option value="sudah">Sudah Punya PA</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-500">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-[#002855] uppercase tracking-widest mb-2">Cari Nama/NIM</label>
                    <div class="relative">
                        <input type="text" wire:model.live.debounce.300ms="search" class="block w-full rounded-xl border-slate-200 bg-slate-50 py-3 pl-10 pr-4 text-sm focus:border-[#002855] focus:ring-[#002855] transition-shadow outline-none font-bold text-slate-700" placeholder="Search...">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center px-3 text-slate-400">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Action Box --}}
            <div class="bg-[#fcc000]/10 p-5 shadow-sm rounded-xl border border-[#fcc000]/20 space-y-4 sticky top-6">
                <h3 class="text-xs font-black text-[#002855] uppercase tracking-widest border-b border-[#fcc000]/20 pb-3 mb-2">Eksekusi Plotting</h3>
                
                <div>
                    <label class="block text-[11px] font-bold text-[#002855] uppercase tracking-widest mb-2">Pilih Dosen Wali Target</label>
                    <div class="relative">
                        <select wire:model="targetDosenId" class="block w-full rounded-xl border-slate-200 bg-white shadow-sm focus:border-[#002855] focus:ring-[#002855] text-sm transition-all outline-none font-bold text-slate-700 py-3 pl-4 pr-10">
                            <option value="">-- Pilih Dosen --</option>
                            @foreach($dosens as $d)
                                <option value="{{ $d->id }}">{{ $d->nama_lengkap_gelar }}</option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-500">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                        </div>
                    </div>
                    @error('targetDosenId') <span class="text-rose-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-between items-center text-xs text-[#002855] font-bold bg-white/50 p-2 rounded-lg">
                    <span>Terpilih: <span class="text-lg">{{ count($selectedMhs) }}</span> Mhs</span>
                    <button wire:click="resetSelection" class="text-rose-500 hover:underline">Reset</button>
                </div>

                <button wire:click="simpanPloting" 
                    wire:loading.attr="disabled"
                    @if(count($selectedMhs) == 0) disabled @endif
                    class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-xl shadow-lg shadow-indigo-900/20 text-sm font-black uppercase tracking-widest text-white bg-[#002855] hover:bg-[#001a38] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#002855] disabled:opacity-50 disabled:cursor-not-allowed transition-all hover:scale-[1.02]">
                    <span wire:loading.remove>Simpan & Terapkan</span>
                    <span wire:loading>Memproses...</span>
                </button>
                @error('selectedMhs') <span class="text-rose-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
            </div>
        </div>

        {{-- KOLOM KANAN: TABEL MAHASISWA --}}
        <div class="lg:col-span-3">
            <div class="bg-white shadow-sm overflow-hidden rounded-2xl border border-slate-200">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100">
                        <thead class="bg-[#002855] text-white">
                            <tr>
                                <th scope="col" class="px-4 py-4 text-left w-10">
                                    <input type="checkbox" wire:model.live="selectAll" class="rounded border-slate-400 text-[#fcc000] shadow-sm focus:border-[#fcc000] focus:ring focus:ring-[#fcc000] focus:ring-opacity-50 h-4 w-4">
                                </th>
                                <th class="px-6 py-4 text-left text-[10px] font-bold uppercase tracking-widest">NIM</th>
                                <th class="px-6 py-4 text-left text-[10px] font-bold uppercase tracking-widest">Nama Mahasiswa</th>
                                <th class="px-6 py-4 text-left text-[10px] font-bold uppercase tracking-widest">Kelas</th>
                                <th class="px-6 py-4 text-left text-[10px] font-bold uppercase tracking-widest">PA Saat Ini</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-50">
                            @forelse($mahasiswas as $mhs)
                            <tr class="hover:bg-slate-50 transition-colors {{ in_array($mhs->id, $selectedMhs) ? 'bg-indigo-50/50' : '' }}">
                                <td class="px-4 py-4">
                                    <input type="checkbox" wire:model.live="selectedMhs" value="{{ $mhs->id }}" class="rounded border-slate-300 text-[#002855] shadow-sm focus:border-[#002855] focus:ring focus:ring-[#002855] focus:ring-opacity-50 h-4 w-4">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-mono font-bold text-slate-600 bg-slate-50/50">
                                    {{ $mhs->nim }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-slate-800">
                                    {{ $mhs->nama_lengkap }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs text-slate-500">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide bg-slate-100 text-slate-600 border border-slate-200">
                                        {{ $mhs->programKelas->nama_program }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if($mhs->dosenWali)
                                        <span class="text-indigo-700 font-bold text-xs">{{ $mhs->dosenWali->nama_lengkap_gelar }}</span>
                                    @else
                                        <span class="text-rose-400 italic text-[10px] font-bold bg-rose-50 px-2 py-1 rounded">Belum Ada</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="bg-slate-50 p-4 rounded-full mb-3">
                                            <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                                        </div>
                                        <p class="text-slate-500 font-medium">Tidak ada data mahasiswa sesuai filter.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                    {{ $mahasiswas->links() }}
                </div>
            </div>
        </div>
    </div>
</div>