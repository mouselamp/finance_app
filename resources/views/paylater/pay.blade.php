@extends('layouts.finance')

@section('title', 'Bayar Cicilan Paylater')
@section('page-title', 'Bayar Cicilan Paylater')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center gap-3 mb-4">
            <a href="{{ route('paylater.details', $paylaterTransaction->id) }}"
               class="text-gray-600 hover:text-gray-900 transition-colors">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Bayar Cicilan</h1>
        </div>

        <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium mb-1">{{ $paylaterTransaction->note }}</p>
                    <p class="text-3xl font-bold">{{ $paylaterTransaction->account->name }}</p>
                    <p class="text-purple-200 text-sm mt-2">
                        Total: Rp {{ number_format($paylaterTransaction->total_amount, 0, ',', '.') }} •
                        Tenor: {{ $paylaterTransaction->tenor }}x •
                        Cicilan: Rp {{ number_format($paylaterTransaction->monthly_amount, 0, ',', '.') }}
                    </p>
                </div>
                <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center">
                    <i class="fas fa-credit-card text-white text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Pilih Cicilan -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h2 class="text-lg font-semibold text-gray-800">
                    <i class="fas fa-list-check mr-2 text-purple-600"></i>
                    Pilih Cicilan yang Akan Dibayar
                </h2>
            </div>

            <form action="{{ route('paylater.processPayment', $paylaterTransaction->id) }}" method="POST" class="p-6">
                @csrf
                <div class="space-y-3 mb-6">
                    @foreach($unpaidInstallments as $installment)
                        <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                            <label class="flex items-start cursor-pointer">
                                <input type="checkbox" name="installment_ids[]" value="{{ $installment->id }}"
                                       class="mt-1 rounded border-gray-300 text-purple-600 focus:ring-purple-500"
                                       onchange="updateTotal()">
                                <div class="ml-3 flex-1">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="font-medium text-gray-900">
                                                @if($paylaterTransaction->payment_type === 'full')
                                                    Pembayaran Penuh
                                                @else
                                                    Cicilan ke-{{ $loop->index + 1 }}
                                                @endif
                                            </p>
                                            <p class="text-sm text-gray-500">
                                                Jatuh Tempo: {{ \Carbon\Carbon::parse($installment->due_date)->format('d M Y') }}
                                            </p>
                                        </div>
                                        <p class="font-semibold text-purple-600">
                                            Rp {{ number_format($installment->amount, 0, ',', '.') }}
                                        </p>
                                    </div>
                                    @if ($installment->is_overdue)
                                        <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800 mt-2">
                                            <i class="fas fa-exclamation-triangle mr-1"></i>
                                            Terlambat {{ $installment->days_until_due * -1 }} hari
                                        </span>
                                    @endif
                                </div>
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Metode Pembayaran -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h2 class="text-lg font-semibold text-gray-800">
                    <i class="fas fa-wallet mr-2 text-green-600"></i>
                    Metode Pembayaran
                </h2>
            </div>

            <div class="p-6">
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Pilih Akun Pembayaran</label>
                    <div class="space-y-2">
                        @foreach($accounts as $account)
                            <label class="flex items-center p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                                <input type="radio" name="account_id" value="{{ $account->id }}"
                                       class="text-green-600 focus:ring-green-500" required>
                                <div class="ml-3 flex-1">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $account->name }}</p>
                                            <p class="text-sm text-gray-500">{{ $account->type_label }}</p>
                                        </div>
                                        <p class="font-semibold text-green-600">
                                            Rp {{ number_format($account->balance, 0, ',', '.') }}
                                        </p>
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    @if($accounts->isEmpty())
                        <div class="text-center py-8 bg-gray-50 rounded-lg">
                            <i class="fas fa-wallet text-gray-400 text-3xl mb-3"></i>
                            <p class="text-gray-600">Tidak ada akun pembayaran yang tersedia</p>
                            <p class="text-sm text-gray-500 mt-1">Silakan buat akun baru terlebih dahulu</p>
                            <a href="{{ route('accounts.create') }}"
                               class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-all duration-200 mt-4">
                                <i class="fas fa-plus mr-2"></i>
                                Buat Akun Baru
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Total Pembayaran -->
                <div class="border-t pt-6">
                    <div class="bg-gradient-to-r from-green-50 to-green-100 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm text-gray-600">Jumlah Cicilan:</span>
                            <span id="selectedCount" class="font-semibold text-gray-900">0</span>
                        </div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-sm text-gray-600">Total Pembayaran:</span>
                            <span id="selectedTotal" class="text-xl font-bold text-green-600">Rp 0</span>
                        </div>
                        <button type="submit"
                                id="payButton"
                                class="w-full bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-medium py-3 px-6 rounded-lg transition-all duration-200 shadow hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed"
                                disabled>
                            <i class="fas fa-money-bill-wave mr-2"></i>
                            Bayar Sekarang
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function updateTotal() {
    const checkboxes = document.querySelectorAll('input[name="installment_ids[]"]:checked');
    const installments = @json($unpaidInstallments);

    let count = 0;
    let total = 0;
    console.log('installments', installments);

    checkboxes.forEach(checkbox => {
        const installment = installments.find(i => i.id == checkbox.value);
        if (installment) {
            count++;
            total += parseFloat(installment.amount);
        }
    });
    console.log('total', total);

    document.getElementById('selectedCount').textContent = count;
    // Format Indonesian Rupiah properly
    const formattedTotal = 'Rp ' + total.toLocaleString('id-ID', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    });
    document.getElementById('selectedTotal').textContent = formattedTotal;

    const payButton = document.getElementById('payButton');
    const hasSelectedPayment = document.querySelector('input[name="account_id"]:checked');

    if (count > 0 && hasSelectedPayment) {
        payButton.disabled = false;
    } else {
        payButton.disabled = true;
    }
}

// Update total when payment method is selected
document.querySelectorAll('input[name="account_id"]').forEach(radio => {
    radio.addEventListener('change', updateTotal);
});
</script>
@endsection