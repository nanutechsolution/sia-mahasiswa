<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Manajemen Role & Hak Akses</h1>
            <p class="text-sm text-gray-500">Atur peran pengguna dalam sistem (RBAC).</p>
        </div>
        @if(!$showForm)
        <button wire:click="create" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 font-bold text-sm shadow-sm">
            + Tambah Role Baru
        </button>
        @endif
    </div>

    @if (session()->has('success'))
        <div class="bg-green-50 p-4 rounded text-green-800 text-sm font-bold border border-green-100">{{ session('success') }}</div>
    @endif
    @if (session()->has('error'))
        <div class="bg-red-50 p-4 rounded text-red-800 text-sm font-bold border border-red-100">{{ session('error') }}</div>
    @endif

    <!-- Form Input -->
    @if($showForm)
    <div class="bg-white p-6 shadow rounded-lg border border-gray-200 animate-in fade-in slide-in-from-top-4">
        <h3 class="text-lg font-bold text-gray-900 mb-4 border-b pb-2">
            {{ $editMode ? 'Edit Role' : 'Role Baru' }}
        </h3>
        <div class="max-w-md">
            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nama Role (Huruf Kecil)</label>
            <input type="text" wire:model="name" placeholder="contoh: laboran, perpustakaan" class="block w-full rounded border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            <p class="text-xs text-gray-400 mt-1">*Gunakan huruf kecil, tanpa spasi (gunakan underscore _ jika perlu).</p>
        </div>
        <div class="mt-6 flex gap-3">
            <button wire:click="save" class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700 text-sm font-bold shadow-md">Simpan</button>
            <button wire:click="$set('showForm', false)" class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded text-sm font-medium">Batal</button>
        </div>
    </div>
    @endif

    <!-- Tabel Data -->
    <div class="bg-white shadow overflow-hidden sm:rounded-lg border border-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Nama Role</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Guard</th>
                    <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Jumlah User</th>
                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @foreach($roles as $role)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 capitalize">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                            {{ in_array($role->name, ['superadmin', 'admin']) ? 'bg-purple-100 text-purple-800' : 'bg-indigo-100 text-indigo-800' }}">
                            {{ $role->name }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-mono text-xs">
                        {{ $role->guard_name }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center font-bold">
                        {{ $role->users_count }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <button wire:click="edit({{ $role->id }})" class="text-indigo-600 hover:text-indigo-900 mr-3 font-bold">Edit</button>
                        
                        @if(!in_array($role->name, ['superadmin', 'dosen', 'mahasiswa', 'admin']))
                            <button wire:click="delete({{ $role->id }})" wire:confirm="Hapus role ini?" class="text-red-600 hover:text-red-900 font-bold">Hapus</button>
                        @else
                            <span class="text-gray-300 cursor-not-allowed text-xs italic font-semibold">Locked (System)</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-4 border-t border-gray-200 bg-gray-50">
            {{ $roles->links() }}
        </div>
    </div>
</div>