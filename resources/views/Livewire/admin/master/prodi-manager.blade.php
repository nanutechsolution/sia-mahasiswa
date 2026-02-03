<div class="space-y-6">
    {{-- Header & Toolbar --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-unmaris-blue">Master Program Studi</h1>
            <p class="text-slate-500 text-sm mt-1">Kelola jurusan, jenjang, format NIM, dan kebijakan paket SKS.</p>
        </div>
        @if(!$showForm)
        <button wire:click="create" class="inline-flex items-center px-5 py-2.5 bg-unmaris-blue text-white rounded-xl font-black text-sm shadow-lg shadow-unmaris-blue-500/20 hover:bg-unmaris-gold hover:scale-105 transition-all hover:text-unmaris-blue">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Prodi
        </button>
        @endif
    </div>

    {{-- Alert Messages --}}
    @if (session()->has('success'))
    <div class="p-4 bg-emerald-50 border border-emerald-100 text-emerald-800 rounded-xl text-sm font-bold flex items-center shadow-sm animate-in fade-in">
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

    {{-- Form Section --}}
    @if($showForm)
    <div class="bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden animate-in zoom-in-95 duration-200">
        <div class="px-8 py-5 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
            <h3 class="text-sm font-bold text-unmaris-blue uppercase tracking-wider flex items-center gap-2">
                @if($editMode) Edit Program Studi @else Tambah Prodi Baru @endif
            </h3>
            <button wire:click="batal" class="text-slate-400 hover:text-slate-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="p-8 space-y-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

                <div class="col-span-2">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Fakultas Induk</label>
                    <select wire:model="fakultas_id" class="w-full rounded-xl border-slate-200 bg-slate-50 text-slate-900 py-2.5 pl-4 text-sm font-bold focus:ring-unmaris-blue focus:border-unmaris-blue">
                        <option value="">-- Pilih Fakultas --</option>
                        @foreach($fakultas_list as $f)
                        <option value="{{ $f->id }}">{{ $f->nama_fakultas }}</option>
                        @endforeach
                    </select>
                    @error('fakultas_id') <span class="text-rose-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="col-span-2">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Nama Prodi</label>
                    <input type="text" wire:model="nama_prodi" class="w-full rounded-xl border-slate-200 bg-slate-50 text-slate-900 py-2.5 pl-4 text-sm font-bold focus:ring-unmaris-blue placeholder-slate-400" placeholder="Teknik Informatika">
                    @error('nama_prodi') <span class="text-rose-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Kode Internal</label>
                    <input type="text" wire:model="kode_prodi_internal" class="w-full rounded-xl border-slate-200 bg-slate-50 text-slate-900 py-2.5 pl-4 text-sm font-bold uppercase focus:ring-unmaris-blue" placeholder="TI">
                    @error('kode_prodi_internal') <span class="text-rose-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Kode Dikti</label>
                    <input type="text" wire:model="kode_prodi_dikti" class="w-full rounded-xl border-slate-200 bg-slate-50 text-slate-900 py-2.5 pl-4 text-sm font-bold focus:ring-unmaris-blue" placeholder="55201">
                </div>

                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Jenjang</label>
                    <select wire:model="jenjang" class="w-full rounded-xl border-slate-200 bg-slate-50 text-slate-900 py-2.5 pl-4 text-sm font-bold focus:ring-unmaris-blue">
                        <option value="D3">D3</option>
                        <option value="D4">D4</option>
                        <option value="S1">S1</option>
                        <option value="S2">S2</option>
                        <option value="S3">S3</option>
                        <option value="PROFESI">PROFESI</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Gelar Lulusan</label>
                    <input type="text" wire:model="gelar_lulusan" class="w-full rounded-xl border-slate-200 bg-slate-50 text-slate-900 py-2.5 pl-4 text-sm font-bold focus:ring-unmaris-blue" placeholder="S.Kom">
                </div>

                {{-- Format NIM --}}
                <div class="col-span-2 lg:col-span-4 bg-indigo-50/50 p-4 rounded-xl border border-indigo-100 mt-2">
                    <label class="block text-[10px] font-black text-indigo-700 uppercase tracking-widest mb-2">Format Auto-Generate NIM</label>
                    <div class="flex flex-col md:flex-row gap-4 items-start">
                        <div class="flex-1 w-full">
                            <input type="text" wire:model="format_nim" class="w-full rounded-xl border-indigo-200 bg-white text-indigo-900 py-2.5 pl-4 text-sm font-mono font-bold focus:ring-unmaris-blue">
                            <p class="text-[10px] text-indigo-500/80 mt-2 font-medium">
                                Variabel: <span class="bg-white px-1 rounded border border-indigo-100">{THN}</span> (2 Digit Thn), <span class="bg-white px-1 rounded border border-indigo-100">{TAHUN}</span> (4 Digit), <span class="bg-white px-1 rounded border border-indigo-100">{KODE}</span> (Kode Dikti), <span class="bg-white px-1 rounded border border-indigo-100">{INTERNAL}</span>, <span class="bg-white px-1 rounded border border-indigo-100">{NO:3}</span> (Urutan).
                            </p>
                        </div>
                        <div class="w-full md:w-auto bg-white p-3 rounded-lg border border-indigo-100 shadow-sm">
                            <p class="text-[10px] font-bold text-slate-400 uppercase">Preview:</p>
                            <p class="text-sm font-mono font-black text-unmaris-blue">2555201001</p>
                        </div>
                    </div>
                </div>

                {{-- Settings --}}
                <div class="col-span-2 lg:col-span-4 grid grid-cols-1 md:grid-cols-2 gap-6 mt-2 pt-6 border-t border-slate-100">
                    <div class="bg-amber-50 p-4 rounded-xl border border-amber-100">
                        <label class="flex items-center cursor-pointer gap-3">
                            <input type="checkbox" wire:model="is_paket" class="w-5 h-5 text-amber-500 rounded border-gray-300 focus:ring-amber-500">
                            <div>
                                <span class="block text-sm font-bold text-slate-800">Sistem Paket (KRS Otomatis)</span>
                                <span class="block text-[10px] text-slate-500 mt-0.5">Jika aktif, mahasiswa semester baru akan otomatis mendapatkan MK paket sesuai kurikulum.</span>
                            </div>
                        </label>
                    </div>
                    <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                        <label class="flex items-center cursor-pointer gap-3">
                            <input type="checkbox" wire:model="is_active" class="w-5 h-5 text-unmaris-blue rounded border-gray-300 focus:ring-unmaris-blue">
                            <span class="block text-sm font-bold text-slate-800">Prodi Aktif</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="pt-6 border-t border-slate-100 flex justify-end gap-3">
                <button wire:click="batal" class="px-6 py-2.5 text-sm font-bold text-slate-500 hover:text-slate-700 hover:bg-slate-100 rounded-xl transition-all">Batal</button>
                <button wire:click="save" class="px-8 py-2.5 bg-unmaris-blue text-white rounded-xl text-sm font-bold shadow-lg hover:bg-[#001a38] transition-all hover:scale-105 trantition-all">Simpan Data</button>
            </div>
        </div>
    </div>
    @endif

    {{-- Data Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-4 bg-slate-50/50 border-b flex items-center gap-4 rounded-t-2xl">
            <div class="relative flex-1">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari Prodi..." class="w-full pl-10 pr-4 py-2 rounded-xl border-slate-200 text-sm focus:ring-unmaris-blue focus:border-unmaris-blue transition-shadow outline-none font-bold text-slate-700 placeholder-slate-400">
                <svg class="w-4 h-4 absolute left-3 top-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-unmaris-blue border-b border-[#001a38] text-white">
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest">Kode</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest">Nama Prodi</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest">Fakultas</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest">Jenjang</th>
                        <th class="px-6 py-4 text-center text-[10px] font-bold uppercase tracking-widest">Paket</th>
                        <th class="px-6 py-4 text-center text-[10px] font-bold uppercase tracking-widest">Status</th>
                        <th class="px-6 py-4 text-right text-[10px] font-bold uppercase tracking-widest">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($prodis as $p)
                    <tr class="hover:bg-slate-50/80 transition-colors group">
                        <td class="px-6 py-4 align-middle">
                            <span class="font-mono text-xs font-black text-unmaris-blue bg-indigo-50 px-2 py-0.5 rounded border border-indigo-100">{{ $p->kode_prodi_internal }}</span>
                            @if($p->kode_prodi_dikti)
                            <span class="block text-[9px] text-slate-400 mt-1 font-mono">DIKTI: {{ $p->kode_prodi_dikti }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 align-middle">
                            <div class="text-sm font-bold text-slate-800">{{ $p->nama_prodi }}</div>
                            <div class="text-[10px] text-slate-400 mt-0.5 font-bold">Kaprodi: <span class="text-indigo-600">{{ $p->kaprodi }}</span></div>
                        </td>
                        <td class="px-6 py-4 align-middle text-xs font-medium text-slate-600">
                            {{ $p->fakultas->nama_fakultas ?? '-' }}
                        </td>
                        <td class="px-6 py-4 align-middle">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-700 uppercase tracking-wide border border-slate-200">
                                {{ $p->jenjang }}
                            </span>
                        </td>
                        <td class="px-6 py-4 align-middle text-center">
                            @if($p->is_paket)
                            <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-amber-100 text-amber-600 font-bold text-xs" title="Sistem Paket">✓</span>
                            @else
                            <span class="text-slate-300 font-bold text-xs">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 align-middle text-center">
                            @if($p->is_active)
                            <span class="inline-flex h-2 w-2 rounded-full bg-emerald-500" title="Aktif"></span>
                            @else
                            <span class="inline-flex h-2 w-2 rounded-full bg-rose-500" title="Non-Aktif"></span>
                            @endif
                        </td>
                        <td class="px-6 py-4 align-middle text-right space-x-2">
                            <button wire:click="edit({{ $p->id }})" class="text-unmaris-blue hover:text-indigo-600 font-bold text-[10px] uppercase transition-colors">Edit</button>
                            <button wire:click="delete({{ $p->id }})" wire:confirm="Hapus Prodi ini?" class="text-rose-500 hover:text-rose-700 font-bold text-[10px] uppercase transition-colors">Hapus</button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-16 text-center text-slate-400 italic">Data tidak ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">{{ $prodis->links() }}</div>
    </div>
</div>