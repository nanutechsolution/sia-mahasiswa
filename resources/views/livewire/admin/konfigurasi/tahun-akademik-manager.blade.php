<div class="space-y-8 font-sans text-slate-900">

    {{-- HEADER SECTION --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Tahun Akademik</h1>
            <p class="text-slate-500 text-sm mt-1">Atur periode semester, jadwal KRS, dan akses penilaian dosen.</p>
        </div>
        
        @if(!$showForm)
        <button wire:click="$set('showForm', true)" class="group inline-flex items-center justify-center px-5 py-2.5 bg-[#002855] text-white rounded-xl font-bold text-sm shadow-md shadow-indigo-900/10 hover:shadow-lg hover:shadow-indigo-900/20 hover:-translate-y-0.5 transition-all duration-200">
            <svg class="w-4 h-4 mr-2 transition-transform group-hover:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
            Semester Baru
        </button>
        @endif
    </div>

    {{-- NOTIFICATIONS --}}
    @if (session()->has('success'))
        <div class="bg-emerald-50 border border-emerald-100 p-4 rounded-xl flex items-center gap-3 text-emerald-800 text-sm font-bold animate-in fade-in slide-in-from-top-2 shadow-sm">
            <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- FORM SECTION (Collapsible) --}}
    @if($showForm)
    <div class="bg-white rounded-2xl shadow-xl shadow-slate-200/50 border border-slate-200 overflow-hidden animate-in fade-in zoom-in-95 duration-300 ring-1 ring-slate-100">
        {{-- Form Header --}}
        <div class="px-8 py-5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between backdrop-blur-sm">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-[#002855] text-white flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                </div>
                <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wide">
                    {{ $editMode ? 'Edit Data Semester' : 'Setup Semester Baru' }}
                </h3>
            </div>
            <button wire:click="batal" class="p-2 rounded-lg text-slate-400 hover:text-rose-500 hover:bg-rose-50 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>

        {{-- Form Body --}}
        <div class="p-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                {{-- COLUMN 1: Identitas --}}
                <div class="space-y-6 lg:col-span-3">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="group">
                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2 group-focus-within:text-[#002855]">Kode Semester</label>
                            <div class="relative">
                                <input type="text" wire:model="kode_tahun" placeholder="20251" class="block w-full rounded-xl border-slate-200 bg-slate-50/50 text-sm font-bold py-3 pl-4 focus:ring-2 focus:ring-[#fcc000] focus:border-transparent focus:bg-white transition-all shadow-sm">
                            </div>
                            @error('kode_tahun') <span class="text-rose-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                        </div>

                        <div class="group">
                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2 group-focus-within:text-[#002855]">Nama Semester</label>
                            <input type="text" wire:model="nama_tahun" placeholder="Ganjil 2025/2026" class="block w-full rounded-xl border-slate-200 bg-slate-50/50 text-sm font-bold py-3 pl-4 focus:ring-2 focus:ring-[#fcc000] focus:border-transparent focus:bg-white transition-all shadow-sm">
                            @error('nama_tahun') <span class="text-rose-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                        </div>

                        <div class="group">
                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2 group-focus-within:text-[#002855]">Tipe Semester</label>
                            <div class="relative">
                                <select wire:model="semester_tipe" class="block w-full rounded-xl border-slate-200 bg-slate-50/50 text-sm font-bold py-3 pl-4 pr-10 focus:ring-2 focus:ring-[#fcc000] focus:border-transparent focus:bg-white transition-all shadow-sm appearance-none">
                                    <option value="1">Ganjil</option>
                                    <option value="2">Genap</option>
                                    <option value="3">Pendek</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none text-slate-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- COLUMN 2: Periode Akademik --}}
                <div class="bg-indigo-50/30 p-6 rounded-2xl border border-indigo-100/50 space-y-5">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                        </div>
                        <h4 class="text-xs font-black text-indigo-900 uppercase tracking-widest">Periode Kuliah</h4>
                    </div>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1.5">Mulai</label>
                            <input type="date" wire:model="tanggal_mulai" class="w-full rounded-lg border-slate-200 text-sm font-medium py-2 px-3 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1.5">Selesai</label>
                            <input type="date" wire:model="tanggal_selesai" class="w-full rounded-lg border-slate-200 text-sm font-medium py-2 px-3 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>
                </div>

                {{-- COLUMN 3: Periode KRS --}}
                <div class="bg-amber-50/30 p-6 rounded-2xl border border-amber-100/50 space-y-5">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-6 h-6 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                        </div>
                        <h4 class="text-xs font-black text-amber-900 uppercase tracking-widest">Masa KRS</h4>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1.5">Buka KRS</label>
                            <input type="date" wire:model="tgl_mulai_krs" class="w-full rounded-lg border-slate-200 text-sm font-medium py-2 px-3 focus:ring-amber-500 focus:border-amber-500">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1.5">Tutup KRS</label>
                            <input type="date" wire:model="tgl_selesai_krs" class="w-full rounded-lg border-slate-200 text-sm font-medium py-2 px-3 focus:ring-amber-500 focus:border-amber-500">
                        </div>
                    </div>
                </div>

                {{-- COLUMN 4: Switch Akses --}}
                <div class="bg-slate-50/50 p-6 rounded-2xl border border-slate-200 flex flex-col justify-center">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-6 h-6 rounded-full bg-slate-200 text-slate-600 flex items-center justify-center">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                        </div>
                        <h4 class="text-xs font-black text-slate-600 uppercase tracking-widest">Akses Dosen</h4>
                    </div>

                    <label class="flex items-center justify-between cursor-pointer group p-3 rounded-xl hover:bg-white hover:shadow-sm transition-all border border-transparent hover:border-slate-200">
                        <span class="text-sm font-bold text-slate-700 group-hover:text-[#002855]">Input Nilai</span>
                        <div class="relative">
                            <input type="checkbox" wire:model="buka_input_nilai" class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                        </div>
                    </label>
                    <p class="text-[10px] text-slate-400 mt-2 px-3 leading-relaxed">
                        Aktifkan untuk mengizinkan dosen mengisi nilai pada periode ini.
                    </p>
                </div>
            </div>

            <div class="pt-8 mt-8 border-t border-slate-100 flex justify-end gap-3">
                <button wire:click="batal" class="px-6 py-2.5 text-sm font-bold text-slate-500 hover:text-slate-800 hover:bg-slate-50 rounded-xl transition-all">Batal</button>
                <button wire:click="save" class="px-8 py-2.5 bg-[#002855] text-white rounded-xl text-sm font-bold shadow-lg shadow-indigo-900/20 hover:bg-[#001a38] hover:shadow-indigo-900/30 hover:-translate-y-0.5 transition-all">
                    {{ $editMode ? 'Simpan Perubahan' : 'Terbitkan Semester' }}
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- DATA TABLE --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden ring-1 ring-slate-100">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-[#002855]">
                    <tr>
                        <th class="px-6 py-4 text-left text-[10px] font-bold text-slate-300 uppercase tracking-widest">Kode</th>
                        <th class="px-6 py-4 text-left text-[10px] font-bold text-slate-300 uppercase tracking-widest">Semester</th>
                        <th class="px-6 py-4 text-center text-[10px] font-bold text-slate-300 uppercase tracking-widest">Status</th>
                        <th class="px-6 py-4 text-center text-[10px] font-bold text-slate-300 uppercase tracking-widest">Akses KRS</th>
                        <th class="px-6 py-4 text-center text-[10px] font-bold text-slate-300 uppercase tracking-widest">Input Nilai</th>
                        <th class="px-6 py-4 text-right text-[10px] font-bold text-slate-300 uppercase tracking-widest">Opsi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($semesters as $sem)
                    <tr class="group hover:bg-slate-50/80 transition-colors {{ $sem->is_active ? 'bg-indigo-50/20' : '' }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="font-black text-[#002855] text-sm">{{ $sem->kode_tahun }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-bold text-slate-800 group-hover:text-[#002855] transition-colors">{{ $sem->nama_tahun }}</div>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="text-[10px] text-slate-400 font-medium bg-slate-100 px-1.5 py-0.5 rounded border border-slate-200">
                                    {{ $sem->tanggal_mulai ? $sem->tanggal_mulai->format('d M Y') : '-' }} s/d {{ $sem->tanggal_selesai ? $sem->tanggal_selesai->format('d M Y') : '-' }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($sem->is_active)
                                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black bg-emerald-50 text-emerald-600 border border-emerald-100 shadow-sm">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    AKTIF
                                </div>
                            @else
                                <button wire:click="aktifkanSemester({{ $sem->id }})" wire:confirm="Aktifkan semester ini?" class="text-[10px] font-bold text-slate-400 hover:text-[#002855] border border-slate-200 px-3 py-1 rounded-full hover:bg-white hover:border-[#002855] transition-all uppercase opacity-60 group-hover:opacity-100">
                                    Set Aktif
                                </button>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col items-center gap-1">
                                <button wire:click="toggleKrs({{ $sem->id }})" class="relative inline-flex h-5 w-9 items-center rounded-full transition-colors focus:outline-none {{ $sem->buka_krs ? 'bg-blue-600' : 'bg-slate-200' }}">
                                    <span class="sr-only">Toggle KRS</span>
                                    <span class="{{ $sem->buka_krs ? 'translate-x-5' : 'translate-x-1' }} inline-block h-3 w-3 transform rounded-full bg-white transition-transform"></span>
                                </button>
                                @if($sem->tgl_mulai_krs)
                                    <span class="text-[9px] text-slate-400 font-medium">
                                        {{ $sem->tgl_mulai_krs->format('d M') }} - {{ $sem->tgl_selesai_krs->format('d M') }}
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <button wire:click="toggleInputNilai({{ $sem->id }})" class="relative inline-flex h-5 w-9 items-center rounded-full transition-colors focus:outline-none {{ $sem->buka_input_nilai ? 'bg-emerald-500' : 'bg-slate-200' }}">
                                <span class="sr-only">Toggle Nilai</span>
                                <span class="{{ $sem->buka_input_nilai ? 'translate-x-5' : 'translate-x-1' }} inline-block h-3 w-3 transform rounded-full bg-white transition-transform"></span>
                            </button>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <button wire:click="edit({{ $sem->id }})" class="p-2 text-slate-400 hover:text-[#002855] hover:bg-slate-100 rounded-lg transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="w-12 h-12 text-slate-200 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                <p class="text-sm font-medium">Belum ada data tahun akademik</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>