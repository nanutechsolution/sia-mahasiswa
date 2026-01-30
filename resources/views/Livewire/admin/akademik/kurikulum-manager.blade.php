<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-[#002855]">Manajemen Kurikulum</h1>
            <p class="text-slate-500 text-sm mt-1">Daftar kurikulum per Program Studi (Standar Feeder).</p>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="bg-emerald-50 border border-emerald-100 p-4 rounded-xl text-emerald-800 text-sm flex items-center shadow-sm animate-in fade-in slide-in-from-top-2">
            <svg class="w-5 h-5 mr-3 text-emerald-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
            <span class="font-bold">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Form Tambah Kurikulum -->
    <div class="bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden animate-in zoom-in-95 duration-200">
        <div class="px-8 py-5 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
            <h3 class="text-sm font-bold text-[#002855] uppercase tracking-wider flex items-center gap-2">
                <svg class="w-5 h-5 text-[#fcc000]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                Buat Kurikulum Baru
            </h3>
        </div>

        <div class="p-8 space-y-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="col-span-1">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Program Studi</label>
                    <select wire:model="prodi_id" class="block w-full rounded-xl border-slate-300 bg-slate-50 text-slate-900 py-2.5 px-4 text-sm font-bold focus:outline-none focus:ring-2 focus:ring-[#fcc000] focus:border-transparent transition-all">
                        <option value="">Pilih Prodi</option>
                        @foreach($prodis as $p)
                            <option value="{{ $p->id }}">{{ $p->nama_prodi }}</option>
                        @endforeach
                    </select>
                    @error('prodi_id') <span class="text-rose-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div class="col-span-2">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Nama Kurikulum</label>
                    <input type="text" wire:model="nama_kurikulum" placeholder="Contoh: Kurikulum 2024 Merdeka" class="block w-full rounded-xl border-slate-300 bg-slate-50 text-slate-900 py-2.5 px-4 text-sm font-bold placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#fcc000] focus:border-transparent transition-all">
                    @error('nama_kurikulum') <span class="text-rose-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div class="col-span-1">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Tahun Mulai</label>
                    <input type="number" wire:model="tahun_mulai" placeholder="2024" class="block w-full rounded-xl border-slate-300 bg-slate-50 text-slate-900 py-2.5 px-4 text-sm font-bold text-center placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#fcc000] focus:border-transparent transition-all">
                    @error('tahun_mulai') <span class="text-rose-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- FEEDER FIELDS (Grouped) -->
            <div class="bg-[#002855]/5 p-6 rounded-2xl border border-[#002855]/10">
                <label class="block text-xs font-black text-[#002855] uppercase tracking-widest mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    Konfigurasi Feeder
                </label>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="col-span-1">
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1 text-center">Min SKS Lulus</label>
                        <input type="number" wire:model="jumlah_sks_lulus" class="block w-full border-0 bg-white rounded-lg py-2 text-center text-xl font-black text-slate-800 shadow-sm ring-1 ring-slate-200 focus:ring-2 focus:ring-[#002855]">
                    </div>
                    <div class="col-span-1">
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1 text-center">ID Semester Mulai</label>
                        <input type="text" wire:model="id_semester_mulai" placeholder="Cth: 20241" class="block w-full border-0 bg-white rounded-lg py-2 text-center text-sm font-bold text-slate-800 shadow-sm ring-1 ring-slate-200 focus:ring-2 focus:ring-[#002855]">
                    </div>
                    <div class="col-span-2 flex items-center">
                        <p class="text-[10px] text-slate-500 italic bg-white/50 p-3 rounded-lg border border-slate-200/50 w-full">
                            <span class="font-bold text-[#002855]">*Info:</span> Jumlah SKS Wajib & Pilihan akan dihitung otomatis oleh sistem saat Anda menambahkan mata kuliah ke dalam struktur kurikulum nanti.
                        </p>
                    </div>
                </div>
            </div>

            <div class="pt-6 border-t border-slate-100 flex justify-end">
                <button wire:click="saveHeader" class="inline-flex items-center px-6 py-3 bg-[#002855] text-white rounded-xl font-bold text-sm shadow-lg shadow-indigo-900/20 hover:bg-[#001a38] hover:scale-105 transition-all">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" /></svg>
                    Simpan Kurikulum
                </button>
            </div>
        </div>
    </div>

    <!-- List Kurikulum -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @foreach($kurikulums as $k)
        <div class="group bg-white rounded-2xl border transition-all duration-300 hover:-translate-y-1 hover:shadow-xl {{ $k->is_active ? 'border-emerald-200 shadow-emerald-50' : 'border-slate-200 shadow-sm' }}">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <span class="inline-flex items-center rounded-lg bg-slate-50 px-2.5 py-1 text-[10px] font-bold text-slate-600 ring-1 ring-inset ring-slate-500/10 uppercase tracking-wide">
                        {{ $k->prodi->nama_prodi }}
                    </span>
                    <button wire:click="toggleActive({{ $k->id }})" 
                        class="px-2.5 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest transition-colors {{ $k->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-400 hover:bg-emerald-50 hover:text-emerald-600' }}">
                        {{ $k->is_active ? 'AKTIF' : 'NON-AKTIF' }}
                    </button>
                </div>
                
                <h3 class="text-lg font-black text-[#002855] leading-tight mb-1">{{ $k->nama_kurikulum }}</h3>
                <p class="text-xs font-bold text-slate-400">Mulai Tahun: {{ $k->tahun_mulai }} <span class="font-mono text-[10px] opacity-70">({{ $k->id_semester_mulai }})</span></p>
                
                <div class="mt-6 space-y-3">
                    <div class="flex justify-between items-center text-xs border-b border-slate-50 pb-2">
                        <span class="text-slate-500 font-medium">Min. Lulus</span>
                        <span class="font-bold text-slate-800">{{ $k->jumlah_sks_lulus }} SKS</span>
                    </div>
                    <div class="flex justify-between items-center text-xs">
                        <span class="text-slate-500 font-medium">Struktur SKS</span>
                        <div class="flex gap-2">
                            <span class="bg-[#002855]/5 text-[#002855] px-2 py-0.5 rounded font-bold" title="Wajib">W: {{ $k->jumlah_sks_wajib }}</span>
                            <span class="bg-[#fcc000]/20 text-slate-700 px-2 py-0.5 rounded font-bold" title="Pilihan">P: {{ $k->jumlah_sks_pilihan }}</span>
                        </div>
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-slate-100">
                    <button wire:click="manage({{ $k->id }})" 
                        class="w-full flex items-center justify-center rounded-xl bg-white border-2 border-[#002855] px-4 py-2.5 text-xs font-bold text-[#002855] hover:bg-[#002855] hover:text-white transition-all group-hover:shadow-md">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        Atur Mata Kuliah
                    </button>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>