<div class="space-y-6">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Pengaturan Skala Nilai</h1>
            <p class="mt-2 text-sm text-gray-700">Atur bobot indeks, rentang nilai angka, dan status kelulusan prasyarat.</p>
        </div>
        <div class="mt-4 sm:mt-0">
            @if(!$showForm)
                <button wire:click="create" class="inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-bold text-white shadow-sm hover:bg-indigo-700 transition-all">
                    + Tambah Skala Baru
                </button>
            @endif
        </div>
    </div>

    @if (session()->has('success'))
        <div class="bg-green-50 p-4 rounded-md border border-green-200 text-sm text-green-700 font-bold flex items-center animate-in fade-in">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    <!-- Form Input (Tampil jika showForm = true) -->
    @if($showForm)
    <div class="bg-white p-6 shadow-lg rounded-xl border border-indigo-100 animate-in slide-in-from-top-4 duration-300">
        <h3 class="text-lg font-bold text-slate-800 mb-6 border-b pb-2">
            {{ $selectedId ? 'Edit Skala Nilai' : 'Tambah Skala Nilai Baru' }}
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Huruf (A-E)</label>
                <input type="text" wire:model="huruf" placeholder="Contoh: A" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 text-sm uppercase font-bold">
                @error('huruf') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>
            
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Bobot Indeks (0-4)</label>
                <input type="number" step="0.01" wire:model="bobot_indeks" placeholder="4.00" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 text-sm">
                @error('bobot_indeks') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nilai Angka Min</label>
                <input type="number" step="0.01" wire:model="nilai_min" placeholder="80.00" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 text-sm">
                @error('nilai_min') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nilai Angka Max</label>
                <input type="number" step="0.01" wire:model="nilai_max" placeholder="100.00" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 text-sm">
                @error('nilai_max') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="flex items-center pt-5">
                <label class="inline-flex items-center cursor-pointer">
                    <input type="checkbox" wire:model="is_lulus" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 h-5 w-5">
                    <span class="ml-2 text-sm font-bold text-gray-700">Status Lulus</span>
                </label>
            </div>
        </div>

        <div class="mt-8 flex justify-end gap-3 border-t pt-4">
            <button wire:click="$set('showForm', false)" class="px-6 py-2 text-sm font-bold text-gray-500 hover:text-gray-700 transition-all">
                Batal
            </button>
            <button wire:click="save" class="px-8 py-2 bg-indigo-600 text-white rounded-lg text-sm font-bold shadow-lg hover:bg-indigo-700 transition-all">
                Simpan Skala
            </button>
        </div>
    </div>
    @endif

    <!-- Tabel Data -->
    <div class="bg-white shadow-sm rounded-xl border border-slate-200 overflow-hidden">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-widest">Huruf</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-widest">Bobot Indeks</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-widest">Rentang Nilai</th>
                    <th class="px-6 py-4 text-center text-xs font-bold text-slate-400 uppercase tracking-widest">Status Kelulusan</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-slate-400 uppercase tracking-widest">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-slate-100">
                @forelse($skala as $item)
                <tr class="hover:bg-slate-50 transition-colors group">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-xl font-black text-slate-800">{{ $item->huruf }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-indigo-600">
                        {{ number_format($item->bobot_indeks, 2) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                        {{ number_format($item->nilai_min, 2) }} &mdash; {{ number_format($item->nilai_max, 2) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        @if($item->is_lulus)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide bg-green-100 text-green-700 border border-green-200">
                                Lulus
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide bg-red-100 text-red-700 border border-red-200">
                                Tidak Lulus
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <button wire:click="edit({{ $item->id }})" class="text-indigo-600 hover:text-indigo-900 font-bold px-3 py-1 bg-indigo-50 rounded-lg transition-colors">
                            Edit
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-10 text-center text-slate-500 italic">
                        Belum ada data skala nilai. Gunakan seeder atau tambah manual.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>