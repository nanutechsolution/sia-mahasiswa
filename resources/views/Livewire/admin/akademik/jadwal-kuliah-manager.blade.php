<div class="space-y-6 animate-in fade-in duration-700">
    
    {{-- Header Page --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm">
        <div>
            <h1 class="text-3xl font-black text-[#002855] uppercase tracking-tight">Manajemen Jadwal Kuliah</h1>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">Otorisasi Kurikulum & Validasi Bentrok Real-Time</p>
        </div>
        <button wire:click="$set('showForm', true)" class="px-8 py-4 bg-[#002855] text-white rounded-2xl font-black text-xs uppercase tracking-widest shadow-xl shadow-indigo-900/20 hover:scale-105 active:scale-95 transition-all">
            + Buka Penawaran Baru
        </button>
    </div>

    {{-- Filter Area --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 flex items-center gap-5 shadow-sm">
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-black text-sm shadow-inner">TA</div>
            <div class="flex-1 min-w-0">
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Tahun Akademik</label>
                <select wire:model.live="filterSemesterId" class="w-full border-none p-0 font-black text-[#002855] focus:ring-0 text-base bg-transparent cursor-pointer">
                    @foreach($semesters as $s) <option value="{{ $s->id }}">{{ $s->nama_tahun }}</option> @endforeach
                </select>
            </div>
        </div>
        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 flex items-center gap-5 shadow-sm">
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center font-black text-sm shadow-inner">PS</div>
            <div class="flex-1 min-w-0">
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Program Studi</label>
                <select wire:model.live="filterProdiId" class="w-full border-none p-0 font-black text-[#002855] focus:ring-0 text-base bg-transparent cursor-pointer">
                    @foreach($prodis as $p) <option value="{{ $p->id }}">{{ $p->nama_prodi }}</option> @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- FORM PENYUSUNAN JADWAL (WIDE LAYOUT) --}}
    @if($showForm)
    <div class="bg-white rounded-[3rem] shadow-2xl border border-slate-200 overflow-hidden animate-in zoom-in-95 duration-300">
        <div class="px-10 py-8 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
            <div class="flex items-center gap-4">
                <div class="w-4 h-4 rounded-full {{ $formStatus == 'green' ? 'bg-emerald-500 animate-pulse' : ($formStatus == 'red' ? 'bg-rose-500 animate-bounce' : 'bg-slate-300') }}"></div>
                <div>
                    <h3 class="text-sm font-black text-[#002855] uppercase tracking-[0.2em]">Penerbitan Penawaran Kelas</h3>
                    <p class="text-[10px] text-slate-400 font-bold uppercase mt-0.5">Lengkapi data untuk mendeteksi bentrok otomatis</p>
                </div>
            </div>
            <button wire:click="resetForm" class="text-slate-300 hover:text-rose-500 transition-colors text-3xl font-light">&times;</button>
        </div>

        <div class="p-10 space-y-12">
            {{-- LANGKAH 1: OTORISASI --}}
            <div class="space-y-6">
                <div class="flex items-center gap-3">
                    <span class="w-8 h-8 rounded-full bg-[#002855] text-[#fcc000] flex items-center justify-center font-black text-xs shadow-lg">1</span>
                    <h4 class="text-xs font-black text-[#002855] uppercase tracking-[0.2em]">Otorisasi Dokumen Kurikulum</h4>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pl-11">
                    <div class="space-y-2">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Pilih Kurikulum Sumber *</label>
                        <select wire:model.live="kurikulum_id" class="w-full rounded-2xl border-slate-200 bg-slate-50 py-4 px-6 font-bold text-slate-700 focus:ring-[#fcc000] transition-all">
                            <option value="">-- Pilih Kurikulum Aktif --</option>
                            @foreach($kurikulumOptions as $ko) <option value="{{ $ko->id }}">{{ $ko->nama_kurikulum }}</option> @endforeach
                        </select>
                        @error('kurikulum_id') <span class="text-rose-500 text-[10px] font-bold mt-1 block">Wajib pilih kurikulum sumber.</span> @enderror
                    </div>

                    <div class="relative" x-data="{ open: false }">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Pilih Mata Kuliah *</label>
                        <input type="text" wire:model.live="searchMk" @focus="open = true" @click.away="open = false"
                            placeholder="{{ $selectedMkName ?: 'Ketik Nama atau Kode MK...' }}"
                            class="w-full rounded-2xl border-slate-200 py-4 px-6 text-sm font-bold shadow-sm @error('mata_kuliah_id') border-rose-500 @enderror {{ !$kurikulum_id ? 'bg-slate-100 cursor-not-allowed' : 'bg-white' }}"
                            {{ !$kurikulum_id ? 'disabled' : '' }}>
                        
                        @if(!empty($searchMk))
                        <div x-show="open" class="absolute z-50 w-full bg-white shadow-2xl rounded-2xl mt-3 border border-slate-100 overflow-hidden animate-in fade-in zoom-in-95">
                            @foreach($formMks as $mk)
                            <div wire:click="pilihMk('{{ $mk->id }}', '{{ $mk->nama_mk }}')" @click="open = false" class="px-8 py-5 hover:bg-indigo-50 cursor-pointer border-b border-slate-50 last:border-0 transition-colors">
                                <p class="text-sm font-black text-[#002855] uppercase">{{ $mk->nama_mk }}</p>
                                <p class="text-[10px] text-slate-400 font-bold uppercase mt-1">Semester {{ $mk->semester_paket }} &bull; {{ $mk->sks_default }} SKS</p>
                            </div>
                            @endforeach
                        </div>
                        @endif
                        @error('mata_kuliah_id') <span class="text-rose-500 text-[10px] font-bold mt-1 block">Wajib memilih mata kuliah.</span> @enderror
                    </div>
                </div>
            </div>

            {{-- LANGKAH 2: WAKTU & RUANG --}}
            <div class="space-y-6">
                <div class="flex items-center gap-3">
                    <span class="w-8 h-8 rounded-full bg-[#002855] text-[#fcc000] flex items-center justify-center font-black text-xs shadow-lg">2</span>
                    <h4 class="text-xs font-black text-[#002855] uppercase tracking-[0.2em]">Pengaturan Waktu & Lokasi (Format 24 Jam)</h4>
                </div>
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 pl-11">
                    {{-- HARI --}}
                    <div class="space-y-2">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Hari Perkuliahan *</label>
                        <select wire:model.live="hari" class="w-full rounded-2xl border-slate-200 bg-slate-50 py-4 px-6 font-bold text-slate-700">
                            <option value="">-- Pilih Hari --</option>
                            @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $h) <option value="{{ $h }}">{{ $h }}</option> @endforeach
                        </select>
                    </div>

                    {{-- WAKTU (HH:mm) --}}
                    <div class="lg:col-span-2 grid grid-cols-2 gap-4 bg-indigo-50/30 p-2 rounded-3xl border border-indigo-100">
                        <div class="space-y-1">
                            <label class="block text-[9px] font-black text-indigo-400 uppercase text-center tracking-widest">Jam Mulai (24H)</label>
                            <input type="text" wire:model.live="jam_mulai" placeholder="08:00" maxlength="5" class="w-full bg-white border-none rounded-2xl py-4 text-center text-xl font-black text-[#002855] focus:ring-2 focus:ring-indigo-400 shadow-sm placeholder-slate-200">
                        </div>
                        <div class="space-y-1">
                            <label class="block text-[9px] font-black text-indigo-400 uppercase text-center tracking-widest">Jam Selesai (24H)</label>
                            <input type="text" wire:model.live="jam_selesai" placeholder="10:30" maxlength="5" class="w-full bg-white border-none rounded-2xl py-4 text-center text-xl font-black text-[#002855] focus:ring-2 focus:ring-indigo-400 shadow-sm placeholder-slate-200">
                        </div>
                    </div>

                    {{-- RUANG & KELAS --}}
                    <div class="space-y-2">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Nama Ruang *</label>
                        <input type="text" wire:model.live="ruang" placeholder="Cth: B-102" class="w-full rounded-2xl border-slate-200 py-4 px-6 text-base font-black uppercase text-[#002855] @error('ruang') border-rose-500 @enderror">
                        @if($roomConflict)
                            <div class="mt-3 p-4 bg-rose-50 rounded-2xl border border-rose-100 animate-in slide-in-from-top-2">
                                <p class="text-[10px] font-black text-rose-600 uppercase tracking-tight leading-tight">
                                    🔴 Ruangan Sudah Terpakai!<br>
                                    <span class="text-rose-400 font-bold normal-case">Lawan: {{ $roomConflict['mk'] }} ({{ $roomConflict['kelas'] }}) jam {{ $roomConflict['waktu'] }}</span>
                                </p>
                            </div>
                        @endif
                    </div>

                    <div class="space-y-2 text-center">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Identitas Kelas *</label>
                        <input type="text" wire:model="nama_kelas" placeholder="Cth: TI-2A" class="w-full rounded-2xl border-slate-200 py-4 text-base font-black uppercase text-center text-[#002855]">
                    </div>

                    <div class="space-y-2 text-center">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Kuota Kursi</label>
                        <input type="number" wire:model="kuota_kelas" class="w-full rounded-2xl border-slate-200 py-4 text-base font-black text-center text-[#002855]">
                    </div>
                </div>
            </div>

            {{-- LANGKAH 3: DOSEN --}}
            <div class="space-y-6">
                <div class="flex items-center gap-3">
                    <span class="w-8 h-8 rounded-full bg-[#002855] text-[#fcc000] flex items-center justify-center font-black text-xs shadow-lg">3</span>
                    <h4 class="text-xs font-black text-[#002855] uppercase tracking-[0.2em]">Penugasan Dosen Pengampu</h4>
                </div>
                
                <div class="pl-11 relative" x-data="{ open: false }">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Cari & Pilih Dosen Utama *</label>
                    <input type="text" wire:model.live="searchDosen" @focus="open = true" @click.away="open = false"
                        placeholder="{{ $selectedDosenName ?: 'Cari Nama Dosen...' }}"
                        class="w-full rounded-2xl border-slate-200 py-4 px-6 text-sm font-bold @error('dosen_id') border-rose-500 @enderror">
                    
                    @if(!empty($searchDosen))
                    <div x-show="open" class="absolute z-50 w-[calc(100%-2.75rem)] bg-white shadow-2xl rounded-2xl mt-3 border border-slate-100 overflow-hidden animate-in fade-in">
                        @foreach($dosens as $d)
                        <div wire:click="pilihDosen('{{ $d->id }}', '{{ $d->person->nama_lengkap }}')" @click="open = false" class="px-8 py-4 hover:bg-amber-50 cursor-pointer border-b border-slate-50 last:border-0 transition-colors">
                            <p class="text-sm font-bold text-slate-700 uppercase">{{ $d->person->nama_lengkap }}</p>
                            <p class="text-[9px] font-mono text-slate-400 uppercase tracking-widest">NIDN: {{ $d->nidn }}</p>
                        </div>
                        @endforeach
                    </div>
                    @endif

                    @if($lecturerConflict)
                        <div class="mt-4 p-4 bg-rose-50 rounded-2xl border border-rose-100 animate-in slide-in-from-top-2">
                            <p class="text-[10px] font-black text-rose-600 uppercase tracking-tight leading-tight">
                                🔴 Dosen Sedang Mengajar!<br>
                                <span class="text-rose-400 font-bold normal-case">Jadwal Lain: {{ $lecturerConflict['mk'] }} di R.{{ $lecturerConflict['ruang'] }} jam {{ $lecturerConflict['waktu'] }}</span>
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- FOOTER ACTION --}}
            <div class="pt-10 border-t border-slate-100 flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="flex items-center gap-6">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full {{ $formStatus == 'red' ? 'bg-rose-500' : ($formStatus == 'green' ? 'bg-emerald-500' : 'bg-slate-300') }}"></div>
                        <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Status Kelayakan</span>
                    </div>
                    @if($curriculumNotice)
                        <span class="px-3 py-1 bg-amber-50 text-amber-600 text-[9px] font-black rounded-lg uppercase border border-amber-100">{{ $curriculumNotice }}</span>
                    @endif
                </div>

                <div class="flex items-center gap-4">
                    <button wire:click="resetForm" class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-slate-600 transition-colors">Batalkan Setup</button>
                    <button wire:click="save" 
                        {{ $formStatus == 'red' ? 'disabled' : '' }}
                        class="px-12 py-5 {{ $formStatus == 'red' ? 'bg-slate-100 text-slate-300 cursor-not-allowed shadow-none' : 'bg-[#002855] text-white shadow-2xl shadow-indigo-900/40 hover:scale-105 active:scale-95' }} rounded-[1.5rem] font-black text-xs uppercase tracking-[0.3em] transition-all">
                        {{ $jadwalId ? 'Simpan Perubahan' : 'Terbitkan Penawaran' }}
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
                        <th class="px-8 py-5 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Waktu & Lokasi</th>
                        <th class="px-8 py-5 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Mata Kuliah & Otorisasi</th>
                        <th class="px-8 py-5 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Pengampu</th>
                        <th class="px-8 py-5 text-right text-[10px] font-black text-slate-400 uppercase tracking-widest">Opsi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($jadwals as $j)
                    <tr class="hover:bg-indigo-50/20 transition-colors group">
                        <td class="px-8 py-6">
                            <div class="text-sm font-black text-[#002855] uppercase tracking-tighter">{{ $j->hari }}</div>
                            <div class="text-[11px] font-bold text-slate-400 mt-1">{{ substr($j->jam_mulai,0,5) }} - {{ substr($j->jam_selesai,0,5) }} WITA</div>
                            <div class="mt-2 inline-flex px-2 py-0.5 rounded-md bg-white border border-slate-100 text-[#002855] text-[9px] font-black uppercase tracking-widest shadow-sm">Ruang: {{ $j->ruang }}</div>
                        </td>
                        <td class="px-8 py-6">
                            <div class="text-sm font-black text-slate-700 leading-tight uppercase">{{ $j->mataKuliah->nama_mk }}</div>
                            <div class="flex items-center gap-2 mt-2">
                                <span class="text-[9px] font-black text-indigo-400 uppercase border border-indigo-100 px-1.5 py-0.5 rounded">Ref: {{ $j->kurikulum->nama_kurikulum ?? '-' }}</span>
                                <span class="text-[9px] font-black bg-[#002855] text-[#fcc000] px-2 py-0.5 rounded shadow-sm">{{ $j->mataKuliah->sks_default }} SKS</span>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-center font-black text-[#002855] text-xs">
                                    {{ substr($j->dosen->person->nama_lengkap, 0, 1) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-bold text-slate-700 truncate uppercase tracking-tight">{{ $j->dosen->person->nama_lengkap }}</p>
                                    <p class="text-[10px] font-mono text-slate-400 mt-0.5 tracking-widest">NIDN: {{ $j->dosen->nidn }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-6 text-right space-x-2">
                            <button wire:click="edit('{{ $j->id }}')" class="p-2.5 text-indigo-600 hover:bg-indigo-50 rounded-xl transition-all font-black text-[10px] uppercase tracking-widest">Edit</button>
                            <button class="p-2.5 text-rose-500 hover:bg-rose-50 rounded-xl transition-all font-black text-[10px] uppercase tracking-widest">Hapus</button>
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