<div class="space-y-6 animate-in fade-in duration-500">
    
    {{-- 1. Header Section --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-[#002855] rounded-2xl flex items-center justify-center text-[#fcc000] shadow-lg shadow-blue-900/20">
                <x-heroicon-o-document-check class="w-7 h-7" />
            </div>
            <div>
                <h1 class="text-2xl font-black text-[#002855] tracking-tight uppercase">Jadwal Ujian</h1>
                <p class="text-slate-500 text-sm font-medium">Manajemen UTS, UAS, dan Alokasi Pengawas Ruangan Semester {{ $filterSemesterId }}</p>
            </div>
        </div>
        
        @if(!$showForm)
        <button wire:click="$set('showForm', true)" class="inline-flex items-center px-6 py-3 bg-[#fcc000] text-[#002855] rounded-xl font-black text-xs uppercase tracking-widest shadow-lg shadow-amber-500/20 hover:bg-[#fbbf24] hover:scale-105 transition-all group">
            <x-heroicon-s-plus class="w-5 h-5 mr-2 transition-transform group-hover:rotate-90" />
            Buat Jadwal Ujian
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

    {{-- 3. Form Section (Create/Edit) --}}
    @if($showForm)
    <div class="bg-white rounded-3xl shadow-2xl border border-slate-200 overflow-hidden animate-in zoom-in-95 duration-300">
        <div class="grid grid-cols-1 lg:grid-cols-12">
            
            {{-- Left Side: Exam Config --}}
            <div class="lg:col-span-7 p-8 md:p-10 space-y-10 border-r border-slate-100">
                <div class="flex items-center justify-between">
                    <h2 class="text-sm font-black text-[#002855] uppercase tracking-[0.2em] flex items-center gap-3">
                        <span class="w-7 h-7 rounded-lg bg-[#fcc000] text-[#002855] flex items-center justify-center text-xs shadow-sm">1</span>
                        Informasi Ujian
                    </h2>
                    <button wire:click="resetForm" class="text-slate-400 hover:text-rose-500 transition-colors bg-slate-50 p-1.5 rounded-full">
                        <x-heroicon-s-x-mark class="w-5 h-5" />
                    </button>
                </div>

                <div class="space-y-6">
                    <div class="space-y-2">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Mata Kuliah & Kelas *</label>
                        <select wire:model="jadwal_kuliah_id" class="block w-full rounded-xl border-slate-300 bg-slate-50 p-4 focus:border-[#002855] focus:ring-[#002855] text-sm font-bold @error('jadwal_kuliah_id') border-rose-300 bg-rose-50 @enderror" {{ $ujianId ? 'disabled' : '' }}>
                            <option value="">-- Pilih Kelas Terdaftar --</option>
                            @foreach($jadwalKuliahOptions as $jk)
                                <option value="{{ $jk->id }}">{{ $jk->mataKuliah->nama_mk }} (Kelas: {{ $jk->nama_kelas }})</option>
                            @endforeach
                        </select>
                        @error('jadwal_kuliah_id') <p class="text-[10px] font-bold text-rose-500 uppercase px-1">Wajib pilih kelas perkuliahan</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Jenis Ujian</label>
                            <select wire:model="jenis_ujian" class="block w-full rounded-xl border-slate-300 bg-white p-3.5 focus:ring-[#fcc000] font-bold text-sm text-[#002855]">
                                <option value="UTS">Tengah Semester (UTS)</option>
                                <option value="UAS">Akhir Semester (UAS)</option>
                                <option value="SUSULAN">Ujian Susulan</option>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Metode Ujian</label>
                            <select wire:model="metode_ujian" class="block w-full rounded-xl border-slate-300 bg-white p-3.5 focus:ring-[#fcc000] font-bold text-sm text-slate-600">
                                <option value="TERTULIS">Tertulis (Paper Based)</option>
                                <option value="CBT">CBT (Computer Based)</option>
                                <option value="PRAKTEK">Praktek</option>
                                <option value="TAKE_HOME">Project / Take Home</option>
                            </select>
                        </div>
                    </div>

                    <div class="space-y-4 pt-4 border-t border-slate-50">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Alokasi Waktu & Ruangan</label>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="space-y-1">
                                <input type="date" wire:model.live="tanggal_ujian" class="block w-full rounded-xl border-slate-300 bg-slate-50 p-3 text-sm font-bold @error('tanggal_ujian') border-rose-300 @enderror">
                                @error('tanggal_ujian') <p class="text-[9px] font-bold text-rose-500 uppercase">Wajib diisi</p> @enderror
                            </div>
                            <div class="space-y-1">
                                <input type="time" wire:model.live="jam_mulai" class="block w-full rounded-xl border-slate-300 bg-slate-50 p-3 text-sm font-bold text-center">
                            </div>
                            <div class="space-y-1">
                                <input type="time" wire:model.live="jam_selesai" class="block w-full rounded-xl border-slate-300 bg-slate-50 p-3 text-sm font-bold text-center">
                            </div>
                        </div>

                        <select wire:model.live="ruang_id" class="block w-full rounded-xl border-slate-300 bg-white p-4 focus:ring-[#fcc000] text-sm font-bold @error('ruang_id') border-rose-300 @enderror">
                            <option value="">-- Pilih Ruang Ujian --</option>
                            @foreach($ruangan as $r)
                                <option value="{{ $r->id }}">R. {{ $r->kode_ruang }} - {{ $r->nama_ruang }} (Kapasitas: {{ $r->kapasitas }})</option>
                            @endforeach
                        </select>
                        @error('ruang_id') <p class="text-[10px] font-bold text-rose-500 uppercase px-1">Ruangan belum dipilih</p> @enderror

                        @if($roomConflict)
                        <div class="p-4 bg-rose-50 border border-rose-100 rounded-2xl flex items-start gap-3 animate-pulse shadow-sm">
                            <x-heroicon-s-exclamation-triangle class="w-5 h-5 text-rose-500 mt-0.5" />
                            <div>
                                <p class="text-[10px] font-black text-rose-700 uppercase tracking-widest">⚠ Bentrok Ruangan!</p>
                                <p class="text-[10px] text-rose-500 font-bold uppercase">{{ $roomConflict['mk'] }} &bull; Jam {{ $roomConflict['waktu'] }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Right Side: Proctors & Action --}}
            <div class="lg:col-span-5 bg-slate-50/70 p-8 md:p-10 space-y-10">
                <h2 class="text-sm font-black text-[#002855] uppercase tracking-[0.2em] flex items-center gap-3">
                    <span class="w-7 h-7 rounded-lg bg-[#fcc000] text-[#002855] flex items-center justify-center text-xs shadow-sm">2</span>
                    Alokasi Pengawas
                </h2>

                <div class="space-y-6">
                    <div class="space-y-2">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Cari Dosen / Staff</label>
                        <div class="relative" x-data="{ open: false }">
                            <div class="relative">
                                <input type="text" wire:model.live="searchPengawas" @focus="open = true" @click.away="open = false" placeholder="Ketik Nama Pengawas..." class="block w-full rounded-xl border-slate-300 bg-white py-3.5 pl-10 pr-4 text-sm font-bold focus:ring-[#fcc000] shadow-sm">
                                <x-heroicon-o-user-group class="w-5 h-5 absolute left-3.5 top-3.5 text-slate-400" />
                            </div>
                            @if(!empty($calonPengawas))
                            <div x-show="open" class="absolute z-50 w-full bg-white shadow-2xl rounded-2xl mt-2 border border-slate-100 max-h-52 overflow-y-auto">
                                @foreach($calonPengawas as $cp)
                                <div wire:click="tambahPengawas('{{ $cp->id }}', '{{ $cp->nama_lengkap }}')" @click="open = false" class="px-5 py-3 hover:bg-indigo-50 cursor-pointer border-b border-slate-50 transition-all flex justify-between items-center group">
                                    <div class="text-xs font-black text-[#002855] uppercase">{{ $cp->nama_lengkap }}</div>
                                    <x-heroicon-s-plus-circle class="w-5 h-5 text-slate-300 group-hover:text-indigo-500" />
                                </div>
                                @endforeach
                            </div>
                            @endif
                        </div>
                    </div>

                    <div class="space-y-3">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Daftar Pengawas Tugas</label>
                        <div class="space-y-2">
                            @forelse($selectedPengawas as $sp)
                            <div class="flex items-center justify-between p-3.5 bg-white rounded-xl border border-slate-200 group hover:border-[#002855] transition-all shadow-sm">
                                <div class="flex-1">
                                    <p class="text-xs font-black text-[#002855] uppercase leading-tight">{{ $sp['nama'] }}</p>
                                    <select wire:change="setPeranPengawas('{{ $sp['id'] }}', $event.target.value)" class="mt-1.5 text-[9px] font-black uppercase text-indigo-600 bg-indigo-50 border-none rounded-lg py-1 pl-2 pr-6 focus:ring-0 cursor-pointer">
                                        <option value="UTAMA" {{ $sp['peran'] == 'UTAMA' ? 'selected' : '' }}>Pengawas Utama</option>
                                        <option value="PENDAMPING" {{ $sp['peran'] == 'PENDAMPING' ? 'selected' : '' }}>Pendamping</option>
                                    </select>
                                </div>
                                <button wire:click="hapusPengawas('{{ $sp['id'] }}')" class="ml-4 text-slate-300 hover:text-rose-500 transition-colors">
                                    <x-heroicon-s-trash class="w-5 h-5" />
                                </button>
                            </div>
                            @empty
                            <div class="text-center py-8 border-2 border-dashed border-slate-200 rounded-2xl text-slate-400 text-[10px] font-black uppercase tracking-widest">Belum Ada Pengawas</div>
                            @endforelse
                        </div>
                    </div>

                    <div class="pt-8">
                        <button wire:click="save" class="w-full py-4 rounded-2xl bg-[#002855] text-white font-black text-xs tracking-[0.3em] uppercase shadow-2xl shadow-blue-900/30 hover:scale-[1.02] active:scale-95 transition-all duration-300 disabled:bg-slate-200 disabled:shadow-none" {{ $roomConflict ? 'disabled' : '' }}>
                            <span wire:loading.remove wire:target="save">Simpan Jadwal Ujian</span>
                            <span wire:loading wire:target="save" class="flex items-center justify-center">
                                <svg class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            </span>
                        </button>
                        <p class="text-center text-[9px] font-black text-slate-400 mt-6 uppercase tracking-widest leading-loose">Validasi bentrok jam pengawas dilakukan secara otomatis</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- 4. Filter Bar --}}
    @if(!$showForm)
    <div class="bg-white p-2.5 rounded-2xl border border-slate-200 grid grid-cols-1 md:grid-cols-4 gap-3 shadow-sm">
        <div class="flex items-center bg-slate-50 rounded-xl px-4 py-2.5 border border-slate-100">
            <span class="text-[9px] font-black text-slate-400 mr-3 uppercase tracking-tighter">Semester</span>
            <select wire:model.live="filterSemesterId" class="flex-1 bg-transparent border-none text-sm font-bold text-[#002855] focus:ring-0 cursor-pointer">
                @foreach($semesters as $s) <option value="{{ $s->id }}">{{ $s->nama_tahun }}</option> @endforeach
            </select>
        </div>
        <div class="flex items-center bg-slate-50 rounded-xl px-4 py-2.5 border border-slate-100">
            <span class="text-[9px] font-black text-slate-400 mr-3 uppercase tracking-tighter">Prodi</span>
            <select wire:model.live="filterProdiId" class="flex-1 bg-transparent border-none text-sm font-bold text-slate-700 focus:ring-0 cursor-pointer">
                @foreach($prodis as $p) <option value="{{ $p->id }}">{{ $p->nama_prodi }}</option> @endforeach
            </select>
        </div>
        <div class="flex items-center bg-slate-50 rounded-xl px-4 py-2.5 border border-slate-100">
            <span class="text-[9px] font-black text-slate-400 mr-3 uppercase tracking-tighter">Tipe</span>
            <select wire:model.live="filterJenisUjian" class="flex-1 bg-transparent border-none text-sm font-bold text-indigo-700 focus:ring-0 cursor-pointer">
                <option value="">Semua Ujian</option>
                <option value="UTS">UTS</option>
                <option value="UAS">UAS</option>
                <option value="SUSULAN">Susulan</option>
            </select>
        </div>
        <div class="relative group">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari Mata Kuliah..." class="w-full bg-white rounded-xl px-10 py-2.5 border border-slate-200 text-xs font-bold focus:ring-[#002855] focus:border-[#002855] transition-all shadow-xs">
            <x-heroicon-o-magnifying-glass class="w-4 h-4 absolute left-3.5 top-3 text-slate-400 group-focus-within:text-[#002855]" />
        </div>
    </div>
    @endif

    {{-- 5. Main Table Section --}}
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden relative">
        <div wire:loading.flex wire:target="filterSemesterId, filterProdiId, filterJenisUjian, search, gotoPage, nextPage, previousPage" class="absolute inset-0 z-10 bg-white/60 backdrop-blur-[1px] items-center justify-center">
            <div class="p-4 bg-white rounded-2xl shadow-xl border border-slate-100 flex flex-col items-center">
                <svg class="w-8 h-8 text-[#002855] animate-spin mb-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Sinkronisasi...</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#002855] text-white">
                        <th class="px-8 py-5 text-[10px] font-bold uppercase tracking-widest">Tipe & Waktu</th>
                        <th class="px-8 py-5 text-[10px] font-bold uppercase tracking-widest">Mata Kuliah / Ruang</th>
                        <th class="px-8 py-5 text-[10px] font-bold uppercase tracking-widest">Personel Pengawas</th>
                        <th class="px-8 py-5 text-[10px] font-bold uppercase tracking-widest text-right">Opsi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($ujians as $u)
                    <tr class="hover:bg-slate-50/80 transition-all group">
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-4">
                                <div class="w-14 h-14 rounded-2xl flex flex-col items-center justify-center text-white shadow-lg group-hover:scale-110 transition-transform {{ $u->jenis_ujian == 'UTS' ? 'bg-indigo-600 shadow-indigo-200' : ($u->jenis_ujian == 'UAS' ? 'bg-emerald-600 shadow-emerald-200' : 'bg-amber-500 shadow-amber-200') }}">
                                    <span class="text-[9px] font-black tracking-widest uppercase opacity-70">{{ $u->jenis_ujian }}</span>
                                    <span class="text-xs font-black mt-0.5">{{ \Carbon\Carbon::parse($u->tanggal_ujian)->format('d/m') }}</span>
                                </div>
                                <div>
                                    <p class="text-xs font-black text-slate-800 uppercase">{{ \Carbon\Carbon::parse($u->tanggal_ujian)->isoFormat('dddd, D MMM') }}</p>
                                    <p class="text-[10px] font-bold text-slate-400 mt-1 uppercase tracking-tighter">{{ substr($u->jam_mulai, 0, 5) }} - {{ substr($u->jam_selesai, 0, 5) }} WITA</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <h4 class="text-sm font-black text-[#002855] uppercase tracking-tight">{{ $u->jadwalKuliah->mataKuliah->nama_mk }}</h4>
                            <div class="flex items-center gap-3 mt-3">
                                <span class="px-2.5 py-1 bg-white border border-slate-200 text-slate-700 text-[9px] font-black rounded-lg shadow-xs tracking-widest uppercase">RUANG {{ $u->ruang->kode_ruang ?? 'TBA' }}</span>
                                <span class="px-2.5 py-1 bg-sky-50 text-sky-600 text-[9px] font-black rounded-lg border border-sky-100 uppercase tracking-widest">{{ $u->metode_ujian }}</span>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <div class="flex flex-col gap-1.5">
                                @forelse($u->pengawas as $p)
                                    <div class="flex items-center gap-2 text-[10px] font-bold text-slate-600 uppercase group-hover:text-[#002855] transition-colors">
                                        <div class="w-1.5 h-1.5 rounded-full {{ $p->peran == 'UTAMA' ? 'bg-rose-400' : 'bg-slate-300' }}"></div>
                                        {{ $p->person->nama_lengkap }}
                                        @if($p->peran == 'UTAMA') <span class="text-[7px] font-black text-rose-500 bg-rose-50 border border-rose-100 px-1 rounded shadow-xs">MAIN</span> @endif
                                    </div>
                                @empty
                                    <span class="text-[9px] text-rose-400 italic font-black uppercase tracking-widest">Belum Ada Pengawas</span>
                                @endforelse
                            </div>
                        </td>
                        <td class="px-8 py-6 text-right">
                            <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-all transform translate-x-2 group-hover:translate-x-0">
                                <button wire:click="edit('{{ $u->id }}')" class="p-2.5 text-[#002855] bg-indigo-50 hover:bg-[#002855] hover:text-white rounded-xl transition-all shadow-sm">
                                    <x-heroicon-s-pencil class="w-4 h-4" />
                                </button>
                                <button wire:click="delete('{{ $u->id }}')" wire:confirm="Hapus jadwal ujian ini? Data absensi ujian terkait akan hilang." class="p-2.5 text-rose-600 bg-rose-50 hover:bg-rose-600 hover:text-white rounded-xl transition-all shadow-sm">
                                    <x-heroicon-s-trash class="w-4 h-4" />
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center justify-center opacity-30 grayscale">
                                <x-heroicon-o-calendar-days class="w-16 h-16 text-slate-400 mb-4" />
                                <p class="text-sm font-black uppercase tracking-widest text-slate-500">Jadwal Ujian Belum Tersedia</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($ujians->hasPages())
        <div class="px-8 py-6 border-t border-slate-100 bg-slate-50/50">
            {{ $ujians->links() }}
        </div>
        @endif
    </div>

    {{-- System Footer --}}
    <div class="flex items-center justify-center gap-3 opacity-20 grayscale select-none pointer-events-none py-6">
        <div class="h-px bg-slate-300 w-12"></div>
        <p class="text-[9px] font-black uppercase tracking-[0.4em] text-[#002855]">Exam Management Control &bull; UNMARIS</p>
        <div class="h-px bg-slate-300 w-12"></div>
    </div>

    {{-- SweetAlert2 Listener --}}
    @script
    <script>
        $wire.on('swal:success', data => { 
            // Implement alert logic if needed
            console.log('Success:', data[0].text); 
        });
        $wire.on('swal:error', data => { 
            console.error('Error:', data[0].text); 
        });
    </script>
    @endscript
</div>