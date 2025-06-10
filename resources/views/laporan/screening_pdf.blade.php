<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Screening Kesehatan - {{ $siswa->nama_siswa ?? 'Nama Siswa' }}</title>
    <style>
        @page {
            size: A4;
            margin: 5cm 4cm 4cm 5cm; /* Margin lebih lebar seperti gambar 1 */
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.6;
            color: #000;
            background: white;
            padding: 0;
            margin: 0;
        }
        
        /* CONTAINER UTAMA */
        .document-container {
            max-width: 21cm;
            margin: 0 auto;
            padding: 2cm;
            background: white;
            min-height: 29.7cm;
        }
        
        /* KOP SURAT - IMPROVED */
        .kop-surat {
            text-align: center;
            border-bottom: 3px solid #000;
            padding-bottom: 20px;
            margin-bottom: 30px;
            position: relative;
            min-height: 120px;
        }
        
        .logo-container {
            position: absolute;
            left: 0;
            top: 0;
            width: 100px;
            height: 100px;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10;
        }
        
        .logo-container img {
            max-width: 90px;
            max-height: 90px;
            width: auto;
            height: auto;
            object-fit: contain;
        }
        
        .kop-text {
            margin-left: 120px;
            margin-right: 0;
            text-align: center;
        }
        
        .kop-text h1 {
            font-size: 16pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 5px;
            line-height: 1.3;
        }
        
        .kop-text h2 {
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 3px;
            line-height: 1.3;
        }
        
        .kop-text h3 {
            font-size: 13pt;
            font-weight: bold;
            margin-bottom: 8px;
            line-height: 1.3;
        }
        
        .kop-text p {
            font-size: 11pt;
            margin-bottom: 3px;
            line-height: 1.4;
        }
        
        /* JUDUL DOKUMEN */
        .judul-dokumen {
            text-align: center;
            margin: 30px 0;
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: underline;
            letter-spacing: 1px;
        }
        
        /* SECTION */
        .section {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }
        
        .section-title {
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 15px;
            padding: 8px 0;
            border-bottom: 2px solid #000;
            letter-spacing: 0.5px;
        }
        
        /* TABLES - IMPROVED */
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 11pt;
        }
        
        .table th,
        .table td {
            padding: 10px;
            text-align: left;
            border: 1px solid #000;
            vertical-align: top;
        }
        
        .table th {
            font-weight: bold;
            text-align: center;
            background-color: #f5f5f5;
        }
        
        .table .label {
            width: 25%;
            font-weight: bold;
            background-color: #f8f8f8;
        }
        
        /* INFO TABLE - untuk data siswa */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 11pt;
        }
        
        .info-table td {
            padding: 8px;
            border: 1px solid #000;
            vertical-align: top;
        }
        
        .info-table .label {
            width: 25%;
            font-weight: bold;
            background-color: #f8f8f8;
        }
        
        /* VITAL SIGNS LAYOUT - IMPROVED */
        .vital-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
            border: 2px solid #000;
        }
        
        .vital-row {
            display: table-row;
        }
        
        .vital-cell {
            display: table-cell;
            width: 25%;
            text-align: center;
            padding: 15px;
            border: 1px solid #000;
            background-color: #f8f8f8;
        }
        
        .vital-label {
            font-weight: bold;
            font-size: 10pt;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .vital-value {
            font-size: 14pt;
            font-weight: bold;
            color: #000;
        }
        
        /* ANTHROPOMETRIC GRID */
        .anthro-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
            border: 2px solid #000;
        }
        
        .anthro-row {
            display: table-row;
        }
        
        .anthro-cell {
            display: table-cell;
            width: 25%;
            text-align: center;
            padding: 15px;
            border: 1px solid #000;
            background-color: #f8f8f8;
        }
        
        /* TEXT AREAS */
        .text-box {
            padding: 15px;
            border: 1px solid #000;
            background-color: #fafafa;
            min-height: 60px;
            margin-bottom: 15px;
            font-size: 11pt;
            line-height: 1.5;
        }
        
        .text-box.empty {
            font-style: italic;
            color: #666;
        }
        
        /* STATUS BADGES */
        .status-badge {
            padding: 4px 12px;
            border: 2px solid #000;
            font-size: 10pt;
            font-weight: bold;
            display: inline-block;
            text-transform: uppercase;
        }
        
        .status-lengkap {
            background-color: #e8f5e8;
            border-color: #2d5a2d;
        }
        
        .status-belum {
            background-color: #f5e8e8;
            border-color: #5a2d2d;
        }
        
        /* BMI INDICATOR */
        .bmi-indicator {
            display: inline-block;
            padding: 4px 12px;
            border: 1px solid #000;
            font-size: 10pt;
            font-weight: bold;
            margin-left: 15px;
            background-color: #f0f0f0;
        }
        
        /* RESEP ITEM */
        .resep-item {
            border: 2px solid #000;
            padding: 15px;
            margin-bottom: 20px;
            background-color: #fafafa;
        }
        
        .resep-header {
            font-weight: bold;
            margin-bottom: 10px;
            font-size: 11pt;
            text-transform: uppercase;
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
        }
        
        .resep-footer {
            text-align: right;
            font-size: 10pt;
            margin-top: 10px;
            font-style: italic;
            border-top: 1px dashed #666;
            padding-top: 8px;
        }
        
        /* SIGNATURE SECTION - IMPROVED */
        .signature-section {
            margin-top: 60px;
            display: table;
            width: 100%;
            page-break-inside: avoid;
        }
        
        .signature-row {
            display: table-row;
        }
        
        .signature-box {
            display: table-cell;
            width: 50%;
            text-align: center;
            padding: 0 30px;
            vertical-align: top;
        }
        
        .signature-line {
            border-bottom: 1px solid #000;
            width: 200px;
            margin: 80px auto 15px auto;
        }
        
        .signature-name {
            font-weight: bold;
            font-size: 12pt;
            margin-bottom: 5px;
        }
        
        .signature-nip {
            font-size: 11pt;
        }
        
        /* FOOTER - IMPROVED */
        .footer {
            position: fixed;
            bottom: 3cm;
            left: 5cm;
            right: 4cm;
            text-align: center;
            font-size: 10pt;
            border-top: 1px solid #000;
            padding-top: 8px;
            background: white;
        }
        
        /* PAGE BREAK */
        .page-break {
            page-break-before: always;
        }
        
        /* PRINT STYLES - IMPROVED */
        @media print {
            .no-print {
                display: none;
            }
            
            body {
                font-size: 11pt;
                margin: 0;
                padding: 0;
            }
            
            .document-container {
                padding: 0;
                margin: 0;
                max-width: none;
            }
            
            .section {
                page-break-inside: avoid;
            }
            
            .page-break {
                page-break-before: always;
            }
            
            /* Pastikan margin konsisten saat print */
            @page {
                margin: 5cm 4cm 4cm 5cm;
            }
        }
        
        /* GENERAL FORMATTING */
        .center {
            text-align: center;
        }
        
        .bold {
            font-weight: bold;
        }
        
        .italic {
            font-style: italic;
        }
        
        .uppercase {
            text-transform: uppercase;
        }
        
        .underline {
            text-decoration: underline;
        }
        
        /* RESPONSIVE IMPROVEMENTS */
        @media screen and (max-width: 768px) {
            .document-container {
                padding: 1cm;
            }
            
            .kop-text {
                margin-left: 0;
                margin-top: 110px;
            }
            
            .logo-container {
                position: relative;
                width: 100%;
                height: 100px;
                margin-bottom: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="document-container">
        <!-- KOP SURAT - IMPROVED VERSION -->
        <div class="kop-surat">
            <div class="logo-container">
                @php
                    // Cek beberapa kemungkinan nama file logo
                    $logoFound = false;
                    $logoBase64 = '';
                    
                    $logoOptions = [
                        'logo_pemprov.jpg',         // logo yang diminta user
                        'logosekolah.png',          // backup option
                        'logoSekolah.png',          // case sensitive alternative  
                        'logo_sekolah.png',         // dengan underscore
                        'logo.png'                  // nama sederhana
                    ];
                    
                    foreach ($logoOptions as $logoFile) {
                        $logoPath = public_path('images/' . $logoFile);
                        
                        if (file_exists($logoPath)) {
                            try {
                                $logoData = file_get_contents($logoPath);
                                if ($logoData !== false && strlen($logoData) > 0) {
                                    // Detect file type for proper base64 encoding
                                    $extension = pathinfo($logoFile, PATHINFO_EXTENSION);
                                    $mimeType = $extension === 'jpg' || $extension === 'jpeg' ? 'image/jpeg' : 'image/png';
                                    $logoBase64 = 'data:' . $mimeType . ';base64,' . base64_encode($logoData);
                                    $logoFound = true;
                                    break;
                                }
                            } catch (\Exception $e) {
                                // Continue ke file berikutnya
                            }
                        }
                    }
                @endphp
                
                @if($logoFound && $logoBase64)
                    {{-- Logo berhasil dimuat --}}
                    <img src="{{ $logoBase64 }}" alt="Logo SLB Negeri 1 Bantul" />
                @else
                    {{-- Logo fallback dengan desain yang lebih profesional --}}
                    <div style="
                        width: 90px; 
                        height: 90px; 
                        border: 3px solid #1e40af; 
                        border-radius: 50%; 
                        display: flex; 
                        align-items: center; 
                        justify-content: center; 
                        font-size: 8pt; 
                        font-weight: bold;
                        text-align: center;
                        background: linear-gradient(45deg, #3b82f6, #1e40af);
                        color: white;
                        font-family: Arial, sans-serif;
                        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
                    ">
                        <div style="line-height: 1.2;">SLB<br>NEGERI<br>1<br>BANTUL</div>
                    </div>
                @endif
            </div>
            <div class="kop-text">
                <h1>PEMERINTAH DAERAH ISTIMEWA YOGYAKARTA</h1>
                <h2>DINAS PENDIDIKAN, PEMUDA DAN OLAHRAGA</h2>
                <h3>SLB NEGERI 1 BANTUL</h3>
                <p><strong>UNIT KESEHATAN SEKOLAH (UKS)</strong></p>
                <p>Alamat: Jalan Wates km 3 No. 147, Sonopakis Lor, Ngestiharjo, Kec. Kasihan, Kabupaten Bantul, Daerah Istimewa Yogyakarta 55182</p>
                <p>Telepon:  (0274) 374410 | Email: slbnegeri1bantul@gmail.com</p>
            </div>
        </div>

        <!-- JUDUL DOKUMEN -->
        <div class="judul-dokumen">
            LAPORAN HASIL SCREENING KESEHATAN SISWA
        </div>

        <!-- INFORMASI SISWA -->
        <div class="section">
            <div class="section-title">
                I. IDENTITAS SISWA
            </div>
            <table class="info-table">
                <tr>
                    <td class="label">Nama Lengkap</td>
                    <td><strong>{{ $siswa->nama_siswa ?? 'Angga' }}</strong></td>
                    <td class="label">Nomor Induk Siswa</td>
                    <td><strong>{{ $siswa->id_siswa ?? '6A25001' }}</strong></td>
                </tr>
                <tr>
                    <td class="label">Tempat, Tanggal Lahir</td>
                    <td>{{ ($siswa->tempat_lahir ?? 'Jogja') . ', ' . (\Carbon\Carbon::parse($siswa->tanggal_lahir ?? '2001-05-03')->format('d F Y')) }}</td>
                    <td class="label">Jenis Kelamin</td>
                    <td>{{ $siswa->jenis_kelamin == 'L' ? 'Laki-laki' : ($siswa->jenis_kelamin == 'P' ? 'Perempuan' : 'Laki-laki') }}</td>
                </tr>
                <tr>
                    <td class="label">Kelas</td>
                    <td>{{ $siswa->detailSiswa->kelas->Nama_Kelas ?? '1' }}</td>
                    <td class="label">Program Keahlian</td>
                    <td>{{ $siswa->detailSiswa->kelas->jurusan->Nama_Jurusan ?? 'Autis' }}</td>
                </tr>
                <tr>
                    <td class="label">Usia Saat Pemeriksaan</td>
                    <td>{{ $siswa->tanggal_lahir ? \Carbon\Carbon::parse($siswa->tanggal_lahir ?? '2001-05-03')->age : '24' }} Tahun</td>
                    <td class="label">Tanggal Pemeriksaan</td>
                    <td><strong>{{ \Carbon\Carbon::parse($detailPemeriksaan->tanggal_jam ?? '2025-05-10')->format('d F Y') }}</strong></td>
                </tr>
                <tr>
                    <td class="label">ID Pemeriksaan</td>
                    <td>{{ $detailPemeriksaan->id_detprx ?? 'DP001' }}</td>
                    <td class="label">Status Pemeriksaan</td>
                    <td>
                        <span class="status-badge {{ ($detailPemeriksaan->status_pemeriksaan ?? 'lengkap') == 'lengkap' ? 'status-lengkap' : 'status-belum' }}">
                            {{ ($detailPemeriksaan->status_pemeriksaan ?? 'lengkap') == 'lengkap' ? 'LENGKAP' : 'BELUM LENGKAP' }}
                        </span>
                    </td>
                </tr>
            </table>
        </div>

        <!-- REKAM MEDIS -->
        @if(isset($rekamMedis) && $rekamMedis)
        <div class="section">
            <div class="section-title">
                II. ANAMNESIS DAN RIWAYAT KESEHATAN
            </div>
            
            <table class="info-table">
                <tr>
                    <td class="label">Nomor Rekam Medis</td>
                    <td><strong>{{ $rekamMedis->No_Rekam_Medis ?? 'RM001' }}</strong></td>
                    <td class="label">Dokter Pemeriksa</td>
                    <td><strong>{{ $rekamMedis->dokter->Nama_Dokter ?? 'Dr. Ahmad Suryadi, Sp.A' }}</strong></td>
                </tr>
                <tr>
                    <td class="label">Spesialisasi</td>
                    <td>{{ $rekamMedis->dokter->Spesialisasi ?? 'Dokter Spesialis Anak' }}</td>
                    <td class="label">Waktu Pemeriksaan</td>
                    <td>{{ \Carbon\Carbon::parse($rekamMedis->Tanggal_Jam ?? '2025-05-10 09:00:00')->format('d F Y, H:i') }} WIB</td>
                </tr>
            </table>
            
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 25%;">Aspek Anamnesis</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Keluhan Utama</strong></td>
                        <td>{{ $rekamMedis->Keluhan_Utama ?? 'Tidak ada keluhan khusus, pemeriksaan rutin kesehatan sekolah' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Riwayat Penyakit Sekarang</strong></td>
                        <td>{{ $rekamMedis->Riwayat_Penyakit_Sekarang ?? 'Kondisi siswa secara umum baik, tidak ada keluhan yang berarti' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Riwayat Penyakit Dahulu</strong></td>
                        <td>{{ $rekamMedis->Riwayat_Penyakit_Dahulu ?? 'Tidak ada riwayat penyakit serius sebelumnya' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Riwayat Imunisasi</strong></td>
                        <td>{{ $rekamMedis->Riwayat_Imunisasi ?? 'Imunisasi lengkap sesuai jadwal yang direkomendasikan' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Riwayat Penyakit Keluarga</strong></td>
                        <td>{{ $rekamMedis->Riwayat_Penyakit_Keluarga ?? 'Tidak ada riwayat penyakit turunan yang signifikan' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        @endif

        <!-- PEMERIKSAAN AWAL & VITAL SIGNS -->
        @if(isset($pemeriksaanAwal) && $pemeriksaanAwal)
        <div class="section">
            <div class="section-title">
                III. PEMERIKSAAN AWAL DAN TANDA VITAL
            </div>
            
            <!-- Vital Signs Grid -->
            <div class="vital-grid">
                <div class="vital-row">
                    <div class="vital-cell">
                        <div class="vital-label">Suhu Tubuh</div>
                        <div class="vital-value">{{ $pemeriksaanAwal->suhu ?? '36.5' }}°C</div>
                    </div>
                    <div class="vital-cell">
                        <div class="vital-label">Nadi</div>
                        <div class="vital-value">{{ $pemeriksaanAwal->nadi ?? '80' }} x/menit</div>
                    </div>
                    <div class="vital-cell">
                        <div class="vital-label">Tekanan Darah</div>
                        <div class="vital-value">{{ $pemeriksaanAwal->tegangan ?? '120/80' }} mmHg</div>
                    </div>
                    <div class="vital-cell">
                        <div class="vital-label">Pernapasan</div>
                        <div class="vital-value">{{ $pemeriksaanAwal->pernapasan ?? '20' }} x/menit</div>
                    </div>
                </div>
            </div>
            
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 25%;">Aspek Pemeriksaan</th>
                        <th>Hasil dan Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Pemeriksaan Umum</strong></td>
                        <td>{{ $pemeriksaanAwal->pemeriksaan ?? 'Kondisi umum baik, kompos mentis, tidak tampak sakit berat' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Keluhan Terdahulu</strong></td>
                        <td>{{ $pemeriksaanAwal->keluhan_dahulu ?? 'Tidak ada keluhan terdahulu yang signifikan' }}</td>
                    </tr>
                    @if($pemeriksaanAwal->status_nyeri ?? false)
                    <tr>
                        <td><strong>Penilaian Nyeri</strong></td>
                        <td>
                            <strong>Skala:</strong> {{ $pemeriksaanAwal->status_nyeri ?? '0' }}/10
                            @if($pemeriksaanAwal->tipe ?? false)
                                <br><strong>Tipe:</strong> {{ $pemeriksaanAwal->tipe }}
                            @endif
                            @if($pemeriksaanAwal->lokasi ?? false)
                                <br><strong>Lokasi:</strong> {{ $pemeriksaanAwal->lokasi }}
                            @endif
                            @if($pemeriksaanAwal->durasi ?? false)
                                <br><strong>Durasi:</strong> {{ $pemeriksaanAwal->durasi }}
                            @endif
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
        @endif

        <!-- PAGE BREAK -->
        @if(isset($pemeriksaanFisik) && $pemeriksaanFisik)
        <div class="page-break"></div>
        
        <!-- PEMERIKSAAN FISIK -->
        <div class="section">
            <div class="section-title">
                IV. PEMERIKSAAN FISIK DAN ANTROPOMETRI
            </div>
            
            <!-- Anthropometric Measurements -->
            <div class="anthro-grid">
                <div class="anthro-row">
                    <div class="anthro-cell">
                        <div class="vital-label">Tinggi Badan</div>
                        <div class="vital-value">{{ $pemeriksaanFisik->tinggi_badan ?? '165' }} cm</div>
                    </div>
                    <div class="anthro-cell">
                        <div class="vital-label">Berat Badan</div>
                        <div class="vital-value">{{ $pemeriksaanFisik->berat_badan ?? '60' }} kg</div>
                    </div>
                    <div class="anthro-cell">
                        <div class="vital-label">Lingkar Kepala</div>
                        <div class="vital-value">{{ $pemeriksaanFisik->lingkar_kepala ?? '54' }} cm</div>
                    </div>
                    <div class="anthro-cell">
                        <div class="vital-label">Lingkar Lengan Atas</div>
                        <div class="vital-value">{{ $pemeriksaanFisik->lingkar_lengan_atas ?? '28' }} cm</div>
                    </div>
                </div>
            </div>
            
            <!-- BMI Calculation -->
            @if(($pemeriksaanFisik->tinggi_badan ?? 165) && ($pemeriksaanFisik->berat_badan ?? 60))
            @php
                $tinggiMeter = ($pemeriksaanFisik->tinggi_badan ?? 165) / 100;
                $bmi = round(($pemeriksaanFisik->berat_badan ?? 60) / ($tinggiMeter * $tinggiMeter), 2);
                
                if ($bmi < 18.5) {
                    $statusBmi = 'Berat Badan Kurang';
                } elseif ($bmi < 25) {
                    $statusBmi = 'Normal';
                } elseif ($bmi < 30) {
                    $statusBmi = 'Berat Badan Berlebih';
                } else {
                    $statusBmi = 'Obesitas';
                }
            @endphp
            
            <table class="info-table" style="margin-bottom: 25px;">
                <tr>
                    <td class="label">Indeks Massa Tubuh (BMI)</td>
                    <td>
                        <strong>{{ $bmi }} kg/m²</strong>
                        <span class="bmi-indicator">{{ $statusBmi }}</span>
                    </td>
                </tr>
            </table>
            @endif
            
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 20%;">Sistem/Organ</th>
                        <th>Hasil Pemeriksaan</th>
                        <th style="width: 15%;">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Kepala dan Leher</strong></td>
                        <td>{{ $pemeriksaanFisik->kepala ?? 'Bentuk dan ukuran normal, tidak ada kelainan yang tampak' }}</td>
                        <td class="center">Normal</td>
                    </tr>
                    <tr>
                        <td><strong>Dada</strong></td>
                        <td>{{ $pemeriksaanFisik->dada ?? 'Bentuk simetris, tidak ada deformitas' }}</td>
                        <td class="center">Normal</td>
                    </tr>
                    <tr>
                        <td><strong>Jantung</strong></td>
                        <td>{{ $pemeriksaanFisik->jantung ?? 'Bunyi jantung I dan II normal, tidak ada murmur' }}</td>
                        <td class="center">Normal</td>
                    </tr>
                    <tr>
                        <td><strong>Paru-paru</strong></td>
                        <td>{{ $pemeriksaanFisik->paru ?? 'Suara napas vesikuler normal, tidak ada ronki atau wheezing' }}</td>
                        <td class="center">Normal</td>
                    </tr>
                    <tr>
                        <td><strong>Abdomen</strong></td>
                        <td>{{ $pemeriksaanFisik->perut ?? 'Datar, supel, tidak ada nyeri tekan, bising usus normal' }}</td>
                        <td class="center">Normal</td>
                    </tr>
                    <tr>
                        <td><strong>Hepar</strong></td>
                        <td>{{ $pemeriksaanFisik->hepar ?? 'Tidak teraba membesar, tidak ada nyeri tekan' }}</td>
                        <td class="center">Normal</td>
                    </tr>
                    <tr>
                        <td><strong>Genitalia</strong></td>
                        <td>{{ $pemeriksaanFisik->anogenital ?? 'Dalam batas normal sesuai usia dan jenis kelamin' }}</td>
                        <td class="center">Normal</td>
                    </tr>
                    <tr>
                        <td><strong>Ekstremitas</strong></td>
                        <td>{{ $pemeriksaanFisik->ekstremitas ?? 'Simetris, tidak ada deformitas, fungsi gerak normal' }}</td>
                        <td class="center">Normal</td>
                    </tr>
                </tbody>
            </table>
            
            @if($pemeriksaanFisik->pemeriksaan_penunjang ?? false)
            <div style="margin-top: 25px;">
                <div class="section-title" style="font-size: 11pt; margin-bottom: 10px;">
                    Pemeriksaan Penunjang
                </div>
                <div class="text-box">{{ $pemeriksaanFisik->pemeriksaan_penunjang }}</div>
            </div>
            @endif
        </div>
        @endif

        <!-- RESEP OBAT -->
        @if(isset($resepObat) && $resepObat->count() > 0)
        <div class="section">
            <div class="section-title">
                V. TERAPI DAN PENGOBATAN
            </div>
            
            @foreach($resepObat as $index => $resep)
            <div class="resep-item">
                <div class="resep-header">
                    Resep {{ $index + 1 }} - Tanggal: {{ \Carbon\Carbon::parse($resep->Tanggal_Resep)->format('d F Y') }}
                </div>
                <table class="info-table" style="margin: 0;">
                    <tr>
                        <td class="label" style="width: 20%;">Nama Obat</td>
                        <td style="width: 30%;"><strong>{{ $resep->Nama_Obat }}</strong></td>
                        <td class="label" style="width: 15%;">Dosis</td>
                        <td><strong>{{ $resep->Dosis }}</strong></td>
                    </tr>
                    <tr>
                        <td class="label">Durasi Pengobatan</td>
                        <td colspan="3"><strong>{{ $resep->Durasi }}</strong></td>
                    </tr>
                </table>
                <div class="resep-footer">
                    Diresepkan oleh: <strong>{{ $resep->dokter->Nama_Dokter ?? 'Dr. Ahmad Suryadi' }}</strong>
                    {{ $resep->dokter->Spesialisasi ? '(' . $resep->dokter->Spesialisasi . ')' : '(Sp.A)' }}
                </div>
            </div>
            @endforeach
        </div>
        @endif

        <!-- KESIMPULAN DAN REKOMENDASI -->
        <div class="section">
            <div class="section-title">
                VI. KESIMPULAN DAN REKOMENDASI
            </div>
            
            @php
                $statusKesehatan = 'Baik';
                $statusClass = 'status-lengkap';
                $rekomendasi = 'Lanjutkan pola hidup sehat dengan gizi seimbang, olahraga teratur, dan istirahat cukup';
                $kontrolUlang = 'Screening kesehatan rutin 6 bulan mendatang';
                
                // Tentukan status kesehatan
                if(isset($pemeriksaanFisik) && ($pemeriksaanFisik->masalah_aktif ?? false)) {
                    $statusKesehatan = 'Perlu Perhatian Khusus';
                    $statusClass = 'status-belum';
                }
                if(isset($rekamMedis) && ($rekamMedis->Keluhan_Utama ?? false)) {
                    $statusKesehatan = 'Ada Keluhan';
                    $statusClass = 'status-belum';
                }
                
                // Set rekomendasi
                if(isset($pemeriksaanFisik) && ($pemeriksaanFisik->rencana_medis_dan_terapi ?? false)) {
                    $rekomendasi = $pemeriksaanFisik->rencana_medis_dan_terapi;
                }
                
                if(isset($resepObat) && $resepObat && $resepObat->count() > 0) {
                    $kontrolUlang = 'Kontrol rutin sesuai jadwal pengobatan dan konsumsi obat sesuai petunjuk dokter';
                }
            @endphp
            
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 25%;">Aspek Penilaian</th>
                        <th>Hasil dan Rekomendasi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Status Kesehatan Umum</strong></td>
                        <td>
                            <span class="status-badge {{ $statusClass }}">
                                {{ strtoupper($statusKesehatan) }}
                            </span>
                            @if($statusKesehatan == 'Baik')
                                - Kondisi kesehatan siswa secara umum dalam batas normal dan tidak ditemukan kelainan yang signifikan.
                            @elseif($statusKesehatan == 'Perlu Perhatian Khusus')
                                - Terdapat kondisi kesehatan yang memerlukan perhatian dan tindak lanjut medis.
                            @else
                                - Ditemukan keluhan yang perlu evaluasi dan penanganan lebih lanjut.
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Rekomendasi Tindak Lanjut</strong></td>
                        <td>{{ $rekomendasi }}</td>
                    </tr>
                    <tr>
                        <td><strong>Jadwal Kontrol</strong></td>
                        <td>{{ $kontrolUlang }}</td>
                    </tr>
                    <tr>
                        <td><strong>Keterangan Khusus</strong></td>
                        <td>
                            @if(isset($pemeriksaanAwal) && ($pemeriksaanAwal->suhu ?? 36.5) >= 37.5)
                                • Ditemukan peningkatan suhu tubuh yang perlu pemantauan.<br>
                            @endif
                            @if(isset($pemeriksaanFisik) && ($pemeriksaanFisik->tinggi_badan ?? 165) && ($pemeriksaanFisik->berat_badan ?? 60))
                                @php
                                    $tinggiMeter = ($pemeriksaanFisik->tinggi_badan ?? 165) / 100;
                                    $bmi = round(($pemeriksaanFisik->berat_badan ?? 60) / ($tinggiMeter * $tinggiMeter), 2);
                                @endphp
                                @if($bmi < 18.5 || $bmi >= 25)
                                    • Status gizi memerlukan perhatian khusus (BMI: {{ $bmi }} kg/m²).<br>
                                @endif
                            @endif
                            Siswa dapat melanjutkan aktivitas pembelajaran normal dengan tetap memperhatikan anjuran kesehatan yang diberikan.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- TANDA TANGAN -->
        <div class="signature-section">
            <div class="signature-row">
                <div class="signature-box">
                    <p style="margin-bottom: 20px;"><strong>Mengetahui,</strong></p>
                    <p style="margin-bottom: 20px;"><strong>Petugas UKS</strong></p>
                    <div class="signature-line"></div>
                    <p class="signature-name">{{ $detailPemeriksaan->petugasUks->nama_petugas_uks ?? 'Sari Dewi, S.Kep' }}</p>
                    <p class="signature-nip">NIP. {{ $detailPemeriksaan->nip ?? '197803052006042001' }}</p>
                </div>
                <div class="signature-box">
                    <p style="margin-bottom: 20px;"><strong>Dokter Pemeriksa,</strong></p>
                    <p style="margin-bottom: 20px;">&nbsp;</p>
                    <div class="signature-line"></div>
                    <p class="signature-name">{{ $detailPemeriksaan->dokter->Nama_Dokter ?? 'Dr. Ahmad Suryadi, Sp.A' }}</p>
                    <p class="signature-nip">{{ $detailPemeriksaan->dokter->Spesialisasi ?? 'Dokter Spesialis Anak' }}</p>
                </div>
            </div>
        </div>

        <!-- FOOTER -->
        <div class="footer">
            <p>Dokumen ini digenerate secara otomatis pada {{ \Carbon\Carbon::now()->format('d F Y, H:i') }} WIB</p>
            <p style="margin-top: 3px;">SLB Negeri 1 Bantul - Unit Kesehatan Sekolah | Sistem Informasi Kesehatan (SIHATI)</p>
        </div>
    </div>
</body>
</html>