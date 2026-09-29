<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            'password' => 'required|string',
        ]);

        $member = Member::where('member_id', $request->member_id)->first();

        if (!$member) {
            return back()->withErrors(['member_id' => 'ID Anggota tidak ditemukan.'])->withInput();
        }

        $passwordValid = false;
        $inputPassword = trim($request->password);

        // 1. Check Bcrypt hash
        if (!empty($member->mpasswd) && Hash::check($inputPassword, $member->mpasswd)) {
            $passwordValid = true;
        }
        // 2. Check if password matches member_id (NIM default)
        elseif ($inputPassword === $member->member_id) {
            $passwordValid = true;
        }
        // 3. Check if password matches PIN
        elseif (!empty($member->pin) && $inputPassword === $member->pin) {
            $passwordValid = true;
        }
        // 4. Check if password matches birth date in various formats
        elseif (!empty($member->birth_date)) {
            $birthDate = $member->birth_date;
            $birthFormats = [
                $birthDate, // YYYY-MM-DD
                str_replace('-', '', $birthDate), // YYYYMMDD
                date('dmY', strtotime($birthDate)), // DDMMYYYY
                date('d-m-Y', strtotime($birthDate)), // DD-MM-YYYY
                date('d/m/Y', strtotime($birthDate)), // DD/MM/YYYY
                date('dmy', strtotime($birthDate)), // DDMMYY
            ];
            if (in_array($inputPassword, $birthFormats, true)) {
                $passwordValid = true;
            }
        }
        // 5. Legacy MD5 or plain
        elseif (!empty($member->mpasswd) && (md5($inputPassword) === $member->mpasswd || $inputPassword === $member->mpasswd)) {
            $passwordValid = true;
        }

        if (!$passwordValid) {
            return back()->withErrors(['password' => 'Kata sandi salah. Gunakan NIM atau tanggal lahir Anda.'])->withInput();
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

        return view('member.dashboard', compact('member', 'activeLoans', 'loanHistories', 'announcement', 'libraryRules', 'isContactIncomplete'));
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

    public function logout(Request $request)
    {
        Auth::guard('member')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('opac.index')->with('info', 'Anda telah keluar dari area anggota.');
    }
}
