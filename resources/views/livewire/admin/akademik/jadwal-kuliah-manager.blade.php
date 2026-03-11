<div class="space-y-6 animate-in fade-in duration-500">
    
    {{-- 1. Header Section --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-[#002855] rounded-2xl flex items-center justify-center text-[#fcc000] shadow-lg shadow-blue-900/20">
                <x-heroicon-o-calendar-days class="w-7 h-7" />
            </div>
            <div>
                <h1 class="text-2xl font-black text-[#002855] tracking-tight uppercase">Master Penjadwalan</h1>
                <p class="text-slate-500 text-sm font-medium">Pengaturan Team Teaching & Distribusi Ruangan Semester {{ $filterSemesterId }}</p>
            </div>
        </div>
        
        @if(!$showForm)
        <button wire:click="$set('showForm', true)" class="inline-flex items-center px-6 py-3 bg-[#fcc000] text-[#002855] rounded-xl font-black text-xs uppercase tracking-widest shadow-lg shadow-amber-500/20 hover:bg-[#fbbf24] hover:scale-105 transition-all group">
            <x-heroicon-s-plus class="w-5 h-5 mr-2 transition-transform group-hover:rotate-90" />
            Buat Jadwal Baru
        </button>
        @endif
    </div>

    {{-- 2. Alert Messages --}}
    @if (session()->has('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-100 text-emerald-800 rounded-2xl text-sm font-bold flex items-center shadow-sm animate-in slide-in-from-top-2">
            <x-heroicon-s-check-circle class="w-5 h-5 mr-3 text-emerald-500" />
            {{ session('success') }}
        </div>
    @endif

    {{-- 3. Filter Bar --}}
    @if(!$showForm)
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-white p-2 rounded-2xl border border-slate-200 shadow-sm">
        <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl border border-slate-100">
            <div class="w-8 h-8 rounded-lg bg-[#002855] text-[#fcc000] flex items-center justify-center font-black text-[10px]">SEM</div>
            <select wire:model.live="filterSemesterId" class="flex-1 bg-transparent border-none font-bold text-sm text-[#002855] focus:ring-0 cursor-pointer">
                @foreach($semesters as $s) <option value="{{ $s->id }}">{{ $s->nama_tahun }}</option> @endforeach
            </select>
        </div>
        <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl border border-slate-100">
            <div class="w-8 h-8 rounded-lg bg-[#fcc000] text-[#002855] flex items-center justify-center font-black text-[10px]">PRD</div>
            <select wire:model.live="filterProdiId" class="flex-1 bg-transparent border-none font-bold text-sm text-slate-700 focus:ring-0 cursor-pointer">
                @foreach($prodis as $p) <option value="{{ $p->id }}">{{ $p->nama_prodi }}</option> @endforeach
            </select>
        </div>
    </div>
    @endif

    {{-- 4. Unified Form Section --}}
    @if($showForm)
    <div class="bg-white rounded-3xl shadow-2xl border border-slate-200 overflow-hidden animate-in zoom-in-95 duration-300">
        <div class="grid grid-cols-1 lg:grid-cols-12">
            
            {{-- Left Side: Main Config --}}
            <div class="lg:col-span-8 p-8 md:p-10 space-y-10 border-r border-slate-100">
                <div class="flex items-center justify-between">
                    <h2 class="text-sm font-black text-[#002855] uppercase tracking-[0.2em] flex items-center gap-3">
                        <span class="w-7 h-7 rounded-lg bg-[#fcc000] text-[#002855] flex items-center justify-center text-xs shadow-sm">1</span>
                        Konfigurasi Akademik
                    </h2>
                    <button wire:click="resetForm" class="text-slate-400 hover:text-rose-500 transition-colors bg-slate-50 p-1.5 rounded-full">
                        <x-heroicon-s-x-mark class="w-5 h-5" />
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    {{-- Row 1: Kurikulum & MK --}}
                    <div class="space-y-6">
                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Sumber Kurikulum</label>
                            <select wire:model.live="kurikulum_id" class="block w-full rounded-xl border-slate-300 bg-slate-50 p-3.5 focus:border-[#002855] focus:ring-[#002855] text-sm font-bold @error('kurikulum_id') border-rose-300 bg-rose-50 @enderror">
                                <option value="">-- Pilih Kurikulum --</option>
                                @foreach($kurikulumOptions as $ko) <option value="{{ $ko->id }}">{{ $ko->nama_kurikulum }}</option> @endforeach
                            </select>
                            @error('kurikulum_id') <p class="text-[10px] font-bold text-rose-500 uppercase px-1">Kurikulum wajib dipilih</p> @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Mata Kuliah</label>
                            <div class="relative" x-data="{ open: false }">
                                <div class="relative">
                                    <input type="text" wire:model.live="searchMk" @focus="open = true" @click.away="open = false"
                                        placeholder="{{ $selectedMkName ?: 'Ketik Nama MK...' }}"
                                        class="block w-full rounded-xl border-slate-300 py-3.5 pl-10 pr-4 font-bold text-sm focus:ring-[#fcc000] @error('mata_kuliah_id') border-rose-300 bg-rose-50 @enderror {{ !$kurikulum_id ? 'bg-slate-100 italic cursor-not-allowed' : 'bg-white' }}" {{ !$kurikulum_id ? 'disabled' : '' }}>
                                    <x-heroicon-o-magnifying-glass class="w-4 h-4 absolute left-3.5 top-4 text-slate-400" />
                                </div>
                                @if(!empty($searchMk))
                                <div x-show="open" class="absolute z-50 w-full bg-white shadow-2xl rounded-2xl mt-2 border border-slate-100 overflow-hidden animate-in fade-in zoom-in-95">
                                    @foreach($formMks as $mk)
                                    <div wire:click="pilihMk('{{ $mk->id }}', '{{ $mk->nama_mk }}')" @click="open = false" class="px-5 py-3 hover:bg-amber-50 cursor-pointer border-b border-slate-50 last:border-0 transition-all">
                                        <p class="text-xs font-black text-[#002855] uppercase">{{ $mk->nama_mk }}</p>
                                        <p class="text-[9px] text-slate-400 font-bold mt-1 uppercase tracking-wider">{{ $mk->kode_mk }} &bull; {{ $mk->sks_default }} SKS</p>
                                    </div>
                                    @endforeach
                                </div>
                                @endif
                            </div>
                            @error('mata_kuliah_id') <p class="text-[10px] font-bold text-rose-500 uppercase px-1">Mata kuliah belum dipilih</p> @enderror
                        </div>
                    </div>

                    {{-- Row 2: Kelas & Kuota --}}
                    <div class="space-y-6">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Nama Kelas</label>
                                <input type="text" wire:model="nama_kelas" class="block w-full rounded-xl border-slate-300 bg-slate-50 p-3.5 text-center font-black text-[#002855] uppercase focus:ring-[#fcc000] @error('nama_kelas') border-rose-300 @enderror" placeholder="TI-3A">
                                @error('nama_kelas') <p class="text-[9px] font-bold text-rose-500 uppercase text-center">Wajib diisi</p> @enderror
                            </div>
                            <div class="space-y-2">
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Kuota</label>
                                <input type="number" wire:model="kuota_kelas" class="block w-full rounded-xl border-slate-300 bg-slate-50 p-3.5 text-center font-black text-slate-600 focus:ring-[#fcc000] @error('kuota_kelas') border-rose-300 @enderror">
                                @error('kuota_kelas') <p class="text-[9px] font-bold text-rose-500 uppercase text-center">Minimal 1</p> @enderror
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Ruangan Kuliah</label>
                            <div class="relative" x-data="{ open: false }">
                                <div class="relative">
                                    <input type="text" wire:model.live="searchRuang" @focus="open = true" @click.away="open = false"
                                        placeholder="{{ $selectedRuangName ?: 'Cari Kode atau Nama Ruang...' }}"
                                        class="block w-full rounded-xl border-slate-300 py-3.5 pl-10 pr-4 font-bold text-sm focus:ring-[#fcc000] bg-white @error('ruang_id') border-rose-300 @enderror">
                                    <x-heroicon-o-home-modern class="w-4 h-4 absolute left-3.5 top-4 text-slate-400" />
                                </div>
                                @if(!empty($searchRuang))
                                <div x-show="open" class="absolute z-50 w-full bg-white shadow-2xl rounded-2xl mt-2 border border-slate-100 max-h-60 overflow-y-auto animate-in fade-in zoom-in-95">
                                    @foreach($ruangOptions as $ro)
                                    <div wire:click="pilihRuang('{{ $ro->id }}', '[{{ $ro->kode_ruang }}] {{ $ro->nama_ruang }}')" @click="open = false" class="px-5 py-3 hover:bg-indigo-50 cursor-pointer border-b border-slate-50 last:border-0 transition-all">
                                        <p class="text-xs font-black text-[#002855] uppercase">[{{ $ro->kode_ruang }}] {{ $ro->nama_ruang }}</p>
                                        <p class="text-[9px] text-slate-400 font-bold mt-1 uppercase tracking-wider">Kapasitas: {{ $ro->kapasitas }} Mahasiswa</p>
                                    </div>
                                    @endforeach
                                </div>
                                @endif
                            </div>
                            @error('ruang_id') <p class="text-[10px] font-bold text-rose-500 uppercase px-1 mt-1">Ruangan wajib dipilih</p> @enderror
                            
                            @if($roomConflict)
                            <div class="p-3 bg-rose-50 border border-rose-100 rounded-xl mt-3 flex items-start gap-3 animate-pulse">
                                <x-heroicon-s-exclamation-triangle class="w-4 h-4 text-rose-500 mt-0.5" />
                                <div>
                                    <p class="text-[10px] font-black text-rose-700 uppercase tracking-widest">⚠ KONFLIK RUANGAN!</p>
                                    <p class="text-[10px] text-rose-500 font-bold">{{ $roomConflict['mk'] }} ({{ $roomConflict['kelas'] }}) jam {{ $roomConflict['waktu'] }}</p>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Team Teaching Section --}}
                <div class="pt-8 border-t border-slate-100 space-y-6">
                    <h2 class="text-sm font-black text-[#002855] uppercase tracking-[0.2em] flex items-center gap-3">
                        <span class="w-7 h-7 rounded-lg bg-[#fcc000] text-[#002855] flex items-center justify-center text-xs shadow-sm">2</span>
                        Team Teaching
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-start">
                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Cari & Tambah Dosen</label>
                            <div class="relative" x-data="{ open: false }">
                                <div class="relative">
                                    <input type="text" wire:model.live="searchDosen" @focus="open = true" @click.away="open = false" placeholder="Ketik Nama Dosen..." class="block w-full rounded-xl border-slate-300 bg-white py-3.5 pl-10 pr-4 text-sm font-bold focus:ring-[#fcc000]">
                                    <x-heroicon-o-user-plus class="w-4 h-4 absolute left-3.5 top-4 text-slate-400" />
                                </div>
                                @if(!empty($searchDosen))
                                <div x-show="open" class="absolute z-50 w-full bg-white shadow-2xl rounded-2xl mt-2 border border-slate-100 max-h-52 overflow-y-auto">
                                    @foreach($dosens as $d)
                                    <div wire:click="tambahDosen('{{ $d->id }}', '{{ $d->person->nama_lengkap }}')" @click="open = false" class="px-5 py-3 hover:bg-indigo-50 cursor-pointer border-b border-slate-50 transition-all flex justify-between items-center group">
                                        <div class="text-xs font-black text-[#002855] uppercase group-hover:text-indigo-600">{{ $d->person->nama_lengkap }}</div>
                                        <x-heroicon-s-plus-circle class="w-5 h-5 text-slate-300 group-hover:text-indigo-500" />
                                    </div>
                                    @endforeach
                                </div>
                                @endif
                            </div>
                        </div>

                        <div class="space-y-3">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Dosen Terpilih</label>
                            <div class="space-y-2">
                                @forelse($selectedDosenList as $sd)
                                <div class="flex items-center justify-between p-3.5 bg-slate-50 rounded-xl border border-slate-100 group hover:border-indigo-200 transition-all">
                                    <div class="flex items-center gap-3">
                                        <input type="radio" wire:model="koordinator_id" value="{{ $sd['id'] }}" class="w-4 h-4 text-[#002855] border-slate-300 focus:ring-[#fcc000]">
                                        <span class="text-xs font-black text-[#002855] uppercase {{ $koordinator_id == $sd['id'] ? 'text-indigo-600' : '' }}">{{ $sd['nama'] }}</span>
                                        @if($koordinator_id == $sd['id']) 
                                            <span class="text-[7px] bg-[#fcc000] text-[#002855] px-1.5 py-0.5 rounded font-black tracking-widest uppercase shadow-sm">Koor</span> 
                                        @endif
                                    </div>
                                    <button wire:click="hapusDosen('{{ $sd['id'] }}')" class="text-slate-300 hover:text-rose-500 transition-colors">
                                        <x-heroicon-s-trash class="w-4 h-4" />
                                    </button>
                                </div>
                                @empty
                                <div class="text-center py-8 border-2 border-dashed border-slate-100 rounded-2xl text-slate-400 text-[10px] font-black uppercase tracking-widest">Dosen Belum Ditentukan</div>
                                @endforelse
                                @if(empty($dosen_ids)) <p class="text-[9px] font-bold text-rose-500 uppercase text-center mt-2">Minimal 1 dosen pengampu wajib ditambahkan</p> @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Side: Schedule & Status --}}
            <div class="lg:col-span-4 bg-slate-50/70 p-8 md:p-10 space-y-10">
                <h2 class="text-sm font-black text-[#002855] uppercase tracking-[0.2em] flex items-center gap-3">
                    <span class="w-7 h-7 rounded-lg bg-[#fcc000] text-[#002855] flex items-center justify-center text-xs shadow-sm">3</span>
                    Waktu Perkuliahan
                </h2>

                <div class="space-y-8">
                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 block">Pilih Hari</label>
                        <div class="grid grid-cols-3 gap-2">
                            @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $h)
                            <button wire:click="$set('hari', '{{ $h }}')" class="py-3 rounded-xl text-[10px] font-black uppercase transition-all shadow-sm @error('hari') border-rose-300 @enderror {{ $hari == $h ? 'bg-[#002855] text-white' : 'bg-white text-slate-400 border border-slate-200 hover:bg-slate-50' }}">
                                {{ $h }}
                            </button>
                            @endforeach
                        </div>
                        @error('hari') <p class="text-[9px] font-bold text-rose-500 uppercase text-center">Hari wajib dipilih</p> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-3 text-center">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block">Jam Mulai</label>
                            <input type="text" wire:model.live="jam_mulai" placeholder="00:00" class="block w-full rounded-xl border-slate-300 bg-white py-4 text-center font-black text-2xl text-[#002855] shadow-sm focus:ring-[#fcc000] @error('jam_mulai') border-rose-300 bg-rose-50 @enderror">
                            @error('jam_mulai') <p class="text-[9px] font-bold text-rose-500 uppercase text-center">Format Salah</p> @enderror
                        </div>
                        <div class="space-y-3 text-center">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block">Jam Selesai</label>
                            <input type="text" wire:model.live="jam_selesai" placeholder="00:00" class="block w-full rounded-xl border-slate-300 bg-white py-4 text-center font-black text-2xl text-[#002855] shadow-sm focus:ring-[#fcc000] @error('jam_selesai') border-rose-300 bg-rose-50 @enderror">
                            @error('jam_selesai') <p class="text-[9px] font-bold text-rose-500 uppercase text-center">Harus > Mulai</p> @enderror
                        </div>
                    </div>

                    @if(!empty($lecturerConflict))
                    <div class="p-5 bg-rose-50 border border-rose-100 rounded-2xl space-y-4 shadow-sm animate-in fade-in">
                        <h5 class="text-[9px] font-black text-rose-700 uppercase tracking-[0.2em] flex items-center gap-2">
                            <x-heroicon-s-no-symbol class="w-4 h-4" />
                            BENTROK JADWAL DOSEN
                        </h5>
                        @foreach($lecturerConflict as $lc)
                        <div class="bg-white p-3 rounded-xl border border-rose-100 shadow-sm">
                            <p class="text-[10px] font-black text-slate-700 uppercase">{{ $lc['nama'] }}</p>
                            <p class="text-[10px] text-rose-500 font-bold mt-1 uppercase tracking-tight">{{ $lc['mk'] }} &bull; {{ $lc['waktu'] }}</p>
                        </div>
                        @endforeach
                    </div>
                    @endif

                    <div class="pt-6">
                        <button wire:click="save" class="w-full py-4 rounded-2xl bg-[#002855] text-white font-black text-xs tracking-[0.3em] uppercase shadow-2xl shadow-blue-900/30 hover:scale-[1.02] active:scale-95 transition-all duration-300 disabled:bg-slate-200 disabled:shadow-none" {{ $formStatus == 'red' ? 'disabled' : '' }}>
                            <span wire:loading.remove wire:target="save">Publish Jadwal</span>
                            <span wire:loading wire:target="save" class="flex items-center justify-center">
                                <svg class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            </span>
                        </button>
                        <p class="text-center text-[9px] font-black text-slate-400 mt-6 uppercase tracking-widest leading-loose">Data diverifikasi secara real-time untuk mencegah jadwal ganda</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- 5. Jadwal Table Section --}}
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden relative">
        <div wire:loading.flex wire:target="filterSemesterId, filterProdiId, gotoPage, nextPage, previousPage" class="absolute inset-0 z-10 bg-white/60 backdrop-blur-[1px] items-center justify-center">
            <div class="p-4 bg-white rounded-2xl shadow-xl border border-slate-100 flex flex-col items-center">
                <svg class="w-8 h-8 text-[#002855] animate-spin mb-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Sinkronisasi...</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#002855] text-white">
                        <th class="px-8 py-5 text-[10px] font-bold uppercase tracking-widest">Waktu & Ruang</th>
                        <th class="px-8 py-5 text-[10px] font-bold uppercase tracking-widest">Mata Kuliah & Kelas</th>
                        <th class="px-8 py-5 text-[10px] font-bold uppercase tracking-widest">Dosen Pengampu</th>
                        <th class="px-8 py-5 text-[10px] font-bold uppercase tracking-widest text-right">Opsi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($jadwals as $j)
                    <tr class="hover:bg-slate-50/80 transition-all group">
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-indigo-50 text-[#002855] rounded-2xl flex flex-col items-center justify-center border border-indigo-100 group-hover:scale-110 transition-transform shadow-sm">
                                    <span class="text-[8px] font-black uppercase leading-none">{{ substr($j->hari, 0, 3) }}</span>
                                    <span class="text-xs font-black mt-1">{{ substr($j->jam_mulai, 0, 5) }}</span>
                                </div>
                                <div>
                                    <p class="text-sm font-black text-[#002855] uppercase tracking-tight">Ruang {{ $j->ruang->kode_ruang ?? 'TBA' }}</p>
                                    <p class="text-[10px] font-bold text-slate-400 mt-1 uppercase tracking-widest">{{ $j->ruang->nama_ruang ?? '-' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <h4 class="text-sm font-black text-slate-800 uppercase tracking-tight">{{ $j->mataKuliah->nama_mk }}</h4>
                            <div class="flex items-center gap-3 mt-3">
                                <span class="px-2.5 py-0.5 bg-[#fcc000] text-[#002855] text-[9px] font-black rounded-lg shadow-sm tracking-widest uppercase">{{ $j->nama_kelas }}</span>
                                <span class="px-2.5 py-0.5 bg-slate-100 text-slate-500 text-[9px] font-bold rounded-lg border border-slate-200 uppercase">{{ $j->mataKuliah->sks_default }} SKS</span>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <div class="flex flex-col gap-2">
                                @foreach($j->dosens as $d)
                                <div class="flex items-center gap-2">
                                    <div class="w-1.5 h-1.5 rounded-full {{ $d->pivot->is_koordinator ? 'bg-amber-400' : 'bg-slate-300' }}"></div>
                                    <span class="text-[11px] font-bold text-slate-600 uppercase group-hover:text-[#002855] transition-colors">{{ $d->person->nama_lengkap }}</span>
                                    @if($d->pivot->is_koordinator) 
                                        <span class="text-[7px] font-black text-amber-600 bg-amber-50 border border-amber-200 px-1 rounded uppercase tracking-tighter shadow-xs">Koor</span> 
                                    @endif
                                </div>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-8 py-6 text-right">
                            <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-all transform translate-x-2 group-hover:translate-x-0">
                                <button wire:click="edit('{{ $j->id }}')" class="p-2.5 text-[#002855] bg-indigo-50 hover:bg-[#002855] hover:text-white rounded-xl transition-all shadow-sm">
                                    <x-heroicon-s-pencil class="w-4 h-4" />
                                </button>
                                <button class="p-2.5 text-rose-600 bg-rose-50 hover:bg-rose-600 hover:text-white rounded-xl transition-all shadow-sm">
                                    <x-heroicon-s-trash class="w-4 h-4" />
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center justify-center opacity-30 grayscale">
                                <x-heroicon-o-archive-box-x-mark class="w-16 h-16 text-slate-400 mb-4" />
                                <p class="text-sm font-black uppercase tracking-widest text-slate-500">Belum Ada Jadwal Terbit</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($jadwals->hasPages())
        <div class="px-8 py-6 border-t border-slate-100 bg-slate-50/50">
            {{ $jadwals->links() }}
        </div>
        @endif
    </div>

    {{-- System Footer --}}
    <div class="flex items-center justify-center gap-3 opacity-20 grayscale select-none pointer-events-none py-6">
        <div class="h-px bg-slate-300 w-12"></div>
        <p class="text-[9px] font-black uppercase tracking-[0.4em] text-[#002855]">Scheduling Intelligence System &bull; UNMARIS</p>
        <div class="h-px bg-slate-300 w-12"></div>
    </div>
</div>