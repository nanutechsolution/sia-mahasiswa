<div class="space-y-6">
    <div class="sm:flex sm:items-center sm:justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Perwalian Akademik</h1>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Mahasiswa</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Program</th>
                    <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase">Status KRS</th>
                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($mahasiswas as $mhs)
                @php 
                    $krs = $mhs->krs->first(); 
                    $status = $krs ? $krs->status_krs : 'BELUM ISI';
                @endphp
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-bold text-gray-900">{{ $mhs->nama_lengkap }}</div>
                        <div class="text-xs text-gray-500">{{ $mhs->nim }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $mhs->programKelas->nama_program }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            {{ $status == 'AJUKAN' ? 'bg-yellow-100 text-yellow-800' : 
                              ($status == 'DISETUJUI' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800') }}">
                            {{ $status }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        @if($krs)
                            <a href="{{ route('dosen.perwalian.detail', $krs->id) }}" class="text-indigo-600 hover:text-indigo-900 font-bold">Review KRS</a>
                        @else
                            <span class="text-gray-400 italic">Belum ada KRS</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>