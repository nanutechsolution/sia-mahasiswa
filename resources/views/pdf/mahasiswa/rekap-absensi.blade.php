<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Rekap Absensi - {{ $mahasiswa->nim }}</title>
    <style>
        @page { margin: 1cm 1.5cm; }
        body { font-family: 'Times New Roman', Times, serif; font-size: 11px; line-height: 1.2; color: #000; }
        
        /* KOP SURAT */
        .kop-table { width: 100%; border-bottom: 3px double #000; margin-bottom: 20px; padding-bottom: 10px; }
        .logo-cell { width: 80px; text-align: center; vertical-align: middle; }
        .logo-placeholder { 
            width: 60px; height: 60px; 
            border: 2px solid #000; border-radius: 50%;
            text-align: center; line-height: 60px; font-weight: bold; font-size: 9px;
        }
        .kop-text { text-align: center; vertical-align: middle; }
        .univ-name { font-size: 14px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; }
        .fakultas-name { font-size: 12px; font-weight: bold; margin: 2px 0; }
        .address { font-size: 9px; font-style: italic; }

        /* JUDUL */
        .judul { text-align: center; font-weight: bold; font-size: 12px; text-decoration: underline; margin-bottom: 20px; text-transform: uppercase; }

        /* INFO MAHASISWA & MATKUL */
        .info-container { width: 100%; margin-bottom: 20px; }
        .info-table { width: 100%; }
        .info-table td { vertical-align: top; padding: 2px; }
        .label { font-weight: bold; width: 100px; }
        .colon { width: 10px; text-align: center; }

        /* TABEL KEHADIRAN */
        .table-data { width: 100%; border-collapse: collapse; font-size: 10px; }
        .table-data th, .table-data td { border: 1px solid #000; padding: 5px; text-align: center; vertical-align: middle; }
        .table-data th { background-color: #f0f0f0; font-weight: bold; }
        .text-left { text-align: left !important; padding-left: 8px !important; }

        /* STATUS COLORS (Optional for PDF if color allowed) */
        .status-h { color: green; font-weight: bold; }
        .status-a { color: red; font-weight: bold; }
        .status-i { color: blue; }
        
        /* FOOTER */
        .footer { margin-top: 30px; width: 100%; }
        .ttd-box { float: right; width: 200px; text-align: center; }
        .ttd-space { height: 60px; }
    </style>
</head>
<body>
    <!-- KOP SURAT -->
    <table class="kop-table">
        <tr>
            <td class="logo-cell">
                <div class="logo-placeholder">LOGO</div>
            </td>
            <td class="kop-text">
                <div class="univ-name">UNIVERSITAS MARITIM (UNMARIS)</div>
                <div class="fakultas-name">FAKULTAS TEKNIK & ILMU KELAUTAN</div>
                <div class="address">
                    Jl. Bahari Raya No. 99, Kota Pelabuhan, Indonesia 12345<br>
                    Website: www.unmaris.ac.id | Email: akademik@unmaris.ac.id
                </div>
            </td>
        </tr>
    </table>

    <div class="judul">LAPORAN KEHADIRAN MAHASISWA</div>

    <div class="info-container">
        <table class="info-table">
            <tr>
                <td class="label">Nama Mahasiswa</td><td class="colon">:</td>
                <td width="250">{{ $mahasiswa->person->nama_lengkap }}</td>
                
                <td class="label">Mata Kuliah</td><td class="colon">:</td>
                <td>{{ $jadwal->mataKuliah->nama_mk }}</td>
            </tr>
            <tr>
                <td class="label">NIM</td><td class="colon">:</td>
                <td>{{ $mahasiswa->nim }}</td>
                
                <td class="label">Dosen</td><td class="colon">:</td>
                <td>{{ $jadwal->dosen->person->nama_lengkap }}</td>
            </tr>
            <tr>
                <td class="label">Program Studi</td><td class="colon">:</td>
                <td>{{ $jadwal->mataKuliah->prodi->nama_prodi ?? '-' }}</td>
                
                <td class="label">Tahun Akademik</td><td class="colon">:</td>
                <td>{{ $jadwal->tahunAkademik->nama_tahun ?? '-' }}</td>
            </tr>
        </table>
    </div>

    <!-- TABEL DATA -->
    <table class="table-data">
        <thead>
            <tr>
                <th width="30">No</th>
                <th width="100">Tanggal</th>
                <th width="40">Pert. Ke</th>
                <th>Materi / Pokok Bahasan</th>
                <th width="80">Metode</th>
                <th width="60">Status</th>
                <th width="60">Waktu</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sesiList as $sesi)
                @php 
                    $dataAbsen = $absensi[$sesi->id] ?? null;
                    $status = $dataAbsen ? $dataAbsen->status_kehadiran : 'A'; // Default Alpha jika sesi ada tapi data absen tidak ada
                    $jam = $dataAbsen ? (\Carbon\Carbon::parse($dataAbsen->waktu_check_in)->format('H:i') ?? '-') : '-';
                    $metode = $dataAbsen->bukti_validasi['method'] ?? $sesi->metode_validasi ?? '-';
                @endphp
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ \Carbon\Carbon::parse($sesi->waktu_mulai_realisasi)->isoFormat('D MMM Y') }}</td>
                <td>{{ $sesi->pertemuan_ke }}</td>
                <td class="text-left">{{ Str::limit($sesi->materi_kuliah ?? '-', 50) }}</td>
                <td>{{ str_replace('_CHECK', '', $metode) }}</td>
                <td class="{{ $status == 'H' ? 'status-h' : ($status == 'A' ? 'status-a' : 'status-i') }}">
                    @if($status == 'H') HADIR
                    @elseif($status == 'I') IJIN
                    @elseif($status == 'S') SAKIT
                    @else ALPHA
                    @endif
                </td>
                <td>{{ $jam }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="padding: 20px;">Belum ada riwayat pertemuan.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 15px; font-size: 10px;">
        <strong>Statistik Kehadiran:</strong> 
        Hadir: {{ $statistik['hadir'] }} dari {{ $statistik['total'] }} Pertemuan 
        (<strong>{{ $statistik['persen'] }}%</strong>)
    </div>

    <div class="footer">
        <div class="ttd-box">
            <div class="ttd-date">Kota Bahari, {{ $tanggal_cetak }}</div>
            <div>Mengetahui,<br>Dosen Pengampu</div>
            <div class="ttd-space"></div>
            <div class="ttd-name">{{ $jadwal->dosen->person->nama_lengkap }}</div>
            <div>NIDN. {{ $jadwal->dosen->nidn ?? '-' }}</div>
        </div>
    </div>
</body>
</html>