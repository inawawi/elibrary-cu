<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;

class AuthController extends Controller
{
    public static function generateCaptcha(string $sessionKey = 'admin_captcha'): array
    {
        $num1 = rand(0, 9);
        $num2 = rand(0, 9);
        $operator = rand(0, 1) === 1 ? '+' : '-';
        if ($operator === '-' && $num1 < $num2) {
            $temp = $num1;
            $num1 = $num2;
            $num2 = $temp;
        }
        $answer = $operator === '+' ? ($num1 + $num2) : ($num1 - $num2);
        session([$sessionKey => $answer]);

        // Enkripsi token agar sinkron dengan form dan kebal dari expired session/multi-tab/bfcache
        $token = Crypt::encryptString("{$num1}|{$operator}|{$num2}|{$answer}|" . time());

        return [
            'num1' => $num1,
            'num2' => $num2,
            'operator' => $operator,
            'question' => "{$num1} {$operator} {$num2}",
            'token' => $token,
        ];
    }

    public static function validateCaptcha(Request $request, string $sessionKey = 'admin_captcha'): bool
    {
        $inputAnswer = trim($request->input('captcha', ''));
        if ($inputAnswer === '' || !is_numeric($inputAnswer)) {
            return false;
        }
        $userVal = intval($inputAnswer);

        // 1. Validasi via captcha_token terenkripsi (kebal multi-tab / bfcache / session race)
        if ($request->filled('captcha_token')) {
            try {
                $decrypted = Crypt::decryptString($request->input('captcha_token'));
                $parts = explode('|', $decrypted);
                if (count($parts) >= 5) {
                    $expected = intval($parts[3]);
                    $timestamp = intval($parts[4]);
                    // Berlaku maksimal 30 menit (1800 detik)
                    if ((time() - $timestamp) <= 1800 && $userVal === $expected) {
                        session()->forget($sessionKey);
                        return true;
                    }
                }
            } catch (\Exception $e) {
                // Token tidak valid atau dimanipulasi, lanjut fallback session
            }
        }

        // 2. Fallback validasi via session
        $sessionCaptcha = session($sessionKey);
        if ($sessionCaptcha !== null && $userVal === intval($sessionCaptcha)) {
            session()->forget($sessionKey);
            return true;
        }

        return false;
    }

    public function refreshCaptcha(Request $request)
    {
        $type = $request->query('type', 'admin');
        $sessionKey = $type === 'member' ? 'member_captcha' : 'admin_captcha';
        $captcha = self::generateCaptcha($sessionKey);
        return response()->json($captcha);
    }

    public function showLoginForm()
    {
        if (Auth::guard('web')->check()) {
            return redirect()->route('admin.dashboard');
        }
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'Username atau email wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $user = User::where('username', $request->username)
            ->orWhere('email', $request->username)
            ->first();

        if (!$user) {
            return back()->withErrors(['username' => 'Pengguna tidak ditemukan.'])->withInput();
        }

        $valid = false;

        // Check Bcrypt
        if (!empty($user->passwd) && Hash::check($request->password, $user->passwd)) {
            $valid = true;
        }
        // Legacy MD5 or plain
        elseif (!empty($user->passwd) && (md5($request->password) === $user->passwd || $request->password === $user->passwd)) {
            $valid = true;
            $user->passwd = Hash::make($request->password);
            $user->save();
        }
        // Admin default setup password fallback (admin123)
        elseif ($request->password === 'admin123' && $user->username === 'AdminLib') {
            $valid = true;
            $user->passwd = Hash::make('admin123');
            $user->save();
        }

        if (!$valid) {
            return back()->withErrors(['password' => 'Kata sandi tidak sesuai.'])->withInput();
        }

        Auth::guard('web')->login($user);
        $user->last_login = Carbon::now();
        $user->last_login_ip = $request->ip();
        $user->save();

        $request->session()->regenerate();

        return redirect()->route('admin.dashboard')->with('success', 'Selamat datang kembali, ' . ($user->realname ?: $user->username) . '!');
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('info', 'Anda telah keluar dari panel admin.');
    }
}
