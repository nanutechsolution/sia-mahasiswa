<div>
    {{-- SEO & Header Layout Integration --}}
    <x-slot name="title">Manajemen Komponen Biaya</x-slot>
    <x-slot name="header">Master Komponen Biaya</x-slot>

    <div class="space-y-8">
        {{-- Top Toolbar --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <p class="text-slate-500 text-sm font-medium">Definisikan jenis-jenis tagihan resmi berdasarkan SK Rektor Universitas Stella Maris Sumba.</p>
            </div>

            @if(!$showForm)
            <button wire:click="create"
                class="inline-flex items-center px-6 py-3 bg-unmaris-yellow text-unmaris-blue rounded-xl font-black text-sm shadow-lg shadow-unmaris-yellow/20 hover:scale-105 transition-all">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Komponen
            </button>
            @endif
        </div>

        {{-- Notifications --}}
        @if (session()->has('success'))
        <div class="bg-emerald-50 border border-emerald-100 p-4 rounded-2xl text-emerald-800 text-sm flex items-center animate-in fade-in duration-300 shadow-sm">
            <svg class="w-5 h-5 mr-3 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            <span class="font-bold">{{ session('success') }}</span>
        </div>
        @endif

        {{-- Form Section --}}
        @if($showForm)
        <div class="bg-white rounded-3xl shadow-xl border border-slate-200 overflow-hidden animate-in slide-in-from-top-4 duration-500">
            <div class="px-8 py-6 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
                <div>
                    <h3 class="text-sm font-black text-unmaris-blue uppercase tracking-widest">
                        {{ $editMode ? 'Perbarui Komponen' : 'Setup Komponen Baru' }}
                    </h3>
                    <p class="text-[10px] text-slate-400 font-bold uppercase mt-1">Ledger Configuration</p>
                </div>
                <button wire:click="$set('showForm', false)" class="text-xs font-bold text-slate-400 hover:text-slate-600 transition-colors uppercase tracking-widest">Batalkan</button>
            </div>

            <div class="p-8 lg:p-10">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-2">Nama Komponen (Sesuai SK) *</label>
                        <input type="text" wire:model="nama_komponen" placeholder="Contoh: Biaya Penyelenggaraan Pendidikan"
                            class="block w-full rounded-2xl border-slate-200 bg-slate-50 py-4 px-5 focus:bg-white focus:border-unmaris-blue focus:ring-4 focus:ring-unmaris-blue/5 text-sm font-semibold transition-all outline-none">
                        @error('nama_komponen') <span class="text-rose-500 text-[11px] font-bold mt-2 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-2">Klasifikasi Tipe Tagihan *</label>
                        <select wire:model="tipe_biaya" class="block w-full rounded-2xl border-slate-200 bg-slate-50 py-4 px-5 focus:bg-white focus:border-unmaris-blue text-sm font-semibold transition-all outline-none">
                            <option value="">-- Pilih Tipe --</option>
                            <option value="TETAP">TETAP (Rutin per Semester)</option>
                            <option value="SKS">PER SKS (Variabel Perkuliahan)</option>
                            <option value="SEKALI">SEKALI (Awal Masuk Saja)</option>
                            <option value="INSIDENTAL">INSIDENTAL (Non-Reguler)</option>
                        </select>
                        @error('tipe_biaya') <span class="text-rose-500 text-[11px] font-bold mt-2 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="mt-10 pt-8 border-t border-slate-100 flex justify-end">
                    <button wire:click="save" class="px-12 py-4 bg-unmaris-blue text-white rounded-2xl text-sm font-black shadow-2xl shadow-indigo-200 hover:scale-105 transition-all uppercase tracking-widest">
                        Simpan Komponen Biaya
                    </button>
                </div>
            </div>
        </div>
        @endif

        {{-- Table Section --}}
        <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-100 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                            <th class="px-8 py-5 w-20">No</th>
                            <th class="px-8 py-5">Identitas Komponen Biaya</th>
                            <th class="px-8 py-5 text-center">Tipe Penagihan</th>
                            <th class="px-8 py-5 text-right">Opsi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($komponen as $index => $k)
                        <tr class="hover:bg-slate-50/50 transition-colors group">
                            <td class="px-8 py-6 text-sm font-bold text-slate-400 tabular-nums">
                                {{ ($komponen->currentPage() - 1) * $komponen->perPage() + $loop->iteration }}
                            </td>
                            <td class="px-8 py-6">
                                <div class="text-sm font-black text-slate-800 leading-tight uppercase tracking-tight">{{ $k->nama_komponen }}</div>
                                <div class="text-[10px] text-slate-400 font-bold mt-1 uppercase tracking-widest">SK Rektor Registered</div>
                            </td>
                            <td class="px-8 py-6 text-center">
                                <span class="px-3 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-widest border
                                @switch($k->tipe_biaya)
                                    @case('TETAP') bg-blue-50 text-blue-600 border-blue-100 @break
                                    @case('SEKALI') bg-purple-50 text-purple-600 border-purple-100 @break
                                    @case('SKS') bg-amber-50 text-amber-600 border-amber-100 @break
                                    @default bg-slate-50 text-slate-600 border-slate-100
                                @endswitch">
                                    {{ $k->tipe_biaya }}
                                </span>
                            </td>
                            <td class="px-8 py-6 text-right">
                                <div class="flex justify-end space-x-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button wire:click="edit({{ $k->id }})" class="p-2.5 text-slate-400 hover:text-unmaris-blue hover:bg-unmaris-blue/5 rounded-xl transition-all shadow-sm">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button wire:click="delete({{ $k->id }})" wire:confirm="Hapus komponen ini?" class="p-2.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition-all shadow-sm">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-8 py-5 bg-slate-50/50 border-t border-slate-100">
                {{ $komponen->links() }}
            </div>
        </div>
    </div>


</div>