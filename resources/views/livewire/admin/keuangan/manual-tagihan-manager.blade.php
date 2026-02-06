<div class="max-w-5xl mx-auto space-y-6">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-black text-[#002855]">Buat Tagihan Manual</h1>
            <p class="mt-2 text-sm text-slate-500">Penerbitan invoice khusus (Denda, Ganti Rugi, Biaya Susulan) perorangan.</p>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="bg-emerald-50 border border-emerald-100 p-4 rounded-xl text-emerald-800 text-sm flex items-center shadow-sm animate-in fade-in slide-in-from-top-2">
            <svg class="w-5 h-5 mr-3 text-emerald-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            <span class="font-bold">{{ session('success') }}</span>
        </div>
    @endif

    <div class="bg-white shadow-xl rounded-3xl border border-slate-200 overflow-hidden">
        <div class="p-8 space-y-10">
            
            {{-- LANGKAH 1: PILIH MAHASISWA --}}
            <div class="space-y-4">
                <h4 class="text-xs font-bold text-[#002855] uppercase border-l-4 border-[#fcc000] pl-3 tracking-widest">1. Target Mahasiswa</h4>
                
                @if(!$selectedMhs)
                    <div class="relative">
                        <input type="text" wire:model.live.debounce.300ms="searchMhs" 
                            class="block w-full rounded-xl border-slate-300 bg-white py-4 pl-12 pr-4 text-sm focus:border-[#002855] focus:ring-[#002855] transition-shadow outline-none font-bold placeholder-slate-400" 
                            placeholder="Ketik NIM atau Nama Mahasiswa...">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        
                        {{-- Dropdown Hasil Pencarian --}}
                        @if(!empty($searchResults))
                            <div class="absolute z-10 mt-2 w-full bg-white shadow-2xl rounded-xl py-2 text-base ring-1 ring-black ring-opacity-5 overflow-hidden">
                                @foreach($searchResults as $mhs)
                                    <div wire:click="selectMhs('{{ $mhs->id }}')" class="cursor-pointer px-4 py-3 hover:bg-indigo-50 border-b border-slate-50 last:border-0 transition-colors">
                                        <span class="font-bold text-[#002855] block">{{ $mhs->person->nama_lengkap ?? $mhs->nama_lengkap }}</span>
                                        <span class="text-slate-500 text-xs font-mono">{{ $mhs->nim }} - {{ $mhs->prodi->nama_prodi }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @elseif(strlen($searchMhs) >= 3)
                            <div class="absolute z-10 mt-2 w-full bg-white shadow-xl rounded-xl py-4 px-6 text-sm text-slate-500 italic border border-slate-100 text-center">
                                Tidak ditemukan data mahasiswa.
                            </div>
                        @endif
                    </div>
                @else
                    {{-- Kartu Mahasiswa Terpilih --}}
                    <div class="bg-indigo-50 border border-indigo-100 rounded-2xl p-5 flex justify-between items-center animate-in zoom-in-95">
                        <div class="flex items-center gap-4">
                            <div class="h-12 w-12 bg-[#002855] rounded-full flex items-center justify-center text-white text-lg font-black shadow-lg">
                                {{ substr($selectedMhs->person->nama_lengkap ?? $selectedMhs->nama_lengkap, 0, 1) }}
                            </div>
                            <div>
                                <div class="text-sm font-black text-[#002855] uppercase tracking-wide">{{ $selectedMhs->person->nama_lengkap ?? $selectedMhs->nama_lengkap }}</div>
                                <div class="text-xs text-indigo-600 font-bold mt-0.5">{{ $selectedMhs->nim }} • {{ $selectedMhs->prodi->nama_prodi }}</div>
                            </div>
                        </div>
                        <button wire:click="resetSelection" class="text-rose-600 hover:text-rose-800 text-xs font-black uppercase tracking-widest bg-white px-4 py-2 rounded-lg border border-rose-100 hover:bg-rose-50 transition-all shadow-sm">
                            Ganti Target
                        </button>
                    </div>
                @endif
                @error('selectedMhs') <span class="text-rose-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
            </div>

            {{-- LANGKAH 2: DETAIL TAGIHAN --}}
            <div class="{{ !$selectedMhs ? 'opacity-50 pointer-events-none grayscale' : '' }} space-y-6 transition-all duration-300">
                <h4 class="text-xs font-bold text-[#002855] uppercase border-l-4 border-[#fcc000] pl-3 tracking-widest">2. Rincian Invoice</h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-slate-50 p-6 rounded-2xl border border-slate-100">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-2">Untuk Semester</label>
                        <select wire:model="semesterId" class="block w-full rounded-xl border-slate-300 bg-white text-slate-900 focus:border-[#002855] focus:ring-[#002855] text-sm transition-all">
                            @foreach($semesters as $sem)
                                <option value="{{ $sem->id }}">{{ $sem->nama_tahun }} {{ $sem->is_active ? '(Aktif)' : '' }}</option>
                            @endforeach
                        </select>
                        @error('semesterId') <span class="text-rose-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-2">Jenis Biaya</label>
                        <select wire:model.live="komponenId" class="block w-full rounded-xl border-slate-300 bg-white text-slate-900 focus:border-[#002855] focus:ring-[#002855] text-sm transition-all">
                            <option value="">-- Pilih Komponen --</option>
                            @foreach($komponens as $k)
                                <option value="{{ $k->id }}">{{ $k->nama_komponen }} ({{ $k->tipe_biaya }})</option>
                            @endforeach
                        </select>
                        @error('komponenId') <span class="text-rose-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-2">Deskripsi / Keterangan Tagihan</label>
                        <input type="text" wire:model="deskripsi" class="block w-full rounded-xl border-slate-300 bg-white text-slate-900 focus:border-[#002855] focus:ring-[#002855] text-sm" placeholder="Contoh: Denda Keterlambatan Pengembalian Alat Lab">
                        @error('deskripsi') <span class="text-rose-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-2">Nominal Tagihan (Rp)</label>
                        <div class="relative rounded-xl shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <span class="text-slate-400 font-bold sm:text-sm">Rp</span>
                            </div>
                            <input type="number" wire:model="nominal" class="block w-full rounded-xl border-slate-300 pl-12 pr-4 py-3 focus:border-[#002855] focus:ring-[#002855] text-lg font-black text-slate-800 placeholder-slate-300" placeholder="0">
                        </div>
                        @error('nominal') <span class="text-rose-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="flex justify-end pt-6 border-t border-slate-100">
                    <button wire:click="simpanTagihan" 
                        wire:loading.attr="disabled"
                        class="px-8 py-3 bg-[#002855] text-white rounded-xl text-sm font-bold shadow-xl shadow-indigo-900/20 hover:bg-[#001a38] hover:scale-105 transition-all flex items-center disabled:opacity-50 disabled:cursor-not-allowed">
                        
                        <svg wire:loading wire:target="simpanTagihan" class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        
                        <span wire:loading.remove>Terbitkan Tagihan</span>
                        <span wire:loading>Memproses...</span>
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>  