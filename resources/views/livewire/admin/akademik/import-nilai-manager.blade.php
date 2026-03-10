<div>
    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-800 tracking-tight">Kelola Nilai Historis</h1>
            <p class="text-sm text-slate-500 mt-1">Migrasi data nilai mahasiswa lama (Transkrip/KHS) secara massal via Excel atau Input Manual.</p>
        </div>
        @if($activeTab === 'import')
        <button wire:click="downloadTemplate" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-xl shadow-sm transition-all focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
            </svg>
            Download Template Excel
        </button>
        @endif
    </div>

    {{-- TAB NAVIGATION --}}
    <div class="flex gap-2 mb-6 border-b border-slate-200">
        <button wire:click="switchTab('import')" class="px-5 py-3 text-sm font-bold transition-all relative {{ $activeTab === 'import' ? 'text-unmaris-blue' : 'text-slate-400 hover:text-slate-600' }}">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                Import Excel
            </div>
            @if($activeTab === 'import')
                <div class="absolute bottom-0 left-0 right-0 h-0.5 bg-unmaris-blue rounded-t-full"></div>
            @endif
        </button>
        <button wire:click="switchTab('manual')" class="px-5 py-3 text-sm font-bold transition-all relative {{ $activeTab === 'manual' ? 'text-unmaris-blue' : 'text-slate-400 hover:text-slate-600' }}">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                Input / Edit Manual
            </div>
            @if($activeTab === 'manual')
                <div class="absolute bottom-0 left-0 right-0 h-0.5 bg-unmaris-blue rounded-t-full"></div>
            @endif
        </button>
    </div>

    {{-- =============================================== --}}
    {{-- TAB 1: IMPORT EXCEL --}}
    {{-- =============================================== --}}
    @if($activeTab === 'import')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6" x-data x-transition>
        
        {{-- Kiri: Form Upload --}}
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 border-t-4 border-t-unmaris-blue">
                <h2 class="text-lg font-bold text-slate-800 mb-4 border-b border-slate-100 pb-3">Upload File Nilai</h2>
                
                <form wire:submit="prosesImport">
                    <div class="mb-5">
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Pilih File Excel (.xlsx / .csv)</label>
                        <input type="file" wire:model="file_excel" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-unmaris-blue/10 file:text-unmaris-blue hover:file:bg-unmaris-blue/20 transition-all border border-slate-200 rounded-xl focus:ring-unmaris-blue focus:border-unmaris-blue" accept=".xlsx, .xls, .csv">
                        @error('file_excel') <span class="text-xs text-rose-500 mt-1 block font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <div wire:loading wire:target="file_excel" class="text-sm text-unmaris-gold font-semibold mb-4 flex items-center">
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-unmaris-gold" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        Membaca file...
                    </div>

                    <button type="submit" wire:loading.attr="disabled" class="w-full flex justify-center items-center py-2.5 px-4 border border-transparent rounded-xl shadow-sm text-sm font-bold text-white bg-unmaris-blue hover:bg-unmaris-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-unmaris-blue disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                        <span wire:loading.remove wire:target="prosesImport">Proses Import Data</span>
                        <span wire:loading wire:target="prosesImport" class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            Sedang Memproses...
                        </span>
                    </button>
                </form>
            </div>

            <div class="bg-blue-50 rounded-2xl border border-blue-100 p-5">
                <h3 class="font-bold text-blue-800 mb-2 flex items-center text-sm">
                    <svg class="w-5 h-5 mr-1 text-unmaris-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Catatan Penting
                </h3>
                <ul class="text-xs text-blue-700 space-y-1.5 list-disc pl-4">
                    <li>Pastikan <span class="font-bold">NIM</span> mahasiswa terdaftar di sistem.</li>
                    <li>Pastikan <span class="font-bold">Kode Mata Kuliah</span> sesuai dengan master data.</li>
                    <li>Format <span class="font-bold">Kode Tahun</span> contoh: <code>20171</code> (Ganjil 2017), <code>20172</code> (Genap).</li>
                </ul>
            </div>
        </div>

        {{-- Kanan: Hasil & Log Error --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 h-full">
                <div class="p-6 border-b border-slate-100">
                    <h2 class="text-lg font-bold text-slate-800">Log Hasil Import</h2>
                </div>
                
                <div class="p-6">
                    @if(!$importResult)
                        <div class="flex flex-col items-center justify-center text-slate-400 py-12">
                            <svg class="w-16 h-16 mb-4 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            <p class="text-sm">Belum ada proses import yang dijalankan.</p>
                        </div>
                    @else
                        @if($importResult['status'] == 'error')
                            <div class="p-4 bg-rose-50 border border-rose-200 rounded-xl text-rose-700 text-sm font-semibold mb-4">
                                {{ $importResult['message'] }}
                            </div>
                        @else
                            <div class="flex gap-4 mb-6">
                                <div class="flex-1 bg-emerald-50 border border-emerald-200 p-4 rounded-xl flex items-center">
                                    <div class="bg-emerald-100 p-2 rounded-lg text-emerald-600 mr-3">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                    <div>
                                        <div class="text-xs text-emerald-600 font-bold uppercase tracking-wider">Berhasil</div>
                                        <div class="text-2xl font-black text-emerald-700 leading-none mt-1">{{ $importResult['success_count'] }} <span class="text-sm font-semibold">baris</span></div>
                                    </div>
                                </div>
                                
                                <div class="flex-1 bg-rose-50 border border-rose-200 p-4 rounded-xl flex items-center">
                                    <div class="bg-rose-100 p-2 rounded-lg text-rose-600 mr-3">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </div>
                                    <div>
                                        <div class="text-xs text-rose-600 font-bold uppercase tracking-wider">Gagal</div>
                                        <div class="text-2xl font-black text-rose-700 leading-none mt-1">{{ count($importResult['errors']) }} <span class="text-sm font-semibold">baris</span></div>
                                    </div>
                                </div>
                            </div>

                            @if(count($importResult['errors']) > 0)
                                <div class="mt-4">
                                    <h4 class="text-sm font-bold text-slate-700 mb-3 flex items-center">
                                        Rincian Error (Perbaiki baris ini di Excel lalu re-upload):
                                    </h4>
                                    <div class="bg-slate-50 border border-slate-200 rounded-xl max-h-64 overflow-y-auto p-4">
                                        <ul class="space-y-2 text-xs text-rose-600 font-mono">
                                            @foreach($importResult['errors'] as $error)
                                                <li class="flex items-start">
                                                    <span class="mr-2 mt-0.5">•</span>
                                                    <span>{{ $error }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            @endif
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif


    {{-- =============================================== --}}
    {{-- TAB 2: INPUT / EDIT MANUAL --}}
    {{-- =============================================== --}}
    @if($activeTab === 'manual')
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6" x-data x-transition>
        
        {{-- Form Input Manual --}}
        <div class="xl:col-span-1">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 sticky top-6 border-t-4 border-t-unmaris-gold">
                <h2 class="text-lg font-bold text-slate-800 mb-4 border-b border-slate-100 pb-3">
                    {{ $krs_detail_id ? 'Edit Nilai Mahasiswa' : 'Input Nilai Baru' }}
                </h2>
                
                <form wire:submit.prevent="saveManual" class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">NIM Mahasiswa</label>
                        <input type="text" wire:model="nim" class="w-full px-4 py-2.5 text-sm border-slate-300 rounded-xl focus:border-unmaris-blue focus:ring-unmaris-blue placeholder-slate-400" placeholder="Contoh: 17010001" {{ $krs_detail_id ? 'readonly class=bg-slate-100' : '' }}>
                        @error('nim') <span class="text-xs text-rose-500 font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Kode Tahun Akademik</label>
                        <input type="text" wire:model="kode_tahun" class="w-full px-4 py-2.5 text-sm border-slate-300 rounded-xl focus:border-unmaris-blue focus:ring-unmaris-blue placeholder-slate-400" placeholder="Contoh: 20171">
                        @error('kode_tahun') <span class="text-xs text-rose-500 font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Kode Mata Kuliah</label>
                        <input type="text" wire:model="kode_mk" class="w-full px-4 py-2.5 text-sm border-slate-300 rounded-xl focus:border-unmaris-blue focus:ring-unmaris-blue placeholder-slate-400" placeholder="Contoh: MKU101">
                        @error('kode_mk') <span class="text-xs text-rose-500 font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Nilai Huruf</label>
                            <input type="text" wire:model="nilai_huruf" class="w-full px-4 py-2.5 text-sm border-slate-300 rounded-xl focus:border-unmaris-blue focus:ring-unmaris-blue uppercase" placeholder="A, B+, dll">
                            @error('nilai_huruf') <span class="text-xs text-rose-500 font-semibold">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Nilai Angka <span class="font-normal text-slate-400">(Opsional)</span></label>
                            <input type="number" wire:model="nilai_angka" class="w-full px-4 py-2.5 text-sm border-slate-300 rounded-xl focus:border-unmaris-blue focus:ring-unmaris-blue" placeholder="0 - 100">
                            @error('nilai_angka') <span class="text-xs text-rose-500 font-semibold">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="pt-4 flex gap-3">
                        @if($krs_detail_id)
                        <button type="button" wire:click="resetForm" class="flex-1 py-2.5 px-4 border border-slate-300 text-slate-700 rounded-xl text-sm font-bold hover:bg-slate-50 transition-colors">
                            Batal
                        </button>
                        @endif
                        <button type="submit" class="flex-1 py-2.5 px-4 bg-unmaris-blue text-white rounded-xl text-sm font-bold hover:bg-unmaris-dark transition-colors shadow-md">
                            {{ $krs_detail_id ? 'Update Data' : 'Simpan Data' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Tabel Data Riwayat --}}
        <div class="xl:col-span-2">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden flex flex-col h-full">
                <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-center gap-4">
                    <h2 class="text-lg font-bold text-slate-800">Riwayat Nilai Terbaru</h2>
                    <div class="w-full sm:w-72 relative">
                        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari NIM atau Nama MK..." class="w-full px-4 py-2.5 text-sm border-slate-300 rounded-xl pl-10 focus:border-unmaris-blue focus:ring-unmaris-blue">
                        <svg class="w-5 h-5 text-slate-400 absolute left-3 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                </div>
                
                <div class="overflow-x-auto flex-1">
                    <table class="w-full text-left text-sm text-slate-600">
                        <thead class="bg-slate-50 border-b border-slate-200 text-slate-700">
                            <tr>
                                <th class="px-5 py-3 font-bold">Mahasiswa</th>
                                <th class="px-5 py-3 font-bold">Thn Akad.</th>
                                <th class="px-5 py-3 font-bold">Mata Kuliah</th>
                                <th class="px-5 py-3 font-bold text-center">Nilai</th>
                                <th class="px-5 py-3 font-bold text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($riwayatNilai as $item)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-5 py-3">
                                    <div class="font-bold text-slate-800">{{ $item->krs->mahasiswa->nim ?? '-' }}</div>
                                    <div class="text-xs text-slate-500">{{ $item->krs->mahasiswa->person->nama_lengkap ?? 'Unknown' }}</div>
                                </td>
                                <td class="px-5 py-3">
                                    <span class="bg-slate-100 text-slate-700 px-2.5 py-1 rounded-lg text-xs font-bold border border-slate-200">
                                        {{ $item->krs->tahunAkademik->kode_tahun ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-5 py-3">
                                    <div class="font-bold text-slate-800">{{ $item->kode_mk_snapshot }}</div>
                                    <div class="text-xs text-slate-500 line-clamp-1">{{ $item->nama_mk_snapshot }}</div>
                                </td>
                                <td class="px-5 py-3 text-center">
                                    <div class="font-black text-unmaris-blue">{{ $item->nilai_huruf }}</div>
                                    <div class="text-[10px] text-slate-400 font-semibold">{{ number_format($item->nilai_angka, 2) }}</div>
                                </td>
                                <td class="px-5 py-3 text-right">
                                    <div class="flex justify-end gap-2">
                                        <button wire:click="editManual({{ $item->id }})" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </button>
                                        <button wire:click="deleteManual({{ $item->id }})" wire:confirm="Yakin ingin menghapus data nilai ini?" class="p-1.5 text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-5 py-10 text-center text-slate-400">
                                    <svg class="w-12 h-12 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                                    Tidak ada data nilai yang ditemukan.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if($riwayatNilai && $riwayatNilai->hasPages())
                <div class="p-4 border-t border-slate-100 bg-slate-50 rounded-b-2xl">
                    {{ $riwayatNilai->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif
</div>

{{-- SweetAlert listener --}}
@script
<script>
    Livewire.on('swal:success', (data) => {
        Swal.fire({
            icon: 'success',
            title: data[0].title,
            text: data[0].text,
            confirmButtonColor: '#002855', // unmaris-blue
            confirmButtonText: 'Oke',
            customClass: {
                popup: 'rounded-2xl',
                confirmButton: 'rounded-xl font-bold'
            }
        });
    });

    Livewire.on('swal:error', (data) => {
        Swal.fire({
            icon: 'error',
            title: data[0].title,
            text: data[0].text,
            confirmButtonColor: '#e11d48', // rose-600
            confirmButtonText: 'Tutup',
            customClass: {
                popup: 'rounded-2xl',
                confirmButton: 'rounded-xl font-bold'
            }
        });
    });
</script>
@endscript