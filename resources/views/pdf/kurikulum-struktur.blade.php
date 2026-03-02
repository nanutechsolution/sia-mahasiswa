<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struktur Kurikulum - {{ $kurikulum->nama_kurikulum }}</title>
    <style>
        @page {
            margin: 1.2cm;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            line-height: 1.4;
            color: #1e293b;
            margin-top: 2.5cm;
            /* Memberi ruang untuk header yang fixed */
            margin-bottom: 1.5cm;
            /* Memberi ruang untuk footer */
        }

        /* Header & Footer Fixed */
        .header {
            position: fixed;
            top: -1cm;
            left: 0;
            right: 0;
            height: 3cm;
            border-bottom: 2px solid #002855;
            padding-bottom: 10px;
        }

        .footer {
            position: fixed;
            bottom: -0.5cm;
            left: 0;
            right: 0;
            height: 50px;
            border-top: 1px solid #e2e8f0;
            padding-top: 5px;
        }

        /* Table Styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .main-table th {
            background-color: #002855;
            color: white;
            text-transform: uppercase;
            font-size: 9px;
            padding: 8px 4px;
            border: 1px solid #001a38;
        }

        .main-table td {
            border: 1px solid #e2e8f0;
            padding: 6px 4px;
            font-size: 10px;
            text-align: center;
        }

        .semester-header {
            background-color: #f1f5f9;
            font-weight: bold;
            text-align: left !important;
            padding-left: 10px !important;
            color: #002855;
            font-size: 11px;
        }

        /* Typography */
        .text-left {
            text-align: left !important;
        }

        .univ-name {
            font-size: 16px;
            font-weight: 900;
            color: #002855;
            text-transform: uppercase;
        }

        .footer-text {
            font-size: 8px;
            color: #64748b;
            line-height: 1.3;
        }

        .alert-text {
            color: #b91c1c;
            font-weight: bold;
        }

        /* Badge Sifat */
        .badge {
            padding: 2px 5px;
            border-radius: 4px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .badge-wajib {
            background-color: #e0f2fe;
            color: #0369a1;
        }

        .badge-pilihan {
            background-color: #fef3c7;
            color: #92400e;
        }

        .page-number:before {
            content: "Halaman " counter(page);
        }
    </style>
</head>

<body>

    <div class="header">
        <table width="100%" style="border: none;">
            <tr>
                <td width="12%" style="border: none;">
                    @if($logoBase64)
                    <img src="{{ $logoBase64 }}" style="width: 70px; height: auto;">
                    @else
                    <div style="width: 70px; height: 70px; background: #f1f5f9;"></div>
                    @endif
                </td>
                <td width="88%" class="text-left" style="border: none; padding-left: 15px; vertical-align: top;">
                    <div class="univ-name">Universitas Stella Maris Sumba</div>
                    <div style="font-size: 13px; font-weight: bold; color: #334155;">{{ $kurikulum->prodi->fakultas->nama_fakultas ?? '-' }}</div>
                    <div style="font-size: 11px; color: #64748b;">
                        Program Studi: {{ $kurikulum->prodi->nama_prodi }}<br>
                        Alamat: Jl. Karya Kasih No.5 Tambolaka, Sumba Barat Daya, NTT
                    </div>
                </td>
            </tr>
        </table>
        <div style="text-align: center; margin-top: 10px;">
            <h3 style="margin: 0; text-transform: uppercase; font-size: 14px;">Struktur Kurikulum: {{ $kurikulum->nama_kurikulum }}</h3>
        </div>
    </div>

    <div class="footer">
        <table width="100%" style="border: none;">
            <tr>
                <td class="footer-text" style="border: none; width: 75%;">
                    <strong>SIAKAD CLOUD - UNMARIS</strong><br>
                    Dokumen ini adalah salinan digital resmi. Jika terdapat perbedaan data,
                    <span class="alert-text">segera melapor ke Bagian Administrasi Akademik (BARA)</span>.
                </td>
                <td align="right" class="footer-text" style="border: none; width: 25%; vertical-align: bottom;">
                    Dicetak: {{ $date }}<br>
                    <script type="text/php">
                        if ( isset($pdf) ) {
                        $font = $fontMetrics->get_font("helvetica", "bold");
                        $pdf->page_text($pdf->get_width() - 85, $pdf->get_height() - 35, "Halaman {PAGE_NUM} dari {PAGE_COUNT}", $font, 8, array(100, 116, 139));
                    }
                </script>
                </td>
            </tr>
        </table>
    </div>

    <table class="main-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="12%">Kode MK</th>
                <th class="text-left">Mata Kuliah</th>
                <th width="4%">T</th>
                <th width="4%">P</th>
                <th width="4%">L</th>
                <th width="6%">SKS</th>
                <th width="8%">Sifat</th>
                <th width="18%">Prasyarat (Min)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($mataKuliahGrouped as $semester => $mks)
            <tr>
                <td colspan="9" class="semester-header">SEMESTER {{ $semester }}</td>
            </tr>
            @php $totalSksSemester = 0; @endphp
            @foreach($mks as $index => $mk)
            @php
            $sks = $mk->pivot->sks_tatap_muka + $mk->pivot->sks_praktek + $mk->pivot->sks_lapangan;
            $totalSksSemester += $sks;

            // Ambil data prasyarat
            $prasyarat = $kurikulum->mataKuliahs->firstWhere('id', $mk->pivot->prasyarat_mk_id);
            @endphp
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td style="font-family: monospace;">{{ $mk->kode_mk }}</td>
                <td class="text-left">{{ $mk->nama_mk }}</td>
                <td>{{ $mk->pivot->sks_tatap_muka }}</td>
                <td>{{ $mk->pivot->sks_praktek }}</td>
                <td>{{ $mk->pivot->sks_lapangan }}</td>
                <td style="font-weight: bold;">{{ $sks }}</td>
                <td>
                    <span class="badge {{ $mk->pivot->sifat_mk == 'W' ? 'badge-wajib' : 'badge-pilihan' }}">
                        {{ $mk->pivot->sifat_mk == 'W' ? 'Wajib' : 'Pilihan' }}
                    </span>
                </td>
                <td style="font-size: 8px; color: #b91c1c; text-align: left;">
                    @if($prasyarat)
                    {{ $prasyarat->kode_mk }} ({{ $mk->pivot->min_nilai_prasyarat }})
                    @else
                    <span style="color: #cbd5e1;">-</span>
                    @endif
                </td>
            </tr>
            @endforeach
            <tr style="background-color: #f8fafc; font-weight: bold;">
                <td colspan="6" class="text-right" style="padding-right: 10px;">TOTAL SKS SEMESTER {{ $semester }}</td>
                <td>{{ $totalSksSemester }}</td>
                <td colspan="2"></td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 30px; border: 1px dashed #cbd5e1; padding: 15px; border-radius: 8px;">
        <h4 style="margin: 0 0 8px 0; font-size: 11px; color: #0f172a;">INFORMASI UNTUK MAHASISWA & DOSEN:</h4>
        <ul style="font-size: 9px; color: #475569; padding-left: 15px; margin: 0;">
            <li>Gunakan kolom <strong>Prasyarat</strong> sebagai acuan sebelum mengambil mata kuliah pada semester berikutnya.</li>
            <li>Pastikan total SKS yang Anda ambil tidak melampaui batas maksimal beban studi per semester.</li>
            <li><strong>PENTING:</strong> Jika terdapat ketidaksesuaian data (SKS, Kode MK, atau Prasyarat), mahasiswa diwajibkan segera melapor ke <strong>Program Studi</strong> atau <strong>Bagian Akademik</strong> agar segera dilakukan perbaikan di sistem.</li>
            <li>Dokumen ini adalah panduan akademik resmi Universitas Stella Maris Sumba yang diterbitkan melalui sistem SIAKAD.</li>
        </ul>
    </div>

</body>

</html>