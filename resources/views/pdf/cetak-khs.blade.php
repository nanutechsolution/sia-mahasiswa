<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>KHS - {{ $mahasiswa->nim }}</title>
    <style>
        /* Setup Kertas A4 Portrait */
        @page { margin: 1.2cm 1.5cm; size: a4 portrait; }
        
        body { 
            font-family: 'Times New Roman', Times, serif; 
            font-size: 11px; 
            line-height: 1.4; 
            color: #000; 
        }
        
        /* KOP SURAT */
        .header { 
            text-align: center; 
            margin-bottom: 20px; 
            position: relative; 
            border-bottom: 3px double #000; 
            padding-bottom: 10px; 
        }
        .header h1 { 
            margin: 0; 
            font-size: 16px; 
            text-transform: uppercase; 
            font-weight: bold; 
            color: #002855;
        }
        .header p { 
            margin: 2px 0; 
            font-size: 9px; 
            font-style: italic;
        }
        .logo { 
            position: absolute; 
            left: 0; 
            top: -5px; 
            width: 70px; 
        }

        .main-title { 
            text-align: center; 
            font-size: 14px; 
            font-weight: bold; 
            text-decoration: underline; 
            margin: 20px 0; 
            text-transform: uppercase; 
        }

        /* TABEL INFO MAHASISWA */
        .info-table { width: 100%; margin-bottom: 20px; border-collapse: collapse; }
        .info-table td { padding: 3px 0; vertical-align: top; }
        .label { font-weight: bold; width: 110px; }
        .colon { width: 15px; text-align: center; }

        /* TABEL DATA NILAI */
        .data-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .data-table th, .data-table td { border: 1px solid #000; padding: 6px 4px; }
        .data-table th { 
            background-color: #f2f2f2; 
            font-weight: bold; 
            text-transform: uppercase; 
            font-size: 9px; 
            text-align: center; 
        }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .bold { font-weight: bold; }

        /* SUMMARY & IPS */
        .ips-container { margin-top: 20px; }
        .ips-box { 
            border: 1.5px solid #000; 
            padding: 8px 15px; 
            display: inline-block; 
            font-weight: bold; 
            font-size: 12px;
            background-color: #f9f9f9;
        }

        /* SIGNATURE */
        .footer-area { margin-top: 40px; width: 100%; page-break-inside: avoid; }
        .sig-right { float: right; width: 230px; text-align: center; }
        .sig-space { height: 75px; }
        .clear { clear: both; }
    </style>
</head>
<body>

    <div class="header">
        @php $logoPath = public_path('logo.png'); @endphp
        @if(file_exists($logoPath))
            <img src="{{ $logoPath }}" class="logo">
        @endif
        <h1>UNIVERSITAS STELLA MARIS SUMBA</h1>
        <p>Gedung Rektorat: Jalan Karya Kasih No. 5 Tambolaka, Sumba Barat Daya, NTT.</p>
        <p>Situs Resmi: www.unmarissumba.ac.id | Email: akademik@unmarissumba.ac.id</p>
    </div>
    
    <div class="main-title">KARTU HASIL STUDI (KHS)</div>

    <table class="info-table">
        <tr>
            <td class="label">Nama Mahasiswa</td><td class="colon">:</td>
            <td width="35%"><span class="bold">{{ strtoupper($mahasiswa->person->nama_lengkap ?? $mahasiswa->nama_lengkap) }}</span></td>
            <td class="label">NIM</td><td class="colon">:</td>
            <td><code>{{ $mahasiswa->nim }}</code></td>
        </tr>
        <tr>
            <td class="label">Program Studi</td><td class="colon">:</td>
            <td>{{ $mahasiswa->prodi->nama_prodi }}</td>
            <td class="label">Periode</td><td class="colon">:</td>
            <td>{{ $ta->kode_tahun }} ({{ $ta->nama_tahun }})</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th width="4%" rowspan="2">No</th>
                <th width="14%" rowspan="2">Kode MK</th>
                <th rowspan="2">Nama Mata Kuliah</th>
                <th width="7%" rowspan="2">SKS</th>
                <th colspan="3">Nilai</th>
                <th width="10%" rowspan="2">Mutu<br>(K x N)</th>
            </tr>
            <tr>
                <th width="8%">Angka</th>
                <th width="6%">Huruf</th>
                <th width="8%">Indeks</th>
            </tr>
        </thead>
        <tbody>
            @forelse($details as $index => $row)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                {{-- PERBAIKAN: Menggunakan kolom snapshot agar data tidak null --}}
                <td class="text-center bold">{{ $row->kode_mk_snapshot ?? $row->kode_mk }}</td>
                <td class="text-left">{{ $row->nama_mk_snapshot ?? $row->nama_mk }}</td>
                <td class="text-center">{{ $row->sks_snapshot ?? $row->sks_default }}</td>
                <td class="text-center">{{ number_format($row->nilai_angka, 2) }}</td>
                <td class="text-center bold">{{ $row->nilai_huruf }}</td>
                <td class="text-center">{{ number_format($row->nilai_indeks, 2) }}</td>
                <td class="text-center bold">
                    {{ number_format(($row->sks_snapshot ?? $row->sks_default) * $row->nilai_indeks, 2) }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center" style="padding: 20px;">
                    Belum ada nilai yang dipublikasikan untuk semester ini.
                </td>
            </tr>
            @endforelse
        </tbody>
        @if(count($details) > 0)
        <tfoot>
            <tr class="bold" style="background-color: #f2f2f2;">
                <td colspan="3" class="text-center">JUMLAH CAPAIAN SEMESTER</td>
                <td class="text-center">{{ $totalSks }}</td>
                <td colspan="3"></td>
                <td class="text-center">{{ number_format($totalMutu, 2) }}</td>
            </tr>
        </tfoot>
        @endif
    </table>

    @if(count($details) > 0)
    <div class="ips-container">
        <div class="ips-box">
            INDEKS PRESTASI SEMESTER (IPS) : {{ number_format($ips, 2) }}
        </div>
    </div>
    @endif

    <div class="footer-area">
        <div class="sig-right">
            <p>Tambolaka, {{ now()->translatedFormat('d F Y') }}</p>
            <p>Ketua Program Studi,</p>
            <div class="sig-space"></div>
            <p><span class="bold" style="text-decoration:underline;">{{ $kaProdi->nama ?? '..................................' }}</span></p>
            <p>{{ $kaProdi->identitas ?? 'NIDN. .........................' }}</p>
        </div>
        <div class="clear"></div>
    </div>

    <div style="position: fixed; bottom: 0; width: 100%; font-size: 8px; color: #888; text-align: left; border-top: 1px solid #eee; padding-top: 5px;">
        Dokumen ini diterbitkan secara elektronik melalui sistem SIAKAD UNMARIS v4.2 dan dianggap sah tanpa tanda tangan basah selama data terverifikasi di pangkalan data universitas.
    </div>

</body>
</html>