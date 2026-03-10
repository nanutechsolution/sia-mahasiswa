<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Transkrip - {{ $mahasiswa->nim }}</title>
    <style>
        @page { margin: 1.2cm 1.5cm; }
        body { font-family: 'Times New Roman', Times, serif; font-size: 11px; color: #000; line-height: 1.4; }
        
        .header { text-align: center; margin-bottom: 15px; position: relative; border-bottom: 3px double #000; padding-bottom: 10px; }
        .header h1 { font-size: 18px; margin: 0; text-transform: uppercase; font-weight: bold; color: #002855; }
        .header p { margin: 2px 0; font-size: 9px; font-weight: bold; }
        
        .main-title { text-align: center; font-size: 14px; font-weight: bold; margin: 20px 0; text-transform: uppercase; text-decoration: underline; }
        
        .info-table { width: 100%; margin-bottom: 20px; border-collapse: collapse; }
        .info-table td { padding: 2px 0; vertical-align: top; }
        .info-label { font-weight: bold; width: 120px; }
        .info-sep { width: 15px; text-align: center; }

        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .data-table th, .data-table td { border: 1px solid #000; padding: 5px 4px; }
        .data-table th { background-color: #f2f2f2; text-transform: uppercase; font-size: 9px; font-weight: bold; }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .bold { font-weight: bold; }

        .summary-box { width: 280px; margin-top: 15px; border: 1px solid #000; }
        .summary-box table { width: 100%; border-collapse: collapse; }
        .summary-box td { padding: 5px; border: 1px solid #000; }

        .sig-container { margin-top: 40px; width: 100%; page-break-inside: avoid; }
        .sig-right { float: right; width: 250px; text-align: center; }
        .sig-space { height: 75px; }
        .clear { clear: both; }
    </style>
</head>
<body>
    <div class="header">
        <h1>UNIVERSITAS STELLA MARIS SUMBA</h1>
        <p>Jl. Karya Kasih No. 5, Tambolaka, Sumba Barat Daya, NTT</p>
        <p>Situs Resmi: www.unmarissumba.ac.id | Email: info@unmarissumba.ac.id</p>
    </div>
    
    <div class="main-title">TRANSKRIP NILAI SEMENTARA</div>

    <table class="info-table">
        <tr>
            <td class="info-label">Nama Mahasiswa</td><td class="info-sep">:</td>
            <td width="280"><span class="bold">{{ strtoupper($mahasiswa->person->nama_lengkap) }}</span></td>
            <td class="info-label">NIM</td><td class="info-sep">:</td>
            <td><code>{{ $mahasiswa->nim }}</code></td>
        </tr>
        <tr>
            <td class="info-label">Program Studi</td><td class="info-sep">:</td>
            <td>{{ $mahasiswa->prodi->jenjang }} - {{ $mahasiswa->prodi->nama_prodi }}</td>
            <td class="info-label">Fakultas</td><td class="info-sep">:</td>
            <td>{{ $mahasiswa->prodi->fakultas->nama_fakultas ?? '-' }}</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th width="30">No</th>
                <th width="80">Kode MK</th>
                <th>Mata Kuliah</th>
                <th width="40">SKS</th>
                <th width="40">Nilai</th>
                <th width="40">Bobot</th>
                <th width="50">Mutu</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transkrip as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center">{{ $item->mataKuliah->kode_mk ?? '-' }}</td>
                <td class="text-left">{{ $item->mataKuliah->nama_mk ?? '-' }}</td>
                <td class="text-center">{{ $item->sks_diakui }}</td>
                <td class="text-center bold">{{ $item->nilai_huruf_final }}</td>
                <td class="text-center">{{ number_format($item->nilai_indeks_final, 2) }}</td>
                <td class="text-center">{{ number_format($item->sks_diakui * $item->nilai_indeks_final, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary-box">
        <table>
            <tr>
                <td class="bold">Total Kredit Diakui (SKS)</td>
                <td class="text-center bold" width="80">{{ $totalSks }}</td>
            </tr>
            <tr>
                <td class="bold">Indeks Prestasi Kumulatif (IPK)</td>
                <td class="text-center bold" width="80">{{ number_format($ipk, 2) }}</td>
            </tr>
        </table>
    </div>

    <div class="sig-container">
        <div class="sig-right">
            <p>Tambolaka, {{ now()->isoFormat('D MMMM Y') }}</p>
            <p>Ketua Program Studi,</p>
            <div class="sig-space"></div>
            <p><span class="bold" style="text-decoration:underline;">{{ $kaProdi->nama ?? '..................................' }}</span></p>
            <p>{{ $kaProdi->identitas ?? 'NIDN. .........................' }}</p>
        </div>
        <div class="clear"></div>
    </div>

    <div style="position: fixed; bottom: 0; width: 100%; font-size: 8px; color: #999; text-align: left; border-top: 1px solid #eee; padding-top: 5px;">
        Dokumen ini adalah transkrip nilai sementara yang diterbitkan secara elektronik melalui SIAKAD UNMARIS v4.2.
    </div>
</body>
</html>