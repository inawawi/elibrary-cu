<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    private function checkDeveloperAccess()
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();
        if (!$currentUser || !$currentUser->isDeveloper()) {
            abort(403, 'Akses ditolak: Menu Manajemen User Admin khusus untuk role Pengembang Sistem.');
        }
    }

    public function index(Request $request)
    {
        $this->checkDeveloperAccess();

        $search = $request->input('search');
        $roleFilter = $request->input('role');

        $query = User::query();

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                  ->orWhere('realname', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if (!empty($roleFilter)) {
            $query->where('role', $roleFilter);
        }

        $users = $query->orderBy('user_id', 'asc')->paginate(15)->withQueryString();

        // Stat counts
        $totalUsers = User::count();
        $developerCount = User::where('role', 'pengembang sistem')->count();
        $adminCount = User::where(function($q) {
            $q->where('role', 'administrator')->orWhereNull('role')->orWhere('role', '');
        })->count();
        $staffCount = User::where('role', 'staf')->count();

        return view('admin.users.index', compact(
            'users', 'search', 'roleFilter',
            'totalUsers', 'developerCount', 'adminCount', 'staffCount'
        ));
    }

    public function store(Request $request)
    {
        $this->checkDeveloperAccess();

        $validated = $request->validate([
            'username' => 'required|string|max:50|alpha_dash|unique:user,username',
            'realname' => 'required|string|max:100',
            'email' => 'required|email|max:100|unique:user,email',
            'role' => 'required|in:pengembang sistem,administrator,staf',
            'password' => 'required|string|min:6',
        ], [
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan oleh user lain.',
            'username.alpha_dash' => 'Username hanya boleh berupa huruf, angka, tanda minus, dan garis bawah.',
            'realname.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.unique' => 'Alamat email sudah terdaftar.',
            'role.required' => 'Role pengguna wajib dipilih.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal 6 karakter.',
        ]);

        $roleGroupMap = [
            'pengembang sistem' => '2',
            'administrator' => '1',
            'staf' => '3',
        ];

        $groupId = $roleGroupMap[$validated['role']] ?? '1';

        User::create([
            'username' => trim($validated['username']),
            'realname' => trim($validated['realname']),
            'email' => strtolower(trim($validated['email'])),
            'role' => $validated['role'],
            'passwd' => Hash::make($validated['password']),
            'user_type' => 1,
            'groups' => serialize([$groupId]),
            'input_date' => Carbon::now()->toDateString(),
            'last_update' => Carbon::now(),
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User admin baru "' . $validated['realname'] . '" (' . $validated['username'] . ') berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $this->checkDeveloperAccess();

        $user = User::findOrFail($id);

        $validated = $request->validate([
            'username' => 'required|string|max:50|alpha_dash|unique:user,username,' . $id . ',user_id',
            'realname' => 'required|string|max:100',
            'email' => 'required|email|max:100|unique:user,email,' . $id . ',user_id',
            'role' => 'required|in:pengembang sistem,administrator,staf',
            'password' => 'nullable|string|min:6',
        ], [
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan oleh user lain.',
            'realname.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.unique' => 'Alamat email sudah terdaftar.',
            'password.min' => 'Kata sandi minimal 6 karakter.',
        ]);

        $roleGroupMap = [
            'pengembang sistem' => '2',
            'administrator' => '1',
            'staf' => '3',
        ];

        $groupId = $roleGroupMap[$validated['role']] ?? '1';

        $updateData = [
            'username' => trim($validated['username']),
            'realname' => trim($validated['realname']),
            'email' => strtolower(trim($validated['email'])),
            'role' => $validated['role'],
            'groups' => serialize([$groupId]),
            'last_update' => Carbon::now(),
        ];

        if (!empty($validated['password'])) {
            $updateData['passwd'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        return redirect()->route('admin.users.index')->with('success', 'Data user admin "' . $user->realname . '" berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $this->checkDeveloperAccess();

        /** @var User $currentUser */
        $currentUser = Auth::user();

        if ((int)$currentUser->user_id === (int)$id) {
            return back()->with('error', 'Tindakan ditolak: Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif digunakan.');
        }

        $user = User::findOrFail($id);

        // Jangan izinkan menghapus satu-satunya pengembang sistem jika tersisa 1
        if ($user->isDeveloper() && User::where('role', 'pengembang sistem')->count() <= 1) {
            return back()->with('error', 'Tindakan ditolak: Minimal harus ada 1 akun dengan role Pengembang Sistem.');
        }

        $name = $user->realname ?: $user->username;
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User admin "' . $name . '" berhasil dihapus.');
    }
}
