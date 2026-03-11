<div class="max-w-[1600px] mx-auto p-4 md:p-8 space-y-8 animate-in fade-in duration-500">

    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="space-y-2">
            <h1 class="text-3xl font-black text-[#002855] tracking-tight flex items-center gap-3">
                <div class="w-12 h-12 bg-[#002855] rounded-2xl flex items-center justify-center text-[#fcc000] shadow-xl shadow-blue-900/20">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                    </svg>
                </div>
                Penerbitan Invoice Manual
            </h1>
            <p class="text-slate-400 font-bold text-sm ml-1 uppercase tracking-widest italic">Pembuatan Tagihan Denda, Susulan, & Ganti Rugi Perorangan</p>
        </div>
    </div>

    <div class="bg-white shadow-xl shadow-slate-200/50 rounded-[3rem] border border-slate-100 overflow-hidden">
        <div class="p-8 md:p-12 space-y-12">

            {{-- LANGKAH 1: PILIH MAHASISWA --}}
            <div class="space-y-5">
                <h4 class="text-xs font-black text-[#002855] uppercase border-l-[3px] border-[#fcc000] pl-4 tracking-[0.2em]">1. Cari Identitas Mahasiswa</h4>

                @if(!$selectedMhs)
                <div class="relative max-w-2xl">
                    <input type="text" wire:model.live.debounce.300ms="searchMhs"
                        class="block w-full rounded-2xl border-slate-200 bg-slate-50 py-4 pl-14 pr-4 text-sm focus:border-[#002855] focus:ring-2 focus:ring-indigo-100 transition-all font-bold placeholder-slate-400"
                        placeholder="Ketik NIM atau Nama Lengkap Mahasiswa...">
                    <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-slate-400">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>

                    {{-- Dropdown Hasil Pencarian --}}
                    @if(!empty($searchResults))
                    <div class="absolute z-20 mt-3 w-full bg-white shadow-2xl rounded-2xl border border-slate-100 overflow-hidden animate-in slide-in-from-top-2">
                        @foreach($searchResults as $mhs)
                        <div wire:click="selectMhs('{{ $mhs->id }}')" class="cursor-pointer px-6 py-4 hover:bg-indigo-50 border-b border-slate-50 last:border-0 transition-colors flex items-center gap-4">
                            <div class="w-10 h-10 bg-slate-100 text-[#002855] rounded-xl flex items-center justify-center font-black text-xs shadow-inner uppercase">{{ substr($mhs->person->nama_lengkap ?? $mhs->nama_lengkap, 0, 1) }}</div>
                            <div>
                                <span class="font-black text-[#002855] block uppercase tracking-tight">{{ $mhs->person->nama_lengkap ?? $mhs->nama_lengkap }}</span>
                                <span class="text-slate-500 text-[10px] font-bold uppercase tracking-widest mt-0.5 block">{{ $mhs->nim }} &bull; {{ $mhs->prodi->nama_prodi }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @elseif(strlen($searchMhs) >= 3)
                    <div class="absolute z-20 mt-3 w-full bg-white shadow-xl rounded-2xl py-6 px-6 text-sm text-slate-400 font-bold tracking-widest uppercase italic border border-slate-100 text-center animate-in slide-in-from-top-2">
                        Data mahasiswa tidak ditemukan.
                    </div>
                    @endif
                </div>
                @error('searchMhs') <span class="text-rose-500 text-[10px] font-bold mt-2 block uppercase tracking-widest">{{ $message }}</span> @enderror
                @else
                {{-- Kartu Mahasiswa Terpilih --}}
                <div class="bg-[#002855] shadow-xl shadow-blue-900/20 rounded-[2rem] p-6 md:p-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6 animate-in zoom-in-95 max-w-3xl relative overflow-hidden">
                    <div class="absolute right-0 top-0 p-6 opacity-10 pointer-events-none">
                        <svg class="w-32 h-32 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 14l9-5-9-5-9 5 9 5z" />
                            <path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                        </svg>
                    </div>
                    <div class="flex items-center gap-6 relative z-10">
                        <div class="h-16 w-16 bg-[#fcc000] rounded-2xl flex items-center justify-center text-[#002855] text-2xl font-black shadow-inner uppercase shrink-0">
                            {{ substr($selectedMhs->person->nama_lengkap ?? $selectedMhs->nama_lengkap, 0, 1) }}
                        </div>
                        <div class="space-y-1">
                            <p class="text-[10px] text-indigo-300 font-bold uppercase tracking-[0.2em]">Target Penagihan</p>
                            <div class="text-xl font-black text-white uppercase tracking-tight">{{ $selectedMhs->person->nama_lengkap ?? $selectedMhs->nama_lengkap }}</div>
                            <div class="flex items-center gap-3">
                                <span class="text-xs text-white font-mono bg-white/20 px-2 py-0.5 rounded">{{ $selectedMhs->nim }}</span>
                                <span class="text-[10px] font-bold text-indigo-200 uppercase tracking-widest">{{ $selectedMhs->prodi->nama_prodi }}</span>
                            </div>
                        </div>
                    </div>
                    <button wire:click="resetSelection" class="relative z-10 text-rose-300 hover:text-white text-[10px] font-black uppercase tracking-widest bg-rose-500/20 px-5 py-2.5 rounded-xl border border-rose-500/30 hover:bg-rose-500 transition-all shadow-sm">
                        Ganti Mahasiswa
                    </button>
                </div>
                @endif
            </div>

            {{-- LANGKAH 2: DETAIL TAGIHAN --}}
            <div class="{{ !$selectedMhs ? 'opacity-40 pointer-events-none grayscale' : '' }} space-y-6 transition-all duration-500 max-w-4xl">
                <h4 class="text-xs font-black text-[#002855] uppercase border-l-[3px] border-[#fcc000] pl-4 tracking-[0.2em]">2. Spesifikasi Tagihan</h4>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 bg-slate-50/50 p-8 rounded-[2.5rem] border border-slate-100">
                    <div class="space-y-2">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Periode Semester</label>
                        <select wire:model="semesterId" class="block w-full rounded-2xl border-slate-200 bg-white text-slate-800 font-bold focus:border-[#002855] focus:ring-2 focus:ring-indigo-100 py-4 px-5 text-sm transition-all">
                            @foreach($semesters as $sem)
                            <option value="{{ $sem->id }}">{{ $sem->nama_tahun }} {{ $sem->is_active ? '(Aktif)' : '' }}</option>
                            @endforeach
                        </select>
                        @error('semesterId') <span class="text-rose-500 text-[10px] font-bold mt-1 block ml-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Komponen Master</label>
                        <select wire:model.live="komponenId" class="block w-full rounded-2xl border-slate-200 bg-white text-slate-800 font-bold focus:border-[#002855] focus:ring-2 focus:ring-indigo-100 py-4 px-5 text-sm transition-all">
                            <option value="">-- Pilih Jenis Biaya --</option>
                            @foreach($komponens as $k)
                            <option value="{{ $k->id }}">{{ $k->nama_komponen }} ({{ $k->tipe_biaya }})</option>
                            @endforeach
                        </select>
                        @error('komponenId') <span class="text-rose-500 text-[10px] font-bold mt-1 block ml-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="md:col-span-2 space-y-2">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Keterangan / Alasan Penagihan</label>
                        <input type="text" wire:model="deskripsi" class="block w-full rounded-2xl border-slate-200 bg-white text-slate-800 font-bold focus:border-[#002855] focus:ring-2 focus:ring-indigo-100 py-4 px-5 text-sm transition-all" placeholder="Contoh: Denda Keterlambatan Pengembalian Alat Laboratorium Jaringan">
                        @error('deskripsi') <span class="text-rose-500 text-[10px] font-bold mt-1 block ml-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="md:col-span-2 space-y-2 mt-4">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-center">Nominal Wajib Dibayar</label>
                        <div class="relative max-w-md mx-auto">
                            <div class="absolute inset-y-0 left-0 pl-6 flex items-center pointer-events-none">
                                <span class="text-slate-400 font-black text-lg">Rp</span>
                            </div>
                            <input type="number" wire:model="nominal" class="block w-full rounded-[2rem] border-2 border-[#fcc000]/50 bg-white pl-16 pr-6 py-5 focus:border-[#fcc000] focus:ring-4 focus:ring-[#fcc000]/20 text-3xl font-black text-[#002855] placeholder-slate-200 text-center transition-all shadow-sm" placeholder="0">
                        </div>
                        @error('nominal') <span class="text-rose-500 text-[10px] font-bold mt-2 block text-center uppercase tracking-widest">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="flex justify-end pt-8">
                    <button wire:click="simpanTagihan"
                        wire:loading.attr="disabled"
                        class="px-12 py-5 bg-[#002855] text-white rounded-3xl text-[11px] font-black uppercase tracking-[0.2em] shadow-2xl shadow-blue-900/20 hover:bg-[#001a38] hover:-translate-y-1 transition-all flex items-center disabled:opacity-50 disabled:transform-none">

                        <svg wire:loading wire:target="simpanTagihan" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>

                        <span wire:loading.remove>Sah & Terbitkan Tagihan</span>
                        <span wire:loading>Memproses Data...</span>
                    </button>
                </div>
            </div>

        </div>
    </div>

    {{-- SweetAlert2 Listener --}}
    @script
    <script>
        $wire.on('swal:success', data => {
            alert(data[0].text);
        });
        $wire.on('swal:error', data => {
            alert(data[0].text);
        });
    </script>
    @endscript
</div>