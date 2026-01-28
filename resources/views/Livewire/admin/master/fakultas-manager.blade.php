<div>
    {{-- Mengirimkan Judul ke Tab Browser --}}
    <x-slot name="title">Manajemen Fakultas</x-slot>

    {{-- Mengirimkan Judul ke Header Layout agar "Beranda Utama" terganti --}}
    <x-slot name="header">Manajemen Fakultas</x-slot>

    <div class="space-y-8">
        <!-- Header Section (Sub-header untuk Deskripsi & Tombol) -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <p class="text-slate-500 text-sm">Kelola data fakultas induk di lingkungan Universitas Stella Maris Sumba.</p>
            </div>

            @if(!$showForm)
            <button wire:click="create"
                class="inline-flex items-center px-5 py-2.5 bg-unmaris-yellow text-unmaris-blue rounded-xl font-bold text-sm shadow-lg shadow-unmaris-yellow/20 hover:scale-105 transition-all">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Fakultas
            </button>
            @endif
        </div>

        <!-- Feedback Notifications -->
        @if (session()->has('success'))
        <div class="bg-emerald-50 border border-emerald-100 p-4 rounded-xl text-emerald-800 text-sm flex items-center shadow-sm animate-in fade-in duration-300">
            <svg class="w-5 h-5 mr-3 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            {{ session('success') }}
        </div>
        @endif

        <!-- Form Section -->
        @if($showForm)
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden animate-in slide-in-from-top-4 duration-500">
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                <h3 class="text-sm font-bold text-unmaris-blue uppercase tracking-wider">Formulir Data Fakultas</h3>
                <span class="px-2 py-1 bg-unmaris-yellow/20 text-unmaris-gold text-[10px] font-bold rounded-md">Wajib Diisi</span>
            </div>

            <div class="p-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-2">Kode Fakultas</label>
                        <input type="text" wire:model="kode_fakultas"
                            class="block w-full rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:border-unmaris-blue focus:ring-4 focus:ring-unmaris-blue/5 text-sm transition-all"
                            placeholder="Contoh: FT, FKIP">
                        @error('kode_fakultas') <span class="text-red-500 text-[11px] font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="md:col-span-1">
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-2">Nama Lengkap Fakultas</label>
                        <input type="text" wire:model="nama_fakultas"
                            class="block w-full rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:border-unmaris-blue focus:ring-4 focus:ring-unmaris-blue/5 text-sm transition-all"
                            placeholder="Fakultas Teknik">
                        @error('nama_fakultas') <span class="text-red-500 text-[11px] font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-2">Nama Dekan (Gelar)</label>
                        <input type="text" wire:model="nama_dekan"
                            class="block w-full rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:border-unmaris-blue focus:ring-4 focus:ring-unmaris-blue/5 text-sm transition-all"
                            placeholder="Nama Lengkap & Gelar">
                    </div>
                </div>

                <div class="mt-8 pt-6 border-t border-slate-100 flex justify-end gap-3">
                    <button wire:click="$set('showForm', false)"
                        class="px-6 py-2.5 text-sm font-bold text-slate-500 hover:text-slate-700 hover:bg-slate-100 rounded-xl transition-all">
                        Batalkan
                    </button>
                    <button wire:click="save"
                        class="px-8 py-2.5 bg-unmaris-blue text-white rounded-xl text-sm font-bold shadow-lg shadow-unmaris-blue/20 hover:bg-unmaris-blue/90 transition-all">
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </div>
        @endif

        <!-- Data Table Section -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-100">
                            <th class="px-8 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Kode</th>
                            <th class="px-8 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Nama Fakultas</th>
                            <th class="px-8 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Pimpinan / Dekan</th>
                            <th class="px-8 py-4 text-right text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Opsi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($fakultas as $f)
                        <tr class="hover:bg-slate-50/50 transition-colors group">
                            <td class="px-8 py-5">
                                <span class="inline-flex items-center justify-center w-10 h-10 bg-unmaris-blue text-unmaris-yellow font-black text-xs rounded-xl shadow-sm">
                                    {{ $f->kode_fakultas }}
                                </span>
                            </td>
                            <td class="px-8 py-5">
                                <div class="text-sm font-bold text-slate-800">{{ $f->nama_fakultas }}</div>
                                <div class="text-[10px] text-slate-400 uppercase tracking-tighter mt-0.5">Institusi Aktif</div>
                            </td>
                            <td class="px-8 py-5">
                                <div class="flex items-center space-x-3 text-sm text-slate-600 font-medium">
                                    <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center">
                                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                    <span>{{ $f->nama_dekan ?? 'Belum Ditentukan' }}</span>
                                </div>
                            </td>
                            <td class="px-8 py-5 text-right">
                                <div class="flex justify-end space-x-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button wire:click="edit({{ $f->id }})"
                                        class="p-2 text-slate-400 hover:text-unmaris-blue hover:bg-unmaris-blue/5 rounded-lg transition-all"
                                        title="Edit Data">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button wire:click="delete({{ $f->id }})"
                                        wire:confirm="Apakah Anda yakin ingin menghapus fakultas ini?"
                                        class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all"
                                        title="Hapus Data">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-8 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="p-4 bg-slate-50 rounded-full mb-4">
                                        <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                        </svg>
                                    </div>
                                    <p class="text-slate-400 font-medium text-sm">Tidak ada data fakultas ditemukan.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>