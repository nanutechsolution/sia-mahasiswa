<!DOCTYPE html>
<html>
<head>
    <title>Berita Acara Perkuliahan</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 18px; text-transform: uppercase; }
        .header p { margin: 2px 0; font-size: 10px; }
        
        .meta-table { width: 100%; margin-bottom: 20px; }
        .meta-table td { padding: 3px; vertical-align: top; }
        .meta-label { font-weight: bold; width: 120px; }

        .content-table { width: 100%; border-collapse: collapse; }
        .content-table th, .content-table td { border: 1px solid #000; padding: 5px; text-align: left; }
        .content-table th { background-color: #f0f0f0; text-align: center; }
        .center { text-align: center; }
        
        .footer { margin-top: 30px; text-align: right; }
        .signature { margin-top: 50px; text-align: right; margin-right: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>UNIVERSITAS MARITIM (UNMARIS)</h1>
        <p>Jl. Contoh Kampus No. 123, Kota Bahari</p>
        <h3>BERITA ACARA PERKULIAHAN</h3>
    </div>

    <table class="meta-table">
        <tr>
            <td class="meta-label">Mata Kuliah</td>
            <td>: {{ $sesi->jadwalKuliah->mataKuliah->nama_mk }} ({{ $sesi->jadwalKuliah->mataKuliah->kode_mk }})</td>
            <td class="meta-label">Pertemuan Ke</td>
            <td>: {{ $sesi->pertemuan_ke }}</td>
        </tr>
        <tr>
            <td class="meta-label">Dosen</td>
            <td>: {{ $sesi->jadwalKuliah->dosen->person->nama_lengkap }}</td>
            <td class="meta-label">Tanggal</td>
            <td>: {{ \Carbon\Carbon::parse($sesi->waktu_mulai_realisasi)->isoFormat('dddd, D MMMM Y') }}</td>
        </tr>
        <tr>
            <td class="meta-label">Materi</td>
            <td colspan="3">: {{ $sesi->materi_kuliah }}</td>
        </tr>
    </table>

    <table class="content-table">
        <thead>
            <tr>
                <th width="30">No</th>
                <th width="100">NIM</th>
                <th>Nama Mahasiswa</th>
                <th width="80">Status</th>
                <th width="80">Waktu</th>
                <th width="80">Paraf</th>
            </tr>
        </thead>
        <tbody>
            @foreach($peserta as $index => $mhs)
            <tr>
                <td class="center">{{ $loop->iteration }}</td>
                <td class="center">{{ $mhs['nim'] }}</td>
                <td>{{ strtoupper($mhs['nama']) }}</td>
                <td class="center">
                    @if($mhs['status'] == 'H') Hadir
                    @elseif($mhs['status'] == 'I') Ijin
                    @elseif($mhs['status'] == 'S') Sakit
                    @else Alpha
                    @endif
                </td>
                <td class="center">{{ $mhs['waktu'] ? \Carbon\Carbon::parse($mhs['waktu'])->format('H:i') : '-' }}</td>
                <td></td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>{{ $tanggal_cetak }}</p>
        <div class="signature">
            <p>Dosen Pengampu,</p>
            <br><br><br>
            <p><strong>{{ $sesi->jadwalKuliah->dosen->person->nama_lengkap }}</strong></p>
        </div>
    </div>
</body>
</html>