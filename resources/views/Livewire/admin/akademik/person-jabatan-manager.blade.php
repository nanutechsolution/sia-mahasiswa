<div>
    <h2 class="text-xl font-bold mb-2">
        Riwayat Jabatan: {{ $person->nama_lengkap }}
    </h2>

    <form wire:submit.prevent="assign" class="grid grid-cols-4 gap-3 mb-6">
        <select wire:model="jabatan_id" class="border p-2">
            <option value="">-- Pilih Jabatan --</option>
            @foreach($jabatan as $j)
                <option value="{{ $j->id }}">{{ $j->nama_jabatan }}</option>
            @endforeach
        </select>

        <input type="date" wire:model="tanggal_mulai" class="border p-2">

        <button class="bg-green-600 text-white px-4 py-2 col-span-2">
            Tetapkan Jabatan
        </button>
    </form>

    <table class="w-full border">
        <thead>
            <tr class="bg-gray-100">
                <th>Jabatan</th>
                <th>Mulai</th>
                <th>Selesai</th>
            </tr>
        </thead>
        <tbody>
            @foreach($riwayat as $r)
                <tr class="border-t">
                    <td>{{ $r->jabatan->nama_jabatan }}</td>
                    <td>{{ $r->tanggal_mulai }}</td>
                    <td>{{ $r->tanggal_selesai ?? 'Aktif' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
