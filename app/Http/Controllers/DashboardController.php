<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Account;
use App\Models\Category;
use App\Models\Installment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Get total balance from all accounts
        $totalBalance = Account::where('user_id', $user->id)->sum('balance');

        // Get recent transactions
        $recentTransactions = Transaction::where('user_id', $user->id)
            ->with('category', 'account')
            ->orderBy('date', 'desc')
            ->limit(5)
            ->get();

        // Get current month income and expenses
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $monthlyIncome = Transaction::where('user_id', $user->id)
            ->where('type', 'income')
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->sum('amount');

        $monthlyExpenses = Transaction::where('user_id', $user->id)
            ->where('type', 'expense')
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->sum('amount');

        // Get accounts
        $accounts = Account::where('user_id', $user->id)->get();

        // Get upcoming installments
        $upcomingInstallments = Installment::where('user_id', $user->id)
            ->where('status', 'unpaid')
            ->where('due_date', '>=', Carbon::today())
            ->where('due_date', '<=', Carbon::today()->addDays(30))
            ->orderBy('due_date', 'asc')
            ->limit(3)
            ->get();

        return view('dashboard.index', compact(
            'totalBalance',
            'recentTransactions',
            'monthlyIncome',
            'monthlyExpenses',
            'accounts',
            'upcomingInstallments'
        ));
    }
}