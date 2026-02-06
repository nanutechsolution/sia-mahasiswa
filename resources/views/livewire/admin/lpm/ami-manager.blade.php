<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-black text-[#002855]">Audit Mutu Internal (AMI)</h2>
            <p class="text-sm text-slate-500">Evaluasi, Pengendalian, dan Peningkatan standar mutu prodi.</p>
        </div>
    </div>

    <div class="flex gap-2 p-1 bg-white rounded-2xl shadow-sm border border-slate-200 w-fit">
        <button wire:click="switchTab('periode')" class="px-6 py-2 rounded-xl text-xs font-black uppercase tracking-widest transition-all {{ $activeTab == 'periode' ? 'bg-[#002855] text-white shadow-lg' : 'text-slate-500 hover:bg-slate-50' }}">Sesi Audit</button>
        <button wire:click="switchTab('temuan')" class="px-6 py-2 rounded-xl text-xs font-black uppercase tracking-widest transition-all {{ $activeTab == 'temuan' ? 'bg-[#002855] text-white shadow-lg' : 'text-slate-500 hover:bg-slate-50' }}">Temuan Audit (Findings)</button>
    </div>

    @if($activeTab == 'periode')
        <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden animate-in fade-in">
            <div class="p-4 bg-slate-50 border-b flex justify-between items-center">
                <h3 class="text-xs font-black uppercase text-slate-400">Daftar Sesi AMI</h3>
                <button wire:click="$toggle('showForm')" class="bg-indigo-600 text-white px-4 py-2 rounded-xl text-xs font-bold uppercase tracking-widest">+ Sesi Baru</button>
            </div>
            @if($showForm)
                <div class="p-6 border-b bg-indigo-50/30">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <input type="text" wire:model="nama_periode" placeholder="Nama Sesi (cth: AMI 2025/2026)" class="rounded-xl border-slate-200 text-sm py-2 px-4 focus:ring-indigo-600">
                        <input type="date" wire:model="tgl_mulai" class="rounded-xl border-slate-200 text-sm py-2 px-4 focus:ring-indigo-600">
                        <input type="date" wire:model="tgl_selesai" class="rounded-xl border-slate-200 text-sm py-2 px-4 focus:ring-indigo-600">
                    </div>
                    <div class="mt-4 flex justify-end gap-2"><button wire:click="$set('showForm', false)" class="text-xs font-bold text-slate-500">Batal</button><button wire:click="savePeriode" class="bg-[#002855] text-white px-6 py-2 rounded-xl text-xs font-bold uppercase tracking-widest">Simpan Sesi</button></div>
                </div>
            @endif
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50 text-slate-400"><tr class="text-[10px] font-black uppercase tracking-widest"><th class="px-6 py-4 text-left">Nama Sesi</th><th class="px-6 py-4 text-left">Masa Audit</th><th class="px-6 py-4 text-center">Status</th><th class="px-6 py-4 text-right">Opsi</th></tr></thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($periodes as $p)
                    <tr class="hover:bg-slate-50"><td class="px-6 py-4 text-sm font-bold text-slate-800">{{ $p->nama_periode }}</td><td class="px-6 py-4 text-xs font-mono text-slate-500">{{ $p->tgl_mulai }} s/d {{ $p->tgl_selesai }}</td><td class="px-6 py-4 text-center"><span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase bg-emerald-100 text-emerald-700">{{ $p->status }}</span></td><td class="px-6 py-4 text-right"><button class="text-indigo-600 font-bold text-[10px] uppercase">Review</button></td></tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden animate-in fade-in">
            <div class="p-4 bg-slate-50 border-b flex justify-between items-center"><h3 class="text-xs font-black uppercase text-slate-400">Penatatan Temuan (PTK)</h3><button wire:click="$toggle('showForm')" class="bg-rose-600 text-white px-4 py-2 rounded-xl text-xs font-bold uppercase tracking-widest">+ Catat Temuan</button></div>
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50 text-slate-400"><tr class="text-[10px] font-black uppercase tracking-widest"><th class="px-6 py-4 text-left">Unit / Prodi</th><th class="px-6 py-4 text-left">Standar</th><th class="px-6 py-4 text-left">Klasifikasi</th><th class="px-6 py-4 text-left">Deskripsi</th><th class="px-6 py-4 text-right">Status</th></tr></thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($findings as $f)
                    <tr class="hover:bg-slate-50"><td class="px-6 py-4"><div class="text-sm font-bold text-slate-800">{{ $f->nama_prodi }}</div></td><td class="px-6 py-4"><span class="text-xs font-mono font-bold text-indigo-600">{{ $f->kode_standar }}</span></td><td class="px-6 py-4 text-xs font-bold">{{ $f->klasifikasi }}</td><td class="px-6 py-4 text-xs text-slate-600 max-w-xs truncate">{{ $f->deskripsi_temuan }}</td><td class="px-6 py-4 text-right"><span class="px-2 py-0.5 rounded bg-rose-100 text-rose-700 text-[9px] font-black uppercase">OPEN</span></td></tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>