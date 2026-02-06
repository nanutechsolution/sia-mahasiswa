<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-black text-[#002855]">Repositori Dokumen Mutu</h2>
            <p class="text-sm text-slate-500">Pusat arsip digital SPMI universitas.</p>
        </div>
        <button wire:click="create" class="bg-[#002855] text-white px-5 py-2.5 rounded-xl font-bold text-sm">+ Unggah Dokumen</button>
    </div>

    @if($showForm)
    <div class="bg-white p-8 rounded-3xl shadow-xl border border-indigo-100 animate-in zoom-in-95">
        <form wire:submit.prevent="save" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div><label class="block text-xs font-bold text-slate-400 uppercase mb-1 tracking-widest">Nama Dokumen</label><input type="text" wire:model="nama_dokumen" class="w-full rounded-xl border-slate-200 bg-slate-50 py-2.5 pl-4 focus:ring-2 focus:ring-[#fcc000] font-bold"></div>
                <div><label class="block text-xs font-bold text-slate-400 uppercase mb-1 tracking-widest">Jenis Dokumen</label><select wire:model="jenis" class="w-full rounded-xl border-slate-200 bg-slate-50 py-2.5 px-4 focus:ring-2 focus:ring-[#fcc000] font-bold"><option value="KEBIJAKAN">Kebijakan</option><option value="MANUAL">Manual</option><option value="STANDAR">Standar</option><option value="FORMULIR">Formulir</option></select></div>
                <div><label class="block text-xs font-bold text-slate-400 uppercase mb-1 tracking-widest">Tanggal Berlaku</label><input type="date" wire:model="tgl_berlaku" class="w-full rounded-xl border-slate-200 bg-slate-50 py-2.5 px-4 focus:ring-2 focus:ring-[#fcc000]"></div>
                <div><label class="block text-xs font-bold text-slate-400 uppercase mb-1 tracking-widest">Berkas (PDF/Word)</label><input type="file" wire:model="file" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:bg-indigo-50 file:text-indigo-600 font-bold"></div>
            </div>
            <div class="flex justify-end gap-3 pt-4"><button type="button" wire:click="batal" class="text-xs font-black text-slate-400 uppercase tracking-widest">Batal</button><button type="submit" class="bg-emerald-600 text-white px-8 py-3 rounded-xl font-black text-xs uppercase tracking-widest shadow-lg">Mulai Unggah</button></div>
        </form>
    </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($docs as $doc)
        <div class="bg-white rounded-[2rem] p-6 shadow-sm border border-slate-200 group hover:border-[#002855] transition-all">
            <div class="flex justify-between items-start mb-4">
                <div class="p-3 bg-indigo-50 text-indigo-600 rounded-2xl group-hover:bg-[#002855] group-hover:text-white transition-colors"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></div>
                <span class="text-[9px] font-black px-2 py-0.5 rounded-full bg-slate-100 text-slate-500 uppercase">{{ $doc->jenis }}</span>
            </div>
            <h4 class="text-sm font-black text-[#002855] mb-1 line-clamp-2 leading-snug">{{ $doc->nama_dokumen }}</h4>
            <p class="text-[10px] text-slate-400 font-bold">Berlaku: {{ date('d M Y', strtotime($doc->tgl_berlaku)) }}</p>
            <div class="mt-6 pt-4 border-t border-slate-50 flex justify-between items-center"><span class="text-[9px] font-black text-slate-300">V.{{ $doc->versi }}</span><a href="{{ Storage::url($doc->file_path) }}" target="_blank" class="text-indigo-600 text-[10px] font-black uppercase hover:underline tracking-widest">Download</a></div>
        </div>
        @endforeach
    </div>
</div>