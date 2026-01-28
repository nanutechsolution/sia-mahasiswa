<div class="space-y-6">
    <div class="bg-white shadow sm:rounded-lg p-6 border-l-4 border-indigo-500">
        <h3 class="text-lg font-bold text-gray-900">Review KRS Mahasiswa</h3>
        <div class="mt-4 grid grid-cols-2 gap-4 text-sm">
            <div>
                <span class="block text-gray-500">Nama:</span>
                <span class="font-bold">{{ $krs->mahasiswa->nama_lengkap }}</span>
            </div>
            <div>
                <span class="block text-gray-500">NIM:</span>
                <span class="font-bold">{{ $krs->mahasiswa->nim }}</span>
            </div>
        </div>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Mata Kuliah</th>
                    <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase">SKS</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Jadwal</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @php $totalSks = 0; @endphp
                @foreach($krs->details as $mk)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $mk->jadwalKuliah->mataKuliah->nama_mk }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">
                        {{ $mk->jadwalKuliah->mataKuliah->sks_default }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $mk->jadwalKuliah->hari }}, {{ $mk->jadwalKuliah->jam_mulai }}
                    </td>
                </tr>
                @php $totalSks += $mk->jadwalKuliah->mataKuliah->sks_default; @endphp
                @endforeach
                <tr class="bg-gray-50">
                    <td class="px-6 py-3 font-bold text-right">Total SKS</td>
                    <td class="px-6 py-3 font-bold text-center">{{ $totalSks }}</td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="flex justify-end gap-4">
        <button wire:click="tolak" wire:confirm="Yakin tolak? Mahasiswa harus mengisi ulang." class="bg-red-100 text-red-700 px-4 py-2 rounded-md font-bold hover:bg-red-200">
            Tolak (Revisi)
        </button>
        <button wire:click="setujui" wire:confirm="Setujui KRS ini?" class="bg-green-600 text-white px-4 py-2 rounded-md font-bold hover:bg-green-700">
            SETUJUI KRS
        </button>
    </div>
</div>