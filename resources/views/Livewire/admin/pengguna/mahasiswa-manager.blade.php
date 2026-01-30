<div class="space-y-8">
    {{-- Header & Toolbar --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-[#002855]">Data Mahasiswa</h1>
            <p class="text-slate-500 text-sm mt-1">Manajemen biodata, status dispensasi, dan akun login mahasiswa aktif.</p>
        </div>

        @if(!$showForm)
        <button wire:click="create"
            class="inline-flex items-center px-5 py-2.5 bg-[#fcc000] text-[#002855] rounded-xl font-black text-sm shadow-lg shadow-orange-500/20 hover:bg-[#fbbf24] hover:scale-105 transition-all">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" />
            </svg>
            Input Mahasiswa Baru
        </button>
        @endif
    </div>

    {{-- Filters --}}
    <div class="bg-white p-5 shadow-sm rounded-2xl border border-slate-200 grid grid-cols-1 md:grid-cols-3 gap-6">
        <div>
            <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Program Studi</label>
            <div class="relative">
                <select wire:model.live="filterProdiId" class="block w-full rounded-xl border-slate-200 bg-slate-50 py-3 pl-4 pr-10 text-sm focus:border-[#002855] focus:ring-[#002855] transition-shadow outline-none appearance-none font-bold text-slate-700">
                    <option value="">Semua Prodi</option>
                    @foreach($prodis as $p)
                    <option value="{{ $p->id }}">{{ $p->jenjang }} - {{ $p->nama_prodi }}</option>
                    @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-500">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                </div>
            </div>
        </div>
        <div>
            <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Angkatan</label>
            <div class="relative">
                <select wire:model.live="filterAngkatan" class="block w-full rounded-xl border-slate-200 bg-slate-50 py-3 pl-4 pr-10 text-sm focus:border-[#002855] focus:ring-[#002855] transition-shadow outline-none appearance-none font-bold text-slate-700">
                    <option value="">Semua Angkatan</option>
                    @foreach($angkatans as $akt)
                    <option value="{{ $akt->id_tahun }}">{{ $akt->id_tahun }}</option>
                    @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-500">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                </div>
            </div>
        </div>
        <div>
            <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Cari Data</label>
            <div class="relative">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="NIM atau Nama..." class="block w-full rounded-xl border-slate-200 bg-slate-50 py-3 pl-10 pr-4 text-sm focus:border-[#002855] focus:ring-[#002855] transition-shadow outline-none font-bold text-slate-700">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center px-3 text-slate-400">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Alert Success --}}
    @if (session()->has('success'))
    <div class="bg-emerald-50 border border-emerald-100 p-4 rounded-xl text-emerald-800 text-sm flex items-center shadow-sm animate-in fade-in slide-in-from-top-2">
        <svg class="w-5 h-5 mr-3 text-emerald-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
        </svg>
        <span class="font-bold">{{ session('success') }}</span>
    </div>
    @endif

    {{-- Form Section --}}
    @if($showForm)
    <div class="bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden animate-in zoom-in-95 duration-200">
        <div class="px-8 py-5 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
            <h3 class="text-sm font-bold text-[#002855] uppercase tracking-wider flex items-center gap-2">
                @if($editMode)
                    <svg class="w-5 h-5 text-[#fcc000]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                    Edit Biodata Mahasiswa
                @else
                    <svg class="w-5 h-5 text-[#002855]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" /></svg>
                    Registrasi Mahasiswa Baru
                @endif
            </h3>
            <button wire:click="batal" class="text-slate-400 hover:text-slate-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>
        
        <div class="p-8 space-y-8">
            {{-- Group 1: Identitas --}}
            <div class="space-y-4">
                <h4 class="text-xs font-bold text-[#002855] uppercase border-l-4 border-[#fcc000] pl-3 tracking-widest">1. Identitas & Akun</h4>
                <div class="grid grid-cols-1 md:grid-cols-6 gap-6 bg-slate-50 p-6 rounded-xl border border-slate-100">
                    <div class="md:col-span-2">
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-2">NIM *</label>
                        <input type="text" wire:model="nim" class="block w-full rounded-lg border-slate-300 focus:border-[#002855] focus:ring-[#002855] text-sm" placeholder="24xxxxx">
                        @error('nim') <span class="text-rose-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div class="md:col-span-4">
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-2">Nama Lengkap *</label>
                        <input type="text" wire:model="nama_lengkap" class="block w-full rounded-lg border-slate-300 focus:border-[#002855] focus:ring-[#002855] text-sm" placeholder="Nama Mahasiswa">
                        @error('nama_lengkap') <span class="text-rose-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    
                    <div class="md:col-span-3">
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-2">Nomor HP / WA</label>
                        <input type="text" wire:model="nomor_hp" class="block w-full rounded-lg border-slate-300 focus:border-[#002855] focus:ring-[#002855] text-sm">
                    </div>
                    <div class="md:col-span-3">
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-2">
                            {{ $editMode ? 'Reset Password (Opsional)' : 'Password Awal *' }}
                        </label>
                        <input type="password" wire:model="password_baru" class="block w-full rounded-lg border-slate-300 focus:border-[#002855] focus:ring-[#002855] text-sm" placeholder="Min. 6 Karakter">
                        @error('password_baru') <span class="text-rose-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- Group 2: Akademik --}}
            <div class="space-y-4">
                <h4 class="text-xs font-bold text-[#002855] uppercase border-l-4 border-[#fcc000] pl-3 tracking-widest">2. Informasi Akademik</h4>
                <div class="grid grid-cols-1 md:grid-cols-6 gap-6 bg-slate-50 p-6 rounded-xl border border-slate-100">
                    <div class="md:col-span-2">
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-2">Tahun Angkatan *</label>
                        <select wire:model="angkatan_id" class="block w-full rounded-lg border-slate-300 focus:border-[#002855] focus:ring-[#002855] text-sm">
                            @foreach($angkatans as $akt)
                                <option value="{{ $akt->id_tahun }}">{{ $akt->id_tahun }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-2">Program Studi *</label>
                        <select wire:model="prodi_id" class="block w-full rounded-lg border-slate-300 focus:border-[#002855] focus:ring-[#002855] text-sm">
                            <option value="">Pilih Prodi</option>
                            @foreach($prodis as $p)
                                <option value="{{ $p->id }}">{{ $p->nama_prodi }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-2">Program Kelas *</label>
                        <select wire:model="program_kelas_id" class="block w-full rounded-lg border-slate-300 focus:border-[#002855] focus:ring-[#002855] text-sm">
                            <option value="">Pilih Kelas</option>
                            @foreach($programKelasList as $pk)
                                <option value="{{ $pk->id }}">{{ $pk->nama_program }}</option>
                            @endforeach
                        </select>
                        @error('program_kelas_id') <span class="text-rose-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- Dosen Wali --}}
                    <div class="md:col-span-6 bg-white p-4 rounded-lg border border-slate-200">
                        <label class="block text-[11px] font-bold text-[#002855] uppercase tracking-widest mb-2">Dosen Wali (PA)</label>
                        <select wire:model="dosen_wali_id" class="block w-full rounded-lg border-slate-300 focus:border-[#002855] focus:ring-[#002855] text-sm">
                            <option value="">-- Belum Ditentukan --</option>
                            @foreach($dosens as $dosen)
                                <option value="{{ $dosen->id }}">{{ $dosen->nama_lengkap_gelar }}</option>
                            @endforeach
                        </select>
                        <p class="text-[10px] text-slate-400 mt-1 italic">*Wajib diisi agar mahasiswa bisa mengisi KRS.</p>
                    </div>
                </div>
            </div>

            {{-- Group 3: Dispensasi --}}
            <div class="bg-amber-50 rounded-xl p-6 border border-amber-100 flex flex-col gap-3">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-[#fcc000]/20 rounded-lg text-[#002855]">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <h4 class="text-sm font-bold text-[#002855] uppercase tracking-tight">Dispensasi Keuangan</h4>
                </div>
                
                <label class="flex items-start gap-3 cursor-pointer bg-white p-4 rounded-lg border border-amber-200 hover:border-amber-300 transition-colors">
                    <input type="checkbox" wire:model="bebas_keuangan" class="mt-1 h-5 w-5 text-[#002855] border-gray-300 rounded focus:ring-[#fcc000]">
                    <div>
                        <span class="block text-sm font-bold text-gray-900">Aktifkan Dispensasi (Bebas Syarat Bayar)</span>
                        <span class="block text-xs text-gray-500 mt-1">Mahasiswa dapat mengisi KRS meskipun pembayaran belum mencapai target minimal. Gunakan untuk kasus beasiswa atau perjanjian khusus.</span>
                    </div>
                </label>
            </div>

            <div class="pt-6 border-t border-slate-100 flex justify-end gap-3">
                <button wire:click="batal" class="px-6 py-2.5 text-sm font-bold text-slate-500 hover:text-slate-700 hover:bg-slate-100 rounded-xl transition-all">Batalkan</button>
                
                <button wire:click="save" 
                    wire:loading.attr="disabled"
                    class="px-8 py-2.5 bg-[#002855] text-white rounded-xl text-sm font-bold shadow-lg shadow-indigo-200 hover:bg-[#001a38] hover:scale-105 transition-all flex items-center disabled:opacity-50 disabled:cursor-not-allowed">
                    <span wire:loading.remove wire:target="save">Simpan Data</span>
                    <span wire:loading wire:target="save">Menyimpan...</span>
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- Data Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        {{-- Table Header Info --}}
        <div class="px-6 py-3 border-b border-slate-100 bg-slate-50/80 flex items-center justify-between">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Total Data: {{ $mahasiswas->total() }}</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#002855] border-b border-[#001a38] text-white">
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
                            <span class="font-black text-[#002855] text-sm">{{ $mhs->nim }}</span>
                            @if($mhs->data_tambahan['bebas_keuangan'] ?? false)
                                <div class="mt-1">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-black bg-[#fcc000] text-[#002855]">
                                        DISPENSASI
                                    </span>
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 align-top">
                            <div class="text-sm font-bold text-slate-800">{{ $mhs->nama_lengkap }}</div>
                            <div class="text-xs text-slate-400 mt-0.5 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                User: {{ $mhs->user->username ?? 'No User' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 align-top">
                            <div class="text-xs font-bold text-slate-600 uppercase">{{ $mhs->prodi->nama_prodi }}</div>
                            <div class="text-[10px] text-slate-400 font-bold mt-1">Angkatan {{ $mhs->angkatan_id }}</div>
                            <div class="mt-2 flex items-center text-xs text-[#002855] font-medium bg-indigo-50 px-2 py-1 rounded w-fit">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                                PA: {{ $mhs->dosenWali->nama_lengkap_gelar ?? '-' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 align-top text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide bg-sky-100 text-sky-700 border border-sky-200">
                                {{ $mhs->programKelas->nama_program }}
                            </span>
                        </td>
                        <td class="px-6 py-4 align-top text-right">
                            <div class="flex justify-end gap-2 opacity-60 group-hover:opacity-100 transition-opacity">
                                <button wire:click="edit('{{ $mhs->id }}')" class="p-2 text-[#002855] hover:bg-[#002855]/10 rounded-lg transition-colors" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                </button>
                                <button wire:click="delete('{{ $mhs->id }}')" wire:confirm="Hapus mahasiswa ini? User login juga akan dihapus." class="p-2 text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="bg-slate-50 p-4 rounded-full mb-3">
                                    <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                                </div>
                                <p class="text-slate-500 font-medium">Tidak ada data mahasiswa ditemukan.</p>
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