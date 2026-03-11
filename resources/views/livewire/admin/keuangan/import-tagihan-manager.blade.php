<div class="space-y-8 animate-in fade-in duration-700 pb-12 max-w-[1600px] mx-auto p-4 md:p-8">
    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div class="space-y-2">
            <h1 class="text-3xl font-black text-[#002855] tracking-tight flex items-center gap-3">
                <div class="w-12 h-12 bg-[#002855] rounded-2xl flex items-center justify-center text-rose-400 shadow-xl shadow-blue-900/20">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                Migrasi Tunggakan Historis
            </h1>
            <p class="text-slate-400 font-medium text-sm ml-1 uppercase tracking-widest italic">Cut-Off Balance & Piutang Pra-SIAKAD</p>
        </div>
        <div class="flex bg-white p-1.5 rounded-2xl border border-slate-200 shadow-sm">
            <button wire:click="switchTab('import')" class="px-6 py-3 rounded-xl text-xs font-black uppercase tracking-widest transition-all {{ $activeTab == 'import' ? 'bg-[#002855] text-[#fcc000] shadow-lg' : 'text-slate-400 hover:text-[#002855]' }}">Import Excel</button>
            <button wire:click="switchTab('manual')" class="px-6 py-3 rounded-xl text-xs font-black uppercase tracking-widest transition-all {{ $activeTab == 'manual' ? 'bg-[#002855] text-[#fcc000] shadow-lg' : 'text-slate-400 hover:text-[#002855]' }}">Input Manual</button>
        </div>
    </div>

    @if($activeTab == 'import')
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Instruction Card --}}
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-rose-50 p-8 rounded-[2.5rem] border border-rose-100">
                    <h3 class="font-black text-rose-900 text-lg uppercase tracking-tight mb-6 flex items-center gap-2">
                        <svg class="w-5 h-5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        Panduan Import
                    </h3>
                    <ul class="space-y-4">
                        <li class="flex gap-3">
                            <div class="w-6 h-6 rounded-full bg-rose-200 text-rose-800 flex items-center justify-center text-[10px] font-black shrink-0">1</div>
                            <p class="text-xs text-rose-800/80 leading-relaxed font-bold">Gunakan template Excel yang disediakan. Jangan merubah nama/posisi Header.</p>
                        </li>
                        <li class="flex gap-3">
                            <div class="w-6 h-6 rounded-full bg-rose-200 text-rose-800 flex items-center justify-center text-[10px] font-black shrink-0">2</div>
                            <p class="text-xs text-rose-800/80 leading-relaxed font-bold">Pastikan <strong>NIM</strong> valid. Jika <strong>Kode Tahun</strong> tidak diisi, tagihan akan menjadi tagihan umum (Tanpa Semester).</p>
                        </li>
                        <li class="flex gap-3">
                            <div class="w-6 h-6 rounded-full bg-rose-200 text-rose-800 flex items-center justify-center text-[10px] font-black shrink-0">3</div>
                            <p class="text-xs text-rose-800/80 leading-relaxed font-bold">Tagihan yang terimport akan otomatis menahan (Block) hak mahasiswa dalam pengisian KRS berjalan.</p>
                        </li>
                    </ul>
                    <button wire:click="downloadTemplate" class="w-full mt-8 py-4 bg-white border-2 border-rose-200 text-rose-600 rounded-2xl font-black text-xs uppercase tracking-widest hover:border-rose-400 hover:text-rose-700 transition-all shadow-sm">Unduh Template CSV</button>
                </div>
            </div>

            {{-- Upload Area --}}
            <div class="lg:col-span-2">
                <div class="bg-white p-10 rounded-[3rem] border-4 border-dashed border-slate-200 flex flex-col items-center justify-center text-center shadow-sm">
                    <div class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mb-6 shadow-inner">
                        <svg class="w-12 h-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                    </div>
                    <h3 class="text-2xl font-black text-[#002855] uppercase tracking-tight italic">Unggah File Tunggakan</h3>
                    <p class="text-slate-400 text-xs mt-2 mb-8 uppercase tracking-widest font-bold">Maksimal 5MB (.xlsx / .csv)</p>

                    <div class="w-full max-w-md">
                        <input type="file" wire:model="file_excel" class="block w-full text-sm text-slate-500 file:mr-4 file:py-3 file:px-6 file:rounded-xl file:border-0 file:text-xs file:font-black file:uppercase file:tracking-widest file:bg-[#002855] file:text-white hover:file:bg-black cursor-pointer bg-slate-50 p-2 rounded-2xl border border-slate-200">
                        @error('file_excel') <span class="text-rose-500 text-[10px] font-bold mt-2 block">{{ $message }}</span> @enderror
                    </div>

                    <button wire:click="prosesImport" wire:loading.attr="disabled" class="mt-10 px-12 py-4 bg-emerald-500 text-white rounded-2xl font-black text-xs uppercase tracking-[0.2em] shadow-xl shadow-emerald-500/20 hover:scale-105 active:scale-95 transition-all disabled:opacity-50 disabled:scale-100 flex items-center justify-center gap-3">
                        <span wire:loading.remove wire:target="prosesImport">Eksekusi Import Data</span>
                        <span wire:loading wire:target="prosesImport" class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            Memproses Tagihan...
                        </span>
                    </button>
                </div>

                @if($importResult)
                    <div class="mt-8 p-8 rounded-[2rem] {{ $importResult['status'] == 'success' ? 'bg-emerald-50 border border-emerald-100' : 'bg-rose-50 border border-rose-100' }} animate-in slide-in-from-bottom-4">
                        <h4 class="font-black text-sm uppercase tracking-widest {{ $importResult['status'] == 'success' ? 'text-emerald-700' : 'text-rose-700' }}">Hasil Sinkronisasi Data:</h4>
                        @if($importResult['status'] == 'success')
                            <p class="text-sm font-black text-emerald-600 mt-2">{{ $importResult['count'] }} Baris Piutang Berhasil Diterbitkan ke Mahasiswa.</p>
                        @endif
                        @if(!empty($importResult['errors']))
                            <div class="mt-4 p-4 bg-white/60 rounded-xl border border-rose-200/50 max-h-48 overflow-y-auto custom-scrollbar space-y-2">
                                @foreach($importResult['errors'] as $err)
                                    <p class="text-[10px] text-rose-600 font-bold tracking-wide"><span class="bg-rose-100 px-2 py-0.5 rounded mr-2">Baris {{ $err['row'] }}</span> {{ $err['message'] }}</p>
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
                <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-200 space-y-6 lg:sticky lg:top-6">
                    <div class="border-b border-slate-100 pb-4">
                        <h3 class="text-sm font-black text-[#002855] uppercase tracking-[0.2em]">{{ $tagihan_id ? 'Update Tagihan Tunggakan' : 'Input Tunggakan Manual' }}</h3>
                        <p class="text-[10px] font-bold text-slate-400 mt-1 tracking-widest uppercase">Target 1 Mahasiswa</p>
                    </div>
                    
                    <div class="space-y-5">
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-2">NIM Mahasiswa *</label>
                            <input type="text" wire:model="nim" placeholder="Contoh: 21010001" class="w-full px-5 py-4 rounded-2xl bg-slate-50 border-slate-200 text-sm font-black focus:ring-[#fcc000] focus:border-[#fcc000] text-[#002855] @error('nim') border-rose-400 bg-rose-50 @enderror" {{ $tagihan_id ? 'disabled' : '' }}>
                            @error('nim') <span class="text-rose-500 text-[10px] font-bold mt-1 block pl-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-2">Kode Transaksi *</label>
                            <div class="relative">
                                <input type="text" wire:model="kode_transaksi" class="w-full px-5 py-4 rounded-2xl bg-white border-slate-200 text-xs font-mono font-bold text-slate-600 focus:ring-[#fcc000] uppercase" readonly>
                                <button wire:click="generateKodeTransaksi" class="absolute right-2 top-2 bottom-2 px-4 bg-slate-100 hover:bg-[#002855] hover:text-white rounded-xl text-[9px] font-black uppercase tracking-widest transition-colors" {{ $tagihan_id ? 'disabled' : '' }}>Acak Baru</button>
                            </div>
                            @error('kode_transaksi') <span class="text-rose-500 text-[10px] font-bold mt-1 block pl-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-2">Total Tunggakan (Rp) *</label>
                                <input type="number" wire:model="total_tagihan" placeholder="3500000" class="w-full px-5 py-4 rounded-2xl bg-rose-50 border-rose-100 text-sm font-black text-rose-700 focus:ring-rose-400 placeholder:text-rose-200">
                                @error('total_tagihan') <span class="text-rose-500 text-[10px] font-bold mt-1 block pl-1">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-2">Kode Tahun (Opsional)</label>
                                <input type="text" wire:model="kode_tahun" placeholder="Cth: 20211" class="w-full px-5 py-4 rounded-2xl bg-slate-50 border-slate-200 text-sm font-bold text-[#002855] focus:ring-[#fcc000]">
                                @error('kode_tahun') <span class="text-rose-500 text-[10px] font-bold mt-1 block pl-1">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-2">Deskripsi / Keterangan *</label>
                            <textarea wire:model="deskripsi" rows="3" class="w-full rounded-[1.5rem] border-slate-200 bg-slate-50 p-5 text-sm font-medium focus:ring-[#fcc000] resize-none" placeholder="Cth: Sisa Pembayaran Gedung Tahun 2018"></textarea>
                            @error('deskripsi') <span class="text-rose-500 text-[10px] font-bold mt-1 block pl-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="pt-6 flex flex-col sm:flex-row gap-3">
                        @if($tagihan_id)
                            <button wire:click="resetForm" class="px-6 py-4 bg-slate-100 text-slate-500 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-slate-200 transition-all text-center">Batal</button>
                        @endif
                        <button wire:click="saveManual" wire:loading.attr="disabled" class="flex-1 py-4 bg-[#fcc000] text-[#002855] rounded-2xl font-black text-xs uppercase tracking-[0.2em] shadow-xl shadow-amber-500/20 hover:-translate-y-1 active:scale-95 transition-all disabled:opacity-50">
                            <span wire:loading.remove wire:target="saveManual">{{ $tagihan_id ? 'Perbarui Tagihan' : 'Terbitkan Tagihan' }}</span>
                            <span wire:loading wire:target="saveManual">Menyimpan...</span>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Table Riwayat Piutang Legacy --}}
            <div class="xl:col-span-8">
                <div class="bg-white rounded-[3rem] shadow-sm border border-slate-200 overflow-hidden">
                    <div class="p-8 border-b border-slate-50 flex justify-between items-center bg-slate-50/50">
                        <h3 class="text-[11px] font-black text-[#002855] uppercase tracking-[0.3em]">Arsip Tunggakan Historis</h3>
                        <div class="relative w-64">
                            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari NIM / No. Invoice..." class="w-full pl-10 pr-4 py-3 bg-white border border-slate-200 rounded-xl text-xs font-bold focus:ring-[#002855] shadow-sm">
                            <svg class="w-4 h-4 text-slate-300 absolute left-4 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-100">
                            <thead class="bg-[#002855]">
                                <tr>
                                    <th class="px-8 py-5 text-left text-[10px] font-black text-white uppercase tracking-widest">Identitas Tagihan</th>
                                    <th class="px-6 py-5 text-left text-[10px] font-black text-white uppercase tracking-widest">Deskripsi (Periode)</th>
                                    <th class="px-6 py-5 text-right text-[10px] font-black text-white uppercase tracking-widest">Nilai Piutang</th>
                                    <th class="px-8 py-5 text-right text-[10px] font-black text-white uppercase tracking-widest">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50 bg-white">
                                @forelse($riwayatTagihan as $row)
                                <tr class="hover:bg-slate-50/80 transition-all group">
                                    <td class="px-8 py-6 align-top">
                                        <p class="text-sm font-black text-[#002855] uppercase tracking-tight">{{ $row->mahasiswa->person->nama_lengkap ?? 'Unknown' }}</p>
                                        <div class="flex items-center gap-2 mt-1.5">
                                            <span class="text-[10px] font-mono font-bold bg-slate-100 text-slate-500 px-2 py-0.5 rounded border border-slate-200">{{ $row->mahasiswa->nim }}</span>
                                            <span class="text-[9px] font-black text-rose-500 uppercase tracking-widest">{{ $row->kode_transaksi }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-6 align-top">
                                        <p class="text-xs font-bold text-slate-700 leading-snug max-w-[250px]">{{ $row->deskripsi }}</p>
                                        @if($row->tahunAkademik)
                                            <span class="inline-flex mt-2 text-[9px] font-black text-indigo-500 uppercase tracking-widest border border-indigo-100 bg-indigo-50 px-2 py-0.5 rounded">TA. {{ $row->tahunAkademik->kode_tahun }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-6 text-right align-top">
                                        <div class="inline-flex flex-col items-end">
                                            <span class="text-sm font-black text-rose-600 italic tracking-tighter">Rp {{ number_format($row->total_tagihan, 0, ',', '.') }}</span>
                                            
                                            @if($row->status_bayar == 'LUNAS')
                                                <span class="mt-1 text-[8px] font-black bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded uppercase tracking-widest">LUNAS</span>
                                            @elseif($row->status_bayar == 'CICIL')
                                                <span class="mt-1 text-[8px] font-black bg-amber-100 text-amber-700 px-2 py-0.5 rounded uppercase tracking-widest">DICICIL (Sisa: Rp {{ number_format($row->sisa_tagihan, 0, ',', '.') }})</span>
                                            @else
                                                <span class="mt-1 text-[8px] font-black bg-rose-100 text-rose-700 px-2 py-0.5 rounded uppercase tracking-widest">BELUM BAYAR</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 text-right align-top">
                                        <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <button wire:click="editManual('{{ $row->id }}')" class="p-2.5 text-indigo-600 bg-indigo-50 hover:bg-indigo-100 rounded-xl transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button>
                                            <button wire:click="deleteManual('{{ $row->id }}')" wire:confirm="Batalkan/Hapus Tagihan ini secara permanen?" class="p-2.5 text-rose-500 bg-rose-50 hover:bg-rose-100 rounded-xl transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="py-24 text-center">
                                        <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 text-3xl grayscale opacity-30">💳</div>
                                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.4em]">Tidak ada data tagihan legacy</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="px-8 py-5 bg-slate-50/80 border-t border-slate-100">
                        {{ $riwayatTagihan->links() }}
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- System Footer Info --}}
    <div class="pt-10 flex flex-col items-center gap-2 opacity-20 grayscale pointer-events-none border-t border-slate-100">
        <p class="text-[9px] font-black uppercase tracking-[0.6em] text-[#002855]">SIAKAD FINANCIAL BILLING MIGRATOR &bull; v4.2 PRO</p>
    </div>

    {{-- SweetAlert2 Listener --}}
    @script
    <script>
        $wire.on('swal:success', data => { alert(data[0].text); });
        $wire.on('swal:error', data => { alert(data[0].text); });
    </script>
    @endscript

    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(244, 63, 94, 0.2); border-radius: 10px; }
    </style>
</div>