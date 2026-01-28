<!DOCTYPE html>
<html>

<head>
    <title>Daftar Hadir Kuliah</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 11px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid black;
            padding-bottom: 10px;
        }

        .info {
            width: 100%;
            margin-bottom: 15px;
            font-weight: bold;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .table th,
        .table td {
            border: 1px solid black;
            padding: 5px;
        }

        .table th {
            background-color: #f0f0f0;
            text-align: center;
        }

        .ttd-box {
            width: 100%;
            margin-top: 30px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2 style="margin:0">UNIVERSITAS MARITIM SISTEM (UNMARIS)</h2>
        <p style="margin:0">DAFTAR HADIR PERKULIAHAN & JURNAL KELAS</p>
        <p style="margin:0">Semester {{ $jadwal->tahunAkademik->nama_tahun }}</p>
    </div>

    <table class="info">
        <tr>
            <td width="15%">Mata Kuliah</td>
            <td>: {{ $jadwal->mataKuliah->nama_mk }} ({{ $jadwal->mataKuliah->kode_mk }})</td>
            <td width="15%">Kelas</td>
            <td>: {{ $jadwal->nama_kelas }}</td>
        </tr>
        <tr>
            <td>Dosen</td>
            <td>: {{ $jadwal->dosen->nama_lengkap_gelar }}</td>
            <td>Ruang/Hari</td>
            <td>: {{ $jadwal->ruang }} / {{ $jadwal->hari }}</td>
        </tr>
    </table>

    <table class="table">
        <thead>
            <tr>
                <th width="5%" rowspan="2">No</th>
                <th width="15%" rowspan="2">NIM</th>
                <th rowspan="2">Nama Mahasiswa</th>
                <th colspan="16">Pertemuan Ke- (Tanggal & Paraf)</th>
            </tr>
            <tr>
                <!-- Buat 16 Kolom Pertemuan -->
                @for($i=1; $i<=16; $i++)
                    <th width="3%">{{ $i }}</th>
                    @endfor
            </tr>
        </thead>
        <tbody>
            @foreach($peserta as $index => $row)
            <tr>
                <td style="text-align:center">{{ $index + 1 }}</td>
                <td>{{ $row->krs->mahasiswa->nim }}</td>
                <td>{{ $row->krs->mahasiswa->nama_lengkap }}</td>
                <!-- Kotak Kosong untuk Paraf -->
                @for($i=1; $i<=16; $i++)
                    <td>
                    </td>
                    @endfor
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="ttd-box">
        <table width="100%">
            <tr>
                <td width="70%"></td>
                <td align="center">
                    <p>Mengetahui,<br>Ketua Program Studi</p>
                    <br><br><br>
                    <p>( ................................. )</p>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>