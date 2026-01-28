<div>
    {{-- SEO & Header --}}
    <x-slot name="title">Manajemen Tahun Akademik</x-slot>
    <x-slot name="header">Tahun Akademik & Semester</x-slot>

    <div class="space-y-8">
        {{-- Top Toolbar --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <p class="text-slate-500 text-sm">Konfigurasi periode perkuliahan aktif, kalender akademik, dan kontrol akses KRS.</p>
            </div>

            <div class="flex items-center gap-3">
                @if($showForm)
                <button wire:click="batal"
                    class="inline-flex items-center px-6 py-3 bg-white border border-slate-200 text-slate-600 rounded-xl font-bold text-sm hover:bg-slate-50 transition-all">
                    Batalkan
                </button>
                @else
                <button wire:click="$toggle('showForm')"
                    class="inline-flex items-center px-6 py-3 bg-unmaris-yellow text-unmaris-blue rounded-xl font-bold text-sm shadow-lg shadow-unmaris-yellow/20 hover:scale-105 transition-all">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Semester Baru
                </button>
                @endif
            </div>
        </div>

        {{-- Alert Feedback --}}
        @if (session()->has('success'))
        <div class="bg-emerald-50 border border-emerald-100 p-4 rounded-xl text-emerald-800 text-sm flex items-center animate-in fade-in duration-300 shadow-sm">
            <svg class="w-5 h-5 mr-3 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            <span class="font-bold">{{ session('success') }}</span>
        </div>
        @endif

        {{-- Form Section --}}
        @if($showForm)
        <div class="bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden animate-in slide-in-from-top-4 duration-500">
            <div class="px-8 py-5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                <h3 class="text-sm font-bold text-unmaris-blue uppercase tracking-wider">
                    {{ $editMode ? 'Perbarui Konfigurasi Semester' : 'Setup Tahun Akademik Baru' }}
                </h3>
                <div class="flex items-center space-x-2">
                    <span class="w-2 h-2 bg-unmaris-yellow rounded-full animate-pulse"></span>
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Configuration Mode</span>
                </div>
            </div>

            <div class="p-8 lg:p-10 space-y-10">
                {{-- Row 1: Identitas Semester --}}
                <div class="space-y-6">
                    <h4 class="text-xs font-bold text-unmaris-blue uppercase border-l-4 border-unmaris-yellow pl-4 tracking-widest">Identitas Periode</h4>
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-8">
                        <div class="md:col-span-3">
                            <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-2">Kode Semester *</label>
                            <input type="text" wire:model="kode_tahun" class="block w-full rounded-xl border-slate-200 bg-slate-50 py-3.5 px-4 focus:bg-white focus:border-unmaris-blue text-sm transition-all outline-none" placeholder="Contoh: 20251">
                            @error('kode_tahun') <span class="text-red-500 text-[11px] font-bold mt-2 block">{{ $message }}</span> @enderror
                        </div>
                        <div class="md:col-span-6">
                            <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-2">Nama Semester (Deskripsi) *</label>
                            <input type="text" wire:model="nama_tahun" class="block w-full rounded-xl border-slate-200 bg-slate-50 py-3.5 px-4 focus:bg-white focus:border-unmaris-blue text-sm transition-all outline-none" placeholder="Contoh: Ganjil 2025/2026">
                            @error('nama_tahun') <span class="text-red-500 text-[11px] font-bold mt-2 block">{{ $message }}</span> @enderror
                        </div>
                        <div class="md:col-span-3">
                            <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-2">Tipe Semester</label>
                            <select wire:model="semester_tipe" class="block w-full rounded-xl border-slate-200 bg-slate-50 py-3.5 px-4 focus:bg-white focus:border-unmaris-blue text-sm transition-all outline-none">
                                <option value="1">Ganjil</option>
                                <option value="2">Genap</option>
                                <option value="3">Pendek / Antara</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Row 2: Periode Aktif Semester (Baru) --}}
                <div class="space-y-6">
                    <h4 class="text-xs font-bold text-unmaris-blue uppercase border-l-4 border-unmaris-yellow pl-4 tracking-widest">Periode Aktif Perkuliahan</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-2">Tanggal Mulai Semester</label>
                            <input type="date" wire:model="tanggal_mulai" class="block w-full rounded-xl border-slate-200 bg-slate-50 py-3.5 px-4 focus:bg-white focus:border-unmaris-blue text-sm transition-all outline-none">
                            @error('tanggal_mulai') <span class="text-red-500 text-[11px] font-bold mt-2 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-2">Tanggal Selesai Semester</label>
                            <input type="date" wire:model="tanggal_selesai" class="block w-full rounded-xl border-slate-200 bg-slate-50 py-3.5 px-4 focus:bg-white focus:border-unmaris-blue text-sm transition-all outline-none">
                            @error('tanggal_selesai') <span class="text-red-500 text-[11px] font-bold mt-2 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                {{-- Row 3: Masa KRS --}}
                <div class="space-y-6">
                    <h4 class="text-xs font-bold text-unmaris-blue uppercase border-l-4 border-unmaris-yellow pl-4 tracking-widest">Periode Pengisian KRS Online</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-2">Tanggal Mulai Akses KRS</label>
                            <input type="date" wire:model="tgl_mulai_krs" class="block w-full rounded-xl border-slate-200 bg-slate-50 py-3.5 px-4 focus:bg-white focus:border-unmaris-blue text-sm transition-all outline-none">
                            @error('tgl_mulai_krs') <span class="text-red-500 text-[11px] font-bold mt-2 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-2">Tanggal Berakhir Akses KRS</label>
                            <input type="date" wire:model="tgl_selesai_krs" class="block w-full rounded-xl border-slate-200 bg-slate-50 py-3.5 px-4 focus:bg-white focus:border-unmaris-blue text-sm transition-all outline-none">
                            @error('tgl_selesai_krs') <span class="text-red-500 text-[11px] font-bold mt-2 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <div class="pt-10 border-t border-slate-100 flex justify-end gap-4">
                    <button wire:click="{{ $editMode ? 'simpanUpdate' : 'simpanBaru' }}"
                        class="px-12 py-4 bg-unmaris-blue text-white rounded-xl text-sm font-bold shadow-xl shadow-unmaris-blue/20 hover:scale-105 transition-all">
                        {{ $editMode ? 'Update Konfigurasi' : 'Simpan & Publikasikan' }}
                    </button>
                </div>
            </div>
        </div>
        @endif

        {{-- Table Section --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-100">
                            <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Kode</th>
                            <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Nama Semester & Periode</th>
                            <th class="px-8 py-5 text-center text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Status Akademik</th>
                            <th class="px-8 py-5 text-center text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Sistem KRS</th>
                            <th class="px-8 py-5 text-right text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($semesters as $sem)
                        <tr class="hover:bg-slate-50/50 transition-colors group {{ $sem->is_active ? 'bg-indigo-50/30' : '' }}">
                            <td class="px-8 py-6">
                                <span class="text-sm font-black text-unmaris-blue bg-unmaris-blue/5 px-3 py-1.5 rounded-lg border border-unmaris-blue/10">
                                    {{ $sem->kode_tahun }}
                                </span>
                            </td>
                            <td class="px-8 py-6">
                                <div class="text-sm font-bold text-slate-800">{{ $sem->nama_tahun }}</div>
                                <div class="text-[10px] text-slate-400 font-bold uppercase tracking-tighter mt-1">
                                    @if($sem->tanggal_mulai && $sem->tanggal_selesai)
                                    Periode: {{ $sem->tanggal_mulai->format('d/m/Y') }} - {{ $sem->tanggal_selesai->format('d/m/Y') }}
                                    @else
                                    <span class="italic">Rentang periode belum diatur</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-8 py-6 text-center">
                                @if($sem->is_active)
                                <span class="inline-flex items-center px-4 py-1.5 rounded-full bg-emerald-100 text-emerald-700 text-[10px] font-black uppercase tracking-widest border border-emerald-200">
                                    <span class="w-2 h-2 bg-emerald-500 rounded-full mr-2 animate-pulse"></span>
                                    Running
                                </span>
                                @else
                                <button wire:click="aktifkanSemester({{ $sem->id }})"
                                    wire:confirm="Aktifkan semester ini? Semester lain akan otomatis dinonaktifkan."
                                    class="px-4 py-1.5 rounded-xl border border-slate-200 text-slate-400 text-[10px] font-bold uppercase hover:bg-unmaris-blue hover:text-white hover:border-unmaris-blue transition-all">
                                    Set Aktif
                                </button>
                                @endif
                            </td>
                            <td class="px-8 py-6 text-center">
                                <button wire:click="toggleKrs({{ $sem->id }})"
                                    class="inline-flex items-center px-3 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all
                                {{ $sem->buka_krs ? 'bg-blue-600 text-white shadow-md shadow-blue-200' : 'bg-slate-100 text-slate-400 border border-slate-200' }}">
                                    {{ $sem->buka_krs ? 'Open Access' : 'Locked' }}
                                </button>

                                @if($sem->buka_krs)
                                <div class="mt-3">
                                    @if($sem->tgl_mulai_krs && $sem->tgl_selesai_krs)
                                    <div class="text-[10px] font-bold text-slate-500 flex flex-col items-center">
                                        <span class="bg-white px-2 py-0.5 rounded border border-slate-100 shadow-sm">
                                            {{ $sem->tgl_mulai_krs->format('d/m/Y') }} - {{ $sem->tgl_selesai_krs->format('d/m/Y') }}
                                        </span>

                                        @if(now()->gt($sem->tgl_selesai_krs->endOfDay()))
                                        <span class="text-[9px] text-rose-500 font-black mt-1 uppercase tracking-tighter">Expired Periode</span>
                                        @elseif(now()->lt($sem->tgl_mulai_krs->startOfDay()))
                                        <span class="text-[9px] text-amber-500 font-black mt-1 uppercase tracking-tighter">Upcoming</span>
                                        @else
                                        <span class="text-[9px] text-blue-500 font-black mt-1 uppercase tracking-tighter italic">Currently Available</span>
                                        @endif
                                    </div>
                                    @else
                                    <span class="text-[9px] text-rose-400 italic">Jadwal Tgl Belum Set</span>
                                    @endif
                                </div>
                                @endif
                            </td>
                            <td class="px-8 py-6 text-right">
                                <button wire:click="edit({{ $sem->id }})"
                                    class="p-2.5 text-slate-400 hover:text-unmaris-blue hover:bg-unmaris-blue/5 rounded-xl transition-all opacity-0 group-hover:opacity-100">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>


</div>