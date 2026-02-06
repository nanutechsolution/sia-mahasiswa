<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Rekap Presensi - {{ $jadwal->mataKuliah->nama_mk }}</title>
    <style>
        /* Setup Kertas A4 Landscape */
        @page { margin: 1cm 1.5cm; size: a4 landscape; }
        
        body { 
            font-family: 'Times New Roman', Times, serif; 
            font-size: 11px; 
            line-height: 1.15; 
            color: #000; 
        }
        
        /* KOP SURAT */
        .kop-table { width: 100%; border-bottom: 3px double #000; margin-bottom: 20px; padding-bottom: 10px; }
        .logo-cell { width: 80px; text-align: center; vertical-align: middle; }
        .logo-placeholder { 
            width: 70px; height: 70px; 
            border: 2px solid #000; border-radius: 50%;
            text-align: center; line-height: 70px; font-weight: bold; font-size: 10px;
        }
        .kop-text { text-align: center; vertical-align: middle; }
        .univ-name { font-size: 16px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; }
        .fakultas-name { font-size: 14px; font-weight: bold; margin: 3px 0; }
        .address { font-size: 10px; font-style: italic; }

        /* JUDUL HALAMAN */
        .judul { 
            text-align: center; 
            font-weight: bold; 
            font-size: 13px; 
            text-decoration: underline; 
            margin-bottom: 15px; 
            text-transform: uppercase; 
        }

        /* TABEL INFORMASI KELAS */
        .meta-table { width: 100%; font-size: 11px; margin-bottom: 15px; }
        .meta-table td { padding: 2px; vertical-align: top; }
        .label { font-weight: bold; width: 130px; }
        .colon { width: 10px; text-align: center; }

        /* TABEL DATA UTAMA */
        .table-data { width: 100%; border-collapse: collapse; font-size: 10px; margin-bottom: 20px; }
        .table-data th, .table-data td { 
            border: 1px solid #000; 
            padding: 4px; 
            text-align: center; 
            vertical-align: middle; 
        }
        .table-data th { background-color: #e0e0e0; font-weight: bold; }
        .text-left { text-align: left !important; padding-left: 5px !important; }
        .nowrap { white-space: nowrap; }

        /* WARNA STATUS KEHADIRAN */
        .bg-alpha { background-color: #ffebee; color: #c62828; font-weight: bold; } /* Merah Pucat */
        .bg-ijin { background-color: #e3f2fd; color: #0d47a1; } /* Biru Pucat */
        .bg-sakit { background-color: #fff3e0; color: #e65100; } /* Oranye Pucat */

        /* JUDUL BAGIAN (JURNAL) */
        .section-title { 
            font-weight: bold; 
            font-size: 11px; 
            margin-top: 10px;
            margin-bottom: 5px; 
            text-decoration: underline; 
        }

        /* AREA TANDA TANGAN */
        .footer-wrapper { width: 100%; margin-top: 20px; page-break-inside: avoid; }
        .ttd-box { float: right; width: 250px; text-align: center; }
        .ttd-date { margin-bottom: 50px; }
        .ttd-name { font-weight: bold; text-decoration: underline; }
    </style>
</head>
<body>
    <!-- KOP SURAT (Sesuaikan dengan Logo Kampus Anda) -->
    <table class="kop-table">
        <tr>
            <td class="logo-cell">
                {{-- Gunakan public_path agar gambar terbaca oleh DomPDF --}}
                {{-- <img src="{{ public_path('images/logo.png') }}" width="70"> --}}
                <div class="logo-placeholder">LOGO</div>
            </td>
            <td class="kop-text">
                <div class="univ-name">UNIVERSITAS MARITIM (UNMARIS)</div>
                <div class="fakultas-name">FAKULTAS TEKNIK & ILMU KELAUTAN</div>
                <div class="address">
                    Jl. Bahari Raya No. 99, Kota Pelabuhan, Indonesia 12345<br>
                    Telp: (021) 555-1234 | Email: akademik@unmaris.ac.id | Website: www.unmaris.ac.id
                </div>
            </td>
        </tr>
    </table>

    <div class="judul">REKAPITULASI PRESENSI MAHASISWA</div>

    <!-- INFORMASI MATA KULIAH -->
    <table class="meta-table">
        <tr>
            <td class="label">Mata Kuliah</td>
            <td class="colon">:</td>
            <td>{{ $jadwal->mataKuliah->nama_mk }} ({{ $jadwal->mataKuliah->kode_mk }})</td>
            <td class="label">Tahun Akademik</td>
            <td class="colon">:</td>
            <td>{{ $jadwal->tahunAkademik->nama_tahun ?? 'Aktif' }}</td>
        </tr>
        <tr>
            <td class="label">Dosen Pengampu</td>
            <td class="colon">:</td>
            <td>{{ $jadwal->dosen->person->nama_lengkap }}</td>
            <td class="label">Kelas / Ruang</td>
            <td class="colon">:</td>
            <td>{{ $jadwal->nama_kelas }} / {{ $jadwal->ruang }}</td>
        </tr>
        <tr>
            <td class="label">Program Studi</td>
            <td class="colon">:</td>
            <td>{{ $jadwal->mataKuliah->prodi->nama_prodi ?? 'Umum' }}</td>
            <td class="label">Total Sesi</td>
            <td class="colon">:</td>
            <td>{{ $listSesi->count() }} Pertemuan Realisasi</td>
        </tr>
    </table>

    <!-- TABEL DATA PRESENSI (MATRIX 16 PERTEMUAN) -->
    <table class="table-data">
        <thead>
            <tr>
                <th rowspan="3" width="25">No</th>
                <th rowspan="3" width="80">NIM</th>
                <th rowspan="3">Nama Mahasiswa</th>
                <th colspan="16" style="height: 20px;">Pertemuan Ke-</th>
                <th rowspan="3" width="35" title="Persentase Kehadiran">%</th>
            </tr>
            <tr>
                {{-- Baris Nomor Pertemuan --}}
                @for($i = 1; $i <= 16; $i++)
                    <th width="20">{{ $i }}</th>
                @endfor
            </tr>
            <tr>
                {{-- Baris Tanggal & Mode Pertemuan --}}
                @for($i = 1; $i <= 16; $i++)
                    @php 
                        $sesi = $listSesi->firstWhere('pertemuan_ke', $i);
                        $tgl = $sesi ? \Carbon\Carbon::parse($sesi->waktu_mulai_realisasi)->format('d/m') : '-';
                        
                        // LOGIKA DETEKSI MODE (Tatap Muka, Daring, Manual/Tugas)
                        $mode = '';
                        $color = 'black';
                        
                        if ($sesi) {
                            $namaRuang = strtoupper($sesi->jadwalKuliah->ruang ?? '');
                            $metode = strtoupper($sesi->metode_validasi);
                            
                            // Cek apakah Daring/Tugas
                            $isOnline = \Illuminate\Support\Str::contains($namaRuang, ['DARING', 'ONLINE', 'ZOOM', 'MANDIRI', 'LMS', 'TUGAS']);
                            
                            if ($metode == 'TUGAS' || $metode == 'DARING' || $isOnline) {
                                $mode = 'D'; // Daring / Penugasan
                                $color = 'blue';
                            } elseif ($metode == 'MANUAL') {
                                $mode = 'M'; // Manual
                                $color = '#d97706'; // Amber/Orange
                            } else {
                                $mode = 'T'; // Tatap Muka (GPS/QR)
                            }
                        }
                    @endphp
                    <th style="font-size: 8px; font-weight: normal; height: 25px;">
                        {{ $tgl }}<br>
                        @if($mode)
                            <span style="font-weight: bold; color: {{ $color }}">{{ $mode }}</span>
                        @endif
                    </th>
                @endfor
            </tr>
        </thead>
        <tbody>
            @foreach($rekap as $row)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $row['nim'] }}</td>
                <td class="text-left nowrap">{{ strtoupper($row['nama']) }}</td>
                
                {{-- Loop Isi Status Kehadiran --}}
                @for($i = 1; $i <= 16; $i++)
                    @php 
                        $status = $row['kehadiran'][$i] ?? '';
                        $class = '';
                        if($status == 'A') $class = 'bg-alpha';
                        elseif($status == 'I') $class = 'bg-ijin';
                        elseif($status == 'S') $class = 'bg-sakit';
                    @endphp
                    <td class="{{ $class }}">{{ $status }}</td>
                @endfor

                {{-- Persentase --}}
                <td style="font-weight:bold;">{{ $row['persentase'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div style="font-size: 9px; margin-top: -10px; margin-bottom: 20px;">
        <strong>Keterangan Mode:</strong> 
        T = Tatap Muka (GPS/QR), 
        <span style="color:blue">D = Daring / Penugasan (Bebas Lokasi)</span>, 
        <span style="color:#d97706">M = Input Manual Dosen</span>.
    </div>

    <!-- TABEL RINCIAN MATERI (JURNAL KULIAH) -->
    <div class="section-title">RINCIAN MATERI PERKULIAHAN</div>
    <table class="table-data">
        <thead>
            <tr>
                <th width="30">Ke</th>
                <th width="100">Tanggal</th>
                <th width="100">Metode</th>
                <th>Pokok Bahasan / Materi / Kegiatan Pembelajaran</th>
                <th width="50">Hadir</th>
            </tr>
        </thead>
        <tbody>
            @foreach($listSesi as $sesi)
            <tr>
                <td>{{ $sesi->pertemuan_ke }}</td>
                <td>{{ \Carbon\Carbon::parse($sesi->waktu_mulai_realisasi)->isoFormat('dddd, D MMM Y') }}</td>
                <td>
                    {{-- Translate Kode Metode ke Bahasa Manusia --}}
                    {{ match($sesi->metode_validasi) {
                        'MANUAL' => 'Input Manual',
                        'DARING' => 'Daring / Online',
                        'TUGAS'  => 'Penugasan Mandiri',
                        'QR'     => 'Tatap Muka (QR)',
                        'GPS'    => 'Tatap Muka (GPS)',
                        default  => $sesi->metode_validasi
                    } }}
                </td>
                <td class="text-left" style="padding: 5px;">{{ $sesi->materi_kuliah ?? '-' }}</td>
                <td>{{ $sesi->absensi->where('status_kehadiran', 'H')->count() }}</td>
            </tr>
            @endforeach
            
            {{-- Isi baris kosong sisa jika sesi kurang dari 16 --}}
            @if($listSesi->count() < 16)
                @for($j = $listSesi->count() + 1; $j <= 16; $j++)
                <tr>
                    <td>{{ $j }}</td>
                    <td>-</td>
                    <td>-</td>
                    <td class="text-left">-</td>
                    <td>-</td>
                </tr>
                @endfor
            @endif
        </tbody>
    </table>

    <!-- KOLOM TANDA TANGAN -->
    <div class="footer-wrapper">
        <div class="ttd-box">
            <div class="ttd-date">Kota Bahari, {{ $tanggal_cetak }}<br>Dosen Pengampu,</div>
            <br><br><br>
            <div class="ttd-name">{{ $jadwal->dosen->person->nama_lengkap }}</div>
            <div>NIDN. {{ $jadwal->dosen->nidn ?? '-' }}</div>
        </div>
    </div>

    {{-- Script Nomor Halaman Otomatis (DomPDF) --}}
    <script type="text/php">
        if (isset($pdf)) {
            $text = "Halaman {PAGE_NUM} dari {PAGE_COUNT}";
            $size = 9;
            $font = $fontMetrics->getFont("Times New Roman");
            $width = $fontMetrics->get_text_width($text, $font, $size) / 2;
            $x = ($pdf->get_width() - $width) / 2;
            $y = $pdf->get_height() - 35;
            $pdf->page_text($x, $y, $text, $font, $size);
        }
    </script>
</body>
</html>