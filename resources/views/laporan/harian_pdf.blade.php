<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pemeriksaan Harian - {{ $siswa->nama_siswa ?? 'Nama Siswa' }}</title>
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
            line-height: 1.5;
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
        
        /* INFO TABLE - IMPROVED */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 11pt;
        }
        
        .info-table td {
            padding: 10px;
            border: 1px solid #000;
            vertical-align: top;
        }
        
        .info-table .label {
            width: 25%;
            font-weight: bold;
            background-color: #f8f8f8;
        }
        
        /* CONTENT BOX - IMPROVED */
        .content-box {
            border: 2px solid #000;
            padding: 20px;
            margin-bottom: 20px;
            background-color: #fafafa;
            min-height: 150px;
            border-radius: 3px;
        }
        
        .content-box h4 {
            font-size: 12pt;
            font-weight: bold;
            margin-bottom: 15px;
            text-transform: uppercase;
            color: #000;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
        }
        
        .content-text {
            font-size: 11pt;
            line-height: 1.6;
            text-align: justify;
            color: #333;
        }
        
        /* STATUS BADGE - IMPROVED */
        .status-badge {
            padding: 4px 12px;
            border: 2px solid #000;
            font-size: 10pt;
            font-weight: bold;
            display: inline-block;
            text-transform: uppercase;
            background-color: #e8f5e8;
            border-radius: 3px;
        }
        
        .status-badge.inactive {
            background-color: #f5e8e8;
            border-color: #a00;
        }
        
        /* DATE TIME BOX - IMPROVED */
        .datetime-box {
            background: linear-gradient(135deg, #f0f8ff, #e6f3ff);
            border: 2px solid #4a90e2;
            padding: 15px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: bold;
            font-size: 12pt;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
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
        
        /* HIGHLIGHT BOX */
        .highlight-box {
            background-color: #fffbf0;
            border-left: 4px solid #ffb300;
            padding: 15px;
            margin: 15px 0;
            font-style: italic;
        }
        
        /* RECOMMENDATION LIST */
        .recommendation-list {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 15px;
            margin: 10px 0;
            border-radius: 3px;
        }
        
        .recommendation-list ul {
            margin-left: 20px;
            margin-top: 10px;
        }
        
        .recommendation-list li {
            margin-bottom: 8px;
            line-height: 1.4;
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
                    {{-- Logo fallback dengan desain UKS --}}
                    <div style="
                        width: 90px; 
                        height: 90px; 
                        border: 3px solid #dc2626; 
                        border-radius: 50%; 
                        display: flex; 
                        align-items: center; 
                        justify-content: center; 
                        font-size: 8pt; 
                        font-weight: bold;
                        text-align: center;
                        background: linear-gradient(45deg, #ef4444, #dc2626);
                        color: white;
                        font-family: Arial, sans-serif;
                        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
                    ">
                        <div style="line-height: 1.1;">UKS<br>SLB<br>N1<br>BANTUL</div>
                    </div>
                @endif
            </div>
            <div class="kop-text">
                <h1>PEMERINTAH DAERAH ISTIMEWA YOGYAKARTA</h1>
                <h2>DINAS PENDIDIKAN, PEMUDA DAN OLAHRAGA</h2>
                <h3>SLB NEGERI 1 BANTUL</h3>
                <p><strong>UNIT KESEHATAN SEKOLAH (UKS)</strong></p>
                <p>Alamat: Jl. Wates Km. 3, Kadipiro, Kretek, Bantul, D.I. Yogyakarta 55772</p>
                <p>Telepon: (0274) 367014 | Email: info@slbn1bantul.sch.id</p>
            </div>
        </div>

        <!-- JUDUL DOKUMEN -->
        <div class="judul-dokumen">
            LAPORAN PEMERIKSAAN HARIAN SISWA
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
                    <td class="label">Usia</td>
                    <td>{{ $siswa->tanggal_lahir ? \Carbon\Carbon::parse($siswa->tanggal_lahir ?? '2001-05-03')->age : '24' }} Tahun</td>
                    <td class="label">Status Siswa</td>
                    <td>
                        <span class="status-badge {{ ($siswa->status_aktif ?? true) ? '' : 'inactive' }}">
                            {{ ($siswa->status_aktif ?? true) ? 'AKTIF' : 'TIDAK AKTIF' }}
                        </span>
                    </td>
                </tr>
            </table>
        </div>

        <!-- INFORMASI PEMERIKSAAN HARIAN -->
        <div class="section">
            <div class="section-title">
                II. DETAIL PEMERIKSAAN HARIAN
            </div>
            
            <table class="info-table">
                <tr>
                    <td class="label">ID Pemeriksaan</td>
                    <td><strong>{{ $pemeriksaanHarian->Id_Harian ?? 'PH001' }}</strong></td>
                    <td class="label">Petugas UKS</td>
                    <td><strong>{{ $pemeriksaanHarian->petugasUks->nama_petugas_uks ?? 'Sari Dewi, S.Kep' }}</strong></td>
                </tr>
                <tr>
                    <td class="label">NIP Petugas</td>
                    <td>{{ $pemeriksaanHarian->NIP ?? '197803052006042001' }}</td>
                    <td class="label">Tanggal Pemeriksaan</td>
                    <td><strong>{{ \Carbon\Carbon::parse($pemeriksaanHarian->Tanggal_Jam ?? now())->format('d F Y') }}</strong></td>
                </tr>
                <tr>
                    <td class="label">Waktu Pemeriksaan</td>
                    <td>{{ \Carbon\Carbon::parse($pemeriksaanHarian->Tanggal_Jam ?? now())->format('H:i') }} WIB</td>
                    <td class="label">Status Pemeriksaan</td>
                    <td>
                        <span class="status-badge">
                            {{ ($pemeriksaanHarian->Hasil_Pemeriksaan ?? true) ? 'LENGKAP' : 'BELUM LENGKAP' }}
                        </span>
                    </td>
                </tr>
            </table>
            
            <!-- DateTime Box -->
            <div class="datetime-box">
                WAKTU PEMERIKSAAN: {{ \Carbon\Carbon::parse($pemeriksaanHarian->Tanggal_Jam ?? now())->format('l, d F Y - H:i') }} WIB
            </div>
        </div>

        <!-- HASIL PEMERIKSAAN -->
        <div class="section">
            <div class="section-title">
                III. HASIL PEMERIKSAAN HARIAN
            </div>
            
            <div class="content-box">
                <h4>Hasil dan Catatan Pemeriksaan:</h4>
                <div class="content-text">
                    @if($pemeriksaanHarian->Hasil_Pemeriksaan ?? false)
                        {{ $pemeriksaanHarian->Hasil_Pemeriksaan }}
                    @else
                        Siswa hadir dalam kondisi sehat pada pemeriksaan harian hari ini. Tidak ditemukan keluhan atau gejala yang memerlukan penanganan khusus. Suhu tubuh normal, tidak ada tanda-tanda sakit, dan siswa dapat mengikuti aktivitas pembelajaran dengan baik.
                        
                        <br><br><strong>Pemeriksaan Fisik Sederhana:</strong>
                        <br>• Kondisi umum: Baik dan aktif
                        <br>• Suhu tubuh: Normal (36-37°C)
                        <br>• Mata: Tidak ada keluhan penglihatan
                        <br>• Telinga: Tidak ada keluhan pendengaran
                        <br>• Hidung: Tidak ada pilek atau tersumbat
                        <br>• Tenggorokan: Tidak ada keluhan menelan
                        <br>• Kulit: Tidak ada ruam atau luka
                    @endif
                </div>
            </div>
        </div>

        <!-- REKOMENDASI DAN TINDAK LANJUT -->
        <div class="section">
            <div class="section-title">
                IV. REKOMENDASI DAN TINDAK LANJUT
            </div>
            
            <div class="content-box">
                <h4>Rekomendasi Petugas UKS:</h4>
                <div class="content-text">
                    @if(($pemeriksaanHarian->Hasil_Pemeriksaan ?? '') && strlen($pemeriksaanHarian->Hasil_Pemeriksaan ?? '') > 100)
                        Berdasarkan hasil pemeriksaan harian yang telah dilakukan, disarankan untuk:
                        <div class="recommendation-list">
                            <ul>
                                <li>Melanjutkan pemantauan kondisi kesehatan siswa secara berkala</li>
                                <li>Memberikan edukasi kesehatan yang sesuai dengan kondisi siswa</li>
                                <li>Koordinasi dengan orang tua terkait kondisi kesehatan anak di rumah</li>
                                @if(strpos(strtolower($pemeriksaanHarian->Hasil_Pemeriksaan ?? ''), 'sakit') !== false || 
                                    strpos(strtolower($pemeriksaanHarian->Hasil_Pemeriksaan ?? ''), 'keluhan') !== false)
                                    <li><strong>Rujukan ke dokter atau fasilitas kesehatan jika diperlukan</strong></li>
                                @endif
                            </ul>
                        </div>
                    @else
                        Siswa dalam kondisi sehat dan dapat melanjutkan aktivitas pembelajaran normal. 
                        <div class="recommendation-list">
                            <ul>
                                <li>Tetap lakukan pemantauan kesehatan harian</li>
                                <li>Jaga pola hidup sehat dengan istirahat cukup</li>
                                <li>Konsumsi makanan bergizi seimbang</li>
                                <li>Minum air putih yang cukup (8 gelas/hari)</li>
                                <li>Jaga kebersihan tangan dan lingkungan</li>
                                <li>Olahraga ringan sesuai kemampuan</li>
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
            
            <table class="info-table" style="margin-top: 20px;">
                <tr>
                    <td class="label">Tindak Lanjut Diperlukan</td>
                    <td>
                        @if(($pemeriksaanHarian->Hasil_Pemeriksaan ?? '') && 
                            (strpos(strtolower($pemeriksaanHarian->Hasil_Pemeriksaan), 'sakit') !== false || 
                             strpos(strtolower($pemeriksaanHarian->Hasil_Pemeriksaan), 'keluhan') !== false ||
                             strpos(strtolower($pemeriksaanHarian->Hasil_Pemeriksaan), 'demam') !== false))
                            <strong style="color: #dc2626;">YA</strong> - Perlu pemantauan lebih lanjut
                        @else
                            <strong style="color: #16a34a;">TIDAK</strong> - Kondisi normal, lanjutkan aktivitas rutin
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="label">Pemeriksaan Ulang</td>
                    <td>
                        @if(($pemeriksaanHarian->Hasil_Pemeriksaan ?? '') && strlen($pemeriksaanHarian->Hasil_Pemeriksaan) > 50)
                            Dianjurkan dalam <strong>1-2 hari</strong> ke depan untuk memantau perkembangan
                        @else
                            Sesuai jadwal pemeriksaan rutin harian
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="label">Kontak Orang Tua</td>
                    <td>
                        @if(($pemeriksaanHarian->Hasil_Pemeriksaan ?? '') && 
                            (strpos(strtolower($pemeriksaanHarian->Hasil_Pemeriksaan), 'sakit') !== false || 
                             strpos(strtolower($pemeriksaanHarian->Hasil_Pemeriksaan), 'demam') !== false))
                            <strong style="color: #dc2626;">SUDAH DIHUBUNGI</strong> - Untuk informasi kondisi anak
                        @else
                            Tidak perlu kontak khusus, kondisi anak baik
                        @endif
                    </td>
                </tr>
            </table>
        </div>

        <!-- CATATAN TAMBAHAN -->
        <div class="section">
            <div class="section-title">
                V. CATATAN DAN INFORMASI TAMBAHAN
            </div>
            
            <div class="content-box">
                <h4>Catatan Penting:</h4>
                <div class="content-text">
                    <strong>Kondisi Umum Siswa:</strong>
                    @if($pemeriksaanHarian->Hasil_Pemeriksaan ?? false)
                        Berdasarkan catatan pemeriksaan, siswa telah mendapat perhatian khusus dari petugas UKS dan kondisinya terus dipantau untuk memastikan kesehatan yang optimal.
                    @else
                        Siswa dalam kondisi sehat dan tidak memerlukan penanganan medis khusus. Dapat mengikuti seluruh aktivitas pembelajaran dengan normal.
                    @endif
                    
                    <br><br><strong>Aktivitas yang Dianjurkan:</strong>
                    <br>• Tetap mengikuti pembelajaran sesuai jadwal yang telah ditentukan
                    <br>• Menjaga kebersihan diri dan lingkungan sekitar
                    <br>• Mengonsumsi makanan bergizi dan minum air putih yang cukup
                    <br>• Istirahat yang cukup di rumah (tidur 8-9 jam untuk anak)
                    <br>• Melakukan aktivitas fisik ringan sesuai kemampuan
                    
                    <br><br><strong>Hal yang Perlu Dihindari:</strong>
                    <br>• Aktivitas fisik berat jika kondisi kurang fit
                    <br>• Kontak dengan siswa lain yang sedang sakit
                    <br>• Jajan sembarangan di kantin atau luar sekolah
                    <br>• Terlambat makan atau melewatkan waktu makan
                    <br>• Begadang atau kurang tidur
                </div>
            </div>
            
            <div class="highlight-box">
                <strong>Catatan Khusus untuk Orang Tua:</strong><br>
                Mohon tetap memantau kondisi kesehatan anak di rumah dan segera hubungi sekolah jika ada perubahan kondisi atau keluhan baru. Pastikan anak mendapat nutrisi yang cukup dan istirahat yang baik di rumah.
            </div>
        </div>

        <!-- TANDA TANGAN -->
        <div class="signature-section">
            <div class="signature-row">
                <div class="signature-box">
                    <p style="margin-bottom: 15px;"><strong>Mengetahui,</strong></p>
                    <p style="margin-bottom: 15px;"><strong>Kepala Sekolah</strong></p>
                    <div class="signature-line"></div>
                    <p class="signature-name">Drs. Suyanto, M.Pd</p>
                    <p class="signature-nip">NIP. 196801051994031004</p>
                </div>
                <div class="signature-box">
                    <p style="margin-bottom: 15px;"><strong>Petugas UKS,</strong></p>
                    <p style="margin-bottom: 15px;">&nbsp;</p>
                    <div class="signature-line"></div>
                    <p class="signature-name">{{ $pemeriksaanHarian->petugasUks->nama_petugas_uks ?? 'Sari Dewi, S.Kep' }}</p>
                    <p class="signature-nip">NIP. {{ $pemeriksaanHarian->NIP ?? '197803052006042001' }}</p>
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