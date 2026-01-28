<!DOCTYPE html>
<html>
<head>
    <title>Transkrip Nilai Akademik</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid black; padding-bottom: 10px; }
        .info-table { width: 100%; margin-bottom: 15px; font-weight: bold; }
        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .data-table th, .data-table td { border: 1px solid black; padding: 4px 6px; }
        .data-table th { background-color: #f0f0f0; text-align: center; }
        .summary { float: right; width: 300px; border: 1px solid black; padding: 10px; margin-bottom: 20px; }
        .ttd { margin-top: 50px; float: right; text-align: center; width: 200px; }
    </style>
</head>
<body>
    <div class="header">
        <h2 style="margin:0">UNIVERSITAS MARITIM SISTEM (UNMARIS)</h2>
        <p style="margin:0">Jl. Teknologi No. 1, Cloud Server, Indonesia</p>
        <h3>TRANSKRIP NILAI AKADEMIK SEMENTARA</h3>
    </div>

    <table class="info-table">
        <tr>
            <td width="15%">Nama</td><td>: {{ $mahasiswa->nama_lengkap }}</td>
            <td width="15%">Prodi</td><td>: {{ $mahasiswa->prodi->nama_prodi }}</td>
        </tr>
        <tr>
            <td>NIM</td><td>: {{ $mahasiswa->nim }}</td>
            <td>Jenjang</td><td>: {{ $mahasiswa->prodi->jenjang }}</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th>Kode MK</th>
                <th>Mata Kuliah</th>
                <th width="5%">SKS</th>
                <th width="5%">Huruf</th>
                <th width="5%">Bobot</th>
                <th width="10%">Mutu (KxN)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transkrip as $index => $mk)
            <tr>
                <td style="text-align:center">{{ $index + 1 }}</td>
                <td>{{ $mk->kode_mk }}</td>
                <td>{{ $mk->nama_mk }}</td>
                <td style="text-align:center">{{ $mk->sks_default }}</td>
                <td style="text-align:center">{{ $mk->nilai_huruf }}</td>
                <td style="text-align:center">{{ number_format($mk->nilai_indeks, 2) }}</td>
                <td style="text-align:center">{{ number_format($mk->sks_default * $mk->nilai_indeks, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary">
        <table width="100%">
            <tr>
                <td>Total SKS Lulus</td>
                <td style="text-align:right"><b>{{ $totalSks }}</b></td>
            </tr>
            <tr>
                <td>Indeks Prestasi Kumulatif (IPK)</td>
                <td style="text-align:right"><b>{{ number_format($ipk, 2) }}</b></td>
            </tr>
        </table>
    </div>

    <div class="ttd">
        <p>Sumba, {{ date('d F Y') }}<br>Ketua Program Studi</p>
        <br><br><br>
        <p>__________________________</p>
    </div>
</body>
</html>