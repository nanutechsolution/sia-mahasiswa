<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Setting Program Kelas</h1>
            <p class="text-sm text-gray-500">Atur syarat pembayaran minimal untuk KRS per program.</p>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="bg-green-50 p-4 rounded text-green-800 text-sm font-bold">{{ session('success') }}</div>
    @endif

    @if($showForm)
    <div class="bg-white p-6 shadow rounded-lg border border-gray-200">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Edit Aturan Program</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nama Program</label>
                <input type="text" wire:model="nama_program" class="block w-full rounded border-gray-300 shadow-sm text-sm">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Syarat Bayar KRS (%)</label>
                <div class="flex items-center">
                    <input type="number" wire:model="min_pembayaran_persen" class="block w-24 rounded border-gray-300 shadow-sm text-sm mr-2">
                    <span class="text-gray-500">%</span>
                </div>
                <p class="text-xs text-gray-400 mt-1">Standar minimal pembayaran agar mahasiswa bisa mengisi KRS.</p>
                
                {{-- INFO DISPENSASI --}}
                <div class="mt-2 bg-yellow-50 border border-yellow-100 p-2 rounded text-[11px] text-yellow-800 leading-tight">
                    <strong>Tips Dispensasi:</strong> <br>
                    Jangan ubah angka ini untuk kasus perorangan (misal: mahasiswa kurang mampu yang diizinkan bayar 10%). 
                    <br>Untuk kasus tersebut, silakan beri <strong>Status Dispensasi</strong> pada menu <em>Data Mahasiswa</em>.
                </div>
            </div>
        </div>
        <div class="mt-4 flex justify-end gap-2">
            <button wire:click="batal" class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded text-sm">Batal</button>
            <button wire:click="save" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 text-sm font-bold">Simpan</button>
        </div>
    </div>
    @endif

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Kode</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Nama Program</th>
                    <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase">Syarat KRS</th>
                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($programs as $pk)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-indigo-600">{{ $pk->kode_internal }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $pk->nama_program }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        <span class="px-2 inline-flex text-xs leading-5 font-bold rounded-full bg-yellow-100 text-yellow-800">
                            Min. {{ $pk->min_pembayaran_persen }}%
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <button wire:click="edit({{ $pk->id }})" class="text-indigo-600 hover:text-indigo-900 font-bold">Edit Aturan</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>