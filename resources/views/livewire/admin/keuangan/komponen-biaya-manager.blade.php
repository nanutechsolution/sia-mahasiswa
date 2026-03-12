<div class="max-w-[1600px] mx-auto p-4 md:p-8 space-y-8 animate-in fade-in duration-500">

    {{-- 1. HEADER & SUMMARY CARDS --}}
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-[#002855] rounded-2xl flex items-center justify-center text-[#fcc000] shadow-lg shadow-blue-900/20">
                <x-heroicon-o-banknotes class="w-7 h-7" />
            </div>
            <div class="space-y-1">
                <h1 class="text-2xl font-black text-[#002855] tracking-tight uppercase">Katalog Komponen Biaya</h1>
                <p class="text-slate-500 text-sm font-medium italic uppercase tracking-wider">Standardisasi Jenis Tagihan Berdasarkan SK Rektor</p>
            </div>
        </div>

        @if(!$showForm)
        <button wire:click="create" class="px-8 py-3 bg-[#fcc000] text-[#002855] rounded-xl font-black text-xs uppercase tracking-widest shadow-lg shadow-amber-500/20 hover:bg-[#fbbf24] hover:scale-105 transition-all flex items-center gap-3">
            <x-heroicon-s-plus class="w-5 h-5" />
            Tambah Komponen
        </button>
        @endif
    </div>

    {{-- Stats Bento Box --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm flex items-center gap-5 group hover:border-[#002855] transition-all">
            <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center text-xl shadow-inner group-hover:scale-110 transition-transform">📋</div>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Total Komponen</p>
                <h3 class="text-2xl font-black text-[#002855] italic">{{ $stats['total'] }} <span class="text-xs not-italic text-slate-300 font-bold uppercase tracking-tighter">Items</span></h3>
            </div>
        </div>
        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm flex items-center gap-5 group hover:border-emerald-500 transition-all">
            <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center text-xl shadow-inner group-hover:scale-110 transition-transform">💰</div>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Biaya Tetap</p>
                <h3 class="text-2xl font-black text-emerald-600 italic">{{ $stats['tetap'] }} <span class="text-xs not-italic text-slate-300 font-bold uppercase tracking-tighter">Items</span></h3>
            </div>
        </div>
        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm flex items-center gap-5 group hover:border-amber-500 transition-all">
            <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center text-xl shadow-inner group-hover:scale-110 transition-transform">📏</div>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Biaya Per SKS</p>
                <h3 class="text-2xl font-black text-amber-600 italic">{{ $stats['sks'] }} <span class="text-xs not-italic text-slate-300 font-bold uppercase tracking-tighter">Items</span></h3>
            </div>
        </div>
    </div>

    {{-- 2. FORM SECTION (MODAL STYLE) --}}
    @if($showForm)
    <div class="bg-white rounded-[2.5rem] shadow-2xl border border-slate-200 overflow-hidden animate-in zoom-in-95 duration-500 max-w-4xl mx-auto">
        <div class="px-10 py-8 bg-slate-50/50 border-b border-slate-100 flex justify-between items-center">
            <div>
                <h3 class="text-xl font-black text-[#002855] uppercase tracking-tight italic">
                    {{ $editMode ? 'Update Komponen' : 'Setup Komponen Baru' }}
                </h3>
                <p class="text-[10px] font-bold text-slate-400 mt-1 uppercase tracking-widest">Financial Master Data Configuration</p>
            </div>
            <button wire:click="$set('showForm', false)" class="text-slate-400 hover:text-rose-500 transition-colors bg-white p-2 rounded-full shadow-sm">
                <x-heroicon-s-x-mark class="w-6 h-6" />
            </button>
        </div>

        <div class="p-10 space-y-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                {{-- Field Nama Komponen --}}
                <div class="space-y-2 min-w-0">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Nama Komponen (Sesuai SK) *</label>
                    <input type="text" wire:model="nama_komponen" placeholder="Misal: SPP TETAP"
                        class="w-full rounded-xl border-slate-200 bg-slate-50 py-4 px-6 text-sm font-bold text-[#002855] focus:ring-2 focus:ring-[#fcc000] focus:border-[#fcc000] transition-all uppercase placeholder-slate-300 shadow-xs">
                    @error('nama_komponen') <span class="text-rose-500 text-[10px] font-bold mt-1 block ml-1 uppercase">{{ $message }}</span> @enderror
                </div>

                {{-- Field Tipe Biaya - Perbaikan: w-full & min-w-0 --}}
                <div class="space-y-2 min-w-0">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Klasifikasi Tipe Tagihan *</label>
                    <div class="relative min-w-0 flex-1">
                        <select wire:model="tipe_biaya" class="w-full rounded-xl border-slate-200 bg-slate-50 py-4 px-6 text-sm font-bold text-[#002855] focus:ring-2 focus:ring-[#fcc000] focus:border-[#fcc000] transition-all cursor-pointer appearance-none shadow-xs">
                            <option value="">-- Pilih Tipe --</option>
                            <option value="TETAP">TETAP (Tagihan per Semester)</option>
                            <option value="SKS">PER SKS (Tergantung Jumlah SKS)</option>
                            <option value="SEKALI">SEKALI (Tagihan Mahasiswa Baru)</option>
                            <option value="INSIDENTAL">INSIDENTAL (Denda/Administrasi)</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-4 flex items-center text-slate-400">
                            <x-heroicon-s-chevron-down class="w-4 h-4" />
                        </div>
                    </div>
                    @error('tipe_biaya') <span class="text-rose-500 text-[10px] font-bold mt-1 block ml-1 uppercase">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="bg-indigo-50/50 p-6 rounded-2xl border border-indigo-100 flex items-start gap-4">
                <div class="text-indigo-500 mt-0.5">
                    <x-heroicon-s-information-circle class="w-5 h-5" />
                </div>
                <p class="text-[11px] font-bold text-indigo-700 leading-relaxed italic uppercase tracking-tight">
                    Catatan Akuntansi: Nama komponen yang dimasukkan akan tercetak otomatis pada Invoice mahasiswa. Pastikan penulisan baku sesuai Nomenklatur Keuangan Pusat.
                </p>
            </div>
        </div>

        <div class="px-10 py-8 bg-slate-50/50 border-t border-slate-100 flex flex-col md:flex-row justify-end gap-3">
            <button wire:click="$set('showForm', false)" class="px-8 py-3 text-xs font-black text-slate-500 uppercase tracking-widest hover:bg-slate-200 rounded-xl transition-all">Batal</button>
            <button wire:click="save" class="px-12 py-3 bg-[#002855] text-white rounded-xl font-black text-xs uppercase tracking-[0.2em] shadow-xl shadow-blue-900/20 hover:bg-[#001a38] transition-all flex items-center justify-center gap-3">
                <x-heroicon-s-check-circle class="w-5 h-5" />
                Sah & Simpan Komponen
            </button>
        </div>
    </div>
    @endif

    {{-- 3. TABLE SECTION --}}
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden relative">
        {{-- Search Bar Inside Table Header --}}
        <div class="px-10 py-6 border-b border-slate-100 flex flex-col md:flex-row justify-between items-center gap-6 bg-slate-50/30">
            <h3 class="text-xs font-black text-[#002855] uppercase tracking-widest flex items-center gap-2">
                <div class="w-1.5 h-1.5 rounded-full bg-[#fcc000] animate-pulse"></div>
                Daftar Komponen Aktif
            </h3>
            <div class="relative w-full md:w-96 min-w-0">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari Nama Komponen..."
                    class="w-full rounded-xl border-slate-200 bg-white py-2.5 pl-12 pr-4 text-xs font-bold focus:ring-2 focus:ring-[#fcc000] transition-all shadow-sm outline-none">
                <x-heroicon-o-magnifying-glass class="w-5 h-5 absolute left-4 top-2.5 text-slate-400" />
            </div>
        </div>

        <div class="overflow-x-auto custom-scrollbar">
            <table class="min-w-full border-collapse">
                <thead>
                    <tr class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] bg-slate-50/50">
                        <th class="px-10 py-5 text-left w-24 italic">#ID</th>
                        <th class="px-8 py-5 text-left">Nomenklatur Komponen</th>
                        <th class="px-8 py-5 text-center">Klasifikasi</th>
                        <th class="px-10 py-5 text-right">Manajemen</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($komponen as $k)
                    <tr class="hover:bg-slate-50/80 transition-all group">
                        <td class="px-10 py-6 text-xs font-mono font-bold text-slate-300 group-hover:text-[#002855] transition-colors">{{ $k->id }}</td>
                        <td class="px-8 py-6">
                            <div class="text-sm font-black text-slate-800 uppercase tracking-tight group-hover:text-indigo-600 transition-colors leading-none mb-1.5">{{ $k->nama_komponen }}</div>
                            <div class="flex items-center gap-2 text-[9px] font-bold text-slate-400 uppercase tracking-widest">
                                <x-heroicon-s-calendar class="w-3 h-3 opacity-50" />
                                Terdaftar {{ $k->created_at->format('d M Y') }}
                            </div>
                        </td>
                        <td class="px-8 py-6 text-center">
                            @php
                            $badgeStyle = match($k->tipe_biaya) {
                                'TETAP' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                'SKS' => 'bg-amber-50 text-amber-600 border-amber-100',
                                'SEKALI' => 'bg-indigo-50 text-indigo-600 border-indigo-100',
                                'INSIDENTAL' => 'bg-rose-50 text-rose-600 border-rose-100',
                                default => 'bg-slate-50 text-slate-500 border-slate-200'
                            };
                            @endphp
                            <span class="inline-flex px-3 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest border shadow-xs {{ $badgeStyle }}">
                                {{ $k->tipe_biaya }}
                            </span>
                        </td>
                        <td class="px-10 py-6 text-right">
                            <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-all duration-300">
                                <button wire:click="edit('{{ $k->id }}')" class="p-2.5 bg-indigo-50 text-indigo-600 rounded-xl hover:bg-[#002855] hover:text-white transition-all shadow-sm">
                                    <x-heroicon-s-pencil class="w-4 h-4" />
                                </button>
                                <button wire:click="delete('{{ $k->id }}')" wire:confirm="PERINGATAN: Menghapus komponen dapat merusak integrasi data tagihan lama. Lanjutkan?" class="p-2.5 bg-rose-50 text-rose-500 rounded-xl hover:bg-rose-500 hover:text-white transition-all shadow-sm">
                                    <x-heroicon-s-trash class="w-4 h-4" />
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-24 text-center">
                            <div class="flex flex-col items-center justify-center opacity-30 grayscale italic pointer-events-none">
                                <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center text-3xl mb-4 shadow-inner">📦</div>
                                <p class="text-xs font-black text-slate-500 uppercase tracking-[0.3em]">Belum Ada Komponen Terdaftar</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-10 py-6 bg-slate-50/50 border-t border-slate-100 flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                Showing Halaman {{ $komponen->currentPage() }} dari {{ $komponen->lastPage() }}
            </div>
            <div class="flex-shrink-0">{{ $komponen->links() }}</div>
        </div>
    </div>

    {{-- System Footer --}}
    <div class="flex items-center justify-center gap-3 opacity-20 grayscale select-none pointer-events-none py-6 transition-opacity hover:opacity-50">
        <div class="h-px bg-slate-300 w-16"></div>
        <p class="text-[9px] font-black uppercase tracking-[0.6em] text-[#002855]">Ledger Infrastructure &bull; UNMARIS</p>
        <div class="h-px bg-slate-300 w-16"></div>
    </div>

    {{-- SweetAlert2 Listener --}}
    @script
    <script>
        $wire.on('swal:success', data => { 
            console.log('Success:', data[0].text); 
        });
        $wire.on('swal:error', data => { 
            console.error('Error:', data[0].text); 
        });
    </script>
    @endscript

    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    </style>
</div>