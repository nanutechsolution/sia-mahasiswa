<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-[#002855]">Aturan Beban SKS</h1>
            <p class="text-slate-500 text-sm mt-1">Konfigurasi batas maksimal SKS berdasarkan IPS mahasiswa.</p>
        </div>
        @if(!$showForm)
        <button wire:click="create" class="inline-flex items-center px-5 py-2.5 bg-[#fcc000] text-[#002855] rounded-xl font-black text-sm shadow-lg shadow-orange-500/20 hover:bg-[#fbbf24] hover:scale-105 transition-all">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" /></svg>
            Tambah Aturan
        </button>
        @endif
    </div>

    @if (session()->has('success'))
        <div class="bg-emerald-50 border border-emerald-100 p-4 rounded-xl text-emerald-800 text-sm font-bold animate-in fade-in flex items-center">
            <svg class="w-5 h-5 mr-3 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Form --}}
    @if($showForm)
    <div class="bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden animate-in zoom-in-95 duration-200">
        <div class="px-8 py-5 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
            <h3 class="text-sm font-bold text-[#002855] uppercase tracking-wider">
                {{ $editMode ? 'Edit Aturan' : 'Buat Aturan Baru' }}
            </h3>
            <button wire:click="batal" class="text-slate-400 hover:text-slate-600 transition-colors">&times;</button>
        </div>

        <div class="p-8 grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">IPS Minimal</label>
                <input type="number" step="0.01" wire:model="min_ips" class="block w-full rounded-xl border-slate-200 bg-slate-50 text-slate-900 py-2.5 pl-4 font-bold focus:ring-[#fcc000] focus:border-[#fcc000]" placeholder="0.00">
                @error('min_ips') <span class="text-rose-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">IPS Maksimal</label>
                <input type="number" step="0.01" wire:model="max_ips" class="block w-full rounded-xl border-slate-200 bg-slate-50 text-slate-900 py-2.5 pl-4 font-bold focus:ring-[#fcc000] focus:border-[#fcc000]" placeholder="4.00">
                @error('max_ips') <span class="text-rose-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Jatah SKS</label>
                <input type="number" wire:model="max_sks" class="block w-full rounded-xl border-slate-200 bg-slate-50 text-slate-900 py-2.5 pl-4 font-bold focus:ring-[#fcc000] focus:border-[#fcc000]" placeholder="24">
                @error('max_sks') <span class="text-rose-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="px-8 pb-8 pt-0 flex justify-end gap-3">
            <button wire:click="batal" class="px-6 py-2.5 text-sm font-bold text-slate-500 hover:bg-slate-50 rounded-xl transition-all">Batal</button>
            <button wire:click="save" class="px-8 py-2.5 bg-[#002855] text-white rounded-xl text-sm font-bold shadow-lg hover:bg-[#001a38] transition-all">Simpan</button>
        </div>
    </div>
    @endif

    {{-- Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead class="bg-[#002855] text-white">
                <tr>
                    <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-center">Range IPS</th>
                    <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-center">Batas SKS</th>
                    <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($aturan as $item)
                <tr class="hover:bg-slate-50/80 transition-colors">
                    <td class="px-6 py-4 text-center font-bold text-slate-700">
                        {{ number_format($item->min_ips, 2) }} - {{ number_format($item->max_ips, 2) }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-black bg-indigo-50 text-indigo-700">
                            {{ $item->max_sks }} SKS
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right space-x-2">
                        <button wire:click="edit({{ $item->id }})" class="text-[#002855] font-bold text-[10px] uppercase hover:underline">Edit</button>
                        <button wire:click="delete({{ $item->id }})" wire:confirm="Hapus aturan ini?" class="text-rose-500 font-bold text-[10px] uppercase hover:underline">Hapus</button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="px-6 py-12 text-center text-slate-400 italic">Belum ada aturan SKS dibuat.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>