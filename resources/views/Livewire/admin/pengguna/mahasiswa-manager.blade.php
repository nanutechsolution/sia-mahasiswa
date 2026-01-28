<div class="space-y-6">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Data Mahasiswa</h1>
            <p class="mt-2 text-sm text-gray-700">Manajemen biodata, dispensasi, dan akun login mahasiswa aktif.</p>
        </div>
        <div class="mt-4 sm:mt-0">
            @if(!$showForm)
                <button wire:click="create" class="inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700">
                    + Input Mahasiswa Baru
                </button>
            @endif
        </div>
    </div>

    <!-- Filter -->
    <div class="bg-white p-4 shadow sm:rounded-lg border border-gray-200 grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="block text-xs font-medium text-gray-700 uppercase">Program Studi</label>
            <select wire:model.live="filterProdiId" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 sm:text-sm">
                <option value="">Semua Prodi</option>
                @foreach($prodis as $p)
                    <option value="{{ $p->id }}">{{ $p->jenjang }} - {{ $p->nama_prodi }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 uppercase">Angkatan</label>
            <select wire:model.live="filterAngkatan" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 sm:text-sm">
                <option value="">Semua Angkatan</option>
                @foreach($angkatans as $akt)
                    <option value="{{ $akt->id_tahun }}">{{ $akt->id_tahun }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 uppercase">Cari Nama/NIM</label>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari..." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 sm:text-sm">
        </div>
    </div>

    @if (session()->has('success'))
        <div class="bg-green-50 p-4 rounded-md border border-green-200 text-sm text-green-700 font-medium">
            {{ session('success') }}
        </div>
    @endif

    <!-- Form -->
    @if($showForm)
    <div class="bg-indigo-50 shadow sm:rounded-lg p-6 border border-indigo-100">
        <h3 class="text-lg font-medium leading-6 text-indigo-900 mb-4">
            {{ $editMode ? 'Edit Biodata Mahasiswa' : 'Registrasi Mahasiswa Baru' }}
        </h3>
        
        <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
            <!-- Akun & Identitas -->
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700">NIM *</label>
                <input type="text" wire:model="nim" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 sm:text-sm">
                @error('nim') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div class="sm:col-span-4">
                <label class="block text-sm font-medium text-gray-700">Nama Lengkap *</label>
                <input type="text" wire:model="nama_lengkap" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 sm:text-sm">
                @error('nama_lengkap') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <!-- Akademik -->
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700">Angkatan *</label>
                <select wire:model="angkatan_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 sm:text-sm">
                    @foreach($angkatans as $akt)
                        <option value="{{ $akt->id_tahun }}">{{ $akt->id_tahun }}</option>
                    @endforeach
                </select>
            </div>
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700">Program Studi *</label>
                <select wire:model="prodi_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 sm:text-sm">
                    <option value="">Pilih Prodi</option>
                    @foreach($prodis as $p)
                        <option value="{{ $p->id }}">{{ $p->nama_prodi }}</option>
                    @endforeach
                </select>
            </div>
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700">Program Kelas *</label>
                <select wire:model="program_kelas_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 sm:text-sm">
                    <option value="">Pilih Kelas</option>
                    @foreach($programKelasList as $pk)
                        <option value="{{ $pk->id }}">{{ $pk->nama_program }}</option>
                    @endforeach
                </select>
                @error('program_kelas_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <!-- Dosen Wali -->
            <div class="sm:col-span-6 bg-white p-3 rounded border border-gray-200">
                <label class="block text-sm font-medium text-gray-700 mb-1">Dosen Wali (Penasihat Akademik)</label>
                <select wire:model="dosen_wali_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 sm:text-sm">
                    <option value="">-- Pilih Dosen Wali --</option>
                    @foreach($dosens as $dosen)
                        <option value="{{ $dosen->id }}">{{ $dosen->nama_lengkap_gelar }}</option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-500 mt-1">Dosen wali bertugas menyetujui KRS mahasiswa ini.</p>
            </div>

            <!-- DISPENSASI -->
            <div class="sm:col-span-6 bg-yellow-50 p-4 rounded border border-yellow-200">
                <div class="flex items-center">
                    <input type="checkbox" wire:model="bebas_keuangan" id="dispensasi" class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                    <label for="dispensasi" class="ml-2 block text-sm font-bold text-gray-900">
                        Aktifkan Dispensasi Keuangan (Bebas Syarat Bayar)
                    </label>
                </div>
                <p class="text-xs text-gray-600 mt-1 ml-6">
                    Jika dicentang, mahasiswa ini <strong>BISA MENGISI KRS</strong> meskipun pembayaran belum mencapai target persentase minimal. Gunakan untuk kasus khusus (Beasiswa, Anak Dosen, dll).
                </p>
            </div>

            <!-- Kontak & Password -->
            <div class="sm:col-span-3">
                <label class="block text-sm font-medium text-gray-700">Nomor HP</label>
                <input type="text" wire:model="nomor_hp" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 sm:text-sm">
            </div>
            <div class="sm:col-span-3">
                <label class="block text-sm font-medium text-gray-700">
                    {{ $editMode ? 'Reset Password (Isi jika ingin ubah)' : 'Password Awal *' }}
                </label>
                <input type="password" wire:model="password_baru" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 sm:text-sm" placeholder="Min. 6 Karakter">
                @error('password_baru') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="mt-5 flex justify-end space-x-2">
            <button wire:click="batal" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                Batal
            </button>
            <button wire:click="save" class="px-4 py-2 text-sm font-bold text-white bg-indigo-600 rounded-md hover:bg-indigo-700">
                Simpan Data
            </button>
        </div>
    </div>
    @endif

    <!-- Tabel Data -->
    <div class="flex flex-col">
        <div class="-my-2 -mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="inline-block min-w-full py-2 align-middle md:px-6 lg:px-8">
                <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-300">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">NIM</th>
                                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Nama Lengkap</th>
                                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Info Akademik</th>
                                <th class="px-3 py-3.5 text-center text-sm font-semibold text-gray-900">Status</th>
                                <th class="relative py-3.5 pl-3 pr-4 sm:pr-6">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse($mahasiswas as $mhs)
                            <tr>
                                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-indigo-600 sm:pl-6">
                                    {{ $mhs->nim }}
                                    @if($mhs->data_tambahan['bebas_keuangan'] ?? false)
                                        <div class="mt-1">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-yellow-100 text-yellow-800">
                                                DISPENSASI
                                            </span>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-3 py-4 text-sm text-gray-900">
                                    <div class="font-bold">{{ $mhs->nama_lengkap }}</div>
                                    <div class="text-xs text-gray-500">
                                        User ID: {{ $mhs->user->username ?? 'No User' }}
                                    </div>
                                </td>
                                <td class="px-3 py-4 text-sm text-gray-500">
                                    <div>{{ $mhs->prodi->nama_prodi }}</div>
                                    <div class="text-xs">Angkatan {{ $mhs->angkatan_id }}</div>
                                    <div class="text-xs text-indigo-600 mt-1 font-medium">
                                        PA: {{ $mhs->dosenWali->nama_lengkap_gelar ?? '-' }}
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-center">
                                    <span class="inline-flex rounded-full bg-blue-100 px-2 text-xs font-semibold leading-5 text-blue-800">
                                        {{ $mhs->programKelas->nama_program }}
                                    </span>
                                </td>
                                <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6 space-x-2">
                                    <button wire:click="edit('{{ $mhs->id }}')" class="text-indigo-600 hover:text-indigo-900">Edit</button>
                                    <button wire:click="delete('{{ $mhs->id }}')" wire:confirm="Hapus mahasiswa ini? User login juga akan dihapus." class="text-red-600 hover:text-red-900">Hapus</button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-gray-500 italic">
                                    Tidak ada data mahasiswa ditemukan.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="p-4 border-t border-gray-200">
                        {{ $mahasiswas->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>