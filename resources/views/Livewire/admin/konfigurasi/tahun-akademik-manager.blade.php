<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-[#002855]">Konfigurasi Tahun Akademik</h1>
            <p class="text-slate-500 text-sm mt-1">Kelola periode aktif, masa KRS, dan akses input nilai dosen.</p>
        </div>
        @if(!$showForm)
        <button wire:click="$set('showForm', true)" class="inline-flex items-center px-5 py-2.5 bg-[#002855] text-white rounded-xl font-bold text-sm shadow-lg hover:bg-[#001a38] transition-all">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
            Tambah Semester
        </button>
        @endif
    </div>

    @if (session()->has('success'))
        <div class="bg-emerald-50 border border-emerald-100 p-4 rounded-xl text-emerald-800 text-sm font-bold animate-in fade-in">
            {{ session('success') }}
        </div>
    @endif

    {{-- Form Section --}}
    @if($showForm)
    <div class="bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden animate-in zoom-in-95 duration-200">
        <div class="px-8 py-5 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
            <h3 class="text-sm font-bold text-[#002855] uppercase tracking-wider">
                {{ $editMode ? 'Edit Semester' : 'Setup Semester Baru' }}
            </h3>
            <button wire:click="batal" class="text-slate-400 hover:text-slate-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button>
        </div>

        <div class="p-8 space-y-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Basic Info --}}
                <div class="space-y-4 md:col-span-3 grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Kode Semester</label>
                        <input type="text" wire:model="kode_tahun" placeholder="Cth: 20251" class="block w-full rounded-xl border-slate-200 bg-slate-50 text-sm font-black py-2.5 pl-4 focus:ring-2 focus:ring-[#fcc000] focus:outline-none transition-all">
                        @error('kode_tahun') <span class="text-rose-500 text-[10px] font-bold">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Nama Semester</label>
                        <input type="text" wire:model="nama_tahun" placeholder="Cth: Ganjil 2025/2026" class="block w-full rounded-xl border-slate-200 bg-slate-50 text-sm font-bold py-2.5 pl-4 focus:ring-2 focus:ring-[#fcc000] focus:outline-none transition-all">
                        @error('nama_tahun') <span class="text-rose-500 text-[10px] font-bold">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Tipe</label>
                        <select wire:model="semester_tipe" class="block w-full rounded-xl border-slate-200 bg-slate-50 text-sm font-bold py-2.5 pl-4 focus:ring-2 focus:ring-[#fcc000] focus:outline-none transition-all">
                            <option value="1">Ganjil</option>
                            <option value="2">Genap</option>
                            <option value="3">Pendek</option>
                        </select>
                    </div>
                </div>

                {{-- Dates Semester --}}
                <div class="bg-indigo-50/50 p-6 rounded-2xl border border-indigo-100 space-y-4">
                    <h4 class="text-[11px] font-black text-indigo-700 uppercase tracking-widest">Rentang Semester</h4>
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase">Tanggal Mulai</label>
                            <input type="date" wire:model="tanggal_mulai" class="w-full rounded-lg border-slate-200 text-sm py-2 pl-4 focus:ring-[#002855]">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase">Tanggal Selesai</label>
                            <input type="date" wire:model="tanggal_selesai" class="w-full rounded-lg border-slate-200 text-sm py-2 pl-4 focus:ring-[#002855]">
                        </div>
                    </div>
                </div>

                {{-- Dates KRS --}}
                <div class="bg-amber-50/50 p-6 rounded-2xl border border-amber-100 space-y-4">
                    <h4 class="text-[11px] font-black text-amber-700 uppercase tracking-widest">Masa Pengisian KRS</h4>
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase">Buka KRS</label>
                            <input type="date" wire:model="tgl_mulai_krs" class="w-full rounded-lg border-slate-200 text-sm py-2 pl-4 focus:ring-[#fcc000]">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase">Tutup KRS</label>
                            <input type="date" wire:model="tgl_selesai_krs" class="w-full rounded-lg border-slate-200 text-sm py-2 pl-4 focus:ring-[#fcc000]">
                        </div>
                    </div>
                </div>

                {{-- Access Toggle --}}
                <div class="bg-slate-50 p-6 rounded-2xl border border-slate-200 flex flex-col justify-center">
                    <h4 class="text-[11px] font-black text-slate-500 uppercase tracking-widest mb-4">Akses Perkuliahan</h4>
                    <label class="flex items-center cursor-pointer group">
                        <div class="relative">
                            <input type="checkbox" wire:model="buka_input_nilai" class="sr-only">
                            <div class="block w-14 h-8 rounded-full transition-colors {{ $buka_input_nilai ? 'bg-emerald-500' : 'bg-slate-300' }}"></div>
                            <div class="dot absolute left-1 top-1 bg-white w-6 h-6 rounded-full transition-transform {{ $buka_input_nilai ? 'translate-x-6' : '' }}"></div>
                        </div>
                        <div class="ml-4 text-sm font-bold text-slate-700 group-hover:text-[#002855]">Buka Input Nilai Dosen</div>
                    </label>
                    <p class="text-[10px] text-slate-400 mt-3 italic">*Jika aktif, dosen dapat menginput dan mempublikasikan nilai mahasiswa di portal dosen.</p>
                </div>
            </div>

            <div class="pt-6 border-t border-slate-100 flex justify-end gap-3">
                <button wire:click="batal" class="px-6 py-2.5 text-sm font-bold text-slate-500 hover:bg-slate-100 rounded-xl transition-all">Batal</button>
                <button wire:click="save" class="px-8 py-2.5 bg-[#002855] text-white rounded-xl text-sm font-bold shadow-lg shadow-indigo-900/20 hover:bg-[#001a38] transition-all">
                    {{ $editMode ? 'Simpan Perubahan' : 'Terbitkan Semester' }}
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- Table List --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-[#002855] text-white">
                    <tr>
                        <th class="px-6 py-4 text-left text-[10px] font-bold uppercase tracking-widest">Kode</th>
                        <th class="px-6 py-4 text-left text-[10px] font-bold uppercase tracking-widest">Semester</th>
                        <th class="px-6 py-4 text-center text-[10px] font-bold uppercase tracking-widest">Status Aktif</th>
                        <th class="px-6 py-4 text-center text-[10px] font-bold uppercase tracking-widest">Masa KRS</th>
                        <th class="px-6 py-4 text-center text-[10px] font-bold uppercase tracking-widest">Input Nilai</th>
                        <th class="px-6 py-4 text-right text-[10px] font-bold uppercase tracking-widest">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($semesters as $sem)
                    <tr class="hover:bg-slate-50/80 transition-colors {{ $sem->is_active ? 'bg-indigo-50/30' : '' }}">
                        <td class="px-6 py-4 whitespace-nowrap font-black text-[#002855]">{{ $sem->kode_tahun }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-bold text-slate-800">{{ $sem->nama_tahun }}</div>
                            <div class="text-[10px] text-slate-400 font-medium">
                                {{ $sem->tanggal_mulai ? $sem->tanggal_mulai->format('d/m/Y') : '-' }} s.d {{ $sem->tanggal_selesai ? $sem->tanggal_selesai->format('d/m/Y') : '-' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($sem->is_active)
                                <span class="px-3 py-1 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-700 border border-emerald-200 uppercase">Aktif</span>
                            @else
                                <button wire:click="aktifkanSemester({{ $sem->id }})" wire:confirm="Jadikan semester ini sebagai semester aktif?" class="text-[10px] font-bold text-slate-400 hover:text-indigo-600 border border-slate-200 px-3 py-1 rounded-lg hover:bg-white transition-all uppercase">Set Aktif</button>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <button wire:click="toggleKrs({{ $sem->id }})" class="inline-flex flex-col items-center group">
                                <span class="px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase border {{ $sem->buka_krs ? 'bg-blue-100 text-blue-700 border-blue-200' : 'bg-rose-100 text-rose-700 border-rose-200' }}">
                                    {{ $sem->buka_krs ? 'DIBUKA' : 'DITUTUP' }}
                                </span>
                                @if($sem->tgl_mulai_krs)
                                    <span class="text-[9px] text-slate-400 mt-1 font-bold group-hover:text-blue-600">
                                        {{ $sem->tgl_mulai_krs->format('d M') }} - {{ $sem->tgl_selesai_krs->format('d M') }}
                                    </span>
                                @endif
                            </button>
                        </td>
                        <td class="px-6 py-4 text-center">
                             <button wire:click="toggleInputNilai({{ $sem->id }})" class="px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase border {{ $sem->buka_input_nilai ? 'bg-emerald-100 text-emerald-700 border-emerald-200' : 'bg-slate-100 text-slate-400 border-slate-200' }}">
                                {{ $sem->buka_input_nilai ? 'OPEN' : 'LOCKED' }}
                            </button>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <button wire:click="edit({{ $sem->id }})" class="p-2 text-[#002855] hover:bg-[#002855]/10 rounded-lg transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>