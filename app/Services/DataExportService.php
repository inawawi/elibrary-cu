<?php

namespace App\Services;

use Carbon\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\Response;

class DataExportService
{
    /**
     * Ekspor data ke berbagai format: excel, word, csv, atau pdf/print
     *
     * @param string $format Format ekspor: 'excel', 'word', 'csv', 'pdf'
     * @param string $filename Nama file tanpa ekstensi
     * @param string $title Judul laporan
     * @param array $metadata Informasi metadata (misal: Periode, Filter, Petugas, Tanggal)
     * @param array $headers Daftar nama kolom header
     * @param array $rows Array data baris (setiap item adalah array associative atau index sesuai header)
     * @param string $orientation 'landscape' atau 'portrait'
     * @return Response|StreamedResponse
     */
    public static function export(
        string $format,
        string $filename,
        string $title,
        array $metadata,
        array $headers,
        array $rows,
        string $orientation = 'landscape',
        array $chartImages = [],
        ?string $extraHtml = null
    ): Response|StreamedResponse {
        $cleanFormat = strtolower(trim($format));

        return match ($cleanFormat) {
            'excel', 'xls', 'xlsx' => self::exportExcel($filename . '.xls', $title, $metadata, $headers, $rows),
            'word', 'doc', 'docx'  => self::exportWord($filename . '.doc', $title, $metadata, $headers, $rows, $orientation, $chartImages, $extraHtml),
            'csv'                  => self::exportCsv($filename . '.csv', $headers, $rows),
            'pdf', 'print'         => self::renderPrintView($title, $metadata, $headers, $rows, $orientation),
            default                => self::exportCsv($filename . '.csv', $headers, $rows),
        };
    }

    /**
     * Ekspor ke Excel (.xls) dengan format HTML Table & MSO XML
     * Mendukung formatting sel, border, warna header, dan text formatting agar barcode/NIM tidak terpotong nol depannya
     */
    public static function exportExcel(string $fullFilename, string $title, array $metadata, array $headers, array $rows): Response
    {
        $html = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
        $html .= '<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">';
        $html .= '<!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>Data Ekspor</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->';
        $html .= '<style>';
        $html .= 'body { font-family: Calibri, Arial, sans-serif; font-size: 11pt; color: #1e293b; }';
        $html .= 'table { border-collapse: collapse; width: 100%; }';
        $html .= 'th { background-color: #0f172a; color: #ffffff; font-weight: bold; border: 1px solid #475569; padding: 10px 8px; text-align: left; }';
        $html .= 'td { border: 1px solid #cbd5e1; padding: 7px 8px; vertical-align: top; font-size: 10pt; }';
        $html .= 'tr:nth-child(even) td { background-color: #f8fafc; }';
        $html .= '.header-title { font-size: 16pt; font-weight: bold; color: #0284c7; text-align: left; }';
        $html .= '.header-subtitle { font-size: 11pt; color: #475569; }';
        $html .= '.meta-box { margin-bottom: 15px; font-size: 10pt; color: #334155; }';
        $html .= '.text-format { mso-number-format:"\@"; }'; // Mencegah Excel mengubah 000123 menjadi 123
        $html .= '.text-center { text-align: center; }';
        $html .= '.text-right { text-align: right; }';
        $html .= '</style></head><body>';

        // University Header
        $html .= '<table>';
        $html .= '<tr><td colspan="' . count($headers) . '" class="header-title">' . htmlspecialchars(strtoupper($title)) . '</td></tr>';
        $html .= '<tr><td colspan="' . count($headers) . '" class="header-subtitle">PERPUSTAKAAN UNIVERSITAS SIBER INDONESIA (CYBER UNIVERSITY)</td></tr>';
        $html .= '<tr><td colspan="' . count($headers) . '" style="height: 10px;"></td></tr>';

        // Metadata rows
        foreach ($metadata as $key => $val) {
            $html .= '<tr>';
            $html .= '<td style="font-weight: bold; width: 160px; border: none; background: transparent;">' . htmlspecialchars($key) . '</td>';
            $html .= '<td colspan="' . (count($headers) - 1) . '" style="border: none; background: transparent;">: ' . htmlspecialchars($val) . '</td>';
            $html .= '</tr>';
        }

        $html .= '<tr><td colspan="' . count($headers) . '" style="height: 15px; border: none;"></td></tr>';
        $html .= '</table>';

        // Main Data Table
        $html .= '<table>';
        $html .= '<thead><tr>';
        foreach ($headers as $h) {
            $html .= '<th>' . htmlspecialchars($h) . '</th>';
        }
        $html .= '</tr></thead><tbody>';

        foreach ($rows as $index => $row) {
            $html .= '<tr>';
            foreach (array_values($row) as $cell) {
                // If numeric string with leading zeros or long ID/barcode, apply text-format
                $isTextNumber = is_string($cell) && preg_match('/^[0-9A-Za-z\-_]+$/', $cell) && (strlen($cell) > 5 || str_starts_with($cell, '0'));
                $class = $isTextNumber ? ' class="text-format"' : '';
                $html .= '<td' . $class . '>' . htmlspecialchars((string)($cell ?? '-')) . '</td>';
            }
            $html .= '</tr>';
        }

        $html .= '</tbody></table>';

        // Footer note
        $html .= '<br><table><tr><td colspan="' . count($headers) . '" style="border: none; font-size: 9pt; color: #64748b;">';
        $html .= 'Dokumen ini diekspor secara otomatis melalui Sistem e-Library Universitas Siber Indonesia pada ' . date('d/m/Y H:i:s') . ' WIB.';
        $html .= '</td></tr></table>';

        $html .= '</body></html>';

        return response($html, 200, [
            'Content-Type'        => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $fullFilename . '"',
            'Pragma'              => 'no-cache',
            'Expires'             => '0',
        ]);
    }

