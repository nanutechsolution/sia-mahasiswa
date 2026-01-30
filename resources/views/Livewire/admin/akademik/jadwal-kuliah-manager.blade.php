<div class="space-y-8">
    {{-- Header & Toolbar --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-[#002855]">Manajemen Jadwal Kuliah</h1>
            <p class="text-slate-500 text-sm mt-1">Kelola distribusi kelas, penugasan dosen, dan alokasi ruangan.</p>
        </div>

        @if(!$showForm)
        <button wire:click="create"
            class="inline-flex items-center px-5 py-2.5 bg-[#fcc000] text-[#002855] rounded-xl font-black text-sm shadow-lg shadow-orange-500/20 hover:bg-[#fbbf24] hover:scale-105 transition-all">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
            </svg>
            Buat Kelas Baru
        </button>
        @endif
    </div>

    {{-- Filters --}}
    <div class="bg-white p-5 shadow-sm rounded-2xl border border-slate-200 grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Periode Semester</label>
            <div class="relative">
                <select wire:model.live="filterSemesterId" class="block w-full rounded-xl border-slate-200 bg-white text-slate-900 py-3 pl-4 pr-10 text-sm focus:border-[#002855] focus:ring-[#002855] transition-shadow outline-none appearance-none font-bold">
                    @foreach($semesters as $sem)
                    <option value="{{ $sem->id }}">{{ $sem->nama_tahun }} {{ $sem->is_active ? '(Aktif)' : '' }}</option>
                    @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-500">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                </div>
            </div>
        </div>
        <div>
            <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Filter List Prodi</label>
            <div class="relative">
                <select wire:model.live="filterProdiId" class="block w-full rounded-xl border-slate-200 bg-white text-slate-900 py-3 pl-4 pr-10 text-sm focus:border-[#002855] focus:ring-[#002855] transition-shadow outline-none appearance-none font-bold">
                    @foreach($prodis as $prodi)
                    <option value="{{ $prodi->id }}">{{ $prodi->jenjang }} - {{ $prodi->nama_prodi }}</option>
                    @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-500">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Feedback Messages --}}
    <div id="alert-box">
        @if (session()->has('success'))
        <div class="bg-emerald-50 border border-emerald-100 p-4 rounded-xl text-emerald-800 text-sm flex items-center shadow-sm animate-in fade-in slide-in-from-top-2">
            <svg class="w-5 h-5 mr-3 text-emerald-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            <span class="font-bold">{{ session('success') }}</span>
        </div>
        @endif

        @if (session()->has('error'))
        <div class="bg-rose-50 border-l-4 border-rose-500 p-4 rounded-r-xl text-rose-800 text-sm shadow-md animate-in shake">
            <div class="flex items-start">
                <svg class="w-5 h-5 mr-3 text-rose-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
                <div>
                    <span class="font-black block uppercase text-xs mb-1">Gagal Menyimpan (Bentrok / Error)</span>
                    <span class="font-medium">{{ session('error') }}</span>
                </div>
            </div>
        </div>
        @endif
    </div>

    {{-- Form Section --}}
    @if($showForm)
    <div class="bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden animate-in zoom-in-95 duration-200">
        <div class="px-8 py-5 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
            <h3 class="text-sm font-bold text-[#002855] uppercase tracking-wider flex items-center gap-2">
                @if($editMode)
                    <svg class="w-5 h-5 text-[#fcc000]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                    Edit Jadwal Kelas
                @else
                    <svg class="w-5 h-5 text-[#002855]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    Setup Kelas Baru
                @endif
            </h3>
            <button wire:click="batal" class="text-slate-400 hover:text-slate-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>

        {{-- INFO CONTEXT FORM --}}
        <div class="px-8 py-3 bg-[#fcc000]/10 border-b border-[#fcc000]/20 flex items-center gap-2 text-[#002855] text-xs">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <span>
                <span class="font-bold">Perhatian:</span> Jadwal yang Anda buat akan didaftarkan untuk Semester <span class="font-black underline">{{ $semesters->find($filterSemesterId)->nama_tahun }}</span>.
            </span>
        </div>

        <div class="p-8 space-y-8">
            {{-- Group 1: Mata Kuliah & Dosen --}}
            <div class="space-y-4">
                <h4 class="text-xs font-bold text-[#002855] uppercase border-l-4 border-[#fcc000] pl-3 tracking-widest">1. Penugasan Akademik</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 bg-slate-50 p-6 rounded-xl border border-slate-100">
                    {{-- DROPDOWN: Filter Prodi Form --}}
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-2">Pilih Program Studi *</label>
                        <select wire:model.live="form_prodi_id" class="block w-full rounded-lg border-slate-300 bg-white text-slate-900 focus:border-[#002855] focus:ring-[#002855] text-sm transition-all">
                            <option value="" class="text-slate-500">-- Pilih Prodi --</option>
                            @foreach($prodis as $prodi)
                            <option value="{{ $prodi->id }}" class="text-slate-900 bg-white">{{ $prodi->jenjang }} - {{ $prodi->nama_prodi }}</option>
                            @endforeach
                        </select>
                        @error('form_prodi_id') <span class="text-rose-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- SEARCHABLE SELECT: MATA KULIAH --}}
                    <div x-data="{ open: false }" class="relative">
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-2">Pilih Mata Kuliah *</label>
                        
                        <!-- Input Display (Readonly or Search Trigger) -->
                        <div class="relative">
                            <input type="text" 
                                wire:model.live="searchMk"
                                @focus="open = true"
                                @click.away="open = false"
                                placeholder="{{ $selectedMkName ?: '-- Cari Mata Kuliah --' }}"
                                class="block w-full rounded-lg border-slate-300 bg-white text-slate-900 focus:border-[#002855] focus:ring-[#002855] text-sm transition-all pr-8"
                                @if(!$form_prodi_id) disabled @endif
                            >
                            <!-- Ikon Cari/Loading -->
                            <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                <svg wire:loading.remove wire:target="searchMk" class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                                <svg wire:loading wire:target="searchMk" class="animate-spin w-4 h-4 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            </div>
                        </div>

                        <!-- Dropdown List -->
                        @if(!empty($searchMk) && !empty($formMks))
                        <div x-show="open" class="absolute z-50 mt-1 w-full bg-white shadow-xl max-h-60 rounded-lg py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm">
                            @foreach($formMks as $mk)
                            <div wire:click="pilihMk('{{ $mk->id }}', '{{ $mk->nama_mk }}', '{{ $mk->kode_mk }}', '{{ $mk->sks_default }}')" 
                                @click="open = false"
                                class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-indigo-50 text-slate-900 border-b border-slate-50 last:border-0">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold block truncate">{{ $mk->kode_mk }} - {{ $mk->nama_mk }}</span>
                                    <span class="text-xs font-bold bg-slate-100 text-slate-500 px-2 rounded">{{ $mk->sks_default }} SKS</span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @elseif(!empty($searchMk) && empty($formMks))
                        <div x-show="open" class="absolute z-50 mt-1 w-full bg-white shadow-lg rounded-lg py-2 px-3 text-sm text-slate-500 italic border">
                            Tidak ditemukan.
                        </div>
                        @endif

                        @error('mata_kuliah_id') <span class="text-rose-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                        @if($selectedMkName) 
                            <div class="mt-1 text-xs text-emerald-600 font-bold flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Terpilih: {{ $selectedMkName }}
                            </div>
                        @endif
                    </div>

                    {{-- SEARCHABLE SELECT: DOSEN --}}
                    <div x-data="{ open: false }" class="relative">
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-2">Dosen Pengampu *</label>
                        
                        <!-- Input Display -->
                        <div class="relative">
                            <input type="text" 
                                wire:model.live="searchDosen"
                                @focus="open = true"
                                @click.away="open = false"
                                placeholder="{{ $selectedDosenName ?: '-- Cari Dosen --' }}"
                                class="block w-full rounded-lg border-slate-300 bg-white text-slate-900 focus:border-[#002855] focus:ring-[#002855] text-sm transition-all pr-8"
                            >
                            <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                <svg wire:loading.remove wire:target="searchDosen" class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                                <svg wire:loading wire:target="searchDosen" class="animate-spin w-4 h-4 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            </div>
                        </div>

                        <!-- Dropdown List -->
                        @if(!empty($searchDosen) && !empty($dosens))
                        <div x-show="open" class="absolute z-50 mt-1 w-full bg-white shadow-xl max-h-60 rounded-lg py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm">
                            @foreach($dosens as $dosen)
                            <div wire:click="pilihDosen('{{ $dosen->id }}', '{{ $dosen->nama_lengkap }}')" 
                                @click="open = false"
                                class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-indigo-50 text-slate-900 border-b border-slate-50 last:border-0">
                                <div class="flex flex-col">
                                    <span class="font-bold block truncate">{{ $dosen->nama_lengkap }}</span>
                                    <span class="text-xs text-slate-500">NIDN: {{ $dosen->nidn ?? '-' }}</span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @elseif(!empty($searchDosen) && empty($dosens))
                        <div x-show="open" class="absolute z-50 mt-1 w-full bg-white shadow-lg rounded-lg py-2 px-3 text-sm text-slate-500 italic border">
                            Tidak ditemukan.
                        </div>
                        @endif

                        @error('dosen_id') <span class="text-rose-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                        @if($selectedDosenName) 
                            <div class="mt-1 text-xs text-emerald-600 font-bold flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Terpilih: {{ $selectedDosenName }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Group 2: Logistik --}}
            <div class="space-y-4">
                <h4 class="text-xs font-bold text-[#002855] uppercase border-l-4 border-[#fcc000] pl-3 tracking-widest">2. Logistik & Waktu</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 bg-slate-50 p-6 rounded-xl border border-slate-100">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-2">Nama Kelas *</label>
                        <input type="text" wire:model="nama_kelas" class="block w-full rounded-lg border-slate-300 bg-white text-slate-900 focus:border-[#002855] focus:ring-[#002855] text-sm" placeholder="Contoh: A, B, Pagi">
                        @error('nama_kelas') <span class="text-rose-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-2">Ruangan *</label>
                        <input type="text" wire:model="ruang" class="block w-full rounded-lg border-slate-300 bg-white text-slate-900 focus:border-[#002855] focus:ring-[#002855] text-sm" placeholder="Contoh: R.101">
                        @error('ruang') <span class="text-rose-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text--[11px] font-bold text-slate-500 uppercase tracking-widest mb-2">Kuota (Mhs)</label>
                        <input type="number" wire:model="kuota_kelas" class="block w-full rounded-lg border-slate-300 bg-white text-slate-900 focus:border-[#002855] focus:ring-[#002855] text-sm">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-2">Hari *</label>
                        <select wire:model="hari" class="block w-full rounded-lg border-slate-300 bg-white text-slate-900 focus:border-[#002855] focus:ring-[#002855] text-sm">
                            <option value="" class="text-slate-500">-- Pilih Hari --</option>
                            @foreach($hariList as $h)
                            <option value="{{ $h }}" class="text-slate-900 bg-white">{{ $h }}</option>
                            @endforeach
                        </select>
                        @error('hari') <span class="text-rose-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-2">Jam Mulai</label>
                        <input type="time" wire:model="jam_mulai" class="block w-full rounded-lg border-slate-300 bg-white text-slate-900 focus:border-[#002855] focus:ring-[#002855] text-sm">
                        @error('jam_mulai') <span class="text-rose-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-2">Jam Selesai</label>
                        <input type="time" wire:model="jam_selesai" class="block w-full rounded-lg border-slate-300 bg-white text-slate-900 focus:border-[#002855] focus:ring-[#002855] text-sm">
                        @error('jam_selesai') <span class="text-rose-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- Group 3: Restriksi --}}
            <div class="bg-slate-50 rounded-xl p-6 border border-slate-200 flex flex-col md:flex-row gap-6 items-start">
                <div class="flex-shrink-0 pt-1">
                    <div class="p-2 bg-[#fcc000]/20 rounded-lg text-[#002855]">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m0 0v3m0-3h3m-3 0H9m12-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                </div>
                <div class="flex-1">
                    <h4 class="text-sm font-bold text-[#002855] uppercase tracking-tight mb-1">Restriksi Program (Segregasi)</h4>
                    <p class="text-xs text-slate-500 mb-3">Batasi akses kelas ini hanya untuk program tertentu (misal: hanya Ekstensi). Kosongkan jika terbuka umum.</p>
                    <select wire:model="id_program_kelas_allow" class="block w-full rounded-lg border-slate-300 bg-white text-slate-900 focus:border-[#002855] focus:ring-[#002855] text-sm">
                        <option value="" class="text-slate-500">-- Terbuka Untuk Semua (Umum) --</option>
                        @foreach($programKelasList as $pk)
                        <option value="{{ $pk->id }}" class="text-slate-900 bg-white">Khusus {{ $pk->nama_program }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="pt-6 border-t border-slate-100 flex justify-end gap-3">
                <button wire:click="batal" class="px-6 py-2.5 text-sm font-bold text-slate-500 hover:text-slate-700 hover:bg-slate-100 rounded-xl transition-all">Batalkan</button>
                
                {{-- TOMBOL SIMPAN DENGAN EFEK LOADING --}}
                <button wire:click="save" 
                    wire:loading.attr="disabled"
                    class="px-8 py-2.5 bg-[#002855] text-white rounded-xl text-sm font-bold shadow-lg shadow-indigo-200 hover:bg-[#001a38] hover:scale-105 transition-all flex items-center disabled:opacity-50 disabled:cursor-not-allowed">
                    
                    {{-- Spinner Icon --}}
                    <svg wire:loading wire:target="save" class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    
                    <span wire:loading.remove wire:target="save">Simpan Jadwal</span>
                    <span wire:loading wire:target="save">Menyimpan...</span>
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- Data Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        {{-- INFO CONTEXT TABLE --}}
        <div class="px-6 py-3 border-b border-slate-100 bg-slate-50/80 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Data Semester:</span>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold bg-[#002855]/10 text-[#002855]">
                    {{ $semesters->find($filterSemesterId)->nama_tahun }}
                </span>
            </div>
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Total {{ $jadwals->total() }} Kelas</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#002855] border-b border-[#001a38] text-white">
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest">Waktu</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest">Mata Kuliah / Kelas</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest">Dosen / Ruang</th>
                        <th class="px-6 py-4 text-center text-[10px] font-bold uppercase tracking-widest">Kuota</th>
                        <th class="px-6 py-4 text-center text-[10px] font-bold uppercase tracking-widest">Akses</th>
                        <th class="px-6 py-4 text-right text-[10px] font-bold uppercase tracking-widest">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($jadwals as $jadwal)
                    <tr class="hover:bg-slate-50/80 transition-colors group">
                        <td class="px-6 py-4 align-top">
                            <div class="flex flex-col">
                                <span class="font-black text-[#002855] text-sm">{{ $jadwal->hari }}</span>
                                <div class="flex items-center text-xs font-semibold text-slate-500 mt-1">
                                    <svg class="w-3 h-3 mr-1 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 align-top">
                            <div class="text-sm font-bold text-slate-800">{{ $jadwal->mataKuliah->nama_mk }}</div>
                            <div class="flex items-center gap-2 mt-1.5">
                                <span class="px-2 py-0.5 bg-[#002855]/10 text-[#002855] text-[10px] font-bold uppercase rounded border border-[#002855]/20">{{ $jadwal->mataKuliah->kode_mk }}</span>
                                <span class="text-xs text-slate-500 font-medium">Kls {{ $jadwal->nama_kelas }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 align-top">
                            <div class="text-sm font-medium text-slate-700">{{ $jadwal->dosen->nama_lengkap_gelar }}</div>
                            <div class="text-[10px] font-bold text-amber-600 uppercase mt-1 tracking-wider">Ruang {{ $jadwal->ruang }}</div>
                        </td>
                        <td class="px-6 py-4 align-top text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                {{ $jadwal->kuota_kelas }}
                            </span>
                        </td>
                        <td class="px-6 py-4 align-top text-center">
                            @if($jadwal->programKelasAllow)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide bg-[#fcc000]/20 text-[#002855] border border-[#fcc000]/30">
                                    {{ $jadwal->programKelasAllow->nama_program }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide bg-slate-100 text-slate-500 border border-slate-200">
                                    Umum / Semua
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 align-top text-right">
                            <div class="flex justify-end gap-2 opacity-60 group-hover:opacity-100 transition-opacity">
                                <button wire:click="edit('{{ $jadwal->id }}')" class="p-2 text-[#002855] hover:bg-[#002855]/10 rounded-lg transition-colors" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                </button>
                                <button wire:click="delete('{{ $jadwal->id }}')" wire:confirm="Hapus jadwal ini?" class="p-2 text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="bg-slate-50 p-4 rounded-full mb-3">
                                    <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z" /></svg>
                                </div>
                                <p class="text-slate-500 font-medium">Belum ada jadwal yang dipetakan.</p>
                                <p class="text-xs text-slate-400 mt-1">Silakan sesuaikan filter atau buat kelas baru.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
            {{ $jadwals->links() }}
        </div>
    </div>
</div>

{{-- Script untuk Auto-Scroll ke Notifikasi Error --}}
<script>
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('scroll-to-top', () => {
            const alertBox = document.getElementById('alert-box');
            if (alertBox) {
                alertBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
            } else {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        });
    });
</script>