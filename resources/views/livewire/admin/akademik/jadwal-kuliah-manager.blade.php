<div class="space-y-8">
    
    {{-- 1. HEADER & TOOLBAR --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <h1 class="text-2xl font-black text-[#002855] tracking-tight">Jadwal Perkuliahan</h1>
            <p class="text-slate-500 text-sm mt-1">Penyusunan jadwal kelas, validasi bentrok ruang & dosen (Real-time).</p>
        </div>
        
        @if(!$showForm)
        <button wire:click="$set('showForm', true)" class="group inline-flex items-center px-6 py-3 bg-[#fcc000] text-[#002855] rounded-2xl font-black text-sm shadow-lg shadow-orange-500/20 hover:bg-[#fbbf24] hover:scale-105 transition-all">
            <svg class="w-5 h-5 mr-2 transition-transform group-hover:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" /></svg>
            Buka Kelas Baru
        </button>
        @endif
    </div>

    {{-- 2. NOTIFIKASI --}}
    @if (session()->has('success'))
    <div class="p-4 bg-emerald-50 border border-emerald-100 text-emerald-800 rounded-2xl text-sm font-bold flex items-center shadow-sm animate-in fade-in slide-in-from-top-2">
        <svg class="w-5 h-5 mr-3 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
        {{ session('success') }}
    </div>
    @endif

    {{-- 3. FILTER CONTEXT (TA & PRODI) --}}
    @if(!$showForm)
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-sm flex items-center gap-4 relative overflow-hidden group hover:border-[#002855]/30 transition-colors">
            <div class="absolute right-0 top-0 h-full w-1 bg-[#002855]"></div>
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-[#002855] flex items-center justify-center font-black text-xs shadow-inner">
                TA
            </div>
            <div class="flex-1 min-w-0">
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Tahun Akademik</label>
                <div class="relative">
                    <select wire:model.live="filterSemesterId" class="w-full bg-transparent border-none p-0 text-sm font-bold text-[#002855] focus:ring-0 cursor-pointer truncate pr-4">
                        @foreach($semesters as $s) <option value="{{ $s->id }}">{{ $s->nama_tahun }} {{ $s->is_active ? '(Aktif)' : '' }}</option> @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-sm flex items-center gap-4 relative overflow-hidden group hover:border-[#fcc000] transition-colors">
            <div class="absolute right-0 top-0 h-full w-1 bg-[#fcc000]"></div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center font-black text-xs shadow-inner">
                PS
            </div>
            <div class="flex-1 min-w-0">
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Program Studi</label>
                <div class="relative">
                    <select wire:model.live="filterProdiId" class="w-full bg-transparent border-none p-0 text-sm font-bold text-slate-800 focus:ring-0 cursor-pointer truncate pr-4">
                        @foreach($prodis as $p) <option value="{{ $p->id }}">{{ $p->nama_prodi }}</option> @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- 4. FORM PENYUSUNAN JADWAL --}}
    @if($showForm)
    <div class="bg-white rounded-[2.5rem] shadow-2xl border border-slate-200 overflow-hidden animate-in slide-in-from-bottom-8 duration-500">
        
        {{-- Header Form --}}
        <div class="px-8 py-6 border-b border-slate-100 bg-slate-50/80 backdrop-blur-sm flex justify-between items-center sticky top-0 z-30">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-full bg-[#002855] flex items-center justify-center text-white shadow-lg shadow-indigo-900/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                </div>
                <div>
                    <h3 class="text-sm font-black text-[#002855] uppercase tracking-wider">Form Jadwal Kuliah</h3>
                    <div class="flex items-center gap-2 mt-0.5">
                        <div class="w-2 h-2 rounded-full {{ $formStatus == 'green' ? 'bg-emerald-500 animate-pulse' : ($formStatus == 'red' ? 'bg-rose-500 animate-pulse' : 'bg-slate-300') }}"></div>
                        <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wide">Status: {{ $formStatus == 'green' ? 'Aman' : ($formStatus == 'red' ? 'Konflik Terdeteksi' : 'Draft') }}</p>
                    </div>
                </div>
            </div>
            <button wire:click="resetForm" class="p-2 rounded-full text-slate-400 hover:bg-slate-100 hover:text-rose-500 transition-all">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="p-8 lg:p-12 space-y-12">
            
            {{-- STEP 1: KURIKULUM --}}
            <div class="relative pl-8 md:pl-12 border-l-2 border-slate-100">
                <div class="absolute -left-[9px] top-0 w-4 h-4 rounded-full bg-[#002855] border-2 border-white ring-2 ring-indigo-50"></div>
                
                <h4 class="text-sm font-black text-[#002855] uppercase tracking-widest mb-6">1. Otorisasi Mata Kuliah</h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    {{-- Kurikulum --}}
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-2">Kurikulum Sumber *</label>
                        <select wire:model.live="kurikulum_id" class="w-full rounded-xl border-slate-200 bg-slate-50 p-3 text-sm font-bold text-slate-700 focus:border-[#002855] focus:ring-[#002855] transition-all cursor-pointer">
                            <option value="">-- Pilih Kurikulum --</option>
                            @foreach($kurikulumOptions as $ko) <option value="{{ $ko->id }}">{{ $ko->nama_kurikulum }}</option> @endforeach
                        </select>
                        @error('kurikulum_id') <span class="text-rose-500 text-[10px] font-bold mt-1 block">Wajib dipilih</span> @enderror
                    </div>

                    {{-- Searchable Mata Kuliah --}}
                    <div class="relative" x-data="{ open: false }">
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-2">Mata Kuliah *</label>
                        <div class="relative">
                            <input type="text" wire:model.live="searchMk" 
                                @focus="open = true" @click.away="open = false"
                                placeholder="{{ $selectedMkName ?: 'Ketik Nama / Kode MK...' }}"
                                class="w-full rounded-xl border-slate-200 p-3 pl-10 text-sm font-bold focus:border-[#002855] focus:ring-[#002855] shadow-sm placeholder:font-normal placeholder:text-slate-400 {{ !$kurikulum_id ? 'bg-slate-100 cursor-not-allowed' : 'bg-white' }}"
                                {{ !$kurikulum_id ? 'disabled' : '' }}>
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                        </div>

                        {{-- Dropdown MK --}}
                        @if(!empty($searchMk) && $kurikulum_id)
                        <div x-show="open" class="absolute z-50 w-full bg-white shadow-2xl rounded-xl mt-2 border border-slate-100 overflow-hidden max-h-60 overflow-y-auto">
                            @foreach($formMks as $mk)
                            <div wire:click="pilihMk('{{ $mk->id }}', '{{ $mk->nama_mk }}')" @click="open = false" 
                                class="px-4 py-3 hover:bg-indigo-50 cursor-pointer border-b border-slate-50 last:border-0 group transition-colors">
                                <p class="text-xs font-black text-[#002855] uppercase group-hover:text-indigo-600">{{ $mk->nama_mk }}</p>
                                <p class="text-[9px] text-slate-500 font-mono mt-0.5">{{ $mk->kode_mk }} &bull; Sem {{ $mk->semester_paket }} &bull; {{ $mk->sks_default }} SKS</p>
                            </div>
                            @endforeach
                        </div>
                        @endif
                        @error('mata_kuliah_id') <span class="text-rose-500 text-[10px] font-bold mt-1 block">Mata kuliah wajib dipilih</span> @enderror
                    </div>
                </div>
            </div>

            {{-- STEP 2: WAKTU & LOKASI --}}
            <div class="relative pl-8 md:pl-12 border-l-2 border-slate-100">
                <div class="absolute -left-[9px] top-0 w-4 h-4 rounded-full bg-[#fcc000] border-2 border-white ring-2 ring-amber-50"></div>
                
                <h4 class="text-sm font-black text-[#002855] uppercase tracking-widest mb-6">2. Waktu & Lokasi</h4>
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    {{-- Hari --}}
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-2">Hari Perkuliahan *</label>
                        <select wire:model.live="hari" class="w-full rounded-xl border-slate-200 bg-white p-3 text-sm font-bold text-slate-700 focus:border-[#002855] focus:ring-[#002855] cursor-pointer">
                            <option value="">-- Pilih --</option>
                            @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $h) <option value="{{ $h }}">{{ $h }}</option> @endforeach
                        </select>
                    </div>

                    {{-- Jam --}}
                    <div class="lg:col-span-2">
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-2">Durasi Waktu (WITA) *</label>
                        <div class="flex items-center gap-4">
                            <div class="relative flex-1">
                                <input type="text" wire:model.live="jam_mulai" placeholder="08:00" maxlength="5"
                                    class="w-full text-center text-lg font-black text-[#002855] rounded-xl border-slate-200 p-3 focus:border-[#002855] focus:ring-[#002855] @error('jam_mulai') border-rose-500 @enderror">
                                <span class="absolute inset-y-0 right-3 flex items-center text-xs font-bold text-slate-300">WITA</span>
                            </div>
                            <span class="text-slate-300 font-bold">-</span>
                            <div class="relative flex-1">
                                <input type="text" wire:model.live="jam_selesai" placeholder="10:30" maxlength="5"
                                    class="w-full text-center text-lg font-black text-[#002855] rounded-xl border-slate-200 p-3 focus:border-[#002855] focus:ring-[#002855] @error('jam_selesai') border-rose-500 @enderror">
                                <span class="absolute inset-y-0 right-3 flex items-center text-xs font-bold text-slate-300">WITA</span>
                            </div>
                        </div>
                        @if($timeFormatError) <span class="text-rose-500 text-[10px] font-bold mt-1 block">{{ $timeFormatError }}</span> @endif
                    </div>

                    {{-- Ruang & Kelas --}}
                    <div class="lg:col-span-3 grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="md:col-span-1">
                            <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-2">Nama Ruang *</label>
                            <input type="text" wire:model.live="ruang" placeholder="Ex: B-201" class="w-full rounded-xl border-slate-200 p-3 font-black text-[#002855] uppercase focus:border-[#002855] focus:ring-[#002855] @error('ruang') border-rose-500 @enderror">
                            
                            {{-- ALERT BENTROK RUANG --}}
                            @if($roomConflict)
                            <div class="mt-3 p-3 bg-rose-50 border border-rose-100 rounded-lg animate-in slide-in-from-top-1">
                                <p class="text-[10px] font-black text-rose-600 uppercase">⚠ Bentrok Ruangan!</p>
                                <p class="text-[10px] text-rose-500 leading-tight mt-1">
                                    {{ $roomConflict['mk'] }} ({{ $roomConflict['kelas'] }})<br>
                                    {{ $roomConflict['waktu'] }}
                                </p>
                            </div>
                            @endif
                        </div>
                        <div class="md:col-span-1">
                            <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-2">Nama Kelas *</label>
                            <input type="text" wire:model="nama_kelas" placeholder="Ex: TI-2A" class="w-full rounded-xl border-slate-200 p-3 font-black text-[#002855] uppercase text-center focus:border-[#002855] focus:ring-[#002855]">
                        </div>
                        <div class="md:col-span-1">
                            <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-2">Kuota Kursi *</label>
                            <input type="number" wire:model="kuota_kelas" class="w-full rounded-xl border-slate-200 p-3 font-bold text-slate-700 text-center focus:border-[#002855] focus:ring-[#002855]">
                        </div>
                    </div>
                </div>
            </div>

            {{-- STEP 3: DOSEN PENGAMPU --}}
            <div class="relative pl-8 md:pl-12 border-l-2 border-transparent">
                <div class="absolute -left-[9px] top-0 w-4 h-4 rounded-full bg-slate-300 border-2 border-white ring-2 ring-slate-100"></div>
                
                <h4 class="text-sm font-black text-[#002855] uppercase tracking-widest mb-6">3. Dosen Pengampu</h4>

                <div class="relative" x-data="{ open: false }">
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-2">Cari Dosen *</label>
                    <div class="relative">
                        <input type="text" wire:model.live="searchDosen" @focus="open = true" @click.away="open = false"
                            placeholder="{{ $selectedDosenName ?: 'Ketik Nama Dosen...' }}"
                            class="w-full rounded-xl border-slate-200 p-3 pl-10 text-sm font-bold focus:border-[#002855] focus:ring-[#002855] shadow-sm placeholder:font-normal placeholder:text-slate-400 @error('dosen_id') border-rose-500 @enderror">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                    </div>

                    {{-- Dropdown Dosen --}}
                    @if(!empty($searchDosen))
                    <div x-show="open" class="absolute z-50 w-full bg-white shadow-2xl rounded-xl mt-2 border border-slate-100 overflow-hidden max-h-60 overflow-y-auto">
                        @foreach($dosens as $d)
                        <div wire:click="pilihDosen('{{ $d->id }}', '{{ $d->person->nama_lengkap }}')" @click="open = false" class="px-4 py-3 hover:bg-amber-50 cursor-pointer border-b border-slate-50 last:border-0 transition-colors group">
                            <p class="text-xs font-black text-[#002855] uppercase group-hover:text-amber-700">{{ $d->person->nama_lengkap }}</p>
                            <p class="text-[9px] text-slate-400 font-mono mt-0.5">NIDN: {{ $d->nidn }}</p>
                        </div>
                        @endforeach
                    </div>
                    @endif

                    {{-- ALERT BENTROK DOSEN --}}
                    @if($lecturerConflict)
                    <div class="mt-3 p-4 bg-rose-50 rounded-xl border border-rose-100 animate-in slide-in-from-top-2 flex items-start gap-3">
                        <div class="p-1 bg-rose-100 rounded text-rose-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg></div>
                        <div>
                            <p class="text-[10px] font-black text-rose-700 uppercase tracking-wide">Dosen Sedang Mengajar!</p>
                            <p class="text-xs text-rose-600 mt-1 font-medium">
                                Jadwal Lain: {{ $lecturerConflict['mk'] }} <br>
                                Ruang {{ $lecturerConflict['ruang'] }} &bull; {{ $lecturerConflict['waktu'] }}
                            </p>
                        </div>
                    </div>
                    @endif
                    @error('dosen_id') <span class="text-rose-500 text-[10px] font-bold mt-1 block">Wajib memilih dosen.</span> @enderror
                </div>
            </div>

            {{-- FOOTER ACTION --}}
            <div class="pt-8 border-t border-slate-100 flex flex-col md:flex-row items-center justify-between gap-6 sticky bottom-0 bg-white py-4 z-20">
                <div class="flex items-center gap-2 text-slate-400">
                    <div class="w-2.5 h-2.5 rounded-full {{ $formStatus == 'red' ? 'bg-rose-500 animate-pulse' : ($formStatus == 'green' ? 'bg-emerald-500' : 'bg-slate-300') }}"></div>
                    <span class="text-[10px] font-bold uppercase tracking-widest">
                        Status: {{ $formStatus == 'green' ? 'Siap Terbit' : ($formStatus == 'red' ? 'Perbaiki Konflik' : 'Drafting') }}
                    </span>
                </div>
                
                <div class="flex gap-3 w-full md:w-auto">
                    <button wire:click="resetForm" class="flex-1 md:flex-none px-6 py-3 rounded-xl border border-slate-200 text-slate-500 text-xs font-bold uppercase tracking-widest hover:bg-slate-50 transition-colors">
                        Batal
                    </button>
                    <button wire:click="save" 
                        {{ $formStatus == 'red' ? 'disabled' : '' }}
                        class="flex-1 md:flex-none px-8 py-3 rounded-xl {{ $formStatus == 'red' ? 'bg-slate-100 text-slate-300 cursor-not-allowed' : 'bg-[#002855] text-white shadow-lg hover:bg-[#001a38] hover:scale-105' }} text-xs font-black uppercase tracking-[0.2em] transition-all flex items-center justify-center gap-2">
                        <span wire:loading.remove>Simpan Jadwal</span>
                        <span wire:loading class="flex items-center gap-2">
                            <svg class="w-4 h-4 animate-spin" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            Menyimpan...
                        </span>
                    </button>
                </div>
            </div>

        </div>
    </div>
    @endif

    {{-- LIST TABLE (VIEW ONLY) --}}
    <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden animate-in fade-in">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50/80">
                    <tr>
                        <th class="px-8 py-5 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest w-1/4">Waktu & Lokasi</th>
                        <th class="px-8 py-5 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest w-1/4">Mata Kuliah</th>
                        <th class="px-8 py-5 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest w-1/4">Pengampu</th>
                        <th class="px-8 py-5 text-right text-[10px] font-black text-slate-400 uppercase tracking-widest w-1/4">Opsi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($jadwals as $j)
                    <tr class="hover:bg-indigo-50/20 transition-colors group">
                        <td class="px-8 py-6 align-top">
                            <div class="text-sm font-black text-[#002855] uppercase tracking-tighter">{{ $j->hari }}</div>
                            <div class="text-xs font-bold text-slate-500 mt-1">{{ substr($j->jam_mulai,0,5) }} - {{ substr($j->jam_selesai,0,5) }} WITA</div>
                            <div class="mt-2 inline-flex px-2 py-0.5 rounded bg-white border border-slate-200 text-slate-600 text-[9px] font-bold uppercase tracking-widest">R. {{ $j->ruang }}</div>
                        </td>
                        <td class="px-8 py-6 align-top">
                            <div class="text-sm font-bold text-slate-800 leading-tight uppercase">{{ $j->mataKuliah->nama_mk }}</div>
                            <div class="flex items-center gap-2 mt-2">
                                <span class="text-[9px] font-bold text-slate-400 border border-slate-200 px-1.5 py-0.5 rounded">{{ $j->nama_kelas }}</span>
                                <span class="text-[9px] font-black bg-[#fcc000] text-[#002855] px-2 py-0.5 rounded">{{ $j->mataKuliah->sks_default }} SKS</span>
                            </div>
                        </td>
                        <td class="px-8 py-6 align-top">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center font-bold text-[#002855] text-xs shrink-0">
                                    {{ substr($j->dosen->person->nama_lengkap, 0, 1) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="text-xs font-bold text-slate-700 uppercase">{{ $j->dosen->person->nama_lengkap }}</p>
                                    <p class="text-[9px] font-mono text-slate-400 mt-0.5">NIDN: {{ $j->dosen->nidn }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-6 align-top text-right">
                            <div class="flex justify-end gap-2 opacity-60 group-hover:opacity-100 transition-opacity">
                                <button wire:click="edit('{{ $j->id }}')" class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors border border-transparent hover:border-indigo-100">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                </button>
                                <button wire:click="delete('{{ $j->id }}')" wire:confirm="Hapus jadwal ini?" class="p-2 text-rose-500 hover:bg-rose-50 rounded-lg transition-colors border border-transparent hover:border-rose-100">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-8 bg-slate-50 border-t border-slate-100">
            {{ $jadwals->links() }}
        </div>
    </div>
</div>