<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Manajemen Staff & Admin</h1>
            <p class="text-sm text-gray-500">Kelola akun pengguna internal (Non-Dosen & Non-Mahasiswa).</p>
        </div>
        @if(!$showForm)
        <button wire:click="create" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 font-bold text-sm shadow-sm">
            + Tambah User Baru
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
    <div class="bg-white p-6 shadow rounded-lg border border-gray-200">
        <h3 class="text-lg font-bold text-gray-900 mb-4 border-b pb-2">
            {{ $editMode ? 'Edit User' : 'Buat User Baru' }}
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nama Lengkap</label>
                <input type="text" wire:model="name" class="block w-full rounded border-gray-300 shadow-sm focus:border-indigo-500 text-sm">
                @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Username Login</label>
                <input type="text" wire:model="username" class="block w-full rounded border-gray-300 shadow-sm focus:border-indigo-500 text-sm">
                @error('username') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Email</label>
                <input type="email" wire:model="email" class="block w-full rounded border-gray-300 shadow-sm focus:border-indigo-500 text-sm">
                @error('email') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">
                    {{ $editMode ? 'Password (Isi jika ingin ubah)' : 'Password' }}
                </label>
                <input type="password" wire:model="password" class="block w-full rounded border-gray-300 shadow-sm focus:border-indigo-500 text-sm">
                @error('password') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>
            
            <div class="md:col-span-2">
                <label class="block text-xs font-bold text-gray-700 uppercase mb-2">Role / Hak Akses</label>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                    @foreach($allRoles as $role)
                    <label class="flex items-center space-x-2 border p-3 rounded cursor-pointer hover:bg-gray-50">
                        <input type="checkbox" wire:model="selectedRoles" value="{{ $role->name }}" class="rounded text-indigo-600 focus:ring-indigo-500">
                        <span class="text-sm font-medium capitalize">{{ $role->name }}</span>
                    </label>
                    @endforeach
                </div>
                @error('selectedRoles') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="flex items-center space-x-2 mt-4">
                    <input type="checkbox" wire:model="is_active" class="rounded text-indigo-600 focus:ring-indigo-500">
                    <span class="text-sm font-bold text-gray-700">Akun Aktif?</span>
                </label>
            </div>
        </div>

        <div class="mt-6 flex gap-3 pt-4 border-t">
            <button wire:click="save" class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700 text-sm font-bold shadow-md">Simpan User</button>
            <button wire:click="$set('showForm', false)" class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded text-sm font-medium">Batal</button>
        </div>
    </div>
    @endif

    <div class="bg-white shadow overflow-hidden sm:rounded-lg border border-gray-200">
        <div class="p-4 border-b border-gray-200 bg-gray-50">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama / username..." class="w-full max-w-sm rounded border-gray-300 text-sm">
        </div>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">User Info</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Role</th>
                    <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @foreach($users as $user)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-bold text-gray-900">{{ $user->name }}</div>
                        <div class="text-xs text-gray-500">{{ $user->email }}</div>
                        <div class="text-xs text-indigo-500 font-mono mt-1">{{ $user->username }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex flex-wrap gap-1">
                            @foreach($user->roles as $role)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-blue-100 text-blue-800 capitalize">
                                    {{ $role->name }}
                                </span>
                            @endforeach
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $user->is_active ? 'Aktif' : 'Non-Aktif' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <button wire:click="edit('{{ $user->id }}')" class="text-indigo-600 hover:text-indigo-900 mr-3 font-bold">Edit</button>
                        
                        @if(!$user->hasRole('superadmin'))
                            <button wire:click="delete('{{ $user->id }}')" wire:confirm="Hapus user ini?" class="text-red-600 hover:text-red-900 font-bold">Hapus</button>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-4 border-t border-gray-200 bg-gray-50">
            {{ $users->links() }}
        </div>
    </div>
</div>