<!DOCTYPE html>
<html>
<head>
    <title>Transkrip - {{ $mahasiswa->nim }}</title>
    <style>
        @page { margin: 1cm 1.5cm; }
        body { font-family: 'Arial', sans-serif; font-size: 10px; color: #000; line-height: 1.4; }
        .header { text-align: center; margin-bottom: 10px; position: relative; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header h1 { font-size: 16px; margin: 0; text-transform: uppercase; font-weight: bold; }
        .logo { position: absolute; left: 0; top: -15px; width: 65px; }
        .main-title { text-align: center; font-size: 15px; font-weight: 900; text-decoration: underline; margin: 20px 0; text-transform: uppercase; }
        .info-table { width: 100%; margin-bottom: 20px; border-collapse: collapse; }
        .data-table { width: 100%; border-collapse: collapse; }
        .data-table th, .data-table td { border: 1px solid black; padding: 5px; }
        .data-table th { background-color: #f2f2f2; text-transform: uppercase; font-size: 9px; }
        .summary-table { width: 40%; margin-top: 15px; font-weight: bold; border-collapse: collapse; }
        .summary-table td { padding: 4px; border: 1px solid black; }
        .sig-container { margin-top: 30px; width: 100%; }
        .sig-right { float: right; width: 250px; text-align: center; }
        .sig-space { height: 70px; }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('logo.png') }}" class="logo">
        <h1>UNIVERSITAS STELLA MARIS SUMBA (UNMARIS)</h1>
        <p style="font-size: 9px; margin:2px 0;">Situs Resmi: www.unmarissumba.ac.id | Email: info@unmarissumba.ac.id</p>
    </div>
    
    <div class="main-title">TRANSKRIP NILAI SEMENTARA</div>

    <table class="info-table">
        <tr>
            <td width="20%">Nama Mahasiswa</td><td width="2%">:</td><td width="30%" style="font-weight:bold">{{ strtoupper($mahasiswa->person->nama_lengkap) }}</td>
            <td width="20%">NIM</td><td width="2%">:</td><td>{{ $mahasiswa->nim }}</td>
        </tr>
        <tr>
            <td>Program Studi</td><td>:</td><td>{{ $mahasiswa->prodi->nama_prodi }}</td>
            <td>Fakultas</td><td>:</td><td>{{ $mahasiswa->prodi->fakultas->nama_fakultas ?? '-' }}</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">Kode MK</th>
                <th>Mata Kuliah</th>
                <th width="8%">SKS</th>
                <th width="8%">Huruf</th>
                <th width="8%">Indeks</th>
                <th width="10%">Mutu</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transkrip as $index => $mk)
            <tr>
                <td style="text-align:center">{{ $index + 1 }}</td>
                <td style="text-align:center">{{ $mk->kode_mk }}</td>
                <td>{{ $mk->nama_mk }}</td>
                <td style="text-align:center">{{ $mk->sks_default }}</td>
                <td style="text-align:center; font-weight:bold">{{ $mk->nilai_huruf }}</td>
                <td style="text-align:center">{{ number_format($mk->nilai_indeks, 2) }}</td>
                <td style="text-align:center">{{ number_format($mk->sks_default * $mk->nilai_indeks, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table class="summary-table">
        <tr>
            <td>Total Kredit (SKS)</td>
            <td style="text-align:center">{{ $totalSks }}</td>
        </tr>
        <tr>
            <td>Indeks Prestasi Kumulatif (IPK)</td>
            <td style="text-align:center">{{ number_format($ipk, 2) }}</td>
        </tr>
    </table>

    <div class="sig-container">
        <div class="sig-right">
            Tambolaka, {{ date('d F Y') }}<br>
            Ketua Program Studi,<br>
            <div class="sig-space"></div>
            <p style="font-weight:bold; text-decoration:underline;">( {{ $kaProdi->nama ?? '..................................' }} )</p>
            <p>NIK/NIP. {{ $kaProdi->identitas ?? '.........................' }}</p>
        </div>
        <div style="clear: both;"></div>
    </div>
</body>
</html>