<!DOCTYPE html>
<html>
<head>
    <title>Berita Acara Perkuliahan</title>
    <style>
        @page { margin: 1.5cm; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 11px; color: #333; line-height: 1.4; }
        
        /* Header styling */
        .header { text-align: center; margin-bottom: 25px; border-bottom: 3px double #000; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 18px; color: #002855; }
        .header p { margin: 2px 0; font-size: 10px; font-weight: bold; color: #666; }
        .header h3 { margin: 10px 0 0 0; font-size: 14px; text-decoration: underline; }

        /* Meta Table */
        .meta-table { width: 100%; margin-bottom: 20px; border: 1px solid #eee; padding: 10px; background-color: #fafafa; }
        .meta-table td { padding: 4px; vertical-align: top; }
        .meta-label { font-weight: bold; width: 110px; color: #002855; }

        /* Main Table */
        .content-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .content-table th, .content-table td { border: 1px solid #000; padding: 6px 8px; }
        .content-table th { background-color: #f2f2f2; font-weight: bold; text-transform: uppercase; font-size: 10px; }
        
        .center { text-align: center; }
        .uppercase { text-transform: uppercase; }
        
        /* Status colors */
        .status-h { color: #059669; font-weight: bold; }
        .status-a { color: #dc2626; font-weight: bold; }

        /* Footer / Signature */
        .footer-area { margin-top: 40px; width: 100%; }
        .signature-box { float: right; width: 250px; text-align: center; }
        .signature-space { height: 70px; }
        
        .clear { clear: both; }
    </style>
</head>
<body>
    <div class="header">
        <h1>UNIVERSITAS MARITIM (UNMARIS)</h1>
        <p>Gedung Rektorat Lt. 2, Jl. Bahari No. 123, Kota Kupang - NTT</p>
        <p>Telepon: (0380) 123456 | Website: www.unmaris.ac.id</p>
        <h3>BERITA ACARA & DAFTAR HADIR PERKULIAHAN</h3>
    </div>

    <table class="meta-table">
        <tr>
            <td class="meta-label">Mata Kuliah</td>
            <td>: {{ $sesi->jadwalKuliah->mataKuliah->nama_mk }} ({{ $sesi->jadwalKuliah->mataKuliah->kode_mk }})</td>
            <td class="meta-label">Pertemuan Ke</td>
            <td>: <strong>{{ $sesi->pertemuan_ke }}</strong></td>
        </tr>
        <tr>
            <td class="meta-label">Tim Pengajar</td>
            <td>: 
                @foreach($sesi->jadwalKuliah->dosens as $d)
                    {{ $d->person->nama_lengkap }}{{ !$loop->last ? ',' : '' }}
                @endforeach
            </td>
            <td class="meta-label">Waktu / Ruang</td>
            <td>: {{ substr($sesi->waktu_mulai_realisasi, 11, 5) }} / {{ $sesi->jadwalKuliah->ruang->kode_ruang ?? 'TBA' }}</td>
        </tr>
        <tr>
            <td class="meta-label">Materi</td>
            <td colspan="3">: {{ $sesi->materi_kuliah }}</td>
        </tr>
    </table>

    <table class="content-table">
        <thead>
            <tr>
                <th width="25">No</th>
                <th width="80">NIM</th>
                <th>Nama Lengkap Mahasiswa</th>
                <th width="60">Status</th>
                <th width="50">Waktu</th>
                <th width="80">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($peserta as $mhs)
            <tr>
                <td class="center">{{ $loop->iteration }}</td>
                <td class="center"><code>{{ $mhs['nim'] }}</code></td>
                <td class="uppercase">{{ $mhs['nama'] }}</td>
                <td class="center">
                    @php
                        $label = match($mhs['status']) {
                            'H' => 'HADIR',
                            'I' => 'IZIN',
                            'S' => 'SAKIT',
                            default => 'ALPHA'
                        };
                        $class = $mhs['status'] == 'H' ? 'status-h' : ($mhs['status'] == 'A' ? 'status-a' : '');
                    @endphp
                    <span class="{{ $class }}">{{ $label }}</span>
                </td>
                <td class="center">{{ $mhs['waktu'] ?: '-' }}</td>
                <td></td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer-area">
        <div class="signature-box">
            <p>Kota Kupang, {{ $tanggal_cetak }}</p>
            <p>Dosen Pengampu / Koordinator,</p>
            <div class="signature-space"></div>
            <p><strong>{{ $ttdDosen->nama }}</strong></p>
            <p style="font-size: 9px;">{{ $ttdDosen->identitas }}</p>
        </div>
        <div class="clear"></div>
    </div>

    <div style="position: fixed; bottom: 0; width: 100%; font-size: 8px; color: #999; text-align: left; border-top: 1px solid #eee; padding-top: 5px;">
        Dokumen ini diterbitkan secara digital melalui SIAKAD UNMARIS v4.2. Validitas data dapat dicek melalui sistem.
    </div>
</body>
</html>