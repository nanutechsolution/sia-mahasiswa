<div>
    <h2 class="text-xl font-bold mb-4">Master Jabatan</h2>

    <form wire:submit.prevent="save" class="grid grid-cols-3 gap-3 mb-6">
        <input wire:model="kode_jabatan" placeholder="Kode (DEKAN)" class="border p-2">
        <input wire:model="nama_jabatan" placeholder="Nama Jabatan" class="border p-2">

        <select wire:model="jenis" class="border p-2">
            <option value="STRUKTURAL">Struktural</option>
            <option value="FUNGSIONAL">Fungsional</option>
        </select>

        <button class="bg-blue-600 text-white px-4 py-2 col-span-3">
            Simpan
        </button>
    </form>

    <table class="w-full border">
        <thead>
            <tr class="bg-gray-100">
                <th>Kode</th>
                <th>Nama</th>
                <th>Jenis</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($list as $j)
                <tr class="border-t">
                    <td>{{ $j->kode_jabatan }}</td>
                    <td>{{ $j->nama_jabatan }}</td>
                    <td>{{ $j->jenis }}</td>
                    <td>{{ $j->is_active ? 'Aktif' : 'Nonaktif' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
