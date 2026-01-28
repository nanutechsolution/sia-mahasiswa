<div class="bg-white shadow sm:rounded-lg p-6">
    <div class="border-b border-gray-200 pb-4 mb-4 flex justify-between items-center">
        <div>
            <h2 class="text-xl font-bold text-gray-800">{{ $jadwal->mataKuliah->nama_mk }} ({{ $jadwal->nama_kelas }})</h2>
            <p class="text-gray-500">{{ $jadwal->mataKuliah->kode_mk }} • {{ $jadwal->mataKuliah->sks_default }} SKS</p>
        </div>
        <button wire:click="publishNilai" 
                wire:confirm="Yakin publish? Nilai akan tampil di akun mahasiswa."
                class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 text-sm font-bold">
            PUBLISH NILAI & HITUNG IPS
        </button>
    </div>

    @if(session('global_success'))
        <div class="bg-green-100 text-green-800 p-3 rounded mb-4">{{ session('global_success') }}</div>
    @endif

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-300">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">NIM</th>
                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mahasiswa</th>
                    <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase w-24">Tugas (30%)</th>
                    <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase w-24">UTS (30%)</th>
                    <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase w-24">UAS (40%)</th>
                    <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase w-24">Akhir</th>
                    <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase w-24">Huruf</th>
                    <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @foreach($pesertaKelas as $mhs)
                <tr wire:key="row-{{ $mhs->id }}">
                    <td class="px-3 py-4 text-sm text-gray-500">{{ $mhs->krs->mahasiswa->nim }}</td>
                    <td class="px-3 py-4 text-sm font-medium text-gray-900">{{ $mhs->krs->mahasiswa->nama_lengkap }}</td>
                    
                    <td class="px-3 py-4">
                        <input type="number" wire:model="nilaiTugas.{{ $mhs->id }}" max="100" class="w-20 text-center text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </td>
                    
                    <td class="px-3 py-4">
                        <input type="number" wire:model="nilaiUts.{{ $mhs->id }}" max="100" class="w-20 text-center text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </td>
                    
                    <td class="px-3 py-4">
                        <input type="number" wire:model="nilaiUas.{{ $mhs->id }}" max="100" class="w-20 text-center text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </td>

                    <td class="px-3 py-4 text-center text-sm font-bold text-gray-700">
                        {{ number_format($mhs->nilai_angka, 0) }}
                    </td>
                    <td class="px-3 py-4 text-center text-sm font-bold 
                        {{ $mhs->nilai_huruf == 'A' ? 'text-green-600' : ($mhs->nilai_huruf == 'E' ? 'text-red-600' : 'text-gray-900') }}">
                        {{ $mhs->nilai_huruf ?? '-' }}
                    </td>

                    <td class="px-3 py-4 text-center">
                        <button wire:click="simpanNilai('{{ $mhs->id }}')" class="text-indigo-600 hover:text-indigo-900 text-xs font-semibold">
                            <span wire:loading.remove target="simpanNilai('{{ $mhs->id }}')">Simpan</span>
                            <span wire:loading target="simpanNilai('{{ $mhs->id }}')">...</span>
                        </button>
                        @if(session('success-'.$mhs->id))
                            <span class="text-green-500 text-xs block">OK</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>