<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Installment;
use App\Models\Transaction;
use Carbon\Carbon;

class InstallmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();

        $unpaidInstallments = Installment::where('user_id', $user->id)
            ->where('status', 'unpaid')
            ->with('paylaterTransaction')
            ->orderBy('due_date', 'asc')
            ->get();

        $paidInstallments = Installment::where('user_id', $user->id)
            ->where('status', 'paid')
            ->orderBy('paid_at', 'desc')
            ->limit(10)
            ->get();

        $upcomingPayments = Installment::where('user_id', $user->id)
            ->where('status', 'unpaid')
            ->where('due_date', '>=', Carbon::today())
            ->where('due_date', '<=', Carbon::today()->addDays(30))
            ->orderBy('due_date', 'asc')
            ->get();

        return view('installments.index', compact(
            'unpaidInstallments',
            'paidInstallments',
            'upcomingPayments'
        ));
    }

    public function pay(Installment $installment)
    {
        if ($installment->user_id !== auth()->id()) {
            abort(403);
        }

        if ($installment->status !== 'unpaid') {
            return redirect()->route('installments.index')
                ->with('error', 'Cicilan sudah dibayar.');
        }

        return view('installments.pay', compact('installment'));
    }

    public function processPayment(Request $request, Installment $installment)
    {
        if ($installment->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'payment_date' => 'required|date',
            'notes' => 'nullable|string'
        ]);

        if ($installment->status !== 'unpaid') {
            return redirect()->route('installments.index')
                ->with('error', 'Cicilan sudah dibayar.');
        }

        // Create payment transaction
        Transaction::create([
            'user_id' => auth()->id(),
            'account_id' => $request->account_id,
            'type' => 'paylater_payment',
            'amount' => $installment->amount,
            'date' => $request->payment_date,
            'note' => "Pembayaran cicilan #{$installment->id}"
        ]);

        // Mark installment as paid
        $installment->markAsPaid();

        return redirect()->route('installments.index')
            ->with('success', 'Pembayaran cicilan berhasil dicatat!');
    }
}