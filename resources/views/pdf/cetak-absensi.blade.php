<!DOCTYPE html>
<html>

<head>
    <title>DHMD - {{ $jadwal->mataKuliah->nama_mk }} - {{ $jadwal->nama_kelas }}</title>
    <style>
        @page {
            margin: 1cm 1cm 1cm 1cm;
        }

        body {
            font-family: 'Times New Roman', serif;
            font-size: 11px;
            color: #000;
            line-height: 1.1;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px double #000;
            padding-bottom: 10px;
            position: relative;
        }

        .header img {
            width: 70px;
            position: absolute;
            left: 10px;
            top: 5px;
        }

        .header h1 {
            margin: 0;
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .header h2 {
            margin: 2px 0;
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .header p {
            margin: 0;
            font-size: 11px;
        }

        .doc-title {
            text-align: center;
            font-weight: bold;
            font-size: 12px;
            margin-top: 15px;
            margin-bottom: 10px;
            text-decoration: underline;
            text-transform: uppercase;
        }

        .meta-table {
            width: 100%;
            margin-bottom: 10px;
            border-collapse: collapse;
            font-size: 11px;
        }

        .meta-table td {
            padding: 2px;
            vertical-align: top;
        }

        .meta-label {
            width: 14%;
            font-weight: bold;
        }

        .meta-sep {
            width: 1%;
        }

        .meta-val {
            width: 35%;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }

        .data-table th,
        .data-table td {
            border: 1px solid #000;
            padding: 4px 2px;
            font-size: 9px;
            vertical-align: middle;
        }

        .data-table th {
            background-color: #f0f0f0;
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
            height: 25px;
        }

        .footer {
            margin-top: 20px;
            width: 100%;
            page-break-inside: avoid;
        }

        .note-box {
            float: left;
            width: 55%;
            font-size: 10px;
            border: 1px solid #000;
            padding: 5px;
        }

        .ttd-box {
            float: right;
            width: 30%;
            text-align: center;
        }

        .ttd-space {
            height: 60px;
        }

        .clear {
            clear: both;
        }

        .bold {
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="header">
        <img src="{{ public_path('logo.png') }}" onerror="this.style.display='none'">
        <h1>UNIVERSITAS STELLA MARIS SUMBA</h1>
        <h2>{{ strtoupper($fakultas) }}</h2>
        <p>Alamat: Jl. Karya Kasih No. 5, Tambolaka, Sumba Barat Daya, NTT</p>
        <p>Website: www.unmarissumba.ac.id | Email: info@unmaris.ac.id</p>
    </div>

    <div class="doc-title">DAFTAR HADIR MAHASISWA DAN DOSEN (DHMD)</div>

    <table class="meta-table">
        <tr>
            <td class="meta-label">Program Studi</td>
            <td class="meta-sep">:</td>
            <td class="meta-val">{{ $prodi }}</td>
            <td class="meta-label">Hari / Jam</td>
            <td class="meta-sep">:</td>
            <td class="meta-val">{{ $jadwal->hari }}, {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }}</td>
        </tr>
        <tr>
            <td class="meta-label">Mata Kuliah</td>
            <td class="meta-sep">:</td>
            <td class="meta-val"><strong>{{ $jadwal->mataKuliah->nama_mk }}</strong></td>
            <td class="meta-label">Ruang / Kelas</td>
            <td class="meta-sep">:</td>
            <td class="meta-val">{{ $jadwal->ruang }} / {{ $jadwal->nama_kelas }}</td>
        </tr>
        <tr>
            <td class="meta-label">Dosen Pengampu</td>
            <td class="meta-sep">:</td>
            <td class="meta-val">{{ $dosen }}</td>
            <td class="meta-label">Semester</td>
            <td class="meta-sep">:</td>
            <td class="meta-val">{{ $semester }}</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th rowspan="2" width="3%">No</th>
                <th rowspan="2" width="10%">NIM</th>
                <th rowspan="2">Nama Mahasiswa</th>
                <th rowspan="2" width="3%">L/P</th>
                <th colspan="16">Pertemuan Ke-</th>
            </tr>
            <tr>
                @for($i=1; $i<=16; $i++) <th width="3.5%">{{ $i }}</th> @endfor
            </tr>
        </thead>
        <tbody>
            {{-- [FIX] Gunakan variabel $mahasiswas sesuai pengiriman dari controller --}}
            @foreach($mahasiswas as $index => $mhs)
            <tr>
                <td style="text-align:center">{{ $index + 1 }}</td>
                <td style="text-align:center">{{ $mhs->nim }}</td>
                <td style="padding-left: 5px;">{{ strtoupper($mhs->nama_lengkap) }}</td>
                <td style="text-align:center">{{ $mhs->jenis_kelamin }}</td>
                @for($i=1; $i<=16; $i++) <td>
                    </td> @endfor
            </tr>
            @endforeach

            {{-- Baris Cadangan --}}
            @for($spare=1; $spare<=2; $spare++)
                <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>@for($i=1; $i<=16; $i++) <td>
                    </td> @endfor</tr>
                    @endfor
        </tbody>
    </table>

    <div class="footer">
        <div class="note-box">
            <strong>CATATAN:</strong>
            <ol style="margin-top: 2px; padding-left: 15px;">
                <li>Daftar hadir wajib diisi setiap pertemuan.</li>
                <li>Kehadiran kurang dari 75% tidak diperkenankan mengikuti UAS.</li>
            </ol>
        </div>

        <div class="ttd-box">
            <p>Sumba, {{ date('d F Y') }}</p>
            <p>Dosen Pengampu,</p>
            <div class="ttd-space"></div>
            <p class="bold" style="text-decoration: underline;">{{ $dosen }}</p>
            <p>NIDN. {{ $jadwal->dosen->nidn ?? '....................' }}</p>
        </div>
        <div class="clear"></div>
    </div>
</body>

</html>