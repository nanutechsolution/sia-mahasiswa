<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900">Master Program Studi</h1>
        @if(!$showForm)
        <button wire:click="create" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 font-bold text-sm shadow-sm transition-all">
            + Tambah Prodi
        </button>
        @endif
    </div>

    @if (session()->has('success'))
        <div class="bg-green-50 p-4 rounded-lg border border-green-100 text-green-800 text-sm font-medium animate-in fade-in">{{ session('success') }}</div>
    @endif
    @if (session()->has('error'))
        <div class="bg-red-50 p-4 rounded-lg border border-red-100 text-red-800 text-sm font-medium animate-in fade-in">{{ session('error') }}</div>
    @endif

    @if($showForm)
    <div class="bg-white p-6 shadow-lg rounded-xl border border-slate-200 animate-in slide-in-from-top-4 duration-300">
        <div class="flex justify-between items-center mb-6 pb-4 border-b border-slate-100">
            <h3 class="text-lg font-bold text-slate-800">
                {{ $editMode ? 'Edit Data Prodi' : 'Tambah Prodi Baru' }}
            </h3>
            <button wire:click="$set('showForm', false)" class="text-slate-400 hover:text-slate-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="col-span-2">
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Fakultas</label>
                <select wire:model="fakultas_id" class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    <option value="">-- Pilih Fakultas --</option>
                    @foreach($fakultas_list as $f)
                        <option value="{{ $f->id }}">{{ $f->nama_fakultas }}</option>
                    @endforeach
                </select>
                @error('fakultas_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>
            
            <div class="col-span-2">
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Nama Prodi</label>
                <input type="text" wire:model="nama_prodi" class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="Contoh: Teknik Informatika">
                @error('nama_prodi') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Kode Internal</label>
                <input type="text" wire:model="kode_prodi_internal" class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="Contoh: TI">
                @error('kode_prodi_internal') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Kode Dikti</label>
                <input type="text" wire:model="kode_prodi_dikti" class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="Contoh: 55201">
            </div>
            
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Jenjang</label>
                <select wire:model="jenjang" class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    <option value="D3">D3</option>
                    <option value="D4">D4</option>
                    <option value="S1">S1</option>
                    <option value="S2">S2</option>
                    <option value="S3">S3</option>
                    <option value="PROFESI">PROFESI</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Gelar Lulusan</label>
                <input type="text" wire:model="gelar_lulusan" class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="Contoh: S.Kom">
            </div>

            <!-- FORMAT NIM CONFIG -->
            <div class="col-span-2 lg:col-span-4 bg-indigo-50 p-4 rounded-lg border border-indigo-100 mt-2">
                <label class="block text-xs font-black text-indigo-700 uppercase mb-1">Format Auto-Generate NIM</label>
                <div class="flex gap-4 items-start">
                    <div class="flex-1">
                        <input type="text" wire:model="format_nim" class="block w-full rounded-lg border-indigo-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm font-mono tracking-wide" placeholder="Contoh: {THN}{KODE}{NO:4}">
                        <p class="text-xs text-indigo-600 mt-2 leading-relaxed">
                            <strong>Variabel Tersedia:</strong><br>
                            <code class="bg-white px-1 rounded border border-indigo-200">{THN}</code> : 2 digit tahun (24)<br>
                            <code class="bg-white px-1 rounded border border-indigo-200">{TAHUN}</code> : 4 digit tahun (2024)<br>
                            <code class="bg-white px-1 rounded border border-indigo-200">{KODE}</code> : Kode Prodi Dikti (55201)<br>
                            <code class="bg-white px-1 rounded border border-indigo-200">{INTERNAL}</code> : Kode Internal (TI)<br>
                            <code class="bg-white px-1 rounded border border-indigo-200">{NO:3}</code> : Nomor Urut 3 digit (001)
                        </p>
                    </div>
                    <div class="hidden md:block w-1/3">
                        <div class="bg-white p-3 rounded border border-indigo-200 text-xs text-slate-500">
                            <strong>Contoh Hasil:</strong><br>
                            Input: <span class="font-mono text-slate-700">{THN}{KODE}{NO:4}</span><br>
                            Output: <span class="font-mono font-bold text-indigo-700">24552010001</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-8 flex justify-end gap-3 pt-4 border-t border-slate-100">
            <button wire:click="$set('showForm', false)" class="px-6 py-2.5 text-slate-500 hover:text-slate-700 hover:bg-slate-50 rounded-lg text-sm font-bold transition-all">Batal</button>
            <button wire:click="save" class="px-8 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm font-bold shadow-lg shadow-indigo-200 hover:scale-105 transition-all">Simpan Data</button>
        </div>
    </div>
    @endif

    <div class="bg-white shadow-sm rounded-xl border border-slate-200 overflow-hidden">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-widest">Kode</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-widest">Program Studi</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-widest">Fakultas</th>
                    <th class="px-6 py-4 text-center text-xs font-bold text-slate-400 uppercase tracking-widest">Jenjang</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-widest">Format NIM</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-slate-400 uppercase tracking-widest">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-slate-100">
                @foreach($prodis as $p)
                <tr class="hover:bg-slate-50/80 transition-colors group">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-indigo-600">{{ $p->kode_prodi_internal }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-800 font-medium">{{ $p->nama_prodi }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">{{ $p->fakultas->nama_fakultas }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                        <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-bold rounded-md bg-blue-50 text-blue-700 border border-blue-100">
                            {{ $p->jenjang }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 font-mono text-xs">
                        {{ $p->format_nim ?? '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex justify-end gap-2 opacity-60 group-hover:opacity-100 transition-opacity">
                            <button wire:click="edit({{ $p->id }})" class="text-indigo-600 hover:text-indigo-800 hover:bg-indigo-50 px-3 py-1.5 rounded-lg transition-colors">Edit</button>
                            <button wire:click="delete({{ $p->id }})" wire:confirm="Hapus Prodi ini?" class="text-rose-600 hover:text-rose-800 hover:bg-rose-50 px-3 py-1.5 rounded-lg transition-colors">Hapus</button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @if($prodis->hasPages())
        <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
            {{ $prodis->links() }}
        </div>
        @endif
    </div>
</div>