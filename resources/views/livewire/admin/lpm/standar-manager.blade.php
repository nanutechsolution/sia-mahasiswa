<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-black text-[#002855]">Standar Mutu (SPMI)</h2>
            <p class="text-sm text-slate-500">Penetapan butir-butir standar universitas.</p>
        </div>
        @if(!$showForm)
        <button wire:click="create" class="bg-[#002855] text-white px-5 py-2.5 rounded-xl font-bold text-sm">+ Tambah Standar</button>
        @endif
    </div>

    @if($showForm)
    <div class="bg-white p-8 rounded-3xl shadow-xl border border-slate-200 animate-in zoom-in-95">
        <form wire:submit.prevent="save" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div><label class="block text-xs font-bold text-slate-400 uppercase mb-1">Kode Standar</label><input type="text" wire:model="kode_standar" class="w-full rounded-xl border-slate-200 bg-slate-50 py-2.5 pl-4 focus:ring-2 focus:ring-[#fcc000] font-bold"></div>
                <div><label class="block text-xs font-bold text-slate-400 uppercase mb-1">Nama Standar</label><input type="text" wire:model="nama_standar" class="w-full rounded-xl border-slate-200 bg-slate-50 py-2.5 pl-4 focus:ring-2 focus:ring-[#fcc000] font-bold"></div>
                <div class="md:col-span-2"><label class="block text-xs font-bold text-slate-400 uppercase mb-1">Pernyataan Standar</label><textarea wire:model="pernyataan_standar" rows="3" class="w-full rounded-xl border-slate-200 bg-slate-50 p-4 focus:ring-2 focus:ring-[#fcc000]"></textarea></div>
            </div>
            <div class="flex justify-end gap-3"><button type="button" wire:click="resetForm" class="px-6 text-slate-500 font-bold">Batal</button><button type="submit" class="bg-[#002855] text-white px-8 py-2.5 rounded-xl font-bold">Simpan Standar</button></div>
        </form>
    </div>
    @endif

    <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="min-w-full divide-y divide-slate-100">
            <thead class="bg-[#002855] text-white">
                <tr><th class="px-6 py-4 text-left text-[10px] font-bold uppercase tracking-widest">Kode</th><th class="px-6 py-4 text-left text-[10px] font-bold uppercase tracking-widest">Nama Standar</th><th class="px-6 py-4 text-center text-[10px] font-bold uppercase tracking-widest">Target</th><th class="px-6 py-4 text-right text-[10px] font-bold uppercase tracking-widest">Aksi</th></tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @foreach($standars as $s)
                <tr class="hover:bg-slate-50">
                    <td class="px-6 py-4 text-xs font-bold text-indigo-600">{{ $s->kode_standar }}</td>
                    <td class="px-6 py-4 text-sm font-bold text-slate-800">{{ $s->nama_standar }}</td>
                    <td class="px-6 py-4 text-center"><span class="px-2 py-1 bg-indigo-50 text-indigo-700 rounded text-xs font-black">{{ $s->target_pencapaian }}{{ $s->satuan }}</span></td>
                    <td class="px-6 py-4 text-right space-x-2"><button wire:click="edit({{ $s->id }})" class="text-indigo-600 font-bold text-[10px] uppercase">Edit</button><button wire:click="delete({{ $s->id }})" wire:confirm="Hapus standar ini?" class="text-rose-500 font-bold text-[10px] uppercase">Hapus</button></td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-4 bg-slate-50">{{ $standars->links() }}</div>
    </div>
</div>