<div class="space-y-6">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Mutasi Status Mahasiswa</h1>
            <p class="mt-2 text-sm text-gray-700">Kelola status Cuti, Non-Aktif, Keluar, atau Lulus untuk semester aktif.</p>
        </div>
    </div>

    <!-- Pencarian -->
    <div class="bg-white p-4 shadow sm:rounded-lg border border-gray-200">
        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Cari Mahasiswa</label>
        <div class="flex gap-2">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="NIM atau Nama..." class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 sm:text-sm">
        </div>
    </div>

    @if (session()->has('success'))
        <div class="bg-green-50 p-4 rounded-md border border-green-200 text-sm text-green-700 font-bold">{{ session('success') }}</div>
    @endif

    <!-- Tabel Data -->
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Mahasiswa</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Prodi</th>
                    <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase">Status Semester Ini</th>
                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($mahasiswas as $mhs)
                @php
                    // Ambil status semester ini jika ada, default A (Aktif) atau N (Non-Aktif/Belum KRS)
                    $riwayat = $mhs->riwayatStatus->first(); 
                    $status = $riwayat ? $riwayat->status_kuliah : '-';
                    
                    $badgeColor = match($status) {
                        'A' => 'bg-green-100 text-green-800',
                        'C' => 'bg-yellow-100 text-yellow-800',
                        'N' => 'bg-gray-100 text-gray-800',
                        'L' => 'bg-blue-100 text-blue-800',
                        'K' => 'bg-red-100 text-red-800',
                        'D' => 'bg-red-100 text-red-800',
                        default => 'bg-slate-100 text-slate-500'
                    };
                    $statusLabel = match($status) {
                        'A' => 'AKTIF', 'C' => 'CUTI', 'N' => 'NON-AKTIF', 
                        'L' => 'LULUS', 'K' => 'KELUAR', 'D' => 'DROP OUT', default => 'BELUM REGISTRASI'
                    };
                @endphp
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-bold text-gray-900">{{ $mhs->nama_lengkap }}</div>
                        <div class="text-xs text-gray-500">{{ $mhs->nim }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $mhs->prodi->nama_prodi }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        <span class="px-2 inline-flex text-xs leading-5 font-bold rounded-full {{ $badgeColor }}">
                            {{ $statusLabel }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <button wire:click="openMutasi('{{ $mhs->id }}')" class="text-indigo-600 hover:text-indigo-900 font-bold bg-indigo-50 px-3 py-1 rounded">
                            Ubah Status
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-10 text-center text-gray-500 italic">Cari mahasiswa untuk mengubah status.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4 border-t border-gray-200">
            {{ $mahasiswas->links() }}
        </div>
    </div>

    <!-- MODAL FORM MUTASI -->
    @if($showModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="batal"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Proses Mutasi: <span class="font-bold">{{ $selectedMhsName }}</span>
                            </h3>
                            <div class="mt-4 space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Status Baru</label>
                                    <select wire:model.live="status_baru" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 sm:text-sm">
                                        <option value="A">Aktif (Batalkan Cuti)</option>
                                        <option value="C">Cuti Akademik</option>
                                        <option value="N">Non-Aktif (Mangkir)</option>
                                        <option value="L">Lulus / Yudisium</option>
                                        <option value="K">Keluar / Pindah</option>
                                        <option value="D">Drop Out (DO)</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Nomor SK (Opsional)</label>
                                    <input type="text" wire:model="nomor_sk" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 sm:text-sm" placeholder="Contoh: SK-123/UN/2024">
                                </div>

                                <!-- OPSI TAGIHAN (Hanya muncul jika CUTI) -->
                                @if($status_baru == 'C')
                                <div class="bg-indigo-50 p-3 rounded border border-indigo-100">
                                    <div class="flex items-start">
                                        <div class="flex items-center h-5">
                                            <input id="tagihan_cuti" type="checkbox" wire:model.live="buat_tagihan_cuti" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <label for="tagihan_cuti" class="font-medium text-gray-700">Buat Tagihan Biaya Cuti?</label>
                                            <p class="text-gray-500 text-xs">Otomatis membuat invoice administrasi cuti.</p>
                                        </div>
                                    </div>
                                    
                                    @if($buat_tagihan_cuti)
                                    <div class="mt-2">
                                        <label class="block text-xs font-bold text-gray-700">Nominal Biaya Cuti (Rp)</label>
                                        <input type="number" wire:model="nominal_biaya_cuti" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 sm:text-sm">
                                    </div>
                                    @endif
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button wire:click="simpanMutasi" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                        Simpan Perubahan
                    </button>
                    <button wire:click="batal" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>