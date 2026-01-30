<div class="space-y-6">
    <!-- Header Informasi Mata Kuliah -->
    <div class="bg-white p-6 shadow-sm rounded-2xl border border-slate-200">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h2 class="text-2xl font-black text-slate-800 uppercase tracking-tight">
                    {{ $jadwal->mataKuliah->nama_mk }}
                </h2>
                <div class="flex items-center gap-3 mt-1 text-sm font-medium text-slate-500">
                    <span class="bg-slate-100 px-2 py-0.5 rounded text-indigo-600 font-bold">{{ $jadwal->nama_kelas }}</span>
                    <span>&bull;</span>
                    <span>{{ $jadwal->tahunAkademik->nama_tahun }}</span>
                    <span>&bull;</span>
                    <span>{{ $jadwal->dosen->nama_lengkap_gelar }}</span>
                </div>
            </div>
            
            <div class="flex items-center gap-3">
                <button wire:click="publishNilai" 
                    wire:confirm="Setelah dipublikasi, mahasiswa dapat melihat nilai di KHS dan nilai tidak dapat diubah lagi. Lanjutkan?"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-xl font-bold text-sm shadow-lg shadow-indigo-100 transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                    Publikasikan Nilai Ke KHS
                </button>
            </div>
        </div>

        @if (session()->has('global_success'))
            <div class="mt-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm font-bold animate-pulse">
                {{ session('global_success') }}
            </div>
        @endif
    </div>

    <!-- Tabel Input Nilai -->
    <div class="bg-white shadow-sm overflow-hidden rounded-2xl border border-slate-200">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-black text-slate-500 uppercase tracking-widest">Mahasiswa</th>
                        <th class="px-4 py-4 text-center text-xs font-black text-slate-500 uppercase tracking-widest w-24">Tugas (30%)</th>
                        <th class="px-4 py-4 text-center text-xs font-black text-slate-500 uppercase tracking-widest w-24">UTS (30%)</th>
                        <th class="px-4 py-4 text-center text-xs font-black text-slate-500 uppercase tracking-widest w-24">UAS (40%)</th>
                        <th class="px-4 py-4 text-center text-xs font-black text-slate-500 uppercase tracking-widest bg-indigo-50/30">Nilai Akhir</th>
                        <th class="px-4 py-4 text-center text-xs font-black text-slate-500 uppercase tracking-widest">Grade</th>
                        <th class="px-6 py-4 text-center text-xs font-black text-slate-500 uppercase tracking-widest">Status</th>
                        <th class="px-6 py-4 text-right"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($pesertaKelas as $mhs)
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-slate-800">{{ $mhs->krs->mahasiswa->nama_lengkap }}</span>
                                <span class="text-xs font-mono text-slate-400 tracking-tighter">{{ $mhs->krs->mahasiswa->nim }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-4">
                            <input type="number" step="0.01" min="0" max="100" 
                                wire:model.defer="nilaiTugas.{{ $mhs->id }}" 
                                class="w-full text-center rounded-lg border-slate-200 focus:ring-indigo-500 focus:border-indigo-500 text-sm font-bold p-1.5"
                                {{ $mhs->is_published ? 'disabled' : '' }}>
                        </td>
                        <td class="px-4 py-4">
                            <input type="number" step="0.01" min="0" max="100" 
                                wire:model.defer="nilaiUts.{{ $mhs->id }}" 
                                class="w-full text-center rounded-lg border-slate-200 focus:ring-indigo-500 focus:border-indigo-500 text-sm font-bold p-1.5"
                                {{ $mhs->is_published ? 'disabled' : '' }}>
                        </td>
                        <td class="px-4 py-4">
                            <input type="number" step="0.01" min="0" max="100" 
                                wire:model.defer="nilaiUas.{{ $mhs->id }}" 
                                class="w-full text-center rounded-lg border-slate-200 focus:ring-indigo-500 focus:border-indigo-500 text-sm font-bold p-1.5"
                                {{ $mhs->is_published ? 'disabled' : '' }}>
                        </td>
                        <td class="px-4 py-4 text-center bg-indigo-50/30">
                            <span class="text-sm font-black text-slate-700">
                                {{ number_format($mhs->nilai_angka, 2) }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-center">
                            <span class="text-xl font-black {{ in_array($mhs->nilai_huruf, ['A','B']) ? 'text-emerald-600' : ($mhs->nilai_huruf == 'E' ? 'text-rose-600' : 'text-indigo-600') }}">
                                {{ $mhs->nilai_huruf ?? '-' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($mhs->is_published)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black bg-green-100 text-green-700 border border-green-200 uppercase tracking-tighter">
                                    Published
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black bg-orange-100 text-orange-700 border border-orange-200 uppercase tracking-tighter">
                                    Draft
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            @if(!$mhs->is_published)
                                <div class="flex items-center justify-end gap-2">
                                    @if (session()->has('success-' . $mhs->id))
                                        <span class="text-[10px] font-bold text-green-600 animate-bounce">OK!</span>
                                    @endif
                                    <button wire:click="simpanNilai('{{ $mhs->id }}')" 
                                        class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors"
                                        title="Simpan Draft">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    </button>
                                </div>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                <p class="text-slate-500 font-bold">Belum ada mahasiswa yang KRS-nya disetujui di kelas ini.</p>
                                <p class="text-xs text-slate-400 mt-1">Pastikan Dosen Wali telah menyetujui KRS mahasiswa bersangkutan.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Info Skala Nilai (Helper) -->
    <div class="bg-slate-800 p-4 rounded-2xl shadow-lg border border-slate-700">
        <h5 class="text-xs font-black text-unmaris-gold uppercase tracking-widest mb-3 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            Panduan Pembobotan Nilai
        </h5>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-white/70 text-[10px]">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-unmaris-gold"></span>
                <span>Komponen: 30% Tugas + 30% UTS + 40% UAS</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-unmaris-gold"></span>
                <span>Konversi Huruf: Otomatis sesuai Master Skala Nilai</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-unmaris-gold"></span>
                <span>Status Published: Nilai akan mengunci dan terhitung ke IPS</span>
            </div>
        </div>
    </div>
</div>