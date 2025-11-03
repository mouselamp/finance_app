<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PaylaterTransaction;
use App\Models\Installment;
use Carbon\Carbon;

class PaylaterController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of paylater transactions.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $paylaterTransactions = PaylaterTransaction::where('user_id', auth()->id())
            ->with('account', 'installments')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $totalPaylater = PaylaterTransaction::where('user_id', auth()->id())->sum('total_amount');
        // Get full payments total
        $fullPaymentTotal = PaylaterTransaction::where('user_id', auth()->id())
            ->where('payment_type', 'full')
            ->sum('total_amount');

        // Get installment payments total
        $installmentPaymentTotal = PaylaterTransaction::where('user_id', auth()->id())
            ->where('payment_type', 'installment')
            ->with(['installments' => function($query) {
                $query->where('status', 'paid');
            }])
            ->get()
            ->sum(function($transaction) {
                return $transaction->installments->sum('amount');
            });

        $totalPaid = $fullPaymentTotal + $installmentPaymentTotal;

        return view('paylater.index', compact('paylaterTransactions', 'totalPaylater', 'totalPaid'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('paylater.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // This is handled by the transaction creation process
        return redirect()->route('transactions.index')
            ->with('info', 'Paylater transactions are created through the expense form.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $paylaterTransaction = PaylaterTransaction::where('user_id', auth()->id())
            ->with(['account', 'installments'])
            ->findOrFail($id);

        return view('paylater.show', compact('paylaterTransaction'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $paylaterTransaction = PaylaterTransaction::where('user_id', auth()->id())
            ->findOrFail($id);

        return view('paylater.edit', compact('paylaterTransaction'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $paylaterTransaction = PaylaterTransaction::where('user_id', auth()->id())
            ->findOrFail($id);

        $request->validate([
            'note' => 'required|string|max:255',
        ]);

        $paylaterTransaction->update([
            'note' => $request->note,
        ]);

        return redirect()->route('paylater.index')
            ->with('success', 'Paylater transaction updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $paylaterTransaction = PaylaterTransaction::where('user_id', auth()->id())
            ->findOrFail($id);

        // Check if there are paid installments
        if ($paylaterTransaction->installments()->where('status', 'paid')->count() > 0) {
            return redirect()->route('paylater.index')
                ->with('error', 'Cannot delete paylater transaction with paid installments.');
        }

        $paylaterTransaction->installments()->delete();
        $paylaterTransaction->delete();

        return redirect()->route('paylater.index')
            ->with('success', 'Paylater transaction deleted successfully.');
    }

    /**
     * Display details of a specific paylater transaction.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function details($id)
    {
        $paylaterTransaction = PaylaterTransaction::where('user_id', auth()->id())
            ->with(['account', 'installments'])
            ->findOrFail($id);

        // Calculate remaining balance
        $totalPaid = $paylaterTransaction->installments()
            ->where('status', 'paid')
            ->sum('amount');

        $remainingBalance = $paylaterTransaction->total_amount - $totalPaid;

        return view('paylater.details', compact('paylaterTransaction', 'totalPaid', 'remainingBalance'));
    }

    /**
     * Show payment form for paylater installment.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function pay($id)
    {
        $paylaterTransaction = PaylaterTransaction::where('user_id', auth()->id())
            ->with(['account', 'installments'])
            ->findOrFail($id);

        // Get unpaid installments
        $unpaidInstallments = $paylaterTransaction->installments()
            ->where('status', 'unpaid')
            ->orderBy('due_date')
            ->get();

        if ($unpaidInstallments->isEmpty()) {
            return redirect()->route('paylater.details', $id)
                ->with('info', 'Semua cicilan sudah lunas.');
        }

        // Get user's all accounts for payment (including the paylater account itself)
        $accounts = \App\Models\Account::where('user_id', auth()->id())
            ->whereNull('deleted_at')
            ->get();

        return view('paylater.pay', compact('paylaterTransaction', 'unpaidInstallments', 'accounts'));
    }

    /**
     * Process payment for paylater installment or full payment.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function processPayment(Request $request, $id)
    {
        $paylaterTransaction = PaylaterTransaction::where('user_id', auth()->id())
            ->findOrFail($id);

        $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'installment_ids' => 'required|array',
            'installment_ids.*' => 'exists:installments,id'
        ]);

        // Verify all installments belong to this transaction and are unpaid
        $installments = $paylaterTransaction->installments()
            ->whereIn('id', $request->installment_ids)
            ->where('status', 'unpaid')
            ->get();

        if ($installments->count() !== count($request->installment_ids)) {
            return redirect()->back()
                ->with('error', 'Beberapa cicilan tidak valid atau sudah dibayar.');
        }

        // Get payment account
        $paymentAccount = \App\Models\Account::where('user_id', auth()->id())
            ->findOrFail($request->account_id);

        $totalAmount = $installments->sum('amount');

        // Check if payment account has sufficient balance
        if ($paymentAccount->balance < $totalAmount) {
            return redirect()->back()
                ->with('error', 'Saldo akun pembayaran tidak mencukupi.');
        }

        // Process payment
        \DB::transaction(function() use ($installments, $paymentAccount, $paylaterTransaction) {
            // Deduct from payment account
            $paymentAccount->updateBalance($installments->sum('amount'), 'subtract');

            // Add balance back to paylater account
            $paylaterTransaction->account->updateBalance($installments->sum('amount'), 'add');

            // Mark installments as paid
            foreach ($installments as $installment) {
                $installment->markAsPaid();
            }

            // Create transaction record for payment account deduction
            \App\Models\Transaction::create([
                'user_id' => auth()->id(),
                'account_id' => $paymentAccount->id,
                'category_id' => null, // You might want to create a special category for paylater payments
                'type' => 'paylater_payment',
                'date' => \Carbon\Carbon::now()->format('Y-m-d'),
                'amount' => $installments->sum('amount'),
                'note' => 'Pembayaran ' . ($paylaterTransaction->payment_type === 'full' ? 'lunas' : 'cicilan') . ' ' . $paylaterTransaction->note . ' (' . $installments->count() . 'x)',
            ]);

            // Create transaction record for paylater account addition
            \App\Models\Transaction::create([
                'user_id' => auth()->id(),
                'account_id' => $paylaterTransaction->account->id,
                'category_id' => null,
                'type' => 'income',
                'date' => \Carbon\Carbon::now()->format('Y-m-d'),
                'amount' => $installments->sum('amount'),
                'note' => 'Pembayaran masuk ' . ($paylaterTransaction->payment_type === 'full' ? 'lunas' : 'cicilan') . ' ' . $paylaterTransaction->note . ' (' . $installments->count() . 'x)',
            ]);
        });

        $message = 'Berhasil membayar ' . ($paylaterTransaction->payment_type === 'full' ? 'tagihan' : $installments->count() . ' cicilan') . ' sebesar Rp ' . number_format($totalAmount, 0, ',', '.');

        return redirect()->route('paylater.details', $id)
            ->with('success', $message);
    }
}