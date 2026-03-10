<div class="space-y-8 animate-in fade-in duration-700">
    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-[#002855] tracking-tight uppercase italic">Pengelolaan Nilai Historis</h1>
            <p class="text-slate-500 text-sm font-medium mt-1">Import massal atau input manual nilai dari semester lampau (Migrasi/Konversi).</p>
        </div>
        <div class="flex bg-white p-1.5 rounded-2xl border border-slate-200 shadow-sm">
            <button wire:click="switchTab('import')" class="px-6 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest transition-all {{ $activeTab == 'import' ? 'bg-[#002855] text-white shadow-lg' : 'text-slate-400 hover:text-slate-600' }}">Import Excel</button>
            <button wire:click="switchTab('manual')" class="px-6 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest transition-all {{ $activeTab == 'manual' ? 'bg-[#002855] text-white shadow-lg' : 'text-slate-400 hover:text-slate-600' }}">Input Manual</button>
        </div>
    </div>

    @if($activeTab == 'import')
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Instruction Card --}}
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-indigo-50 p-8 rounded-[2.5rem] border border-indigo-100">
                    <h3 class="font-black text-[#002855] text-lg uppercase leading-tight mb-4">Panduan Import</h3>
                    <ul class="space-y-4">
                        <li class="flex gap-3">
                            <div class="w-6 h-6 rounded-full bg-[#002855] text-white flex items-center justify-center text-[10px] font-bold shrink-0">1</div>
                            <p class="text-xs text-slate-600 leading-relaxed font-medium">Download template Excel yang disediakan untuk memastikan format kolom sesuai.</p>
                        </li>
                        <li class="flex gap-3">
                            <div class="w-6 h-6 rounded-full bg-[#002855] text-white flex items-center justify-center text-[10px] font-bold shrink-0">2</div>
                            <p class="text-xs text-slate-600 leading-relaxed font-medium">Gunakan <strong>KODE TAHUN</strong> resmi (Contoh: 20211 untuk Ganjil 2021).</p>
                        </li>
                        <li class="flex gap-3">
                            <div class="w-6 h-6 rounded-full bg-[#002855] text-white flex items-center justify-center text-[10px] font-bold shrink-0">3</div>
                            <p class="text-xs text-slate-600 leading-relaxed font-medium">Sistem akan otomatis membuat record KRS dan mengupdate Transkrip Mahasiswa.</p>
                        </li>
                    </ul>
                    <button wire:click="downloadTemplate" class="w-full mt-8 py-4 bg-white border-2 border-[#002855] text-[#002855] rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-[#002855] hover:text-white transition-all shadow-sm">Download Template</button>
                </div>
            </div>

            {{-- Upload Area --}}
            <div class="lg:col-span-2">
                <div class="bg-white p-10 rounded-[3rem] border-4 border-dashed border-slate-100 flex flex-col items-center justify-center text-center">
                    <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mb-6">
                        <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                    </div>
                    <h3 class="text-xl font-black text-slate-800 uppercase tracking-tight">Unggah File Nilai</h3>
                    <p class="text-slate-400 text-xs mt-1 mb-8 uppercase tracking-widest font-bold">Maksimal 5MB (.xlsx / .xls)</p>

                    <div class="w-full max-w-md">
                        <input type="file" wire:model="file_excel" class="block w-full text-sm text-slate-500 file:mr-4 file:py-3 file:px-6 file:rounded-xl file:border-0 file:text-xs file:font-black file:uppercase file:bg-[#002855] file:text-white hover:file:bg-black cursor-pointer bg-slate-50 p-2 rounded-2xl border border-slate-200">
                        @error('file_excel') <span class="text-rose-500 text-[10px] font-bold mt-2 block">{{ $message }}</span> @enderror
                    </div>

                    <button wire:click="prosesImport" wire:loading.attr="disabled" class="mt-10 px-12 py-4 bg-[#fcc000] text-[#002855] rounded-2xl font-black text-xs uppercase tracking-[0.2em] shadow-xl shadow-amber-500/20 hover:scale-105 active:scale-95 transition-all disabled:opacity-50">
                        <span wire:loading.remove>Mulai Proses Import</span>
                        <span wire:loading>Sedang Memproses...</span>
                    </button>
                </div>

                @if($importResult)
                    <div class="mt-8 p-6 rounded-3xl {{ $importResult['status'] == 'success' ? 'bg-emerald-50 border border-emerald-100' : 'bg-rose-50 border border-rose-100' }}">
                        <h4 class="font-black text-sm uppercase tracking-widest {{ $importResult['status'] == 'success' ? 'text-emerald-700' : 'text-rose-700' }}">Hasil Import:</h4>
                        @if($importResult['status'] == 'success')
                            <p class="text-xs font-bold text-emerald-600 mt-1">{{ $importResult['count'] }} Data Berhasil Disimpan.</p>
                        @endif
                        @if(!empty($importResult['errors']))
                            <div class="mt-4 space-y-1">
                                @foreach($importResult['errors'] as $err)
                                    <p class="text-[10px] text-rose-500 font-bold">• Baris {{ $err['row'] }}: {{ $err['message'] }}</p>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    @endif

    @if($activeTab == 'manual')
        <div class="grid grid-cols-1 xl:grid-cols-12 gap-8">
            {{-- Form Manual --}}
            <div class="xl:col-span-4">
                <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-200 space-y-6 sticky top-8">
                    <h3 class="text-sm font-black text-[#002855] uppercase tracking-[0.2em] mb-4">{{ $krs_detail_id ? 'Update Record Nilai' : 'Input Nilai Baru' }}</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">NIM Mahasiswa</label>
                            <input type="text" wire:model="nim" placeholder="Contoh: 21010001" class="w-full mt-1 px-5 py-3 rounded-xl bg-slate-50 border-slate-200 text-sm font-bold focus:ring-[#002855]">
                            @error('nim') <span class="text-rose-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Kode Tahun</label>
                                <input type="text" wire:model="kode_tahun" placeholder="20211" class="w-full mt-1 px-5 py-3 rounded-xl bg-slate-50 border-slate-200 text-sm font-bold">
                                @error('kode_tahun') <span class="text-rose-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Kode MK</label>
                                <input type="text" wire:model="kode_mk" placeholder="MK001" class="w-full mt-1 px-5 py-3 rounded-xl bg-slate-50 border-slate-200 text-sm font-bold">
                                @error('kode_mk') <span class="text-rose-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Nilai Huruf</label>
                                <input type="text" wire:model="nilai_huruf" placeholder="A" class="w-full mt-1 px-5 py-3 rounded-xl bg-slate-50 border-slate-200 text-sm font-bold uppercase">
                                @error('nilai_huruf') <span class="text-rose-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Nilai Angka</label>
                                <input type="number" wire:model="nilai_angka" placeholder="85" class="w-full mt-1 px-5 py-3 rounded-xl bg-slate-50 border-slate-200 text-sm font-bold">
                                @error('nilai_angka') <span class="text-rose-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="pt-6 flex gap-3">
                        <button wire:click="saveManual" class="flex-1 py-4 bg-[#002855] text-white rounded-2xl font-black text-xs uppercase tracking-widest shadow-xl shadow-blue-900/10 hover:bg-black transition-all">Simpan Data</button>
                        @if($krs_detail_id)
                            <button wire:click="resetForm" class="px-6 py-4 bg-slate-100 text-slate-400 rounded-2xl font-black text-xs uppercase hover:bg-rose-50 hover:text-rose-500 transition-all">Batal</button>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Table Riwayat --}}
            <div class="xl:col-span-8">
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-200 overflow-hidden">
                    <div class="p-8 border-b border-slate-50 flex justify-between items-center">
                        <h3 class="text-xs font-black text-[#002855] uppercase tracking-[0.2em]">Log Record Nilai Terakhir</h3>
                        <div class="relative w-64">
                            <input type="text" wire:model.live="search" placeholder="Cari NIM / MK..." class="w-full pl-10 pr-4 py-2 bg-slate-50 border-none rounded-xl text-xs font-bold focus:ring-indigo-500">
                            <svg class="w-4 h-4 text-slate-300 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-100">
                            <thead class="bg-slate-50/50">
                                <tr>
                                    <th class="px-8 py-4 text-left text-[9px] font-black text-slate-400 uppercase tracking-widest">Mahasiswa</th>
                                    <th class="px-6 py-4 text-left text-[9px] font-black text-slate-400 uppercase tracking-widest">Semester</th>
                                    <th class="px-6 py-4 text-left text-[9px] font-black text-slate-400 uppercase tracking-widest">Mata Kuliah</th>
                                    <th class="px-6 py-4 text-center text-[9px] font-black text-slate-400 uppercase tracking-widest">Nilai</th>
                                    <th class="px-8 py-4 text-right text-[9px] font-black text-slate-400 uppercase tracking-widest">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @forelse($riwayatNilai as $row)
                                <tr class="hover:bg-slate-50/50 transition-all group">
                                    <td class="px-8 py-4">
                                        <p class="text-xs font-black text-[#002855] uppercase tracking-tighter">{{ $row->krs->mahasiswa->person->nama_lengkap }}</p>
                                        <p class="text-[10px] font-mono text-slate-400">{{ $row->krs->mahasiswa->nim }}</p>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-[10px] font-bold text-slate-600 bg-slate-100 px-2 py-1 rounded-lg border border-slate-200 uppercase">{{ $row->krs->tahunAkademik->kode_tahun }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="text-[10px] font-black text-slate-700 uppercase tracking-tighter">{{ $row->nama_mk_snapshot }}</p>
                                        <p class="text-[9px] font-bold text-indigo-400 uppercase">{{ $row->kode_mk_snapshot }} &bull; {{ $row->sks_snapshot }} SKS</p>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="inline-flex flex-col items-center">
                                            <span class="text-lg font-black text-[#002855] italic">{{ $row->nilai_huruf }}</span>
                                            <span class="text-[8px] font-bold text-slate-300 uppercase tracking-widest">Bobot: {{ number_format($row->nilai_indeks, 1) }}</span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-4 text-right">
                                        <div class="flex justify-end gap-2">
                                            <button wire:click="editManual('{{ $row->id }}')" class="p-2 text-indigo-400 hover:text-indigo-600 transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button>
                                            <button onclick="confirm('Hapus record ini?') || event.stopImmediatePropagation()" wire:click="deleteManual('{{ $row->id }}')" class="p-2 text-rose-300 hover:text-rose-500 transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="py-20 text-center">
                                        <p class="text-[10px] font-black text-slate-300 uppercase tracking-[0.4em]">Data riwayat tidak ditemukan</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="px-8 py-6 bg-slate-50/50 border-t border-slate-50">
                        {{ $riwayatNilai->links() }}
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- System Footer Info --}}
    <div class="pt-10 flex flex-col items-center gap-2 opacity-20 grayscale pointer-events-none border-t border-slate-100">
        <p class="text-[9px] font-black uppercase tracking-[0.6em] text-[#002855]">LEGACY GRADE MIGRATOR &bull; v4.2 PRO</p>
    </div>
</div>