<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Rekap Absensi - {{ $mahasiswa->nim }}</title>
    <style>
        /* Setup Kertas A4 Portrait */
        @page { margin: 1.2cm 1.5cm; size: a4 portrait; }
        
        body { 
            font-family: 'Times New Roman', Times, serif; 
            font-size: 11px; 
            line-height: 1.3; 
            color: #000; 
        }
        
        /* KOP SURAT */
        .kop-table { width: 100%; border-bottom: 3px double #000; margin-bottom: 20px; padding-bottom: 10px; }
        .kop-text { text-align: center; vertical-align: middle; }
        .univ-name { font-size: 16px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; }
        .fakultas-name { font-size: 13px; font-weight: bold; margin: 3px 0; text-transform: uppercase; }
        .address { font-size: 9px; font-style: italic; color: #444; }

        .judul { 
            text-align: center; 
            font-weight: bold; 
            font-size: 13px; 
            text-decoration: underline; 
            margin-bottom: 20px; 
            text-transform: uppercase; 
        }

        /* INFO CONTAINER */
        .info-container { width: 100%; margin-bottom: 25px; }
        .info-table { width: 100%; border-collapse: collapse; }
        .info-table td { vertical-align: top; padding: 2px 0; }
        .label { font-weight: bold; width: 110px; }
        .colon { width: 15px; text-align: center; }

        /* TABEL DATA */
        .table-data { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .table-data th, .table-data td { 
            border: 1px solid #000; 
            padding: 6px 4px; 
            text-align: center; 
            vertical-align: middle; 
        }
        .table-data th { background-color: #f5f5f5; font-weight: bold; text-transform: uppercase; font-size: 9px; }
        .text-left { text-align: left !important; padding-left: 8px !important; }

        /* STATUS BADGES */
        .status-h { color: #059669; font-weight: bold; } /* Green */
        .status-a { color: #dc2626; font-weight: bold; } /* Red */
        .status-other { color: #2563eb; font-weight: bold; } /* Blue for I/S */
        
        /* SUMMARY STATS */
        .stats-box { 
            margin-top: 15px; 
            padding: 10px; 
            background-color: #fafafa; 
            border: 1px solid #eee; 
            font-size: 10px; 
        }

        /* SIGNATURE */
        .footer-area { margin-top: 40px; width: 100%; page-break-inside: avoid; }
        .ttd-box { float: right; width: 230px; text-align: center; }
        .ttd-space { height: 65px; }
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

    <div class="judul">LAPORAN REKAPITULASI KEHADIRAN MAHASISWA</div>

    <div class="info-container">
        <table class="info-table">
            <tr>
                <td class="label">Nama Mahasiswa</td><td class="colon">:</td>
                <td width="240"><strong>{{ strtoupper($mahasiswa->person->nama_lengkap) }}</strong></td>
                
                <td class="label">Mata Kuliah</td><td class="colon">:</td>
                <td>{{ $jadwal->mataKuliah->nama_mk }}</td>
            </tr>
            <tr>
                <td class="label">NIM</td><td class="colon">:</td>
                <td><code>{{ $mahasiswa->nim }}</code></td>
                
                <td class="label">Kode MK / SKS</td><td class="colon">:</td>
                <td>{{ $jadwal->mataKuliah->kode_mk }} / {{ $jadwal->mataKuliah->sks_default }} SKS</td>
            </tr>
            <tr>
                <td class="label">Program Studi</td><td class="colon">:</td>
                <td>{{ $mahasiswa->prodi->nama_prodi }}</td>
                
                <td class="label">Tim Pengajar</td><td class="colon">:</td>
                <td>
                    @foreach($jadwal->dosens as $d)
                        {{ $d->person->nama_lengkap }}{{ !$loop->last ? ', ' : '' }}
                    @endforeach
                </td>
            </tr>
            <tr>
                <td class="label">Semester / TA</td><td class="colon">:</td>
                <td>{{ $jadwal->tahunAkademik->nama_tahun }}</td>

                <td class="label">Kelas / Ruang</td><td class="colon">:</td>
                <td>{{ $jadwal->nama_kelas }} / R. {{ $jadwal->ruang->kode_ruang ?? 'TBA' }}</td>
            </tr>
        </table>
    </div>

    <table class="table-data">
        <thead>
            <tr>
                <th width="30">No</th>
                <th width="100">Hari, Tanggal</th>
                <th width="40">Pert.</th>
                <th>Materi / Pokok Bahasan Perkuliahan</th>
                <th width="70">Metode</th>
                <th width="60">Status</th>
                <th width="50">Waktu</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sesiList as $sesi)
                @php 
                    $dataAbsen = $absensi[$sesi->id] ?? null;
                    $status = $dataAbsen ? $dataAbsen->status_kehadiran : 'A';
                    $jamCheckIn = $dataAbsen && $dataAbsen->waktu_check_in ? $dataAbsen->waktu_check_in->timezone('Asia/Makassar')->format('H:i') : '-';
                    
                    // Logic untuk label metode validasi
                    $metodeRaw = $dataAbsen->bukti_validasi['method'] ?? $sesi->metode_validasi ?? 'SYS';
                    $metodeLabel = match($metodeRaw) {
                        'GPS', 'GPS_CHECK' => '📍 GPS',
                        'QR', 'QR_CHECK', 'TOKEN' => '🔑 Token',
                        'DARING' => '🌐 Daring',
                        'MANUAL' => '✍️ Manual',
                        'TUGAS' => '📋 Tugas',
                        default => $metodeRaw
                    };
                @endphp
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td class="nowrap">{{ \Carbon\Carbon::parse($sesi->waktu_mulai_realisasi)->isoFormat('dddd, D MMM Y') }}</td>
                <td>{{ $sesi->pertemuan_ke }}</td>
                <td class="text-left" style="font-size: 9px;">{{ $sesi->materi_kuliah ?? '-' }}</td>
                <td style="font-size: 8px;">{{ $metodeLabel }}</td>
                <td>
                    @php
                        $statusText = match($status) {
                            'H' => 'HADIR',
                            'I' => 'IZIN',
                            'S' => 'SAKIT',
                            default => 'ALPHA'
                        };
                        $statusClass = match($status) {
                            'H' => 'status-h',
                            'A' => 'status-a',
                            default => 'status-other'
                        };
                    @endphp
                    <span class="{{ $statusClass }}">{{ $statusText }}</span>
                </td>
                <td>{{ $jamCheckIn }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="padding: 30px; color: #999;">Belum ada riwayat pertemuan perkuliahan yang tercatat.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="stats-box">
        <strong>RINGKASAN KEHADIRAN:</strong><br>
        Total Pertemuan Realisasi: {{ $statistik['total'] }} Sesi | 
        Hadir: {{ $statistik['hadir'] }} | 
        Tidak Hadir (A/I/S): {{ $statistik['total'] - $statistik['hadir'] }} | 
        Persentase Kehadiran: <strong>{{ $statistik['persen'] }}%</strong>
    </div>

    <div class="footer-area">
        <div class="ttd-box">
            <p>Kota Kupang, {{ $tanggal_cetak }}</p>
            <p>Mengetahui,<br>Dosen Koordinator,</p>
            <div class="ttd-space"></div>
            @php
                // Cari dosen koordinator dari relasi Team Teaching
                $koordinator = $jadwal->dosens->where('pivot.is_koordinator', true)->first() ?? $jadwal->dosens->first();
            @endphp
            <p><strong>{{ $koordinator->person->nama_lengkap }}</strong></p>
            <p>NIDN. {{ $koordinator->nidn ?? '-' }}</p>
        </div>
        <div class="clear"></div>
    </div>

    <div style="position: fixed; bottom: 0; width: 100%; font-size: 8px; color: #888; text-align: left; border-top: 1px solid #eee; padding-top: 5px;">
        Dicetak secara otomatis melalui Portal SIAKAD UNMARIS Mobile v4.2. Kejujuran akademik adalah cermin integritas mahasiswa.
    </div>
</body>
</html>