    /**
     * Ekspor ke Word (.doc) dengan layout dokumen resmi dan dukungan grafik base64
     */
    public static function exportWord(
        string $fullFilename, 
        string $title, 
        array $metadata, 
        array $headers, 
        array $rows, 
        string $orientation = 'landscape',
        array $chartImages = [],
        ?string $extraHtml = null
    ): Response
    {
        $pageSize = $orientation === 'portrait' ? 'size: 21cm 29.7cm; margin: 2cm;' : 'size: 29.7cm 21cm; margin: 1.5cm;';

        $html = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:w="urn:schemas-microsoft-com:office:word" xmlns="http://www.w3.org/TR/REC-html40">';
        $html .= '<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">';
        $html .= '<!--[if gte mso 9]><xml><w:WordDocument><w:View>Print</w:View><w:Zoom>100</w:Zoom><w:DoNotOptimizeForBrowser/></w:WordDocument></xml><![endif]-->';
        $html .= '<style>';
        $html .= "@page { {$pageSize} }";
        $html .= 'body { font-family: "Times New Roman", Times, serif; font-size: 11pt; line-height: 1.3; color: #000; }';
        $html .= '.inst-header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 8px; margin-bottom: 16px; }';
        $html .= '.inst-title { font-size: 14pt; font-weight: bold; }';
        $html .= '.inst-subtitle { font-size: 16pt; font-weight: bold; color: #0284c7; }';
        $html .= '.inst-address { font-size: 9pt; color: #333; }';
        $html .= '.doc-title { text-align: center; font-size: 13pt; font-weight: bold; margin-top: 14px; margin-bottom: 12px; text-decoration: underline; text-transform: uppercase; }';
        $html .= 'table.meta-table { width: 100%; border-collapse: collapse; margin-bottom: 14px; font-size: 10pt; }';
        $html .= 'table.meta-table td { padding: 3px 5px; border: none; }';
        $html .= 'table.data-table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 9pt; }';
        $html .= 'table.data-table th { background-color: #f1f5f9; border: 1px solid #333; padding: 6px 5px; font-weight: bold; text-align: center; }';
        $html .= 'table.data-table td { border: 1px solid #333; padding: 5px 6px; vertical-align: top; }';
        $html .= '.ttd-section { margin-top: 30px; width: 100%; font-size: 10pt; }';
        $html .= '</style></head><body>';

        // Kop Surat Resmi
        $html .= '<div class="inst-header">';
        $html .= '<div class="inst-title">UNIVERSITAS SIBER INDONESIA (CYBER UNIVERSITY)</div>';
        $html .= '<div class="inst-subtitle">UPT PERPUSTAKAAN & PUSAT SUMBER BELAJAR</div>';
        $html .= '<div class="inst-address">Jl. TB Simatupang No. 6, Pasar Minggu, Jakarta Selatan 12540 | Telp: (021) 7800777 | https://library.cyber-univ.ac.id</div>';
        $html .= '</div>';

        // Judul Dokumen
        $html .= '<div class="doc-title">' . htmlspecialchars($title) . '</div>';

        // Metadata
        $html .= '<table class="meta-table">';
        foreach ($metadata as $k => $v) {
            $html .= '<tr><td style="width: 140px; font-weight: bold;">' . htmlspecialchars($k) . '</td><td style="width: 10px;">:</td><td>' . htmlspecialchars($v) . '</td></tr>';
        }
        $html .= '</table>';

        // Visualisasi Grafik Akreditasi (Embedded Base64 Images)
        if (!empty($chartImages)) {
            $html .= '<div style="margin: 20px 0; page-break-inside: avoid;">';
            $html .= '<div style="font-size: 11pt; font-weight: bold; text-align: center; margin-bottom: 12px; color: #0f172a; text-transform: uppercase; border-bottom: 1px solid #cbd5e1; padding-bottom: 4px;">VISUALISASI GRAFIK BORANG AKREDITASI (TS-2, TS-1, TS)</div>';
            foreach ($chartImages as $cTitle => $imgBase64) {
                if (!empty($imgBase64)) {
                    $src = str_starts_with($imgBase64, 'data:') ? $imgBase64 : 'data:image/png;base64,' . $imgBase64;
                    $html .= '<div style="text-align: center; margin-bottom: 24px; page-break-inside: avoid;">';
                    if (!is_numeric($cTitle)) {
                        $html .= '<div style="font-size: 10pt; font-weight: bold; margin-bottom: 6px; color: #334155;">' . htmlspecialchars($cTitle) . '</div>';
                    }
                    $html .= '<img src="' . $src . '" width="680" style="max-width: 100%; border: 1px solid #cbd5e1; padding: 4px;" alt="Grafik Akreditasi" />';
                    $html .= '</div>';
                }
            }
            $html .= '</div>';
        }

        // Extra HTML if any (e.g. Borang Akreditasi Summary Table)
        if (!empty($extraHtml)) {
            $html .= '<div style="margin: 15px 0;">' . $extraHtml . '</div>';
        }

        // Data Table
        if (!empty($headers) && !empty($rows)) {
            $html .= '<table class="data-table">';
            $html .= '<thead><tr>';
            foreach ($headers as $h) {
                $html .= '<th>' . htmlspecialchars($h) . '</th>';
            }
            $html .= '</tr></thead><tbody>';

            foreach ($rows as $row) {
                $html .= '<tr>';
                foreach (array_values($row) as $cell) {
                    $html .= '<td>' . htmlspecialchars((string)($cell ?? '-')) . '</td>';
                }
                $html .= '</tr>';
            }

            $html .= '</tbody></table>';
        }


        // Tanda Tangan
        $html .= '<table class="ttd-section">';
        $html .= '<tr>';
        $html .= '<td style="width: 60%;"></td>';
        $html .= '<td style="width: 40%; text-align: center;">';
        $html .= 'Jakarta, ' . Carbon::now()->translatedFormat('d F Y') . '<br>';
        $html .= 'Kepala UPT Perpustakaan,<br><br><br><br><br>';
        $html .= '<b>( _________________________ )</b><br>';
        $html .= 'NIP/NIDN. ';
        $html .= '</td>';
        $html .= '</tr>';
        $html .= '</table>';

        $html .= '</body></html>';

        return response($html, 200, [
            'Content-Type'        => 'application/msword; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $fullFilename . '"',
            'Pragma'              => 'no-cache',
            'Expires'             => '0',
        ]);
    }

