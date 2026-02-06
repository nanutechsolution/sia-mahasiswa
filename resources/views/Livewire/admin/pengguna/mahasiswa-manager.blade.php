<div class="space-y-8">
    {{-- Header & Toolbar --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-[#002855]">Data Mahasiswa</h1>
            <p class="text-slate-500 text-sm mt-1">Manajemen biodata, status dispensasi, dan akun login mahasiswa aktif.</p>
        </div>

        @if(!$showForm)
        <div class="flex gap-2">
            <button wire:click="openImport" class="inline-flex items-center px-4 py-2.5 bg-white border border-slate-300 text-slate-700 rounded-xl font-bold text-sm shadow-sm hover:bg-slate-50 transition-all">
                <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                </svg>
                Import CSV
            </button>
            <button wire:click="create"
                class="inline-flex items-center px-5 py-2.5 bg-[#fcc000] text-[#002855] rounded-xl font-black text-sm shadow-lg shadow-orange-500/20 hover:bg-[#fbbf24] hover:scale-105 transition-all">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" />
                </svg>
                Input Baru
            </button>
        </div>
        @endif
    </div>

    {{-- Alert Messages --}}
    @if (session()->has('success'))
    <div class="p-4 bg-emerald-50 border border-emerald-100 text-emerald-800 rounded-xl text-sm font-bold flex items-center shadow-sm animate-in fade-in slide-in-from-top-2">
        <svg class="w-5 h-5 mr-3 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
        </svg>
        {{ session('success') }}
    </div>
    @endif
    @if (session()->has('error'))
    <div class="p-4 bg-rose-50 border border-rose-100 text-rose-800 rounded-xl text-sm font-bold shadow-sm animate-in fade-in">
        {{ session('error') }}
    </div>
    @endif

    {{-- IMPORT MODAL --}}
    @if($showImportModal)
    <div class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 animate-in fade-in duration-200">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-lg overflow-hidden border border-white/20">
            <div class="bg-[#002855] px-8 py-6 text-white">
                <h3 class="text-lg font-black uppercase tracking-widest leading-none">Import Data Mahasiswa</h3>
                <p class="text-[10px] font-bold uppercase opacity-60 mt-2">Migrasi data massal via CSV</p>
            </div>

            <div class="p-8 space-y-6">
                <div class="bg-slate-50 p-4 rounded-xl border border-slate-100 text-sm text-slate-600">
                    <p class="font-bold mb-2 text-[#002855]">Format Wajib CSV:</p>
                    <code class="block bg-white p-2 rounded border border-slate-200 text-[10px] text-indigo-600 font-mono mb-4 break-all leading-relaxed">
                        NIM, Nama Lengkap, NIK, Email, No HP, Kode Prodi (Internal), Kode Kelas (REG/EKS), Tahun Angkatan, Gender (L/P)
                    </code>
                    <button wire:click="downloadTemplate" class="mt-3 text-xs font-bold text-[#002855] hover:underline flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Download Template CSV
                    </button>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-2">Upload File CSV</label>
                    <input type="file" wire:model="fileImport" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-[#002855] file:text-white hover:file:bg-[#001a38] transition-colors cursor-pointer">
                    <div wire:loading wire:target="fileImport" class="text-xs text-indigo-600 mt-2 font-bold animate-pulse">Sedang mengupload...</div>
                    @error('fileImport') <span class="text-rose-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="pt-4 flex justify-end gap-3">
                    <button wire:click="batal" class="px-6 py-2 text-sm font-bold text-slate-400 hover:text-slate-600">Batal</button>
                    <button wire:click="processImport" wire:loading.attr="disabled" class="bg-emerald-600 text-white px-6 py-2 rounded-xl font-bold text-sm shadow-md hover:bg-emerald-700 transition-all flex items-center disabled:opacity-50">
                        <span wire:loading.remove wire:target="processImport">Mulai Import</span>
                        <span wire:loading wire:target="processImport">Memproses...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Filters --}}
    <div class="bg-white p-5 shadow-sm rounded-2xl border border-slate-200 grid grid-cols-1 md:grid-cols-3 gap-6">
        <div>
            <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Program Studi</label>
            <div class="relative">
                <select wire:model.live="filterProdiId" class="block w-full rounded-xl border-slate-200 bg-slate-50 text-slate-900 py-2.5 pl-4 pr-10 text-sm focus:border-[#002855] focus:ring-[#002855] transition-shadow outline-none appearance-none font-bold">
                    <option value="">Semua Prodi</option>
                    @foreach($prodis as $p) <option value="{{ $p->id }}">{{ $p->jenjang }} - {{ $p->nama_prodi }}</option> @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-500">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                </div>
            </div>
        </div>
        <div>
            <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Angkatan</label>
            <div class="relative">
                <select wire:model.live="filterAngkatan" class="block w-full rounded-xl border-slate-200 bg-slate-50 text-slate-900 py-2.5 pl-4 pr-10 text-sm focus:border-[#002855] focus:ring-[#002855] transition-shadow outline-none appearance-none font-bold">
                    <option value="">Semua Angkatan</option>
                    @foreach($angkatans as $akt) <option value="{{ $akt->id_tahun }}">{{ $akt->id_tahun }}</option> @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-500">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                </div>
            </div>
        </div>
        <div>
            <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Cari Data</label>
            <div class="relative">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="NIM atau Nama..." class="block w-full rounded-xl border-slate-200 bg-slate-50 py-2.5 pl-10 pr-4 text-sm focus:border-[#002855] focus:ring-[#002855] transition-shadow outline-none font-bold placeholder-slate-400">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center px-3 text-slate-400">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Form Section (Create/Edit) --}}
    @if($showForm)
    <div class="bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden animate-in zoom-in-95 duration-200">
        <div class="px-8 py-5 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
            <h3 class="text-sm font-bold text-[#002855] uppercase tracking-wider flex items-center gap-2">
                @if($editMode) Edit Biodata Mahasiswa @else Registrasi Mahasiswa Baru @endif
            </h3>
            <button wire:click="batal" class="text-slate-400 hover:text-slate-600 transition-colors">&times;</button>
        </div>

        <div class="p-8 space-y-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Group 1: Identitas -->
                <div class="space-y-4">
                    <h4 class="text-xs font-bold text-[#002855] uppercase border-l-4 border-[#fcc000] pl-3 tracking-widest">1. Identitas & Akun</h4>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">NIM *</label>
                        <input type="text" wire:model="nim" class="block w-full rounded-lg border-slate-300 bg-slate-50 p-2.5 focus:border-[#002855] focus:ring-[#002855] text-sm font-bold placeholder-slate-400" placeholder="24xxxxx">
                        @error('nim') <span class="text-rose-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Nama Lengkap *</label>
                        <input type="text" wire:model="nama_lengkap" class="block w-full rounded-lg border-slate-300 bg-slate-50 p-2.5 focus:border-[#002855] focus:ring-[#002855] text-sm font-bold placeholder-slate-400" placeholder="Nama Mahasiswa">
                        @error('nama_lengkap') <span class="text-rose-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Email Pribadi</label>
                        <input type="email" wire:model="email_pribadi" class="block w-full rounded-lg border-slate-300 bg-slate-50 p-2.5 focus:border-[#002855] focus:ring-[#002855] text-sm">
                        @error('email_pribadi') <span class="text-rose-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Nomor HP / WhatsApp</label>
                        <input type="text" wire:model="nomor_hp" class="block w-full rounded-lg border-slate-300 bg-slate-50 p-2.5 focus:border-[#002855] focus:ring-[#002855] text-sm">
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">
                            {{ $editMode ? 'Reset Password (Opsional)' : 'Password Awal *' }}
                        </label>
                        <input type="password" wire:model="password_baru" class="block w-full rounded-lg border-slate-300 bg-slate-50 p-2.5 focus:border-[#002855] focus:ring-[#002855] text-sm" placeholder="Min. 6 Karakter">
                    </div>
                </div>

                <!-- Group 2: Akademik -->
                <div class="space-y-4">
                    <h4 class="text-xs font-bold text-[#002855] uppercase border-l-4 border-[#fcc000] pl-3 tracking-widest">2. Informasi Akademik</h4>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Tahun Angkatan *</label>
                            <select wire:model="angkatan_id" class="block w-full rounded-lg border-slate-300 bg-slate-50 p-2.5 focus:border-[#002855] focus:ring-[#002855] text-sm font-bold text-slate-700">
                                @foreach($angkatans as $akt) <option value="{{ $akt->id_tahun }}">{{ $akt->id_tahun }}</option> @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Program Kelas *</label>
                            <select wire:model="program_kelas_id" class="block w-full rounded-lg border-slate-300 bg-slate-50 p-2.5 focus:border-[#002855] focus:ring-[#002855] text-sm font-bold text-slate-700">
                                <option value="">Pilih Kelas</option>
                                @foreach($programKelasList as $pk) <option value="{{ $pk->id }}">{{ $pk->nama_program }}</option> @endforeach
                            </select>
                            @error('program_kelas_id') <span class="text-rose-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Program Studi *</label>
                        <select wire:model="prodi_id" class="block w-full rounded-lg border-slate-300 bg-slate-50 p-2.5 focus:border-[#002855] focus:ring-[#002855] text-sm font-bold text-slate-700">
                            <option value="">Pilih Prodi</option>
                            @foreach($prodis as $p) <option value="{{ $p->id }}">{{ $p->nama_prodi }}</option> @endforeach
                        </select>
                    </div>

                    {{-- Dosen Wali (Searchable) --}}
                    <div class="bg-[#002855]/5 p-4 rounded-xl border border-[#002855]/10"
                        wire:ignore {{-- Ignore to prevent re-render issues while searching --}}
                        x-data="{
                            open: false,
                            search: '',
                            selectedId: @entangle('dosen_wali_id'),
                            selectedName: '{{ $dosen_wali_id ? ($dosens->firstWhere('id', $dosen_wali_id)->person->nama_lengkap ?? 'Nama Tidak Tersedia') : '-- Belum Ditentukan --' }}',
                            dosens: {{ $dosens->map(function($d) {
                                return [
                                    'id' => $d->id,
                                    'name' => ($d->nama_lengkap_gelar ?? 'Nama Tidak Tersedia') . ($d->nidn ? ' (' . $d->nidn . ')' : ''),
                                    'search_text' => strtolower(($d->nama_lengkap_gelar ?? '') . ' ' . ($d->nidn ?? ''))
                                ];
                            })->toJson() }},
                            
                            init() {
                                if(this.selectedId) {
                                    const found = this.dosens.find(d => d.id == this.selectedId);
                                    if(found) this.selectedName = found.name;
                                }
                                
                                // Watch for external changes
                                this.$watch('selectedId', value => {
                                    if(!value) {
                                        this.selectedName = '-- Belum Ditentukan --';
                                    } else {
                                        const found = this.dosens.find(d => d.id == value);
                                        if(found) this.selectedName = found.name;
                                    }
                                });
                            },
                            
                            get filteredDosens() {
                                if (this.search === '') return this.dosens;
                                return this.dosens.filter(d => d.search_text.includes(this.search.toLowerCase()));
                            }
                        }"
                    >
                        <label class="block text-[11px] font-bold text-[#002855] uppercase tracking-widest mb-2">Dosen Wali (PA)</label>
                        
                        <div class="relative">
                            <button type="button" 
                                @click="open = !open; if(open) $nextTick(() => $refs.searchInput.focus())"
                                @click.away="open = false"
                                class="relative w-full bg-white border border-slate-300 rounded-lg shadow-sm pl-3 pr-10 py-2.5 text-left cursor-default focus:outline-none focus:ring-1 focus:ring-[#002855] focus:border-[#002855] text-sm font-medium text-slate-700"
                            >
                                <span class="block truncate" x-text="selectedName"></span>
                                <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                    <svg class="h-5 w-5 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </span>
                            </button>

                            <div x-show="open" 
                                x-transition:leave="transition ease-in duration-100"
                                x-transition:leave-start="opacity-100"
                                x-transition:leave-end="opacity-0"
                                class="absolute z-10 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm"
                                style="display: none;"
                            >
                                <div class="sticky top-0 z-10 bg-white px-2 py-2 border-b border-slate-100">
                                    <input type="text" 
                                        x-model="search"
                                        x-ref="searchInput"
                                        class="block w-full border-slate-300 rounded-md text-sm focus:ring-[#002855] focus:border-[#002855] p-2" 
                                        placeholder="Cari dosen...">
                                </div>

                                <ul class="pt-1" role="listbox">
                                    <li class="text-slate-900 cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-[#002855]/5"
                                        @click="selectedId = null; selectedName = '-- Belum Ditentukan --'; open = false;">
                                        <span class="font-normal block truncate text-slate-500 italic">-- Belum Ditentukan --</span>
                                    </li>
                                    
                                    <template x-for="dosen in filteredDosens" :key="dosen.id">
                                        <li class="text-slate-900 cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-[#002855]/5"
                                            @click="selectedId = dosen.id; selectedName = dosen.name; open = false;"
                                        >
                                            <span class="font-normal block truncate" x-text="dosen.name"></span>
                                            
                                            <span x-show="selectedId == dosen.id" class="text-[#002855] absolute inset-y-0 right-0 flex items-center pr-4">
                                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                </svg>
                                            </span>
                                        </li>
                                    </template>
                                    
                                    <div x-show="filteredDosens.length === 0" class="px-3 py-2 text-slate-500 italic text-center">
                                        Dosen tidak ditemukan
                                    </div>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Dispensasi Section --}}
            <div class="bg-[#fcc000]/10 rounded-xl p-5 border border-[#fcc000]/20 flex flex-col gap-3">
                <div class="flex items-center gap-3">
                    <div class="p-1.5 bg-[#fcc000] rounded-lg text-[#002855]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h4 class="text-sm font-bold text-[#002855] uppercase tracking-tight">Dispensasi Keuangan</h4>
                </div>
                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" wire:model="bebas_keuangan" class="mt-1 h-5 w-5 text-[#002855] border-slate-300 rounded focus:ring-[#fcc000] bg-white">
                    <div>
                        <span class="block text-sm font-bold text-slate-800">Aktifkan Dispensasi (Bebas Syarat Bayar)</span>
                        <span class="block text-xs text-slate-500 mt-0.5">Jika dicentang, mahasiswa ini <strong>BISA MENGISI KRS</strong> meskipun pembayaran belum mencapai target.</span>
                    </div>
                </label>
            </div>

            <div class="pt-6 border-t border-slate-100 flex justify-end gap-3">
                <button wire:click="batal" class="px-6 py-2.5 text-sm font-bold text-slate-500 hover:text-slate-600 transition-colors">Batal</button>
                <button wire:click="save" class="px-8 py-2.5 bg-[#002855] text-white rounded-xl text-sm font-bold shadow-lg shadow-indigo-200 hover:bg-[#001a38] hover:scale-105 transition-all">Simpan Data</button>
            </div>
        </div>
    </div>
    @endif

    <!-- Tabel Data -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden relative">
        {{-- Loading Overlay --}}
        <div wire:loading.flex wire:target="search, filterProdiId, filterAngkatan, gotoPage, nextPage, previousPage" 
             class="absolute inset-0 z-20 bg-white/60 backdrop-blur-[1px] items-center justify-center hidden">
             <div class="flex flex-col items-center justify-center p-4 bg-white rounded-2xl shadow-xl border border-slate-100">
                 <svg class="w-8 h-8 text-[#002855] animate-spin mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                     <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                     <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                 </svg>
                 <span class="text-xs font-bold text-slate-500 animate-pulse">Memperbarui Data...</span>
             </div>
        </div>

        <div class="px-6 py-3 border-b border-slate-100 bg-slate-50/80 flex items-center justify-between">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Total Data: {{ $mahasiswas->total() }}</span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-[#002855] text-white">
                    <tr>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest">NIM / Identitas</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest">Nama Mahasiswa</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest">Info Akademik</th>
                        <th class="px-6 py-4 text-center text-[10px] font-bold uppercase tracking-widest">Kelas</th>
                        <th class="px-6 py-4 text-right text-[10px] font-bold uppercase tracking-widest">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($mahasiswas as $mhs)
                    <tr class="hover:bg-slate-50/80 transition-colors group">
                        <td class="px-6 py-4 align-top">
                            <span class="font-mono text-sm font-black text-[#002855] bg-indigo-50 px-2 py-0.5 rounded w-fit">{{ $mhs->nim }}</span>
                            @if($mhs->data_tambahan['bebas_keuangan'] ?? false)
                            <div class="mt-2"><span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-black bg-[#fcc000] text-[#002855]">DISPENSASI</span></div>
                            @endif
                        </td>
                        <td class="px-6 py-4 align-top">
                            <div class="text-sm font-bold text-slate-800">{{ $mhs->person->nama_lengkap }}</div>
                            <div class="text-[10px] text-slate-400 mt-1 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                User: {{ $mhs->user->username ?? 'No User' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 align-top">
                            <div class="text-xs font-bold text-slate-600 uppercase">{{ $mhs->prodi->nama_prodi }}</div>
                            <div class="text-[10px] text-slate-400 font-bold mt-1">Angkatan {{ $mhs->angkatan_id }}</div>
                            <div class="mt-2 flex items-center text-[10px] text-[#002855] font-medium bg-indigo-50 px-2 py-1 rounded w-fit border border-indigo-100">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                                PA: {{ $mhs->dosenWali->nama_lengkap_gelar ?? 'Belum Ada' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 align-top text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide bg-sky-100 text-sky-700 border border-sky-200">
                                {{ $mhs->programKelas->nama_program }}
                            </span>
                        </td>
                        <td class="px-6 py-4 align-top text-right">
                            <button wire:click="edit('{{ $mhs->id }}')" class="p-2 text-[#002855] hover:bg-[#002855]/10 rounded-lg transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg></button>
                            <button wire:click="delete('{{ $mhs->id }}')" wire:confirm="Hapus mahasiswa ini? User login juga akan dihapus." class="p-2 text-rose-600 hover:bg-rose-50 rounded-lg transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg></button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center">
                            <p class="text-slate-400 font-medium italic">Tidak ada data mahasiswa ditemukan.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">{{ $mahasiswas->links() }}</div>
    </div>
</div>