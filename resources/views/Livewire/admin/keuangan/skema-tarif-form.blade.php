 <div class="bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden animate-in slide-in-from-bottom-5 duration-300">
        
        {{-- 1. Header Form --}}
        <div class="px-8 py-5 border-b border-slate-100 bg-slate-50 flex justify-between items-center sticky top-0 z-10">
            <div class="flex items-center gap-3">
                <div class="bg-[#002855] p-2 rounded-lg text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 3.666V14m0-7.104l-8.68 3.098a1.57 1.57 0 00-.78 1.692l.435 2.85c.193 1.27.105 2.56.087 3.065.11 3.553 4.253 4.854 4.253 4.854s4.143-1.3 4.253-4.853c-.018-.505-.106-1.794.087-3.065l.435-2.85c.298-1.897-1.42-3.348-3.34-2.671z"/></svg>
                </div>
                <div>
                    <h3 class="text-sm font-black text-[#002855] uppercase tracking-wider">
                        {{ $skemaId ? 'Edit Skema Tarif' : 'Setup Skema Baru' }}
                    </h3>
                    <p class="text-[10px] text-slate-500 font-medium">Konfigurasi paket pembayaran mahasiswa</p>
                </div>
            </div>
            <button wire:click="batal" class="group flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 text-slate-500 rounded-lg text-xs font-bold hover:bg-slate-50 hover:text-rose-500 transition-all">
                <span>Batal</span>
                <svg class="w-4 h-4 group-hover:rotate-90 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="p-8 space-y-8">
            
            {{-- 2. Identitas Skema --}}
            <div class="bg-white p-1">
                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-6 flex items-center gap-2">
                    <span class="w-2 h-2 bg-[#fcc000] rounded-full"></span> Identitas Paket
                </h4>
                
                <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
                    {{-- Nama Paket --}}
                    <div class="md:col-span-6">
                        <label class="block text-[11px] font-bold text-[#002855] uppercase tracking-widest mb-2">Nama Label Paket *</label>
                        <input type="text" wire:model="nama_skema" placeholder="Contoh: Paket Reguler TI 2024"
                            class="block w-full rounded-xl border-slate-300 bg-white p-3 text-sm font-bold text-slate-700 focus:border-[#002855] focus:ring-[#002855] placeholder-slate-300 transition-all">
                        @error('nama_skema') <span class="text-rose-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- Angkatan --}}
                    <div class="md:col-span-3">
                        <label class="block text-[11px] font-bold text-[#002855] uppercase tracking-widest mb-2">Target Angkatan *</label>
                        <div class="relative">
                            <select wire:model="angkatan_id" class="block w-full rounded-xl border-slate-300 bg-slate-50 p-3 text-sm font-bold text-slate-700 focus:border-[#002855] focus:ring-[#002855] appearance-none cursor-pointer">
                                <option value="">-- Pilih --</option>
                                @foreach($angkatans as $akt)
                                <option value="{{ $akt->id_tahun }}">{{ $akt->id_tahun }}</option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-500"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg></div>
                        </div>
                        @error('angkatan_id') <span class="text-rose-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- Kelas --}}
                    <div class="md:col-span-3">
                        <label class="block text-[11px] font-bold text-[#002855] uppercase tracking-widest mb-2">Program Kelas *</label>
                        <div class="relative">
                            <select wire:model="program_kelas_id" class="block w-full rounded-xl border-slate-300 bg-slate-50 p-3 text-sm font-bold text-slate-700 focus:border-[#002855] focus:ring-[#002855] appearance-none cursor-pointer">
                                <option value="">-- Pilih --</option>
                                @foreach($programKelas as $pk)
                                <option value="{{ $pk->id }}">{{ $pk->nama_program }}</option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-500"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg></div>
                        </div>
                        @error('program_kelas_id') <span class="text-rose-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- Prodi --}}
                    <div class="md:col-span-12">
                        <label class="block text-[11px] font-bold text-[#002855] uppercase tracking-widest mb-2">Program Studi Target *</label>
                        <div class="relative">
                            <select wire:model="prodi_id" class="block w-full rounded-xl border-slate-300 bg-slate-50 p-3 text-sm font-bold text-slate-700 focus:border-[#002855] focus:ring-[#002855] appearance-none cursor-pointer">
                                <option value="">-- Pilih Program Studi --</option>
                                @foreach($prodis as $p)
                                <option value="{{ $p->id }}">{{ $p->jenjang }} - {{ $p->nama_prodi }}</option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-500"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg></div>
                        </div>
                        @error('prodi_id') <span class="text-rose-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- Separator --}}
            <div class="border-t border-dashed border-slate-200"></div>

            {{-- 3. Rincian Biaya (Detail Items) --}}
            <div>
                <div class="flex items-center justify-between mb-6">
                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest flex items-center gap-2">
                        <span class="w-2 h-2 bg-[#fcc000] rounded-full"></span> Komponen Biaya
                    </h4>
                    <button wire:click="addItem" class="inline-flex items-center px-4 py-2 bg-emerald-50 text-emerald-700 rounded-lg text-[10px] font-black uppercase tracking-widest hover:bg-emerald-100 transition-colors border border-emerald-200">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Tambah Item
                    </button>
                </div>

                {{-- Pro Tip --}}
                <div class="bg-amber-50 rounded-xl p-4 border border-amber-100 mb-6 flex gap-4 items-start">
                    <svg class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <div class="text-xs text-amber-800 leading-relaxed">
                        <span class="font-bold block mb-1">Tips Pengaturan:</span>
                        Biaya yang sama tiap semester cukup diinput sekali dengan mengosongkan kolom <strong>Semester</strong>. Jika biaya berbeda (misal SPI hanya di Smt 1), isi kolom Semester dengan angka spesifik (1, 2, dst).
                    </div>
                </div>

                <div class="bg-slate-50/50 rounded-xl border border-slate-200 overflow-hidden">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-100 text-[10px] font-black text-slate-500 uppercase tracking-widest border-b border-slate-200">
                                <th class="px-6 py-4 w-[40%]">Nama Komponen</th>
                                <th class="px-6 py-4 w-[35%]">Nominal (IDR)</th>
                                <th class="px-6 py-4 w-[15%] text-center">Semester</th>
                                <th class="px-6 py-4 w-[10%] text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($items as $index => $item)
                            <tr class="group hover:bg-white transition-colors" wire:key="item-{{ $index }}">
                                <td class="px-6 py-3 align-top">
                                    <select wire:model="items.{{ $index }}.komponen_id" class="block w-full rounded-lg border-slate-300 text-sm font-bold text-slate-700 focus:border-[#002855] focus:ring-[#002855] p-2.5">
                                        <option value="">-- Pilih --</option>
                                        @foreach($komponens as $kom)
                                        <option value="{{ $kom->id }}">{{ $kom->nama_komponen }} ({{ $kom->tipe_biaya }})</option>
                                        @endforeach
                                    </select>
                                    @error("items.{$index}.komponen_id") <span class="text-rose-500 text-[10px] font-bold mt-1 block">Wajib pilih</span> @enderror
                                </td>
                                
                                <td class="px-6 py-3 align-top">
                                    {{-- INPUT NOMINAL DENGAN FORMAT RUPIAH (ALPINE JS) --}}
                                    <div class="relative"
                                         x-data="{
                                            displayValue: '',
                                            model: @entangle('items.'.$index.'.nominal'),
                                            formatRupiah(value) {
                                                if (!value && value !== 0) return '';
                                                return new Intl.NumberFormat('id-ID').format(value);
                                            },
                                            updateValue(event) {
                                                // Hapus non-digit
                                                let raw = event.target.value.replace(/\D/g, '');
                                                this.model = raw; // Update Livewire property
                                                this.displayValue = this.formatRupiah(raw); // Update tampilan
                                            }
                                         }"
                                         x-init="displayValue = formatRupiah(model); $watch('model', value => displayValue = formatRupiah(value))"
                                    >
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-slate-400 font-bold text-xs">Rp</span>
                                        </div>
                                        <input type="text" 
                                            x-model="displayValue" 
                                            @input="updateValue"
                                            class="block w-full rounded-lg border-slate-300 pl-10 pr-3 py-2.5 text-sm font-bold text-right text-[#002855] focus:border-[#002855] focus:ring-[#002855]"
                                            placeholder="0"
                                        >
                                    </div>
                                    @error("items.{$index}.nominal") <span class="text-rose-500 text-[10px] font-bold mt-1 block">Wajib isi</span> @enderror
                                </td>

                                <td class="px-6 py-3 align-top">
                                    <input type="number" wire:model="items.{{ $index }}.semester" placeholder="All" class="block w-full rounded-lg border-slate-300 text-sm font-bold text-center text-slate-500 focus:border-[#002855] focus:ring-[#002855] p-2.5 placeholder-slate-300">
                                </td>

                                <td class="px-6 py-3 align-top text-center">
                                    <button wire:click="removeItem({{ $index }})" class="p-2 text-slate-300 hover:text-rose-500 hover:bg-rose-50 rounded-lg transition-colors" title="Hapus Baris">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                            @if(count($items) === 0)
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-slate-400 italic text-xs">
                                    Belum ada komponen biaya. Klik tombol "Tambah Item" di atas.
                                </td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- 4. Footer Actions --}}
            <div class="pt-6 border-t border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2 text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-[10px] font-bold uppercase tracking-widest">Pastikan data sudah benar</span>
                </div>
                <div class="flex gap-3">
                    <button wire:click="batal" class="px-6 py-3 rounded-xl border border-slate-200 text-slate-600 text-xs font-bold uppercase tracking-widest hover:bg-slate-50 transition-colors">
                        Batalkan
                    </button>
                    <button wire:click="save" class="px-8 py-3 rounded-xl bg-[#002855] text-white text-xs font-bold uppercase tracking-widest shadow-lg shadow-indigo-900/20 hover:bg-[#001a38] hover:scale-105 transition-all flex items-center gap-2">
                        <svg wire:loading.remove wire:target="save" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <svg wire:loading wire:target="save" class="w-4 h-4 animate-spin" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        Simpan Skala
                    </button>
                </div>
            </div>

        </div>
    </div>