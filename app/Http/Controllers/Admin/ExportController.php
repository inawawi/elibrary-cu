<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Biblio;
use App\Models\Gmd;
use App\Models\GuestBook;
use App\Models\Member;
use App\Models\MemberType;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Services\DataExportService;
use Illuminate\Support\Facades\DB;

class ExportController extends Controller
{
    /**
     * Tampilkan Pusat Ekspor & Laporan Perpustakaan lengkap dengan Visualisasi Grafik Akreditasi (TS-2, TS-1, TS)
     */
    public function index(Request $request)
    {
        // 1. Daftar 6 GMD Spesifik sesuai kebutuhan perpustakaan
        $gmdCustomList = [
            [
                'id'    => '1',
                'name'  => '1. Buku (Teks Fisik)',
                'count' => Biblio::where('gmd_id', 1)->count()
            ],
            [
                'id'    => '262',
                'name'  => '2. Skripsi / Tugas Akhir',
                'count' => Biblio::where('gmd_id', 262)->count()
            ],
            [
                'id'    => '263',
                'name'  => '3. Jurnal Ilmiah',
                'count' => Biblio::where('gmd_id', 263)->count()
            ],
            [
                'id'    => '30',
                'name'  => '4. e-Book (Electronic Resource)',
                'count' => Biblio::where(function($q){ $q->where('gmd_id', 30)->orWhereNotNull('file_att'); })->count()
            ],
            [
                'id'    => 'prosiding',
                'name'  => '5. Prosiding (Seminar / Konferensi)',
                'count' => Biblio::where(function($q){ $q->where('title', 'like', '%prosiding%')->orWhere('title', 'like', '%proceedings%'); })->count()
            ],
            [
                'id'    => 'lainnya',
                'name'  => '6. Lainnya (Media / Koleksi Khusus)',
                'count' => Biblio::whereNotIn('gmd_id', [1, 262, 263, 30])->whereNull('file_att')->count()
            ],
        ];

        $memberTypes = MemberType::withCount('members')->orderBy('member_type_id')->get();

        $stats = [
            'total_biblio'   => Biblio::count(),
            'total_skripsi'  => Biblio::where('gmd_id', 262)->count(),
            'total_jurnal'   => Biblio::where('gmd_id', 263)->count(),
            'total_ebook'    => Biblio::where(function($q){ $q->where('gmd_id', 30)->orWhereNotNull('file_att'); })->count(),
            'total_member'   => Member::count(),
            'student_count'  => Member::where('member_type_id', 1)->count(),
            'staff_count'    => Member::where('member_type_id', '!=', 1)->count(),
            'total_visits'   => GuestBook::count(),
            'visits_today'   => GuestBook::where('tgl', Carbon::today()->toDateString())->count(),
        ];

        // 2. Logika Penentuan Tahun Semester (TS) Akreditasi
        // Pilihan TS Fleksibel yang tersedia
        $availableTsOptions = [
            '2026/2027' => 'TS 2026/2027 (Tahun Berjalan)',
            '2025/2026' => 'TS 2025/2026 (Rekomendasi Akreditasi)',
            '2024/2025' => 'TS 2024/2025',
            '2023/2024' => 'TS 2023/2024',
        ];

        $selectedTs = $request->input('ts', '2025/2026');
        if (!array_key_exists($selectedTs, $availableTsOptions)) {
            $selectedTs = '2025/2026';
        }

        $baseStartYear = (int) explode('/', $selectedTs)[0];

        // Struktur 3 Tahun Semester Borang Akreditasi: TS-2, TS-1, TS
        $tsStructure = [
            'TS-2' => $this->getAcademicYearDates($baseStartYear - 2),
            'TS-1' => $this->getAcademicYearDates($baseStartYear - 1),
            'TS'   => $this->getAcademicYearDates($baseStartYear),
        ];

        // 3. Agregasi Data Buku Tamu & Anggota untuk TS-2, TS-1, TS
        $akreditasiVisits = [];
        $akreditasiMembers = [];

        foreach ($tsStructure as $key => $info) {
            // Kunjungan Buku Tamu
            $visitGanjil = GuestBook::whereBetween('tgl', [$info['ganjil']['start'], $info['ganjil']['end']])->count();
            $visitGenap  = GuestBook::whereBetween('tgl', [$info['genap']['start'], $info['genap']['end']])->count();
            $visitTotal  = $visitGanjil + $visitGenap;
            $visitMonthlyAvg = round($visitTotal / 12, 1);

            $akreditasiVisits[$key] = [
                'label'       => $info['label'],
                'ganjil'      => $visitGanjil,
                'genap'       => $visitGenap,
                'total'       => $visitTotal,
                'monthly_avg' => $visitMonthlyAvg,
            ];

            // Pendaftaran Anggota Baru
            $memberGanjil = Member::whereBetween('register_date', [$info['ganjil']['start'], $info['ganjil']['end']])->count();
            $memberGenap  = Member::whereBetween('register_date', [$info['genap']['start'], $info['genap']['end']])->count();
            $memberTotal  = $memberGanjil + $memberGenap;

            $akreditasiMembers[$key] = [
                'label'  => $info['label'],
                'ganjil' => $memberGanjil,
                'genap'  => $memberGenap,
                'total'  => $memberTotal,
            ];
        }

        // 4. Rincian Kunjungan Bulanan pada TS terpilih (12 Bulan: Sep s/d Agu)
        $selectedYearInfo = $tsStructure['TS'];
        $monthlyMonths = [
            ['month' => 9,  'year' => $selectedYearInfo['start_year'], 'name' => 'Sep', 'semester' => 'Ganjil'],
            ['month' => 10, 'year' => $selectedYearInfo['start_year'], 'name' => 'Okt', 'semester' => 'Ganjil'],
            ['month' => 11, 'year' => $selectedYearInfo['start_year'], 'name' => 'Nov', 'semester' => 'Ganjil'],
            ['month' => 12, 'year' => $selectedYearInfo['start_year'], 'name' => 'Des', 'semester' => 'Ganjil'],
            ['month' => 1,  'year' => $selectedYearInfo['end_year'],   'name' => 'Jan', 'semester' => 'Ganjil'],
            ['month' => 2,  'year' => $selectedYearInfo['end_year'],   'name' => 'Feb', 'semester' => 'Ganjil'],
            ['month' => 3,  'year' => $selectedYearInfo['end_year'],   'name' => 'Mar', 'semester' => 'Genap'],
            ['month' => 4,  'year' => $selectedYearInfo['end_year'],   'name' => 'Apr', 'semester' => 'Genap'],
            ['month' => 5,  'year' => $selectedYearInfo['end_year'],   'name' => 'Mei', 'semester' => 'Genap'],
            ['month' => 6,  'year' => $selectedYearInfo['end_year'],   'name' => 'Jun', 'semester' => 'Genap'],
            ['month' => 7,  'year' => $selectedYearInfo['end_year'],   'name' => 'Jul', 'semester' => 'Genap'],
            ['month' => 8,  'year' => $selectedYearInfo['end_year'],   'name' => 'Agu', 'semester' => 'Genap'],
        ];

        $monthlyVisits = [];
        foreach ($monthlyMonths as $m) {
            $totalMonth = GuestBook::whereYear('tgl', $m['year'])
                                   ->whereMonth('tgl', $m['month'])
                                   ->count();

            // Library vs Student Corner / Anggota vs Tamu
            $libraryCount = GuestBook::whereYear('tgl', $m['year'])
                                     ->whereMonth('tgl', $m['month'])
                                     ->where(function($q) {
                                         $q->where('tujuan', 'like', '%Library%')
                                           ->orWhere('status', 'Anggota')
                                           ->orWhere('keperluan', 'like', '%Baca%')
                                           ->orWhere('keperluan', 'like', '%Pinjam%')
                                           ->orWhere('keperluan', 'like', '%Kunjungan%');
                                     })->count();

            $studentCornerCount = GuestBook::whereYear('tgl', $m['year'])
                                           ->whereMonth('tgl', $m['month'])
                                           ->where(function($q) {
                                               $q->where('tujuan', 'like', '%Student%')
                                                 ->orWhere('status', '!=', 'Anggota')
                                                 ->orWhere('keperluan', 'like', '%Diskusi%')
                                                 ->orWhere('keperluan', 'like', '%Corner%');
                                           })->count();

            $monthlyVisits[] = [
                'name'           => $m['name'],
                'full_name'      => "{$m['name']} {$m['year']}",
                'semester'       => $m['semester'],
                'total'          => $totalMonth,
                'library'        => $libraryCount,
                'student_corner' => $studentCornerCount,
            ];
        }

        // 5. Distribusi Program Studi Anggota Aktif
        $prodiCounts = [
            'Sistem Informasi'            => Member::where('member_id', 'like', '12%')->count(),
            'Teknologi Informasi'         => Member::where('member_id', 'like', '13%')->orWhere('member_id', 'like', '14%')->count(),
            'Bisnis Digital'              => Member::where('member_id', 'like', '15%')->orWhere('member_id', 'like', '16%')->count(),
            'Kewirausahaan / Manajemen'   => Member::where('member_id', 'like', '21%')->orWhere('member_id', 'like', '22%')->count(),
            'Dosen & Staf Akademik'       => Member::where('member_type_id', '!=', 1)->count(),
        ];

        return view('admin.export.index', compact(
            'gmdCustomList',
            'memberTypes',
            'stats',
            'availableTsOptions',
            'selectedTs',
            'tsStructure',
            'akreditasiVisits',
            'akreditasiMembers',
            'monthlyVisits',
            'prodiCounts'
        ));
    }

