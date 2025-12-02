<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ApiReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth.token');
    }

    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            $period = $request->query('period', 'this_month');
            $now = now();

            // Group Logic: Determine target user IDs (Single or Group)
            $targetUserIds = [$user->id];
            if ($user->group_id) {
                $targetUserIds = \App\Models\User::where('group_id', $user->group_id)->pluck('id')->toArray();
            }

            // Determine date range
            switch ($period) {
                case 'last_month':
                    $startDate = $now->copy()->subMonth()->startOfMonth();
                    $endDate = $now->copy()->subMonth()->endOfMonth();
                    break;
                case 'last_30_days':
                    $startDate = $now->copy()->subDays(29)->startOfDay();
                    $endDate = $now->copy()->endOfDay();
                    break;
                case 'this_year':
                    $startDate = $now->copy()->startOfYear();
                    $endDate = $now->copy()->endOfDay();
                    break;
                case 'this_month':
                default:
                    $startDate = $now->copy()->startOfMonth();
                    $endDate = $now->copy()->endOfDay();
                    break;
            }

            // Base query
            $query = Transaction::whereIn('user_id', $targetUserIds)
                ->whereBetween('date', [$startDate, $endDate]);

            // 1. Summary Stats
            $summary = [
                'total_income' => (clone $query)->where('type', 'income')->sum('amount'),
                'total_expense' => (clone $query)->where('type', 'expense')->sum('amount'),
            ];
            $summary['net_balance'] = $summary['total_income'] - $summary['total_expense'];

            // 2. Trend Chart (Income vs Expense per time unit)
            $trend = $this->getTrendData($targetUserIds, $period, $startDate, $endDate);

            // 3. Category Distribution (Expense only)
            $distribution = Transaction::whereIn('user_id', $targetUserIds)
                ->where('type', 'expense')
                ->whereBetween('date', [$startDate, $endDate])
                ->select('category_id', DB::raw('SUM(amount) as total'))
                ->with('category')
                ->groupBy('category_id')
                ->orderByDesc('total')
                ->get()
                ->map(function ($item) {
                    return [
                        'category_name' => $item->category ? $item->category->name : 'Tanpa Kategori',
                        'color' => $item->category ? $item->category->color : null,
                        'total' => $item->total
                    ];
                });

            // 4. Detailed Table Data (Grouped by Category)
            $details = Transaction::whereIn('user_id', $targetUserIds)
                ->whereBetween('date', [$startDate, $endDate])
                ->whereIn('type', ['income', 'expense'])
                ->select(
                    'category_id',
                    DB::raw("SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) as income"),
                    DB::raw("SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) as expense"),
                    DB::raw('COUNT(*) as count')
                )
                ->with('category')
                ->groupBy('category_id')
                ->get()
                ->map(function ($item) {
                    return [
                        'category_id' => $item->category_id,
                        'category_name' => $item->category ? $item->category->name : 'Tanpa Kategori',
                        'income' => $item->income,
                        'expense' => $item->expense,
                        'transaction_count' => $item->count
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => [
                    'period' => [
                        'label' => $this->getPeriodLabel($period),
                        'start' => $startDate->toDateString(),
                        'end' => $endDate->toDateString()
                    ],
                    'summary' => $summary,
                    'trend' => $trend,
                    'distribution' => $distribution,
                    'details' => $details
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load report data: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getTrendData($userIds, $period, $startDate, $endDate)
    {
        // Ensure userIds is array
        if (!is_array($userIds)) {
            $userIds = [$userIds];
        }

        // For year view, group by month. For others, group by day.
        $groupBy = $period === 'this_year' ? 'month' : 'day';
        // PostgreSQL format: YYYY-MM for month, YYYY-MM-DD for day
        $dateFormat = $groupBy === 'month' ? 'YYYY-MM' : 'YYYY-MM-DD';
        $phpDateFormat = $groupBy === 'month' ? 'Y-m' : 'Y-m-d';

        // PostgreSQL specific date formatting
        $rawDate = "TO_CHAR(date, '$dateFormat')";

        $incomes = Transaction::whereIn('user_id', $userIds)
            ->where('type', 'income')
            ->whereBetween('date', [$startDate, $endDate])
            ->select(DB::raw("$rawDate as date_group"), DB::raw('SUM(amount) as total'))
            ->groupBy('date_group')
            ->pluck('total', 'date_group');

        $expenses = Transaction::whereIn('user_id', $userIds)
            ->where('type', 'expense')
            ->whereBetween('date', [$startDate, $endDate])
            ->select(DB::raw("$rawDate as date_group"), DB::raw('SUM(amount) as total'))
            ->groupBy('date_group')
            ->pluck('total', 'date_group');

        // Generate labels and fill zeroes
        $labels = [];
        $incomeData = [];
        $expenseData = [];
        
        $current = $startDate->copy();
        while ($current <= $endDate) {
            $key = $current->format($phpDateFormat);
            
            // Format label based on period
            if ($groupBy === 'month') {
                $labels[] = $current->translatedFormat('F'); // Nama bulan
                $current->addMonth();
            } else {
                $labels[] = $current->format('d/m');
                $current->addDay();
            }

            $incomeData[] = $incomes[$key] ?? 0;
            $expenseData[] = $expenses[$key] ?? 0;
        }

        return [
            'labels' => $labels,
            'datasets' => [
                'income' => $incomeData,
                'expense' => $expenseData
            ]
        ];
    }

    private function getPeriodLabel($period)
    {
        return match($period) {
            'this_month' => 'Bulan Ini',
            'last_month' => 'Bulan Lalu',
            'last_30_days' => '30 Hari Terakhir',
            'this_year' => 'Tahun Ini',
            default => 'Bulan Ini'
        };
    }

    public function details(Request $request)
    {
        try {
            $user = Auth::user();
            $categoryId = $request->query('category_id');
            $period = $request->query('period', 'this_month');
            $now = now();

            // Handle special "Tanpa Kategori" case (null category_id)
            // In frontend we might pass "null" string or actual null
            if ($categoryId === 'null') {
                $categoryId = null;
            }

            // Determine date range (Reuse logic or refactor into private method)
            switch ($period) {
                case 'last_month':
                    $startDate = $now->copy()->subMonth()->startOfMonth();
                    $endDate = $now->copy()->subMonth()->endOfMonth();
                    break;
                case 'last_30_days':
                    $startDate = $now->copy()->subDays(29)->startOfDay();
                    $endDate = $now->copy()->endOfDay();
                    break;
                case 'this_year':
                    $startDate = $now->copy()->startOfYear();
                    $endDate = $now->copy()->endOfDay();
                    break;
                case 'this_month':
                default:
                    $startDate = $now->copy()->startOfMonth();
                    $endDate = $now->copy()->endOfDay();
                    break;
            }

            $query = Transaction::where('user_id', $user->id)
                ->whereBetween('date', [$startDate, $endDate])
                ->with('account') // Eager load account
                ->orderBy('date', 'desc');

            if ($categoryId) {
                $query->where('category_id', $categoryId);
            } else {
                $query->whereNull('category_id');
            }

            $transactions = $query->get();

            return response()->json([
                'success' => true,
                'data' => $transactions
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load transaction details: ' . $e->getMessage()
            ], 500);
        }
    }
}
