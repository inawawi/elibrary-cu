<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Keterangan Bebas Pustaka - {{ $member->member_id }} - {{ $member->member_name }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            background-color: #f1f5f9;
            color: #111827;
            line-height: 1.6;
            font-size: 12pt;
        }

        /* Screen Action Bar */
        .no-print-bar {
            background-color: #1e293b;
            color: #ffffff;
            padding: 12px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 50;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            font-family: system-ui, -apple-system, sans-serif;
        }

        .no-print-bar .title {
            font-weight: 700;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .no-print-bar .btn {
            background: #0284c7;
            color: #ffffff;
            border: none;
            padding: 8px 18px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 13px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
        }

        .no-print-bar .btn:hover {
            background: #0369a1;
        }

        .no-print-bar .btn-back {
            background: transparent;
            color: #cbd5e1;
            border: 1px solid #475569;
        }

        .no-print-bar .btn-back:hover {
            background: #334155;
            color: #ffffff;
        }

        /* Printable Sheet */
        .sheet-container {
            padding: 30px 15px;
            display: flex;
            justify-content: center;
        }

        .sheet {
            background: #ffffff;
            width: 210mm;
            min-height: 297mm;
            padding: 30mm 25mm 25mm 25mm;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        /* Header / Kop */
        .kop-surat {
            display: flex;
            align-items: center;
            gap: 18px;
            border-bottom: 2px solid #000000;
            padding-bottom: 12px;
            margin-bottom: 30px;
        }

        .kop-logo {
            width: 75px;
            height: 75px;
            object-fit: contain;
        }

        .kop-text {
            flex-grow: 1;
            text-align: center;
        }

        .kop-instansi {
            font-size: 15pt;
            font-weight: 900;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .kop-unit {
            font-size: 13pt;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-top: 2px;
        }

        .kop-alamat {
            font-size: 9pt;
            color: #374151;
            margin-top: 4px;
            font-family: system-ui, -apple-system, sans-serif;
        }

        /* Document Title */
        .doc-title-box {
            text-align: center;
            margin-bottom: 28px;
        }

        .doc-title {
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            line-height: 1.4;
        }

        /* Identity Table */
        .table-identitas {
            width: 100%;
            margin-bottom: 22px;
            border-collapse: collapse;
        }

        .table-identitas td {
            padding: 4px 0;
            font-size: 11pt;
            vertical-align: top;
        }

        .table-identitas .col-label {
            width: 130px;
            font-weight: normal;
        }

        .table-identitas .col-separator {
            width: 20px;
            text-align: center;
        }

        .table-identitas .col-value {
            font-weight: normal;
        }

        /* Narrative */
        .p-text {
            font-size: 11pt;
            text-align: justify;
            margin-bottom: 16px;
            line-height: 1.6;
        }

        /* Statement Box */
        .statement-badge {
            text-align: center;
            font-size: 13pt;
            font-weight: bold;
            letter-spacing: 1px;
            margin: 22px 0;
            text-transform: uppercase;
        }

        /* Issue info */
        .issue-section {
            margin-top: 25px;
            font-size: 10pt;
        }

        .issue-table {
            width: 100%;
            margin-bottom: 6px;
        }

        .issue-table td {
            font-size: 10pt;
            padding: 2px 0;
        }

        .note-verification {
            font-size: 9pt;
            font-style: italic;
            color: #374151;
            margin-top: 4px;
        }

        /* Signature block */
        .signature-section {
            margin-top: 40px;
            display: flex;
            justify-content: flex-end;
            text-align: left;
        }

        .signature-box {
            width: 260px;
            text-align: center;
        }

        .signature-role {
            font-size: 11pt;
            margin-bottom: 75px;
        }

        .signature-space {
            height: 75px;
        }

        .signature-name {
            font-size: 11pt;
            font-weight: bold;
            border-bottom: 1px solid #111827;
            display: inline-block;
            padding: 0 10px;
        }

        .signature-unit {
            font-size: 10pt;
            margin-top: 2px;
        }

        /* Print Media Styles */
        @media print {
            body {
                background: #ffffff !important;
                color: #000000 !important;
            }

            .no-print-bar {
                display: none !important;
            }

            .sheet-container {
                padding: 0 !important;
                margin: 0 !important;
            }

            .sheet {
                width: 100% !important;
                min-height: auto !important;
                box-shadow: none !important;
                padding: 20mm 20mm 20mm 20mm !important;
                page-break-after: avoid;
            }

            @page {
                size: A4 portrait;
                margin: 0;
            }
        }
    </style>
</head>
<body>

    <!-- On-screen Navigation & Actions -->
    <div class="no-print-bar">
        <div class="title">
            <a href="{{ route('member.skripsi') }}" class="btn btn-back">
                &larr; Kembali ke Formulir
            </a>
            <span>Pratinjau Surat Keterangan Bebas Pustaka</span>
        </div>
        <div style="display: flex; gap: 10px;">
            <button onclick="window.print()" class="btn">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <polyline points="6 9 6 2 18 2 18 9"></polyline>
                    <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                    <rect x="6" y="14" width="12" height="8"></rect>
                </svg>
                <span>Cetak / Unduh PDF</span>
            </button>
        </div>
    </div>

    <!-- Printable A4 Sheet -->
    <div class="sheet-container">
        <div class="sheet">
            <div>
                <!-- Header / Kop Surat -->
                <div class="kop-surat">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo Universitas Siber Indonesia" class="kop-logo">
                    <div class="kop-text">
                        <div class="kop-instansi">UNIVERSITAS SIBER INDONESIA</div>
                        <div class="kop-unit">PERPUSTAKAAN & PUSAT SUMBER BELAJAR</div>
                        <div class="kop-alamat">Jl. TB Simatupang No. 38, Jakarta Selatan 12540 | Website: https://cyber-univ.ac.id | Email: perpustakaan@cyber-univ.ac.id</div>
                    </div>
                </div>

                <!-- Document Title -->
                <div class="doc-title-box">
                    <div class="doc-title">
                        KETERANGAN BEBAS PUSTAKA<br>
                        BAGI CALON WISUDAWAN/I<br>
                        UNIVERSITAS SIBER INDONESIA
                    </div>
                </div>

                <!-- Student Identity Table -->
                <table class="table-identitas">
                    <tr>
                        <td class="col-label">Nama</td>
                        <td class="col-separator">:</td>
                        <td class="col-value"><strong>{{ $member->member_name }}</strong></td>
                    </tr>
                    <tr>
                        <td class="col-label">NIM</td>
                        <td class="col-separator">:</td>
                        <td class="col-value">{{ $member->member_id }}</td>
                    </tr>
                    <tr>
                        <td class="col-label">Program Studi</td>
                        <td class="col-separator">:</td>
                        <td class="col-value">{{ $member->prodi_name }}</td>
                    </tr>
                    <tr>
                        <td class="col-label">Semester</td>
                        <td class="col-separator">:</td>
                        <td class="col-value">Semester {{ $member->semester }}</td>
                    </tr>
                </table>

                <!-- Narrative Text -->
                <p class="p-text">
                    Berdasarkan hasil pemeriksaan pada sistem administrasi Perpustakaan Universitas Siber Indonesia, mahasiswa tersebut di atas dinyatakan:
                </p>

                <!-- Status Statement -->
                <div class="statement-badge">
                    BEBAS PUSTAKA
                </div>

                <p class="p-text">
                    Mahasiswa yang bersangkutan <strong>tidak memiliki tanggungan peminjaman koleksi, denda, maupun kewajiban administrasi lainnya</strong> pada Perpustakaan Universitas Siber Indonesia.
                </p>

                <p class="p-text">
                    Dengan demikian, mahasiswa tersebut telah memenuhi persyaratan administrasi perpustakaan untuk mengikuti <strong>Wisuda Universitas Siber Indonesia</strong>.
                </p>

                <p class="p-text">
                    Keterangan Bebas Pustaka ini diterbitkan secara elektronik melalui sistem Perpustakaan Universitas Siber Indonesia dan dapat digunakan sebagai salah satu dokumen persyaratan administrasi wisuda.
                </p>

                <!-- Issue Details -->
                <div class="issue-section">
                    <table class="issue-table">
                        <tr>
                            <td style="width: 130px;">Diterbitkan pada</td>
                            <td style="width: 20px; text-align: center;">:</td>
                            <td><strong>{{ \Carbon\Carbon::parse($bebasPustaka?->tgl_in ?? now())->translatedFormat('d F Y') }}</strong></td>
                        </tr>
                    </table>
                    <div class="note-verification">
                        [Silakan lakukan verifikasi dokumen dengan stamp dan paraf pustakawan di perpustakaan]
                    </div>
                </div>
            </div>

            <!-- Signature Section -->
            <div class="signature-section">
                <div class="signature-box">
                    <div class="signature-role">
                        Perpustakaan Universitas Siber Indonesia<br>
                        Pustakawan,
                    </div>
                    <div class="signature-space"></div>
                    <div class="signature-name">
                        ( .................................................... )
                    </div>
                    <div class="signature-unit">
                        Petugas Administrasi Perpustakaan
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
