<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class ApiAccountController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $accounts = Account::where('user_id', Auth::id())->get();

            // Add type_label to each account
            $accountsData = $accounts->map(function ($account) {
                return [
                    'id' => $account->id,
                    'user_id' => $account->user_id,
                    'name' => $account->name,
                    'type' => $account->type,
                    'type_label' => $account->type_label,
                    'balance' => $account->balance,
                    'note' => $account->note,
                    'created_at' => $account->created_at,
                    'updated_at' => $account->updated_at,
                ];
            });

            $totalBalance = $accounts->sum('balance');

            return response()->json([
                'success' => true,
                'data' => [
                    'accounts' => $accountsData,
                    'total_balance' => $totalBalance
                ],
                'message' => 'Accounts retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve accounts: ' . $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTrace() : null
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'type' => 'required|in:cash,bank,paylater',
                'balance' => 'required|numeric|min:0',
                'note' => 'nullable|string'
            ]);

            $account = Account::create([
                'user_id' => Auth::id(),
                'name' => $request->name,
                'type' => $request->type,
                'balance' => $request->balance,
                'note' => $request->note
            ]);

            return response()->json([
                'success' => true,
                'data' => $account,
                'message' => 'Account created successfully'
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
                'message' => 'Failed to create account: ' . $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTrace() : null
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $account = Account::where('user_id', Auth::id())->find($id);

            if (!$account) {
                return response()->json([
                    'success' => false,
                    'message' => 'Account not found'
                ], 404);
            }

            $transactions = Transaction::where('user_id', Auth::id())
                ->where(function($query) use ($account) {
                    $query->where('account_id', $account->id)
                          ->orWhere('related_account_id', $account->id);
                })
                ->with('category', 'relatedAccount')
                ->orderBy('date', 'desc')
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            return response()->json([
                'success' => true,
                'data' => [
                    'account' => $account,
                    'transactions' => $transactions
                ],
                'message' => 'Account details retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve account: ' . $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTrace() : null
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $account = Account::where('user_id', Auth::id())->find($id);

            if (!$account) {
                return response()->json([
                    'success' => false,
                    'message' => 'Account not found'
                ], 404);
            }

            $request->validate([
                'name' => 'required|string|max:255',
                'note' => 'nullable|string'
            ]);

            $account->update([
                'name' => $request->name,
                'note' => $request->note
            ]);

            return response()->json([
                'success' => true,
                'data' => $account,
                'message' => 'Account updated successfully'
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
                'message' => 'Failed to update account: ' . $e->getMessage(),
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
            $account = Account::where('user_id', Auth::id())->find($id);

            if (!$account) {
                return response()->json([
                    'success' => false,
                    'message' => 'Account not found'
                ], 404);
            }

            // Check if account has transactions
            $transactionCount = Transaction::where('user_id', Auth::id())
                ->where(function($query) use ($account) {
                    $query->where('account_id', $account->id)
                          ->orWhere('related_account_id', $account->id);
                })
                ->count();

            if ($transactionCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete account with existing transactions'
                ], 400);
            }

            $account->delete();

            return response()->json([
                'success' => true,
                'message' => 'Account deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete account: ' . $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTrace() : null
            ], 500);
        }
    }
}