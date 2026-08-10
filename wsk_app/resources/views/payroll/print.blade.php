<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slip Gaji - {{ $user->name }} - {{ \Carbon\Carbon::parse($selectedMonth . '-01')->translatedFormat('F Y') }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        @page {
            size: A4;
            margin: 20mm 15mm;
        }
        body {
            font-family: 'Courier New', Courier, monospace; /* Classic slip/receipt font for authenticity, or fallback sans-serif */
            font-size: 12px;
            line-height: 1.5;
            color: #111;
            margin: 0;
            padding: 0;
            background: #fff;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .payslip-wrapper {
            max-width: 800px;
            margin: 0 auto;
        }
        /* Header styling */
        .header-table {
            width: 100%;
            border-bottom: 2px double #111;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header-logo {
            width: 60px;
            height: 60px;
            object-fit: contain;
            border-radius: 50%;
            background: #fff;
            border: 1px solid #ddd;
            margin-right: 15px;
        }
        .res-title {
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0;
            letter-spacing: 1px;
        }
        .res-subtitle {
            font-size: 10px;
            color: #555;
            margin: 3px 0 0 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .payslip-title-container {
            text-align: right;
        }
        .payslip-main-title {
            font-size: 18px;
            font-weight: 900;
            margin: 0;
            letter-spacing: 0.5px;
            color: #111;
        }
        .payslip-period {
            font-size: 11px;
            margin: 3px 0 0 0;
            font-weight: bold;
        }

        /* Info meta columns */
        .info-table {
            width: 100%;
            margin-bottom: 25px;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 4px 0;
            vertical-align: top;
        }
        .info-label {
            font-weight: bold;
            width: 130px;
        }
        .info-value {
            color: #333;
        }

        /* Detail/Calculation Table styling */
        .calc-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .calc-table th {
            border-top: 1px solid #111;
            border-bottom: 1px solid #111;
            padding: 8px 10px;
            font-weight: bold;
            text-align: left;
            background-color: #f5f5f5;
        }
        .calc-table td {
            padding: 8px 10px;
            border-bottom: 1px dashed #ddd;
        }
        .text-right {
            text-align: right;
        }
        .font-mono {
            font-family: 'Courier New', Courier, monospace;
            font-weight: bold;
        }
        .net-salary-row td {
            border-top: 1px solid #111;
            border-bottom: 2px double #111;
            font-size: 14px;
            font-weight: bold;
            background-color: #f9f9f9;
            padding: 12px 10px;
        }

        /* Attendance Table details */
        .section-title {
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            border-bottom: 1px solid #111;
            padding-bottom: 5px;
            margin-top: 30px;
            margin-bottom: 12px;
            letter-spacing: 0.5px;
        }
        .attendance-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
            margin-bottom: 35px;
        }
        .attendance-table th {
            border-bottom: 1px solid #111;
            padding: 6px 8px;
            font-weight: bold;
            text-align: left;
        }
        .attendance-table td {
            padding: 6px 8px;
            border-bottom: 1px solid #eee;
        }
        .status-badge-ontime {
            color: #065f46;
            font-weight: bold;
        }
        .status-badge-late {
            color: #9f1239;
            font-weight: bold;
        }
        .status-badge-absent {
            color: #6b7280;
            font-style: italic;
        }

        /* Signatures block */
        .signature-section {
            width: 100%;
            margin-top: 50px;
            page-break-inside: avoid;
        }
        .signature-box {
            width: 45%;
            text-align: center;
        }
        .signature-line {
            margin-top: 65px;
            border-top: 1px solid #111;
            width: 180px;
            margin-left: auto;
            margin-right: auto;
        }
        .signature-name {
            margin-top: 5px;
            font-weight: bold;
        }
        .signature-role {
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="payslip-wrapper">
        
        <!-- Header Info -->
        <table class="header-table">
            <tr>
                <td style="width: 70%; vertical-align: middle;">
                    <div style="display: flex; align-items: center;">
                        @if($imgPath)
                            <img src="{{ $imgPath }}" class="header-logo" alt="Logo">
                        @endif
                        <div>
                            <h1 class="res-title">{{ $resName }}</h1>
                            @if($resLocation)
                                <p class="res-subtitle"><i class="fas fa-map-marker-alt"></i> {{ $resLocation }}</p>
                            @endif
                        </div>
                    </div>
                </td>
                <td style="width: 30%; vertical-align: middle;" class="payslip-title-container">
                    <h2 class="payslip-main-title">SLIP GAJI STAF</h2>
                    <p class="payslip-period">{{ \Carbon\Carbon::parse($selectedMonth . '-01')->translatedFormat('F Y') }}</p>
                </td>
            </tr>
        </table>

        <!-- Employee Info Meta -->
        <table class="info-table">
            <tr>
                <td style="width: 50%;">
                    <table>
                        <tr>
                            <td class="info-label">Nama Karyawan</td>
                            <td>:</td>
                            <td class="info-value"><strong>{{ $user->name }}</strong></td>
                        </tr>
                        <tr>
                            <td class="info-label">Jabatan (Role)</td>
                            <td>:</td>
                            <td class="info-value" style="text-transform: uppercase;">{{ $user->role }}</td>
                        </tr>
                    </table>
                </td>
                <td style="width: 50%;">
                    <table>
                        <tr>
                            <td class="info-label">Tanggal Cetak</td>
                            <td>:</td>
                            <td class="info-value">{{ date('d/m/Y H:i') }} WIB</td>
                        </tr>
                        <tr>
                            <td class="info-label">Total Kehadiran</td>
                            <td>:</td>
                            <td class="info-value"><strong>{{ $totalDaysWorked }} Hari Kerja</strong></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <!-- Salary Computations -->
        <table class="calc-table">
            <thead>
                <tr>
                    <th style="width: 60%;">Deskripsi Komponen Gaji</th>
                    <th style="width: 20%; text-align: right;">Detail</th>
                    <th style="width: 20%; text-align: right;">Jumlah (Rupiah)</th>
                </tr>
            </thead>
            <tbody>
                <!-- 1. Base Salary -->
                <tr>
                    <td>Gaji Pokok Kehadiran Harian</td>
                    <td class="text-right font-mono">{{ $totalDaysWorked }} Hari x Rp {{ number_format($dailySalary, 0, ',', '.') }}</td>
                    <td class="text-right font-mono">Rp {{ number_format($baseSalaryTotal, 0, ',', '.') }}</td>
                </tr>

                <!-- 2. Bonuses -->
                @php $hasBonuses = $adjustments->where('type', 'bonus')->count() > 0; @endphp
                @if($hasBonuses)
                    <tr>
                        <td colspan="3" style="font-weight: bold; background-color: #fafafa; padding-left: 10px;">Penambahan Gaji (Bonus / Insentif):</td>
                    </tr>
                    @foreach($adjustments->where('type', 'bonus') as $bonus)
                        <tr>
                            <td style="padding-left: 25px;">
                                <i class="fas fa-circle-plus" style="color: #10b981; font-size: 8px; margin-right: 5px;"></i>
                                {{ \Carbon\Carbon::parse($bonus->tanggal)->translatedFormat('d M') }} - {{ $bonus->notes ?: 'Bonus Kinerja' }}
                            </td>
                            <td></td>
                            <td class="text-right font-mono" style="color: #047857;">+ Rp {{ number_format($bonus->amount, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                @endif

                <!-- 3. Deductions -->
                @php $hasDeductions = $adjustments->where('type', 'deduction')->count() > 0; @endphp
                @if($hasDeductions)
                    <tr>
                        <td colspan="3" style="font-weight: bold; background-color: #fafafa; padding-left: 10px;">Pengurangan Gaji (Potongan / Denda):</td>
                    </tr>
                    @foreach($adjustments->where('type', 'deduction') as $deduction)
                        <tr>
                            <td style="padding-left: 25px;">
                                <i class="fas fa-circle-minus" style="color: #e11d48; font-size: 8px; margin-right: 5px;"></i>
                                {{ \Carbon\Carbon::parse($deduction->tanggal)->translatedFormat('d M') }} - {{ $deduction->notes ?: 'Potongan Keterlambatan' }}
                            </td>
                            <td></td>
                            <td class="text-right font-mono" style="color: #b91c1c;">- Rp {{ number_format($deduction->amount, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                @endif

                <!-- 4. Total Net Salary Payout -->
                <tr class="net-salary-row">
                    <td><strong>TOTAL GAJI BERSIH DITERIMA</strong></td>
                    <td></td>
                    <td class="text-right font-mono"><strong>Rp {{ number_format($netSalary, 0, ',', '.') }}</strong></td>
                </tr>
            </tbody>
        </table>

        <!-- Detailed Attendance Recap Section -->
        <h3 class="section-title">Detail Riwayat Kehadiran Bulanan</h3>
        <table class="attendance-table">
            <thead>
                <tr>
                    <th style="width: 8%;">No</th>
                    <th style="width: 32%;">Hari, Tanggal</th>
                    <th style="width: 20%;">Jam Masuk (Clock In)</th>
                    <th style="width: 20%;">Jam Pulang (Clock Out)</th>
                    <th style="width: 20%;">Status Kehadiran</th>
                </tr>
            </thead>
            <tbody>
                @if($attendances->count() > 0)
                    @foreach($attendances as $idx => $att)
                        @php
                            $isLate = $att->clock_in > $lateTime;
                            $statusText = "Tepat Waktu";
                            $statusClass = "status-badge-ontime";

                            if ($isLate) {
                                $inTime = new \DateTime($att->clock_in);
                                $limitTime = new \DateTime($lateTime);
                                $interval = $inTime->diff($limitTime);
                                $lateMinutes = ($interval->h * 60) + $interval->i;
                                $statusText = "Terlambat (" . $lateMinutes . "m)";
                                $statusClass = "status-badge-late";
                            }
                        @endphp
                        <tr>
                            <td>{{ $idx + 1 }}</td>
                            <td>{{ \Carbon\Carbon::parse($att->tanggal)->translatedFormat('l, d F Y') }}</td>
                            <td class="font-mono">{{ substr($att->clock_in, 0, 5) }}</td>
                            <td class="font-mono">{{ $att->clock_out ? substr($att->clock_out, 0, 5) : '-' }}</td>
                            <td class="{{ $statusClass }}">{{ $statusText }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 15px 0; color: #888; font-style: italic; font-weight: bold;">
                            Tidak ada catatan riwayat kehadiran dalam periode ini.
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>

        <!-- Signatures Block -->
        <table class="signature-section">
            <tr>
                <td class="signature-box" style="width: 50%;">
                    <p>Penerima Gaji,</p>
                    <div class="signature-line"></div>
                    <p class="signature-name">{{ $user->name }}</p>
                    <p class="signature-role" style="text-transform: uppercase;">Staf {{ $user->role }}</p>
                </td>
                <td class="signature-box" style="width: 50%;">
                    <p>Manajemen Restoran,</p>
                    <div class="signature-line"></div>
                    <p class="signature-name">{{ $resName }}</p>
                    <p class="signature-role">Pihak Berwenang</p>
                </td>
            </tr>
        </table>

    </div>

    <script>
        window.onload = function() {
            window.print();
            setTimeout(function() { window.close(); }, 500);
        }
    </script>
</body>
</html>
