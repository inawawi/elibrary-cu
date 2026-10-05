<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GuestBook;
use App\Services\DataExportService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class GuestBookController extends Controller
{
    /**
     * Tampilkan data buku tamu & kunjungan perpustakaan
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = GuestBook::with('member');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('id_anggota', 'like', "%{$search}%")
                  ->orWhere('prodi', 'like', "%{$search}%")
                  ->orWhere('keperluan', 'like', "%{$search}%");
            });
        }

        if (!empty($status) && $status !== 'all') {
            $query->where('status', $status);
        }

        if (!empty($startDate)) {
            $query->whereDate('tgl', '>=', $startDate);
        }

        if (!empty($endDate)) {
            $query->whereDate('tgl', '<=', $endDate);
        }

        $visitors = $query->orderBy('tgl', 'desc')
                          ->orderBy('jam', 'desc')
                          ->paginate(20)
                          ->withQueryString();

        // Statistik Kunjungan
        $today = Carbon::today()->toDateString();
        $thisMonthStart = Carbon::today()->startOfMonth()->toDateString();

        $stats = [
            'total'       => GuestBook::count(),
            'today'       => GuestBook::where('tgl', $today)->count(),
            'this_month'  => GuestBook::where('tgl', '>=', $thisMonthStart)->count(),
            'anggota'     => GuestBook::where('status', 'Anggota')->count(),
            'non_anggota' => GuestBook::where('status', '!=', 'Anggota')->count(),
        ];

        return view('admin.guestbook.index', compact(
            'visitors', 'search', 'status', 'startDate', 'endDate', 'stats'
        ));
    }

    /**
     * Ekspor data buku tamu ke berbagai format (Excel, Word, PDF, CSV)
     */
    public function export(Request $request)
    {
        $format = $request->input('format', 'excel');
        $search = $request->input('search');
        $status = $request->input('status');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = GuestBook::with('member');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('id_anggota', 'like', "%{$search}%")
                  ->orWhere('prodi', 'like', "%{$search}%")
                  ->orWhere('keperluan', 'like', "%{$search}%");
            });
        }

        if (!empty($status) && $status !== 'all') {
            $query->where('status', $status);
        }

        if (!empty($startDate)) {
            $query->whereDate('tgl', '>=', $startDate);
        }

        if (!empty($endDate)) {
            $query->whereDate('tgl', '<=', $endDate);
        }

        $visitors = $query->orderBy('tgl', 'desc')->orderBy('jam', 'desc')->get();

        $headers = [
            'No',
            'Tanggal',
            'Jam',
            'NIM / ID Anggota',
            'Nama Pengunjung',
            'Status',
            'Program Studi / Asal',
            'Keperluan',
            'Tujuan Kunjungan'
        ];

        $rows = [];
        $no = 1;
        foreach ($visitors as $v) {
            $tglIndo = !empty($v->tgl) ? Carbon::parse($v->tgl)->translatedFormat('d F Y') : '-';
            $prodi = $v->prodi ?: ($v->member?->prodi_name ?? '-');
            
            $rows[] = [
                'no'         => $no++,
                'tanggal'    => $tglIndo,
                'jam'        => $v->jam ?? '-',
                'id_anggota' => $v->id_anggota ?? '-',
                'nama'       => $v->nama ?? '-',
                'status'     => $v->status ?? 'Anggota',
                'prodi'      => $prodi,
                'keperluan'  => $v->keperluan ?? '-',
                'tujuan'     => $v->tujuan ?? '-'
            ];
        }

        // Filter text untuk metadata
        $periodeText = 'Semua Periode';
        if (!empty($startDate) && !empty($endDate)) {
            $periodeText = Carbon::parse($startDate)->format('d/m/Y') . ' s/d ' . Carbon::parse($endDate)->format('d/m/Y');
        } elseif (!empty($startDate)) {
            $periodeText = 'Mulai ' . Carbon::parse($startDate)->format('d/m/Y');
        } elseif (!empty($endDate)) {
            $periodeText = 'Sampai ' . Carbon::parse($endDate)->format('d/m/Y');
        }

        $statusText = empty($status) || $status === 'all' ? 'Semua Status (Anggota & Non Anggota)' : $status;

        $metadata = [
            'Jenis Laporan'     => 'Rekap Kunjungan Buku Tamu Perpustakaan',
            'Periode Kunjungan' => $periodeText,
            'Kategori Status'   => $statusText,
            'Total Pengunjung'  => count($rows) . ' Orang',
            'Tanggal Cetak'     => Carbon::now()->translatedFormat('d F Y, H:i') . ' WIB',
            'Dicetak Oleh'      => auth()->user()->username ?? 'Petugas Perpustakaan'
        ];

        $title = 'Laporan Kunjungan Buku Tamu Perpustakaan';
        $filename = 'Laporan_Buku_Tamu_' . date('Ymd_His');

        $chartImages = [];
        if ($request->filled('chart_image')) {
            $chartImages['Grafik Tren Kunjungan Buku Tamu'] = $request->input('chart_image');
        }
        if ($request->filled('chart_image_2')) {
            $chartImages['Grafik Kunjungan per Tujuan / Keperluan'] = $request->input('chart_image_2');
        }

        return DataExportService::export(
            $format,
            $filename,
            $title,
            $metadata,
            $headers,
            $rows,
            'landscape',
            $chartImages
        );
    }
}
