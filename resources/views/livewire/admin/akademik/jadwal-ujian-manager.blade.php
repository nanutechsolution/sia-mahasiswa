<div class="space-y-6 max-w-[1600px] mx-auto p-4 md:p-8 animate-in fade-in duration-500">
    
    {{-- Header --}}
    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6">
        <div class="space-y-2">
            <h1 class="text-3xl font-black text-[#002855] tracking-tight flex items-center gap-3">
                <div class="w-12 h-12 bg-[#002855] rounded-2xl flex items-center justify-center text-[#fcc000] shadow-xl shadow-blue-900/20">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
                </div>
                Manajemen Jadwal Ujian
            </h1>
            <p class="text-slate-400 font-medium text-sm ml-1 uppercase tracking-widest italic">Penjadwalan UTS, UAS, dan Alokasi Pengawas</p>
        </div>
        
        @if(!$showForm)
        <button wire:click="$set('showForm', true)" class="px-8 py-4 bg-[#fcc000] text-[#002855] rounded-2xl font-black text-xs uppercase tracking-[0.2em] hover:bg-[#ffca28] hover:-translate-y-1 transition-all shadow-xl shadow-amber-500/20 flex items-center gap-3">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
            Buat Jadwal Ujian
        </button>
        @endif
    </div>

    @if($showForm)
    {{-- FORM CREATE / EDIT UJIAN --}}
    <div class="bg-white rounded-[3rem] shadow-2xl border border-slate-100 overflow-hidden animate-in slide-in-from-bottom-10 duration-500">
        <div class="grid grid-cols-1 lg:grid-cols-12">
            
            {{-- Left Side: Exam Config --}}
            <div class="lg:col-span-7 p-8 md:p-12 space-y-8 border-r border-slate-50">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-black text-[#002855] uppercase tracking-widest">Informasi Ujian</h2>
                    <button wire:click="resetForm" class="text-slate-400 hover:text-rose-500"><svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>

                <div class="space-y-4">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Mata Kuliah & Kelas</label>
                    <select wire:model="jadwal_kuliah_id" class="w-full rounded-2xl border-slate-200 bg-slate-50 py-4 px-5 text-sm font-bold text-[#002855] focus:ring-[#fcc000]" {{ $ujianId ? 'disabled' : '' }}>
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($jadwalKuliahOptions as $jk)
                            <option value="{{ $jk->id }}">{{ $jk->mataKuliah->nama_mk }} (Kelas: {{ $jk->nama_kelas }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Jenis Ujian</label>
                        <select wire:model="jenis_ujian" class="w-full rounded-2xl border-slate-200 bg-white py-4 px-5 text-sm font-bold text-indigo-700 focus:ring-[#fcc000]">
                            <option value="UTS">Ujian Tengah Semester (UTS)</option>
                            <option value="UAS">Ujian Akhir Semester (UAS)</option>
                            <option value="SUSULAN">Ujian Susulan</option>
                        </select>
                    </div>
                    <div class="space-y-4">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Metode Pelaksanaan</label>
                        <select wire:model="metode_ujian" class="w-full rounded-2xl border-slate-200 bg-white py-4 px-5 text-sm font-bold text-[#002855] focus:ring-[#fcc000]">
                            <option value="TERTULIS">Tertulis (Paper Based)</option>
                            <option value="CBT">Computer Based Test (CBT)</option>
                            <option value="PRAKTEK">Ujian Praktek</option>
                            <option value="TAKE_HOME">Take Home Project</option>
                        </select>
                    </div>
                </div>

                <div class="space-y-4">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Waktu & Tempat</label>
                    <div class="grid grid-cols-3 gap-4">
                        <input type="date" wire:model.live="tanggal_ujian" class="col-span-3 md:col-span-1 w-full rounded-2xl border-slate-200 bg-slate-50 py-3 px-4 font-bold text-sm">
                        <input type="time" wire:model.live="jam_mulai" class="w-full rounded-2xl border-slate-200 bg-slate-50 py-3 px-4 font-bold text-sm text-center">
                        <input type="time" wire:model.live="jam_selesai" class="w-full rounded-2xl border-slate-200 bg-slate-50 py-3 px-4 font-bold text-sm text-center">
                    </div>
                    <select wire:model.live="ruang_id" class="w-full mt-4 rounded-2xl border-slate-200 bg-white py-4 px-5 text-sm font-bold text-[#002855] focus:ring-[#fcc000]">
                        <option value="">-- Pilih Ruang Ujian --</option>
                        @foreach($ruangan as $r)
                            <option value="{{ $r->id }}">R. {{ $r->kode_ruang }} - {{ $r->nama_ruang }} (Kapasitas: {{ $r->kapasitas }})</option>
                        @endforeach
                    </select>

                    @if($roomConflict)
                        <div class="mt-3 p-4 bg-rose-50 border border-rose-100 rounded-2xl animate-pulse">
                            <p class="text-[10px] font-black text-rose-600 uppercase tracking-widest">⚠ Bentrok Ruangan!</p>
                            <p class="text-xs text-rose-500 font-bold mt-1">Digunakan untuk: {{ $roomConflict['mk'] }} jam {{ $roomConflict['waktu'] }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Right Side: Proctors & Action --}}
            <div class="lg:col-span-5 bg-slate-50/50 p-8 md:p-12 flex flex-col justify-between space-y-8">
                <div class="space-y-6">
                    <h2 class="text-xl font-black text-[#002855] uppercase tracking-widest">Pengawas Ujian</h2>
                    
                    <div class="relative" x-data="{ open: false }">
                        <input type="text" wire:model.live="searchPengawas" @focus="open = true" @click.away="open = false" placeholder="Cari Dosen/Staff Pengawas..." class="w-full rounded-2xl border-slate-200 py-3 px-5 text-sm font-bold focus:ring-[#fcc000]">
                        @if(!empty($calonPengawas))
                        <div x-show="open" class="absolute z-50 w-full bg-white shadow-2xl rounded-2xl mt-2 border border-slate-100 overflow-hidden">
                            @foreach($calonPengawas as $cp)
                                <div wire:click="tambahPengawas('{{ $cp->id }}', '{{ $cp->nama_lengkap }}')" @click="open = false" class="px-5 py-3 hover:bg-indigo-50 cursor-pointer border-b border-slate-50 transition-all text-xs font-bold text-[#002855] uppercase">
                                    {{ $cp->nama_lengkap }}
                                </div>
                            @endforeach
                        </div>
                        @endif
                    </div>

                    <div class="space-y-3">
                        @forelse($selectedPengawas as $sp)
                        <div class="flex items-center justify-between p-4 bg-white rounded-2xl border border-slate-200 shadow-sm group">
                            <div>
                                <p class="text-xs font-black text-[#002855] uppercase">{{ $sp['nama'] }}</p>
                                <select wire:change="setPeranPengawas('{{ $sp['id'] }}', $event.target.value)" class="mt-1 text-[9px] font-bold uppercase text-indigo-600 bg-indigo-50 border-none rounded py-0.5 pl-2 pr-6 focus:ring-0 cursor-pointer">
                                    <option value="UTAMA" {{ $sp['peran'] == 'UTAMA' ? 'selected' : '' }}>Pengawas Utama</option>
                                    <option value="PENDAMPING" {{ $sp['peran'] == 'PENDAMPING' ? 'selected' : '' }}>Pendamping</option>
                                </select>
                            </div>
                            <button wire:click="hapusPengawas('{{ $sp['id'] }}')" class="text-slate-300 hover:text-rose-500 transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg></button>
                        </div>
                        @empty
                        <div class="text-center py-6 border-2 border-dashed border-slate-200 rounded-3xl text-slate-400 text-[10px] font-bold uppercase tracking-widest">Belum ada pengawas ditugaskan</div>
                        @endforelse
                    </div>
                </div>

                <div class="pt-8">
                    <button wire:click="save" class="w-full py-5 rounded-3xl bg-[#002855] text-white font-black text-sm tracking-[0.3em] uppercase shadow-xl hover:-translate-y-1 hover:shadow-blue-900/30 transition-all disabled:opacity-50" {{ $roomConflict ? 'disabled' : '' }}>
                        Simpan Jadwal Ujian
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Filter Bar --}}
    @if(!$showForm)
    <div class="bg-white p-3 shadow-sm rounded-[2.5rem] border border-slate-200 grid grid-cols-1 md:grid-cols-4 gap-3">
        <div class="flex items-center bg-slate-50 rounded-[1.8rem] px-4 py-2 border border-slate-100">
            <span class="text-[10px] font-black text-slate-400 mr-3">SEM</span>
            <select wire:model.live="filterSemesterId" class="flex-1 bg-transparent border-none text-sm font-bold text-[#002855] focus:ring-0">
                @foreach($semesters as $s) <option value="{{ $s->id }}">{{ $s->nama_tahun }}</option> @endforeach
            </select>
        </div>
        <div class="flex items-center bg-slate-50 rounded-[1.8rem] px-4 py-2 border border-slate-100">
            <span class="text-[10px] font-black text-slate-400 mr-3">PRD</span>
            <select wire:model.live="filterProdiId" class="flex-1 bg-transparent border-none text-sm font-bold text-slate-700 focus:ring-0">
                @foreach($prodis as $p) <option value="{{ $p->id }}">{{ $p->nama_prodi }}</option> @endforeach
            </select>
        </div>
        <div class="flex items-center bg-slate-50 rounded-[1.8rem] px-4 py-2 border border-slate-100">
            <span class="text-[10px] font-black text-slate-400 mr-3">TIPE</span>
            <select wire:model.live="filterJenisUjian" class="flex-1 bg-transparent border-none text-sm font-bold text-indigo-700 focus:ring-0">
                <option value="">Semua Ujian</option>
                <option value="UTS">UTS</option>
                <option value="UAS">UAS</option>
                <option value="SUSULAN">Susulan</option>
            </select>
        </div>
        <div class="flex items-center bg-white rounded-[1.8rem] px-4 py-2 border border-slate-200 focus-within:ring-2 focus-within:ring-indigo-100">
            <svg class="h-4 w-4 text-slate-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari MK..." class="flex-1 border-none text-xs font-bold focus:ring-0">
        </div>
    </div>

    {{-- Main Table --}}
    <div class="bg-white rounded-[3rem] shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50/50">
                    <tr>
                        <th class="px-8 py-6 text-left text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Tipe & Waktu</th>
                        <th class="px-6 py-6 text-left text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Mata Kuliah / Ruang</th>
                        <th class="px-6 py-6 text-left text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Pengawas</th>
                        <th class="px-8 py-6 text-right text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($ujians as $u)
                    <tr class="hover:bg-slate-50/80 transition-all group">
                        <td class="px-8 py-6 align-top">
                            <div class="flex items-center gap-4">
                                <div class="w-14 h-14 rounded-2xl flex flex-col items-center justify-center text-white {{ $u->jenis_ujian == 'UTS' ? 'bg-indigo-500' : ($u->jenis_ujian == 'UAS' ? 'bg-emerald-500' : 'bg-amber-500') }} shadow-lg shadow-slate-200">
                                    <span class="text-xs font-black tracking-widest">{{ $u->jenis_ujian }}</span>
                                </div>
                                <div>
                                    <p class="text-xs font-black text-slate-800 uppercase">{{ \Carbon\Carbon::parse($u->tanggal_ujian)->isoFormat('D MMM Y') }}</p>
                                    <p class="text-[10px] font-bold text-slate-400 mt-1 uppercase">{{ substr($u->jam_mulai, 0, 5) }} - {{ substr($u->jam_selesai, 0, 5) }} WITA</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-6 align-top">
                            <h4 class="text-sm font-black text-[#002855] uppercase tracking-tight">{{ $u->jadwalKuliah->mataKuliah->nama_mk }}</h4>
                            <div class="flex items-center gap-3 mt-2">
                                <span class="px-2 py-0.5 bg-slate-100 text-slate-600 text-[9px] font-bold rounded uppercase">R. {{ $u->ruang->kode_ruang ?? 'TBA' }}</span>
                                <span class="px-2 py-0.5 bg-sky-50 text-sky-600 text-[9px] font-bold rounded uppercase">{{ $u->metode_ujian }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-6 align-top">
                            <div class="flex flex-col gap-1.5">
                                @forelse($u->pengawas as $p)
                                    <div class="flex items-center gap-2 text-[10px] font-bold text-slate-600 uppercase">
                                        <div class="w-1.5 h-1.5 rounded-full {{ $p->peran == 'UTAMA' ? 'bg-rose-400' : 'bg-slate-300' }}"></div>
                                        {{ $p->person->nama_lengkap }}
                                    </div>
                                @empty
                                    <span class="text-[9px] text-rose-400 italic font-bold">Belum ada pengawas</span>
                                @endforelse
                            </div>
                        </td>
                        <td class="px-8 py-6 text-right align-top">
                            <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <button wire:click="edit('{{ $u->id }}')" class="p-2.5 text-indigo-600 bg-indigo-50 hover:bg-indigo-100 rounded-xl transition-colors" title="Edit"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg></button>
                                <button wire:click="delete('{{ $u->id }}')" wire:confirm="Hapus jadwal ujian ini?" class="p-2.5 text-rose-500 bg-rose-50 hover:bg-rose-100 rounded-xl transition-colors" title="Hapus"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg></button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-24 text-center">
                            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 grayscale opacity-40 text-4xl">📝</div>
                            <p class="text-xs font-black text-slate-400 uppercase tracking-[0.2em]">Jadwal Ujian Belum Dibuat</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-8 py-5 border-t border-slate-100 bg-slate-50/50">
            {{ $ujians->links() }}
        </div>
    </div>
    @endif

    {{-- SweetAlert2 Listener --}}
    @script
    <script>
        $wire.on('swal:success', data => { alert(data[0].text); });
        $wire.on('swal:error', data => { alert(data[0].text); });
    </script>
    @endscript
</div>