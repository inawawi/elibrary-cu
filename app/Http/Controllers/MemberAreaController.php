<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\BebasPustaka;
use App\Models\Biblio;
use App\Models\Gmd;
use App\Models\Item;
use App\Models\Member;
use App\Models\Place;
use App\Models\Publisher;
use App\Models\Setting;
use App\Models\Topic;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MemberAreaController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::guard('member')->check()) {
            return redirect()->route('member.dashboard');
        }
        return view('member.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'member_id' => 'required|string',
            'password'  => 'required|string',
        ], [
            'member_id.required' => 'ID Anggota / NIM wajib diisi.',
            'password.required'  => 'Kata sandi wajib diisi.',
        ]);

        $member = Member::where('member_id', $request->member_id)->first();

        if (!$member) {
            return back()->withErrors(['member_id' => 'ID Anggota tidak ditemukan.'])->withInput();
        }

        $passwordValid = false;
        $inputPassword = trim($request->password);
        $isMahasiswa = (int)$member->member_type_id === 1;

        // 1. Cek Bcrypt hash (jika mahasiswa sudah pernah ubah password atau admin meresetnya)
        if (!empty($member->mpasswd) && Hash::check($inputPassword, $member->mpasswd)) {
            $passwordValid = true;
        }
        // 2. Default password mahasiswa adalah Tanggal Lahir (YYYY-MM-DD)
        elseif ($isMahasiswa && !empty($member->birth_date)) {
            $birthDate = Carbon::parse($member->birth_date)->format('Y-m-d');
            $birthFormats = [
                $birthDate, // YYYY-MM-DD (format utama sesuai permintaan)
                str_replace('-', '', $birthDate), // YYYYMMDD
                date('dmY', strtotime($birthDate)), // DDMMYYYY
                date('d-m-Y', strtotime($birthDate)), // DD-MM-YYYY
            ];
            if (in_array($inputPassword, $birthFormats, true)) {
                $passwordValid = true;
            }
        }
        // 3. Fallback jika mahasiswa belum memiliki tanggal lahir di database: izinkan NIM
        elseif ($isMahasiswa && empty($member->birth_date) && $inputPassword === $member->member_id) {
            $passwordValid = true;
        }
        // 4. Default password Dosen & Staf (non-mahasiswa) menggunakan ID Anggota / NIDN / NIP
        elseif (!$isMahasiswa && $inputPassword === $member->member_id) {
            $passwordValid = true;
        }
        // 5. Cek PIN
        elseif (!empty($member->pin) && $inputPassword === $member->pin) {
            $passwordValid = true;
        }
        // 6. Legacy MD5 atau plain
        elseif (!empty($member->mpasswd) && (md5($inputPassword) === $member->mpasswd || $inputPassword === $member->mpasswd)) {
            $passwordValid = true;
        }

        if (!$passwordValid) {
            if ($isMahasiswa) {
                if (empty($member->birth_date)) {
                    $hintMsg = 'Kata sandi salah. Tanggal lahir Anda belum tercatat di data anggota, silakan gunakan NIM (' . $member->member_id . ') sebagai kata sandi atau hubungi admin perpustakaan.';
                } else {
                    $hintMsg = 'Kata sandi salah. Sandi default mahasiswa adalah tanggal lahir Anda (format: YYYY-MM-DD, contoh: 2004-05-18) atau kata sandi baru jika pernah diubah.';
                }
            } else {
                $hintMsg = 'Kata sandi salah. Silakan periksa kembali kata sandi atau ID Anggota Anda.';
            }
            return back()->withErrors(['password' => $hintMsg])->withInput();
        }

        if ($member->is_pending) {
            return back()->withErrors(['member_id' => 'Status keanggotaan Anda masih menunggu persetujuan.'])->withInput();
        }

        Auth::guard('member')->login($member);
        $request->session()->regenerate();

        return redirect()->route('member.dashboard')->with('success', 'Selamat datang kembali, ' . $member->member_name . '!');
    }

    public function dashboard()
    {
        /** @var Member $member */
        $member = Auth::guard('member')->user();

        $activeLoans = $member->activeLoans()
            ->with(['item.biblio.authors', 'item.location'])
            ->orderBy('due_date', 'asc')
            ->get();

        $loanHistories = $member->loanHistories()
            ->with(['item.biblio.authors'])
            ->orderBy('return_date', 'desc')
            ->take(10)
            ->get();

        $announcement = Setting::get('member_announcement');
        if ($announcement && !empty($announcement['is_active'])) {
            $today = Carbon::today()->toDateString();
            if (!empty($announcement['start_date']) && $today < $announcement['start_date']) {
                $announcement = null;
            } elseif (!empty($announcement['end_date']) && $today > $announcement['end_date']) {
                $announcement = null;
            }
        } else {
            $announcement = null;
        }

        $libraryRules = Setting::get('library_rules');
        $isContactIncomplete = empty(trim($member->member_phone ?? '')) || empty(trim($member->member_email ?? ''));

        $thesis = $member->isStudent() ? $member->thesisSubmission() : null;
        $bebasPustaka = $member->isStudent() ? $member->bebasPustakaRecord() : null;
        $isSenior = $member->isSeniorStudent();
        $isBebasPustakaEligible = $member->isBebasPustakaEligible();

        return view('member.dashboard', compact(
            'member', 
            'activeLoans', 
            'loanHistories', 
            'announcement', 
            'libraryRules', 
            'isContactIncomplete',
            'thesis',
            'bebasPustaka',
            'isSenior',
            'isBebasPustakaEligible'
        ));
    }

    public function showSkripsiForm()
    {
        /** @var Member $member */
        $member = Auth::guard('member')->user();

        if (!$member->isStudent() || !$member->isSeniorStudent()) {
            return redirect()->route('member.dashboard')
                ->with('error', 'Layanan pengunggahan berkas skripsi dan pencetakan bebas pustaka hanya diperuntukkan bagi mahasiswa tingkat akhir (minimal semester 7).');
        }

        $thesis = $member->thesisSubmission();
        $bebasPustaka = $member->bebasPustakaRecord();
        $isBebasPustakaEligible = $member->isBebasPustakaEligible();
        $hasActiveLoans = $member->activeLoans()->exists();

        // Daftar Dosen untuk autocomplete/pembimbing
        $dosenMembers = Member::where('member_type_id', 2)
            ->orWhere('member_notes', 'like', '%dosen%')
            ->orderBy('member_name', 'asc')
            ->get(['member_id', 'member_name']);

        return view('member.skripsi.upload', compact('member', 'thesis', 'bebasPustaka', 'isBebasPustakaEligible', 'hasActiveLoans', 'dosenMembers'));
    }

    public function storeSkripsi(Request $request)
    {
        /** @var Member $member */
        $member = Auth::guard('member')->user();

        if (!$member->isStudent() || !$member->isSeniorStudent()) {
            return redirect()->route('member.dashboard')
                ->with('error', 'Layanan pengunggahan skripsi hanya untuk mahasiswa minimal semester 7.');
        }

        $existingThesis = $member->thesisSubmission();

        $rules = [
            'title' => 'required|string|max:500',
            'pembimbing_1' => 'required|string|max:150',
            'pembimbing_2' => 'nullable|string|max:150',
            'publish_year' => 'required|integer|min:2000|max:2099',
            'abstract' => 'required|string|min:50',
            'agree_single_pdf' => 'accepted',
            'agree_max_10mb' => 'accepted',
            'agree_complete' => 'accepted',
            'agree_signed' => 'accepted',
            'agree_watermark' => 'accepted',
        ];

        // Jika baru pertama upload, file skripsi wajib. Jika re-upload / perbaikan, file opsional
        if (!$existingThesis || empty($existingThesis->file_att)) {
            $rules['skripsi_file'] = 'required|file|mimes:pdf|max:10240';
        } else {
            $rules['skripsi_file'] = 'nullable|file|mimes:pdf|max:10240';
        }

        $messages = [
            'title.required' => 'Judul skripsi wajib diisi.',
            'pembimbing_1.required' => 'Nama Dosen Pembimbing 1 wajib diisi.',
            'publish_year.required' => 'Tahun kelulusan/skripsi wajib diisi.',
            'abstract.required' => 'Abstrak / ringkasan skripsi wajib diisi.',
            'abstract.min' => 'Abstrak minimal 50 karakter agar informatif.',
            'skripsi_file.required' => 'File naskah skripsi (PDF) wajib diunggah.',
            'skripsi_file.mimes' => 'Format file naskah skripsi harus berupa dokumen PDF (.pdf).',
            'skripsi_file.max' => 'Ukuran file naskah skripsi tidak boleh melebihi 10 MB.',
            'agree_single_pdf.accepted' => 'Anda harus mengonfirmasi bahwa dokumen terdiri dari 1 file PDF utuh.',
            'agree_max_10mb.accepted' => 'Anda harus mengonfirmasi bahwa ukuran file maksimal 10 MB.',
            'agree_complete.accepted' => 'Anda harus mengonfirmasi bahwa dokumen lengkap dari halaman cover s.d. daftar pustaka & lampiran.',
            'agree_signed.accepted' => 'Anda harus mengonfirmasi bahwa lembar pengesahan telah ditandatangani lengkap.',
            'agree_watermark.accepted' => 'Anda harus mengonfirmasi bahwa dokumen telah memuat watermark resmi kampus.',
        ];

        $validated = $request->validate($rules, $messages);

        // Pastikan folder penyimpanan ada
        $uploadDir = public_path('files/skripsi');
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $filePath = $existingThesis ? $existingThesis->file_att : null;

        if ($request->hasFile('skripsi_file')) {
            $file = $request->file('skripsi_file');
            $fileName = 'skripsi_' . $member->member_id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move($uploadDir, $fileName);
            $filePath = 'files/skripsi/' . $fileName;
        }

        // Dapatkan Publisher dan Place default (Cyber University)
        $publisher = Publisher::firstOrCreate(
            ['publisher_name' => 'Universitas Siber Indonesia'],
            ['input_date' => Carbon::today()->toDateString(), 'last_update' => Carbon::today()->toDateString()]
        );
        $place = Place::firstOrCreate(
            ['place_name' => 'Jakarta'],
            ['input_date' => Carbon::today()->toDateString(), 'last_update' => Carbon::today()->toDateString()]
        );
        $gmd = Gmd::firstOrCreate(
            ['gmd_code' => 'TH'],
            ['gmd_name' => 'Thesis / Skripsi', 'input_date' => Carbon::today()->toDateString(), 'last_update' => Carbon::today()->toDateString()]
        );

        $prodiCode = match($member->prodi_name) {
            'Teknologi Informasi' => 'TI',
            'Sistem Informasi' => 'SI',
            'Sistem dan Teknologi Informasi' => 'STI',
            'Bisnis Digital' => 'BD',
            'Akuntansi' => 'AKT',
            'Manajemen' => 'MNJ',
            'Kewirausahaan' => 'KW',
            default => 'SKR'
        };
        $callNumber = 'SKR-' . $prodiCode . '-' . $validated['publish_year'] . '-' . substr($member->member_id, -4);

        // Otomatis deteksi rekomendasi subjek dari judul skripsi dan program studi
        $recommendedSubjects = Topic::suggestSubjectsFromTitle($validated['title'], $member->prodi_name);

        $specDetail = [
            'tipe' => 'Skripsi',
            'nim' => $member->member_id,
            'nama' => $member->member_name,
            'prodi' => $member->prodi_name,
            'semester' => $member->semester,
            'pembimbing_1' => $validated['pembimbing_1'],
            'pembimbing_2' => $validated['pembimbing_2'] ?? null,
            'status' => 'pending', // Menunggu verifikasi pustakawan
            'submitted_at' => Carbon::now()->toDateTimeString(),
            'notes_admin' => null,
            'subjects' => $recommendedSubjects,
            'requirements' => [
                'single_pdf' => true,
                'max_10mb' => true,
                'cover_to_appendix' => true,
                'signed_endorsement' => true,
                'watermarked' => true,
            ],
        ];

        $sor = $member->member_name . ' (NIM: ' . $member->member_id . ') ; Pembimbing: ' . $validated['pembimbing_1'];
        if (!empty($validated['pembimbing_2'])) {
            $sor .= ', ' . $validated['pembimbing_2'];
        }

        if ($existingThesis) {
            $existingThesis->title = $validated['title'];
            $existingThesis->sor = $sor;
            $existingThesis->publish_year = $validated['publish_year'];
            $existingThesis->notes = $validated['abstract'];
            $existingThesis->file_att = $filePath;
            $existingThesis->opac_hide = 1; // Hidden until approved
            $existingThesis->spec_detail_info = json_encode($specDetail, JSON_UNESCAPED_UNICODE);
            $existingThesis->last_update = Carbon::now();
            $existingThesis->save();
            $biblio = $existingThesis;
        } else {
            $biblio = Biblio::create([
                'gmd_id' => $gmd->gmd_id ?: 262,
                'title' => $validated['title'],
                'sor' => $sor,
                'edition' => 'Skripsi',
                'publisher_id' => $publisher->publisher_id,
                'publish_year' => $validated['publish_year'],
                'collation' => 'xx, 120 hlm. : ilus. ; 30 cm',
                'series_title' => 'Skripsi Program Studi ' . $member->prodi_name,
                'call_number' => $callNumber,
                'language_id' => 'id',
                'isbn_issn' => $member->member_id,
                'publish_place_id' => $place->place_id,
                'classification' => '004',
                'notes' => $validated['abstract'],
                'file_att' => $filePath,
                'opac_hide' => 1, // Hidden until verified by admin/pustakawan
                'spec_detail_info' => json_encode($specDetail, JSON_UNESCAPED_UNICODE),
                'input_date' => Carbon::now(),
                'last_update' => Carbon::now(),
                'uid' => 1,
            ]);
        }

        // Sinkronisasi subjek otomatis ke biblio_topic
        $topicIds = [];
        foreach ($recommendedSubjects as $subjName) {
            $t = Topic::firstOrCreate(['topic' => trim($subjName)], [
                'topic_type' => 't',
                'input_date' => Carbon::today()->toDateString(),
                'last_update' => Carbon::today()->toDateString(),
            ]);
            $topicIds[] = $t->topic_id;
        }
        if (!empty($topicIds)) {
            $biblio->topics()->sync($topicIds);
        }

        // Register student as author
        $studentAuthor = Author::firstOrCreate(
            ['author_name' => $member->member_name],
            ['authority_type' => 'p', 'input_date' => Carbon::today()->toDateString(), 'last_update' => Carbon::today()->toDateString()]
        );
        $biblio->authors()->syncWithoutDetaching([$studentAuthor->author_id => ['level' => 1]]);

        // Register advisor 1 as author
        if (!empty($validated['pembimbing_1'])) {
            $adv1 = Author::firstOrCreate(
                ['author_name' => $validated['pembimbing_1']],
                ['authority_type' => 'p', 'input_date' => Carbon::today()->toDateString(), 'last_update' => Carbon::today()->toDateString()]
            );
            $biblio->authors()->syncWithoutDetaching([$adv1->author_id => ['level' => 2]]);
        }

        // Register advisor 2 as author
        if (!empty($validated['pembimbing_2'])) {
            $adv2 = Author::firstOrCreate(
                ['author_name' => $validated['pembimbing_2']],
                ['authority_type' => 'p', 'input_date' => Carbon::today()->toDateString(), 'last_update' => Carbon::today()->toDateString()]
            );
            $biblio->authors()->syncWithoutDetaching([$adv2->author_id => ['level' => 3]]);
        }

        // Sync legacy table tb_skripsi
        try {
            $existingTbSkripsi = DB::table('tb_skripsi')->where('nim', $member->member_id)->first();
            $tbData = [
                'kd_skripsi' => $biblio->biblio_id,
                'judul' => $validated['title'],
                'dosen' => $validated['pembimbing_1'],
                'asdos' => $validated['pembimbing_2'] ?? null,
                'subjek' => !empty($recommendedSubjects) ? implode(', ', $recommendedSubjects) : $member->prodi_name,
                'tahun' => $validated['publish_year'],
                'file_skripsi' => $filePath,
                'id_kampus' => 'F1',
                'id_prodi' => (int)($member->prodi_code ?: 11),
                'id_admin' => 1,
                'tgl_up' => Carbon::today()->toDateString(),
                'abstrak' => $validated['abstract'],
                'jmlpenulis' => 1,
                'nim' => $member->member_id,
                'nama' => $member->member_name,
            ];

            if ($existingTbSkripsi) {
                DB::table('tb_skripsi')->where('nim', $member->member_id)->update($tbData);
            } else {
                $tbData['id_skripsi'] = $biblio->biblio_id;
                $tbData['urutan'] = $biblio->biblio_id;
                $tbData['urutaninput'] = $biblio->biblio_id;
                DB::table('tb_skripsi')->insert($tbData);
            }
        } catch (\Throwable $e) {
            // Silently continue if legacy table has different auto-increment
        }

        return redirect()->route('member.skripsi')
            ->with('success', 'Naskah skripsi berhasil diunggah! Berkas Anda saat ini berstatus MENUNGGU VERIFIKASI pustakawan.');
    }

    public function printBebasPustaka()
    {
        /** @var Member $member */
        $member = Auth::guard('member')->user();

        if (!$member->isStudent() || !$member->isSeniorStudent()) {
            return redirect()->route('member.dashboard')
                ->with('error', 'Layanan ini hanya untuk mahasiswa tingkat akhir.');
        }

        if (!$member->isBebasPustakaEligible()) {
            return redirect()->route('member.skripsi')
                ->with('warning', 'Surat Keterangan Bebas Pustaka belum dapat dicetak karena berkas skripsi Anda belum diverifikasi/disetujui pustakawan, atau Anda masih memiliki tanggungan peminjaman buku.');
        }

        $thesis = $member->thesisSubmission();
        $bebasPustaka = $member->bebasPustakaRecord();

        return view('member.skripsi.print_bebas_pustaka', compact('member', 'thesis', 'bebasPustaka'));
    }

    public function updateContact(Request $request)
    {
        /** @var Member $member */
        $member = Auth::guard('member')->user();

        $validated = $request->validate([
            'member_phone' => [
                'required',
                'string',
                'min:9',
                'max:25',
                'regex:/^[0-9+\-\s()]+$/',
            ],
            'member_email' => [
                'required',
                'string',
                'email',
                'max:100',
            ],
        ], [
            'member_phone.required' => 'Nomor Telepon / WhatsApp wajib diisi.',
            'member_phone.min' => 'Nomor Telepon / WhatsApp minimal 9 digit.',
            'member_phone.regex' => 'Format nomor telepon/WhatsApp hanya boleh berupa angka dan tanda (+, -).',
            'member_email.required' => 'Alamat email aktif wajib diisi.',
            'member_email.email' => 'Format alamat email tidak valid.',
        ]);

        $cleanPhone = preg_replace('/[^\d+]/', '', $validated['member_phone']);
        $cleanEmail = strtolower(trim($validated['member_email']));

        $member->member_phone = $cleanPhone;
        $member->member_email = $cleanEmail;
        $member->last_update = Carbon::now();
        $member->save();

        return redirect()->route('member.dashboard')->with('success', 'Data kontak Anda (Nomor WhatsApp & Email) berhasil disimpan! Seluruh layanan keanggotaan kini aktif.');
    }

    public function updatePassword(Request $request)
    {
        /** @var Member $member */
        $member = Auth::guard('member')->user();

        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'current_password.required' => 'Kata sandi saat ini wajib diisi.',
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.min' => 'Kata sandi baru minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak sesuai.',
        ]);

        $currentPassword = trim($request->current_password);
        $currentValid = false;

        // Cek kecocokan sandi saat ini dengan mpasswd hash
        if (!empty($member->mpasswd) && Hash::check($currentPassword, $member->mpasswd)) {
            $currentValid = true;
        }
        // Cek default tanggal lahir (YYYY-MM-DD)
        elseif (!empty($member->birth_date) && $currentPassword === Carbon::parse($member->birth_date)->format('Y-m-d')) {
            $currentValid = true;
        }
        // Cek fallback NIM
        elseif ($currentPassword === $member->member_id) {
            $currentValid = true;
        }
        // Cek PIN
        elseif (!empty($member->pin) && $currentPassword === $member->pin) {
            $currentValid = true;
        }

        if (!$currentValid) {
            return back()->withErrors(['current_password' => 'Kata sandi saat ini salah. Jika belum pernah diubah, gunakan tanggal lahir format YYYY-MM-DD.'])->withInput();
        }

        $member->mpasswd = Hash::make($request->password);
        $member->last_update = Carbon::now();
        $member->save();

        return back()->with('success', 'Kata sandi Anda berhasil diperbarui! Silakan gunakan kata sandi baru ini untuk login berikutnya.');
    }

    public function logout(Request $request)
    {
        Auth::guard('member')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('opac.index')->with('info', 'Anda telah keluar dari area anggota.');
    }

    /**
     * Unduh logo watermark resmi Universitas Siber Indonesia berformat PNG transparan
     */
    public function downloadWatermark()
    {
        $path = public_path('images/Watermark_Universitas_Siber_Indonesia.png');
        if (!file_exists($path)) {
            $path = public_path('images/logo.png');
        }

        return response()->download($path, 'Watermark_Universitas_Siber_Indonesia.png', [
            'Content-Type' => 'image/png',
        ]);
    }
}

