<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Rekap Presensi - {{ $jadwal->mataKuliah->nama_mk }}</title>
    <style>
        /* Setup Kertas A4 Landscape */
        @page { margin: 1cm 1.2cm; size: a4 landscape; }
        body { 
            font-family: 'Times New Roman', Times, serif; 
            font-size: 10px; 
            color: #000; 
            line-height: 1.2; 
        }
        
        /* KOP SURAT INSTITUSI */
        .kop-table { width: 100%; border-bottom: 3px double #000; margin-bottom: 15px; padding-bottom: 8px; }
        .kop-text { text-align: center; vertical-align: middle; }
        .univ-name { font-size: 16px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; }
        .fakultas-name { font-size: 13px; font-weight: bold; margin: 2px 0; text-transform: uppercase; }
        .address { font-size: 9px; font-style: italic; color: #444; }

        .judul { 
            text-align: center; 
            font-weight: bold; 
            font-size: 12px; 
            text-decoration: underline; 
            margin-bottom: 15px; 
            text-transform: uppercase; 
        }

        /* META DATA JADWAL */
        .meta-table { width: 100%; margin-bottom: 15px; }
        .meta-table td { padding: 1px 4px; vertical-align: top; }
        .label { font-weight: bold; width: 110px; }
        .colon { width: 10px; text-align: center; }

        /* TABEL DATA UTAMA (MATRIX KEHADIRAN) */
        .table-data { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .table-data th, .table-data td { border: 1px solid #000; padding: 3px 2px; text-align: center; }
        .table-data th { background-color: #f0f0f0; font-weight: bold; font-size: 9px; }
        .text-left { text-align: left !important; padding-left: 5px !important; }
        
        /* WARNA STATUS KEHADIRAN */
        .bg-alpha { background-color: #fee2e2; color: #991b1b; font-weight: bold; } /* Merah */
        .bg-ijin { background-color: #dbeafe; color: #1e40af; } /* Biru */
        .bg-sakit { background-color: #ffedd5; color: #9a3412; } /* Oranye */

        /* JURNAL PERKULIAHAN (BAP) */
        .section-title { 
            font-weight: bold; 
            font-size: 10px; 
            margin-top: 20px; 
            margin-bottom: 5px; 
            text-decoration: underline; 
            text-transform: uppercase; 
        }
        
        /* FOOTER / TANDA TANGAN */
        .footer-wrapper { width: 100%; margin-top: 25px; page-break-inside: avoid; }
        .ttd-box { float: right; width: 220px; text-align: center; }
        .ttd-space { height: 60px; }
        .clear { clear: both; }
    </style>
</head>
<body>
    {{-- Kop Surat --}}
    <table class="kop-table">
        <tr>
            <td class="kop-text">
                <div class="univ-name">UNIVERSITAS MARITIM (UNMARIS)</div>
                <div class="fakultas-name">{{ $jadwal->mataKuliah->prodi->fakultas->nama_fakultas ?? 'FAKULTAS TEKNIK & ILMU KELAUTAN' }}</div>
                <div class="address">
                    Gedung Rektorat Lt. 2, Jl. Bahari No. 123, Kota Kupang, NTT<br>
                    Telp: (0380) 123456 | Website: www.unmaris.ac.id
                </div>
            </td>
        </tr>
    </table>

    <div class="judul">REKAPITULASI PRESENSI MAHASISWA PER SEMESTER</div>

    {{-- Informasi Metadata --}}
    <table class="meta-table">
        <tr>
            <td class="label">Mata Kuliah</td><td class="colon">:</td>
            <td>{{ $jadwal->mataKuliah->nama_mk }} ({{ $jadwal->mataKuliah->kode_mk }})</td>
            <td class="label">Tahun Akademik</td><td class="colon">:</td>
            <td>{{ $jadwal->tahunAkademik->nama_tahun ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Tim Pengajar</td><td class="colon">:</td>
            <td>
                @foreach($jadwal->dosens as $d)
                    {{ $d->person->nama_lengkap }}{{ !$loop->last ? ', ' : '' }}
                @endforeach
            </td>
            <td class="label">Kelas / Ruang</td><td class="colon">:</td>
            <td>{{ $jadwal->nama_kelas }} / {{ $jadwal->ruang->kode_ruang ?? 'TBA' }}</td>
        </tr>
        <tr>
            <td class="label">Program Studi</td><td class="colon">:</td>
            <td>{{ $jadwal->mataKuliah->prodi->nama_prodi ?? '-' }}</td>
            <td class="label">Jumlah Mahasiswa</td><td class="colon">:</td>
            <td>{{ count($rekap) }} Orang</td>
        </tr>
    </table>

    {{-- Tabel Matriks 16 Pertemuan --}}
    <table class="table-data">
        <thead>
            <tr>
                <th rowspan="2" width="20">No</th>
                <th rowspan="2" width="75">NIM</th>
                <th rowspan="2">Nama Lengkap Mahasiswa</th>
                <th colspan="16">Pertemuan Ke-</th>
                <th rowspan="2" width="30">Total<br>%</th>
            </tr>
            <tr>
                @for($i = 1; $i <= 16; $i++)
                    @php 
                        $sesi = $jadwal->sesi->firstWhere('pertemuan_ke', $i);
                        $tgl = $sesi ? \Carbon\Carbon::parse($sesi->waktu_mulai_realisasi)->format('d/m') : '';
                    @endphp
                    <th width="18" style="font-size: 7px;">{{ $i }}<br>{{ $tgl }}</th>
                @endfor
            </tr>
        </thead>
        <tbody>
            @foreach($rekap as $row)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td><code>{{ $row['nim'] }}</code></td>
                <td class="text-left" style="text-transform: uppercase; font-size: 8.5px;">{{ $row['nama'] }}</td>
                @for($i = 1; $i <= 16; $i++)
                    @php 
                        $status = $row['kehadiran'][$i] ?? '';
                        $class = match($status) { 'A' => 'bg-alpha', 'I' => 'bg-ijin', 'S' => 'bg-sakit', default => '' };
                    @endphp
                    <td class="{{ $class }}">{{ $status }}</td>
                @endfor
                <td style="font-weight: bold; {{ $row['persentase'] < 75 ? 'color: red;' : '' }}">
                    {{ $row['persentase'] }}%
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Jurnal Materi (Audit Trail BAP) --}}
    <div class="section-title">Jurnal Materi Perkuliahan</div>
    <table class="table-data" style="font-size: 8.5px;">
        <thead>
            <tr>
                <th width="20">Ke</th>
                <th width="85">Hari, Tanggal</th>
                <th>Pokok Bahasan / Materi Pembelajaran</th>
                <th width="40">Mhs Hadir</th>
                <th width="65">Metode</th>
            </tr>
        </thead>
        <tbody>
            @foreach($jadwal->sesi->sortBy('pertemuan_ke') as $sesi)
            <tr>
                <td>{{ $sesi->pertemuan_ke }}</td>
                <td>{{ \Carbon\Carbon::parse($sesi->waktu_mulai_realisasi)->isoFormat('dddd, D MMM Y') }}</td>
                <td class="text-left" style="padding: 5px;">{{ $sesi->materi_kuliah }}</td>
                <td>{{ $sesi->absensi->where('status_kehadiran', 'H')->count() }}</td>
                <td style="font-size: 7px;">{{ $sesi->metode_validasi }}</td>
            </tr>
            @endforeach
            @for($j = $jadwal->sesi->count() + 1; $j <= 16; $j++)
            <tr style="color: #ccc;">
                <td>{{ $j }}</td><td>-</td><td class="text-left">Belum terealisasi</td><td>-</td><td>-</td>
            </tr>
            @endfor
        </tbody>
    </table>

    {{-- Kolom Tanda Tangan --}}
    <div class="footer-wrapper">
        <div class="ttd-box">
            <p>Kota Kupang, {{ $tanggal_cetak }}</p>
            <p>Dosen Koordinator,</p>
            <div class="ttd-space"></div>
            @php
                // Mengambil koordinator dari tim pengajar
                $koordinator = $jadwal->dosens->where('pivot.is_koordinator', true)->first() ?? $jadwal->dosens->first();
            @endphp
            <p><strong>{{ $koordinator->person->nama_lengkap ?? '..........................' }}</strong></p>
            <p style="font-size: 9px;">NIDN. {{ $koordinator->nidn ?? '-' }}</p>
        </div>
        <div class="clear"></div>
    </div>

    <div style="position: fixed; bottom: 0; width: 100%; font-size: 7px; color: #777; text-align: left; border-top: 0.5px solid #ccc; padding-top: 3px;">
        Laporan ini diterbitkan secara otomatis oleh SIAKAD UNMARIS v4.2. Kejujuran akademik adalah nilai utama kita.
    </div>
</body>
</html>