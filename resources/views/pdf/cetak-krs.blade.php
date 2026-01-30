<!DOCTYPE html>
<html>
<head>
    <title>KRS - {{ $mahasiswa->nim }}</title>
    <style>
        @page { margin: 0.8cm 1cm; }
        body { font-family: 'Arial', sans-serif; font-size: 9px; color: #000; line-height: 1.3; }
        .header { text-align: center; margin-bottom: 5px; position: relative; }
        .header h1 { font-size: 15px; margin: 0; text-transform: uppercase; font-weight: bold; }
        .header h2 { font-size: 10px; margin: 2px 0; font-weight: bold; }
        .header p { font-size: 8px; margin: 1px 0; }
        .logo { position: absolute; left: 0; top: 0; width: 60px; }
        .line-double { border-bottom: 3px double #000; margin-top: 5px; margin-bottom: 15px; }
        .title-box { text-align: center; margin-bottom: 15px; }
        .title-box h3 { font-size: 13px; text-decoration: underline; margin: 0; font-weight: 900; text-transform: uppercase; }
        .info-table { width: 100%; margin-bottom: 10px; border-collapse: collapse; }
        .info-table td { padding: 2px 0; vertical-align: top; }
        .data-table { width: 100%; border-collapse: collapse; margin-top: 5px; }
        .data-table th, .data-table td { border: 1px solid black; padding: 5px 3px; }
        .data-table th { background-color: #f2f2f2; font-weight: bold; text-transform: uppercase; font-size: 8px; text-align: center; }
        .sig-container { width: 100%; margin-top: 25px; }
        .sig-box { width: 33.3%; float: left; text-align: center; }
        .sig-space { height: 50px; }
        .clear { clear: both; }
        .bold { font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('logo.png') }}" class="logo">
        <h1>UNIVERSITAS STELLA MARIS SUMBA (UNMARIS)</h1>
        <h2>FAKULTAS {{ strtoupper($mahasiswa->prodi->fakultas->nama_fakultas ?? 'AKADEMIK') }}</h2>
        <p>Alamat : Jalan Karya Kasih No. 5 Tambolaka, Kabupaten Sumba Barat Daya, Provinsi NTT.</p>
        <div class="line-double"></div>
    </div>

    <div class="title-box">
        <h3>KARTU RENCANA STUDI (KRS)</h3>
        <p style="font-size: 10px; font-weight: bold;">TAHUN AKADEMIK {{ $ta->nama_tahun }}</p>
    </div>

    <table class="info-table">
        <tr>
            <td width="15%">NIM</td><td width="2%">:</td><td width="33%">{{ $mahasiswa->nim }}</td>
            <td width="15%">Semester</td><td width="2%">:</td><td>
                @php
                    $tahunTa = (int) substr($ta->kode_tahun, 0, 4);
                    $smtTipe = (int) substr($ta->kode_tahun, 4, 1); 
                    $semesterKe = (($tahunTa - (int)$mahasiswa->angkatan_id) * 2) + ($smtTipe >= 2 ? 2 : 1);
                @endphp
                {{ $semesterKe }} ({{ $smtTipe == 1 ? 'Ganjil' : 'Genap' }})
            </td>
        </tr>
        <tr>
            <td>Nama Mahasiswa</td><td>:</td><td class="bold">{{ strtoupper($mahasiswa->nama_lengkap) }}</td>
            <td>Kelas</td><td>:</td><td>{{ $mahasiswa->programKelas->nama_program }}</td>
        </tr>
        <tr>
            <td>Program Studi</td><td>:</td><td>{{ $mahasiswa->prodi->nama_prodi }}</td>
            <td colspan="3"></td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th width="3%">No</th>
                <th width="12%">Kode MK</th>
                <th>Nama Mata Kuliah</th>
                <th width="5%">SKS</th>
                <th width="20%">Dosen Pengampu</th>
                <th width="8%">Ruang</th>
                <th width="8%">Jam</th>
                <th width="8%">Kelas</th>
            </tr>
        </thead>
        <tbody>
            @php $totalSks = 0; @endphp
            @foreach($details as $index => $row)
            @php $totalSks += $row->jadwalKuliah->mataKuliah->sks_default; @endphp
            <tr>
                <td style="text-align:center">{{ $index + 1 }}</td>
                <td style="text-align:center">{{ $row->jadwalKuliah->mataKuliah->kode_mk }}</td>
                <td>{{ $row->jadwalKuliah->mataKuliah->nama_mk }}</td>
                <td style="text-align:center">{{ $row->jadwalKuliah->mataKuliah->sks_default }}</td>
                <td>{{ $row->jadwalKuliah->dosen->nama_lengkap_gelar }}</td>
                <td style="text-align:center">{{ $row->jadwalKuliah->ruang }}</td>
                <td style="text-align:center">{{ \Carbon\Carbon::parse($row->jadwalKuliah->jam_mulai)->format('H:i') }}</td>
                <td style="text-align:center">{{ $row->jadwalKuliah->nama_kelas }}</td>
            </tr>
            @endforeach
            <tr class="bold">
                <td colspan="3" style="text-align: right; padding-right: 10px;">TOTAL SKS DIAMBIL</td>
                <td style="text-align:center">{{ $totalSks }}</td>
                <td colspan="4" style="background-color: #f9f9f9;"></td>
            </tr>
        </tbody>
    </table>

    <div class="sig-container">
        <div class="sig-box">
            <p>Kepala BAAK,</p>
            <div class="sig-space"></div>
            <p class="bold"><u>( {{ $kaBaak->nama ?? '..................................' }} )</u></p>
            <p>NIK. {{ $kaBaak->identitas ?? '.........................' }}</p>
        </div>
        <div class="sig-box">
            <p>Ketua Prodi,</p>
            <div class="sig-space"></div>
            <p class="bold"><u>( {{ $kaProdi->nama ?? '..................................' }} )</u></p>
            <p>NIK. {{ $kaProdi->identitas ?? '.........................' }}</p>
        </div>
        <div class="sig-box">
            <p>Sumba, {{ date('d-m-Y') }}<br>Dosen Wali,</p>
            <div class="sig-space"></div>
            <p class="bold"><u>{{ $mahasiswa->dosenWali->nama_lengkap_gelar ?? '( .................................. )' }}</u></p>
            <p>NIDN. {{ $mahasiswa->dosenWali->nidn ?? '.........................' }}</p>
        </div>
        <div class="clear"></div>
    </div>
</body>
</html>