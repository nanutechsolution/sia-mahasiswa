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
                    // Cek status bayar manual dari relasi tagihan (Ambil tagihan pertama/terbaru)
                    $tagihan = $mhs->tagihan->first();
                    $lunas = $tagihan && $tagihan->status_bayar == 'LUNAS';
                @endphp
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        {{ $mhs->nim }}
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
                        @if($lunas)
                            <button wire:click="generateNimResmi('{{ $mhs->id }}')" 
                                wire:confirm="Yakin resmikan mahasiswa ini? NIM akan digenerate otomatis."
                                class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-bold rounded shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                Generate NIM
                            </button>
                        @else
                            <span class="text-xs text-gray-400 italic">Lunasi tagihan dulu</span>
                        @endif
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
</div>