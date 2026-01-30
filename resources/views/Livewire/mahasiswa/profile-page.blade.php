<div class="space-y-8 animate-in fade-in duration-500" x-data="{ tab: 'biodata' }">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-[#002855]">Profil Mahasiswa</h1>
            <p class="text-slate-500 text-sm mt-1">Kelola informasi pribadi, data keluarga, dokumen, dan keamanan akun Anda.</p>
        </div>
    </div>

    {{-- Navigasi Tab --}}
    <div class="flex flex-wrap gap-2 p-1 bg-white rounded-2xl shadow-sm border border-slate-200 w-fit">
        <button @click="tab = 'biodata'" :class="tab === 'biodata' ? 'bg-[#002855] text-white shadow-lg' : 'text-slate-500 hover:bg-slate-50'" class="px-6 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest transition-all">Biodata</button>
        <button @click="tab = 'keluarga'" :class="tab === 'keluarga' ? 'bg-[#002855] text-white shadow-lg' : 'text-slate-500 hover:bg-slate-50'" class="px-6 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest transition-all">Keluarga & Alamat</button>
        <button @click="tab = 'berkas'" :class="tab === 'berkas' ? 'bg-[#002855] text-white shadow-lg' : 'text-slate-500 hover:bg-slate-50'" class="px-6 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest transition-all">Berkas & Dokumen</button>
        <button @click="tab = 'keamanan'" :class="tab === 'keamanan' ? 'bg-[#002855] text-white shadow-lg' : 'text-slate-500 hover:bg-slate-50'" class="px-6 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest transition-all">Keamanan</button>
    </div>

    {{-- Alert Messages --}}
    @if (session()->has('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-100 text-emerald-800 rounded-2xl text-sm font-bold flex items-center shadow-sm animate-bounce-short">
            <svg class="w-5 h-5 mr-3 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
            {{ session('success') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="p-4 bg-rose-50 border border-rose-100 text-rose-800 rounded-2xl text-sm font-bold flex items-center shadow-sm">
            <svg class="w-5 h-5 mr-3 text-rose-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg>
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- KOLOM UTAMA --}}
        <div class="lg:col-span-2">
            
            {{-- TAB: BIODATA --}}
            <div x-show="tab === 'biodata'" class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden animate-in slide-in-from-left-4">
                <div class="px-8 py-6 border-b border-slate-100 bg-slate-50/50">
                    <h3 class="text-sm font-black text-[#002855] uppercase tracking-widest">Identitas Dasar</h3>
                </div>
                <form wire:submit.prevent="updateProfil" class="p-8 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Nama Lengkap Sesuai Ijazah</label>
                            <input type="text" wire:model="nama_lengkap" class="w-full rounded-xl border-slate-200 bg-slate-50 font-bold text-slate-700 py-2.5 pl-4 focus:ring-2 focus:ring-[#fcc000] focus:outline-none transition-all">
                            @error('nama_lengkap') <span class="text-[10px] text-rose-500 font-bold mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">NIK (KTP)</label>
                            <input type="text" wire:model="nik" class="w-full rounded-xl border-slate-200 bg-slate-50 font-bold text-slate-700 py-2.5 pl-4 focus:ring-2 focus:ring-[#fcc000] focus:outline-none transition-all">
                            @error('nik') <span class="text-[10px] text-rose-500 font-bold mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Email Korespondensi</label>
                            <input type="email" wire:model="email_pribadi" class="w-full rounded-xl border-slate-200 bg-slate-50 font-bold text-slate-700 py-2.5 pl-4 focus:ring-2 focus:ring-[#fcc000] focus:outline-none transition-all">
                            @error('email_pribadi') <span class="text-[10px] text-rose-500 font-bold mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Nomor Ponsel (WA)</label>
                            <input type="text" wire:model="nomor_hp" class="w-full rounded-xl border-slate-200 bg-slate-50 font-bold text-slate-700 py-2.5 pl-4 focus:ring-2 focus:ring-[#fcc000] focus:outline-none transition-all">
                            @error('nomor_hp') <span class="text-[10px] text-rose-500 font-bold mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Tempat Lahir</label>
                            <input type="text" wire:model="tempat_lahir" class="w-full rounded-xl border-slate-200 bg-slate-50 font-bold text-slate-700 py-2.5 pl-4 focus:ring-2 focus:ring-[#fcc000] focus:outline-none transition-all">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Tanggal Lahir</label>
                            <input type="date" wire:model="tanggal_lahir" class="w-full rounded-xl border-slate-200 bg-slate-50 font-bold text-slate-700 py-2.5 pl-4 focus:ring-2 focus:ring-[#fcc000] focus:outline-none transition-all">
                            @error('tanggal_lahir') <span class="text-[10px] text-rose-500 font-bold mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Jenis Kelamin</label>
                            <select wire:model="jenis_kelamin" class="w-full rounded-xl border-slate-200 bg-slate-50 font-bold text-slate-700 py-2.5 pl-4 focus:ring-2 focus:ring-[#fcc000] focus:outline-none transition-all appearance-none">
                                <option value="L">Laki-Laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                    </div>
                    <div class="pt-6 border-t border-slate-100 text-right">
                        <button type="submit" wire:loading.attr="disabled" class="px-8 py-3 bg-[#002855] text-white rounded-xl font-black text-xs uppercase tracking-widest hover:bg-[#001a38] transition-all shadow-lg shadow-indigo-900/20 disabled:opacity-50">
                            <span wire:loading.remove>Simpan Perubahan</span>
                            <span wire:loading>Memproses...</span>
                        </button>
                    </div>
                </form>
            </div>

            {{-- TAB: KELUARGA --}}
            <div x-show="tab === 'keluarga'" class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden animate-in slide-in-from-left-4">
                <div class="px-8 py-6 border-b border-slate-100 bg-slate-50/50">
                    <h3 class="text-sm font-black text-[#002855] uppercase tracking-widest">Alamat & Keluarga</h3>
                </div>
                <form wire:submit.prevent="updateProfil" class="p-8 space-y-8">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="md:col-span-3">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Alamat Jalan / Dusun</label>
                            <input type="text" wire:model="jalan" class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm font-bold py-2.5 pl-4 focus:ring-2 focus:ring-[#fcc000] focus:outline-none transition-all">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Kelurahan</label>
                            <input type="text" wire:model="kelurahan" class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm font-bold py-2.5 pl-4">
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div><label class="block text-[10px] font-black text-slate-400 uppercase text-center">RT</label><input type="text" wire:model="rt" class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm font-bold py-2.5 text-center"></div>
                            <div><label class="block text-[10px] font-black text-slate-400 uppercase text-center">RW</label><input type="text" wire:model="rw" class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm font-bold py-2.5 text-center"></div>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Kode Pos</label>
                            <input type="text" wire:model="kode_pos" class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm font-bold py-2.5 pl-4">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-6 border-t border-slate-100">
                        <div class="space-y-4">
                            <h4 class="text-xs font-black text-[#002855] uppercase tracking-wider flex items-center"><span class="w-1.5 h-4 bg-[#fcc000] mr-2 rounded-full"></span>Data Ayah</h4>
                            <div><label class="block text-[10px] font-black text-slate-400 mb-1">Nama Lengkap Ayah</label><input type="text" wire:model="nama_ayah" class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm font-bold py-2.5 pl-4"></div>
                            <div><label class="block text-[10px] font-black text-slate-400 mb-1">NIK Ayah</label><input type="text" wire:model="nik_ayah" class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm font-bold py-2.5 pl-4"></div>
                        </div>
                        <div class="space-y-4">
                            <h4 class="text-xs font-black text-[#002855] uppercase tracking-wider flex items-center"><span class="w-1.5 h-4 bg-[#fcc000] mr-2 rounded-full"></span>Data Ibu</h4>
                            <div><label class="block text-[10px] font-black text-slate-400 mb-1">Nama Lengkap Ibu</label><input type="text" wire:model="nama_ibu" class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm font-bold py-2.5 pl-4"></div>
                            <div><label class="block text-[10px] font-black text-slate-400 mb-1">NIK Ibu</label><input type="text" wire:model="nik_ibu" class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm font-bold py-2.5 pl-4"></div>
                        </div>
                    </div>
                    <div class="text-right">
                        <button type="submit" class="px-8 py-3 bg-[#002855] text-white rounded-xl font-black text-xs uppercase tracking-widest hover:bg-[#001a38] transition-all">Simpan Data Alamat</button>
                    </div>
                </form>
            </div>

            {{-- TAB: BERKAS & DOKUMEN --}}
            <div x-show="tab === 'berkas'" class="space-y-6 animate-in slide-in-from-left-4">
                <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="px-8 py-6 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
                        <h3 class="text-sm font-black text-[#002855] uppercase tracking-widest">Dokumen Pendukung (DIKTI)</h3>
                        <span class="text-[10px] font-bold text-slate-400 italic">*Format PDF/JPG, Max 2MB</span>
                    </div>
                    <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- KTP --}}
                        <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100 flex flex-col justify-between">
                            <div>
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="p-2 bg-indigo-100 text-indigo-600 rounded-lg"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 012-2h2a2 2 0 012 2v1m-6 0h6"/></svg></div>
                                    <h4 class="text-xs font-black text-[#002855] uppercase">Scan KTP Asli</h4>
                                </div>
                                <input type="file" wire:model="berkas_ktp" class="text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-[10px] file:font-black file:bg-[#002855] file:text-white hover:file:bg-[#001a38] transition-all">
                            </div>
                            <div class="mt-4 pt-4 border-t border-slate-200">
                                @if($mahasiswa->data_tambahan['path_ktp'] ?? false)
                                    <span class="text-[10px] font-bold text-emerald-600 flex items-center"><svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/></svg> Sudah Terunggah</span>
                                @else
                                    <span class="text-[10px] font-bold text-rose-400 italic">Belum tersedia</span>
                                @endif
                            </div>
                        </div>

                        {{-- Ijazah --}}
                        <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100 flex flex-col justify-between">
                            <div>
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="p-2 bg-indigo-100 text-indigo-600 rounded-lg"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg></div>
                                    <h4 class="text-xs font-black text-[#002855] uppercase">Ijazah Terakhir</h4>
                                </div>
                                <input type="file" wire:model="berkas_ijazah" class="text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-[10px] file:font-black file:bg-[#002855] file:text-white hover:file:bg-[#001a38] transition-all">
                            </div>
                            <div class="mt-4 pt-4 border-t border-slate-200">
                                @if($mahasiswa->data_tambahan['path_ijazah'] ?? false)
                                    <span class="text-[10px] font-bold text-emerald-600 flex items-center"><svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/></svg> Sudah Terunggah</span>
                                @else
                                    <span class="text-[10px] font-bold text-rose-400 italic">Belum tersedia</span>
                                @endif
                            </div>
                        </div>

                        {{-- Kartu Keluarga --}}
                        <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100 flex flex-col justify-between">
                            <div>
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="p-2 bg-indigo-100 text-indigo-600 rounded-lg"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg></div>
                                    <h4 class="text-xs font-black text-[#002855] uppercase">Kartu Keluarga</h4>
                                </div>
                                <input type="file" wire:model="berkas_kk" class="text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-[10px] file:font-black file:bg-[#002855] file:text-white hover:file:bg-[#001a38] transition-all">
                            </div>
                            <div class="mt-4 pt-4 border-t border-slate-200">
                                @if($mahasiswa->data_tambahan['path_kk'] ?? false)
                                    <span class="text-[10px] font-bold text-emerald-600 flex items-center"><svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/></svg> Sudah Terunggah</span>
                                @else
                                    <span class="text-[10px] font-bold text-rose-400 italic">Belum tersedia</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="p-8 pt-0 text-right">
                        <button wire:click="uploadBerkas" wire:loading.attr="disabled" class="px-8 py-3 bg-[#002855] text-white rounded-xl font-black text-xs uppercase tracking-widest hover:bg-[#001a38] transition-all shadow-lg">
                            <span wire:loading.remove>Simpan Berkas Dokumen</span>
                            <span wire:loading>Mengunggah...</span>
                        </button>
                    </div>
                </div>
            </div>

            {{-- TAB: KEAMANAN --}}
            <div x-show="tab === 'keamanan'" class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden animate-in slide-in-from-left-4">
                <div class="px-8 py-6 border-b border-slate-100 bg-slate-50/50">
                    <h3 class="text-sm font-black text-[#002855] uppercase tracking-widest">Manajemen Keamanan</h3>
                </div>
                <form wire:submit.prevent="updatePassword" class="p-8 space-y-6">
                    @if (session()->has('success_pw'))
                        <div class="p-3 bg-emerald-50 text-emerald-700 rounded-xl text-xs font-bold border border-emerald-100">{{ session('success_pw') }}</div>
                    @endif
                    <div class="max-w-md space-y-5">
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Kata Sandi Saat Ini</label>
                            <input type="password" wire:model="current_password" class="w-full rounded-xl border-slate-200 bg-slate-50 py-2.5 pl-4 focus:ring-2 focus:ring-rose-500 focus:outline-none transition-all">
                            @error('current_password') <span class="text-[10px] text-rose-500 font-bold mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div class="pt-4 border-t border-slate-50">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Kata Sandi Baru</label>
                            <input type="password" wire:model="new_password" class="w-full rounded-xl border-slate-200 bg-slate-50 py-2.5 pl-4 focus:ring-2 focus:ring-[#fcc000] focus:outline-none transition-all">
                            @error('new_password') <span class="text-[10px] text-rose-500 font-bold mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Ulangi Kata Sandi Baru</label>
                            <input type="password" wire:model="new_password_confirmation" class="w-full rounded-xl border-slate-200 bg-slate-50 py-2.5 pl-4 focus:ring-2 focus:ring-[#fcc000] focus:outline-none">
                            @error('new_password_confirmation') <span class="text-[10px] text-rose-500 font-bold mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <button type="submit" class="px-8 py-3 bg-rose-600 text-white rounded-xl font-black text-xs uppercase tracking-widest hover:bg-rose-700 transition-all shadow-lg shadow-rose-900/20">Perbarui Kata Sandi</button>
                </form>
            </div>
        </div>

        {{-- SIDEBAR: STATUS & FOTO PROFIL --}}
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-200 text-center relative overflow-hidden group">
                <div class="absolute top-0 right-0 w-32 h-32 bg-[#fcc000]/5 rounded-full -mr-16 -mt-16 group-hover:scale-110 transition-transform"></div>
                
                {{-- FOTO PROFIL --}}
                <div class="relative inline-block mb-6">
                    <div class="h-32 w-32 rounded-full bg-[#002855] text-[#fcc000] flex items-center justify-center text-4xl font-black mx-auto border-4 border-slate-50 shadow-xl ring-2 ring-indigo-50 overflow-hidden relative group/avatar">
                        @if ($photo_profil)
                            <img src="{{ $photo_profil->temporaryUrl() }}" class="h-full w-full object-cover">
                        @elseif ($person->photo_path)
                            <img src="{{ Storage::url($person->photo_path) }}" class="h-full w-full object-cover">
                        @else
                            {{ substr($nama_lengkap, 0, 1) }}
                        @endif

                        {{-- Hover Overlay for Photo --}}
                        <label class="absolute inset-0 bg-black/60 opacity-0 group-hover/avatar:opacity-100 transition-opacity flex flex-col items-center justify-center cursor-pointer">
                            <svg class="w-8 h-8 text-white mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span class="text-[9px] font-black text-white uppercase tracking-wider">Ubah Foto</span>
                            <input type="file" wire:model="photo_profil" class="hidden">
                        </label>
                    </div>
                    
                    {{-- Loading Spinner for Photo --}}
                    <div wire:loading wire:target="photo_profil" class="absolute inset-0 bg-white/80 backdrop-blur-sm rounded-full flex items-center justify-center">
                        <svg class="animate-spin h-6 w-6 text-[#002855]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    </div>
                </div>

                <h4 class="text-lg font-black text-[#002855] leading-tight">{{ $nama_lengkap }}</h4>
                <p class="text-[11px] font-mono font-bold text-slate-400 mt-1 uppercase tracking-widest">{{ $mahasiswa->nim }}</p>
                
                <div class="mt-8 pt-6 border-t border-slate-100 flex flex-col gap-3 text-left">
                    <div class="flex justify-between text-xs">
                        <span class="text-slate-400 font-bold uppercase tracking-tighter">Status Akademik</span>
                        <span class="text-emerald-600 font-black uppercase">Mahasiswa Aktif</span>
                    </div>
                    <div class="flex justify-between text-xs">
                        <span class="text-slate-400 font-bold uppercase tracking-tighter">Angkatan</span>
                        <span class="text-slate-700 font-black uppercase">{{ $mahasiswa->angkatan_id }}</span>
                    </div>
                    <div class="flex justify-between text-xs">
                        <span class="text-slate-400 font-bold uppercase tracking-tighter">Kode Prodi</span>
                        <span class="text-slate-700 font-black uppercase">{{ $mahasiswa->prodi->kode_prodi_internal }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-indigo-50/50 p-6 rounded-[2rem] border border-indigo-100">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-2 bg-indigo-100 rounded-xl text-indigo-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <h5 class="text-[11px] font-black text-indigo-900 uppercase tracking-widest">Informasi Dokumen</h5>
                </div>
                <p class="text-[11px] text-indigo-700/70 leading-relaxed font-medium">
                    Unggah dokumen asli untuk validasi data PDDIKTI. Dokumen yang diunggah akan diverifikasi oleh Biro Administrasi Akademik (BAAK).
                </p>
            </div>
        </div>
    </div>
</div>