<div class="space-y-8">
    {{-- Header & Toolbar --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-[#002855]">Data Dosen</h1>
            <p class="text-slate-500 text-sm mt-1">Manajemen biodata, NIDN, jabatan, dan akun login tenaga pengajar.</p>
        </div>

        @if(!$showForm)
        <div class="flex gap-2">
            <button wire:click="openImport" class="inline-flex items-center px-4 py-2.5 bg-white border border-slate-300 text-slate-700 rounded-xl font-bold text-sm shadow-sm hover:bg-slate-50 transition-all">
                <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                </svg>
                Import CSV
            </button>
            <button wire:click="create"
                class="inline-flex items-center px-5 py-2.5 bg-[#fcc000] text-[#002855] rounded-xl font-black text-sm shadow-lg shadow-orange-500/20 hover:bg-[#fbbf24] hover:scale-105 transition-all">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" />
                </svg>
                Input Dosen Baru
            </button>
        </div>
        @endif
    </div>

    {{-- Alert Messages --}}
    @if (session()->has('success'))
    <div class="p-4 bg-emerald-50 border border-emerald-100 text-emerald-800 rounded-xl text-sm font-bold flex items-center shadow-sm animate-in fade-in slide-in-from-top-2">
        <svg class="w-5 h-5 mr-3 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
        </svg>
        {{ session('success') }}
    </div>
    @endif
    @if (session()->has('error'))
    <div class="p-4 bg-rose-50 border border-rose-100 text-rose-800 rounded-xl text-sm font-bold shadow-sm animate-in fade-in">
        {{ session('error') }}
    </div>
    @endif

    {{-- IMPORT MODAL --}}
    @if($showImportModal)
    <div class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 animate-in fade-in duration-200">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-lg overflow-hidden border border-white/20">
            <div class="bg-[#002855] px-8 py-6 text-white">
                <h3 class="text-lg font-black uppercase tracking-widest leading-none">Import Data Dosen</h3>
                <p class="text-[10px] font-bold uppercase opacity-60 mt-2">Format Laporan Penugasan Dosen (PDDIKTI)</p>
            </div>

            <div class="p-8 space-y-6">
                <div class="bg-slate-50 p-4 rounded-xl border border-slate-100 text-sm text-slate-600">
                    <p class="font-bold mb-2 text-[#002855]">Format Wajib CSV:</p>
                    <code class="block bg-white p-3 rounded-lg border border-slate-200 text-[10px] text-indigo-600 font-mono mb-4 break-all leading-relaxed shadow-sm">
                        No, NIDN, NUPTK, Nama, Program Studi, L/P, "Tempat,Tanggal Lahir", Agama
                    </code>
                    <button wire:click="downloadTemplate" class="mt-3 text-xs font-bold text-[#002855] hover:underline flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Download Template Contoh
                    </button>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-2">Upload File CSV</label>
                    <input type="file" wire:model="fileImport" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-[#002855] file:text-white hover:file:bg-[#001a38] transition-colors cursor-pointer bg-slate-50 rounded-lg border border-slate-200">
                    <div wire:loading wire:target="fileImport" class="text-xs text-indigo-600 mt-2 font-bold animate-pulse">Sedang mengupload...</div>
                    @error('fileImport') <span class="text-rose-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="pt-4 flex justify-end gap-3">
                    <button wire:click="batal" class="px-6 py-2.5 text-sm font-bold text-slate-400 hover:text-slate-600 transition-colors">Batal</button>
                    <button wire:click="processImport" wire:loading.attr="disabled" class="bg-emerald-600 text-white px-6 py-2.5 rounded-xl font-bold text-sm shadow-md hover:bg-emerald-700 transition-all flex items-center disabled:opacity-50">
                        <span wire:loading.remove wire:target="processImport">Mulai Import</span>
                        <span wire:loading wire:target="processImport">Memproses...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Form Section (Create/Edit) --}}
    @if($showForm)
    <div class="bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden animate-in zoom-in-95 duration-200">
        <div class="px-8 py-5 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
            <h3 class="text-sm font-bold text-[#002855] uppercase tracking-wider flex items-center gap-2">
                @if($editMode) Edit Data Dosen @else Registrasi Dosen Baru @endif
            </h3>
            <button wire:click="batal" class="text-slate-400 hover:text-slate-600 transition-colors">&times;</button>
        </div>

        <div class="p-8 space-y-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Group 1: Identitas -->
                <div class="space-y-5">
                    <h4 class="text-xs font-bold text-[#002855] uppercase border-l-4 border-[#fcc000] pl-3 tracking-widest">1. Identitas Personil</h4>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Nama Lengkap (Tanpa Gelar) *</label>
                        <input type="text" wire:model="nama_lengkap" class="block w-full rounded-lg border-slate-300 bg-slate-50 p-2.5 focus:border-[#002855] focus:ring-[#002855] text-sm font-bold placeholder-slate-400 shadow-sm" placeholder="Contoh: Budi Santoso">
                        @error('nama_lengkap') <span class="text-rose-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Email</label>
                        <input type="email" wire:model="email" class="block w-full rounded-lg border-slate-300 bg-slate-50 p-2.5 focus:border-[#002855] focus:ring-[#002855] text-sm shadow-sm" placeholder="email@example.com">
                        @error('email') <span class="text-rose-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Nomor HP</label>
                        <input type="text" wire:model="no_hp" class="block w-full rounded-lg border-slate-300 bg-slate-50 p-2.5 focus:border-[#002855] focus:ring-[#002855] text-sm shadow-sm" placeholder="08xxxxxxxxxx">
                    </div>
                </div>

                <!-- Group 2: Akademik -->
                <div class="space-y-5">
                    <h4 class="text-xs font-bold text-[#002855] uppercase border-l-4 border-[#fcc000] pl-3 tracking-widest">2. Info Akademik & Akun</h4>

                    <div class="grid grid-cols-2 gap-5">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Status Kepegawaian</label>
                            <select wire:model.live="jenis_dosen" class="block w-full rounded-lg border-slate-300 bg-white p-2.5 text-sm font-bold text-slate-700 focus:border-[#002855] focus:ring-[#002855] shadow-sm">
                                <option value="TETAP">Dosen Tetap</option>
                                <option value="LB">Dosen Luar Biasa (LB)</option>
                                <option value="PRAKTISI">Dosen Praktisi</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Homebase Prodi *</label>
                            <select wire:model="homebase_prodi_id" class="block w-full rounded-lg border-slate-300 bg-white p-2.5 text-sm font-bold text-slate-700 focus:border-[#002855] focus:ring-[#002855] shadow-sm">
                                <option value="">Pilih Prodi</option>
                                @foreach($prodis as $p)
                                <option value="{{ $p->id }}">{{ $p->jenjang }} - {{ $p->nama_prodi }}</option>
                                @endforeach
                            </select>
                            @error('homebase_prodi_id') <span class="text-rose-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- Form Asal Institusi (Muncul jika bukan Dosen Tetap) --}}
                    @if($jenis_dosen != 'TETAP')
                    <div class="bg-indigo-50 p-4 rounded-xl border border-indigo-100 animate-in fade-in">
                        <label class="block text-[10px] font-bold text-indigo-700 uppercase tracking-widest mb-2">Asal Institusi / Perusahaan</label>
                        <input type="text" wire:model="asal_institusi" class="block w-full rounded-lg border-indigo-200 bg-white p-2.5 focus:border-[#002855] focus:ring-[#002855] text-sm" placeholder="Contoh: Universitas Indonesia / PT. Telkom">
                    </div>
                    @endif

                    <div class="grid grid-cols-2 gap-5">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">NIDN</label>
                            <input type="text" wire:model="nidn" class="block w-full rounded-lg border-slate-300 bg-white p-2.5 text-sm font-bold shadow-sm" placeholder="Nomor NIDN">
                            @error('nidn') <span class="text-rose-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">NUPTK</label>
                            <input type="text" wire:model="nuptk" class="block w-full rounded-lg border-slate-300 bg-white p-2.5 text-sm font-bold shadow-sm" placeholder="Nomor NUPTK">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">
                            {{ $editMode ? 'Reset Password (Opsional)' : 'Password Awal' }}
                        </label>
                        <input type="password" wire:model="password_baru" class="block w-full rounded-lg border-slate-300 bg-white p-2.5 text-sm shadow-sm" placeholder="Min. 6 Karakter">
                        @error('password_baru') <span class="text-rose-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="pt-2">
                        <label class="inline-flex items-center cursor-pointer group">
                            <input type="checkbox" wire:model="is_active" class="rounded border-slate-300 text-[#002855] h-5 w-5 focus:ring-[#fcc000] cursor-pointer">
                            <span class="ml-3 text-sm font-bold text-slate-700 group-hover:text-[#002855] transition-colors">Status Aktif (Bisa Login)</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="pt-6 border-t border-slate-100 flex justify-end gap-3">
                <button wire:click="batal" class="px-6 py-2.5 text-sm font-bold text-slate-500 hover:text-slate-700 transition-colors">Batalkan</button>
                <button wire:click="save" class="px-8 py-2.5 bg-[#002855] text-white rounded-xl text-sm font-bold shadow-lg shadow-indigo-200 hover:bg-[#001a38] hover:scale-105 transition-all flex items-center">
                    <span wire:loading.remove wire:target="save">Simpan Data</span>
                    <span wire:loading wire:target="save">Menyimpan...</span>
                </button>
            </div>
        </div>
    </div>
    @endif

    <!-- Tabel Data (Filter & Table) -->
    <div class="bg-white p-5 shadow-sm rounded-2xl border border-slate-200 grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Filter Homebase Prodi</label>
            <div class="relative">
                <select wire:model.live="filterProdiId" class="block w-full rounded-xl border-slate-200 bg-white text-slate-900 py-2.5 pl-4 pr-10 text-sm focus:border-[#002855] focus:ring-[#002855] transition-shadow outline-none appearance-none font-bold">
                    <option value="">Semua Prodi</option>
                    @foreach($prodis as $p)
                    <option value="{{ $p->id }}">{{ $p->jenjang }} - {{ $p->nama_prodi }}</option>
                    @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-500">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                </div>
            </div>
        </div>
        <div>
            <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Cari Dosen</label>
            <div class="relative">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Nama / NIDN / NUPTK..." class="block w-full rounded-xl border-slate-200 bg-white py-2.5 pl-10 pr-4 text-sm focus:border-[#002855] focus:ring-[#002855] transition-shadow outline-none font-bold text-slate-700 placeholder-slate-400">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center px-3 text-slate-400">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden relative">
        {{-- Loading Overlay --}}
        <div wire:loading.flex wire:target="search, filterProdiId, gotoPage, nextPage, previousPage" 
             class="absolute inset-0 z-20 bg-white/60 backdrop-blur-[1px] items-center justify-center hidden">
             <div class="flex flex-col items-center justify-center p-4 bg-white rounded-2xl shadow-xl border border-slate-100">
                 <svg class="w-8 h-8 text-[#002855] animate-spin mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                     <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                     <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                 </svg>
                 <span class="text-xs font-bold text-slate-500 animate-pulse">Memuat Data...</span>
             </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#002855] border-b border-[#001a38] text-white">
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest">Nama Dosen</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest">NIDN / User</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest">Homebase</th>
                        <th class="px-6 py-4 text-center text-[10px] font-bold uppercase tracking-widest">Status</th>
                        <th class="px-6 py-4 text-right text-[10px] font-bold uppercase tracking-widest">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($dosens as $dosen)
                    <tr class="hover:bg-slate-50/80 transition-colors group">
                        <td class="px-6 py-4 align-top">
                            <div class="text-sm font-black text-slate-800">
                                {{ $dosen->person->nama_dengan_gelar ?? $dosen->nama_lengkap }}
                            </div>
                            <div class="text-[10px] text-slate-400 mt-0.5">{{ $dosen->email ?? '-' }}</div>
                            <div class="mt-2">
                                @if($dosen->jenis_dosen == 'TETAP')
                                <span class="bg-indigo-100 text-indigo-700 text-[9px] px-2 py-0.5 rounded font-bold uppercase border border-indigo-200">Dosen Tetap</span>
                                @elseif($dosen->jenis_dosen == 'LB')
                                <span class="bg-amber-100 text-amber-700 text-[9px] px-2 py-0.5 rounded font-bold uppercase border border-amber-200">Dosen LB</span>
                                @else
                                <span class="bg-teal-100 text-teal-700 text-[9px] px-2 py-0.5 rounded font-bold uppercase border border-teal-200">Praktisi</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 align-top">
                            <div class="font-mono text-xs font-bold text-[#002855] bg-indigo-50 px-2 py-0.5 rounded w-fit">{{ $dosen->nidn ?? 'N/A' }}</div>
                            <div class="text-[10px] text-slate-400 mt-1 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                User: {{ $dosen->user_login ?? 'No Account' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 align-top">
                            <div class="text-xs font-bold text-slate-600 uppercase mb-1">
                                {{ \App\Domains\Core\Models\Prodi::find($dosen->prodi_id)->nama_prodi ?? 'Unknown' }}
                            </div>
                            @if($dosen->asal_institusi)
                            <div class="text-[10px] text-slate-500 italic flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                {{ $dosen->asal_institusi }}
                            </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 align-top text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wide {{ $dosen->is_active ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' : 'bg-rose-100 text-rose-700 border border-rose-200' }}">
                                {{ $dosen->is_active ? 'Aktif' : 'Non-Aktif' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 align-top text-right">
                            <div class="flex justify-end gap-2">
                                {{-- Tombol Gelar --}}
                                <button wire:click="openDegreeModal('{{ $dosen->person_id }}')" class="p-2 text-amber-500 hover:bg-amber-50 rounded-lg transition-colors border border-transparent hover:border-amber-100" title="Atur Gelar">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                                    </svg>
                                </button>

                                <button wire:click="edit('{{ $dosen->id }}')" class="p-2 text-[#002855] hover:bg-[#002855]/10 rounded-lg transition-colors border border-transparent hover:border-[#002855]/20" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <button wire:click="delete('{{ $dosen->id }}')" wire:confirm="Hapus dosen ini? Data login juga akan dihapus." class="p-2 text-rose-600 hover:bg-rose-50 rounded-lg transition-colors border border-transparent hover:border-rose-200" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center">
                            <p class="text-slate-400 font-medium italic">Tidak ada data dosen ditemukan.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
            {{ $dosens->links() }}
        </div>
    </div>

    {{-- MODAL GELAR (Integrated) --}}
    @if($assign_person_id)
    <div class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 animate-in fade-in duration-200">
        <div class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-lg overflow-hidden border border-white/20">
            <div class="bg-amber-500 px-8 py-6 text-white flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-black uppercase tracking-widest leading-none">Manajemen Gelar</h3>
                    @php
                    $pName = \Illuminate\Support\Facades\DB::table('ref_person')->where('id', $assign_person_id)->value('nama_lengkap');
                    @endphp
                    <p class="text-[10px] font-bold uppercase opacity-80 mt-2">Personil: {{ $pName }}</p>
                </div>
                <button wire:click="closeModal" class="text-white/70 hover:text-white"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </div>

            <div class="p-8 space-y-6">
                {{-- Form Gelar --}}
                <div class="bg-slate-50 p-6 rounded-3xl border border-slate-100">
                    <div class="grid grid-cols-3 gap-4">
                        <div class="col-span-2">
                            <label class="block text-[10px] font-black text-slate-400 uppercase mb-1">Pilih Gelar</label>
                            <select wire:model="assign_gelar_id" class="w-full rounded-xl border-slate-200 text-sm font-bold text-slate-700 focus:border-amber-500 focus:ring-amber-500 py-2.5">
                                <option value="">-- Pilih --</option>
                                @foreach($listGelar as $g)
                                <option value="{{ $g->id }}">{{ $g->kode }} ({{ $g->nama }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase mb-1">Urutan</label>
                            <input type="number" wire:model="assign_urutan" class="w-full rounded-xl border-slate-200 text-sm text-center font-bold focus:border-amber-500 focus:ring-amber-500 py-2.5">
                        </div>
                    </div>
                    <button wire:click="saveAssignmentGelar" class="w-full mt-4 bg-amber-500 text-white py-2.5 rounded-xl font-bold text-xs uppercase tracking-widest shadow-lg hover:bg-amber-600 transition-all transform hover:-translate-y-0.5">
                        <span wire:loading.remove wire:target="saveAssignmentGelar">Hubungkan Gelar</span>
                        <span wire:loading wire:target="saveAssignmentGelar">Menyimpan...</span>
                    </button>
                </div>

                {{-- List Gelar --}}
                <div class="space-y-3 max-h-60 overflow-y-auto custom-scrollbar pr-1">
                    <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest sticky top-0 bg-white pb-2">Gelar Terdaftar</h4>
                    @php
                    $assignedGelars = \Illuminate\Support\Facades\DB::table('trx_person_gelar as tpg')
                    ->join('ref_gelar as rg', 'tpg.gelar_id', '=', 'rg.id')
                    ->where('tpg.person_id', $assign_person_id)
                    ->select('tpg.id', 'rg.kode', 'tpg.urutan', 'rg.posisi')
                    ->orderBy('tpg.urutan', 'asc')
                    ->get();
                    @endphp

                    @forelse($assignedGelars as $ag)
                    <div class="flex items-center justify-between p-3 bg-white border border-slate-100 rounded-2xl hover:border-amber-200 transition-colors">
                        <div class="flex items-center gap-3">
                            <span class="w-6 h-6 flex items-center justify-center bg-amber-50 text-amber-600 rounded-lg text-[10px] font-black">{{ $ag->urutan }}</span>
                            <div>
                                <p class="text-sm font-black text-slate-700">{{ $ag->kode }}</p>
                                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wide">{{ $ag->posisi }}</p>
                            </div>
                        </div>
                        <button wire:click="removeAssignmentGelar({{ $ag->id }})" class="p-2 text-rose-500 hover:bg-rose-50 rounded-lg transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                    @empty
                    <div class="text-center py-6 border-2 border-dashed border-slate-100 rounded-2xl">
                        <p class="text-xs text-slate-400 italic">Belum ada gelar yang didaftarkan.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    @endif
</div>