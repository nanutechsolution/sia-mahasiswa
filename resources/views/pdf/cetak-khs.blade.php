<!DOCTYPE html>
<html>
<head>
    <title>KHS - {{ $mahasiswa->nim }}</title>
    <style>
        @page { margin: 0.8cm 1.2cm; }
        body { font-family: 'Arial', sans-serif; font-size: 10px; color: #000; line-height: 1.4; }
        .gov-header { text-align: center; font-weight: bold; margin-bottom: 5px; text-transform: uppercase; font-size: 9px; letter-spacing: 0.5px; }
        .header { text-align: center; margin-bottom: 5px; position: relative; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header h1 { font-size: 15px; margin: 0; text-transform: uppercase; font-weight: bold; }
        .logo { position: absolute; left: 0; top: -20px; width: 60px; }
        .main-title { text-align: center; font-size: 14px; font-weight: 900; text-decoration: underline; margin: 15px 0; text-transform: uppercase; }
        .info-table { width: 100%; margin-bottom: 15px; border-collapse: collapse; }
        .data-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .data-table th, .data-table td { border: 1px solid black; padding: 6px 4px; }
        .data-table th { background-color: #f2f2f2; font-weight: bold; text-transform: uppercase; font-size: 9px; text-align: center; }
        .ips-row { font-weight: bold; font-size: 12px; margin-top: 10px; padding: 6px 10px; border: 1.5px solid #000; display: inline-block; }
        .sig-right { float: right; width: 250px; text-align: center; margin-top: 20px; }
        .sig-space { height: 60px; }
    </style>
</head>
<body>

    <div class="header">
        <img src="{{ public_path('logo.png') }}" class="logo">
        <h1>UNIVERSITAS STELLA MARIS SUMBA (UNMARIS)</h1>
        <p style="font-size: 8px; margin:0">Alamat : Jalan Karya Kasih No. 5 Tambolaka, Sumba Barat Daya, NTT.</p>
    </div>
    
    <div class="main-title">KARTU HASIL STUDI (KHS)</div>

    <table class="info-table">
        <tr>
            <td width="15%">Nama</td><td width="2%">:</td><td width="33%" style="font-weight:bold uppercase">{{ $mahasiswa->person->nama_lengkap }}</td>
            <td width="15%">NIM</td><td width="2%">:</td><td>{{ $mahasiswa->nim }}</td>
        </tr>
        <tr>
            <td>Prodi</td><td>:</td><td>{{ $mahasiswa->prodi->nama_prodi }}</td>
            <td>Periode</td><td>:</td><td>{{ $ta->kode_tahun }} ({{ $ta->nama_tahun }})</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th width="4%" rowspan="2">No</th>
                <th width="14%" rowspan="2">Kode MK</th>
                <th rowspan="2">Nama Mata Kuliah</th>
                <th width="7%" rowspan="2">SKS</th>
                <th colspan="3">Nilai</th>
                <th width="10%" rowspan="2">Mutu<br>(KxN)</th>
            </tr>
            <tr>
                <th width="8%">Angka</th>
                <th width="6%">Huruf</th>
                <th width="8%">Indeks</th>
            </tr>
        </thead>
        <tbody>
            @foreach($details as $index => $row)
            <tr>
                <td style="text-align:center">{{ $index + 1 }}</td>
                <td style="text-align:center; font-weight:bold">{{ $row->kode_mk }}</td>
                <td>{{ $row->nama_mk }}</td>
                <td style="text-align:center">{{ $row->sks_default }}</td>
                <td style="text-align:center">{{ number_format($row->nilai_angka, 2) }}</td>
                <td style="text-align:center; font-weight:bold">{{ $row->nilai_huruf }}</td>
                <td style="text-align:center">{{ number_format($row->nilai_indeks, 2) }}</td>
                <td style="text-align:center; font-weight:bold">{{ number_format($row->sks_default * $row->nilai_indeks, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="font-weight:bold; background-color: #f9f9f9;">
                <td colspan="3" style="text-align:center">JUMLAH TOTAL</td>
                <td style="text-align:center">{{ $totalSks }}</td>
                <td colspan="3"></td>
                <td style="text-align:center">{{ number_format($totalMutu, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="ips-row">
        IPS ( INDEKS PRESTASI SEMESTER ) : {{ number_format($ips, 2) }}
    </div>

    <div class="sig-right">
        Tambolaka, {{ date('d F Y') }}<br>
        Ketua Program Studi,<br>
        <div class="sig-space"></div>
        <p style="font-weight:bold; text-decoration:underline;">
            ( {{ $kaProdi->nama ?? '..................................' }} )
        </p>
        <p>NIDN/NUPTK. {{ $kaProdi->identitas ?? '.........................' }}</p>
    </div>
</body>
</html>