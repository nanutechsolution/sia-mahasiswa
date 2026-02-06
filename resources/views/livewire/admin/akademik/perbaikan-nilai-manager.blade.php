<div class="space-y-6">
    {{-- Header --}}
    <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-200">
        <h1 class="text-2xl font-black text-[#002855] uppercase tracking-tight">Otoritas Perbaikan Nilai</h1>
        <p class="text-sm text-slate-500 font-medium mt-1">Gunakan modul ini untuk mengubah nilai yang sudah dipublikasikan di Transkrip/KHS.</p>

        {{-- Search Input --}}
        <div class="mt-6 relative max-w-xl">
            <input type="text" wire:model.live.debounce.500ms="search" placeholder="Cari Mahasiswa (NIM / Nama)..." class="w-full rounded-2xl border-slate-200 bg-slate-50 py-3 pl-12 pr-4 font-bold focus:ring-[#fcc000] outline-none">
            <svg class="w-5 h-5 text-slate-400 absolute left-4 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2"/></svg>
            
            {{-- Results Dropdown --}}
            @if(!empty($mahasiswas))
            <div class="absolute z-50 w-full mt-2 bg-white rounded-2xl shadow-2xl border border-slate-100 overflow-hidden">
                @foreach($mahasiswas as $m)
                <button wire:click="selectMhs('{{ $m->id }}')" class="w-full text-left px-6 py-4 hover:bg-indigo-50 border-b last:border-0 transition-colors">
                    <p class="text-sm font-black text-[#002855]">{{ $m->nama_lengkap }}</p>
                    <p class="text-[10px] text-slate-400 font-bold uppercase">{{ $m->nim }} &bull; {{ $m->prodi->nama_prodi }}</p>
                </button>
                @endforeach
            </div>
            @endif
        </div>
    </div>

    @if($mahasiswa)
    <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-200 overflow-hidden animate-in fade-in">
        <div class="px-8 py-5 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-xs font-black text-[#002855] uppercase tracking-widest">Riwayat Belajar Kumulatif: {{ $mahasiswa->nama_lengkap }}</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-white">
                    <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                        <th class="px-8 py-4 text-left">Mata Kuliah</th>
                        <th class="px-4 py-4 text-center">Nilai Angka</th>
                        <th class="px-4 py-4 text-center">Huruf</th>
                        <th class="px-8 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 bg-white">
                    @foreach($riwayatNilai as $rn)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-8 py-4">
                            <div class="text-sm font-bold text-slate-800 uppercase">{{ $rn->nama_mk }}</div>
                            <div class="text-[9px] font-mono font-bold text-slate-400">{{ $rn->kode_mk }}</div>
                        </td>
                        <td class="px-4 py-4 text-center font-black text-slate-600">{{ number_format($rn->nilai_angka, 2) }}</td>
                        <td class="px-4 py-4 text-center">
                            <span class="text-lg font-black {{ $rn->nilai_indeks >= 2 ? 'text-emerald-600' : 'text-rose-600' }}">{{ $rn->nilai_huruf }}</span>
                        </td>
                        <td class="px-8 py-4 text-right">
                            <button wire:click="openRevision('{{ $rn->id }}')" class="px-4 py-2 bg-rose-50 text-rose-600 rounded-xl text-[10px] font-black uppercase tracking-widest border border-rose-100 hover:bg-rose-600 hover:text-white transition-all">Perbaiki</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- MODAL FORM PERBAIKAN --}}
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 animate-in fade-in">
        <div class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-lg overflow-hidden border border-white/20">
            <div class="bg-rose-600 px-8 py-6 text-white">
                <h3 class="text-lg font-black uppercase tracking-widest leading-none">Formulir Perbaikan Nilai</h3>
                <p class="text-[10px] font-bold uppercase opacity-80 mt-2">MK: {{ $detailTarget->nama_mk_snapshot ?? 'Mata Kuliah' }}</p>
            </div>
            <div class="p-8 space-y-6">
                <div class="bg-rose-50 p-4 rounded-2xl border border-rose-100 flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-black text-rose-400 uppercase tracking-widest">Nilai Lama</p>
                        <p class="text-2xl font-black text-rose-600">{{ $detailTarget->nilai_huruf }} ({{ number_format($detailTarget->nilai_angka, 2) }})</p>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Nilai Baru (Angka)</p>
                        <input type="number" step="0.01" wire:model="nilai_baru_angka" class="w-24 text-right text-xl font-black text-[#002855] rounded-xl border-slate-200 focus:ring-[#fcc000] py-1 shadow-inner">
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-1">Dasar/Alasan Perbaikan *</label>
                    <textarea wire:model="alasan" class="w-full rounded-2xl border-slate-200 text-sm font-medium p-4 focus:ring-[#fcc000]" rows="3" placeholder="Contoh: Kesalahan input nilai tugas atau hasil ujian remedial..."></textarea>
                    @error('alasan') <span class="text-[10px] text-rose-500 font-bold mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="flex gap-3">
                    <button wire:click="$set('showModal', false)" class="flex-1 py-3 text-xs font-black text-slate-400 uppercase tracking-widest">Batal</button>
                    <button wire:click="processRevision" class="flex-1 py-3 bg-[#002855] text-white rounded-2xl font-black text-xs uppercase tracking-widest shadow-xl hover:bg-black transition-all">Simpan Perbaikan</button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>