    /**
     * Ekspor ke CSV universal (UTF-8 BOM)
     */
    public static function exportCsv(string $fullFilename, array $headers, array $rows): StreamedResponse
    {
        return response()->streamDownload(function () use ($headers, $rows) {
            $output = fopen('php://output', 'w');
            
            // UTF-8 BOM untuk memastikan karakter terbaca dengan benar di Excel Windows
            fputs($output, "\xEF\xBB\xBF");

            // Tulis Header
            fputcsv($output, $headers);

            // Tulis Data
            foreach ($rows as $row) {
                fputcsv($output, array_values($row));
            }

            fclose($output);
        }, $fullFilename, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $fullFilename . '"',
            'Pragma'              => 'no-cache',
            'Expires'             => '0',
        ]);
    }

    /**
     * Render Tampilan PDF / Cetak langsung di browser dengan Kop Surat resmi dan tombol Cetak/Simpan PDF
     */
    public static function renderPrintView(string $title, array $metadata, array $headers, array $rows, string $orientation = 'landscape'): Response
    {
        $content = view('admin.export.print', [
            'title'       => $title,
            'metadata'    => $metadata,
            'headers'     => $headers,
            'rows'        => $rows,
            'orientation' => $orientation,
        ])->render();

        return response($content, 200, [
            'Content-Type' => 'text/html; charset=UTF-8',
        ]);
    }
}
