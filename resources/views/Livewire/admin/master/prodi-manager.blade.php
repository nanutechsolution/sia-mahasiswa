<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-[#002855]">Master Program Studi</h1>
            <p class="text-slate-500 text-sm mt-1">Data jurusan/prodi, jenjang, dan pejabat struktural.</p>
        </div>
        @if(!$showForm)
        <button wire:click="create" class="inline-flex items-center px-5 py-2.5 bg-[#fcc000] text-[#002855] rounded-xl font-black text-sm shadow-lg shadow-orange-500/20 hover:bg-[#fbbf24] hover:scale-105 transition-all">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" /></svg>
            Tambah Prodi
        </button>
        @endif
    </div>

    {{-- Alert --}}
    @if (session()->has('success'))
        <div class="bg-emerald-50 border border-emerald-100 p-4 rounded-xl text-emerald-800 text-sm flex items-center shadow-sm animate-in fade-in slide-in-from-top-2">
            <svg class="w-5 h-5 mr-3 text-emerald-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
            <span class="font-bold">{{ session('success') }}</span>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="bg-rose-50 border border-rose-100 p-4 rounded-xl text-rose-800 text-sm flex items-center shadow-sm animate-in fade-in slide-in-from-top-2">
             <span class="font-bold">{{ session('error') }}</span>
        </div>
    @endif

    {{-- Form --}}
    @if($showForm)
    <div class="bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden animate-in zoom-in-95 duration-200">
        <div class="px-8 py-5 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
            <h3 class="text-sm font-bold text-[#002855] uppercase tracking-wider flex items-center gap-2">
                @if($editMode)
                    <svg class="w-5 h-5 text-[#fcc000]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                    Edit Data Prodi
                @else
                    <svg class="w-5 h-5 text-[#002855]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    Tambah Prodi Baru
                @endif
            </h3>
            <button wire:click="$set('showForm', false)" class="text-slate-400 hover:text-slate-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>

        <div class="p-8 space-y-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="col-span-2">
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-2">Fakultas Induk</label>
                    <select wire:model="fakultas_id" class="block w-full rounded-lg border-slate-300 focus:border-[#002855] focus:ring-[#002855] text-sm font-bold text-slate-700">
                        <option value="">-- Pilih Fakultas --</option>
                        @foreach($fakultas_list as $f)
                            <option value="{{ $f->id }}">{{ $f->nama_fakultas }}</option>
                        @endforeach
                    </select>
                    @error('fakultas_id') <span class="text-rose-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>
                
                <div class="col-span-2">
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-2">Nama Prodi</label>
                    <input type="text" wire:model="nama_prodi" class="block w-full rounded-lg border-slate-300 focus:border-[#002855] focus:ring-[#002855] text-sm font-bold placeholder-slate-300" placeholder="Contoh: Teknik Informatika">
                    @error('nama_prodi') <span class="text-rose-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-2">Kode Internal (Unik)</label>
                    <input type="text" wire:model="kode_prodi_internal" class="block w-full rounded-lg border-slate-300 focus:border-[#002855] focus:ring-[#002855] text-sm font-bold uppercase placeholder-slate-300" placeholder="Contoh: TI">
                    @error('kode_prodi_internal') <span class="text-rose-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-2">Kode Dikti</label>
                    <input type="text" wire:model="kode_prodi_dikti" class="block w-full rounded-lg border-slate-300 focus:border-[#002855] focus:ring-[#002855] text-sm font-bold placeholder-slate-300" placeholder="Contoh: 55201">
                </div>
                
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-2">Jenjang</label>
                    <select wire:model="jenjang" class="block w-full rounded-lg border-slate-300 focus:border-[#002855] focus:ring-[#002855] text-sm font-bold text-slate-700">
                        <option value="D3">D3 (Diploma 3)</option>
                        <option value="D4">D4 (Sarjana Terapan)</option>
                        <option value="S1">S1 (Sarjana)</option>
                        <option value="S2">S2 (Magister)</option>
                        <option value="S3">S3 (Doktor)</option>
                        <option value="PROFESI">PROFESI</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-2">Gelar Lulusan</label>
                    <input type="text" wire:model="gelar_lulusan" class="block w-full rounded-lg border-slate-300 focus:border-[#002855] focus:ring-[#002855] text-sm font-bold placeholder-slate-300" placeholder="Contoh: S.Kom">
                </div>

                <!-- FORMAT NIM CONFIG -->
                <div class="col-span-2 lg:col-span-4 bg-[#002855]/5 p-4 rounded-xl border border-[#002855]/10 mt-2">
                    <label class="block text-[10px] font-black text-[#002855] uppercase tracking-widest mb-2">Format Auto-Generate NIM</label>
                    <div class="flex gap-4 items-start">
                        <div class="flex-1">
                            <input type="text" wire:model="format_nim" class="block w-full rounded-lg border-slate-300 focus:border-[#002855] focus:ring-[#002855] text-sm font-mono font-bold tracking-wide text-slate-700" placeholder="Contoh: {THN}{KODE}{NO:4}">
                            <p class="text-[10px] text-[#002855] mt-2 leading-relaxed font-medium">
                                <span class="opacity-70">Variabel:</span> 
                                <code class="bg-white px-1 rounded border border-slate-200">{THN}</code>, 
                                <code class="bg-white px-1 rounded border border-slate-200">{TAHUN}</code>, 
                                <code class="bg-white px-1 rounded border border-slate-200">{KODE}</code>, 
                                <code class="bg-white px-1 rounded border border-slate-200">{INTERNAL}</code>, 
                                <code class="bg-white px-1 rounded border border-slate-200">{NO:3}</code>
                            </p>
                        </div>
                        <div class="hidden md:block w-1/3">
                            <div class="bg-white p-3 rounded-lg border border-slate-200 text-[10px] text-slate-500 shadow-sm">
                                <strong class="text-[#002855]">Preview Contoh:</strong><br>
                                Pattern: <span class="font-mono text-slate-700">{THN}{KODE}{NO:4}</span><br>
                                Hasil: <span class="font-mono font-black text-[#002855]">24552010001</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-6 border-t border-slate-100 flex justify-end gap-3">
                <button wire:click="$set('showForm', false)" class="px-6 py-2.5 text-sm font-bold text-slate-500 hover:text-slate-700 hover:bg-slate-100 rounded-xl transition-all">Batal</button>
                <button wire:click="save" class="px-8 py-2.5 bg-[#002855] text-white rounded-xl text-sm font-bold shadow-lg shadow-indigo-200 hover:bg-[#001a38] hover:scale-105 transition-all">Simpan Data</button>
            </div>
        </div>
    </div>
    @endif

    {{-- Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        {{-- Search Header --}}
        <div class="p-4 bg-slate-50/50 border-b flex items-center gap-4">
            <div class="relative flex-1">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari Prodi..." class="w-full pl-10 pr-4 py-2 rounded-xl border-slate-200 text-sm focus:ring-[#002855] focus:border-[#002855] transition-shadow outline-none font-bold text-slate-700">
                <svg class="w-4 h-4 absolute left-3 top-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#002855] border-b border-[#001a38] text-white">
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest">Kode</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest">Program Studi</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest">Fakultas</th>
                        <th class="px-6 py-4 text-center text-[10px] font-bold uppercase tracking-widest">Jenjang</th>
                        <th class="px-6 py-4 text-left text-[10px] font-bold uppercase tracking-widest">Kaprodi (HR)</th>
                        <th class="px-6 py-4 text-right text-[10px] font-bold uppercase tracking-widest">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($prodis as $p)
                    <tr class="hover:bg-slate-50/80 transition-colors group">
                        <td class="px-6 py-4 align-top">
                            <div class="font-mono text-xs font-bold text-[#002855] bg-indigo-50 px-2 py-0.5 rounded w-fit">{{ $p->kode_prodi_internal }}</div>
                        </td>
                        <td class="px-6 py-4 align-top">
                            <div class="text-sm font-bold text-slate-800">{{ $p->nama_prodi }}</div>
                        </td>
                        <td class="px-6 py-4 align-top">
                            <div class="text-xs font-medium text-slate-600">{{ $p->fakultas->nama_fakultas }}</div>
                        </td>
                        <td class="px-6 py-4 align-top text-center">
                            <span class="px-2.5 py-0.5 inline-flex text-[10px] font-black leading-5 rounded-md bg-[#fcc000]/20 text-[#002855] border border-[#fcc000]/30 uppercase tracking-wide">
                                {{ $p->jenjang }}
                            </span>
                        </td>
                        <td class="px-6 py-4 align-top text-sm text-slate-500">
                             {{-- Menggunakan Accessor getKaprodiAttribute --}}
                             {{ $p->kaprodi }}
                        </td>
                        <td class="px-6 py-4 align-top text-right">
                            <div class="flex justify-end gap-2 opacity-60 group-hover:opacity-100 transition-opacity">
                                <button wire:click="edit({{ $p->id }})" class="p-2 text-[#002855] hover:bg-[#002855]/10 rounded-lg transition-colors" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                </button>
                                <button wire:click="delete({{ $p->id }})" wire:confirm="Hapus Prodi ini?" class="p-2 text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center">
                            <p class="text-slate-400 font-medium italic">Tidak ada data program studi.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
            {{ $prodis->links() }}
        </div>
    </div>
</div>