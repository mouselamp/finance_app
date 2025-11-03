<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;
use App\Models\Transaction;

class AccountController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();

        $accounts = Account::where('user_id', $user->id)->get();

        $totalBalance = $accounts->sum('balance');

        return view('accounts.index', compact('accounts', 'totalBalance'));
    }

    public function create()
    {
        $accountTypes = (new Account())->getAccountTypes();
        return view('accounts.create', compact('accountTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:cash,bank,paylater',
            'balance' => 'required|numeric|min:0',
            'note' => 'nullable|string'
        ]);

        $user = auth()->user();

        $account = Account::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'type' => $request->type,
            'balance' => $request->balance,
            'note' => $request->note
        ]);

        return redirect()->route('accounts.index')
            ->with('success', 'Akun berhasil ditambahkan!');
    }

    public function show(Account $account)
    {
        if ($account->user_id !== auth()->id()) {
            abort(403);
        }

        $transactions = Transaction::where('user_id', auth()->id())
            ->where(function($query) use ($account) {
                $query->where('account_id', $account->id)
                      ->orWhere('related_account_id', $account->id);
            })
            ->with('category', 'relatedAccount')
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('accounts.show', compact('account', 'transactions'));
    }

    public function edit(Account $account)
    {
        if ($account->user_id !== auth()->id()) {
            abort(403);
        }

        $accountTypes = (new Account())->getAccountTypes();
        return view('accounts.edit', compact('account', 'accountTypes'));
    }

    public function update(Request $request, Account $account)
    {
        if ($account->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'note' => 'nullable|string'
        ]);

        $account->update([
            'name' => $request->name,
            'note' => $request->note
        ]);

        return redirect()->route('accounts.index')
            ->with('success', 'Akun berhasil diperbarui!');
    }

    public function destroy(Account $account)
    {
        if ($account->user_id !== auth()->id()) {
            abort(403);
        }

        // Check if account has transactions
        $transactionCount = Transaction::where('user_id', auth()->id())
            ->where(function($query) use ($account) {
                $query->where('account_id', $account->id)
                      ->orWhere('related_account_id', $account->id);
            })
            ->count();

        if ($transactionCount > 0) {
            return redirect()->route('accounts.index')
                ->with('error', 'Akun tidak dapat dihapus karena masih memiliki transaksi.');
        }

        $account->delete();

        return redirect()->route('accounts.index')
            ->with('success', 'Akun berhasil dihapus!');
    }
}
