<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-black text-[#002855]">Konfigurasi Kuisioner EDOM</h2>
            <p class="text-sm text-slate-500">Atur instrumen evaluasi dosen oleh mahasiswa.</p>
        </div>
        <div class="flex gap-2">
            <button wire:click="$toggle('showFormGroup')" class="bg-indigo-50 text-indigo-600 px-5 py-2.5 rounded-xl font-bold text-xs uppercase tracking-widest border border-indigo-100">+ Kelompok</button>
            <button wire:click="$toggle('showFormQuestion')" class="bg-[#002855] text-white px-5 py-2.5 rounded-xl font-bold text-xs uppercase tracking-widest shadow-lg">+ Pertanyaan</button>
        </div>
    </div>

    {{-- Form Group --}}
    @if($showFormGroup)
    <div class="bg-indigo-50 p-6 rounded-2xl border border-indigo-100 animate-in slide-in-from-top-4">
        <form wire:submit.prevent="saveGroup" class="flex gap-4">
            <input type="text" wire:model="nama_kelompok" placeholder="Nama Kelompok (cth: Kompetensi Pedagogik)" class="flex-1 rounded-xl border-slate-200 text-sm py-2 px-4 focus:ring-[#002855]">
            <input type="number" wire:model="urutan_group" class="w-20 rounded-xl border-slate-200 text-sm text-center">
            <button type="submit" class="bg-indigo-600 text-white px-6 rounded-xl font-bold text-xs uppercase">Simpan</button>
        </form>
    </div>
    @endif

    {{-- Form Question --}}
    @if($showFormQuestion)
    <div class="bg-slate-50 p-8 rounded-3xl border border-slate-200 animate-in slide-in-from-top-4">
        <form wire:submit.prevent="saveQuestion" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <select wire:model="target_group_id" class="rounded-xl border-slate-200 text-sm py-2 font-bold">
                    <option value="">-- Pilih Kelompok --</option>
                    @foreach($groups as $g) <option value="{{ $g->id }}">{{ $g->nama_kelompok }}</option> @endforeach
                </select>
                <input type="number" wire:model="urutan_q" placeholder="Urutan" class="rounded-xl border-slate-200 text-sm text-center">
            </div>
            <textarea wire:model="bunyi_pertanyaan" placeholder="Bunyi pertanyaan kuisioner..." class="w-full rounded-xl border-slate-200 text-sm p-4" rows="3"></textarea>
            <div class="flex justify-end gap-3">
                <button type="button" wire:click="$set('showFormQuestion', false)" class="text-xs font-bold text-slate-400">Batal</button>
                <button type="submit" class="bg-[#002855] text-white px-8 py-2 rounded-xl font-bold text-xs uppercase">Simpan Pertanyaan</button>
            </div>
        </form>
    </div>
    @endif

    <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="min-w-full divide-y divide-slate-100">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-6 py-4 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Kelompok / Butir Pertanyaan</th>
                    <th class="px-6 py-4 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest">Urutan</th>
                    <th class="px-6 py-4 text-right text-[10px] font-black text-slate-400 uppercase tracking-widest">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50 bg-white">
                @foreach($questions as $q)
                <tr class="hover:bg-slate-50/50">
                    <td class="px-6 py-4">
                        <span class="text-[9px] font-black text-indigo-500 uppercase tracking-tighter block mb-1">{{ $q->nama_kelompok }}</span>
                        <p class="text-sm font-medium text-slate-700 leading-relaxed">{{ $q->bunyi_pertanyaan }}</p>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="text-xs font-mono font-bold text-slate-400">{{ $q->urutan }}</span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <button wire:click="deleteQuestion({{ $q->id }})" wire:confirm="Hapus pertanyaan ini?" class="text-rose-500 font-black text-[10px] uppercase">Hapus</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>