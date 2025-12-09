<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Account;
use App\Models\Category;

/**
 * TransactionController - Web Controller
 *
 * This controller only handles VIEW RENDERING.
 * All CRUD operations (store, update, destroy) are handled by ApiTransactionController.
 */
class TransactionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of transactions.
     */
    public function index()
    {
        $user = auth()->user();

        $transactions = Transaction::where('user_id', $user->id)
            ->with('category', 'account', 'relatedAccount')
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $accounts = Account::where('user_id', $user->id)->get();
        $categories = Category::where('user_id', $user->id)->get();

        return view('transactions.index', compact('transactions', 'accounts', 'categories'));
    }

    /**
     * Show the form for creating a new transaction.
     */
    public function create($type = null)
    {
        $user = auth()->user();

        $accounts = Account::where('user_id', $user->id)->get();
        $categories = Category::where('user_id', $user->id)
            ->when($type, function($query) use ($type) {
                if ($type === 'income') {
                    $query->where('type', 'income');
                } elseif ($type === 'expense') {
                    $query->where('type', 'expense');
                }
            })
            ->get();

        return view('transactions.create', compact('accounts', 'categories', 'type'));
    }

    /**
     * Alias for create with type parameter.
     */
    public function createWithType($type)
    {
        return $this->create($type);
    }

    /**
     * Display the specified transaction.
     * Allows group members to view transactions in read-only mode.
     */
    public function show(Transaction $transaction)
    {
        $user = auth()->user();

        // Check permission: Own transaction OR Group member's transaction
        $hasAccess = $transaction->user_id === $user->id;

        if (!$hasAccess && $user->group_id) {
            $owner = \App\Models\User::find($transaction->user_id);
            if ($owner && $owner->group_id === $user->group_id) {
                $hasAccess = true;
            }
        }

        if (!$hasAccess) {
            abort(403);
        }

        $transaction->load('category', 'account', 'relatedAccount', 'user');
        return view('transactions.show', compact('transaction'));
    }

    /**
     * Show the form for editing the specified transaction.
     */
    public function edit(Transaction $transaction)
    {
        if ($transaction->user_id !== auth()->id()) {
            abort(403);
        }

        $user = auth()->user();
        $accounts = Account::where('user_id', $user->id)->get();
        $categories = Category::where('user_id', $user->id)->get();

        return view('transactions.edit', compact('transaction', 'accounts', 'categories'));
    }

    // NOTE: store(), update(), destroy() methods are NOT needed here.
    // All CRUD operations are handled via API (ApiTransactionController).
}
