<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} - Perpustakaan Universitas Siber Indonesia</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Times+New+Roman&display=swap" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            color: #1e293b;
            background-color: #f1f5f9;
            font-size: 11pt;
            line-height: 1.4;
            padding: 24px 0;
        }

        .paper-container {
            max-width: {{ $orientation === 'portrait' ? '210mm' : '297mm' }};
            min-height: {{ $orientation === 'portrait' ? '297mm' : '210mm' }};
            margin: 0 auto;
            background: #ffffff;
            padding: 18mm 18mm 22mm 18mm;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border-radius: 4px;
            position: relative;
        }

        /* Top Action Bar for screen view */
        .no-print-bar {
            position: sticky;
            top: 0;
            z-index: 100;
            background: rgba(15, 23, 42, 0.95);
            backdrop-filter: blur(8px);
            color: #ffffff;
            padding: 12px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            margin-bottom: 24px;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .bar-title {
            font-size: 14px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .bar-badge {
            background: #0284c7;
            padding: 2px 10px;
            border-radius: 9999px;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
        }

        .action-buttons {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: all 0.2s ease;
        }

        .btn-print {
            background: #0284c7;
            color: #ffffff;
        }
        .btn-print:hover {
            background: #0369a1;
        }

        .btn-close {
            background: #334155;
            color: #cbd5e1;
        }
        .btn-close:hover {
            background: #475569;
            color: #ffffff;
        }

        /* Official Letterhead (Kop Surat) */
        .kop-surat {
            display: flex;
            align-items: center;
            border-bottom: 3px double #0f172a;
            padding-bottom: 12px;
            margin-bottom: 18px;
        }

        .kop-logo {
            width: 72px;
            height: 72px;
            object-fit: contain;
            margin-right: 18px;
        }

        .kop-text {
            flex: 1;
            text-align: center;
        }

        .kop-inst {
            font-size: 15pt;
            font-weight: bold;
            color: #0f172a;
            letter-spacing: 0.5px;
        }

        .kop-dept {
            font-size: 13pt;
            font-weight: bold;
            color: #0284c7;
            margin: 2px 0;
            letter-spacing: 0.3px;
        }

        .kop-sub {
            font-size: 8.5pt;
            color: #475569;
            line-height: 1.3;
        }

        /* Document Title */
        .doc-header {
            text-align: center;
            margin-bottom: 18px;
        }

        .doc-title {
            font-size: 14pt;
            font-weight: bold;
            text-decoration: underline;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }

        .doc-sub {
            font-size: 9.5pt;
            color: #475569;
            font-style: italic;
        }

        /* Metadata Box */
        .meta-container {
            width: 100%;
            margin-bottom: 14px;
            font-size: 9.5pt;
        }

        .meta-table {
            border-collapse: collapse;
        }

        .meta-table td {
            padding: 2px 4px;
            border: none;
            vertical-align: top;
        }

        .meta-label {
            font-weight: bold;
            width: 140px;
        }

        /* Data Table */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 8.5pt;
        }

        .data-table th {
            background-color: #f1f5f9;
            color: #0f172a;
            border: 1px solid #334155;
            padding: 8px 6px;
            font-weight: bold;
            text-align: center;
        }

        .data-table td {
            border: 1px solid #475569;
            padding: 6px 6px;
            vertical-align: top;
        }

        .data-table tbody tr:nth-child(even) td {
            background-color: #fafafa;
        }

        /* Signature block */
        .signature-section {
            margin-top: 36px;
            width: 100%;
            display: flex;
            justify-content: flex-end;
            page-break-inside: avoid;
        }

        .signature-box {
            width: 250px;
            text-align: center;
            font-size: 10pt;
        }

        .signature-space {
            height: 70px;
        }

        .signature-name {
            font-weight: bold;
            text-decoration: underline;
        }

        /* Print Media Styles */
        @media print {
            body {
                background: #ffffff;
                padding: 0;
                margin: 0;
            }

            .no-print-bar {
                display: none !important;
            }

            .paper-container {
                box-shadow: none;
                margin: 0;
                padding: 10mm;
                max-width: 100%;
                width: 100%;
            }

            @page {
                size: {{ $orientation === 'portrait' ? 'A4 portrait' : 'A4 landscape' }};
                margin: 10mm;
            }

            .data-table th {
                background-color: #e2e8f0 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>

    <!-- On-screen Control Toolbar -->
    <div class="no-print-bar">
        <div class="bar-title">
            <span class="bar-badge">Cetak / Ekspor PDF</span>
            <span>{{ $title }}</span>
        </div>
        <div class="action-buttons">
            <button onclick="window.print()" class="btn btn-print">
                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Cetak / Simpan PDF</span>
            </button>
            <button onclick="window.close(); if(!window.closed){ window.history.back(); }" class="btn btn-close">
                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                <span>Tutup / Kembali</span>
            </button>
        </div>
    </div>

    <!-- Paper Container -->
    <div class="paper-container">
        
        <!-- Kop Surat -->
        <div class="kop-surat">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="kop-logo">
            <div class="kop-text">
                <div class="kop-inst">UNIVERSITAS SIBER INDONESIA (CYBER UNIVERSITY)</div>
                <div class="kop-dept">UPT PERPUSTAKAAN & PUSAT SUMBER BELAJAR</div>
                <div class="kop-sub">
                    Jl. TB Simatupang No. 6, Pasar Minggu, Jakarta Selatan 12540<br>
                    Website: https://library.cyber-univ.ac.id | Email: perpustakaan@cyber-univ.ac.id | Telp: (021) 7800777
                </div>
            </div>
        </div>

        <!-- Judul Laporan -->
        <div class="doc-header">
            <div class="doc-title">{{ $title }}</div>
            <div class="doc-sub">Dicetak pada tanggal: {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }} WIB</div>
        </div>

        <!-- Informasi Metadata / Filter -->
        @if(!empty($metadata))
        <div class="meta-container">
            <table class="meta-table">
                @foreach($metadata as $k => $v)
                <tr>
                    <td class="meta-label">{{ $k }}</td>
                    <td style="width: 8px;">:</td>
                    <td>{{ $v }}</td>
                </tr>
                @endforeach
            </table>
        </div>
        @endif

        <!-- Tabel Data -->
        <table class="data-table">
            <thead>
                <tr>
                    @foreach($headers as $h)
                    <th>{{ $h }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $row)
                <tr>
                    @foreach(array_values($row) as $cell)
                    <td>{{ $cell ?? '-' }}</td>
                    @endforeach
                </tr>
                @empty
                <tr>
                    <td colspan="{{ count($headers) }}" style="text-align: center; padding: 20px; font-style: italic;">
                        Tidak ada data yang ditemukan sesuai filter yang dipilih.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Tanda Tangan Resmi -->
        <div class="signature-section">
            <div class="signature-box">
                <div>Jakarta, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</div>
                <div>Kepala UPT Perpustakaan,</div>
                <div class="signature-space"></div>
                <div class="signature-name">( _________________________ )</div>
                <div style="font-size: 9pt; color: #475569;">NIP/NIDN. .........................</div>
            </div>
        </div>

    </div>

</body>
</html>
