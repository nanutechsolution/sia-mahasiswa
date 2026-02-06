<div class="space-y-8 animate-in fade-in duration-500" x-data="{ tab: 'biodata' }">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-[#002855]">Profil Dosen</h1>
            <p class="text-slate-500 text-sm mt-1">Kelola biodata pengajar, dokumen sertifikasi, dan keamanan akun.</p>
        </div>
    </div>

    {{-- Navigasi Tab --}}
    <div class="flex flex-wrap gap-2 p-1 bg-white rounded-2xl shadow-sm border border-slate-200 w-fit">
        <button @click="tab = 'biodata'" :class="tab === 'biodata' ? 'bg-[#002855] text-white shadow-lg' : 'text-slate-500 hover:bg-slate-50'" class="px-6 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest transition-all">Biodata</button>
        <button @click="tab = 'berkas'" :class="tab === 'berkas' ? 'bg-[#002855] text-white shadow-lg' : 'text-slate-500 hover:bg-slate-50'" class="px-6 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest transition-all">Berkas & Sertifikasi</button>
        <button @click="tab = 'keamanan'" :class="tab === 'keamanan' ? 'bg-[#002855] text-white shadow-lg' : 'text-slate-500 hover:bg-slate-50'" class="px-6 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest transition-all">Keamanan</button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- KOLOM UTAMA --}}
        <div class="lg:col-span-2">
            
            {{-- TAB: BIODATA --}}
            <div x-show="tab === 'biodata'" class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden animate-in slide-in-from-left-4">
                <div class="px-8 py-6 border-b border-slate-100 bg-slate-50/50">
                    <h3 class="text-sm font-black text-[#002855] uppercase tracking-widest">Identitas Pendidik</h3>
                </div>
                <form wire:submit.prevent="updateProfil" class="p-8 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-[10px] font-black text-slate-400 uppercase mb-1 tracking-widest">Nama Lengkap & Gelar</label>
                            <input type="text" wire:model="nama_lengkap" class="w-full rounded-xl border-slate-200 bg-slate-50 font-bold text-slate-700 py-2.5 pl-4 focus:ring-2 focus:ring-[#fcc000] outline-none">
                            @error('nama_lengkap') <span class="text-[10px] text-rose-500 font-bold mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase mb-1 tracking-widest">NIK (KTP)</label>
                            <input type="text" wire:model="nik" class="w-full rounded-xl border-slate-200 bg-slate-50 font-bold text-slate-700 py-2.5 pl-4 focus:ring-2 focus:ring-[#fcc000] outline-none">
                            @error('nik') <span class="text-[10px] text-rose-500 font-bold mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase mb-1 tracking-widest">Email Aktif</label>
                            <input type="email" wire:model="email_pribadi" class="w-full rounded-xl border-slate-200 bg-slate-50 font-bold text-slate-700 py-2.5 pl-4 focus:ring-2 focus:ring-[#fcc000] outline-none">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase mb-1 tracking-widest">Nomor HP / WA</label>
                            <input type="text" wire:model="nomor_hp" class="w-full rounded-xl border-slate-200 bg-slate-50 font-bold text-slate-700 py-2.5 pl-4 focus:ring-2 focus:ring-[#fcc000] outline-none">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase mb-1 tracking-widest">Tempat Lahir</label>
                            <input type="text" wire:model="tempat_lahir" class="w-full rounded-xl border-slate-200 bg-slate-50 font-bold text-slate-700 py-2.5 pl-4 focus:ring-2 focus:ring-[#fcc000] outline-none">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase mb-1 tracking-widest">Tanggal Lahir</label>
                            <input type="date" wire:model="tanggal_lahir" class="w-full rounded-xl border-slate-200 bg-slate-50 font-bold text-slate-700 py-2.5 pl-4 focus:ring-2 focus:ring-[#fcc000] outline-none">
                        </div>
                    </div>
                    
                    <div class="pt-6 border-t border-slate-100 space-y-4">
                        <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Alamat Tinggal</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="md:col-span-2">
                                <label class="block text-[10px] font-black text-slate-400 uppercase mb-1">Jalan / Dusun</label>
                                <input type="text" wire:model="jalan" class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm font-bold py-2.5 pl-4">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-slate-400 uppercase mb-1">Kode Pos</label>
                                <input type="text" wire:model="kode_pos" class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm font-bold py-2.5 pl-4">
                            </div>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-slate-100 text-right">
                        <button type="submit" wire:loading.attr="disabled" class="px-8 py-3 bg-[#002855] text-white rounded-xl font-black text-xs uppercase tracking-widest hover:bg-[#001a38] transition-all shadow-lg disabled:opacity-50">
                            <span wire:loading.remove>Simpan Profil</span>
                            <span wire:loading>Menyimpan...</span>
                        </button>
                    </div>
                </form>
            </div>

            {{-- TAB: BERKAS --}}
            <div x-show="tab === 'berkas'" class="space-y-6 animate-in slide-in-from-left-4">
                <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="px-8 py-6 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
                        <h3 class="text-sm font-black text-[#002855] uppercase tracking-widest">Dokumen Kepegawaian</h3>
                        <span class="text-[10px] font-bold text-slate-400 italic">Format PDF/JPG, Max 2MB</span>
                    </div>
                    <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100">
                            <h4 class="text-xs font-black text-[#002855] uppercase mb-4 flex items-center"><span class="w-1.5 h-4 bg-[#fcc000] mr-2 rounded-full"></span>Scan KTP</h4>
                            <input type="file" wire:model="berkas_ktp" class="text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-[10px] file:font-black file:bg-[#002855] file:text-white hover:file:bg-[#001a38] cursor-pointer">
                        </div>
                        <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100">
                            <h4 class="text-xs font-black text-[#002855] uppercase mb-4 flex items-center"><span class="w-1.5 h-4 bg-[#fcc000] mr-2 rounded-full"></span>Sertifikat Pendidik (Serdos)</h4>
                            <input type="file" wire:model="berkas_serdos" class="text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-[10px] file:font-black file:bg-[#002855] file:text-white hover:file:bg-[#001a38] cursor-pointer">
                        </div>
                    </div>
                    <div class="p-8 pt-0 text-right">
                        <button wire:click="uploadBerkas" class="px-8 py-3 bg-[#002855] text-white rounded-xl font-black text-xs uppercase tracking-widest hover:bg-[#001a38] shadow-lg transition-all">Simpan Berkas</button>
                    </div>
                </div>
            </div>

            {{-- TAB: KEAMANAN --}}
            <div x-show="tab === 'keamanan'" class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden animate-in slide-in-from-left-4">
                <div class="px-8 py-6 border-b border-slate-100 bg-slate-50/50">
                    <h3 class="text-sm font-black text-[#002855] uppercase tracking-widest">Pengaturan Keamanan</h3>
                </div>
                <form wire:submit.prevent="updatePassword" class="p-8 space-y-6">
                    @if (session()->has('success_pw'))
                        <div class="p-3 bg-emerald-50 text-emerald-700 rounded-xl text-xs font-bold border border-emerald-100">{{ session('success_pw') }}</div>
                    @endif
                    <div class="max-w-md space-y-5">
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Password Saat Ini</label>
                            <input type="password" wire:model="current_password" class="w-full rounded-xl border-slate-200 bg-slate-50 py-2.5 pl-4 focus:ring-2 focus:ring-rose-500 outline-none">
                            @error('current_password') <span class="text-[10px] text-rose-500 font-bold mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div class="pt-4 border-t border-slate-50">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Password Baru</label>
                            <input type="password" wire:model="new_password" class="w-full rounded-xl border-slate-200 bg-slate-50 py-2.5 pl-4 focus:ring-2 focus:ring-[#fcc000] outline-none">
                            @error('new_password') <span class="text-[10px] text-rose-500 font-bold mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Ulangi Password Baru</label>
                            <input type="password" wire:model="new_password_confirmation" class="w-full rounded-xl border-slate-200 bg-slate-50 py-2.5 pl-4 focus:ring-2 focus:ring-[#fcc000] outline-none">
                        </div>
                    </div>
                    <button type="submit" class="px-8 py-3 bg-rose-600 text-white rounded-xl font-black text-xs uppercase tracking-widest hover:bg-rose-700 shadow-lg shadow-rose-900/20 transition-all">Perbarui Password</button>
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

                        <label class="absolute inset-0 bg-black/60 opacity-0 group-hover/avatar:opacity-100 transition-opacity flex flex-col items-center justify-center cursor-pointer">
                            <svg class="w-8 h-8 text-white mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span class="text-[9px] font-black text-white uppercase tracking-wider">Ubah Foto</span>
                            <input type="file" wire:model="photo_profil" class="hidden">
                        </label>
                    </div>
                </div>

                <h4 class="text-lg font-black text-[#002855] leading-tight">{{ $nama_lengkap }}</h4>
                <p class="text-[11px] font-mono font-bold text-slate-400 mt-1 uppercase tracking-widest">NIDN: {{ $dosen->nidn ?? '-' }}</p>
                
                <div class="mt-8 pt-6 border-t border-slate-100 flex flex-col gap-3 text-left">
                    <div class="flex justify-between text-xs">
                        <span class="text-slate-400 font-bold uppercase tracking-tighter">Homebase</span>
                        <span class="text-slate-700 font-black uppercase text-right">{{ $dosen->prodi->nama_prodi ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between text-xs">
                        <span class="text-slate-400 font-bold uppercase tracking-tighter">Status</span>
                        <span class="text-emerald-600 font-black uppercase">Dosen Aktif</span>
                    </div>
                    <div class="flex justify-between text-xs">
                        <span class="text-slate-400 font-bold uppercase tracking-tighter">Ikatan Kerja</span>
                        <span class="text-slate-700 font-black uppercase">{{ $dosen->jenis_dosen }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-indigo-50/50 p-6 rounded-[2rem] border border-indigo-100">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-2 bg-indigo-100 rounded-xl text-indigo-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <h5 class="text-[11px] font-black text-indigo-900 uppercase tracking-widest">Sinkronisasi PDDikti</h5>
                </div>
                <p class="text-[11px] text-indigo-700/70 leading-relaxed font-medium">
                    Data profil dosen disinkronkan dengan aplikasi **Neo Feeder**. Pastikan NIDN dan NUPTK sudah sesuai dengan sistem kementerian.
                </p>
            </div>
        </div>
    </div>
</div>