    /**
     * Ekspor Laporan Eksekutif Akreditasi Perpustakaan ke format Microsoft Word (.doc)
     * Menyertakan Kop Resmi, Matriks Borang LKPS, dan Gambar Grafik Base64
     */
    public function exportWordAkreditasi(Request $request)
    {
        $selectedTs = $request->input('ts', '2025/2026');
        $baseStartYear = (int) explode('/', $selectedTs)[0];

        $tsStructure = [
            'TS-2' => $this->getAcademicYearDates($baseStartYear - 2),
            'TS-1' => $this->getAcademicYearDates($baseStartYear - 1),
            'TS'   => $this->getAcademicYearDates($baseStartYear),
        ];

        // Agregasi Data Kunjungan
        $akreditasiVisits = [];
        $totalAllVisits = 0;
        foreach ($tsStructure as $key => $info) {
            $vGanjil = GuestBook::whereBetween('tgl', [$info['ganjil']['start'], $info['ganjil']['end']])->count();
            $vGenap  = GuestBook::whereBetween('tgl', [$info['genap']['start'], $info['genap']['end']])->count();
            $vTotal  = $vGanjil + $vGenap;
            $totalAllVisits += $vTotal;

            $akreditasiVisits[$key] = [
                'label'       => $info['label'],
                'ganjil'      => $vGanjil,
                'genap'       => $vGenap,
                'total'       => $vTotal,
                'monthly_avg' => round($vTotal / 12, 1),
            ];
        }

        // Agregasi Data Anggota
        $akreditasiMembers = [];
        $totalAllMembers = 0;
        foreach ($tsStructure as $key => $info) {
            $mGanjil = Member::whereBetween('register_date', [$info['ganjil']['start'], $info['ganjil']['end']])->count();
            $mGenap  = Member::whereBetween('register_date', [$info['genap']['start'], $info['genap']['end']])->count();
            $mTotal  = $mGanjil + $mGenap;
            $totalAllMembers += $mTotal;

            $akreditasiMembers[$key] = [
                'label'  => $info['label'],
                'ganjil' => $mGanjil,
                'genap'  => $mGenap,
                'total'  => $mTotal,
            ];
        }

        $title = 'Laporan Statistik & Grafik Kunjungan Akreditasi Perpustakaan (TS-2 s.d. TS)';
        $filename = 'Laporan_Akreditasi_Perpustakaan_TS_' . str_replace('/', '-', $selectedTs) . '_' . date('Ymd_His');

        $metadata = [
            'Jenis Dokumen'         => 'Laporan Rekapitulasi Akreditasi Sarana & Prasarana Perpustakaan',
            'Tahun Semester Acuan'  => "TS ({$tsStructure['TS']['label']}), TS-1 ({$tsStructure['TS-1']['label']}), TS-2 ({$tsStructure['TS-2']['label']})",
            'Ketentuan Semester'    => 'Ganjil: Sep - Feb | Genap: Mar - Agu',
            'Total Kunjungan (3 TS)'=> number_format($totalAllVisits) . ' Kunjungan',
            'Total Anggota Baru'    => number_format($totalAllMembers) . ' Orang',
            'Tanggal Cetak'         => Carbon::now()->translatedFormat('d F Y, H:i') . ' WIB',
            'Penanggung Jawab'      => auth()->user()->username ?? 'Kepala UPT Perpustakaan',
        ];

        // Format Tabel Matriks Borang LKPS Kunjungan
        $extraHtml = '<div style="margin-top: 15px; margin-bottom: 20px;">';
        $extraHtml .= '<h3 style="font-size: 11pt; font-weight: bold; margin-bottom: 6px; text-transform: uppercase;">1. Tabel Rekapitulasi Kunjungan Perpustakaan per Tahun Akademik (Borang LKPS)</h3>';
        $extraHtml .= '<table style="width: 100%; border-collapse: collapse; font-size: 10pt;">';
        $extraHtml .= '<thead><tr style="background-color: #f1f5f9;">';
        $extraHtml .= '<th style="border: 1px solid #333; padding: 6px; text-align: center;">Tahun Akademik</th>';
        $extraHtml .= '<th style="border: 1px solid #333; padding: 6px; text-align: center;">Semester Ganjil</th>';
        $extraHtml .= '<th style="border: 1px solid #333; padding: 6px; text-align: center;">Semester Genap</th>';
        $extraHtml .= '<th style="border: 1px solid #333; padding: 6px; text-align: center;">Total Kunjungan</th>';
        $extraHtml .= '<th style="border: 1px solid #333; padding: 6px; text-align: center;">Rata-rata / Bulan</th>';
        $extraHtml .= '</tr></thead><tbody>';

        foreach ($akreditasiVisits as $k => $v) {
            $extraHtml .= '<tr>';
            $extraHtml .= '<td style="border: 1px solid #333; padding: 6px; font-weight: bold; text-align: center;">' . $k . ' (' . $v['label'] . ')</td>';
            $extraHtml .= '<td style="border: 1px solid #333; padding: 6px; text-align: center;">' . number_format($v['ganjil']) . '</td>';
            $extraHtml .= '<td style="border: 1px solid #333; padding: 6px; text-align: center;">' . number_format($v['genap']) . '</td>';
            $extraHtml .= '<td style="border: 1px solid #333; padding: 6px; text-align: center; font-weight: bold;">' . number_format($v['total']) . '</td>';
            $extraHtml .= '<td style="border: 1px solid #333; padding: 6px; text-align: center;">' . $v['monthly_avg'] . '</td>';
            $extraHtml .= '</tr>';
        }
        $extraHtml .= '</tbody></table></div>';

        // Format Tabel Matriks Borang Anggota
        $extraHtml .= '<div style="margin-top: 15px; margin-bottom: 20px;">';
        $extraHtml .= '<h3 style="font-size: 11pt; font-weight: bold; margin-bottom: 6px; text-transform: uppercase;">2. Tabel Rekapitulasi Keanggotaan Baru Perpustakaan (TS-2 s.d. TS)</h3>';
        $extraHtml .= '<table style="width: 100%; border-collapse: collapse; font-size: 10pt;">';
        $extraHtml .= '<thead><tr style="background-color: #f1f5f9;">';
        $extraHtml .= '<th style="border: 1px solid #333; padding: 6px; text-align: center;">Tahun Akademik</th>';
        $extraHtml .= '<th style="border: 1px solid #333; padding: 6px; text-align: center;">Semester Ganjil</th>';
        $extraHtml .= '<th style="border: 1px solid #333; padding: 6px; text-align: center;">Semester Genap</th>';
        $extraHtml .= '<th style="border: 1px solid #333; padding: 6px; text-align: center;">Total Anggota Baru</th>';
        $extraHtml .= '</tr></thead><tbody>';

        foreach ($akreditasiMembers as $k => $m) {
            $extraHtml .= '<tr>';
            $extraHtml .= '<td style="border: 1px solid #333; padding: 6px; font-weight: bold; text-align: center;">' . $k . ' (' . $m['label'] . ')</td>';
            $extraHtml .= '<td style="border: 1px solid #333; padding: 6px; text-align: center;">' . number_format($m['ganjil']) . '</td>';
            $extraHtml .= '<td style="border: 1px solid #333; padding: 6px; text-align: center;">' . number_format($m['genap']) . '</td>';
            $extraHtml .= '<td style="border: 1px solid #333; padding: 6px; text-align: center; font-weight: bold;">' . number_format($m['total']) . '</td>';
            $extraHtml .= '</tr>';
        }
        $extraHtml .= '</tbody></table></div>';

        // Ambil gambar grafik dari request (Base64 Canvas PNG)
        $chartImages = [];
        if ($request->filled('chart_image_semester')) {
            $chartImages['Grafik 1: Perbandingan Kunjungan Semester (TS-2 vs TS-1 vs TS)'] = $request->input('chart_image_semester');
        }
        if ($request->filled('chart_image_monthly')) {
            $chartImages['Grafik 2: Rincian Kunjungan Bulanan pada TS ' . $selectedTs] = $request->input('chart_image_monthly');
        }
        if ($request->filled('chart_image_member')) {
            $chartImages['Grafik 3: Tren Pertumbuhan Keanggotaan (TS-2 s.d. TS)'] = $request->input('chart_image_member');
        }

        return DataExportService::export(
            'word',
            $filename,
            $title,
            $metadata,
            [], // Tanpa data table individual mentah, fokus pada format laporan eksekutif LKPS
            [],
            'portrait',
            $chartImages,
            $extraHtml
        );
    }

    /**
     * Helper perhitungan rentang tanggal tahun akademik (Ganjil: Sep-Feb, Genap: Mar-Agu)
     */
    private function getAcademicYearDates(int $startYear): array
    {
        $endYear = $startYear + 1;
        $leapFeb = date('t', strtotime("{$endYear}-02-01"));

        return [
            'label'      => "{$startYear}/{$endYear}",
            'start_year' => $startYear,
            'end_year'   => $endYear,
            'ganjil'     => [
                'start' => "{$startYear}-09-01",
                'end'   => "{$endYear}-02-{$leapFeb}",
                'label' => "Ganjil {$startYear}/{$endYear}"
            ],
            'genap'      => [
                'start' => "{$endYear}-03-01",
                'end'   => "{$endYear}-08-31",
                'label' => "Genap {$startYear}/{$endYear}"
            ],
            'full'       => [
                'start' => "{$startYear}-09-01",
                'end'   => "{$endYear}-08-31",
            ]
        ];
    }
}
