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
     * Sinkronisasi data Dosen & Staf dari database kepegawaian eksternal (karyawanbs1)
     * Menggunakan koneksi READ-ONLY (tanpa mengubah sumber data)
     */
    public function syncExternal()
    {
        // 1. Cari file konfigurasi .dbkon atau .db
        $possiblePaths = [
            base_path('.dbkon'),
            base_path('../.dbkon'),
            base_path('.db'),
            base_path('../.db'),
            'c:/laragon/www/elibrarycu/.dbkon',
            'c:/laragon/www/elibrarycu/.db',
        ];

        $config = [
            'host' => '172.16.192.47',
            'port' => 3304,
            'user' => 'root',
            'pass' => 'sT4ff.Cyb3r2o26',
            'database' => 'stafF_br1',
            'table' => 'karyawanbs1',
        ];

        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                foreach ($lines as $line) {
                    $cleanLine = trim(ltrim($line, '#;'));
                    if (strpos($cleanLine, ':') !== false) {
                        list($key, $val) = explode(':', $cleanLine, 2);
                        $key = strtolower(trim($key));
                        $val = trim($val);
                        if (in_array($key, ['ip', 'host', 'server'])) $config['host'] = $val;
                        if (in_array($key, ['port'])) $config['port'] = (int)$val;
                        if (in_array($key, ['user', 'username'])) $config['user'] = $val;
                        if (in_array($key, ['pass', 'password', 'pwd'])) $config['pass'] = $val;
                        if (in_array($key, ['db', 'database', 'dbname'])) $config['database'] = $val;
                        if (in_array($key, ['table', 'tbl', 'tabel'])) $config['table'] = $val;
                    }
                }
                break;
            }
        }

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
                    $member->member_name = $fullName;
                    $member->member_type_id = $typeId;
                    $member->gender = $gender;
                    if (!empty($birthDate)) {
                        $member->birth_date = $birthDate;
                    }
                    if (!empty($jabatan)) {
                        $member->member_notes = $jabatan;
                    }
                    if (!empty($email)) {
                        $member->member_email = $email;
                    }
                    if (!empty($phone)) {
                        $member->member_phone = $phone;
                    }
                    if (!empty($alamat)) {
                        $member->member_address = $alamat;
                    }
                    $member->expire_date = null; // Aktif selama bertugas
                    $member->last_update = $now;
                    $member->save();
                    $updated++;
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
            return redirect()->route('admin.member.index', ['tab' => 'non-mahasiswa'])
                ->with('success', "Sinkronisasi berhasil! Sebanyak {$totalProcessed} data Dosen & Staf berhasil diproses ({$inserted} anggota baru ditambahkan, {$updated} diperbarui) tanpa mengubah sumber data eksternal.");
        } catch (\Throwable $e) {
            return redirect()->route('admin.member.index', ['tab' => 'non-mahasiswa'])
                ->with('error', "Gagal melakukan sinkronisasi dengan server kepegawaian: " . $e->getMessage());
        }
    }

    /**
     * Sinkronisasi data Mahasiswa dari API Web Student (students.cyber-univ.ac.id)
     * Menggunakan koneksi READ-ONLY (GET) tanpa mengubah web/data sumber
     * Tidak menghapus data lama, hanya menambahkan mahasiswa yang belum ada di database elibrary
     */
    public function syncStudentApi(Request $request)
    {
        // 1. Cari konfigurasi API dari file .dbkon atau .db
        $possiblePaths = [
            base_path('.dbkon'),
            base_path('../.dbkon'),
            base_path('.db'),
            base_path('../.db'),
            'c:/laragon/www/elibrarycu/.dbkon',
            'c:/laragon/www/elibrarycu/.db',
        ];

        $apiUrl = 'https://students.cyber-univ.ac.id/api/mahasiswa/63251171';
        $apiKey = 'randstudent851851851yes';

        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                foreach ($lines as $line) {
                    $cleanLine = trim(ltrim($line, '#;'));
                    if (strpos($cleanLine, ':') !== false) {
                        list($k, $v) = explode(':', $cleanLine, 2);
                        $k = strtolower(trim($k));
                        $v = trim($v);
                        if (str_contains($k, 'api') && (str_contains($k, 'url') || str_contains($k, 'endpoint'))) {
                            $apiUrl = $v;
                        } elseif ((str_contains($k, 'api') && str_contains($k, 'key')) || (str_contains($k, 'key') && str_contains($k, 'api'))) {
                            $apiKey = $v;
                        }
                    }
                }
                break;
            }
        }

        // Bolehkan override endpoint jika dikirim dari input form/modal
        if ($request->filled('api_url')) {
            $apiUrl = trim($request->input('api_url'));
        }

        try {
            // 2. Kirim request GET (Read-Only) ke API Web Student
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $apiUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 15);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "X-API-KEY: " . $apiKey,
                "Accept: application/json",
                "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) ElibraryCU/1.0",
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            if ($response === false) {
                return redirect()->route('admin.member.index', ['tab' => 'mahasiswa'])
                    ->with('error', "Gagal menghubungi API Web Student: " . $curlError);
            }

            if ($httpCode === 401) {
                return redirect()->route('admin.member.index', ['tab' => 'mahasiswa'])
                    ->with('error', "Autentikasi API Web Student gagal (401 Unauthorized). Pastikan API Key di file .dbkon sudah sesuai.");
            }

            if ($httpCode >= 400) {
                return redirect()->route('admin.member.index', ['tab' => 'mahasiswa'])
                    ->with('error', "Server API Web Student mengembalikan status HTTP {$httpCode}.");
            }

            // 3. Parse JSON response
            $json = json_decode($response, true);
            if (!is_array($json)) {
                return redirect()->route('admin.member.index', ['tab' => 'mahasiswa'])
                    ->with('error', "Format data dari API Web Student tidak valid.");
            }

            // Ekstrak array data mahasiswa
            $items = $json;
            if (isset($json['data']) && is_array($json['data'])) {
                $items = $json['data'];
            } elseif (isset($json['nim']) || isset($json['member_id'])) {
                $items = [$json];
            }

            // Jika data dari endpoint kosong
            if (empty($items)) {
                return redirect()->route('admin.member.index', ['tab' => 'mahasiswa'])
                    ->with('info', "Koneksi ke API Web Student berhasil (HTTP 200), namun belum ada data mahasiswa baru yang tersedia dari endpoint '{$apiUrl}'. Seluruh data mahasiswa yang sudah ada di e-Library tetap aman.");
            }

            // 4. Proses data mahasiswa: TIDAK ADA DATA LAMA YANG DIHAPUS
            // Sinkronisasi hanya menambahkan data yang belum ada di tabel member elibrary
            $inserted = 0;
            $updated = 0;
            $now = Carbon::now();
            $today = Carbon::today()->toDateString();

            foreach ($items as $item) {
                if (!is_array($item)) {
                    continue;
                }

                $nim = trim($item['nim'] ?? $item['member_id'] ?? $item['nim_mhs'] ?? $item['id'] ?? '');
                if (empty($nim)) {
                    continue;
                }

                $nama = trim($item['nama'] ?? $item['nama_mhs'] ?? $item['name'] ?? $item['member_name'] ?? '');
                if (empty($nama)) {
                    continue;
                }

                $email = trim($item['email'] ?? $item['email_mhs'] ?? '');
                $phone = trim($item['telepon'] ?? $item['telp'] ?? $item['no_hp'] ?? $item['hp'] ?? $item['phone'] ?? '');
                $alamat = trim($item['alamat'] ?? $item['alamat_mhs'] ?? $item['address'] ?? '');
                $prodi = trim($item['prodi'] ?? $item['program_studi'] ?? $item['jurusan'] ?? $item['nama_prodi'] ?? '');
                $kelas = trim($item['kelas'] ?? $item['kd_kelas'] ?? '');
                
                $sexRaw = strtoupper(trim($item['jenis_kelamin'] ?? $item['jk'] ?? $item['gender'] ?? $item['sex'] ?? ''));
                $gender = in_array($sexRaw, ['P', '2', 'PEREMPUAN', 'FEMALE']) ? 2 : 1;

                // Format tanggal lahir yang valid
                $birthDate = null;
                $rawBirth = trim($item['tgl_lahir'] ?? $item['tgllahir'] ?? $item['tanggal_lahir'] ?? $item['birth_date'] ?? '');
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
                } else {
                    // Mahasiswa sudah ada: Data TIDAK DIHAPUS, lengkapi field yang masih kosong
                    $changed = false;
                    if (empty($existing->member_email) && !empty($email)) {
                        $existing->member_email = $email;
                        $changed = true;
                    }
                    if (empty($existing->member_phone) && !empty($phone)) {
                        $existing->member_phone = $phone;
                        $changed = true;
                    }
                    if (empty($existing->member_notes) && !empty($memberNotes)) {
                        $existing->member_notes = $memberNotes;
                        $changed = true;
                    }
                    if (empty($existing->birth_date) && !empty($birthDate)) {
                        $existing->birth_date = $birthDate;
                        $changed = true;
                    }
                    if ($changed) {
                        $existing->last_update = $now;
                        $existing->save();
                        $updated++;
                    }
                }
            }

            return redirect()->route('admin.member.index', ['tab' => 'mahasiswa'])
                ->with('success', "Sinkronisasi berhasil! Sebanyak {$inserted} data mahasiswa baru berhasil ditambahkan" . ($updated > 0 ? " dan {$updated} data profil diperbarui." : ".") . " Seluruh data mahasiswa lama tetap terjaga aman.");
        } catch (\Throwable $e) {
            return redirect()->route('admin.member.index', ['tab' => 'mahasiswa'])
                ->with('error', "Gagal melakukan sinkronisasi dengan API Web Student: " . $e->getMessage());
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



