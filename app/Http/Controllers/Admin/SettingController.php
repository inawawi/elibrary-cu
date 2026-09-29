<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MemberType;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public static function getDefaultSlides(): array
    {
        return [
            [
                'id' => 1,
                'tag' => 'Kampus Cyber University',
                'title' => 'The First Fintech University in Indonesia',
                'desc' => 'Kampus modern berorientasi digital dan keunggulan teknologi siber untuk mencetak generasi inovator masa depan.',
                'image' => 'images/slides/slide1_campus.jpg',
                'is_active' => 1,
            ],
            [
                'id' => 2,
                'tag' => 'Ruang Koleksi Perpustakaan',
                'title' => 'Koleksi Pustaka & Literatur Ilmiah Lengkap',
                'desc' => 'Akses ribuan judul buku teks fisik, e-book, jurnal akademik, dan repositori skripsi untuk sivitas akademika.',
                'image' => 'images/slides/slide2_library.jpg',
                'is_active' => 1,
            ],
            [
                'id' => 3,
                'tag' => 'Student Corner & Diskusi',
                'title' => 'Ruang Belajar Kolaboratif & Kreatif Mahasiswa',
                'desc' => 'Fasilitas student lounge nyaman untuk bedah referensi riset, belajar bersama, dan penyusunan tugas akhir.',
                'image' => 'images/slides/slide3_student_corner.jpg',
                'is_active' => 1,
            ],
            [
                'id' => 4,
                'tag' => 'Podcast Studio & Media Hub',
                'title' => 'Pusat Literasi Digital & Podcast Edukasi',
                'desc' => 'Studio podcast modern untuk menyiarkan diskursus ilmu pengetahuan, review literatur, dan kreativitas mahasiswa.',
                'image' => 'images/slides/slide4_podcast.jpg',
                'is_active' => 1,
            ],
        ];
    }

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

        $heroSlides = Setting::get('hero_slides', self::getDefaultSlides());

        return view('admin.settings.index', compact('memberTypes', 'announcement', 'libraryRules', 'generalSettings', 'heroSlides'));
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

    public function storeSlide(Request $request)
    {
        $validated = $request->validate([
            'tag' => 'required|string|max:100',
            'title' => 'required|string|max:200',
            'desc' => 'required|string|max:500',
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $slides = Setting::get('hero_slides', self::getDefaultSlides());

        $file = $request->file('image');
        $filename = 'slide_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('images/slides'), $filename);

        $nextId = empty($slides) ? 1 : (max(array_column($slides, 'id')) + 1);

        $newSlide = [
            'id' => $nextId,
            'tag' => $validated['tag'],
            'title' => $validated['title'],
            'desc' => $validated['desc'],
            'image' => 'images/slides/' . $filename,
            'is_active' => 1,
        ];

        $slides[] = $newSlide;
        Setting::set('hero_slides', $slides);

        return back()->with('success', 'Slide gambar baru berhasil ditambahkan!');
    }

    public function updateSlide(Request $request, $id)
    {
        $validated = $request->validate([
            'tag' => 'required|string|max:100',
            'title' => 'required|string|max:200',
            'desc' => 'required|string|max:500',
            'is_active' => 'required|in:0,1',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $slides = Setting::get('hero_slides', self::getDefaultSlides());

        $found = false;
        foreach ($slides as &$slide) {
            if ($slide['id'] == $id) {
                $slide['tag'] = $validated['tag'];
                $slide['title'] = $validated['title'];
                $slide['desc'] = $validated['desc'];
                $slide['is_active'] = (int) $validated['is_active'];

                if ($request->hasFile('image')) {
                    $file = $request->file('image');
                    $filename = 'slide_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('images/slides'), $filename);
                    $slide['image'] = 'images/slides/' . $filename;
                }

                $found = true;
                break;
            }
        }

        if (!$found) {
            return back()->with('error', 'Slide tidak ditemukan.');
        }

        Setting::set('hero_slides', $slides);

        return back()->with('success', 'Pengaturan slide berhasil diperbarui!');
    }

    public function deleteSlide($id)
    {
        $slides = Setting::get('hero_slides', self::getDefaultSlides());

        if (count($slides) <= 1) {
            return back()->with('error', 'Minimal harus ada 1 slide gambar yang tersimpan!');
        }

        $filtered = array_values(array_filter($slides, function ($s) use ($id) {
            return $s['id'] != $id;
        }));

        Setting::set('hero_slides', $filtered);

        return back()->with('success', 'Slide gambar berhasil dihapus.');
    }
}
