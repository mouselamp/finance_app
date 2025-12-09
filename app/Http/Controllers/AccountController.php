<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;
use App\Models\Transaction;

/**
 * AccountController - Web Controller
 *
 * This controller only handles VIEW RENDERING.
 * All CRUD operations (store, update, destroy) are handled by ApiAccountController.
 */
class AccountController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of accounts.
     */
    public function index()
    {
        $user = auth()->user();
        $accounts = Account::where('user_id', $user->id)->get();
        $totalBalance = $accounts->sum('balance');

        return view('accounts.index', compact('accounts', 'totalBalance'));
    }

    /**
     * Show the form for creating a new account.
     */
    public function create()
    {
        $accountTypes = (new Account())->getAccountTypes();
        return view('accounts.create', compact('accountTypes'));
    }

    /**
     * Display the specified account.
     * Allows group members to view accounts in read-only mode.
     */
    public function show(Account $account)
    {
        $user = auth()->user();
        $isOwner = $account->user_id === $user->id;
        $canAccess = $isOwner;

        // Check if user can access via group membership
        if (!$isOwner && $user->group_id) {
            $canAccess = \App\Models\User::where('group_id', $user->group_id)
                ->where('id', $account->user_id)
                ->exists();
        }

        if (!$canAccess) {
            abort(403, 'Anda tidak memiliki akses ke akun ini.');
        }

        // Get transactions from the account owner
        $transactions = Transaction::where('user_id', $account->user_id)
            ->where(function($query) use ($account) {
                $query->where('account_id', $account->id)
                      ->orWhere('related_account_id', $account->id);
            })
            ->with('category', 'relatedAccount', 'user')
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('accounts.show', compact('account', 'transactions', 'isOwner'));
    }

    /**
     * Show the form for editing the specified account.
     */
    public function edit(Account $account)
    {
        if ($account->user_id !== auth()->id()) {
            abort(403);
        }

        $accountTypes = (new Account())->getAccountTypes();
        return view('accounts.edit', compact('account', 'accountTypes'));
    }

    // NOTE: store(), update(), destroy() methods are NOT needed here.
    // All CRUD operations are handled via API (ApiAccountController).
}
