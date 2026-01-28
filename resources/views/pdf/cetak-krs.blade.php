<!DOCTYPE html>
<html>
<head>
    <title>KRS Mahasiswa</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid black; padding-bottom: 10px; }
        .info-table { width: 100%; margin-bottom: 20px; }
        .data-table { width: 100%; border-collapse: collapse; }
        .data-table th, .data-table td { border: 1px solid black; padding: 5px; }
        .data-table th { background-color: #f0f0f0; }
        .ttd { margin-top: 40px; float: right; text-align: center; width: 200px; }
    </style>
</head>
<body>
    <div class="header">
        <h2 style="margin:0">UNIVERSITAS MARITIM SISTEM (UNMARIS)</h2>
        <p style="margin:0">Jl. Teknologi No. 1, Cloud Server, Indonesia</p>
        <h3>KARTU RENCANA STUDI (KRS)</h3>
    </div>

    <table class="info-table">
        <tr>
            <td width="15%">Nama</td><td>: {{ $mahasiswa->nama_lengkap }}</td>
            <td width="15%">Semester</td><td>: {{ $krs->tahunAkademik->nama_tahun }}</td>
        </tr>
        <tr>
            <td>NIM</td><td>: {{ $mahasiswa->nim }}</td>
            <td>Program</td><td>: {{ $mahasiswa->programKelas->nama_program }}</td>
        </tr>
        <tr>
            <td>Prodi</td><td>: {{ $mahasiswa->prodi->nama_prodi }}</td>
            <td>Status</td><td>: <b>{{ $krs->status_krs }}</b></td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th>Kode MK</th>
                <th>Mata Kuliah</th>
                <th>SKS</th>
                <th>Dosen Pengampu</th>
                <th>Jadwal</th>
            </tr>
        </thead>
        <tbody>
            @php $totalSks = 0; @endphp
            @foreach($details as $index => $det)
            <tr>
                <td style="text-align:center">{{ $index + 1 }}</td>
                <td>{{ $det->jadwalKuliah->mataKuliah->kode_mk }}</td>
                <td>{{ $det->jadwalKuliah->mataKuliah->nama_mk }}</td>
                <td style="text-align:center">{{ $det->jadwalKuliah->mataKuliah->sks_default }}</td>
                <td>{{ optional($det->jadwalKuliah->dosen)->nama_lengkap_gelar }}</td>
                <td>{{ $det->jadwalKuliah->hari }}, {{ \Carbon\Carbon::parse($det->jadwalKuliah->jam_mulai)->format('H:i') }}</td>
            </tr>
            @php $totalSks += $det->jadwalKuliah->mataKuliah->sks_default; @endphp
            @endforeach
            <tr>
                <td colspan="3" style="text-align:right; font-weight:bold">Total SKS</td>
                <td style="text-align:center; font-weight:bold">{{ $totalSks }}</td>
                <td colspan="2"></td>
            </tr>
        </tbody>
    </table>

    <div class="ttd">
        <p>Mengetahui,<br>Dosen Wali</p>
        <br><br><br>
        <p>( ................................. )</p>
    </div>
</body>
</html>