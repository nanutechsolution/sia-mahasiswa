<div class="space-y-6">
    <div class="flex justify-between items-center bg-white p-6 rounded-3xl shadow-sm border border-slate-200">
        <div>
            <h2 class="text-2xl font-black text-[#002855]">Indikator Kinerja Utama (IKU)</h2>
            <p class="text-sm text-slate-500">Pemantauan target mutu tahunan universitas.</p>
        </div>
        <div class="flex items-center gap-4">
            <select wire:model.live="activeYear" class="rounded-xl border-slate-200 text-sm font-bold">
                @foreach(range(date('Y')-2, date('Y')+2) as $y)
                    <option value="{{ $y }}">Tahun {{ $y }}</option>
                @endforeach
            </select>
            <button wire:click="$toggle('showForm')" class="bg-[#002855] text-white px-5 py-2.5 rounded-xl font-bold text-sm shadow-lg">
                + Set Target
            </button>
        </div>
    </div>

    @if($showForm)
    <div class="bg-white p-8 rounded-3xl shadow-xl border border-indigo-100 animate-in zoom-in-95">
        <form wire:submit.prevent="saveTarget" class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Indikator</label>
                <select wire:model="indikator_id" class="w-full rounded-xl border-slate-200 bg-slate-50 py-2.5 font-bold">
                    <option value="">-- Pilih Indikator --</option>
                    @foreach($indicators as $i) <option value="{{ $i->id }}">{{ $i->nama_indikator }}</option> @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Target Nilai (%)</label>
                <input type="number" step="0.01" wire:model="target_nilai" class="w-full rounded-xl border-slate-200 bg-slate-50 py-2.5 font-bold text-center">
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 bg-emerald-600 text-white py-2.5 rounded-xl font-bold uppercase tracking-widest text-xs">Simpan</button>
                <button type="button" wire:click="$set('showForm', false)" class="px-4 py-2.5 text-slate-400 font-bold">Batal</button>
            </div>
        </form>
    </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($targets as $t)
        @php $persen = ($t->target_nilai > 0) ? ($t->capaian_nilai / $t->target_nilai * 100) : 0; @endphp
        <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-200 relative overflow-hidden group">
            <div class="flex justify-between items-start mb-4">
                <div class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center font-black text-xs">IKU</div>
                <span class="text-[10px] font-black px-2 py-0.5 rounded-full {{ $persen >= 100 ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                    {{ $persen >= 100 ? 'ACHIEVED' : 'ON PROGRESS' }}
                </span>
            </div>
            <h4 class="text-sm font-black text-[#002855] mb-4 leading-tight h-10 line-clamp-2">{{ $t->nama_indikator }}</h4>
            
            <div class="space-y-3">
                <div class="flex justify-between text-xs font-bold">
                    <span class="text-slate-400 uppercase">Progres</span>
                    <span class="text-[#002855]">{{ number_format($persen, 1) }}%</span>
                </div>
                <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                    <div class="h-full {{ $persen >= 100 ? 'bg-emerald-500' : 'bg-amber-400' }} transition-all duration-1000" style="width: {{ min($persen, 100) }}%"></div>
                </div>
                <div class="flex justify-between items-end pt-2">
                    <div>
                        <p class="text-[9px] font-black text-slate-300 uppercase">Target</p>
                        <p class="text-sm font-black text-slate-400">{{ $t->target_nilai }}%</p>
                    </div>
                    <div class="text-right">
                        <p class="text-[9px] font-black text-slate-300 uppercase">Aktual</p>
                        <p class="text-xl font-black text-[#002855]">{{ $t->capaian_nilai }}%</p>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>