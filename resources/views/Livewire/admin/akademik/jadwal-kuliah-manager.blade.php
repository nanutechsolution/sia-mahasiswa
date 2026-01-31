<div class="space-y-8">
    {{-- Header --}}
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-[#002855]">Manajemen Jadwal Kuliah</h1>
            <p class="text-slate-500 text-sm mt-1">Kelola distribusi kelas, penugasan dosen, dan visualisasi jadwal.</p>
        </div>

        @if(!$showForm)
        <div class="flex items-center gap-2">
            <button wire:click="openCloneModal" class="inline-flex items-center px-4 py-2.5 bg-white border border-slate-300 text-slate-600 rounded-xl font-bold text-xs uppercase tracking-widest hover:bg-slate-50 transition-all shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" />
                </svg>
                Clone Jadwal Lalu
            </button>
            <button wire:click="create" class="inline-flex items-center px-5 py-2.5 bg-[#fcc000] text-[#002855] rounded-xl font-black text-xs uppercase tracking-widest shadow-lg shadow-orange-500/20 hover:bg-[#fbbf24] hover:scale-105 transition-all">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" />
                </svg>
                Buat Kelas Baru
            </button>
        </div>
        @endif
    </div>

    {{-- Filter & View Switcher --}}
    @if(!$showForm)
    <div class="bg-white p-2 rounded-2xl shadow-sm border border-slate-200 flex flex-col md:flex-row gap-2">
        <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-2 p-2">
            <div class="relative">
                <select wire:model.live="filterSemesterId" class="block w-full rounded-xl border-slate-200 bg-slate-50 text-slate-900 py-2 pl-4 pr-10 text-sm font-bold focus:ring-[#002855] focus:border-[#002855]">
                    @foreach($semesters as $sem)
                    <option value="{{ $sem->id }}">{{ $sem->nama_tahun }} {{ $sem->is_active ? '(Aktif)' : '' }}</option>
                    @endforeach
                </select>
            </div>
            <div class="relative">
                <select wire:model.live="filterProdiId" class="block w-full rounded-xl border-slate-200 bg-slate-50 text-slate-900 py-2 pl-4 pr-10 text-sm font-bold focus:ring-[#002855] focus:border-[#002855]">
                    @foreach($prodis as $prodi)
                    <option value="{{ $prodi->id }}">{{ $prodi->jenjang }} - {{ $prodi->nama_prodi }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="flex bg-slate-100 p-1 rounded-xl">
            <button wire:click="$set('viewMode', 'list')" class="px-4 py-2 rounded-lg text-xs font-black uppercase transition-all flex items-center gap-2 {{ $viewMode == 'list' ? 'bg-white text-[#002855] shadow-sm' : 'text-slate-400 hover:text-slate-600' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                </svg> List
            </button>
            <button wire:click="$set('viewMode', 'grid')" class="px-4 py-2 rounded-lg text-xs font-black uppercase transition-all flex items-center gap-2 {{ $viewMode == 'grid' ? 'bg-white text-[#002855] shadow-sm' : 'text-slate-400 hover:text-slate-600' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7-4h14m-7-4v8m-9-4h14" />
                </svg> Grid
            </button>
        </div>
    </div>
    @endif

    {{-- Alert Messages (Sukses) --}}
    @if (session()->has('success'))
    <div class="bg-emerald-50 border border-emerald-100 p-4 rounded-xl text-emerald-800 text-sm font-bold flex items-center shadow-sm animate-bounce-short">
        <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
        </svg>
        {{ session('success') }}
    </div>
    @endif

    {{-- Form Section --}}
    @if($showForm)
    <div class="bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden animate-in zoom-in-95 duration-200 relative">
        <div class="px-8 py-5 border-b border-slate-100 bg-slate-50 flex items-center justify-between {{ $conflictMessage ? 'mt-10' : '' }}">
            <h3 class="text-sm font-bold text-[#002855] uppercase tracking-wider flex items-center gap-2">
                {{ $editMode ? 'Edit Jadwal' : 'Setup Kelas Baru' }}
            </h3>
            <button wire:click="batal" class="text-slate-400 hover:text-slate-600">&times;</button>
        </div>

        <div class="p-8 space-y-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                {{-- Input: Prodi --}}
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Program Studi *</label>
                    <select wire:model.live="form_prodi_id" class="w-full rounded-xl border-slate-200 text-sm font-bold py-2 pl-4 focus:ring-2 focus:ring-[#fcc000] focus:outline-none">
                        @foreach($prodis as $p)<option value="{{ $p->id }}">{{ $p->nama_prodi }}</option>@endforeach
                    </select>
                    @error('form_prodi_id') <span class="text-rose-500 text-xs font-bold">{{ $message }}</span> @enderror
                </div>

                <div x-data="{ open: false }" class="relative">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Mata Kuliah *</label>
                    <input type="text" wire:model.live="searchMk" @focus="open = true" @click.away="open = false" placeholder="{{ $selectedMkName ?: 'Cari MK...' }}" class="w-full rounded-xl border-slate-200 text-sm font-bold py-2 pl-4 focus:ring-2 focus:ring-[#fcc000] focus:outline-none" @if(!$form_prodi_id) disabled @endif>
                    @if(!empty($searchMk) && !empty($formMks))
                    <div x-show="open" class="absolute z-10 w-full bg-white shadow-xl max-h-60 rounded-xl py-1 mt-1 overflow-auto border border-slate-100">
                        @foreach($formMks as $mk)
                        <div wire:click="pilihMk('{{ $mk->id }}', '{{ $mk->nama_mk }}', '{{ $mk->kode_mk }}', '{{ $mk->sks_default }}')" @click="open = false" class="px-4 py-2 hover:bg-indigo-50 cursor-pointer border-b border-slate-50 last:border-0 text-sm text-slate-700"><b>{{ $mk->nama_mk }}</b> ({{ $mk->sks_default }} SKS)</div>
                        @endforeach
                    </div>
                    @endif
                    @error('mata_kuliah_id') <span class="text-rose-500 text-xs font-bold">{{ $message }}</span> @enderror
                </div>

                <div x-data="{ open: false }" class="relative">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Dosen Pengampu *</label>
                    <input type="text" wire:model.live="searchDosen" @focus="open = true" @click.away="open = false" placeholder="{{ $selectedDosenName ?: 'Cari Dosen...' }}" class="w-full rounded-xl border-slate-200 text-sm font-bold py-2 pl-4 focus:ring-2 focus:ring-[#fcc000] focus:outline-none">
                    @if(!empty($searchDosen) && !empty($dosens))
                    <div x-show="open" class="absolute z-10 w-full bg-white shadow-xl max-h-60 rounded-xl py-1 mt-1 overflow-auto border border-slate-100">
                        @foreach($dosens as $d)
                        <div wire:click="pilihDosen('{{ $d->id }}', '{{ $d->nama_lengkap }}')" @click="open = false" class="px-4 py-2 hover:bg-indigo-50 cursor-pointer text-sm text-slate-700 border-b border-slate-50 last:border-0"><b>{{ $d->nama_lengkap }}</b> <span class="text-xs text-slate-400">{{ $d->nidn }}</span></div>
                        @endforeach
                    </div>
                    @endif
                    @error('dosen_id') <span class="text-rose-500 text-xs font-bold">{{ $message }}</span> @enderror
                </div>

                {{-- Detail Jadwal --}}
                <div><label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Nama Kelas</label><input type="text" wire:model.blur="nama_kelas" class="w-full rounded-xl border-slate-200 text-sm font-bold py-2 pl-4 focus:ring-2 focus:ring-[#fcc000] focus:outline-none" placeholder="A">@error('nama_kelas') <span class="text-rose-500 text-xs font-bold">{{ $message }}</span> @enderror</div>

                <div><label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Ruangan</label><input type="text" wire:model.blur="ruang" class="w-full rounded-xl border-slate-200 text-sm font-bold py-2 pl-4 focus:ring-2 focus:ring-[#fcc000] focus:outline-none" placeholder="R.101">@error('ruang') <span class="text-rose-500 text-xs font-bold">{{ $message }}</span> @enderror</div>

                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Hari</label>
                    <select wire:model.live="hari" class="w-full rounded-xl border-slate-200 text-sm font-bold py-2 pl-4 focus:ring-2 focus:ring-[#fcc000] focus:outline-none">
                        <option value="">Pilih Hari</option>
                        @foreach($hariList as $h)<option value="{{ $h }}">{{ $h }}</option>@endforeach
                    </select>
                    @error('hari') <span class="text-rose-500 text-xs font-bold">{{ $message }}</span> @enderror
                </div>

                {{-- INPUT JAM MULAI --}}
                <div x-data="{
    time: @entangle('jam_mulai'),
    formatTimeOnBlur() {
        if(!this.time) return;

        // Ambil hanya angka
        let digits = this.time.replace(/\D/g, '');

        let hh = parseInt(digits.substring(0,2) || '0');
        let mm = parseInt(digits.substring(2,4) || '0');

        // Batasi jam & menit
        if(hh > 23) hh = 23;
        if(mm > 59) mm = 59;

        // Format HH:MM
        this.time = hh.toString().padStart(2,'0') + ':' + mm.toString().padStart(2,'0');

        // Set ke Livewire
        $wire.set('jam_mulai', this.time);
    }
}">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">
                        Jam Mulai (WITA)
                    </label>
                    <input type="text"
                        x-model="time"
                        @blur="formatTimeOnBlur"
                        class="w-full rounded-xl border-slate-200 text-sm font-bold py-2 pl-4 focus:ring-2 focus:ring-[#fcc000] focus:outline-none placeholder-slate-300"
                        placeholder="08:00" maxlength="5">
                    @error('jam_mulai') <span class="text-rose-500 text-xs font-bold">{{ $message }}</span> @enderror
                </div>

                {{-- INPUT JAM SELESAI --}}
                <div x-data="{
    time: @entangle('jam_selesai'),
    formatTimeOnBlur() {
        if(!this.time) return;

        let digits = this.time.replace(/\D/g, '');
        let hh = parseInt(digits.substring(0,2) || '0');
        let mm = parseInt(digits.substring(2,4) || '0');

        if(hh > 23) hh = 23;
        if(mm > 59) mm = 59;

        this.time = hh.toString().padStart(2,'0') + ':' + mm.toString().padStart(2,'0');

        $wire.set('jam_selesai', this.time);
    }
}">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">
                        Jam Selesai (WITA)
                    </label>
                    <input type="text"
                        x-model="time"
                        @blur="formatTimeOnBlur"
                        class="w-full rounded-xl border-slate-200 text-sm font-bold py-2 pl-4 focus:ring-2 focus:ring-[#fcc000] focus:outline-none placeholder-slate-300"
                        placeholder="10:30" maxlength="5">
                    @error('jam_selesai') <span class="text-rose-500 text-xs font-bold">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Kuota</label>
                    <input type="number" wire:model="kuota_kelas" class="w-full rounded-xl border-slate-200 text-sm font-bold py-2 pl-4 focus:ring-2 focus:ring-[#fcc000] focus:outline-none">
                </div>

                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Khusus Program (Opsional)</label>
                    <select wire:model="id_program_kelas_allow" class="w-full rounded-xl border-slate-200 text-sm font-bold py-2 pl-4 focus:ring-2 focus:ring-[#fcc000] focus:outline-none">
                        <option value="">-- Semua Program --</option>
                        @foreach($programKelasList as $pk)<option value="{{ $pk->id }}">{{ $pk->nama_program }}</option>@endforeach
                    </select>
                </div>
            </div>

            <div class="pt-6 border-t border-slate-100 flex justify-end gap-3 relative">

                {{-- Tombol Batal --}}
                <button wire:click="batal" class="px-6 py-2 text-sm font-bold text-slate-500 hover:text-slate-700">
                    Batal
                </button>

                {{-- Tombol Simpan --}}
                <button wire:click="save"
                    class="px-8 py-2.5 bg-[#002855] text-white rounded-xl text-sm font-bold shadow-lg hover:bg-[#001a38] transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                    @if($conflictMessage) disabled @endif>
                    Simpan Data
                </button>

                {{-- PESAN KONFLIK DI SAMPING TOMBOL --}}
                @if($conflictMessage)
                <div class="absolute -top-10 right-0 w-max bg-rose-500 text-white px-4 py-2 text-xs font-bold rounded-md shadow-lg flex items-center gap-2 animate-pulse z-20">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    {{ $conflictMessage }}
                </div>
                @endif

            </div>

        </div>
    </div>
    @endif

    {{-- LIST VIEW --}}
    @if($viewMode == 'list')
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-[#002855] text-white">
                    <tr>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest">Waktu</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest">Mata Kuliah / Kelas</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest">Dosen / Ruang</th>
                        <th class="px-6 py-4 text-center text-[10px] font-bold uppercase tracking-widest">Kuota</th>
                        <th class="px-6 py-4 text-right text-[10px] font-bold uppercase tracking-widest">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($jadwals as $jadwal)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="px-6 py-4 align-top">
                            <span class="font-black text-[#002855] text-sm">{{ $jadwal->hari }}</span>
                            <div class="text-xs font-semibold text-slate-500 mt-1">{{ substr($jadwal->jam_mulai,0,5) }} - {{ substr($jadwal->jam_selesai,0,5) }}</div>
                        </td>
                        <td class="px-6 py-4 align-top">
                            <div class="text-sm font-bold text-slate-800">{{ $jadwal->mataKuliah->nama_mk }}</div>
                            <span class="px-2 py-0.5 bg-[#002855]/10 text-[#002855] text-[10px] font-bold uppercase rounded mt-1 inline-block">Kls {{ $jadwal->nama_kelas }}</span>
                        </td>
                        <td class="px-6 py-4 align-top">
                            <div class="text-sm font-medium text-slate-700">{{ $jadwal->dosen->person->nama_lengkap ?? '-' }}</div>
                            <div class="text-[10px] font-bold text-amber-600 uppercase mt-1 tracking-wider">R. {{ $jadwal->ruang }}</div>
                        </td>
                        <td class="px-6 py-4 align-top text-center"><span class="px-2 py-0.5 rounded text-xs font-bold bg-emerald-50 text-emerald-700">{{ $jadwal->kuota_kelas }}</span></td>
                        <td class="px-6 py-4 align-top text-right space-x-2">
                            <button wire:click="edit('{{ $jadwal->id }}')" class="text-indigo-600 font-bold text-[10px] uppercase">Edit</button>
                            <button wire:click="delete('{{ $jadwal->id }}')" class="text-rose-600 font-bold text-[10px] uppercase">Hapus</button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-slate-400 italic">Belum ada jadwal.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100">{{ $jadwals->links() }}</div>
    </div>

    {{-- GRID VIEW --}}
    @elseif($viewMode == 'grid')
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
        @foreach($hariList as $hari)
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="bg-slate-50 px-4 py-3 border-b border-slate-100 text-center font-black text-[#002855] uppercase text-sm">
                {{ $hari }}
            </div>
            <div class="p-2 space-y-2 max-h-[600px] overflow-y-auto">
                @if(isset($jadwals[$hari]))
                @foreach($jadwals[$hari]->sortBy('jam_mulai') as $j)
                <div class="bg-indigo-50 border-l-4 border-[#002855] p-3 rounded-r-lg hover:bg-indigo-100 transition cursor-pointer" wire:click="edit('{{ $j->id }}')">
                    <div class="text-[10px] font-mono font-bold text-slate-500 mb-1">{{ substr($j->jam_mulai,0,5) }} - {{ substr($j->jam_selesai,0,5) }}</div>
                    <h4 class="text-xs font-bold text-[#002855] leading-tight">{{ $j->mataKuliah->nama_mk }}</h4>
                    <div class="flex justify-between items-end mt-2">
                        <span class="text-[10px] font-medium text-slate-600 truncate max-w-[80px]">{{ $j->dosen->person->nama_lengkap ?? '-' }}</span>
                        <span class="text-[9px] bg-white px-1.5 py-0.5 rounded border border-slate-200 font-bold">{{ $j->ruang }}</span>
                    </div>
                </div>
                @endforeach
                @else
                <div class="text-center py-4 text-xs text-slate-300 italic">Kosong</div>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- Clone Modal (Sama seperti sebelumnya) --}}
    @if($showCloneModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 animate-in fade-in">
        <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full p-6">
            <h3 class="text-lg font-black text-[#002855] mb-4">Salin Jadwal Semester Lalu</h3>
            <select wire:model="cloneSourceSemesterId" class="w-full rounded-xl border-slate-200 text-sm font-bold py-2 px-4 mb-4">
                <option value="">-- Pilih Semester Sumber --</option>
                @foreach($semesters as $sm)
                @if($sm->id != $filterSemesterId) <option value="{{ $sm->id }}">{{ $sm->nama_tahun }}</option> @endif
                @endforeach
            </select>
            <div class="flex justify-end gap-3">
                <button wire:click="$set('showCloneModal', false)" class="text-slate-400 font-bold text-xs uppercase">Batal</button>
                <button wire:click="cloneSchedule" class="bg-[#002855] text-white px-6 py-2 rounded-xl font-bold text-sm shadow-lg">Salin Jadwal</button>
            </div>
        </div>
    </div>
    @endif
</div>