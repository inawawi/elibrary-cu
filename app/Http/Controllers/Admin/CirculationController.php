<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Loan;
use App\Models\LoanHistory;
use App\Models\Member;
use App\Models\Reserve;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CirculationController extends Controller
{
    public function index(Request $request)
    {
        $memberId = $request->input('member_id');
        $member = null;
        $activeLoans = collect();
        $reserves = collect();
        $canBorrow = true;
        $borrowBlockReason = null;

        if (!empty($memberId)) {
            $member = Member::with(['memberType', 'activeLoans.item.biblio'])->where('member_id', $memberId)->first();

            if ($member) {
                $activeLoans = $member->activeLoans;
                $reserves = Reserve::where('member_id', $member->member_id)
                    ->with(['biblio.authors', 'item.location'])
                    ->orderBy('reserve_date', 'desc')
                    ->get();

                if ($member->is_pending == 1) {
                    $canBorrow = false;
                    $borrowBlockReason = 'Status keanggotaan sedang dinonaktifkan (Suspen). Hubungi petugas perpustakaan.';
                } elseif ($member->isExpired()) {
                    $canBorrow = false;
                    $borrowBlockReason = 'Masa berlaku keanggotaan telah habis (' . $member->expire_date . ').';
                }

                $loanLimit = $member->memberType?->loan_limit ?? 3;
                if ($activeLoans->count() >= $loanLimit) {
                    $canBorrow = false;
                    $borrowBlockReason = 'Batas maksimal peminjaman (' . $loanLimit . ' buku) telah tercapai.';
                }
            }
        }

        return view('admin.circulation.index', compact('member', 'activeLoans', 'reserves', 'memberId', 'canBorrow', 'borrowBlockReason'));
    }

    public function loan(Request $request)
    {
        $request->validate([
            'member_id' => 'required|string|exists:member,member_id',
            'item_code' => 'required|string|exists:item,item_code',
        ]);

        $member = Member::with('memberType')->where('member_id', $request->member_id)->firstOrFail();
        $item = Item::with(['biblio', 'activeLoan'])->where('item_code', $request->item_code)->firstOrFail();

        if ($member->is_pending == 1) {
            return back()->with('error', 'Peminjaman ditolak: Status keanggotaan "' . $member->member_name . '" sedang dinonaktifkan.');
        }

        if ($member->isExpired()) {
            return back()->with('error', 'Peminjaman ditolak: Masa berlaku keanggotaan telah habis (' . $member->expire_date . ').');
        }

        if ($item->activeLoan) {
            return back()->with('error', 'Buku dengan kode eksemplar "' . $item->item_code . '" sedang dipinjam oleh anggota lain!');
        }

        $loanPeriode = $member->memberType?->loan_periode ?? 7;
        $loanDate = Carbon::today();
        $dueDate = Carbon::today()->addDays($loanPeriode);

        Loan::create([
            'item_code' => $item->item_code,
            'member_id' => $member->member_id,
            'loan_date' => $loanDate->toDateString(),
            'due_date' => $dueDate->toDateString(),
            'renewed' => 0,
            'loan_rules_id' => 1,
            'is_lent' => 1,
            'is_return' => 0,
            'input_date' => Carbon::now(),
            'last_update' => Carbon::now(),
            'uid' => auth()->id() ?? 1,
        ]);

        // Hapus reservasi jika eksemplar ini sebelumnya direservasi
        Reserve::where('item_code', $item->item_code)->delete();

        return redirect()->route('admin.circulation.index', ['member_id' => $member->member_id])
            ->with('success', 'Buku "' . $item->biblio->title . '" (' . $item->item_code . ') berhasil dipinjam hingga ' . $dueDate->format('d/m/Y') . '!');
    }

    public function returnItem(Request $request)
    {
        $request->validate([
            'item_code' => 'required|string',
        ]);

        $loan = Loan::with(['member.memberType', 'item.biblio'])
            ->where('item_code', $request->item_code)
            ->where('is_return', 0)
            ->first();

        if (!$loan) {
            return back()->with('error', 'Tidak ada data peminjaman aktif untuk kode eksemplar: ' . $request->item_code);
        }

        $today = Carbon::today();
        $fine = $loan->calculateFine();

        // Mark returned
        $loan->is_return = 1;
        $loan->return_date = $today->toDateString();
        $loan->actual = $today->toDateString();
        $loan->last_update = Carbon::now();
        $loan->save();

        // Log to loan_history
        LoanHistory::create([
            'loan_id' => $loan->loan_id,
            'item_code' => $loan->item_code,
            'member_id' => $loan->member_id,
            'loan_date' => $loan->loan_date,
            'due_date' => $loan->due_date,
            'return_date' => $today->toDateString(),
        ]);

        $msg = 'Buku "' . ($loan->item?->biblio?->title ?? $loan->item_code) . '" berhasil dikembalikan.';
        if ($fine > 0) {
            $msg .= ' Denda keterlambatan: Rp ' . number_format($fine, 0, ',', '.');
        }

        $memberId = $request->input('redirect_member_id') ?? $loan->member_id;
        return redirect()->route('admin.circulation.index', ['member_id' => $memberId])->with('success', $msg);
    }

    public function activeLoans(Request $request)
    {
        $status = $request->input('status', 'all'); // all, normal, overdue
        $query = Loan::active()->with(['member.memberType', 'item.biblio']);

        if ($status === 'overdue') {
            $query->overdue();
        }

        $loans = $query->orderBy('due_date', 'asc')->paginate(20)->withQueryString();

        return view('admin.circulation.active', compact('loans', 'status'));
    }

    public function history(Request $request)
    {
        $search = $request->input('search');

        $query = LoanHistory::with(['member', 'item.biblio']);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('item_code', 'like', "%{$search}%")
                  ->orWhere('member_id', 'like', "%{$search}%")
                  ->orWhereHas('member', function ($mq) use ($search) {
                      $mq->where('member_name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('item.biblio', function ($bq) use ($search) {
                      $bq->where('title', 'like', "%{$search}%");
                  });
            });
        }

        $histories = $query->orderBy('return_date', 'desc')->paginate(20)->withQueryString();

        return view('admin.circulation.history', compact('histories', 'search'));
    }
}
