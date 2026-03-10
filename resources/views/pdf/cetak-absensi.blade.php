<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>DHMD - {{ $jadwal->mataKuliah->nama_mk }} - {{ $jadwal->nama_kelas }}</title>
    <style>
        @page {
            margin: 1cm;
            size: a4 landscape;
        }

        body {
            font-family: 'Times New Roman', serif;
            font-size: 10px;
            color: #000;
            line-height: 1.2;
        }

        /* HEADER / KOP SURAT */
        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 3px double #000;
            padding-bottom: 8px;
            position: relative;
        }

        .header h1 {
            margin: 0;
            font-size: 16px;
            text-transform: uppercase;
        }

        .header h2 {
            margin: 2px 0;
            font-size: 12px;
            text-transform: uppercase;
        }

        .header p {
            margin: 0;
            font-size: 9px;
            font-style: italic;
        }

        .doc-title {
            text-align: center;
            font-weight: bold;
            font-size: 12px;
            margin: 10px 0;
            text-decoration: underline;
            text-transform: uppercase;
        }

        /* META DATA KELAS */
        .meta-table {
            width: 100%;
            margin-bottom: 10px;
            border-collapse: collapse;
        }

        .meta-table td {
            padding: 1px 3px;
            vertical-align: top;
        }

        .label {
            font-weight: bold;
            width: 100px;
        }

        .sep {
            width: 10px;
            text-align: center;
        }

        /* TABEL ABSENSI (MATRIX) */
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table th,
        .data-table td {
            border: 1px solid #000;
            padding: 4px 2px;
            text-align: center;
        }

        .data-table th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 8px;
        }

        .text-left {
            text-align: left !important;
            padding-left: 5px !important;
        }

        .bold {
            font-weight: bold;
        }

        /* FOOTER & TANDA TANGAN */
        .footer-container {
            margin-top: 20px;
            width: 100%;
        }

        .note-box {
            float: left;
            width: 45%;
            font-size: 9px;
            border: 1px solid #ccc;
            padding: 8px;
            border-radius: 5px;
        }

        .ttd-box {
            float: right;
            width: 250px;
            text-align: center;
        }

        .ttd-space {
            height: 50px;
        }

        .clear {
            clear: both;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>UNIVERSITAS MARITIM (UNMARIS)</h1>
        <h2>{{ strtoupper($fakultas) }}</h2>
        <p>Alamat: Gedung Rektorat Lt. 2, Jl. Bahari No. 123, Kota Kupang, NTT</p>
        <p>Website: www.unmaris.ac.id | Email: akademik@unmaris.ac.id</p>
    </div>

    <div class="doc-title">DAFTAR HADIR MAHASISWA DAN DOSEN (DHMD)</div>

    <table class="meta-table">
        <tr>
            <td class="label">Program Studi</td>
            <td class="sep">:</td>
            <td width="35%">{{ $prodi }}</td>
            <td class="label">Mata Kuliah</td>
            <td class="sep">:</td>
            <td><strong>{{ $jadwal->mataKuliah->nama_mk }}</strong></td>
        </tr>
        <tr>
            <td class="label">Tahun Akademik</td>
            <td class="sep">:</td>
            <td>{{ $semester }}</td>
            <td class="label">SKS / Kode MK</td>
            <td class="sep">:</td>
            <td>{{ $jadwal->mataKuliah->sks_default }} SKS / {{ $jadwal->mataKuliah->kode_mk }}</td>
        </tr>
        <tr>
            <td class="label">Dosen Pengampu</td>
            <td class="sep">:</td>
            <td>
                {{-- Mendukung Team Teaching --}}
                @foreach($jadwal->dosens as $d)
                {{ $d->person->nama_lengkap }}{{ !$loop->last ? ', ' : '' }}
                @endforeach
            </td>
            <td class="label">Hari / Jam</td>
            <td class="sep">:</td>
            <td>{{ $jadwal->hari }}, {{ substr($jadwal->jam_mulai, 0, 5) }} - {{ substr($jadwal->jam_selesai, 0, 5) }}</td>
        </tr>
        <tr>
            <td class="label">Ruang / Kelas</td>
            <td class="sep">:</td>
            <td>{{ $jadwal->ruang->kode_ruang ?? 'TBA' }} / {{ $jadwal->nama_kelas }}</td>
            <td class="label">Jumlah Mhs</td>
            <td class="sep">:</td>
            <td>{{ $jumlah_mhs }} Orang</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th rowspan="2" width="25">No</th>
                <th rowspan="2" width="80">NIM</th>
                <th rowspan="2">Nama Mahasiswa</th>
                <th rowspan="2" width="25">L/P</th>
                <th colspan="16">Pertemuan Ke- (Tanda Tangan Mahasiswa)</th>
            </tr>
            <tr>
                @for($i=1; $i<=16; $i++)
                    <th width="30">{{ $i }}</th>
                    @endfor
            </tr>
        </thead>
        <tbody>
            @foreach($mahasiswas as $index => $mhs)
            <tr>
                <td style="text-align:center">{{ $index + 1 }}</td>
                <td style="text-align:center">{{ $mhs->nim }}</td>
                <td style="padding-left: 5px;">{{ strtoupper($mhs->nama_lengkap) }}</td>
                <td style="text-align:center">{{ $mhs->jenis_kelamin }}</td>

                {{-- PERBAIKAN DI SINI: Cetak isi array kehadiran --}}
                @foreach($mhs->kehadiran as $pertemuan => $status)
                <td style="text-align:center; font-weight: bold;">
                    {{ $status }}
                </td>
                @endforeach
            </tr>
            @endforeach

            {{-- Baris Cadangan untuk Mahasiswa yang baru tervalidasi --}}
            @for($spare=1; $spare<=2; $spare++)
                <tr style="color: #eee;">
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                @for($i=1; $i<=16; $i++) <td>
                    </td> @endfor
                    </tr>
                    @endfor
        </tbody>
    </table>

    <div class="footer-container">
        <div class="note-box">
            <strong>CATATAN PENTING:</strong>
            <ul style="margin: 5px 0; padding-left: 15px;">
                <li>Mahasiswa wajib menandatangani daftar hadir setiap pertemuan.</li>
                <li>Batas minimal kehadiran untuk mengikuti UAS adalah 75% (12 Pertemuan).</li>
                <li>Dosen wajib mengisi Jurnal Perkuliahan di balik halaman ini (atau lembar lampiran).</li>
            </ul>
        </div>

        <div class="ttd-box">
            <p>Kota Kupang, {{ now()->isoFormat('D MMMM Y') }}</p>
            <p>Dosen Koordinator,</p>
            <div class="ttd-space"></div>
            @php
            // Ambil Koordinator untuk tanda tangan
            $koordinator = $jadwal->dosens->where('pivot.is_koordinator', true)->first() ?? $jadwal->dosens->first();
            @endphp
            <p class="bold" style="text-decoration: underline;">{{ $koordinator->person->nama_lengkap ?? '..........................' }}</p>
            <p>NIDN. {{ $koordinator->nidn ?? '-' }}</p>
        </div>
        <div class="clear"></div>
    </div>

    {{-- Penomoran Halaman Otomatis --}}
    <script type="text/php">
        if (isset($pdf)) {
            $text = "DHMD - {PAGE_NUM} / {PAGE_COUNT}";
            $size = 7;
            $font = $fontMetrics->getFont("helvetica");
            $width = $fontMetrics->get_text_width($text, $font, $size);
            $x = ($pdf->get_width() - $width) / 2;
            $y = $pdf->get_height() - 30;
            $pdf->page_text($x, $y, $text, $font, $size);
        }
    </script>
</body>

</html>