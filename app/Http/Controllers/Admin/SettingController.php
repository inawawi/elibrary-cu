<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MemberType;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $memberTypes = MemberType::withCount('members')->orderBy('member_type_id')->get();

        $announcement = Setting::get('member_announcement', [
            'title' => 'Selamat Datang di Perpustakaan Universitas Siber Indonesia',
            'content' => 'Pastikan mengembalikan buku pinjaman tepat waktu untuk menghindari denda. Jam operasional layanan sirkulasi: Senin - Jumat, 08.30 - 16.30 WIB.',
            'type' => 'info', // info, warning, danger, success
            'is_active' => 1,
            'start_date' => null,
            'end_date' => null,
            'updated_at' => Carbon::now()->toDateTimeString(),
        ]);

        $libraryRules = Setting::get('library_rules', "1. Setiap anggota berhak meminjam buku sesuai batas kuota jenis keanggotaan.\n2. Keterlambatan pengembalian buku dikenakan denda per hari per buku.\n3. Buku yang hilang atau rusak wajib diganti dengan judul dan edisi yang sama atau setara.\n4. Kartu anggota tidak dapat dipindahtangankan kepada orang lain.\n5. Menjaga ketertiban, kebersihan, dan kenyamanan di area perpustakaan.");

        $generalSettings = [
            'library_name' => Setting::get('library_name', 'Perpustakaan Universitas Siber Indonesia'),
            'library_subname' => Setting::get('library_subname', 'Universitas Siber Indonesia'),
        ];

        return view('admin.settings.index', compact('memberTypes', 'announcement', 'libraryRules', 'generalSettings'));
    }

    public function updateMemberType(Request $request, $id)
    {
        $memberType = MemberType::findOrFail($id);

        $validated = $request->validate([
            'member_type_name' => 'required|string|max:100',
            'loan_limit' => 'required|integer|min:1|max:50',
            'loan_periode' => 'required|integer|min:1|max:365',
            'fine_each_day' => 'required|numeric|min:0',
            'grace_periode' => 'required|integer|min:0|max:30',
            'reborrow_limit' => 'required|integer|min:0|max:10',
            'member_periode' => 'required|integer|min:1|max:3650',
            'reserve_limit' => 'nullable|integer|min:0|max:20',
        ]);

        $validated['reserve_limit'] = $validated['reserve_limit'] ?? 0;
        $validated['enable_reserve'] = $validated['reserve_limit'] > 0 ? 1 : 0;
        $validated['last_update'] = Carbon::today()->toDateString();

        $memberType->update($validated);

        return back()->with('success', 'Aturan perpustakaan untuk tipe keanggotaan "' . $memberType->member_type_name . '" berhasil diperbarui!');
    }

    public function storeMemberType(Request $request)
    {
        $validated = $request->validate([
            'member_type_name' => 'required|string|max:100|unique:mst_member_type,member_type_name',
            'loan_limit' => 'required|integer|min:1|max:50',
            'loan_periode' => 'required|integer|min:1|max:365',
            'fine_each_day' => 'required|numeric|min:0',
            'grace_periode' => 'required|integer|min:0|max:30',
            'reborrow_limit' => 'required|integer|min:0|max:10',
            'member_periode' => 'required|integer|min:1|max:3650',
            'reserve_limit' => 'nullable|integer|min:0|max:20',
        ]);

        $validated['reserve_limit'] = $validated['reserve_limit'] ?? 0;
        $validated['enable_reserve'] = $validated['reserve_limit'] > 0 ? 1 : 0;
        $validated['input_date'] = Carbon::today()->toDateString();
        $validated['last_update'] = Carbon::today()->toDateString();

        MemberType::create($validated);

        return back()->with('success', 'Tipe keanggotaan baru "' . $validated['member_type_name'] . '" beserta aturannya berhasil ditambahkan!');
    }

    public function deleteMemberType($id)
    {
        $memberType = MemberType::withCount('members')->findOrFail($id);

        if ($memberType->members_count > 0) {
            return back()->with('error', 'Tidak dapat menghapus tipe keanggotaan ini karena masih ada ' . $memberType->members_count . ' anggota yang terdaftar!');
        }

        $name = $memberType->member_type_name;
        $memberType->delete();

        return back()->with('success', 'Tipe keanggotaan "' . $name . '" berhasil dihapus.');
    }

    public function updateAnnouncement(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:200',
            'content' => 'required|string|max:2000',
            'type' => 'required|in:info,warning,danger,success',
            'is_active' => 'required|in:0,1',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $validated['updated_at'] = Carbon::now()->format('d/m/Y H:i');

        Setting::set('member_announcement', $validated);

        return back()->with('success', 'Informasi berkala untuk anggota berhasil disimpan dan dipublikasikan!');
    }

    public function updateGeneral(Request $request)
    {
        $validated = $request->validate([
            'library_rules' => 'nullable|string|max:5000',
            'library_name' => 'required|string|max:100',
            'library_subname' => 'nullable|string|max:100',
        ]);

        Setting::set('library_rules', $validated['library_rules'] ?? '');
        Setting::set('library_name', $validated['library_name']);
        if (isset($validated['library_subname'])) {
            Setting::set('library_subname', $validated['library_subname']);
        }

        return back()->with('success', 'Aturan umum dan identitas perpustakaan berhasil diperbarui!');
    }
}
