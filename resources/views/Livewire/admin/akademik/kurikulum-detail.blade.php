<div class="space-y-6">
    <div class="flex items-center justify-between">
        <button wire:click="backToList" class="flex items-center text-sm text-gray-500 hover:text-gray-700">
            <svg class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M17 10a.75.75 0 01-.75.75H5.612l4.158 3.96a.75.75 0 11-1.04 1.08l-5.5-5.25a.75.75 0 010-1.08l5.5-5.25a.75.75 0 111.04 1.08L5.612 9.25H16.25A.75.75 0 0117 10z" clip-rule="evenodd" />
            </svg>
            Kembali ke Daftar
        </button>
        <div class="text-right">
            <h2 class="text-xl font-bold text-gray-900">{{ $selectedKurikulum->nama_kurikulum }}</h2>
            <p class="text-sm text-gray-500">{{ $selectedKurikulum->prodi->nama_prodi }}</p>
        </div>
    </div>

    <div class="bg-indigo-50 p-4 rounded-lg border border-indigo-100">
        <h4 class="text-sm font-bold text-indigo-800 mb-3 uppercase tracking-wider">Tambah Mata Kuliah</h4>

        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-start">
            <div class="md:col-span-4">
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Pilih Mata Kuliah *</label>
                <select wire:model.live="mk_id_to_add" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 sm:text-sm">
                    <option value="">-- Pilih MK --</option>
                    @foreach($availableMks as $mk)
                    <option value="{{ $mk->id }}">{{ $mk->kode_mk }} - {{ $mk->nama_mk }}</option>
                    @endforeach
                </select>
                @error('mk_id_to_add') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
            </div>

            <div class="md:col-span-2">
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Semester *</label>
                <input type="number" wire:model.live="semester_paket_to_add" min="1" max="8" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 sm:text-sm">
            </div>

            <div class="md:col-span-2">
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Sifat *</label>
                <select wire:model="sifat_mk_to_add" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 sm:text-sm">
                    <option value="W">Wajib</option>
                    <option value="P">Pilihan</option>
                </select>
            </div>

            <div class="md:col-span-4 bg-white p-3 rounded border border-indigo-200">
                <label class="block text-[10px] font-black text-gray-500 uppercase mb-2">Bobot SKS (Fallback ke Master jika 0)</label>
                <div class="grid grid-cols-3 gap-2">
                    <input type="number" wire:model="sks_tatap_muka_to_add" placeholder="T" class="block w-full rounded border-gray-300 text-xs">
                    <input type="number" wire:model="sks_praktek_to_add" placeholder="P" class="block w-full rounded border-gray-300 text-xs">
                    <input type="number" wire:model="sks_lapangan_to_add" placeholder="L" class="block w-full rounded border-gray-300 text-xs">
                </div>
            </div>

            <!-- INPUT PRASYARAT & NILAI MINIMAL -->
            <div class="md:col-span-6 flex gap-4">
                <div class="flex-1">
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Mata Kuliah Syarat (Lulus)</label>
                    <select wire:model="prasyarat_mk_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 sm:text-sm">
                        <option value="">-- Tanpa Syarat --</option>
                        @foreach($prerequisiteOptions as $pre)
                        <option value="{{ $pre->id }}">{{ $pre->nama_mk }} (Smt {{ $pre->pivot->semester_paket }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-32">
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nilai Min.</label>
                    <select wire:model="min_nilai_prasyarat_to_add" class="block w-full rounded-xl border-slate-300 text-xs font-bold text-center">
                        @foreach($availableGrades as $grade)
                        <option value="{{ $grade->huruf }}">{{ $grade->huruf }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="md:col-span-6 pt-5 text-right">
                <button wire:click="addMk" class="bg-indigo-600 text-white px-6 py-2 rounded-md text-sm font-bold hover:bg-indigo-700 shadow-md">
                    + Simpan MK ke Struktur
                </button>
            </div>
        </div>
    </div>

    <div class="bg-white shadow sm:rounded-lg overflow-hidden border border-gray-200">
        <table class="min-w-full divide-y divide-gray-300">
            <thead class="bg-gray-50">
                <tr>
                    <th class="py-3 pl-4 pr-3 text-left text-xs font-bold text-gray-500 uppercase">Smt</th>
                    <th class="px-3 py-3 text-left text-xs font-bold text-gray-500 uppercase">Kode</th>
                    <th class="px-3 py-3 text-left text-xs font-bold text-gray-500 uppercase">Mata Kuliah</th>
                    <th class="px-3 py-3 text-center text-xs font-bold text-gray-500 uppercase">SKS</th>
                    <th class="px-3 py-3 text-center text-xs font-bold text-gray-500 uppercase">Prasyarat</th>
                    <th class="relative py-3 pl-3 pr-4"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white text-sm">
                @foreach($selectedKurikulum->mataKuliahs as $mk)
                <tr class="hover:bg-gray-50 transition">
                    <td class="whitespace-nowrap py-4 pl-4 pr-3 font-black text-indigo-600">{{ $mk->pivot->semester_paket }}</td>
                    <td class="whitespace-nowrap px-3 py-4 font-mono text-gray-500">{{ $mk->kode_mk }}</td>
                    <td class="px-3 py-4">
                        <div class="font-bold text-gray-900">{{ $mk->nama_mk }}</div>
                        <div class="text-[10px] text-gray-400 uppercase font-bold">{{ $mk->pivot->sifat_mk == 'W' ? 'Wajib' : 'Pilihan' }}</div>
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-center">
                        <span class="font-black">{{ $mk->pivot->sks_tatap_muka + $mk->pivot->sks_praktek + $mk->pivot->sks_lapangan }}</span>
                        <div class="text-[9px] text-gray-400">({{ $mk->pivot->sks_tatap_muka }}/{{ $mk->pivot->sks_praktek }}/{{ $mk->pivot->sks_lapangan }})</div>
                    </td>
                    <td class="px-3 py-4 text-xs">
                        @if($mk->pivot->prasyarat_mk_id)
                        @php $pre = $selectedKurikulum->mataKuliahs->firstWhere('id', $mk->pivot->prasyarat_mk_id); @endphp
                        @if($pre)
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-rose-50 text-rose-700 border border-rose-100 font-bold">
                            Syarat: {{ $pre->kode_mk }} (Min: {{ $mk->pivot->min_nilai_prasyarat }})
                        </span>
                        @endif
                        @else
                        <span class="text-gray-300">-</span>
                        @endif
                    </td>
                    <td class="whitespace-nowrap py-4 pl-3 pr-4 text-right">
                        <button wire:click="removeMk({{ $mk->id }})" wire:confirm="Hapus MK dari kurikulum?" class="text-red-600 hover:text-red-900 font-bold">Hapus</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>