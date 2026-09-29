<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\MemberType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');

        $query = Member::with(['memberType', 'activeLoans']);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('member_id', 'like', "%{$search}%")
                  ->orWhere('member_name', 'like', "%{$search}%")
                  ->orWhere('member_email', 'like', "%{$search}%")
                  ->orWhere('member_phone', 'like', "%{$search}%");
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
        $memberTypes = MemberType::all();

        return view('admin.member.index', compact('members', 'search', 'typeId', 'status', 'memberTypes'));
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
        $periodeDays = $memberType?->member_periode ?? 365;

        Member::create([
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
            'expire_date' => Carbon::today()->addDays($periodeDays)->toDateString(),
            'is_pending' => 0,
            'input_date' => Carbon::now(),
            'last_update' => Carbon::now(),
        ]);

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

        $validated['last_update'] = Carbon::now();
        $member->update($validated);

        return redirect()->route('admin.member.index')->with('success', 'Data anggota ' . $member->member_name . ' berhasil diperbarui!');
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

    public function destroy($id)
    {
        $member = Member::with('activeLoans')->findOrFail($id);

        if ($member->activeLoans->isNotEmpty()) {
            return back()->with('error', 'Anggota tidak dapat dihapus karena masih memiliki pinjaman buku yang belum dikembalikan!');
        }

        $member->delete();
        return redirect()->route('admin.member.index')->with('success', 'Anggota berhasil dihapus.');
    }
}
