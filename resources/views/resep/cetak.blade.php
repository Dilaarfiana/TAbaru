<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resep Obat - {{ $resep->Id_Resep ?? 'RCP-001' }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            font-size: 14px;
            line-height: 1.6;
            color: #333;
            background: #fff;
            padding: 20px;
        }
        
        .container {
            max-width: 210mm;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        /* Access Control Styles */
        .access-control-banner {
            background: linear-gradient(45deg, #fee2e2, #fef3c7);
            border: 2px solid #f87171;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: bold;
            color: #dc2626;
        }
        
        .access-control-banner.admin {
            background: linear-gradient(45deg, #dbeafe, #eff6ff);
            border-color: #3b82f6;
            color: #1d4ed8;
        }
        
        .access-control-banner.petugas {
            background: linear-gradient(45deg, #fef3c7, #fffbeb);
            border-color: #f59e0b;
            color: #d97706;
        }
        
        .access-control-banner.dokter {
            background: linear-gradient(45deg, #d1fae5, #ecfdf5);
            border-color: #10b981;
            color: #047857;
        }
        
        /* Header */
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #3b82f6;
        }
        
        .logo-section {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
        }
        
        .logo {
            width: 60px;
            height: 60px;
            margin-right: 15px;
            border-radius: 8px;
        }
        
        .school-info h1 {
            font-size: 20px;
            color: #2563eb;
            margin-bottom: 5px;
        }
        
        .school-info p {
            font-size: 12px;
            color: #6b7280;
            margin-bottom: 2px;
        }
        
        .doc-title {
            font-size: 18px;
            color: #1f2937;
            font-weight: bold;
            margin-top: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .access-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: bold;
            margin-left: 10px;
            text-transform: uppercase;
            border: 1px solid;
        }
        
        .access-badge.admin {
            background: #dbeafe;
            color: #1e40af;
            border-color: #3b82f6;
        }
        
        .access-badge.petugas {
            background: #fef3c7;
            color: #92400e;
            border-color: #f59e0b;
        }
        
        .access-badge.dokter {
            background: #d1fae5;
            color: #065f46;
            border-color: #10b981;
        }
        
        /* Document Info */
        .doc-info {
            background: #f8fafc;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 25px;
            border-left: 4px solid #3b82f6;
        }
        
        .doc-info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        
        .doc-info strong {
            color: #374151;
        }
        
        /* Main Content */
        .content-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .info-card {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 20px;
            background: #fff;
        }
        
        .info-card h3 {
            color: #3b82f6;
            margin-bottom: 15px;
            font-size: 16px;
            display: flex;
            align-items: center;
        }
        
        .info-card h3::before {
            margin-right: 8px;
            font-size: 18px;
        }
        
        .student-card h3::before {
            content: "👨‍🎓";
        }
        
        .doctor-card h3::before {
            content: "👨‍⚕️";
        }
        
        .info-row {
            display: flex;
            margin-bottom: 8px;
        }
        
        .info-label {
            width: 120px;
            font-weight: 600;
            color: #4b5563;
        }
        
        .info-value {
            flex: 1;
            color: #1f2937;
        }
        
        /* Prescription Box */
        .prescription-box {
            background: linear-gradient(135deg, #fef3c7 0%, #fbbf24 20%, #ffffff 80%);
            border: 2px solid #f59e0b;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 25px;
            text-align: center;
        }
        
        .rx-symbol {
            font-size: 40px;
            color: #d97706;
            margin-bottom: 10px;
        }
        
        .medicine-name {
            font-size: 24px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 20px;
            padding: 15px;
            background: white;
            border-radius: 8px;
            border: 1px solid #d1d5db;
        }
        
        .dosage-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-top: 15px;
            text-align: left;
        }
        
        .dosage-item {
            background: white;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #d1d5db;
        }
        
        .dosage-label {
            font-size: 12px;
            color: #6b7280;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .dosage-value {
            font-size: 16px;
            color: #1f2937;
            font-weight: 600;
        }
        
        /* Instructions */
        .instructions {
            background: #f0f9ff;
            border: 1px solid #bae6fd;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 25px;
        }
        
        .instructions h4 {
            color: #0369a1;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }
        
        .instructions h4::before {
            content: "💡";
            margin-right: 8px;
        }
        
        .instructions ul {
            list-style: none;
            padding-left: 0;
        }
        
        .instructions li {
            margin-bottom: 8px;
            padding-left: 20px;
            position: relative;
            color: #374151;
        }
        
        .instructions li::before {
            content: "•";
            position: absolute;
            left: 0;
            color: #3b82f6;
            font-weight: bold;
        }
        
        /* Footer */
        .footer {
            margin-top: 30px;
            border-top: 1px solid #e5e7eb;
            padding-top: 20px;
        }
        
        .signatures {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-top: 25px;
        }
        
        .signature {
            text-align: center;
            padding: 20px;
            border: 1px dashed #9ca3af;
            border-radius: 8px;
            background: #f9fafb;
        }
        
        .signature-date {
            margin-bottom: 60px;
            color: #4b5563;
        }
        
        .signature-line {
            border-bottom: 1px solid #6b7280;
            width: 150px;
            margin: 0 auto 10px;
        }
        
        .signature-name {
            font-weight: bold;
            color: #1f2937;
        }
        
        .signature-title {
            font-size: 12px;
            color: #6b7280;
        }
        
        .print-info {
            margin-bottom: 20px;
            font-size: 11px;
            color: #6b7280;
            text-align: center;
            padding: 12px;
            background: #f8fafc;
            border-radius: 6px;
            border: 1px solid #e5e7eb;
        }
        
        .access-tracking {
            background: #fff7ed;
            border: 1px solid #fed7aa;
            border-radius: 6px;
            padding: 10px;
            margin-bottom: 15px;
            font-size: 10px;
            text-align: center;
            color: #9a3412;
        }
        
        .access-tracking.admin {
            background: #eff6ff;
            border-color: #bfdbfe;
            color: #1e40af;
        }
        
        .access-tracking.petugas {
            background: #fffbeb;
            border-color: #fde68a;
            color: #92400e;
        }
        
        .access-tracking.dokter {
            background: #ecfdf5;
            border-color: #a7f3d0;
            color: #065f46;
        }
        
        /* Print Styles */
        @media print {
            body {
                padding: 0;
                margin: 0;
            }
            
            .container {
                box-shadow: none;
                padding: 20px;
                border-radius: 0;
            }
            
            .access-control-banner {
                display: none !important;
            }
            
            @page {
                margin: 15mm;
                size: A4;
            }
        }
        
        /* Mobile Responsive */
        @media screen and (max-width: 768px) {
            .container {
                padding: 20px;
            }
            
            .content-grid,
            .dosage-info,
            .signatures {
                grid-template-columns: 1fr;
            }
            
            .logo-section {
                flex-direction: column;
            }
            
            .logo {
                margin-right: 0;
                margin-bottom: 10px;
            }
            
            .doc-title {
                flex-direction: column;
            }
            
            .access-badge {
                margin-left: 0;
                margin-top: 5px;
            }
        }
    </style>
</head>
<body onload="window.print()">
    @php
        $userLevel = session('user_level');
        $isAdmin = $userLevel === 'admin';
        $isPetugas = $userLevel === 'petugas';
        $isDokter = $userLevel === 'dokter';
        $isOrangTua = $userLevel === 'orang_tua';
        
        // Redirect orang tua karena tidak boleh print langsung
        if ($isOrangTua) {
            echo '<script>
                alert("Akses Ditolak: Orang tua tidak dapat mencetak resep secara langsung. Anda akan dialihkan ke halaman riwayat resep anak.");
                window.close(); 
                if (window.opener) {
                    window.opener.location.href = "' . route('orangtua.riwayat.resep') . '";
                } else {
                    window.location.href = "' . route('orangtua.riwayat.resep') . '";
                }
            </script>';
            exit;
        }
        
        // Check if user has permission to print
        if (!in_array($userLevel, ['admin', 'petugas', 'dokter'])) {
            echo '<script>
                alert("Akses Ditolak: Anda tidak memiliki izin untuk mencetak resep.");
                window.close(); 
                if (window.opener) {
                    window.opener.location.href = "' . route('dashboard') . '";
                } else {
                    window.location.href = "' . route('dashboard') . '";
                }
            </script>';
            exit;
        }
        
        // Get user info
        $userName = session('user_name') ?? session('nama_petugas_uks') ?? session('nama_dokter') ?? 'Petugas UKS';
        $userRole = $isAdmin ? 'Administrator' : ($isPetugas ? 'Petugas UKS' : ($isDokter ? 'Dokter' : 'User'));
    @endphp

    <div class="container">
        <!-- Access Control Banner (Only visible on screen) -->
        <div class="access-control-banner {{ $userLevel }}">
            🔐 KONTROL AKSES SISTEM RESEP UKS<br>
            @if($isAdmin)
                <strong>ADMINISTRATOR</strong> - Akses penuh untuk mencetak semua resep obat
            @elseif($isPetugas)
                <strong>PETUGAS UKS</strong> - Akses terbatas untuk mencetak resep (tidak dapat menghapus data)
            @elseif($isDokter)
                <strong>DOKTER</strong> - Akses baca untuk mencetak resep (tidak dapat mengedit data)
            @endif
            | User: {{ $userName }}
        </div>

        <!-- Header -->
        <div class="header">
            <div class="logo-section">
                <img src="https://slbn1bantul.sch.id/wp-content/uploads/2025/02/Logo-Kaliba.png" 
                     alt="Logo Sekolah" class="logo" onerror="this.style.display='none'">
                <div class="school-info">
                    <h1>SLB Negeri 1 Bantul</h1>
                    <p>Unit Kesehatan Sekolah (UKS)</p>
                    <p>Jl. Wates Km. 3, Gulurejo, Lendah, Kulon Progo, DIY</p>
                    <p>Telp: (0274) 798021 | Email: uks@slbn1bantul.sch.id</p>
                </div>
            </div>
            <div class="doc-title">
                🏥 Resep Obat UKS
                <span class="access-badge {{ $userLevel }}">
                    @if($isAdmin)
                        🛡️ Admin
                    @elseif($isPetugas)
                        👤 Petugas
                    @elseif($isDokter)
                        🩺 Dokter
                    @endif
                </span>
            </div>
        </div>
        
        <!-- Access Tracking Info -->
        <div class="access-tracking {{ $userLevel }}">
            <strong>📋 JEJAK AKSES:</strong> 
            Dokumen ini dicetak oleh <strong>{{ $userName }}</strong> dengan level akses 
            @if($isAdmin)
                <strong>ADMINISTRATOR</strong> 
            @elseif($isPetugas)
                <strong>PETUGAS UKS</strong>
            @elseif($isDokter)
                <strong>DOKTER</strong>
            @endif
            pada {{ date('d F Y, H:i:s') }} WIB
        </div>
        
        <!-- Document Info -->
        <div class="doc-info">
            <div class="doc-info-grid">
                <div>
                    <strong>No. Resep:</strong> {{ $resep->Id_Resep ?? 'RCP-' . date('Ymd') . '-001' }}
                </div>
                <div>
                    <strong>Tanggal Resep:</strong> 
                    @if(isset($resep->Tanggal_Resep))
                        {{ \Carbon\Carbon::parse($resep->Tanggal_Resep)->locale('id')->translatedFormat('d F Y') }}
                    @else
                        {{ date('d F Y') }}
                    @endif
                </div>
                <div>
                    <strong>Waktu Cetak:</strong> {{ date('H:i') }} WIB
                </div>
                <div>
                    <strong>Status:</strong> <span style="color: #059669; font-weight: bold;">✓ Resep Aktif</span>
                </div>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="content-grid">
            <!-- Student Info -->
            <div class="info-card student-card">
                <h3>Data Siswa</h3>
                <div class="info-row">
                    <span class="info-label">ID Siswa:</span>
                    <span class="info-value">{{ $resep->Id_Siswa ?? 'STD-2024001' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Nama:</span>
                    <span class="info-value">{{ $resep->siswa->nama_siswa ?? 'Ahmad Rizki Pratama' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Kelas:</span>
                    <span class="info-value">
                        @if(isset($resep->siswa->detailSiswa->kelas->Nama_Kelas))
                            {{ $resep->siswa->detailSiswa->kelas->Nama_Kelas }}
                        @else
                            XII IPA 1
                        @endif
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Jenis Kelamin:</span>
                    <span class="info-value">
                        @if(isset($resep->siswa->jenis_kelamin))
                            {{ $resep->siswa->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}
                        @else
                            Laki-laki
                        @endif
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Tanggal Lahir:</span>
                    <span class="info-value">
                        @if(isset($resep->siswa->tanggal_lahir))
                            {{ \Carbon\Carbon::parse($resep->siswa->tanggal_lahir)->locale('id')->translatedFormat('d F Y') }}
                            ({{ \Carbon\Carbon::parse($resep->siswa->tanggal_lahir)->age }} tahun)
                        @else
                            15 Januari 2010 (15 tahun)
                        @endif
                    </span>
                </div>
            </div>
            
            <!-- Doctor Info -->
            <div class="info-card doctor-card">
                <h3>Data Dokter</h3>
                <div class="info-row">
                    <span class="info-label">Nama Dokter:</span>
                    <span class="info-value">{{ $resep->dokter->Nama_Dokter ?? 'dr. Siti Nurhaliza' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Spesialisasi:</span>
                    <span class="info-value">{{ $resep->dokter->Spesialisasi ?? 'Dokter Umum' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">No. Telepon:</span>
                    <span class="info-value">{{ $resep->dokter->No_Telp ?? '(0274) 798021' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Alamat:</span>
                    <span class="info-value">{{ $resep->dokter->Alamat ?? 'Klinik UKS SLB Negeri 1 Bantul' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Status:</span>
                    <span class="info-value">
                        @if(isset($resep->dokter->status_aktif) && $resep->dokter->status_aktif == 1)
                            <span style="color: #059669;">✓ Aktif</span>
                        @else
                            <span style="color: #dc2626;">✗ Tidak Aktif</span>
                        @endif
                    </span>
                </div>
            </div>
        </div>
        
        <!-- Prescription -->
        <div class="prescription-box">
            <div class="rx-symbol">℞</div>
            <div class="medicine-name">
                {{ $resep->Nama_Obat ?? 'Paracetamol 500mg' }}
            </div>
            <div class="dosage-info">
                <div class="dosage-item">
                    <div class="dosage-label">💊 Dosis & Aturan Pakai</div>
                    <div class="dosage-value">{{ $resep->Dosis ?? '3x1 tablet sehari setelah makan' }}</div>
                </div>
                <div class="dosage-item">
                    <div class="dosage-label">⏰ Durasi Pengobatan</div>
                    <div class="dosage-value">{{ $resep->Durasi ?? '5 hari' }}</div>
                </div>
            </div>
        </div>
        
        <!-- Instructions -->
        <div class="instructions">
            <h4>Petunjuk Penggunaan & Peringatan</h4>
            <ul>
                <li>Minum obat sesuai dosis yang ditentukan, jangan menambah atau mengurangi dosis</li>
                <li>Konsumsi setelah makan untuk menghindari iritasi lambung</li>
                <li>Jangan berhenti minum obat sebelum waktu yang ditentukan meskipun merasa sembuh</li>
                <li>Simpan obat di tempat sejuk, kering, dan terhindar dari sinar matahari langsung</li>
                <li>Jauhkan obat dari jangkauan anak-anak</li>
                <li>Jika mengalami efek samping atau reaksi alergi, segera hentikan penggunaan dan hubungi UKS</li>
                <li>Kembali kontrol ke UKS jika gejala tidak membaik dalam 3 hari</li>
                <li>Informasikan kepada dokter jika sedang mengonsumsi obat lain</li>
            </ul>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <div class="print-info">
                <strong>🖨️ INFORMASI PENCETAKAN:</strong><br>
                Dicetak pada: {{ date('d F Y, H:i:s') }} WIB | 
                Oleh: {{ $userName }} | 
                Level Akses: 
                @if($isAdmin)
                    <span style="color: #1e40af; font-weight: bold;">🛡️ ADMINISTRATOR</span>
                @elseif($isPetugas)
                    <span style="color: #92400e; font-weight: bold;">👤 PETUGAS UKS</span>
                @elseif($isDokter)
                    <span style="color: #065f46; font-weight: bold;">🩺 DOKTER</span>
                @endif
                <br>ID Tracking: {{ $resep->Id_Resep ?? 'RCP-' . date('Ymd') }}-{{ date('His') }}-{{ strtoupper(substr($userLevel, 0, 3)) }}
            </div>
            
            <div class="signatures">
                <div class="signature">
                    <div class="signature-date">
                        Bantul, 
                        @if(isset($resep->Tanggal_Resep))
                            {{ \Carbon\Carbon::parse($resep->Tanggal_Resep)->locale('id')->translatedFormat('d F Y') }}
                        @else
                            {{ date('d F Y') }}
                        @endif
                    </div>
                    <div class="signature-line"></div>
                    <div class="signature-name">{{ $resep->dokter->Nama_Dokter ?? 'dr. Siti Nurhaliza' }}</div>
                    <div class="signature-title">Dokter Penanggung Jawab</div>
                </div>
                
                <div class="signature">
                    <div class="signature-date">Mengetahui,</div>
                    <div class="signature-line"></div>
                    <div class="signature-name">
                        @if($isPetugas)
                            {{ $userName }} 
                            <br><small style="color: #6b7280;">(Pencetak)</small>
                        @else
                            {{ session('nama_petugas_uks') ?? 'Ns. Maria Dewi' }}
                        @endif
                    </div>
                    <div class="signature-title">Petugas UKS</div>
                </div>
            </div>
            
            <div style="text-align: center; margin-top: 20px; font-size: 11px; color: #6b7280; border-top: 1px solid #e5e7eb; padding-top: 15px;">
                📋 Dokumen resmi UKS SLB Negeri 1 Bantul - Untuk informasi lebih lanjut hubungi (0274) 798021<br>
                @if($isAdmin)
                    🛡️ <span style="color: #1e40af; font-weight: bold;">Dicetak dengan akses Administrator - Kontrol penuh sistem</span>
                @elseif($isPetugas)
                    👤 <span style="color: #92400e; font-weight: bold;">Dicetak dengan akses Petugas UKS - Akses terbatas (tidak dapat hapus)</span>
                @elseif($isDokter)
                    🩺 <span style="color: #065f46; font-weight: bold;">Dicetak dengan akses Dokter - Hanya baca (tidak dapat edit/hapus)</span>
                @endif
                <br><small style="color: #9ca3af;">Sistem Informasi Kesehatan Siswa v2.0 | Security Level: {{ strtoupper($userLevel) }}</small>
            </div>
        </div>
    </div>
</body>
</html>