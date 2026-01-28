<div>
    <x-slot name="title">Manajemen Dosen</x-slot>
    <x-slot name="header">Data Dosen Pengajar</x-slot>
    <div class="space-y-6">
        {{-- Top Toolbar --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <p class="text-slate-500 text-sm">Kelola biodata, homebase prodi, dan akses akun untuk seluruh dosen pengajar.</p>
            </div>

            @if(!$showForm)
            <button wire:click="create"
                class="inline-flex items-center px-6 py-3 bg-unmaris-yellow text-unmaris-blue rounded-xl font-bold text-sm shadow-lg shadow-unmaris-yellow/20 hover:scale-105 transition-all">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" />
                </svg>
                Registrasi Dosen Baru
            </button>
            @endif
        </div>

        {{-- Filter & Search Card --}}
        <div class="bg-white p-6 shadow-sm rounded-2xl border border-slate-200 grid grid-cols-1 md:grid-cols-12 gap-6 items-end">
            <div class="md:col-span-4">
                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2">Homebase Program Studi</label>
                <select wire:model.live="filterProdiId" class="block w-full rounded-xl border-slate-200 bg-slate-50 py-3 px-4 focus:bg-white focus:border-unmaris-blue focus:ring-4 focus:ring-unmaris-blue/5 text-sm transition-all outline-none">
                    <option value="">Semua Program Studi</option>
                    @foreach($prodis as $p)
                    <option value="{{ $p->id }}">{{ $p->jenjang }} - {{ $p->nama_prodi }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-8">
                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2">Cari Nama atau NIDN</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Ketik nama dosen atau nomor induk dosen..." class="block w-full pl-12 pr-4 py-3 rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:border-unmaris-blue focus:ring-4 focus:ring-unmaris-blue/5 text-sm transition-all outline-none">
                </div>
            </div>
        </div>

        @if (session()->has('success'))
        <div class="bg-emerald-50 border border-emerald-100 p-4 rounded-xl text-emerald-800 text-sm flex items-center animate-in fade-in duration-300">
            <svg class="w-5 h-5 mr-3 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            {{ session('success') }}
        </div>
        @endif

        {{-- Form Section --}}
        @if($showForm)
        <div class="bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden animate-in slide-in-from-top-4 duration-500">
            <div class="px-8 py-5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                <h3 class="text-sm font-bold text-unmaris-blue uppercase tracking-wider">
                    {{ $editMode ? 'Edit Biodata Dosen' : 'Registrasi Dosen Baru' }}
                </h3>
                <span class="px-3 py-1 bg-unmaris-yellow/20 text-unmaris-gold text-[10px] font-bold rounded-lg uppercase tracking-widest">Dosen & Staff</span>
            </div>

            <div class="p-8 lg:p-10 space-y-12">
                {{-- Identitas --}}
                <div class="space-y-6">
                    <h4 class="text-xs font-bold text-unmaris-blue uppercase border-l-4 border-unmaris-yellow pl-4 tracking-widest">Informasi Identitas & Gelar</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-2">NIDN / NIP (Opsional)</label>
                            <input type="text" wire:model="nidn" class="block w-full rounded-xl border-slate-200 bg-slate-50 py-3.5 px-4 focus:bg-white focus:border-unmaris-blue text-sm transition-all outline-none" placeholder="Masukkan NIDN">
                            @error('nidn') <span class="text-red-500 text-[11px] font-bold mt-2 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-2">Nama Lengkap & Gelar Akademik *</label>
                            <input type="text" wire:model="nama_lengkap_gelar" class="block w-full rounded-xl border-slate-200 bg-slate-50 py-3.5 px-4 focus:bg-white focus:border-unmaris-blue text-sm transition-all outline-none" placeholder="Contoh: Dr. Nama Lengkap, S.T., M.T.">
                            @error('nama_lengkap_gelar') <span class="text-red-500 text-[11px] font-bold mt-2 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                {{-- Penugasan --}}
                <div class="space-y-6">
                    <h4 class="text-xs font-bold text-unmaris-blue uppercase border-l-4 border-unmaris-yellow pl-4 tracking-widest">Homebase & Penugasan</h4>
                    <div class="grid grid-cols-1 gap-8">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-2">Program Studi Homebase *</label>
                            <select wire:model="homebase_prodi_id" class="block w-full rounded-xl border-slate-200 bg-slate-50 py-3.5 px-4 focus:bg-white focus:border-unmaris-blue text-sm transition-all outline-none">
                                <option value="">Pilih Program Studi Homebase</option>
                                @foreach($prodis as $p)
                                <option value="{{ $p->id }}">{{ $p->nama_prodi }}</option>
                                @endforeach
                            </select>
                            @error('homebase_prodi_id') <span class="text-red-500 text-[11px] font-bold mt-2 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                {{-- Keamanan --}}
                <div class="space-y-6">
                    <h4 class="text-xs font-bold text-unmaris-blue uppercase border-l-4 border-unmaris-yellow pl-4 tracking-widest">Akses Akun SIAKAD</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="md:col-span-2">
                            <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-2">
                                {{ $editMode ? 'Reset Kata Sandi (Isi jika ingin diubah)' : 'Kata Sandi Awal Login *' }}
                            </label>
                            <input type="password" wire:model="password_baru" class="block w-full rounded-xl border-slate-200 bg-slate-50 py-3.5 px-4 focus:bg-white focus:border-unmaris-blue text-sm transition-all outline-none" placeholder="Minimal 6 karakter">
                            @error('password_baru') <span class="text-red-500 text-[11px] font-bold mt-2 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <div class="pt-10 border-t border-slate-100 flex justify-end gap-4">
                    <button wire:click="batal" class="px-8 py-3 text-sm font-bold text-slate-500 hover:text-slate-700 hover:bg-slate-100 rounded-xl transition-all">Batalkan</button>
                    <button wire:click="save" class="px-12 py-3 bg-unmaris-blue text-white rounded-xl text-sm font-bold shadow-xl shadow-unmaris-blue/20 hover:scale-105 transition-all">Simpan Data Dosen</button>
                </div>
            </div>
        </div>
        @endif

        {{-- Table Section --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-100">
                            <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Nama Dosen</th>
                            <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Identitas & Akun</th>
                            <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Homebase Prodi</th>
                            <th class="px-8 py-5 text-center text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Status</th>
                            <th class="px-8 py-5 text-right text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Opsi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($dosens as $dosen)
                        <tr class="hover:bg-slate-50/50 transition-colors group">
                            <td class="px-8 py-6">
                                <div class="flex items-center space-x-4">
                                    <div class="flex-shrink-0 w-11 h-11 bg-unmaris-blue text-unmaris-yellow rounded-2xl flex items-center justify-center font-black text-sm shadow-sm">
                                        {{ substr($dosen->nama_lengkap_gelar, 0, 1) }}
                                    </div>
                                    <div class="text-sm font-bold text-slate-800 leading-tight">
                                        {{ $dosen->nama_lengkap_gelar }}
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <div class="text-[13px] font-semibold text-slate-600">NIDN: {{ $dosen->nidn ?? 'N/A' }}</div>
                                <div class="text-[11px] font-bold text-indigo-500 uppercase mt-0.5">User: {{ $dosen->user->username ?? '-' }}</div>
                            </td>
                            <td class="px-8 py-6">
                                <div class="inline-flex items-center text-[13px] text-slate-600 font-medium">
                                    <div class="w-1.5 h-1.5 rounded-full bg-unmaris-gold mr-2.5"></div>
                                    {{ \App\Domains\Core\Models\Prodi::find($dosen->homebase_prodi_id)->nama_prodi ?? 'Unknown' }}
                                </div>
                            </td>
                            <td class="px-8 py-6 text-center">
                                <span class="px-3 py-1 rounded-xl text-[10px] font-black uppercase tracking-widest border
                                {{ $dosen->is_active ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : 'bg-rose-50 text-rose-600 border-rose-100' }}">
                                    {{ $dosen->is_active ? 'Aktif' : 'Non-Aktif' }}
                                </span>
                            </td>
                            <td class="px-8 py-6 text-right">
                                <div class="flex justify-end space-x-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button wire:click="edit('{{ $dosen->id }}')" class="p-2.5 text-slate-400 hover:text-unmaris-blue hover:bg-unmaris-blue/5 rounded-xl transition-all shadow-sm">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button wire:click="delete('{{ $dosen->id }}')" wire:confirm="Hapus dosen ini secara permanen?" class="p-2.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition-all shadow-sm">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-8 py-16 text-center">
                                <div class="max-w-xs mx-auto text-slate-400">
                                    <svg class="w-12 h-12 mx-auto mb-4 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2m9-10a4 4 0 11-8 0 4 4 0 018 0zm4.5 3.5l2 2 4-4" />
                                    </svg>
                                    <p class="font-bold italic">Data dosen tidak ditemukan</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-8 py-5 bg-slate-50/50 border-t border-slate-100">
                {{ $dosens->links() }}
            </div>
        </div>
    </div>


</div>