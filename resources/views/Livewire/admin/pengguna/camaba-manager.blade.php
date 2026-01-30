<div class="space-y-6">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-black text-[#002855]">Daftar Ulang (Camaba)</h1>
            <p class="mt-2 text-sm text-slate-500">Validasi calon mahasiswa yang masuk dari jalur PMB dan belum memiliki NIM resmi.</p>
        </div>
    </div>

    <!-- Filter -->
    <div class="bg-white p-5 shadow-sm rounded-2xl border border-slate-200 grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Program Studi</label>
            <div class="relative">
                <select wire:model.live="filterProdiId" class="block w-full rounded-xl border-slate-200 bg-slate-50 py-3 pl-4 pr-10 text-sm focus:border-[#002855] focus:ring-[#002855] transition-shadow outline-none appearance-none font-bold text-slate-700">
                    <option value="">Semua Prodi</option>
                    @foreach($prodis as $p)
                        <option value="{{ $p->id }}">{{ $p->nama_prodi }}</option>
                    @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-500">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                </div>
            </div>
        </div>
        <div>
            <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Cari Nama/No Daftar</label>
            <div class="relative">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari..." class="block w-full rounded-xl border-slate-200 bg-slate-50 py-3 pl-10 pr-4 text-sm focus:border-[#002855] focus:ring-[#002855] transition-shadow outline-none font-bold text-slate-700">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center px-3 text-slate-400">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </div>
            </div>
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

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-[#002855] text-white">
                    <tr>
                        <th class="px-6 py-4 text-left text-[10px] font-bold uppercase tracking-widest">No Pendaftaran</th>
                        <th class="px-6 py-4 text-left text-[10px] font-bold uppercase tracking-widest">Nama Lengkap</th>
                        <th class="px-6 py-4 text-left text-[10px] font-bold uppercase tracking-widest">Prodi / Jalur</th>
                        <th class="px-6 py-4 text-center text-[10px] font-bold uppercase tracking-widest">Status Bayar</th>
                        <th class="px-6 py-4 text-right text-[10px] font-bold uppercase tracking-widest">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($camabas as $mhs)
                    @php
                        $tagihan = $mhs->tagihan->first();
                        $lunas = $tagihan && $tagihan->status_bayar == 'LUNAS';
                        $dispensasi = $mhs->data_tambahan['bebas_keuangan'] ?? false;
                    @endphp
                    <tr class="hover:bg-slate-50/80 transition-colors group">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-mono font-bold text-slate-600 bg-slate-50/50">
                            {{ $mhs->nim }}
                            @if($dispensasi)
                                <div class="mt-1">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-black bg-[#fcc000] text-[#002855]">
                                        DISPENSASI
                                    </span>
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-bold text-slate-800">{{ $mhs->nama_lengkap }}</div>
                            <div class="text-xs text-slate-400 mt-0.5">{{ $mhs->email_pribadi }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-slate-600">{{ $mhs->prodi->nama_prodi }}</div>
                            <div class="text-xs text-[#002855] font-bold mt-0.5">{{ $mhs->programKelas->nama_program }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($lunas)
                                <span class="px-3 py-1 inline-flex text-[10px] font-black uppercase rounded-full bg-emerald-100 text-emerald-700 border border-emerald-200">
                                    LUNAS
                                </span>
                            @else
                                <span class="px-3 py-1 inline-flex text-[10px] font-black uppercase rounded-full bg-rose-100 text-rose-700 border border-rose-200">
                                    BELUM LUNAS
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                            @if($lunas || $dispensasi)
                                <button wire:click="generateNimResmi('{{ $mhs->id }}')" 
                                    wire:confirm="Yakin resmikan mahasiswa ini? NIM akan digenerate otomatis."
                                    class="inline-flex items-center px-3 py-1.5 border border-transparent text-[10px] font-black uppercase rounded-lg shadow-sm text-white bg-[#002855] hover:bg-[#001a38] transition-all">
                                    <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    Generate NIM
                                </button>
                            @else
                                <span class="text-[10px] font-bold text-slate-400 italic bg-slate-100 px-2 py-1 rounded">Menunggu Bayar</span>
                            @endif
                            
                            {{-- Tombol Atur Dispensasi --}}
                            <button wire:click="edit('{{ $mhs->id }}')" class="text-[#002855] hover:text-[#fcc000] text-[10px] font-black uppercase hover:underline ml-2">
                                Dispensasi
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="bg-slate-50 p-4 rounded-full mb-3">
                                    <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" /></svg>
                                </div>
                                <p class="text-slate-500 font-medium">Tidak ada data calon mahasiswa baru.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
            {{ $camabas->links() }}
        </div>
    </div>

    <!-- Modal Form Dispensasi (FIXED) -->
    @if($showForm)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            
            {{-- Backdrop --}}
            <div class="fixed inset-0 bg-slate-900/75 transition-opacity" aria-hidden="true" wire:click="batal"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            {{-- Modal Panel --}}
            <div class="relative inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-white/20 z-50">
                
                {{-- Header Modal --}}
                <div class="bg-[#002855] px-6 py-5 flex items-center gap-4">
                    <div class="bg-[#fcc000] p-2 rounded-xl text-[#002855]">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <div>
                        <h3 class="text-lg leading-6 font-black text-white uppercase tracking-tight" id="modal-title">
                            Dispensasi Camaba
                        </h3>
                        <p class="text-[10px] text-slate-300 font-bold mt-0.5">{{ $nama_lengkap }}</p>
                    </div>
                </div>

                <div class="px-8 py-8">
                    <div class="bg-amber-50 p-5 rounded-2xl border border-amber-100">
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="checkbox" wire:model="bebas_keuangan" class="mt-1 h-5 w-5 text-[#002855] border-gray-300 rounded focus:ring-[#fcc000]">
                            <div>
                                <span class="block text-sm font-bold text-gray-900">Aktifkan Dispensasi (Bebas Syarat Bayar)</span>
                                <span class="block text-xs text-gray-600 mt-1 leading-relaxed">
                                    Jika dicentang, camaba ini dapat di-generate NIM-nya meskipun pembayaran Daftar Ulang belum lunas.
                                </span>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="bg-slate-50 px-8 py-5 border-t border-slate-100 flex flex-row-reverse gap-3">
                    <button wire:click="save" type="button" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-lg px-6 py-3 bg-[#002855] text-sm font-bold text-white hover:bg-[#001a38] hover:scale-105 transition-all focus:outline-none sm:ml-3 sm:w-auto">
                        Simpan Perubahan
                    </button>
                    <button wire:click="batal" type="button" class="mt-3 w-full inline-flex justify-center rounded-xl border border-slate-300 shadow-sm px-6 py-3 bg-white text-sm font-bold text-slate-500 hover:bg-slate-50 hover:text-slate-700 transition-all focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>