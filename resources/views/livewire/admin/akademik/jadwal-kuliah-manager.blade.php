<div class="space-y-6 max-w-[1600px] mx-auto p-4 md:p-8">
    {{-- Header --}}
    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6">
        <div class="space-y-1">
            <h1 class="text-3xl font-black text-[#002855] tracking-tight flex items-center gap-3">
                <div class="w-12 h-12 bg-[#002855] rounded-2xl flex items-center justify-center text-white shadow-xl shadow-blue-900/20">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                </div>
                Master Penjadwalan
            </h1>
            <p class="text-slate-400 font-medium">Pengaturan Team Teaching & Distribusi Ruangan Semester {{ $filterSemesterId }}</p>
        </div>
        
        @if(!$showForm)
        <button wire:click="$set('showForm', true)" class="group flex items-center px-8 py-4 bg-[#fcc000] text-[#002855] rounded-2xl font-black text-sm shadow-xl shadow-amber-500/30 hover:bg-[#ffca28] hover:-translate-y-1 transition-all duration-300">
            <svg class="w-5 h-5 mr-3 transition-transform group-hover:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" /></svg>
            BUAT JADWAL BARU
        </button>
        @endif
    </div>

    {{-- Filter Bar --}}
    @if(!$showForm)
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-white/50 backdrop-blur-md p-2 rounded-[2rem] border border-white shadow-sm">
        <div class="flex items-center gap-4 p-4 bg-white rounded-3xl shadow-sm">
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-xs uppercase tracking-tighter">SEM</div>
            <select wire:model.live="filterSemesterId" class="flex-1 bg-transparent border-none font-bold text-[#002855] focus:ring-0">
                @foreach($semesters as $s) <option value="{{ $s->id }}">{{ $s->nama_tahun }}</option> @endforeach
            </select>
        </div>
        <div class="flex items-center gap-4 p-4 bg-white rounded-3xl shadow-sm">
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-xs uppercase tracking-tighter">PRD</div>
            <select wire:model.live="filterProdiId" class="flex-1 bg-transparent border-none font-bold text-slate-700 focus:ring-0">
                @foreach($prodis as $p) <option value="{{ $p->id }}">{{ $p->nama_prodi }}</option> @endforeach
            </select>
        </div>
    </div>
    @endif

    @if($showForm)
    <div class="bg-white rounded-[3rem] shadow-2xl border border-slate-100 overflow-hidden animate-in slide-in-from-bottom-10 duration-500">
        <div class="grid grid-cols-1 lg:grid-cols-12">
            {{-- Left Side: Configuration --}}
            <div class="lg:col-span-8 p-8 md:p-12 space-y-10 border-r border-slate-50">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-black text-[#002855] flex items-center gap-3">
                        <span class="w-8 h-8 rounded-full bg-[#fcc000] text-[#002855] flex items-center justify-center text-sm">1</span>
                        Konfigurasi Kelas
                    </h2>
                    <button wire:click="resetForm" class="text-slate-400 hover:text-rose-500 transition-colors"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-4">
                        <label class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Sumber Kurikulum</label>
                        <select wire:model.live="kurikulum_id" class="w-full rounded-2xl border-slate-200 bg-slate-50 py-4 px-5 font-bold text-[#002855] focus:ring-[#fcc000]">
                            <option value="">-- Pilih Kurikulum --</option>
                            @foreach($kurikulumOptions as $ko) <option value="{{ $ko->id }}">{{ $ko->nama_kurikulum }}</option> @endforeach
                        </select>
                        
                        <label class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1 block mt-6">Cari Mata Kuliah</label>
                        <div class="relative" x-data="{ open: false }">
                            <input type="text" wire:model.live="searchMk" @focus="open = true" @click.away="open = false"
                                placeholder="{{ $selectedMkName ?: 'Ketik Nama MK...' }}"
                                class="w-full rounded-2xl border-slate-200 py-4 px-5 font-bold focus:ring-[#fcc000] {{ !$kurikulum_id ? 'bg-slate-100' : 'bg-white' }}" {{ !$kurikulum_id ? 'disabled' : '' }}>
                            @if(!empty($searchMk))
                            <div x-show="open" class="absolute z-50 w-full bg-white shadow-2xl rounded-2xl mt-2 border border-slate-100 overflow-hidden animate-in fade-in zoom-in-95">
                                @foreach($formMks as $mk)
                                <div wire:click="pilihMk('{{ $mk->id }}', '{{ $mk->nama_mk }}')" @click="open = false" class="px-5 py-4 hover:bg-amber-50 cursor-pointer border-b border-slate-50 last:border-0 transition-all">
                                    <p class="text-xs font-black text-[#002855] uppercase">{{ $mk->nama_mk }}</p>
                                    <p class="text-[9px] text-slate-400 font-bold mt-1 uppercase tracking-wider">{{ $mk->kode_mk }} &bull; {{ $mk->sks_default }} SKS</p>
                                </div>
                                @endforeach
                            </div>
                            @endif
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Nama Kelas</label>
                                <input type="text" wire:model="nama_kelas" class="w-full rounded-2xl border-slate-200 py-4 text-center font-black text-[#002855] uppercase" placeholder="TI-3A">
                            </div>
                            <div class="space-y-2">
                                <label class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Kuota</label>
                                <input type="number" wire:model="kuota_kelas" class="w-full rounded-2xl border-slate-200 py-4 text-center font-black text-slate-600">
                            </div>
                        </div>

                        <label class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1 block mt-6">Pilih Ruangan Kuliah</label>
                        {{-- PERBAIKAN: Searchable Room Selector --}}
                        <div class="relative" x-data="{ open: false }">
                            <input type="text" wire:model.live="searchRuang" @focus="open = true" @click.away="open = false"
                                placeholder="{{ $selectedRuangName ?: 'Cari Kode atau Nama Ruang...' }}"
                                class="w-full rounded-2xl border-slate-200 py-4 px-5 font-bold focus:ring-[#fcc000] bg-white">
                            @if(!empty($searchRuang))
                            <div x-show="open" class="absolute z-50 w-full bg-white shadow-2xl rounded-2xl mt-2 border border-slate-100 max-h-64 overflow-y-auto animate-in fade-in zoom-in-95">
                                @foreach($ruangOptions as $ro)
                                <div wire:click="pilihRuang('{{ $ro->id }}', '[{{ $ro->kode_ruang }}] {{ $ro->nama_ruang }}')" @click="open = false" class="px-5 py-4 hover:bg-indigo-50 cursor-pointer border-b border-slate-50 last:border-0 transition-all">
                                    <p class="text-xs font-black text-[#002855] uppercase">[{{ $ro->kode_ruang }}] {{ $ro->nama_ruang }}</p>
                                    <p class="text-[9px] text-slate-400 font-bold mt-1 uppercase tracking-wider">Kapasitas: {{ $ro->kapasitas }} Mahasiswa</p>
                                </div>
                                @endforeach
                            </div>
                            @endif
                        </div>

                        @if($roomConflict)
                        <div class="p-4 bg-rose-50 border border-rose-100 rounded-2xl animate-pulse mt-4">
                            <p class="text-[10px] font-black text-rose-600 uppercase tracking-widest">⚠ KONFLIK RUANGAN!</p>
                            <p class="text-xs text-rose-500 font-bold mt-1">{{ $roomConflict['mk'] }} ({{ $roomConflict['kelas'] }}) jam {{ $roomConflict['waktu'] }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="pt-10 border-t border-slate-50 space-y-6">
                    <h2 class="text-xl font-black text-[#002855] flex items-center gap-3">
                        <span class="w-8 h-8 rounded-full bg-[#fcc000] text-[#002855] flex items-center justify-center text-sm">2</span>
                        Team Teaching
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-start">
                        <div class="relative" x-data="{ open: false }">
                            <label class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1 mb-2 block">Cari & Tambah Dosen</label>
                            <input type="text" wire:model.live="searchDosen" @focus="open = true" @click.away="open = false" placeholder="Ketik Nama Dosen..." class="w-full rounded-2xl border-slate-200 py-4 px-5 font-bold focus:ring-[#fcc000]">
                            @if(!empty($searchDosen))
                            <div x-show="open" class="absolute z-50 w-full bg-white shadow-2xl rounded-2xl mt-2 border border-slate-100 max-h-60 overflow-y-auto">
                                @foreach($dosens as $d)
                                <div wire:click="tambahDosen('{{ $d->id }}', '{{ $d->person->nama_lengkap }}')" @click="open = false" class="px-5 py-4 hover:bg-indigo-50 cursor-pointer border-b border-slate-50 transition-all flex justify-between items-center">
                                    <div class="text-xs font-black text-[#002855] uppercase">{{ $d->person->nama_lengkap }}</div>
                                    <div class="text-[10px] bg-indigo-100 text-indigo-600 px-2 py-0.5 rounded font-bold">ADD</div>
                                </div>
                                @endforeach
                            </div>
                            @endif
                        </div>

                        <div class="space-y-3">
                            <label class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1 mb-2 block">Dosen Terpilih (Gunakan Radio untuk Koordinator)</label>
                            <div class="space-y-2">
                                @forelse($selectedDosenList as $sd)
                                <div class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl border border-slate-100 group transition-all">
                                    <div class="flex items-center gap-4">
                                        <input type="radio" wire:model="koordinator_id" value="{{ $sd['id'] }}" class="text-[#fcc000] focus:ring-[#fcc000]">
                                        <span class="text-xs font-black text-[#002855] uppercase">{{ $sd['nama'] }}</span>
                                        @if($koordinator_id == $sd['id']) <span class="text-[8px] bg-[#fcc000] text-[#002855] px-1.5 py-0.5 rounded font-black tracking-widest uppercase">Koor</span> @endif
                                    </div>
                                    <button wire:click="hapusDosen('{{ $sd['id'] }}')" class="text-slate-300 hover:text-rose-500 transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg></button>
                                </div>
                                @empty
                                <div class="text-center py-6 border-2 border-dashed border-slate-100 rounded-3xl text-slate-400 text-xs font-bold uppercase tracking-widest">Belum ada dosen terpilih</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Side: Schedule & Publish --}}
            <div class="lg:col-span-4 bg-slate-50/50 p-8 md:p-12 space-y-10">
                <h2 class="text-xl font-black text-[#002855] flex items-center gap-3">
                    <span class="w-8 h-8 rounded-full bg-[#fcc000] text-[#002855] flex items-center justify-center text-sm">3</span>
                    Waktu Perkuliahan
                </h2>

                <div class="space-y-6">
                    <div class="space-y-2">
                        <label class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em]">Hari</label>
                        <div class="grid grid-cols-3 gap-2">
                            @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $h)
                            <button wire:click="$set('hari', '{{ $h }}')" class="py-3 rounded-xl text-[10px] font-black uppercase transition-all {{ $hari == $h ? 'bg-[#002855] text-white shadow-lg' : 'bg-white text-slate-400 border border-slate-200' }}">
                                {{ $h }}
                            </button>
                            @endforeach
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <div class="space-y-2 text-center">
                            <label class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em]">Mulai</label>
                            <input type="text" wire:model.live="jam_mulai" placeholder="00:00" class="w-full rounded-2xl border-slate-200 py-4 text-center font-black text-2xl text-[#002855]">
                        </div>
                        <div class="space-y-2 text-center">
                            <label class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em]">Selesai</label>
                            <input type="text" wire:model.live="jam_selesai" placeholder="00:00" class="w-full rounded-2xl border-slate-200 py-4 text-center font-black text-2xl text-[#002855]">
                        </div>
                    </div>
                </div>

                @if(!empty($lecturerConflict))
                <div class="p-6 bg-rose-50 border border-rose-100 rounded-[2rem] space-y-4">
                    <h5 class="text-[10px] font-black text-rose-700 uppercase tracking-widest flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        BENTROK JADWAL DOSEN
                    </h5>
                    @foreach($lecturerConflict as $lc)
                    <div class="bg-white p-3 rounded-xl border border-rose-100">
                        <p class="text-[10px] font-black text-slate-700 uppercase">{{ $lc['nama'] }}</p>
                        <p class="text-[10px] text-rose-500 font-bold mt-1">{{ $lc['mk'] }} &bull; {{ $lc['waktu'] }}</p>
                    </div>
                    @endforeach
                </div>
                @endif

                <div class="pt-10">
                    <button wire:click="save" class="w-full py-5 rounded-3xl bg-[#002855] text-white font-black text-sm tracking-[0.3em] uppercase shadow-2xl shadow-blue-900/40 hover:-translate-y-2 transition-all duration-300 disabled:bg-slate-200 disabled:shadow-none" {{ $formStatus == 'red' ? 'disabled' : '' }}>
                        PUBLISH JADWAL
                    </button>
                    <p class="text-center text-[10px] font-bold text-slate-400 mt-6 uppercase tracking-widest">Integritas data diverifikasi otomatis</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Jadwal Table --}}
    <div class="bg-white rounded-[3rem] shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50/50">
                    <tr>
                        <th class="px-8 py-6 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Waktu & Ruang</th>
                        <th class="px-8 py-6 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Mata Kuliah</th>
                        <th class="px-8 py-6 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Team Teaching</th>
                        <th class="px-8 py-6 text-right text-[10px] font-black text-slate-400 uppercase tracking-widest">Opsi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($jadwals as $j)
                    <tr class="hover:bg-slate-50/80 transition-all group">
                        <td class="px-8 py-8 align-top">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-[#002855]/5 rounded-2xl flex flex-col items-center justify-center text-[#002855]">
                                    <span class="text-[9px] font-black uppercase leading-none">{{ substr($j->hari, 0, 3) }}</span>
                                    <span class="text-xs font-black mt-1">{{ substr($j->jam_mulai, 0, 5) }}</span>
                                </div>
                                <div>
                                    <p class="text-xs font-black text-[#002855] uppercase tracking-tighter">Ruang {{ $j->ruang->kode_ruang ?? 'TBA' }}</p>
                                    <p class="text-[10px] font-bold text-slate-400 mt-1">{{ $j->ruang->nama_ruang ?? '-' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-8 align-top">
                            <h4 class="text-sm font-black text-[#002855] leading-none uppercase tracking-tight">{{ $j->mataKuliah->nama_mk }}</h4>
                            <div class="flex items-center gap-3 mt-3">
                                <span class="px-2 py-0.5 bg-[#fcc000] text-[#002855] text-[9px] font-black rounded tracking-widest uppercase">{{ $j->nama_kelas }}</span>
                                <span class="px-2 py-0.5 bg-slate-100 text-slate-500 text-[9px] font-bold rounded uppercase">{{ $j->mataKuliah->sks_default }} SKS</span>
                            </div>
                        </td>
                        <td class="px-8 py-8 align-top">
                            <div class="flex flex-col gap-2">
                                @foreach($j->dosens as $d)
                                <div class="flex items-center gap-2">
                                    <div class="w-1.5 h-1.5 rounded-full {{ $d->pivot->is_koordinator ? 'bg-amber-400' : 'bg-slate-300' }}"></div>
                                    <span class="text-[11px] font-bold text-slate-600 uppercase">{{ $d->person->nama_lengkap }}</span>
                                    @if($d->pivot->is_koordinator) <span class="text-[7px] font-black text-amber-600 border border-amber-200 px-1 rounded uppercase tracking-tighter">Koor</span> @endif
                                </div>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-8 py-8 text-right align-top">
                            <div class="flex justify-end gap-3 opacity-0 group-hover:opacity-100 transition-opacity">
                                <button wire:click="edit('{{ $j->id }}')" class="p-3 text-indigo-600 hover:bg-indigo-50 rounded-xl transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg></button>
                                <button class="p-3 text-rose-500 hover:bg-rose-50 rounded-xl transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg></button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-8 py-6 bg-slate-50/50">
            {{ $jadwals->links() }}
        </div>
    </div>
</div>