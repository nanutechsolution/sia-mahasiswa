<div class="space-y-6">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Cetak Absensi Perkuliahan</h1>
            <p class="mt-2 text-sm text-gray-700">Cetak daftar hadir mahasiswa untuk dosen pengampu.</p>
        </div>
    </div>

    <!-- Filter -->
    <div class="bg-white p-4 shadow sm:rounded-lg border border-gray-200">
        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Program Studi</label>
        <select wire:model.live="filterProdiId" class="block w-full md:w-1/3 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 sm:text-sm">
            @foreach($prodis as $p)
                <option value="{{ $p->id }}">{{ $p->jenjang }} - {{ $p->nama_prodi }}</option>
            @endforeach
        </select>
    </div>

    <!-- Tabel Jadwal -->
    <div class="bg-white shadow overflow-hidden sm:rounded-lg border border-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">Mata Kuliah</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">Kelas / Dosen</th>
                    <th class="px-6 py-3 text-center text-xs font-bold text-slate-500 uppercase">Peserta (Valid)</th>
                    <th class="px-6 py-3 text-right text-xs font-bold text-slate-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse($jadwals as $jadwal)
                <tr class="hover:bg-slate-50 transition">
                    <td class="px-6 py-4">
                        <div class="text-sm font-bold text-slate-900">{{ $jadwal->mataKuliah->nama_mk }}</div>
                        <div class="text-xs text-slate-500">{{ $jadwal->mataKuliah->kode_mk }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-slate-800">Kelas {{ $jadwal->nama_kelas }}</div>
                        <div class="text-xs text-indigo-600">{{ $jadwal->dosen->nama_lengkap_gelar }}</div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold {{ $jadwal->peserta_count > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $jadwal->peserta_count }} Mahasiswa
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        @if($jadwal->peserta_count > 0)
                            <a href="{{ route('admin.cetak.absensi', $jadwal->id) }}" target="_blank" class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-bold rounded text-gray-700 bg-white hover:bg-gray-50">
                                <svg class="w-4 h-4 mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                                Cetak Absen
                            </a>
                        @else
                            <span class="text-xs text-gray-400 italic">Belum ada peserta</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-10 text-center text-slate-400 italic">Tidak ada jadwal kuliah ditemukan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>