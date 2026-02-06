<div class="space-y-6">
    {{-- Header --}}
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-black text-[#002855]">Mutasi Status Mahasiswa</h1>
            <p class="mt-2 text-sm text-slate-500">Kelola status Cuti, Non-Aktif, Keluar, atau Lulus untuk semester aktif.</p>
        </div>
    </div>

    {{-- Filter Pencarian --}}
    <div class="bg-white p-5 shadow-sm rounded-2xl border border-slate-200">
        <label class="block text-xs font-bold text-[#002855] uppercase tracking-widest mb-2">Cari Mahasiswa</label>
        <div class="relative">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari berdasarkan NIM atau Nama..." class="block w-full rounded-xl border-slate-200 bg-slate-50 py-3 pl-10 pr-4 text-sm focus:border-[#002855] focus:ring-[#002855] transition-shadow outline-none font-bold text-slate-700">
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center px-3 text-slate-400">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
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
    @if (session()->has('error'))
        <div class="bg-rose-50 border border-rose-100 p-4 rounded-xl text-rose-800 text-sm flex items-center shadow-sm animate-in fade-in slide-in-from-top-2">
            <span class="font-bold">{{ session('error') }}</span>
        </div>
    @endif

    {{-- Tabel Data --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-[#002855] text-white">
                    <tr>
                        <th class="px-6 py-4 text-left text-[10px] font-bold uppercase tracking-widest">Mahasiswa</th>
                        <th class="px-6 py-4 text-left text-[10px] font-bold uppercase tracking-widest">Prodi</th>
                        <th class="px-6 py-4 text-center text-[10px] font-bold uppercase tracking-widest">Status Semester Ini</th>
                        <th class="px-6 py-4 text-right text-[10px] font-bold uppercase tracking-widest">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($mahasiswas as $mhs)
                    @php
                        // Ambil status semester ini jika ada, default A (Aktif) atau N (Non-Aktif/Belum KRS)
                        $riwayat = $mhs->riwayatStatus->first(); 
                        $status = $riwayat ? $riwayat->status_kuliah : '-';
                        
                        $badgeColor = match($status) {
                            'A' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                            'C' => 'bg-amber-100 text-amber-700 border-amber-200',
                            'N' => 'bg-slate-100 text-slate-600 border-slate-200',
                            'L' => 'bg-sky-100 text-sky-700 border-sky-200',
                            'K' => 'bg-rose-100 text-rose-700 border-rose-200',
                            'D' => 'bg-rose-100 text-rose-700 border-rose-200',
                            default => 'bg-slate-50 text-slate-400 border-slate-100'
                        };
                        $statusLabel = match($status) {
                            'A' => 'AKTIF', 'C' => 'CUTI', 'N' => 'NON-AKTIF', 
                            'L' => 'LULUS', 'K' => 'KELUAR', 'D' => 'DROP OUT', default => 'BELUM REGISTRASI'
                        };
                    @endphp
                    <tr class="hover:bg-slate-50/80 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="text-sm font-black text-slate-800">{{ $mhs->nama_lengkap }}</div>
                            <div class="text-[10px] font-mono text-slate-400 mt-0.5">{{ $mhs->nim }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm font-medium text-slate-600">
                            {{ $mhs->prodi->nama_prodi }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-3 py-1 inline-flex text-[10px] font-black uppercase rounded-full border {{ $badgeColor }}">
                                {{ $statusLabel }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <button wire:click="openMutasi('{{ $mhs->id }}')" 
                                class="inline-flex items-center px-3 py-1.5 bg-[#fcc000]/10 text-[#002855] text-[10px] font-black uppercase tracking-wide rounded-lg border border-[#fcc000]/20 hover:bg-[#fcc000] hover:text-[#002855] hover:border-[#fcc000] transition-all shadow-sm">
                                Ubah Status
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-16 text-center">
                            <p class="text-slate-400 font-medium italic text-sm">Tidak ada mahasiswa yang ditemukan.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
            {{ $mahasiswas->links() }}
        </div>
    </div>

    <!-- MODAL FORM MUTASI (FIXED) -->
    @if($showModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            
            {{-- Backdrop: Ganti blur dengan opacity solid agar tidak glitch --}}
            <div class="fixed inset-0 bg-gray-900/75 transition-opacity" aria-hidden="true" wire:click="batal"></div>

            {{-- Spacer untuk centering --}}
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            {{-- Modal Panel: Tambahkan relative & z-index tinggi --}}
            <div class="relative inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-white/20 z-50">
                
                {{-- Modal Header --}}
                <div class="bg-[#002855] px-6 py-5 flex items-center gap-4">
                    <div class="bg-[#fcc000] p-2 rounded-xl text-[#002855]">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                    </div>
                    <div>
                        <h3 class="text-lg leading-6 font-black text-white uppercase tracking-tight" id="modal-title">
                            Proses Mutasi Mahasiswa
                        </h3>
                        <p class="text-[10px] text-slate-300 font-bold mt-0.5">{{ $selectedMhsName }} ({{ $selectedMhsNim }})</p>
                    </div>
                </div>

                <div class="px-8 py-8 space-y-6">
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Status Baru</label>
                        <select wire:model.live="status_baru" class="block w-full rounded-xl border-slate-200 bg-slate-50 text-slate-900 focus:border-[#002855] focus:ring-[#002855] text-sm font-bold py-3 transition-all">
                            <option value="A">Aktif (Batalkan Cuti)</option>
                            <option value="C">Cuti Akademik</option>
                            <option value="N">Non-Aktif (Mangkir)</option>
                            <option value="L">Lulus / Yudisium</option>
                            <option value="K">Keluar / Pindah</option>
                            <option value="D">Drop Out (DO)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Nomor SK (Opsional)</label>
                        <input type="text" wire:model="nomor_sk" class="block w-full rounded-xl border-slate-200 focus:border-[#002855] focus:ring-[#002855] text-sm" placeholder="Contoh: SK-123/UN/2024">
                    </div>

                    <!-- OPSI TAGIHAN (Hanya muncul jika CUTI) -->
                    @if($status_baru == 'C')
                    <div class="bg-amber-50 p-5 rounded-2xl border border-amber-100 animate-in fade-in slide-in-from-top-2">
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="tagihan_cuti" type="checkbox" wire:model.live="buat_tagihan_cuti" class="focus:ring-[#fcc000] h-4 w-4 text-[#002855] border-gray-300 rounded">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="tagihan_cuti" class="font-bold text-amber-900">Buat Tagihan Biaya Cuti?</label>
                                <p class="text-xs text-amber-700 mt-1">Otomatis menerbitkan invoice administrasi cuti ke akun mahasiswa.</p>
                            </div>
                        </div>
                        
                        @if($buat_tagihan_cuti)
                        <div class="mt-4 pl-7">
                            <label class="block text-[10px] font-black text-amber-800 uppercase tracking-widest mb-1">Nominal Biaya Cuti (Rp)</label>
                            <input type="number" wire:model="nominal_biaya_cuti" class="block w-full rounded-xl border-amber-300 focus:border-[#002855] focus:ring-[#002855] text-sm font-bold text-slate-800">
                        </div>
                        @endif
                    </div>
                    @endif
                </div>

                <div class="bg-slate-50 px-8 py-5 border-t border-slate-100 flex flex-row-reverse gap-3">
                    <button wire:click="simpanMutasi" type="button" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-lg px-6 py-3 bg-[#002855] text-sm font-bold text-white hover:bg-[#001a38] hover:scale-105 transition-all focus:outline-none sm:ml-3 sm:w-auto">
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