<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Kartu {{ $jenis_ujian }} - {{ $mahasiswa->nim }}</title>
    <style>
        /* Setup Kertas A4 Portrait */
        @page { margin: 1cm 1.5cm; size: a4 portrait; }
        body { 
            font-family: 'Times New Roman', Times, serif; 
            font-size: 11px; 
            color: #000; 
            line-height: 1.3; 
        }
        
        /* KOP SURAT */
        .kop-table { width: 100%; border-bottom: 3px double #000; margin-bottom: 15px; padding-bottom: 8px; }
        .kop-text { text-align: center; vertical-align: middle; }
        .univ-name { font-size: 16px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; color: #002855; }
        .fakultas-name { font-size: 13px; font-weight: bold; margin: 3px 0; text-transform: uppercase; }
        .address { font-size: 9px; font-style: italic; color: #444; }

        .judul { 
            text-align: center; 
            font-weight: bold; 
            font-size: 14px; 
            text-decoration: underline; 
            margin-bottom: 5px; 
            text-transform: uppercase; 
        }
        
        .sub-judul { text-align: center; font-weight: bold; font-size: 11px; margin-bottom: 20px; }

        /* INFO MAHASISWA & FOTO */
        .header-container { width: 100%; margin-bottom: 20px; }
        .info-table { width: 100%; border-collapse: collapse; }
        .info-table td { vertical-align: top; padding: 3px 0; }
        .label { font-weight: bold; width: 120px; }
        .colon { width: 15px; text-align: center; }
        
        .foto-box { 
            width: 3cm; 
            height: 4cm; 
            border: 1px solid #000; 
            text-align: center; 
            line-height: 4cm; 
            font-size: 10px; 
            color: #999;
            float: right;
        }

        /* TABEL JADWAL UJIAN */
        .table-data { width: 100%; border-collapse: collapse; margin-bottom: 25px; }
        .table-data th, .table-data td { 
            border: 1px solid #000; 
            padding: 8px 5px; 
            text-align: center; 
            vertical-align: middle; 
        }
        .table-data th { background-color: #f5f5f5; font-weight: bold; text-transform: uppercase; font-size: 9px; }
        .text-left { text-align: left !important; padding-left: 8px !important; }

        /* ATURAN & TTD */
        .footer-container { width: 100%; margin-top: 10px; page-break-inside: avoid; }
        .aturan-box { float: left; width: 50%; font-size: 9px; }
        .aturan-box ul { padding-left: 15px; margin-top: 5px; }
        
        .ttd-box { float: right; width: 230px; text-align: center; }
        .ttd-space { height: 60px; }
        .clear { clear: both; }
    </style>
</head>
<body>
    {{-- Kop Surat Institusi --}}
    <table class="kop-table">
        <tr>
            <td class="kop-text">
                <div class="univ-name">UNIVERSITAS MARITIM (UNMARIS)</div>
                <div class="fakultas-name">{{ $mahasiswa->prodi->fakultas->nama_fakultas ?? 'FAKULTAS TEKNIK & ILMU KELAUTAN' }}</div>
                <div class="address">
                    Jl. Bahari No. 123, Kota Kupang, Nusa Tenggara Timur<br>
                    Website: www.unmaris.ac.id | Email: akademik@unmaris.ac.id
                </div>
            </td>
        </tr>
    </table>

    <div class="judul">KARTU PESERTA UJIAN ({{ $jenis_ujian }})</div>
    <div class="sub-judul">TAHUN AKADEMIK {{ $ta->nama_tahun }}</div>

    <div class="header-container">
        <div class="foto-box">
            Pas Foto 3x4
        </div>
        <div style="width: 70%; float: left;">
            <table class="info-table">
                <tr>
                    <td class="label">Nama Lengkap</td><td class="colon">:</td>
                    <td><strong>{{ strtoupper($mahasiswa->person->nama_lengkap) }}</strong></td>
                </tr>
                <tr>
                    <td class="label">N I M</td><td class="colon">:</td>
                    <td><code>{{ $mahasiswa->nim }}</code></td>
                </tr>
                <tr>
                    <td class="label">Program Studi</td><td class="colon">:</td>
                    <td>{{ $mahasiswa->prodi->jenjang }} - {{ $mahasiswa->prodi->nama_prodi }}</td>
                </tr>
                <tr>
                    <td class="label">Program Kelas</td><td class="colon">:</td>
                    <td>{{ $mahasiswa->programKelas->nama_program ?? '-' }}</td>
                </tr>
            </table>
        </div>
        <div class="clear"></div>
    </div>

    <table class="table-data">
        <thead>
            <tr>
                <th width="30">No</th>
                <th width="110">Hari, Tanggal</th>
                <th width="80">Waktu</th>
                <th>Mata Kuliah / SKS</th>
                <th width="60">Ruang</th>
                <th width="80">Tanda Tangan Pengawas</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pesertaUjians as $index => $peserta)
                @php
                    $ujian = $peserta->jadwalUjian;
                    $mk = $ujian->jadwalKuliah->mataKuliah;
                @endphp
            <tr>
                <td>{{ $index + 1 }}</td>
                <td class="text-left" style="font-size: 10px;">{{ \Carbon\Carbon::parse($ujian->tanggal_ujian)->isoFormat('dddd, D MMM Y') }}</td>
                <td style="font-size: 10px;">{{ substr($ujian->jam_mulai, 0, 5) }} - {{ substr($ujian->jam_selesai, 0, 5) }}</td>
                <td class="text-left">
                    <strong style="font-size: 10px; text-transform: uppercase;">{{ $mk->nama_mk }}</strong><br>
                    <span style="font-size: 8px;">{{ $mk->kode_mk }} ({{ $mk->sks_default }} SKS)</span>
                </td>
                <td style="font-size: 10px;">R. {{ $ujian->ruang->kode_ruang ?? 'TBA' }}</td>
                <td></td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="padding: 30px; color: #999;">Belum ada jadwal ujian {{ $jenis_ujian }} yang diterbitkan untuk mata kuliah yang Anda ambil.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer-container">
        <div class="aturan-box">
            <strong>TATA TERTIB PESERTA UJIAN:</strong>
            <ul>
                <li>Hadir 15 menit sebelum ujian dimulai.</li>
                <li>Wajib membawa Kartu Ujian ini & KTM asli.</li>
                <li>Mengenakan kemeja putih & celana/rok hitam (Almamater).</li>
                <li>Segala bentuk kecurangan (mencontek) akan diberikan sanksi nilai E.</li>
            </ul>
        </div>

        <div class="ttd-box">
            <p>Kota Kupang, {{ now()->isoFormat('D MMMM Y') }}</p>
            <p>Ketua Program Studi,</p>
            <div class="ttd-space"></div>
            <p><strong style="text-decoration: underline;">{{ $kaProdi->nama ?? '..................................' }}</strong></p>
            <p>NIDN. {{ $kaProdi->identitas ?? '-' }}</p>
        </div>
        <div class="clear"></div>
    </div>
</body>
</html>