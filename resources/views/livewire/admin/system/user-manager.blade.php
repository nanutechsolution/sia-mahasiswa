<div class="space-y-6">
    {{-- Header & Toolbar --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-[#002855]">Manajemen Pengguna</h1>
            <p class="text-slate-500 text-sm mt-1">Kelola akun, hak akses, dan status pengguna sistem.</p>
        </div>
        @if(!$showForm)
        <button wire:click="create" class="inline-flex items-center px-5 py-2.5 bg-[#fcc000] text-[#002855] rounded-xl font-black text-sm shadow-lg shadow-orange-500/20 hover:bg-[#fbbf24] hover:scale-105 transition-all">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" /></svg>
            Tambah User
        </button>
        @endif
    </div>

    {{-- Alert Messages --}}
    @if (session()->has('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-100 text-emerald-800 rounded-xl text-sm font-bold flex items-center shadow-sm animate-in fade-in">
            <svg class="w-5 h-5 mr-3 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
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
            <h3 class="text-sm font-bold text-[#002855] uppercase tracking-wider flex items-center gap-2">
                @if($editMode) Edit Pengguna @else Buat Akun Baru @endif
            </h3>
            <button wire:click="batal" class="text-slate-400 hover:text-slate-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>
        
        <div class="p-8 space-y-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Kiri: Data Akun --}}
                <div class="space-y-4">
                    <h4 class="text-xs font-black text-[#002855] uppercase border-l-4 border-[#fcc000] pl-3 tracking-widest">Data Login</h4>
                    
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Username</label>
                        <input type="text" wire:model="username" class="block w-full rounded-xl border-slate-200 bg-slate-50 text-slate-900 py-2.5 pl-4 text-sm font-bold focus:ring-2 focus:ring-[#fcc000] focus:outline-none transition-all">
                        @error('username') <span class="text-rose-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Email</label>
                        <input type="email" wire:model="email" class="block w-full rounded-xl border-slate-200 bg-slate-50 text-slate-900 py-2.5 pl-4 text-sm font-bold focus:ring-2 focus:ring-[#fcc000] focus:outline-none transition-all">
                        @error('email') <span class="text-rose-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">
                            {{ $editMode ? 'Password (Opsional)' : 'Password Wajib' }}
                        </label>
                        <input type="password" wire:model="password" class="block w-full rounded-xl border-slate-200 bg-slate-50 text-slate-900 py-2.5 pl-4 text-sm font-bold focus:ring-2 focus:ring-[#fcc000] focus:outline-none transition-all" placeholder="******">
                        @error('password') <span class="text-rose-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                {{-- Kanan: Role & Profil --}}
                <div class="space-y-4">
                    <h4 class="text-xs font-black text-[#002855] uppercase border-l-4 border-slate-300 pl-3 tracking-widest">Hak Akses & Profil</h4>
                    
                    {{-- Link ke SSOT Person --}}
                    <div x-data="{ open: false }" class="relative">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Hubungkan ke Personil (SSOT)</label>
                        <input type="text" wire:model.live="searchPerson" @focus="open = true" @click.away="open = false" 
                            placeholder="{{ $selectedPersonName ?: '-- Cari Nama Personil --' }}" 
                            class="block w-full rounded-xl border-slate-200 bg-white text-slate-900 py-2.5 pl-4 text-sm font-bold focus:ring-2 focus:ring-[#fcc000] focus:outline-none transition-all cursor-pointer">
                        
                        @if(!empty($searchPerson) && !empty($persons))
                        <div x-show="open" class="absolute z-10 w-full bg-white shadow-xl max-h-48 rounded-xl py-1 mt-1 overflow-auto border border-slate-100">
                            @foreach($persons as $p)
                            <div wire:click="selectPerson('{{ $p->id }}', '{{ $p->nama_lengkap }}')" @click="open = false" class="px-4 py-2 hover:bg-indigo-50 cursor-pointer border-b border-slate-50 last:border-0">
                                <p class="text-sm font-bold text-[#002855]">{{ $p->nama_lengkap }}</p>
                                <p class="text-[10px] text-slate-500">{{ $p->nik ?? '-' }}</p>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Nama Tampilan</label>
                        <input type="text" wire:model="name" class="block w-full rounded-xl border-slate-200 bg-slate-50 text-slate-900 py-2.5 pl-4 text-sm font-bold focus:ring-2 focus:ring-[#fcc000] focus:outline-none transition-all">
                        @error('name') <span class="text-rose-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Role Utama</label>
                            <select wire:model="role" class="block w-full rounded-xl border-slate-200 bg-white text-slate-900 py-2.5 pl-4 text-sm font-bold focus:ring-2 focus:ring-[#fcc000] focus:outline-none transition-all">
                                <option value="superadmin">Superadmin</option>
                                <option value="admin">Administrator</option>
                                <option value="baak">BAAK</option>
                                <option value="keuangan">Keuangan</option>
                                <option value="dosen">Dosen</option>
                                <option value="mahasiswa">Mahasiswa</option>
                            </select>
                        </div>
                        <div class="pt-6">
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" wire:model="is_active" class="rounded border-slate-300 text-[#002855] shadow-sm focus:border-[#002855] focus:ring focus:ring-[#002855] focus:ring-opacity-50 h-5 w-5">
                                <span class="ml-2 text-sm font-bold text-slate-700">Akun Aktif</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-6 border-t border-slate-100 flex justify-end gap-3">
                <button wire:click="batal" class="px-6 py-2.5 text-sm font-bold text-slate-500 hover:text-slate-700 hover:bg-slate-100 rounded-xl transition-all">Batal</button>
                <button wire:click="save" class="px-8 py-2.5 bg-[#002855] text-white rounded-xl text-sm font-bold shadow-lg hover:bg-[#001a38] hover:scale-105 transition-all">Simpan User</button>
            </div>
        </div>
    </div>
    @endif

    {{-- Data Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200">
        <div class="p-4 bg-slate-50/50 border-b flex items-center gap-4 rounded-t-2xl">
            <div class="relative flex-1">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari User..." class="w-full pl-10 pr-4 py-2 rounded-xl border-slate-200 text-sm focus:ring-[#002855] focus:border-[#002855] transition-shadow outline-none font-bold text-slate-700">
                <svg class="w-4 h-4 absolute left-3 top-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
        </div>

        {{-- Gunakan overflow-visible agar dropdown tidak terpotong --}}
        <div class="overflow-visible min-h-[400px]">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#002855] border-b border-[#001a38] text-white">
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest">Pengguna</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest">Role</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest">Link SSOT</th>
                        <th class="px-6 py-4 text-center text-[10px] font-bold uppercase tracking-widest">Status</th>
                        <th class="px-6 py-4 text-right text-[10px] font-bold uppercase tracking-widest">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($users as $user)
                    {{-- WIRE:KEY SANGAT PENTING --}}
                    <tr class="hover:bg-slate-50/80 transition-colors group" wire:key="user-row-{{ $user->id }}">
                        <td class="px-6 py-4 align-top">
                            <div class="flex items-center">
                                <div class="h-8 w-8 rounded-full bg-slate-200 flex items-center justify-center text-slate-500 font-bold text-xs mr-3">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="text-sm font-black text-slate-800">{{ $user->name }}</div>
                                    <div class="text-[10px] font-mono text-slate-400 mt-0.5">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 align-top">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wide bg-indigo-50 text-[#002855] border border-indigo-100">
                                {{ $user->role }}
                            </span>
                        </td>
                        <td class="px-6 py-4 align-top">
                            @if($user->person)
                                <div class="flex items-center text-emerald-600 gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" /></svg>
                                    <span class="text-[10px] font-bold">{{ $user->person->nama_lengkap }}</span>
                                </div>
                            @else
                                <span class="text-[10px] text-slate-400 italic">Belum terhubung</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 align-top text-center">
                            @if($user->is_active)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[9px] font-bold uppercase bg-emerald-100 text-emerald-700">Aktif</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[9px] font-bold uppercase bg-rose-100 text-rose-700">Non-Aktif</span>
                            @endif
                        </td>
                        
                        {{-- MENU DROPDOWN --}}
                        <td class="px-6 py-4 text-right align-middle">
                            <div x-data="{ open: false }" class="relative inline-block text-left">
                                <button @click="open = !open" @click.outside="open = false" class="p-2 text-slate-400 hover:text-[#002855] hover:bg-slate-100 rounded-lg transition-colors focus:outline-none">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" /></svg>
                                </button>
                                
                                <div x-show="open" 
                                     x-transition:enter="transition ease-out duration-100"
                                     x-transition:enter-start="transform opacity-0 scale-95"
                                     x-transition:enter-end="transform opacity-100 scale-100"
                                     class="absolute right-0 mt-2 w-48 rounded-xl shadow-xl bg-white ring-1 ring-black ring-opacity-5 z-50 origin-top-right border border-slate-100" 
                                     style="display: none;">
                                    <div class="py-1">
                                        <button wire:click="edit('{{ $user->id }}')" @click="open = false" class="flex w-full items-center px-4 py-2.5 text-xs font-bold text-slate-600 hover:bg-indigo-50 hover:text-[#002855] transition-colors">
                                            <svg class="mr-3 h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            Edit Data
                                        </button>
                                        <button wire:click="openResetPassword('{{ $user->id }}')" @click="open = false" class="flex w-full items-center px-4 py-2.5 text-xs font-bold text-slate-600 hover:bg-indigo-50 hover:text-[#002855] transition-colors">
                                            <svg class="mr-3 h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
                                            Reset Password
                                        </button>
                                        <div class="border-t border-slate-100 my-1"></div>
                                        <button wire:click="delete('{{ $user->id }}')" wire:confirm="Yakin hapus user ini?" @click="open = false" class="flex w-full items-center px-4 py-2.5 text-xs font-bold text-rose-500 hover:bg-rose-50 transition-colors">
                                            <svg class="mr-3 h-4 w-4 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            Hapus Permanen
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-6 py-16 text-center text-slate-400 italic">Data tidak ditemukan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">{{ $users->links() }}</div>
    </div>

    {{-- MODAL RESET PASSWORD --}}
    @if($showResetModal)
    <div class="fixed inset-0 z-[999] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 animate-in fade-in">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden border border-white/20">
            <div class="bg-[#002855] px-8 py-5 text-white flex justify-between items-center">
                <h3 class="text-lg font-black uppercase tracking-widest">Reset Password</h3>
                <button wire:click="closeResetModal" class="text-white/50 hover:text-white text-2xl">&times;</button>
            </div>
            <div class="p-8 space-y-6">
                <div class="bg-indigo-50 p-4 rounded-xl border border-indigo-100 text-sm text-indigo-900">
                    Reset password untuk akun: <strong>{{ $resetUsername }}</strong>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Password Baru</label>
                    <input type="password" wire:model="new_password_reset" class="w-full rounded-xl border-slate-200 text-sm font-bold py-3 px-4 focus:ring-2 focus:ring-[#fcc000] outline-none" placeholder="Masukkan password baru">
                    @error('new_password_reset') <span class="text-rose-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div class="flex gap-3 pt-2">
                    <button wire:click="closeResetModal" class="flex-1 py-3 border border-slate-200 text-slate-500 rounded-xl font-bold text-xs uppercase hover:bg-slate-50">Batal</button>
                    <button wire:click="processResetPassword" class="flex-1 py-3 bg-[#fcc000] text-[#002855] rounded-xl font-bold text-xs uppercase hover:bg-[#e6b000] shadow-lg">Simpan Password</button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>