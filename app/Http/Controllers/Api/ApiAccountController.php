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
        $this->middleware('auth.token');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $user = Auth::user();
            
            // Get accounts from current user and group members
            if ($user->group) {
                // Get all user IDs in the group
                $groupUserIds = $user->group->users()->pluck('users.id');
                
                // Get accounts from all group members
                $accounts = Account::whereIn('user_id', $groupUserIds)
                    ->with('user') // Load user relationship
                    ->get();
            } else {
                // Only current user's accounts
                $accounts = Account::where('user_id', Auth::id())
                    ->with('user')
                    ->get();
            }

            // Add type_label and owner info to each account
            $accountsData = $accounts->map(function ($account) {
                return [
                    'id' => $account->id,
                    'user_id' => $account->user_id,
                    'user_name' => $account->user ? $account->user->name : 'Unknown',
                    'name' => $account->name,
                    'type' => $account->type,
                    'type_label' => $account->type_label,
                    'balance' => $account->balance,
                    'note' => $account->note,
                    'is_owner' => $account->user_id === Auth::id(),
                    'created_at' => $account->created_at,
                    'updated_at' => $account->updated_at,
                ];
            });

            // Only calculate total balance for current user's accounts
            $totalBalance = $accounts->where('user_id', Auth::id())->sum('balance');

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
            $user = Auth::user();
            $account = Account::with('user')->find($id);

            if (!$account) {
                return response()->json([
                    'success' => false,
                    'message' => 'Account not found'
                ], 404);
            }

            // Check if user can access this account (own account or group member)
            $isOwner = $account->user_id === Auth::id();
            $canAccess = $isOwner;
            
            if (!$isOwner && $user->group) {
                // Check if account owner is in the same group
                $canAccess = $user->group->users()->where('users.id', $account->user_id)->exists();
            }

            if (!$canAccess) {
                return response()->json([
                    'success' => false,
                    'message' => 'Account not found'
                ], 404);
            }

            // Get transactions for this account (only from account owner's transactions)
            $transactions = Transaction::where('user_id', $account->user_id)
                ->where(function($query) use ($account) {
                    $query->where('account_id', $account->id)
                          ->orWhere('related_account_id', $account->id);
                })
                ->with('category', 'relatedAccount', 'user')
                ->orderBy('date', 'desc')
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            return response()->json([
                'success' => true,
                'data' => [
                    'account' => $account,
                    'transactions' => $transactions,
                    'is_owner' => $isOwner
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