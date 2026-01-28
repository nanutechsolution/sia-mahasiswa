<div class="space-y-6">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Daftar Ulang (Camaba)</h1>
            <p class="mt-2 text-sm text-gray-700">Validasi calon mahasiswa yang masuk dari jalur PMB.</p>
        </div>
    </div>

    <!-- Filter -->
    <div class="bg-white p-4 shadow sm:rounded-lg border border-gray-200 grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-xs font-medium text-gray-700 uppercase">Program Studi</label>
            <select wire:model.live="filterProdiId" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 sm:text-sm">
                <option value="">Semua Prodi</option>
                @foreach($prodis as $p)
                    <option value="{{ $p->id }}">{{ $p->nama_prodi }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 uppercase">Cari Nama/No Daftar</label>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari..." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 sm:text-sm">
        </div>
    </div>

    @if (session()->has('success'))
        <div class="bg-green-50 p-4 rounded-md border border-green-200 text-sm text-green-700 font-medium">
            {{ session('success') }}
        </div>
    @endif

    <!-- Tabel Data -->
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">No Pendaftaran</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Nama Lengkap</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Prodi / Jalur</th>
                    <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase">Status Bayar</th>
                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($camabas as $mhs)
                @php
                    $tagihan = $mhs->tagihan->first();
                    $lunas = $tagihan && $tagihan->status_bayar == 'LUNAS';
                    $dispensasi = $mhs->data_tambahan['bebas_keuangan'] ?? false;
                @endphp
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        {{ $mhs->nim }}
                        @if($dispensasi)
                            <div class="mt-1">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-yellow-100 text-yellow-800">
                                    DISPENSASI
                                </span>
                            </div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <div class="font-bold">{{ $mhs->nama_lengkap }}</div>
                        <div class="text-xs text-gray-500">{{ $mhs->email_pribadi }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <div>{{ $mhs->prodi->nama_prodi }}</div>
                        <div class="text-xs text-indigo-500">{{ $mhs->programKelas->nama_program }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        @if($lunas)
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">LUNAS</span>
                        @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">BELUM LUNAS</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex flex-col items-end gap-2">
                            @if($lunas || $dispensasi)
                                <button wire:click="generateNimResmi('{{ $mhs->id }}')" 
                                    wire:confirm="Yakin resmikan mahasiswa ini? NIM akan digenerate otomatis."
                                    class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-bold rounded shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    Generate NIM
                                </button>
                            @else
                                <span class="text-xs text-gray-400 italic mb-1">Menunggu Lunas</span>
                            @endif
                            
                            {{-- Tombol Atur Dispensasi --}}
                            <button wire:click="edit('{{ $mhs->id }}')" class="text-indigo-600 hover:text-indigo-900 text-xs font-bold underline">
                                Atur Dispensasi
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-10 text-center text-gray-500 italic">
                        Tidak ada data calon mahasiswa baru.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4 border-t border-gray-200">
            {{ $camabas->links() }}
        </div>
    </div>

    <!-- Modal Form Dispensasi -->
    @if($showForm)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="batal"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Pengaturan Dispensasi Camaba</h3>
                <div class="mb-4">
                    <p class="text-sm font-bold text-gray-700">{{ $nama_lengkap }}</p>
                </div>
                <div class="bg-yellow-50 p-4 rounded border border-yellow-200 mb-4">
                    <div class="flex items-center">
                        <input type="checkbox" wire:model="bebas_keuangan" id="dispensasi" class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                        <label for="dispensasi" class="ml-2 block text-sm font-bold text-gray-900">
                            Berikan Dispensasi (Boleh Generate NIM walau Belum Lunas)
                        </label>
                    </div>
                    <p class="text-xs text-gray-600 mt-2 ml-6">
                        Gunakan fitur ini hanya untuk kasus khusus (misal: Beasiswa, Anak Dosen, atau Perjanjian Cicilan Khusus).
                    </p>
                </div>
                <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                    <button wire:click="save" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none sm:col-start-2 sm:text-sm">
                        Simpan Perubahan
                    </button>
                    <button wire:click="batal" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:col-start-1 sm:text-sm">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>