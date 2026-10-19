<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Author;
use App\Models\Member;
use App\Models\MemberType;
use App\Services\DataExportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $activeTab = $request->input('tab', 'mahasiswa');
        $search = $request->input('search');
        $typeId = $request->input('type_id');
        $status = $request->input('status');

        // Total counters for Tab badges
        $studentCount = Member::where('member_type_id', 1)->count();
        $staffCount = Member::where('member_type_id', '!=', 1)->count();

        $query = Member::with(['memberType', 'activeLoans']);

        // Separate member data based on selected tab
        if ($activeTab === 'non-mahasiswa') {
            $query->where('member_type_id', '!=', 1);
        } else {
            $activeTab = 'mahasiswa';
            $query->where('member_type_id', 1);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('member_id', 'like', "%{$search}%")
                  ->orWhere('member_name', 'like', "%{$search}%")
                  ->orWhere('member_email', 'like', "%{$search}%")
                  ->orWhere('member_phone', 'like', "%{$search}%")
                  ->orWhere('member_notes', 'like', "%{$search}%");
            });
        }

        if (!empty($typeId)) {
            $query->where('member_type_id', $typeId);
        }

        if ($status === 'active') {
            $query->where('is_pending', 0)
                  ->where(function ($q) {
                      $q->whereNull('expire_date')
                        ->orWhere('expire_date', '>=', Carbon::today()->toDateString());
                  });
        } elseif ($status === 'expired') {
            $query->where('is_pending', 0)
                  ->whereNotNull('expire_date')
                  ->where('expire_date', '<', Carbon::today()->toDateString());
        } elseif ($status === 'inactive') {
            $query->where('is_pending', 1);
        }

        $members = $query->orderBy('member_id', 'desc')->paginate(15)->withQueryString();

        // Filter options for current tab
        if ($activeTab === 'non-mahasiswa') {
            $memberTypes = MemberType::where('member_type_id', '!=', 1)->get();
        } else {
            $memberTypes = MemberType::where('member_type_id', 1)->get();
        }

        return view('admin.member.index', compact('members', 'search', 'typeId', 'status', 'memberTypes', 'activeTab', 'studentCount', 'staffCount'));
    }

    public function create()
    {
        $memberTypes = MemberType::all();
        return view('admin.member.create', compact('memberTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'member_id' => 'required|string|max:20|unique:member,member_id',
            'member_name' => 'required|string|max:100',
            'gender' => 'required|in:1,2',
            'birth_date' => 'nullable|date',
            'member_type_id' => 'required|integer',
            'member_address' => 'nullable|string|max:255',
            'member_email' => 'nullable|email|max:100',
            'member_phone' => 'nullable|string|max:50',
            'inst_name' => 'nullable|string|max:100',
            'password' => 'required|string|min:6',
            'member_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $imageName = null;
        if ($request->hasFile('member_image')) {
            $file = $request->file('member_image');
            $imageName = $validated['member_id'] . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images/persons'), $imageName);
        }

        $memberType = MemberType::find($validated['member_type_id']);
        $isLecturer = (int)$validated['member_type_id'] === 2 
            || str_contains(strtolower($memberType?->member_type_name ?? ''), 'dosen');

        // Dosen tidak memiliki masa aktif (aktif selama masih menjadi dosen)
        // Mahasiswa masa aktif 7 tahun dihitung dari 2 digit tahun angkatan pada NIM (digit ke 3 & 4)
        $expireDate = $isLecturer 
            ? null 
            : Member::calculateStudentExpireDate($validated['member_id'], Carbon::today()->toDateString());

        $member = Member::create([
            'member_id' => $validated['member_id'],
            'member_name' => $validated['member_name'],
            'gender' => (int) $validated['gender'],
            'birth_date' => $validated['birth_date'] ?? null,
            'member_type_id' => $validated['member_type_id'],
            'member_address' => $validated['member_address'] ?? null,
            'member_email' => $validated['member_email'] ?? null,
            'member_phone' => $validated['member_phone'] ?? null,
            'inst_name' => $validated['inst_name'] ?? 'Universitas Siber Indonesia',
            'mpasswd' => Hash::make($validated['password']),
            'member_image' => $imageName,
            'register_date' => Carbon::today()->toDateString(),
            'member_since_date' => Carbon::today()->toDateString(),
            'expire_date' => $expireDate,
            'is_pending' => 0,
            'input_date' => Carbon::now(),
            'last_update' => Carbon::now(),
        ]);

        // If member is Dosen, register as an Author to link to student theses (pembimbing skripsi)
        if ((int)$validated['member_type_id'] === 2) {
            Author::firstOrCreate(
                ['author_name' => $validated['member_name']],
                [
                    'authority_type' => 'p',
                    'input_date' => Carbon::today()->toDateString(),
                    'last_update' => Carbon::today()->toDateString(),
                ]
            );
        }

        return redirect()->route('admin.member.index')->with('success', 'Anggota ' . $validated['member_name'] . ' berhasil didaftarkan!');
    }

    public function edit($id)
    {
        $member = Member::findOrFail($id);
        $memberTypes = MemberType::all();
        return view('admin.member.edit', compact('member', 'memberTypes'));
    }

    public function update(Request $request, $id)
    {
        $member = Member::findOrFail($id);

        $validated = $request->validate([
            'member_name' => 'required|string|max:100',
            'gender' => 'required|in:1,2',
            'birth_date' => 'nullable|date',
            'member_type_id' => 'required|integer',
            'member_address' => 'nullable|string|max:255',
            'member_email' => 'nullable|email|max:100',
            'member_phone' => 'nullable|string|max:50',
            'inst_name' => 'nullable|string|max:100',
            'expire_date' => 'nullable|date',
            'password' => 'nullable|string|min:6',
            'member_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($request->hasFile('member_image')) {
            $file = $request->file('member_image');
            $imageName = $member->member_id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images/persons'), $imageName);
            $validated['member_image'] = $imageName;
        }

        if (!empty($validated['password'])) {
            $validated['mpasswd'] = Hash::make($validated['password']);
        }
        unset($validated['password']);

        // Dosen & Staf tidak ada batas masa berlaku (aktif selama masih bertugas)
        if ((int)$validated['member_type_id'] !== 1) {
            $validated['expire_date'] = null;
        } elseif (empty($validated['expire_date'])) {
            $validated['expire_date'] = Member::calculateStudentExpireDate($member->member_id, $member->register_date);
        }

        $validated['last_update'] = Carbon::now();
        $member->update($validated);

        return redirect()->route('admin.member.index', ['tab' => (int)$member->member_type_id === 1 ? 'mahasiswa' : 'non-mahasiswa'])
            ->with('success', 'Data anggota ' . $member->member_name . ' berhasil diperbarui!');
    }

    public function showCard($id)
    {
        $member = Member::with('memberType')->findOrFail($id);
        return view('admin.member.card', compact('member'));
    }

    public function toggleStatus($id)
    {
        $member = Member::findOrFail($id);
        $newPending = $member->is_pending == 1 ? 0 : 1;
        $member->update([
            'is_pending' => $newPending,
            'last_update' => Carbon::now(),
        ]);

        $statusLabel = $newPending == 0 ? 'diaktifkan kembali' : 'dinonaktifkan';
        return back()->with('success', "Status keanggotaan {$member->member_name} ({$member->member_id}) berhasil {$statusLabel}.");
    }

    public function resetPassword($id)
    {
        $member = Member::findOrFail($id);

        // Jika tanggal lahir mahasiswa masih kosong, otomatis cari dari master mhs_s1
        if (empty($member->birth_date) && (int)$member->member_type_id === 1 && Schema::hasTable('mhs_s1')) {
            $tgl = DB::table('mhs_s1')->where('nim', $member->member_id)->value('tgl_lhr');
            if (!empty($tgl) && $tgl !== '0000-00-00' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $tgl)) {
                $member->birth_date = $tgl;
                $member->save();
            }
        }

        if (!empty($member->birth_date)) {
            $defaultPassword = Carbon::parse($member->birth_date)->format('Y-m-d');
            $member->update([
                'mpasswd' => Hash::make($defaultPassword),
                'last_update' => Carbon::now(),
            ]);
            return back()->with('success', "Kata sandi member {$member->member_name} ({$member->member_id}) berhasil direset kembali ke tanggal lahir: {$defaultPassword}");
        } else {
            $defaultPassword = $member->member_id;
            $member->update([
                'mpasswd' => Hash::make($defaultPassword),
                'last_update' => Carbon::now(),
            ]);
            return back()->with('warning', "Tanggal lahir member belum tercatat di data. Kata sandi direset ke NIM/ID Anggota: {$defaultPassword}");
        }
    }

    public function bulkResetPassword(Request $request)
    {
        $raw = $request->input('selected_members', []);
        if (is_string($raw)) {
            $memberIds = explode(',', $raw);
        } else {
            $memberIds = (array) $raw;
        }
        $memberIds = array_filter(array_map('trim', $memberIds));

        if (empty($memberIds)) {
            return back()->with('error', 'Silakan centang/checklist minimal 1 mahasiswa yang ingin direset kata sandinya.');
        }

        $members = Member::whereIn('member_id', $memberIds)->get();
        if ($members->isEmpty()) {
            return back()->with('error', 'Data mahasiswa yang dipilih tidak ditemukan.');
        }

        $resetWithBirthCount = 0;
        $resetWithNimCount = 0;
        $now = Carbon::now();

        foreach ($members as $member) {
            // Lengkapi tanggal lahir jika masih kosong dari master mhs_s1
            if (empty($member->birth_date) && (int)$member->member_type_id === 1 && Schema::hasTable('mhs_s1')) {
                $tgl = DB::table('mhs_s1')->where('nim', $member->member_id)->value('tgl_lhr');
                if (!empty($tgl) && $tgl !== '0000-00-00' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $tgl)) {
                    $member->birth_date = $tgl;
                    $member->save();
                }
            }

            if (!empty($member->birth_date)) {
                $pwd = Carbon::parse($member->birth_date)->format('Y-m-d');
                $resetWithBirthCount++;
            } else {
                $pwd = $member->member_id;
                $resetWithNimCount++;
            }

            $member->update([
                'mpasswd' => Hash::make($pwd),
                'last_update' => $now,
            ]);
        }

        $total = count($members);
        $detail = [];
        if ($resetWithBirthCount > 0) {
            $detail[] = "{$resetWithBirthCount} mahasiswa direset ke tanggal lahir (format: YYYY-MM-DD)";
        }
        if ($resetWithNimCount > 0) {
            $detail[] = "{$resetWithNimCount} mahasiswa tanpa tanggal lahir direset ke NIM";
        }
        $detailText = !empty($detail) ? ' (' . implode(', ', $detail) . ')' : '';

        return back()->with('success', "Berhasil mereset kata sandi {$total} mahasiswa terpilih{$detailText}!");
    }

    public function destroy($id)
    {
        $member = Member::with('activeLoans')->findOrFail($id);

        if ($member->activeLoans->isNotEmpty()) {
            return back()->with('error', 'Anggota tidak dapat dihapus karena masih memiliki pinjaman buku yang belum dikembalikan!');
        }

        $member->delete();
        return redirect()->route('admin.member.index')->with('success', 'Anggota berhasil dihapus.');
    }

    /**
     * Membaca konfigurasi koneksi database eksternal dari file .dbkon
     * Mendukung format key:value maupun KEY=value, serta pemisahan section staff & mahasiswa
     */
    private function getDbkonConfig($type = 'student')
    {
        $possiblePaths = [
            base_path('.dbkon'),
            base_path('../.dbkon'),
            base_path('.db'),
            base_path('../.db'),
            'c:/laragon/www/elibrarycu/.dbkon',
            'c:/laragon/www/elibrary-cu/.dbkon',
            '/var/www/html/.dbkon',
        ];

        // Default config fallback
        if ($type === 'student') {
            $config = [
                'host' => '172.16.192.47',
                'port' => 3303,
                'database' => 'db_student_usi',
                'user' => 'root',
                'pass' => 'Stud3Nt$.Cyb3r2o26',
                'table' => 'mhs',
            ];
        } else {
            $config = [
                'host' => '172.16.192.47',
                'port' => 3304,
                'database' => 'stafF_br1',
                'user' => 'root',
                'pass' => 'sT4ff.Cyb3r2o26',
                'table' => 'karyawanbs1',
            ];
        }

        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                $currentSection = null;

                foreach ($lines as $rawLine) {
                    $trimmed = trim($rawLine);
                    $clean = trim(ltrim($trimmed, '#; '));
                    if ($clean === '') {
                        continue;
                    }

                    // Deteksi section
                    if (stripos($clean, 'non-mahasiswa') !== false || stripos($clean, 'dosen') !== false || stripos($clean, 'karyawan') !== false) {
                        $currentSection = 'staff';
                        continue;
                    } elseif (stripos($clean, 'mahasiswa') !== false || stripos($clean, 'student') !== false) {
                        $currentSection = 'student';
                        continue;
                    }

                    // Tentukan delimiter: ':' atau '='
                    $sep = null;
                    if (strpos($clean, '=') !== false) {
                        $sep = '=';
                    } elseif (strpos($clean, ':') !== false) {
                        $sep = ':';
                    }

                    if ($sep !== null) {
                        list($k, $v) = explode($sep, $clean, 2);
                        $k = strtolower(trim($k));
                        $v = trim($v);

                        if (str_starts_with($k, 'db_')) {
                            $k = substr($k, 3);
                        }

                        if ($currentSection === null || $currentSection === $type) {
                            if (in_array($k, ['ip', 'host', 'server'])) $config['host'] = $v;
                            if (in_array($k, ['port'])) $config['port'] = (int)$v;
                            if (in_array($k, ['user', 'username'])) $config['user'] = $v;
                            if (in_array($k, ['pass', 'password', 'pwd'])) $config['pass'] = $v;
                            if (in_array($k, ['db', 'database', 'dbname'])) $config['database'] = $v;
                            if (in_array($k, ['table', 'tbl', 'tabel'])) $config['table'] = $v;
                        }
                    }
                }
                break;
            }
        }

        return $config;
    }

    /**
     * Sinkronisasi data Dosen & Staf dari database kepegawaian eksternal (karyawanbs1)
     * Menggunakan koneksi READ-ONLY (tanpa mengubah sumber data)
     */
    public function syncExternal()
    {
        // 1. Ambil konfigurasi database staff dari file .dbkon
        $config = $this->getDbkonConfig('staff');

        try {
            // 2. Buat koneksi PDO ke database eksternal (READ ONLY)
            $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset=utf8mb4";
            $pdo = new \PDO($dsn, $config['user'], $config['pass'], [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_TIMEOUT => 5,
            ]);

            // Pastikan master tipe Staf tersedia di sistem lokal
            $staffType = MemberType::firstOrCreate(
                ['member_type_name' => 'Staf'],
                [
                    'loan_limit' => 2,
                    'loan_periode' => 7,
                    'enable_reserve' => 1,
                    'reserve_limit' => 3,
                    'member_periode' => 0,
                    'reborrow_limit' => 1,
                    'fine_each_day' => 1000,
                    'grace_periode' => 0,
                    'input_date' => Carbon::today()->toDateString(),
                    'last_update' => Carbon::today()->toDateString(),
                ]
            );

            // 3. Ambil data dari tabel karyawanbs1 (Strictly Read-Only Query)
            $table = $config['table'];
            $stmt = $pdo->query("SELECT nip, nama, gelar, jabatan, kd_dosen, email, telp_hp1, sex, tgllahir, alamat1 FROM `{$table}`");
            $rows = $stmt->fetchAll();

            $inserted = 0;
            $updated = 0;
            $unchanged = 0;
            $syncedItems = [];
            $now = Carbon::now();
            $today = Carbon::today()->toDateString();

            foreach ($rows as $row) {
                $nip = trim($row['nip'] ?? '');
                if (empty($nip)) {
                    continue;
                }

                $nama = trim($row['nama'] ?? '');
                $gelar = trim($row['gelar'] ?? '');
                $jabatan = trim($row['jabatan'] ?? '');
                $kdDosen = trim($row['kd_dosen'] ?? '');
                $email = trim($row['email'] ?? '');
                $phone = trim($row['telp_hp1'] ?? '');
                $alamat = trim($row['alamat1'] ?? '');

                // Format: Nama, Gelar (jika ada gelar)
                $fullName = $nama . (!empty($gelar) ? ', ' . $gelar : '');

                // Tentukan tipe: Dosen (2) atau Staf (3)
                $isDosen = !empty($kdDosen) 
                    || stripos($jabatan, 'dosen') !== false 
                    || stripos($jabatan, 'kaprodi') !== false 
                    || stripos($jabatan, 'instruktur') !== false;

                $typeId = $isDosen ? 2 : $staffType->member_type_id;

                // Format gender (1: Laki-laki, 2: Perempuan)
                $gender = (strtoupper(trim($row['sex'] ?? '')) === 'P') ? 2 : 1;

                // Format tanggal lahir (validasi ketat untuk menghindari '0000-00-00' atau '1900-01-00')
                $birthDate = null;
                if (!empty($row['tgllahir']) && $row['tgllahir'] !== '0000-00-00') {
                    $rawDate = trim($row['tgllahir']);
                    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $rawDate)) {
                        list($y, $m, $d) = explode('-', $rawDate);
                        if ((int)$y > 1920 && (int)$m >= 1 && (int)$m <= 12 && (int)$d >= 1 && (int)$d <= 31 && checkdate((int)$m, (int)$d, (int)$y)) {
                            $birthDate = sprintf('%04d-%02d-%02d', (int)$y, (int)$m, (int)$d);
                        }
                    }
                }

                $member = Member::find($nip);

                if ($member) {
                    $changes = [];

                    if ($member->member_name !== $fullName) {
                        $member->member_name = $fullName;
                        $changes[] = 'Nama/Gelar';
                    }
                    if ($member->member_type_id !== $typeId) {
                        $member->member_type_id = $typeId;
                        $changes[] = 'Tipe Anggota';
                    }
                    if ((int)$member->gender !== (int)$gender) {
                        $member->gender = $gender;
                        $changes[] = 'Jenis Kelamin';
                    }
                    if (!empty($birthDate) && $member->birth_date !== $birthDate) {
                        $member->birth_date = $birthDate;
                        $changes[] = 'Tgl Lahir';
                    }
                    if (!empty($jabatan) && $member->member_notes !== $jabatan) {
                        $member->member_notes = $jabatan;
                        $changes[] = 'Jabatan';
                    }
                    if (!empty($email) && $member->member_email !== $email) {
                        $member->member_email = $email;
                        $changes[] = 'Email';
                    }
                    if (!empty($phone) && $member->member_phone !== $phone) {
                        $member->member_phone = $phone;
                        $changes[] = 'No. HP';
                    }
                    if (!empty($alamat) && $member->member_address !== $alamat) {
                        $member->member_address = $alamat;
                        $changes[] = 'Alamat';
                    }

                    if (count($changes) > 0) {
                        $member->expire_date = null; // Aktif selama bertugas
                        $member->last_update = $now;
                        $member->save();
                        $updated++;
                        $syncedItems[] = [
                            'status' => 'Diperbarui',
                            'badge' => 'updated',
                            'member_id' => $nip,
                            'member_name' => $fullName,
                            'prodi' => !empty($jabatan) ? $jabatan : ($isDosen ? 'Dosen' : 'Staf'),
                            'kelas' => '-',
                            'email' => $email ?: '-',
                            'phone' => $phone ?: '-',
                            'changes' => implode(', ', $changes) . ' diperbarui',
                        ];
                    } else {
                        $unchanged++;
                    }
                } else {
                    Member::create([
                        'member_id' => $nip,
                        'member_name' => $fullName,
                        'gender' => $gender,
                        'birth_date' => $birthDate,
                        'member_type_id' => $typeId,
                        'member_address' => !empty($alamat) ? $alamat : null,
                        'member_email' => !empty($email) ? $email : null,
                        'member_phone' => !empty($phone) ? $phone : null,
                        'inst_name' => 'Universitas Siber Indonesia',
                        'member_notes' => !empty($jabatan) ? $jabatan : null,
                        'mpasswd' => Hash::make($nip),
                        'register_date' => $today,
                        'member_since_date' => $today,
                        'expire_date' => null, // Aktif selama bertugas
                        'is_pending' => 0,
                        'input_date' => $now,
                        'last_update' => $now,
                    ]);
                    $inserted++;
                    $syncedItems[] = [
                        'status' => 'Baru Ditambahkan',
                        'badge' => 'new',
                        'member_id' => $nip,
                        'member_name' => $fullName,
                        'prodi' => !empty($jabatan) ? $jabatan : ($isDosen ? 'Dosen' : 'Staf'),
                        'kelas' => '-',
                        'email' => $email ?: '-',
                        'phone' => $phone ?: '-',
                        'changes' => 'Anggota baru ditambahkan',
                    ];
                }

                // Jika Dosen, daftarkan pula sebagai Author/Pengarang agar terhubung ke pembimbing tugas akhir/skripsi
                if ($isDosen) {
                    Author::firstOrCreate(
                        ['author_name' => $fullName],
                        [
                            'authority_type' => 'p',
                            'input_date' => $today,
                            'last_update' => $today,
                        ]
                    );
                }
            }

            $totalProcessed = $inserted + $updated;
            $syncReport = [
                'type' => 'staff',
                'title' => 'Laporan Hasil Sinkronisasi Dosen & Staf',
                'source_db' => $config['database'],
                'source_table' => $config['table'],
                'total_source' => count($rows),
                'inserted' => $inserted,
                'updated' => $updated,
                'unchanged' => $unchanged,
                'total_changed' => $totalProcessed,
                'items' => $syncedItems,
            ];

            return redirect()->route('admin.member.index', ['tab' => 'non-mahasiswa'])
                ->with('syncReport', $syncReport)
                ->with('success', "Sinkronisasi berhasil! Sebanyak {$totalProcessed} data Dosen & Staf berhasil diproses ({$inserted} anggota baru ditambahkan, {$updated} diperbarui) tanpa mengubah sumber data eksternal.");
        } catch (\Throwable $e) {
            return redirect()->route('admin.member.index', ['tab' => 'non-mahasiswa'])
                ->with('error', "Gagal melakukan sinkronisasi dengan server kepegawaian: " . $e->getMessage());
        }
    }

    /**
     * Sinkronisasi data Mahasiswa langsung dari Database eksternal (db_student_usi tabel mhs)
     * Menggunakan koneksi READ-ONLY tanpa mengubah atau mempengaruhi database sumber
     * Tidak menghapus data lama, menambahkan mahasiswa baru dan memperbarui profil jika ada perubahan
     */
    public function syncStudentApi(Request $request)
    {
        // 1. Ambil konfigurasi database mahasiswa dari file .dbkon
        $config = $this->getDbkonConfig('student');

        try {
            // 2. Buat koneksi PDO ke database eksternal db_student_usi (READ ONLY)
            $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset=utf8mb4";
            $pdo = new \PDO($dsn, $config['user'], $config['pass'], [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_TIMEOUT => 5,
            ]);

            // 3. Ambil data dari tabel mhs dan join dengan tabel jrskampus untuk nama prodi (Strictly Read-Only Query)
            $sql = "SELECT 
                        m.nim, 
                        m.nm_mhs, 
                        m.jen_kel, 
                        m.tgl_lhr, 
                        m.alm, 
                        m.no_rmh, 
                        m.kota, 
                        m.kd_pos, 
                        m.telp, 
                        m.no_telp_hp, 
                        m.emailaddress, 
                        m.kd_lokal, 
                        m.kd_jrs, 
                        m.th_masuk, 
                        m.jenjang, 
                        j.nm_jrs
                    FROM mhs m
                    LEFT JOIN jrskampus j ON m.kd_jrs = j.kd_jrs
                    ORDER BY m.th_masuk DESC, m.nim ASC";

            $stmt = $pdo->query($sql);
            $rows = $stmt->fetchAll();

            // 4. Proses data mahasiswa: TIDAK ADA DATA LAMA YANG DIHAPUS
            // Sinkronisasi hanya menambahkan data yang belum ada di tabel member elibrary dan update profil jika ada perubahan
            $inserted = 0;
            $updated = 0;
            $unchanged = 0;
            $syncedItems = [];
            $now = Carbon::now();
            $today = Carbon::today()->toDateString();

            foreach ($rows as $row) {
                $nim = trim($row['nim'] ?? '');
                if (empty($nim)) {
                    continue;
                }

                $nama = trim($row['nm_mhs'] ?? '');
                if (empty($nama)) {
                    continue;
                }

                $email = trim($row['emailaddress'] ?? '');
                $phone = trim($row['no_telp_hp'] ?: ($row['telp'] ?? ''));

                // Susun alamat lengkap
                $addrParts = [];
                if (!empty($row['alm'])) {
                    $addr = trim($row['alm']);
                    if (!empty($row['no_rmh'])) {
                        $addr .= ' No. ' . trim($row['no_rmh']);
                    }
                    $addrParts[] = $addr;
                }
                if (!empty($row['kota'])) $addrParts[] = trim($row['kota']);
                if (!empty($row['kd_pos'])) $addrParts[] = trim($row['kd_pos']);
                $alamat = implode(', ', $addrParts);

                $prodi = trim($row['nm_jrs'] ?? '');
                $kelas = trim($row['kd_lokal'] ?? '');

                // Gender (1: Laki-laki, 2: Perempuan)
                $gender = (strtoupper(trim($row['jen_kel'] ?? '')) === 'P') ? 2 : 1;

                // Format tanggal lahir
                $birthDate = null;
                $rawBirth = trim($row['tgl_lhr'] ?? '');
                if (!empty($rawBirth) && $rawBirth !== '0000-00-00') {
                    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $rawBirth)) {
                        list($y, $m, $d) = explode('-', $rawBirth);
                        if ((int)$y > 1920 && (int)$m >= 1 && (int)$m <= 12 && (int)$d >= 1 && (int)$d <= 31 && checkdate((int)$m, (int)$d, (int)$y)) {
                            $birthDate = sprintf('%04d-%02d-%02d', (int)$y, (int)$m, (int)$d);
                        }
                    }
                }

                $notesArr = [];
                if (!empty($prodi)) $notesArr[] = "Prodi: $prodi";
                if (!empty($kelas)) $notesArr[] = "Kelas: $kelas";
                $memberNotes = !empty($notesArr) ? implode(' | ', $notesArr) : null;

                $existing = Member::find($nim);

                if (!$existing) {
                    // Mahasiswa baru: Masukkan ke tabel member
                    $expireDate = Member::calculateStudentExpireDate($nim, $today);

                    Member::create([
                        'member_id' => $nim,
                        'member_name' => $nama,
                        'gender' => $gender,
                        'birth_date' => $birthDate,
                        'member_type_id' => 1, // Mahasiswa
                        'member_address' => !empty($alamat) ? $alamat : null,
                        'member_email' => !empty($email) ? $email : null,
                        'member_phone' => !empty($phone) ? $phone : null,
                        'inst_name' => 'Universitas Siber Indonesia',
                        'member_notes' => $memberNotes,
                        'mpasswd' => Hash::make($birthDate ?: $nim),
                        'register_date' => $today,
                        'member_since_date' => $today,
                        'expire_date' => $expireDate,
                        'is_pending' => 0,
                        'input_date' => $now,
                        'last_update' => $now,
                    ]);
                    $inserted++;
                    $syncedItems[] = [
                        'status' => 'Baru Ditambahkan',
                        'badge' => 'new',
                        'member_id' => $nim,
                        'member_name' => $nama,
                        'prodi' => $prodi ?: '-',
                        'kelas' => $kelas ?: '-',
                        'email' => $email ?: '-',
                        'phone' => $phone ?: '-',
                        'changes' => 'Anggota baru ditambahkan',
                    ];
                } else {
                    // Mahasiswa sudah ada: Data TIDAK DIHAPUS, perbarui profil jika ada perubahan
                    $changes = [];

                    if (!empty($nama) && $existing->member_name !== $nama) {
                        $existing->member_name = $nama;
                        $changes[] = 'Nama';
                    }
                    if (!empty($email) && $existing->member_email !== $email) {
                        $existing->member_email = $email;
                        $changes[] = 'Email';
                    }
                    if (!empty($phone) && $existing->member_phone !== $phone) {
                        $existing->member_phone = $phone;
                        $changes[] = 'No. HP';
                    }
                    if (!empty($alamat) && $existing->member_address !== $alamat) {
                        $existing->member_address = $alamat;
                        $changes[] = 'Alamat';
                    }
                    if (!empty($memberNotes) && $existing->member_notes !== $memberNotes) {
                        $existing->member_notes = $memberNotes;
                        $changes[] = 'Prodi/Kelas';
                    }
                    if (!empty($birthDate) && $existing->birth_date !== $birthDate) {
                        $existing->birth_date = $birthDate;
                        $changes[] = 'Tgl Lahir';
                    }
                    if (!empty($gender) && (int)$existing->gender !== (int)$gender) {
                        $existing->gender = $gender;
                        $changes[] = 'Jenis Kelamin';
                    }

                    if (count($changes) > 0) {
                        $existing->last_update = $now;
                        $existing->save();
                        $updated++;
                        $syncedItems[] = [
                            'status' => 'Diperbarui',
                            'badge' => 'updated',
                            'member_id' => $nim,
                            'member_name' => $nama,
                            'prodi' => $prodi ?: '-',
                            'kelas' => $kelas ?: '-',
                            'email' => $email ?: '-',
                            'phone' => $phone ?: '-',
                            'changes' => implode(', ', $changes) . ' diperbarui',
                        ];
                    } else {
                        $unchanged++;
                    }
                }
            }

            $totalProcessed = $inserted + $updated;
            $syncReport = [
                'type' => 'mahasiswa',
                'title' => 'Laporan Hasil Sinkronisasi Mahasiswa',
                'source_db' => $config['database'],
                'source_table' => 'mhs',
                'total_source' => count($rows),
                'inserted' => $inserted,
                'updated' => $updated,
                'unchanged' => $unchanged,
                'total_changed' => $totalProcessed,
                'items' => $syncedItems,
            ];

            return redirect()->route('admin.member.index', ['tab' => 'mahasiswa'])
                ->with('syncReport', $syncReport)
                ->with('success', "Sinkronisasi berhasil! Sebanyak {$inserted} data mahasiswa baru berhasil ditambahkan" . ($updated > 0 ? " dan {$updated} data profil diperbarui." : ".") . " Seluruh data mahasiswa lama tetap terjaga aman.");
        } catch (\Throwable $e) {
            return redirect()->route('admin.member.index', ['tab' => 'mahasiswa'])
                ->with('error', "Gagal melakukan sinkronisasi dengan database mahasiswa: " . $e->getMessage());
        }
    }

    /**
     * Ekspor data anggota perpustakaan (Mahasiswa, Dosen & Staff, atau Semua Anggota) ke Excel, Word, PDF, CSV
     */
    public function export(Request $request)
    {
        $format = $request->input('format', 'excel');
        $tab = $request->input('tab');
        $type = $request->input('type');
        $typeId = $request->input('type_id');
        $status = $request->input('status');
        $search = $request->input('search');

        $query = Member::with(['memberType', 'activeLoans']);

        // Filter tab / type
        $categoryLabel = 'Semua Anggota';
        if ($tab === 'mahasiswa' || $type === 'mahasiswa') {
            $query->where('member_type_id', 1);
            $categoryLabel = 'Mahasiswa';
        } elseif ($tab === 'non-mahasiswa' || $type === 'non-mahasiswa' || $type === 'staff') {
            $query->where('member_type_id', '!=', 1);
            $categoryLabel = 'Dosen & Karyawan';
        } elseif (!empty($typeId)) {
            $query->where('member_type_id', $typeId);
            $mt = MemberType::find($typeId);
            if ($mt) {
                $categoryLabel = $mt->member_type_name;
            }
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('member_id', 'like', "%{$search}%")
                  ->orWhere('member_name', 'like', "%{$search}%")
                  ->orWhere('member_email', 'like', "%{$search}%")
                  ->orWhere('member_phone', 'like', "%{$search}%")
                  ->orWhere('member_notes', 'like', "%{$search}%");
            });
        }

        if ($status === 'active') {
            $query->where('is_pending', 0)
                  ->where(function ($q) {
                      $q->whereNull('expire_date')
                        ->orWhere('expire_date', '>=', Carbon::today()->toDateString());
                  });
        } elseif ($status === 'expired') {
            $query->where('is_pending', 0)
                  ->whereNotNull('expire_date')
                  ->where('expire_date', '<', Carbon::today()->toDateString());
        } elseif ($status === 'inactive') {
            $query->where('is_pending', 1);
        }

        $members = $query->orderBy('member_id', 'asc')->get();

        $headers = [
            'No',
            'NIM / NIP / ID Anggota',
            'Nama Lengkap',
            'Tipe Anggota',
            'Program Studi / Unit',
            'L/P',
            'Email',
            'No. Telepon / WhatsApp',
            'Tgl Registrasi',
            'Masa Berlaku',
            'Status',
            'Pinjaman Aktif'
        ];

        $rows = [];
        $no = 1;
        foreach ($members as $m) {
            $genderText = $m->gender == 1 ? 'L' : ($m->gender == 2 ? 'P' : '-');
            $regDate = !empty($m->register_date) ? Carbon::parse($m->register_date)->format('d/m/Y') : '-';
            $expDate = $m->expiry_display;
            $prodi = $m->isStudent() ? $m->prodi_name : ($m->inst_name ?: '-');

            $rows[] = [
                'no'           => $no++,
                'member_id'    => $m->member_id,
                'name'         => $m->member_name,
                'type'         => $m->memberType?->member_type_name ?? 'Mahasiswa',
                'prodi'        => $prodi,
                'gender'       => $genderText,
                'email'        => $m->member_email ?: '-',
                'phone'        => $m->member_phone ?: '-',
                'register'     => $regDate,
                'expire'       => $expDate,
                'status'       => $m->status_label,
                'active_loans' => $m->activeLoans->count() . ' Buku',
            ];
        }

        $title = 'Laporan Data Anggota Perpustakaan (' . $categoryLabel . ')';
        $filename = 'Laporan_Anggota_' . Str::slug($categoryLabel, '_') . '_' . date('Ymd_His');

        $statusText = match ($status) {
            'active'   => 'Anggota Aktif',
            'expired'  => 'Masa Berlaku Kedaluwarsa',
            'inactive' => 'Non-Aktif / Ditangguhkan',
            default    => 'Semua Status'
        };

        $metadata = [
            'Jenis Laporan'     => 'Rekap Data Anggota Perpustakaan',
            'Kategori Anggota'  => $categoryLabel,
            'Status Keaktifan'  => $statusText,
            'Total Anggota'     => count($rows) . ' Orang',
            'Tanggal Cetak'     => Carbon::now()->translatedFormat('d F Y, H:i') . ' WIB',
            'Dicetak Oleh'      => auth()->user()->username ?? 'Petugas Perpustakaan'
        ];

        $chartImages = [];
        if ($request->filled('chart_image')) {
            $chartImages['Grafik Statistik & Distribusi Anggota'] = $request->input('chart_image');
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



