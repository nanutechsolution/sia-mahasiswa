<div class="bg-white rounded-3xl shadow-xl border border-slate-200 overflow-hidden animate-in slide-in-from-top-4 duration-500">
<!-- Header Form -->
<div class="px-8 py-6 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
<div>
<h3 class="text-sm font-black text-unmaris-blue uppercase tracking-widest">
{{ $skemaId ? 'Update Konfigurasi Paket' : 'Setup Master Skema Baru' }}
</h3>
<p class="text-[10px] text-slate-400 font-bold uppercase mt-1">Financial Setup Engine</p>
</div>
<button wire:click="batal" class="px-5 py-2 bg-white border border-slate-200 text-slate-500 rounded-xl text-xs font-bold hover:bg-slate-50 hover:text-unmaris-blue transition-all shadow-sm">
Kembali
</button>
</div>

<div class="p-8 lg:p-12">
    <!-- HEADER FORM FIELDS -->
    <div class="grid grid-cols-1 md:grid-cols-12 gap-8 mb-12">
        <div class="md:col-span-8">
            <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-2">Nama Paket Tarif (Labeling) *</label>
            <input type="text" wire:model="nama_skema" placeholder="Contoh: Paket Reguler TI 2024" 
                   class="block w-full rounded-2xl border-slate-200 bg-slate-50 py-4 px-5 focus:bg-white focus:border-unmaris-blue focus:ring-4 focus:ring-unmaris-blue/5 text-sm font-semibold transition-all outline-none">
            @error('nama_skema') <span class="text-rose-500 text-[11px] font-bold mt-2 block">{{ $message }}</span> @enderror
        </div>
        <div class="md:col-span-4">
            <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-2">Angkatan *</label>
            <select wire:model="angkatan_id" class="block w-full rounded-2xl border-slate-200 bg-slate-50 py-4 px-5 focus:bg-white focus:border-unmaris-blue text-sm font-semibold transition-all outline-none">
                <option value="">Pilih Angkatan</option>
                @foreach($angkatans as $akt)
                    <option value="{{ $akt->id_tahun }}">{{ $akt->id_tahun }}</option>
                @endforeach
            </select>
            @error('angkatan_id') <span class="text-rose-500 text-[11px] font-bold mt-2 block">{{ $message }}</span> @enderror
        </div>
        <div class="md:col-span-4">
            <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-2">Program Kelas *</label>
            <select wire:model="program_kelas_id" class="block w-full rounded-2xl border-slate-200 bg-slate-50 py-4 px-5 focus:bg-white focus:border-unmaris-blue text-sm font-semibold transition-all outline-none">
                <option value="">Pilih Kelas</option>
                @foreach($programKelas as $pk)
                    <option value="{{ $pk->id }}">{{ $pk->nama_program }}</option>
                @endforeach
            </select>
            @error('program_kelas_id') <span class="text-rose-500 text-[11px] font-bold mt-2 block">{{ $message }}</span> @enderror
        </div>
        <div class="md:col-span-8">
            <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-2">Target Program Studi *</label>
            <select wire:model="prodi_id" class="block w-full rounded-2xl border-slate-200 bg-slate-50 py-4 px-5 focus:bg-white focus:border-unmaris-blue text-sm font-semibold transition-all outline-none">
                <option value="">Pilih Prodi</option>
                @foreach($prodis as $p)
                    <option value="{{ $p->id }}">{{ $p->jenjang }} - {{ $p->nama_prodi }}</option>
                @endforeach
            </select>
            @error('prodi_id') <span class="text-rose-500 text-[11px] font-bold mt-2 block">{{ $message }}</span> @enderror
        </div>
    </div>

    <!-- DETAIL ITEMS SECTION -->
    <div class="border-t border-slate-100 pt-10">
        <div class="flex items-center justify-between mb-6">
            <h4 class="text-xs font-black text-unmaris-blue uppercase tracking-[0.2em]">Rincian Ledger Komponen</h4>
            <button wire:click="addItem" class="inline-flex items-center px-6 py-2.5 bg-emerald-50 text-emerald-600 rounded-xl text-[11px] font-black uppercase tracking-widest hover:bg-emerald-600 hover:text-white transition-all border border-emerald-100 shadow-sm">
                + Tambah Baris Biaya
            </button>
        </div>
        
        {{-- Pro Tip Box --}}
        <div class="bg-amber-50 rounded-2xl p-6 border border-amber-100 mb-8 flex items-start space-x-5">
            <div class="p-3 bg-unmaris-yellow rounded-xl shadow-lg shadow-amber-200/50">
                <svg class="w-6 h-6 text-unmaris-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="text-[12px] text-slate-600 leading-relaxed">
                <span class="font-black text-unmaris-blue uppercase block mb-1 tracking-widest">Financial Strategy:</span>
                <ul class="list-disc pl-4 space-y-1 font-medium">
                    <li>Biaya <span class="font-bold">SAMA RATA</span> setiap semester: Kosongkan kolom <span class="italic text-unmaris-blue">Semester</span>.</li>
                    <li>Biaya <span class="font-bold">BERUBAH</span> (misal: naik di SMT 3): Input baris terpisah per semester secara manual.</li>
                </ul>
            </div>
        </div>

        <div class="bg-white rounded-3xl border border-slate-100 overflow-hidden shadow-sm">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                        <th class="px-8 py-5">Item Komponen Biaya</th>
                        <th class="px-8 py-5 w-72">Nominal (IDR)</th>
                        <th class="px-8 py-5 w-48 text-center">Semester Khusus</th>
                        <th class="px-8 py-5 w-20"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($items as $index => $item)
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <td class="px-8 py-4">
                            <select wire:model="items.{{ $index }}.komponen_id" class="block w-full bg-white border border-slate-200 rounded-xl py-3 px-4 focus:ring-4 focus:ring-unmaris-blue/5 focus:border-unmaris-blue text-sm font-bold text-slate-700 outline-none shadow-sm transition-all">
                                <option value="">-- Pilih Komponen --</option>
                                @foreach($komponens as $kom)
                                    <option value="{{ $kom->id }}">{{ $kom->nama_komponen }} ({{ $kom->tipe_biaya }})</option>
                                @endforeach
                            </select>
                            @error("items.{$index}.komponen_id") <span class="text-rose-500 text-[10px] font-bold px-1 mt-1 block">Wajib pilih</span> @enderror
                        </td>
                        <td class="px-8 py-4">
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-4 flex items-center font-bold text-slate-300 text-sm">Rp</span>
                                <input type="number" wire:model="items.{{ $index }}.nominal" class="block w-full pl-11 pr-4 bg-white border border-slate-200 rounded-xl py-3 text-sm font-black text-unmaris-blue focus:ring-4 focus:ring-unmaris-blue/5 focus:border-unmaris-blue outline-none shadow-sm transition-all tabular-nums">
                            </div>
                            @error("items.{$index}.nominal") <span class="text-rose-500 text-[10px] font-bold px-1 mt-1 block">Wajib isi</span> @enderror
                        </td>
                        <td class="px-8 py-4">
                            <input type="number" wire:model="items.{{ $index }}.semester" placeholder="Global" class="block w-full bg-white border border-slate-200 rounded-xl py-3 text-center text-sm font-bold text-slate-500 focus:ring-4 focus:ring-unmaris-blue/5 focus:border-unmaris-blue outline-none shadow-sm transition-all">
                        </td>
                        <td class="px-8 py-4 text-center">
                            <button wire:click="removeItem({{ $index }})" class="p-2.5 text-slate-300 hover:text-rose-500 hover:bg-rose-50 rounded-xl transition-all group-hover:scale-110">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-12 pt-10 border-t border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="flex items-center space-x-3 text-slate-400">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m0 0v3m0-3h3m-3 0H9m12-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span class="text-[11px] font-black uppercase tracking-[0.2em] italic">Configuration Draft Mode</span>
        </div>
        <button wire:click="save" class="group relative px-16 py-4 bg-unmaris-blue text-white rounded-2xl text-sm font-black shadow-2xl shadow-indigo-300 hover:scale-105 transition-all uppercase tracking-[0.2em] overflow-hidden">
            <span class="relative z-10">SIMPAN KONFIGURASI SKEMA</span>
            <div class="absolute inset-0 bg-gradient-to-r from-unmaris-blue to-indigo-600 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
        </button>
    </div>
</div>


</div>