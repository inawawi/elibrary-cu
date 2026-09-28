<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Biblio;
use App\Models\GuestBook;
use App\Models\Item;
use App\Models\Loan;
use App\Models\Member;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today()->toDateString();

        $stats = [
            'total_biblio' => Biblio::count(),
            'total_items' => Item::count(),
            'total_members' => Member::count(),
            'active_loans' => Loan::active()->count(),
            'overdue_loans' => Loan::overdue()->count(),
            'today_visitors' => GuestBook::where('tgl', $today)->count(),
        ];

        $recentLoans = Loan::with(['member', 'item.biblio'])
            ->orderBy('input_date', 'desc')
            ->take(6)
            ->get();

        $overdueLoansList = Loan::overdue()
            ->with(['member', 'item.biblio'])
            ->orderBy('due_date', 'asc')
            ->take(5)
            ->get();

        $recentVisitors = GuestBook::orderBy('tgl', 'desc')
            ->orderBy('jam', 'desc')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentLoans', 'overdueLoansList', 'recentVisitors'));
    }
}
