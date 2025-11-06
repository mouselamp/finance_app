<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Account;
use App\Models\Category;
use App\Models\PaylaterTransaction;
use App\Models\Installment;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ApiTransactionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth.token');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $transactions = Transaction::where('user_id', Auth::id())
            ->with('category', 'account', 'relatedAccount')
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $transactions,
            'message' => 'Transactions retrieved successfully'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Basic validation
            $request->validate([
                'type' => 'required|in:income,expense,transfer',
                'account_id' => 'required|exists:accounts,id,user_id,' . Auth::id(),
                'category_id' => 'sometimes|nullable|exists:categories,id,user_id,' . Auth::id(),
                'amount' => 'required|numeric|min:0',
                'date' => 'required|date',
                'note' => 'required|string|max:255',
                'related_account_id' => 'required_if:type,transfer|exists:accounts,id,user_id,' . Auth::id() . '|different:account_id'
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

            $data = $request->all();
            $data['user_id'] = Auth::id();

            // Handle empty category_id
            if (empty($data['category_id'])) {
                $data['category_id'] = null;
            }

            // Handle paylater transactions
            if ($account && $account->type === 'paylater' && $request->type === 'expense') {
                return $this->createPaylaterTransaction($request, Auth::user());
            }

            // Handle transfer transactions
            if ($request->type === 'transfer') {
                $data['related_account_id'] = $request->related_account_id;
                $transaction = Transaction::create($data);

                // Update balances for transfer
                $fromAccount = Account::find($request->account_id);
                $toAccount = Account::find($request->related_account_id);

                $fromAccount->updateBalance($request->amount, 'subtract');
                $toAccount->updateBalance($request->amount, 'add');
            } else {
                // Regular income/expense transaction
                $transaction = Transaction::create($data);

                // Update account balance
                $account = Account::find($request->account_id);
                if ($request->type === 'income') {
                    $account->updateBalance($request->amount, 'add');
                } elseif ($request->type === 'expense') {
                    $account->updateBalance($request->amount, 'subtract');
                }
            }

            // Load relationships for response
            $transaction->load('category', 'account', 'relatedAccount');

            return response()->json([
                'success' => true,
                'data' => $transaction,
                'message' => 'Transaction created successfully'
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create transaction: ' . $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTrace() : null
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $transaction = Transaction::where('user_id', Auth::id())
            ->with('category', 'account', 'relatedAccount')
            ->find($id);

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $transaction,
            'message' => 'Transaction retrieved successfully'
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $transaction = Transaction::where('user_id', Auth::id())->find($id);

            if (!$transaction) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaction not found'
                ], 404);
            }

            $request->validate([
                'type' => 'required|in:income,expense,transfer',
                'account_id' => 'required|exists:accounts,id,user_id,' . Auth::id(),
                'category_id' => 'sometimes|nullable|exists:categories,id,user_id,' . Auth::id(),
                'amount' => 'required|numeric|min:0',
                'date' => 'required|date',
                'note' => 'required|string|max:255',
                'related_account_id' => 'required_if:type,transfer|exists:accounts,id,user_id,' . Auth::id() . '|different:account_id'
            ]);

            $data = $request->all();

            // Handle empty category_id
            if (empty($data['category_id'])) {
                $data['category_id'] = null;
            }

            $transaction->update($data);

            // Load relationships for response
            $transaction->load('category', 'account', 'relatedAccount');

            return response()->json([
                'success' => true,
                'data' => $transaction,
                'message' => 'Transaction updated successfully'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update transaction: ' . $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTrace() : null
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $transaction = Transaction::where('user_id', Auth::id())->find($id);

            if (!$transaction) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaction not found'
                ], 404);
            }

            // Restore account balance before deletion
            $account = Account::find($transaction->account_id);
            if ($account) {
                if ($transaction->type === 'income') {
                    $account->updateBalance($transaction->amount, 'subtract');
                } elseif ($transaction->type === 'expense') {
                    $account->updateBalance($transaction->amount, 'add');
                }
            }

            $transaction->delete();

            return response()->json([
                'success' => true,
                'message' => 'Transaction deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete transaction: ' . $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTrace() : null
            ], 500);
        }
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
                $transaction = Transaction::create([
                    'user_id' => $user->id,
                    'account_id' => $request->account_id,
                    'category_id' => $request->category_id,
                    'type' => 'expense',
                    'date' => $request->date,
                    'amount' => $request->amount,
                    'note' => '[PAYLATER] ' . $request->note
                ]);

                // Update account balance (reduce paylater limit)
                $account->updateBalance($request->amount, 'subtract');

                return $transaction;
            });

            $paymentTypeText = $request->payment_type === 'full' ? 'Bayar Penuh' : 'Cicilan ' . $request->tenor . 'x';

            return response()->json([
                'success' => true,
                'message' => "Transaksi paylater ({$paymentTypeText}) sebesar Rp " . number_format($request->amount, 0, ',', '.') . " berhasil dibuat!"
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat transaksi paylater: ' . $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTrace() : null
            ], 500);
        }
    }

    /**
     * Get transaction statistics
     */
    public function statistics()
    {
        try {
            $user = Auth::user();

            $totalBalance = Account::where('user_id', $user->id)->sum('balance');

            $monthlyIncome = Transaction::where('user_id', $user->id)
                ->where('type', 'income')
                ->whereMonth('date', now()->month)
                ->whereYear('date', now()->year)
                ->sum('amount');

            $monthlyExpense = Transaction::where('user_id', $user->id)
                ->where('type', 'expense')
                ->whereMonth('date', now()->month)
                ->whereYear('date', now()->year)
                ->sum('amount');

            $recentTransactions = Transaction::where('user_id', $user->id)
                ->with('category', 'account')
                ->orderBy('date', 'desc')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'total_balance' => $totalBalance,
                    'monthly_income' => $monthlyIncome,
                    'monthly_expense' => $monthlyExpense,
                    'monthly savings' => $monthlyIncome - $monthlyExpense,
                    'recent_transactions' => $recentTransactions
                ],
                'message' => 'Statistics retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get statistics: ' . $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTrace() : null
            ], 500);
        }
    }
}