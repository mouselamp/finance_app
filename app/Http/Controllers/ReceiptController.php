<?php

namespace App\Http\Controllers;

use App\Helpers\LogHelper;
use App\Models\Account;
use App\Models\Category;
use App\Models\Installment;
use App\Models\PaylaterTransaction;
use App\Models\ReceiptUpload;
use App\Models\Transaction;
use App\Services\ReceiptAnalyzer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ReceiptController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();

        $recentReceipts = ReceiptUpload::where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        $accounts = Account::where('user_id', $user->id)->get();
        $categories = Category::where('user_id', $user->id)->get();

        return view('receipts.index', compact('recentReceipts', 'accounts', 'categories'));
    }

    public function analyze(Request $request, ReceiptAnalyzer $analyzer)
    {
        $request->validate([
            'receipt' => 'required|image|max:5120',
        ]);

        $userId = auth()->id();
        $upload = $analyzer->createFromUpload($request->file('receipt'), $userId);

        try {
            $parsed = $analyzer->analyze($upload);
        } catch (\Throwable $th) {
            $analyzer->markFailed($upload, $th->getMessage());

            throw ValidationException::withMessages([
                'receipt' => 'Gagal menganalisis struk: ' . $th->getMessage(),
            ]);
        }

        return response()->json([
            'receipt_upload' => $upload->fresh(),
            'parsed_payload' => $parsed,
            'image_url' => Storage::url($upload->image_path),
        ]);
    }

    public function storeTransaction(Request $request, ReceiptUpload $receipt)
    {
        $this->guardReceipt($receipt);

        if ($receipt->status !== 'ready') {
            return response()->json([
                'message' => 'Struk belum siap atau sudah digunakan.',
            ], 422);
        }

        $transaction = $this->persistTransaction($request);

        $receipt->forceFill([
            'status' => 'saved',
            'saved_at' => now(),
        ])->save();

        return response()->json([
            'message' => 'Transaksi berhasil disimpan.',
            'transaction_id' => $transaction->id,
        ]);
    }

    private function guardReceipt(ReceiptUpload $receipt): void
    {
        if ($receipt->user_id !== auth()->id()) {
            abort(403);
        }
    }

    private function persistTransaction(Request $request): Transaction
    {
        $request->validate([
            'type' => 'required|in:income,expense,transfer',
            'account_id' => 'required|exists:accounts,id,user_id,' . auth()->id(),
            'category_id' => 'sometimes|nullable|exists:categories,id,user_id,' . auth()->id(),
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'note' => 'required|string|max:255',
            'related_account_id' => 'required_if:type,transfer|nullable|exists:accounts,id,user_id,' . auth()->id() . '|different:account_id',
            'payment_type' => 'sometimes|nullable|in:full,installment',
            'installment_period' => 'sometimes|nullable|integer|min:1|max:12',
        ]);

        $user = auth()->user();
        $data = $request->all();
        $data['user_id'] = $user->id;

        if (empty($data['category_id'])) {
            $data['category_id'] = null;
        }

        if ($request->type === 'transfer') {
            $data['related_account_id'] = $request->related_account_id;

            $transaction = Transaction::create($data);
            LogHelper::transaction('created', $transaction);

            return $transaction;
        }

        $account = Account::find($request->account_id);

        if ($account && $account->type === 'paylater' && $request->type === 'expense') {
            return $this->createPaylaterTransaction($request, $user);
        }

        $transaction = Transaction::create($data);
        LogHelper::transaction('created', $transaction);

        return $transaction;
    }

    private function createPaylaterTransaction(Request $request, $user): Transaction
    {
        $tenor = $request->payment_type === 'installment'
            ? ($request->installment_period ?? $request->tenor)
            : null;

        $request->merge([
            'tenor' => $tenor,
        ]);

        $request->validate([
            'payment_type' => 'required|in:full,installment',
            'tenor' => 'required_if:payment_type,installment|nullable|integer|min:1|max:12',
        ]);

        $transactionRecord = null;

        DB::transaction(function () use ($request, $user, &$transactionRecord) {
            $paylaterTransaction = PaylaterTransaction::create([
                'user_id' => $user->id,
                'account_id' => $request->account_id,
                'date' => $request->date,
                'total_amount' => $request->amount,
                'payment_type' => $request->payment_type,
                'tenor' => $request->payment_type === 'installment' ? $request->tenor : null,
                'monthly_amount' => $request->payment_type === 'installment' ? $request->amount / $request->tenor : null,
                'note' => $request->note,
            ]);

            if ($request->payment_type === 'installment') {
                $monthlyAmount = $request->amount / $request->tenor;
                $dueDate = Carbon::parse($request->date);

                for ($i = 1; $i <= $request->tenor; $i++) {
                    $installmentDueDate = $dueDate->copy()->addMonths($i);

                    Installment::create([
                        'user_id' => $user->id,
                        'paylater_transaction_id' => $paylaterTransaction->id,
                        'due_date' => $installmentDueDate,
                        'amount' => $monthlyAmount,
                        'status' => 'unpaid',
                    ]);
                }
            } else {
                Installment::create([
                    'user_id' => $user->id,
                    'paylater_transaction_id' => $paylaterTransaction->id,
                    'due_date' => Carbon::parse($request->date)->addMonth(),
                    'amount' => $request->amount,
                    'status' => 'unpaid',
                ]);
            }

            $transactionRecord = Transaction::create([
                'user_id' => $user->id,
                'account_id' => $request->account_id,
                'category_id' => $request->category_id,
                'type' => 'expense',
                'date' => $request->date,
                'amount' => $request->amount,
                'note' => '[PAYLATER] ' . $request->note,
            ]);
        });

        LogHelper::transaction('created', $transactionRecord);

        return $transactionRecord;
    }
}
