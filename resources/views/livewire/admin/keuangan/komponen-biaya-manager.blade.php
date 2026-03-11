<div class="max-w-[1600px] mx-auto p-4 md:p-8 space-y-8 animate-in fade-in duration-500">

    {{-- 1. HEADER & SUMMARY CARDS --}}
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
        <div class="space-y-2">
            <h1 class="text-3xl font-black text-[#002855] tracking-tight flex items-center gap-3">
                <div class="w-12 h-12 bg-[#002855] rounded-2xl flex items-center justify-center text-[#fcc000] shadow-xl shadow-blue-900/20">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </div>
                Katalog Komponen Biaya
            </h1>
            <p class="text-slate-400 font-bold text-sm ml-1 uppercase tracking-widest italic">Standardisasi Jenis Tagihan Berdasarkan SK Rektor</p>
        </div>

        @if(!$showForm)
        <button wire:click="create" class="px-8 py-4 bg-[#fcc000] text-[#002855] rounded-2xl font-black text-xs uppercase tracking-[0.2em] shadow-xl shadow-amber-500/20 hover:bg-[#ffca28] hover:-translate-y-1 transition-all flex items-center gap-3">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Komponen
        </button>
        @endif
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-[2rem] border border-slate-200 shadow-sm flex items-center gap-5">
            <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center text-xl shadow-inner">📋</div>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Komponen</p>
                <h3 class="text-2xl font-black text-[#002855] italic">{{ $stats['total'] }} <span class="text-xs not-italic text-slate-300">Items</span></h3>
            </div>
        </div>
        <div class="bg-white p-6 rounded-[2rem] border border-slate-200 shadow-sm flex items-center gap-5">
            <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center text-xl shadow-inner">💰</div>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Biaya Tetap</p>
                <h3 class="text-2xl font-black text-emerald-600 italic">{{ $stats['tetap'] }} <span class="text-xs not-italic text-slate-300">Items</span></h3>
            </div>
        </div>
        <div class="bg-white p-6 rounded-[2rem] border border-slate-200 shadow-sm flex items-center gap-5">
            <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center text-xl shadow-inner">📏</div>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Biaya Per SKS</p>
                <h3 class="text-2xl font-black text-amber-600 italic">{{ $stats['sks'] }} <span class="text-xs not-italic text-slate-300">Items</span></h3>
            </div>
        </div>
    </div>

    {{-- 2. FORM SECTION (MODAL STYLE) --}}
    @if($showForm)
    <div class="bg-white rounded-[3rem] shadow-2xl border border-slate-100 overflow-hidden animate-in zoom-in-95 duration-500 max-w-4xl mx-auto">
        <div class="px-10 py-8 bg-slate-50 border-b border-slate-100 flex justify-between items-center">
            <div>
                <h3 class="text-xl font-black text-[#002855] uppercase tracking-tight italic">
                    {{ $editMode ? 'Update Komponen' : 'Setup Komponen Baru' }}
                </h3>
                <p class="text-[10px] font-bold text-slate-400 mt-1 uppercase tracking-widest">General Ledger Configuration</p>
            </div>
            <button wire:click="$set('showForm', false)" class="text-slate-400 hover:text-rose-500 transition-colors bg-white p-2 rounded-full shadow-sm">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="p-10 space-y-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Nama Komponen (Sesuai SK) *</label>
                    <input type="text" wire:model="nama_komponen" placeholder="Misal: SPP Tetap"
                        class="w-full rounded-2xl border-slate-200 bg-slate-50 py-4 px-6 text-sm font-bold text-[#002855] focus:ring-2 focus:ring-[#fcc000] focus:border-[#fcc000] transition-all uppercase placeholder-slate-300">
                    @error('nama_komponen') <span class="text-rose-500 text-[10px] font-bold mt-1 block ml-1">{{ $message }}</span> @enderror
                </div>
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Klasifikasi Tipe Tagihan *</label>
                    <select wire:model="tipe_biaya" class="w-full rounded-2xl border-slate-200 bg-slate-50 py-4 px-6 text-sm font-bold text-[#002855] focus:ring-2 focus:ring-[#fcc000] focus:border-[#fcc000] transition-all cursor-pointer">
                        <option value="TETAP">TETAP (Tagihan per Semester)</option>
                        <option value="SKS">PER SKS (Tergantung Jumlah SKS)</option>
                        <option value="SEKALI">SEKALI (Tagihan Mahasiswa Baru)</option>
                        <option value="INSIDENTAL">INSIDENTAL (Denda/Administrasi)</option>
                    </select>
                    @error('tipe_biaya') <span class="text-rose-500 text-[10px] font-bold mt-1 block ml-1">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="bg-blue-50 p-6 rounded-[2rem] border border-blue-100 flex items-start gap-4">
                <div class="text-blue-500 mt-1">💡</div>
                <p class="text-[11px] font-medium text-blue-700 leading-relaxed italic">
                    <strong>Catatan Akuntansi:</strong> Nama komponen yang Anda masukkan akan tercetak langsung pada <strong>Kwitansi/Invoice</strong> mahasiswa. Pastikan penulisan sudah baku dan sesuai dengan nomenklatur bagian keuangan pusat.
                </p>
            </div>
        </div>

        <div class="px-10 py-8 bg-slate-50 border-t border-slate-100 flex justify-end gap-3">
            <button wire:click="$set('showForm', false)" class="px-8 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest hover:bg-slate-200 rounded-2xl transition-all">Batal</button>
            <button wire:click="save" class="px-12 py-4 bg-[#002855] text-white rounded-2xl font-black text-[10px] uppercase tracking-[0.2em] shadow-xl shadow-blue-900/20 hover:-translate-y-1 active:scale-95 transition-all flex items-center gap-3">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                </svg>
                Sah & Simpan Komponen
            </button>
        </div>
    </div>
    @endif

    {{-- 3. TABLE SECTION --}}
    <div class="bg-white rounded-[3rem] shadow-sm border border-slate-200 overflow-hidden relative">
        {{-- Search Bar Inside Table Header --}}
        <div class="px-10 py-8 border-b border-slate-50 flex flex-col md:flex-row justify-between items-center gap-6 bg-slate-50/30">
            <h3 class="text-sm font-black text-[#002855] uppercase tracking-widest flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-[#fcc000] animate-pulse"></span>
                Daftar Komponen Aktif
            </h3>
            <div class="relative w-full md:w-80">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari Nama Komponen..."
                    class="w-full rounded-2xl border-slate-200 bg-white py-3 pl-12 pr-4 text-xs font-bold focus:ring-2 focus:ring-[#fcc000] transition-all shadow-sm">
                <svg class="w-4 h-4 absolute left-4 top-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
        </div>

        <div class="overflow-x-auto custom-scrollbar">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-white">
                    <tr class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                        <th class="px-10 py-6 text-left w-20 italic">ID</th>
                        <th class="px-8 py-6 text-left">Nomenklatur Komponen</th>
                        <th class="px-8 py-6 text-center">Klasifikasi</th>
                        <th class="px-10 py-6 text-right">Manajemen</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 bg-white">
                    @forelse($komponen as $k)
                    <tr class="hover:bg-slate-50/80 transition-all group">
                        <td class="px-10 py-6 text-xs font-mono font-bold text-slate-300">#{{ $k->id }}</td>
                        <td class="px-8 py-6">
                            <div class="text-sm font-black text-[#002855] uppercase tracking-tight group-hover:text-indigo-600 transition-colors">{{ $k->nama_komponen }}</div>
                            <p class="text-[9px] font-bold text-slate-400 mt-1 uppercase tracking-widest italic">Registered at {{ $k->created_at->format('d M Y') }}</p>
                        </td>
                        <td class="px-8 py-6 text-center">
                            @php
                            $badgeStyle = match($k->tipe_biaya) {
                            'TETAP' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                            'SKS' => 'bg-amber-50 text-amber-600 border-amber-100',
                            'SEKALI' => 'bg-indigo-50 text-indigo-600 border-indigo-100',
                            default => 'bg-slate-50 text-slate-500 border-slate-200'
                            };
                            @endphp
                            <span class="inline-flex px-3 py-1 rounded-xl text-[9px] font-black uppercase tracking-widest border {{ $badgeStyle }}">
                                {{ $k->tipe_biaya }}
                            </span>
                        </td>
                        <td class="px-10 py-6 text-right">
                            <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-all duration-300">
                                <button wire:click="edit('{{ $k->id }}')" class="p-3 bg-indigo-50 text-indigo-600 rounded-2xl hover:bg-[#002855] hover:text-white transition-all shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <button wire:click="delete('{{ $k->id }}')" wire:confirm="PERINGATAN: Menghapus komponen dapat merusak integrasi data tagihan lama. Lanjutkan?" class="p-3 bg-rose-50 text-rose-500 rounded-2xl hover:bg-rose-500 hover:text-white transition-all shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
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

        <div class="px-10 py-6 bg-slate-50/50 border-t border-slate-100 flex items-center justify-between">
            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                Halaman {{ $komponen->currentPage() }} dari {{ $komponen->lastPage() }}
            </div>
            <div>{{ $komponen->links() }}</div>
        </div>
    </div>

    {{-- System Footer --}}
    <div class="pt-10 flex flex-col items-center gap-2 opacity-20 grayscale pointer-events-none border-t border-slate-100">
        <p class="text-[9px] font-black uppercase tracking-[0.6em] text-[#002855]">LEDGER INFRASTRUCTURE &bull; v4.2 PRO</p>
    </div>

    {{-- SweetAlert2 Listener --}}
    @script
    <script>
        $wire.on('swal:success', data => {
            alert(data[0].title + "\n" + data[0].text);
        });
        $wire.on('swal:error', data => {
            alert(data[0].title + "\n" + data[0].text);
        });
    </script>
    @endscript

    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
    </style>
</div>