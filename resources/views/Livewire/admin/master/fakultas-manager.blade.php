<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Master Fakultas</h1>
            <p class="text-sm text-slate-500">Data unit fakultas di lingkungan universitas.</p>
        </div>
        @if(!$showForm)
        <button wire:click="create" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 font-bold text-sm shadow-sm transition-all">
            + Tambah Fakultas
        </button>
        @endif
    </div>

    @if (session()->has('success'))
        <div class="bg-green-50 p-4 rounded text-green-800 text-sm font-bold border border-green-100">{{ session('success') }}</div>
    @endif
    @if (session()->has('error'))
        <div class="bg-red-50 p-4 rounded text-red-800 text-sm font-bold border border-red-100">{{ session('error') }}</div>
    @endif

    @if($showForm)
    <div class="bg-white p-6 shadow rounded-lg border border-gray-200 animate-in slide-in-from-top-4">
        <h3 class="text-lg font-bold text-gray-900 mb-4 border-b pb-2">
            {{ $editMode ? 'Edit Fakultas' : 'Tambah Fakultas Baru' }}
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Kode Fakultas (Ex: FT)</label>
                <input type="text" wire:model="kode_fakultas" class="block w-full rounded border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm uppercase">
                @error('kode_fakultas') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nama Fakultas</label>
                <input type="text" wire:model="nama_fakultas" class="block w-full rounded border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                @error('nama_fakultas') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>
        </div>
        <div class="mt-6 flex justify-end gap-3">
            <button wire:click="$set('showForm', false)" class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded text-sm font-bold">Batal</button>
            <button wire:click="save" class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700 text-sm font-bold shadow-md">Simpan</button>
        </div>
    </div>
    @endif

    <div class="bg-white shadow overflow-hidden sm:rounded-lg border border-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-black text-slate-500 uppercase tracking-widest">Kode</th>
                    <th class="px-6 py-3 text-left text-xs font-black text-slate-500 uppercase tracking-widest">Nama Fakultas</th>
                    <th class="px-6 py-3 text-left text-xs font-black text-slate-500 uppercase tracking-widest">Dekan Aktif (HR)</th>
                    <th class="px-6 py-3 text-right text-xs font-black text-slate-500 uppercase tracking-widest">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @foreach($fakultas as $f)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-black text-indigo-600">{{ $f->kode_fakultas }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-700">{{ $f->nama_fakultas }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                        {{-- Mengambil data dari Accessor getDekanAttribute --}}
                        {{ $f->dekan }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                        <button wire:click="edit({{ $f->id }})" class="text-indigo-600 hover:text-indigo-900 font-bold text-xs uppercase">Edit</button>
                        <button wire:click="delete({{ $f->id }})" wire:confirm="Hapus fakultas ini?" class="text-rose-600 hover:text-rose-900 font-bold text-xs uppercase">Hapus</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-4 border-t border-gray-200 bg-slate-50">
            {{ $fakultas->links() }}
        </div>
    </div>
</div>