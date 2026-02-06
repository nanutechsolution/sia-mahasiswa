<div class="space-y-8">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-[#002855] tracking-tight">Plotting Dosen Wali</h1>
            <p class="text-slate-500 text-sm mt-1">Tetapkan Pembimbing Akademik (PA) untuk mahasiswa secara massal.</p>
        </div>
        
        <div class="flex items-center gap-2 text-xs font-medium text-slate-500 bg-white px-3 py-1.5 rounded-lg border border-slate-200 shadow-sm">
            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
            <span>Data Real-time</span>
        </div>
    </div>

    {{-- Alert Messages --}}
    @if (session()->has('success'))
    <div class="p-4 bg-emerald-50 border border-emerald-100 text-emerald-800 rounded-xl text-sm font-bold flex items-center shadow-sm animate-in fade-in slide-in-from-top-2">
        <svg class="w-5 h-5 mr-3 text-emerald-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
        </svg>
        {{ session('success') }}
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8 items-start">
        
        {{-- KOLOM KIRI: FILTER & CONTROL (Sticky) --}}
        <div class="space-y-6 lg:sticky lg:top-6">
            
            {{-- Filter Box --}}
            <div class="bg-white p-5 shadow-sm rounded-2xl border border-slate-200 space-y-5">
                <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest border-b border-slate-100 pb-3">1. Filter Mahasiswa</h3>
                
                <div>
                    <label class="block text-[10px] font-bold text-[#002855] uppercase tracking-widest mb-2">Program Studi</label>
                    <div class="relative">
                        <select wire:model.live="filterProdiId" class="block w-full rounded-xl border-slate-200 bg-slate-50 py-2.5 pl-4 pr-10 text-sm focus:border-[#002855] focus:ring-[#002855] font-bold text-slate-700">
                            @foreach($prodis as $p)
                                <option value="{{ $p->id }}">{{ $p->jenjang }} - {{ $p->nama_prodi }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-[#002855] uppercase tracking-widest mb-2">Angkatan</label>
                    <div class="relative">
                        <select wire:model.live="filterAngkatan" class="block w-full rounded-xl border-slate-200 bg-slate-50 py-2.5 pl-4 pr-10 text-sm focus:border-[#002855] focus:ring-[#002855] font-bold text-slate-700">
                            @foreach($angkatans as $a)
                                <option value="{{ $a->id_tahun }}">{{ $a->id_tahun }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-[#002855] uppercase tracking-widest mb-2">Status PA</label>
                    <div class="relative">
                        <select wire:model.live="filterStatusPa" class="block w-full rounded-xl border-slate-200 bg-slate-50 py-2.5 pl-4 pr-10 text-sm focus:border-[#002855] focus:ring-[#002855] font-bold text-slate-700">
                            <option value="all">Semua Mahasiswa</option>
                            <option value="belum">Belum Punya PA</option>
                            <option value="sudah">Sudah Punya PA</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-[#002855] uppercase tracking-widest mb-2">Cari Nama/NIM</label>
                    <div class="relative">
                        <input type="text" wire:model.live.debounce.300ms="search" class="block w-full rounded-xl border-slate-200 bg-slate-50 py-2.5 pl-10 pr-4 text-sm focus:border-[#002855] focus:ring-[#002855] font-bold text-slate-700" placeholder="Ketik pencarian...">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center px-3 text-slate-400">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Action Box --}}
            <div class="bg-[#fcc000]/10 p-5 shadow-sm rounded-2xl border border-[#fcc000]/30 space-y-4">
                <h3 class="text-xs font-black text-[#002855] uppercase tracking-widest border-b border-[#fcc000]/20 pb-3 mb-2">2. Eksekusi Plotting</h3>
                
                {{-- Searchable Dosen Dropdown --}}
                <div x-data="{
                        open: false,
                        search: '',
                        selectedId: @entangle('targetDosenId'),
                        selectedName: '-- Pilih Dosen --',
                        dosens: {{ $dosens->map(function($d) {
                            return [
                                'id' => $d->id,
                                'name' => ($d->nama_lengkap_gelar ?? 'Nama Tidak Tersedia') . ($d->nidn ? ' (' . $d->nidn . ')' : ''),
                                'search_text' => strtolower(($d->nama_lengkap_gelar ?? '') . ' ' . ($d->nidn ?? ''))
                            ];
                        })->toJson() }},
                        get filtered() {
                            if (this.search === '') return this.dosens;
                            return this.dosens.filter(d => d.search_text.includes(this.search.toLowerCase()));
                        },
                        init() {
                            this.$watch('selectedId', value => {
                                if(!value) this.selectedName = '-- Pilih Dosen --';
                                else {
                                    let found = this.dosens.find(d => d.id == value);
                                    if(found) this.selectedName = found.name;
                                }
                            })
                        }
                    }"
                    class="relative"
                >
                    <label class="block text-[10px] font-bold text-[#002855] uppercase tracking-widest mb-2">Target Dosen Wali</label>
                    
                    <button type="button" @click="open = !open; if(open) $nextTick(() => $refs.input.focus())" @click.away="open = false"
                        class="relative w-full bg-white border border-slate-300 rounded-xl py-3 pl-4 pr-10 text-left cursor-default focus:outline-none focus:ring-1 focus:ring-[#002855] focus:border-[#002855] sm:text-sm font-bold shadow-sm">
                        <span class="block truncate text-slate-700" x-text="selectedName"></span>
                        <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <svg class="h-5 w-5 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </span>
                    </button>

                    <div x-show="open" class="absolute z-10 mt-1 w-full bg-white shadow-2xl rounded-xl max-h-60 overflow-auto border border-slate-100 ring-1 ring-black ring-opacity-5 focus:outline-none text-sm" style="display: none;">
                        <div class="sticky top-0 z-10 bg-white p-2 border-b border-slate-100">
                            <input x-ref="input" x-model="search" type="text" class="block w-full border-slate-300 rounded-lg p-2 text-sm focus:ring-[#002855] focus:border-[#002855]" placeholder="Cari nama dosen...">
                        </div>
                        <ul class="py-1">
                            <template x-for="dosen in filtered" :key="dosen.id">
                                <li @click="selectedId = dosen.id; selectedName = dosen.name; open = false;" 
                                    class="text-slate-900 cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-indigo-50 transition-colors">
                                    <span class="font-bold block truncate" x-text="dosen.name"></span>
                                </li>
                            </template>
                            <div x-show="filtered.length === 0" class="px-3 py-2 text-slate-500 italic text-center text-xs">Tidak ditemukan</div>
                        </ul>
                    </div>
                </div>
                @error('targetDosenId') <span class="text-rose-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror

                <div class="flex justify-between items-center text-xs text-[#002855] font-bold bg-white/60 p-3 rounded-xl border border-white">
                    <span>Terpilih: <span class="text-lg font-black">{{ count($selectedMhs) }}</span> Mhs</span>
                    <button wire:click="resetSelection" class="text-rose-500 hover:text-rose-700 hover:underline">Reset</button>
                </div>

                <button wire:click="simpanPloting" 
                    wire:loading.attr="disabled"
                    @if(count($selectedMhs) == 0) disabled @endif
                    class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-xl shadow-lg shadow-indigo-900/20 text-sm font-black uppercase tracking-widest text-white bg-[#002855] hover:bg-[#001a38] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#002855] disabled:opacity-50 disabled:cursor-not-allowed transition-all hover:scale-[1.02]">
                    <span wire:loading.remove>Simpan & Terapkan</span>
                    <span wire:loading>Memproses...</span>
                </button>
                @error('selectedMhs') <span class="text-rose-500 text-xs font-bold mt-1 block text-center">{{ $message }}</span> @enderror
            </div>
        </div>

        {{-- KOLOM KANAN: TABEL MAHASISWA --}}
        <div class="lg:col-span-3">
            <div class="bg-white shadow-sm overflow-hidden rounded-2xl border border-slate-200 relative">
                
                {{-- Loading Overlay --}}
                <div wire:loading.flex wire:target="filterProdiId, filterAngkatan, filterStatusPa, search, gotoPage, nextPage, previousPage" 
                     class="absolute inset-0 z-20 bg-white/60 backdrop-blur-[1px] items-center justify-center hidden">
                     <div class="bg-white p-4 rounded-xl shadow-xl border border-slate-100 flex flex-col items-center">
                         <svg class="w-8 h-8 text-[#002855] animate-spin mb-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                         <span class="text-xs font-bold text-slate-500 animate-pulse">Memuat Data...</span>
                     </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100">
                        <thead class="bg-[#002855] text-white">
                            <tr>
                                <th scope="col" class="px-4 py-4 text-left w-10">
                                    <input type="checkbox" wire:model.live="selectAll" class="rounded border-slate-400 text-[#fcc000] shadow-sm focus:border-[#fcc000] focus:ring focus:ring-[#fcc000] focus:ring-opacity-50 h-4 w-4 cursor-pointer">
                                </th>
                                <th class="px-6 py-4 text-left text-[10px] font-bold uppercase tracking-widest">NIM</th>
                                <th class="px-6 py-4 text-left text-[10px] font-bold uppercase tracking-widest">Nama Mahasiswa</th>
                                <th class="px-6 py-4 text-left text-[10px] font-bold uppercase tracking-widest">Kelas</th>
                                <th class="px-6 py-4 text-left text-[10px] font-bold uppercase tracking-widest">Dosen Wali (PA)</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-50">
                            @forelse($mahasiswas as $mhs)
                            <tr class="hover:bg-slate-50 transition-colors {{ in_array($mhs->id, $selectedMhs) ? 'bg-amber-50/40' : '' }}">
                                <td class="px-4 py-4">
                                    <input type="checkbox" wire:model.live="selectedMhs" value="{{ $mhs->id }}" class="rounded border-slate-300 text-[#002855] shadow-sm focus:border-[#002855] focus:ring focus:ring-[#002855] focus:ring-opacity-50 h-4 w-4 cursor-pointer">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-mono font-bold text-slate-600">
                                    <span class="bg-slate-100 px-2 py-0.5 rounded border border-slate-200">{{ $mhs->nim }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-slate-800">
                                    {{ $mhs->person->nama_lengkap ?? $mhs->nama_lengkap }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs text-slate-500">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide bg-sky-50 text-sky-700 border border-sky-100">
                                        {{ $mhs->programKelas->nama_program ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if($mhs->dosenWali)
                                        <div class="flex items-center gap-2">
                                            <div class="w-2 h-2 rounded-full bg-indigo-500"></div>
                                            <span class="text-indigo-700 font-bold text-xs">{{ $mhs->dosenWali->nama_lengkap_gelar ?? $mhs->dosenWali->person->nama_lengkap }}</span>
                                        </div>
                                    @else
                                        <span class="text-rose-500 italic text-[10px] font-bold bg-rose-50 px-2 py-1 rounded border border-rose-100">Belum Ada PA</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center opacity-50">
                                        <svg class="w-12 h-12 text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                                        <p class="text-slate-500 font-bold">Tidak ada data mahasiswa sesuai filter.</p>
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