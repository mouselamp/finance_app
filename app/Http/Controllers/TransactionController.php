<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Account;
use App\Models\Category;
use App\Models\PaylaterTransaction;
use App\Models\Installment;
use App\Helpers\LogHelper;
use Carbon\Carbon;

class TransactionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

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

    public function createWithType($type)
    {
        return $this->create($type);
    }

    public function store(Request $request)
    {
        // Basic validation
        $request->validate([
            'type' => 'required|in:income,expense,transfer',
            'account_id' => 'required|exists:accounts,id,user_id,' . auth()->id(),
            'category_id' => 'sometimes|nullable|exists:categories,id,user_id,' . auth()->id(),
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'note' => 'required|string|max:255',
            'related_account_id' => 'required_if:type,transfer|exists:accounts,id,user_id,' . auth()->id() . '|different:account_id'
        ]);

        // Check if selected account is paylater and validate paylater fields
        $account = Account::find($request->account_id);
        if ($account && $account->type === 'paylater') {
            // Handle field mapping from frontend (installment_period) to backend (tenor)
            $tenor = $request->payment_type === 'installment' ? ($request->installment_period ?? $request->tenor) : null;

            $request->merge([
                'tenor' => $tenor
            ]);

            // Validate payment type first
            $request->validate([
                'payment_type' => 'required|in:full,installment'
            ]);

            // Only validate tenor if payment type is installment
            if ($request->payment_type === 'installment') {
                $request->validate([
                    'tenor' => 'required|integer|min:1|max:12'
                ]);
            }
        }

        $user = auth()->user();
        $data = $request->all();
        $data['user_id'] = $user->id;

        // Handle empty category_id
        if (empty($data['category_id'])) {
            $data['category_id'] = null;
        }

        // Handle transfer transactions
        if ($request->type === 'transfer') {
            $data['related_account_id'] = $request->related_account_id;

            // Create transfer record
            Transaction::create($data);

            return redirect()->route('transactions.index')
                ->with('success', 'Transfer berhasil dicatat!');
        }

        // Handle paylater transactions
        $account = Account::find($request->account_id);

        // Debug: Log the account info and request data
        \Log::info('Transaction Debug:', [
            'account_id' => $request->account_id,
            'account_type' => $account ? $account->type : 'not found',
            'transaction_type' => $request->type,
            'payment_type' => $request->payment_type,
            'tenor' => $request->tenor,
            'is_paylater' => $account && $account->type === 'paylater' && $request->type === 'expense'
        ]);

        if ($account && $account->type === 'paylater' && $request->type === 'expense') {
            return $this->createPaylaterTransaction($request, $user);
        }

        // Regular income/expense transaction
        $transaction = Transaction::create($data);

        // Log transaction
        LogHelper::transaction('created', $transaction);

        $typeName = $request->type === 'income' ? 'Pemasukan' : 'Pengeluaran';
        return redirect()->route('transactions.index')
            ->with('success', "{$typeName} berhasil dicatat!");
    }

    /**
     * Create paylater transaction and installments
     */
    private function createPaylaterTransaction(Request $request, $user)
    {
        try {
            \DB::transaction(function() use ($request, $user) {
                $account = Account::find($request->account_id);

                // Create paylater transaction
                $paylaterTransaction = PaylaterTransaction::create([
                    'user_id' => $user->id,
                    'account_id' => $request->account_id,
                    'date' => $request->date,
                    'total_amount' => $request->amount,
                    'payment_type' => $request->payment_type,
                    'tenor' => $request->payment_type === 'installment' ? $request->tenor : null,
                    'monthly_amount' => $request->payment_type === 'installment' ? $request->amount / $request->tenor : null,
                    'note' => $request->note
                ]);

                // Create installments if payment type is installment
                if ($request->payment_type === 'installment') {
                    $monthlyAmount = $request->amount / $request->tenor;
                    $dueDate = Carbon::parse($request->date);

                    for ($i = 1; $i <= $request->tenor; $i++) {
                        // Add 1 month for each installment
                        $installmentDueDate = $dueDate->copy()->addMonths($i);

                        Installment::create([
                            'user_id' => $user->id,
                            'paylater_transaction_id' => $paylaterTransaction->id,
                            'due_date' => $installmentDueDate,
                            'amount' => $monthlyAmount,
                            'status' => 'unpaid'
                        ]);
                    }
                } else {
                    // Create single installment for full payment
                    Installment::create([
                        'user_id' => $user->id,
                        'paylater_transaction_id' => $paylaterTransaction->id,
                        'due_date' => Carbon::parse($request->date)->addMonth(),
                        'amount' => $request->amount,
                        'status' => 'unpaid'
                    ]);
                }

                // Also create a regular transaction record for reference
                Transaction::create([
                    'user_id' => $user->id,
                    'account_id' => $request->account_id,
                    'category_id' => $request->category_id,
                    'type' => 'expense',
                    'date' => $request->date,
                    'amount' => $request->amount,
                    'note' => '[PAYLATER] ' . $request->note
                ]);
            });

            $paymentTypeText = $request->payment_type === 'full' ? 'Bayar Penuh' : 'Cicilan ' . $request->tenor . 'x';
            return redirect()->route('paylater.index')
                ->with('success', "Transaksi paylater ({$paymentTypeText}) sebesar Rp " . number_format($request->amount, 0, ',', '.') . " berhasil dibuat!");

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal membuat transaksi paylater: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Transaction $transaction)
    {
        $user = auth()->user();
        
        // Check permission: Own transaction OR Group member's transaction
        $hasAccess = $transaction->user_id === $user->id;
        
        if (!$hasAccess && $user->group_id) {
            // Check if transaction owner is in the same group
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

    public function update(Request $request, Transaction $transaction)
    {
        if ($transaction->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'type' => 'required|in:income,expense,transfer',
            'account_id' => 'required|exists:accounts,id',
            'category_id' => 'nullable|exists:categories,id',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'note' => 'required|string|max:255',
            'related_account_id' => 'required_if:type,transfer|exists:accounts,id|different:account_id'
        ]);

        $data = $request->all();

        // Handle transfer updates
        if ($request->type === 'transfer') {
            $data['related_account_id'] = $request->related_account_id;
        }

        $transaction->update($data);

        // Log transaction update
        LogHelper::transaction('updated', $transaction);

        return redirect()->route('transactions.index')
            ->with('success', 'Transaksi berhasil diperbarui!');
    }

    public function destroy(Transaction $transaction)
    {
        if ($transaction->user_id !== auth()->id()) {
            abort(403);
        }

        $transaction->delete();

        // Log transaction deletion
        LogHelper::transaction('deleted', $transaction);

        return redirect()->route('transactions.index')
            ->with('success', 'Transaksi berhasil dihapus!');
    }
}
