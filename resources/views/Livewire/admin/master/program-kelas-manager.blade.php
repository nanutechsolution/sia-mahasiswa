<div class="space-y-6">
    {{-- Header & Toolbar --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-[#002855]">Master Program Kelas</h1>
            <p class="text-slate-500 text-sm mt-1">Kelola jenis kelas (Reguler/Ekstensi) dan aturan ambang batas pembayaran KRS.</p>
        </div>
        @if(!$showForm)
        <div class="flex gap-2">
            <button type="button" class="inline-flex items-center px-4 py-2.5 bg-white border border-slate-300 text-slate-600 rounded-xl font-bold text-sm shadow-sm hover:border-blue-400 hover:text-blue-700 transition-all">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Sync Feeder
            </button>
            <button wire:click="create" class="inline-flex items-center px-5 py-2.5 bg-[#fcc000] text-[#002855] rounded-xl font-black text-sm shadow-lg shadow-orange-500/20 hover:bg-[#fbbf24] hover:scale-105 transition-all">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Program
            </button>
        </div>
        @endif
    </div>

    {{-- Alert Messages --}}
    @if (session()->has('success'))
    <div class="p-4 bg-emerald-50 border border-emerald-100 text-emerald-800 rounded-xl text-sm font-bold flex items-center shadow-sm animate-in fade-in">
        <svg class="w-5 h-5 mr-3 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
        </svg>
        {{ session('success') }}
    </div>
    @endif
    @if (session()->has('error'))
    <div class="p-4 bg-rose-50 border border-rose-100 text-rose-800 rounded-xl text-sm font-bold shadow-sm animate-in fade-in">
        {{ session('error') }}
    </div>
    @endif

    {{-- Form Section --}}
    @if($showForm)
    <div class="bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden animate-in zoom-in-95 duration-200">
        <div class="px-8 py-5 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
            <h3 class="text-sm font-bold text-[#002855] uppercase tracking-wider flex items-center gap-2">
                @if($editMode) Edit Program Kelas @else Tambah Program Baru @endif
            </h3>
            <button wire:click="batal" class="text-slate-400 hover:text-slate-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="p-8 space-y-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Kode Internal</label>
                    <input type="text" wire:model="kode_internal" class="block w-full rounded-xl border-slate-200 bg-slate-50 text-slate-900 py-2.5 pl-4 text-sm font-bold uppercase focus:ring-[#002855] focus:border-[#002855]" placeholder="REG">
                    @error('kode_internal') <span class="text-rose-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="lg:col-span-2">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Nama Program</label>
                    <input type="text" wire:model="nama_program" class="block w-full rounded-xl border-slate-200 bg-slate-50 text-slate-900 py-2.5 pl-4 text-sm font-bold focus:ring-[#002855] focus:border-[#002855] placeholder-slate-400" placeholder="Reguler Pagi">
                    @error('nama_program') <span class="text-rose-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Syarat Bayar KRS</label>
                    <div class="relative">
                        <input type="number" wire:model="min_pembayaran_persen" class="block w-full rounded-xl border-slate-200 bg-slate-50 text-slate-900 py-2.5 pl-4 pr-10 text-sm font-bold text-center focus:ring-[#002855] focus:border-[#002855]" placeholder="50">
                        <div class="absolute inset-y-0 right-4 flex items-center pointer-events-none text-slate-400 font-bold">%</div>
                    </div>
                    <p class="text-[9px] text-slate-400 mt-1">*Persentase tagihan lunas agar KRS terbuka.</p>
                    @error('min_pembayaran_persen') <span class="text-rose-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="flex items-center pt-6 lg:col-span-4">
                    <label class="flex items-center cursor-pointer gap-3 p-4 bg-slate-50 rounded-xl border border-slate-100 w-full hover:border-slate-200 transition-colors">
                        <input type="checkbox" wire:model="is_active" class="w-5 h-5 text-[#002855] rounded border-slate-300 focus:ring-[#002855]">
                        <div>
                            <span class="block text-sm font-bold text-slate-800">Status Aktif</span>
                            <span class="block text-[10px] text-slate-500 mt-0.5">Program ini dapat dipilih oleh mahasiswa baru.</span>
                        </div>
                    </label>
                </div>

                {{-- Neo Feeder Integration Panel --}}
                <div class="lg:col-span-4 bg-blue-50/50 p-5 rounded-2xl border border-blue-100 mt-2">
                    <label class="block text-[10px] font-black text-blue-800 uppercase tracking-widest mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                        </svg>
                        Integrasi Neo Feeder
                    </label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">ID Feeder (UUID)</label>
                            <input type="text" wire:model="id_feeder" class="block w-full rounded-lg border-slate-300 bg-white text-slate-600 py-2 pl-3 text-xs font-mono focus:ring-blue-500 focus:border-blue-500 placeholder-slate-300" placeholder="xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx">
                        </div>
                        <div class="flex items-end pb-2">
                            <p class="text-[10px] text-slate-500 leading-tight">
                                *ID ini digunakan untuk sinkronisasi data dengan Pangkalan Data Pendidikan Tinggi (PDDikti). Biarkan kosong jika belum terdaftar di Feeder.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-6 border-t border-slate-100 flex justify-end gap-3">
                <button wire:click="batal" class="px-6 py-2.5 text-sm font-bold text-slate-500 hover:text-slate-700 hover:bg-slate-100 rounded-xl transition-all">Batal</button>
                <button wire:click="save" class="px-8 py-2.5 bg-[#002855] text-white rounded-xl text-sm font-bold shadow-lg shadow-indigo-900/20 hover:bg-[#001a38] hover:scale-105 transition-all">Simpan Data</button>
            </div>
        </div>
    </div>
    @endif

    {{-- Data Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-4 bg-slate-50/50 border-b flex items-center gap-4 rounded-t-2xl">
            <div class="relative flex-1">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari Program..." class="w-full pl-10 pr-4 py-2 rounded-xl border-slate-200 text-sm focus:ring-[#002855] focus:border-[#002855] transition-shadow outline-none font-bold text-slate-700 placeholder-slate-400">
                <svg class="w-4 h-4 absolute left-3 top-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#002855] border-b border-[#001a38] text-white">
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest w-32">Kode</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest">Nama Program</th>
                        <th class="px-6 py-4 text-center text-[10px] font-bold uppercase tracking-widest">Syarat Bayar</th>
                        <th class="px-6 py-4 text-center text-[10px] font-bold uppercase tracking-widest">Status</th>
                        <th class="px-6 py-4 text-right text-[10px] font-bold uppercase tracking-widest">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($programs as $p)
                    <tr class="hover:bg-slate-50/80 transition-colors group">
                        <td class="px-6 py-4 align-middle">
                            <span class="font-mono text-xs font-black text-[#002855] bg-indigo-50 px-2 py-0.5 rounded border border-indigo-100">{{ $p->kode_internal }}</span>
                        </td>
                        <td class="px-6 py-4 align-middle">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-bold text-slate-800">{{ $p->nama_program }}</span>
                                @if($p->id_feeder)
                                <span class="inline-flex items-center justify-center w-5 h-5 bg-blue-100 text-blue-600 rounded-full" title="Terhubung ke Feeder">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 align-middle text-center">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-black bg-amber-50 text-amber-700 border border-amber-100">
                                {{ $p->min_pembayaran_persen }}%
                            </span>
                        </td>
                        <td class="px-6 py-4 align-middle text-center">
                            @if($p->is_active)
                            <span class="inline-flex h-2 w-2 rounded-full bg-emerald-500" title="Aktif"></span>
                            @else
                            <span class="inline-flex h-2 w-2 rounded-full bg-rose-500" title="Non-Aktif"></span>
                            @endif
                        </td>
                        <td class="px-6 py-4 align-middle text-right space-x-2">
                            <button wire:click="edit({{ $p->id }})" class="text-[#002855] hover:text-indigo-600 font-bold text-[10px] uppercase transition-colors">Edit</button>
                            <button wire:click="delete({{ $p->id }})" wire:confirm="Hapus Program ini?" class="text-rose-500 hover:text-rose-700 font-bold text-[10px] uppercase transition-colors">Hapus</button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center text-slate-400 italic">Data tidak ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">{{ $programs->links() }}</div>
    </div>
</div>