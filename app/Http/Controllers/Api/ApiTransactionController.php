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
        $user = Auth::user();
        $query = Transaction::query();

        // Group Logic: If user belongs to a group, fetch transactions for ALL group members
        if ($user->group_id) {
            // Get all user IDs in the group
            $groupMemberIds = \App\Models\User::where('group_id', $user->group_id)->pluck('id');
            $query->whereIn('user_id', $groupMemberIds);
        } else {
            // Fallback to personal transactions only
            $query->where('user_id', $user->id);
        }

        $query->with('category', 'account', 'relatedAccount', 'user'); // Eager load user relationship

        // Apply Filters
        if ($request->has('category') && $request->category) {
            $query->where('category_id', $request->category);
        }

        if ($request->has('account_id') && $request->account_id) {
            $query->where(function($q) use ($request) {
                $q->where('account_id', $request->account_id)
                  ->orWhere('related_account_id', $request->account_id);
            });
        } elseif ($request->has('account') && $request->account) {
            // Fallback for backward compatibility
            $query->where(function($q) use ($request) {
                $q->where('account_id', $request->account)
                  ->orWhere('related_account_id', $request->account);
            });
        }

        if ($request->has('period') && $request->period) {
            $now = now();
            switch ($request->period) {
                case 'month':
                    $query->whereMonth('date', $now->month)->whereYear('date', $now->year);
                    break;
                case 'year':
                    $query->whereYear('date', $now->year);
                    break;
                case '30':
                default:
                    // Assuming '30' is handled by frontend default or explicit check
                    // If period is numeric '30', use 30 days lookback
                    if (is_numeric($request->period)) {
                        $query->where('date', '>=', $now->subDays($request->period)->format('Y-m-d'));
                    }
                    break;
            }
        }

        // Clone query for summary stats BEFORE pagination
        $summaryQuery = clone $query;

        // Get paginated results
        $transactions = $query->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Calculate Summary Stats based on FILTERED data
        // Note: We apply the same Paylater exclusion logic here

        // 1. Total Income (Filtered) - Exclude Paylater Income
        $totalIncome = (clone $summaryQuery)
            ->where('type', 'income')
            ->whereHas('account', function($q) {
                $q->where('type', '!=', 'paylater');
            })
            ->sum('amount');

        // 2. Total Expense (Filtered) - Include Paylater Expense
        $totalExpense = (clone $summaryQuery)
            ->where('type', 'expense')
            ->sum('amount');

        // 3. Net Balance
        $netBalance = $totalIncome - $totalExpense;

        return response()->json([
            'success' => true,
            'data' => $transactions,
            'summary' => [
                'total_income' => $totalIncome,
                'total_expense' => $totalExpense,
                'net_balance' => $netBalance
            ],
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

            // Check if selected account is paylater and validate paylater fields ONLY for expense transactions
            $account = Account::find($request->account_id);
            if ($account && $account->type === 'paylater' && $request->type === 'expense') {
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

            // Handle paylater transactions (already has its own DB::transaction)
            if ($account && $account->type === 'paylater' && $request->type === 'expense') {
                return $this->createPaylaterTransaction($request, Auth::user());
            }

            // Wrap in DB::transaction for atomic operations
            $transaction = \DB::transaction(function () use ($request, $account) {
                $data = $request->all();
                $data['user_id'] = Auth::id();

                // Handle empty category_id
                if (empty($data['category_id'])) {
                    $data['category_id'] = null;
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

                    return $transaction;
                }

                // Regular income/expense transaction
                $transaction = Transaction::create($data);

                // Update account balance
                $acc = Account::find($request->account_id);
                if ($request->type === 'income') {
                    $acc->updateBalance($request->amount, 'add');
                } elseif ($request->type === 'expense') {
                    $acc->updateBalance($request->amount, 'subtract');
                }

                return $transaction;
            });

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
            \Log::error('API Transaction Store Error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'request_data' => $request->except(['_token']),
                'trace' => $e->getTraceAsString()
            ]);

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
        $user = Auth::user();

        // Determine allowed user IDs (Self + Group Members)
        $allowedUserIds = [$user->id];
        if ($user->group_id) {
            $allowedUserIds = \App\Models\User::where('group_id', $user->group_id)->pluck('id')->toArray();
        }

        $transaction = Transaction::whereIn('user_id', $allowedUserIds)
            ->with('category', 'account', 'relatedAccount', 'user')
            ->find($id);

        if (!$transaction) {
            // Check if transaction exists but is not accessible
            $exists = Transaction::find($id);
            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to view this transaction'
                ], 403);
            }

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
            // Ensure user can only edit THEIR OWN transaction, not group member's
            $transaction = Transaction::where('user_id', Auth::id())->find($id);

            if (!$transaction) {
                // Check if transaction exists but belongs to someone else (for better error message or just 404)
                $exists = Transaction::find($id);
                if ($exists) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You do not have permission to edit this transaction'
                    ], 403);
                }

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

            // Wrap in DB::transaction for atomic operations
            $updatedTransaction = \DB::transaction(function () use ($request, $transaction) {
                // Store old values for balance recalculation
                $oldType = $transaction->type;
                $oldAmount = $transaction->amount;
                $oldAccountId = $transaction->account_id;
                $oldRelatedAccountId = $transaction->related_account_id;

                $data = $request->all();

                // Handle empty category_id
                if (empty($data['category_id'])) {
                    $data['category_id'] = null;
                }

                // Revert old balance first
                $oldAccount = Account::find($oldAccountId);
                if ($oldAccount) {
                    if ($oldType === 'income') {
                        $oldAccount->updateBalance($oldAmount, 'subtract');
                    } elseif ($oldType === 'expense') {
                        $oldAccount->updateBalance($oldAmount, 'add');
                    } elseif ($oldType === 'transfer' && $oldRelatedAccountId) {
                        $oldAccount->updateBalance($oldAmount, 'add'); // Revert subtract from source
                        $oldRelatedAccount = Account::find($oldRelatedAccountId);
                        if ($oldRelatedAccount) {
                            $oldRelatedAccount->updateBalance($oldAmount, 'subtract'); // Revert add to destination
                        }
                    }
                }

                // Update transaction
                $transaction->update($data);

                // Apply new balance
                $newAccount = Account::find($request->account_id);
                if ($newAccount) {
                    if ($request->type === 'income') {
                        $newAccount->updateBalance($request->amount, 'add');
                    } elseif ($request->type === 'expense') {
                        $newAccount->updateBalance($request->amount, 'subtract');
                    } elseif ($request->type === 'transfer' && $request->related_account_id) {
                        $newAccount->updateBalance($request->amount, 'subtract');
                        $newRelatedAccount = Account::find($request->related_account_id);
                        if ($newRelatedAccount) {
                            $newRelatedAccount->updateBalance($request->amount, 'add');
                        }
                    }
                }

                return $transaction;
            });

            // Load relationships for response
            $updatedTransaction->load('category', 'account', 'relatedAccount');

            return response()->json([
                'success' => true,
                'data' => $updatedTransaction,
                'message' => 'Transaction updated successfully'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('API Transaction Update Error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'transaction_id' => $id,
                'request_data' => $request->except(['_token']),
                'trace' => $e->getTraceAsString()
            ]);

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
            // Ensure user can only delete THEIR OWN transaction
            $transaction = Transaction::where('user_id', Auth::id())->find($id);

            if (!$transaction) {
                // Check if transaction exists but belongs to someone else
                $exists = Transaction::find($id);
                if ($exists) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You do not have permission to delete this transaction'
                    ], 403);
                }

                return response()->json([
                    'success' => false,
                    'message' => 'Transaction not found'
                ], 404);
            }

            // Wrap in DB::transaction for atomic operations
            \DB::transaction(function () use ($transaction) {
                // Restore account balance before deletion
                $account = Account::find($transaction->account_id);
                if ($account) {
                    if ($transaction->type === 'income') {
                        $account->updateBalance($transaction->amount, 'subtract');
                    } elseif ($transaction->type === 'expense') {
                        $account->updateBalance($transaction->amount, 'add');
                    } elseif ($transaction->type === 'transfer' && $transaction->related_account_id) {
                        $account->updateBalance($transaction->amount, 'add'); // Revert subtract from source
                        $relatedAccount = Account::find($transaction->related_account_id);
                        if ($relatedAccount) {
                            $relatedAccount->updateBalance($transaction->amount, 'subtract'); // Revert add to destination
                        }
                    }
                }

                $transaction->delete();
            });

            return response()->json([
                'success' => true,
                'message' => 'Transaction deleted successfully'
            ]);

        } catch (\Exception $e) {
            \Log::error('API Transaction Delete Error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'transaction_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);

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
            $now = now(); // Gunakan waktu server/aplikasi yang sudah diset timezone-nya

            // Group Logic: Determine target user IDs (Single or Group)
            $targetUserIds = [$user->id];
            if ($user->group_id) {
                $targetUserIds = \App\Models\User::where('group_id', $user->group_id)->pluck('id')->toArray();
            }

            // Total Balance: Exclude Paylater accounts
            $totalBalance = Account::whereIn('user_id', $targetUserIds)
                ->where('type', '!=', 'paylater')
                ->sum('balance');

            // Monthly Income: Exclude income transactions related to Paylater accounts
            $monthlyIncome = Transaction::whereIn('user_id', $targetUserIds)
                ->where('type', 'income')
                ->whereHas('account', function($q) {
                    $q->where('type', '!=', 'paylater');
                })
                ->whereMonth('date', $now->month)
                ->whereYear('date', $now->year)
                ->sum('amount');

            // Monthly Expense: Include Paylater expenses (because it is consumption)
            $monthlyExpense = Transaction::whereIn('user_id', $targetUserIds)
                ->where('type', 'expense')
                ->whereMonth('date', $now->month)
                ->whereYear('date', $now->year)
                ->sum('amount');

            $recentTransactions = Transaction::whereIn('user_id', $targetUserIds)
                ->with('category', 'account', 'user')
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