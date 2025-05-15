<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Rekam Medis - {{ $rekamMedis->No_Rekam_Medis }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        .logo {
            font-size: 18px;
            font-weight: bold;
        }
        .title {
            font-size: 16px;
            font-weight: bold;
            margin: 10px 0;
        }
        .subtitle {
            font-size: 14px;
        }
        .section {
            margin-bottom: 15px;
        }
        .section-title {
            font-weight: bold;
            margin-bottom: 5px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 3px;
        }
        .info-row {
            display: flex;
            margin-bottom: 5px;
        }
        .info-label {
            width: 150px;
            font-weight: bold;
        }
        .info-value {
            flex: 1;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
        }
        .page-break {
            page-break-after: always;
        }
        @media print {
            body {
                padding: 0;
                margin: 0;
            }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <div class="logo">SEHATI</div>
        <div class="title">SISTEM KESEHATAN TERPADU SEKOLAH</div>
        <div class="subtitle">Rekam Medis Siswa</div>
    </div>
    
    <div class="section">
        <div class="section-title">Data Rekam Medis</div>
        <div class="info-row">
            <div class="info-label">No. Rekam Medis</div>
            <div class="info-value">: {{ $rekamMedis->No_Rekam_Medis }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Tanggal & Waktu</div>
            <div class="info-value">: {{ \Carbon\Carbon::parse($rekamMedis->Tanggal_Jam)->format('d M Y H:i') }}</div>
        </div>
    </div>
    
    <div class="section">
        <div class="section-title">Data Siswa</div>
        <div class="info-row">
            <div class="info-label">ID Siswa</div>
            <div class="info-value">: {{ $rekamMedis->Id_Siswa }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Nama Siswa</div>
            <div class="info-value">: {{ $rekamMedis->siswa->Nama_Siswa ?? 'N/A' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Jenis Kelamin</div>
            <div class="info-value">: 
                @if(isset($rekamMedis->siswa->Jenis_Kelamin))
                    {{ $rekamMedis->siswa->Jenis_Kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}
                @else
                    N/A
                @endif
            </div>
        </div>
        <div class="info-row">
            <div class="info-label">Tanggal Lahir</div>
            <div class="info-value">: 
                @if(isset($rekamMedis->siswa->Tanggal_Lahir))
                    {{ \Carbon\Carbon::parse($rekamMedis->siswa->Tanggal_Lahir)->format('d M Y') }}
                @else
                    N/A
                @endif
            </div>
        </div>
    </div>
    
    <div class="section">
        <div class="section-title">Data Dokter</div>
        <div class="info-row">
            <div class="info-label">ID Dokter</div>
            <div class="info-value">: {{ $rekamMedis->Id_Dokter }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Nama Dokter</div>
            <div class="info-value">: {{ $rekamMedis->dokter->Nama_Dokter ?? 'N/A' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Spesialisasi</div>
            <div class="info-value">: {{ $rekamMedis->dokter->Spesialisasi ?? 'N/A' }}</div>
        </div>
    </div>
    
    <div class="section">
        <div class="section-title">Informasi Rekam Medis</div>
        
        <div class="info-row">
            <div class="info-label">Keluhan Utama</div>
            <div class="info-value">: {{ $rekamMedis->Keluhan_Utama ?? 'Tidak ada data' }}</div>
        </div>
        
        <div class="info-row">
            <div class="info-label">Riwayat Penyakit Sekarang</div>
            <div class="info-value">: {{ $rekamMedis->Riwayat_Penyakit_Sekarang ?? 'Tidak ada data' }}</div>
        </div>
        
        <div class="info-row">
            <div class="info-label">Riwayat Penyakit Dahulu</div>
            <div class="info-value">: {{ $rekamMedis->Riwayat_Penyakit_Dahulu ?? 'Tidak ada data' }}</div>
        </div>
        
        <div class="info-row">
            <div class="info-label">Riwayat Imunisasi</div>
            <div class="info-value">: {{ $rekamMedis->Riwayat_Imunisasi ?? 'Tidak ada data' }}</div>
        </div>
        
        <div class="info-row">
            <div class="info-label">Riwayat Penyakit Keluarga</div>
            <div class="info-value">: {{ $rekamMedis->Riwayat_Penyakit_Keluarga ?? 'Tidak ada data' }}</div>
        </div>
        
        <div class="info-row">
            <div class="info-label">Silsilah Keluarga</div>
            <div class="info-value">: {{ $rekamMedis->Silsilah_Keluarga ?? 'Tidak ada data' }}</div>
        </div>
    </div>
    
    @if($detailPemeriksaan->count() > 0)
    <div class="section">
        <div class="section-title">Riwayat Pemeriksaan Terkait</div>
        <table>
            <thead>
                <tr>
                    <th>Tanggal & Waktu</th>
                    <th>Petugas</th>
                    <th>Hasil Pemeriksaan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($detailPemeriksaan as $dp)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($dp->Tanggal_Jam)->format('d M Y H:i') }}</td>
                    <td>
                        @if($dp->Id_Dokter)
                            {{ $dp->dokter->Nama_Dokter ?? 'N/A' }} (Dokter)
                        @elseif($dp->NIP)
                            {{ $dp->petugasUks->Nama_Petugas_UKS ?? 'N/A' }} (Petugas UKS)
                        @else
                            N/A
                        @endif
                    </td>
                    <td>{{ $dp->Hasil_Pemeriksaan }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
    
    <div class="footer">
        <div>Dicetak pada: {{ now()->format('d M Y H:i:s') }}</div>
        <div style="margin-top: 30px;">
            <div>Dokter Penanggung Jawab,</div>
            <div style="height: 60px;"></div>
            <div>( {{ $rekamMedis->dokter->Nama_Dokter ?? '.........................' }} )</div>
        </div>
    </div>
</body>
</html>