@extends('layouts.finance')

@section('title', 'Bayar Cicilan')
@section('page-title', 'Bayar Cicilan')

@section('content')
<div class="mb-6">
    <div class="flex items-center">
        <a href="{{ route('installments.index') }}" class="mr-4 text-gray-600 hover:text-gray-900">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h2 class="text-2xl font-bold text-gray-900">
                <i class="fas fa-credit-card text-green-600 mr-2"></i>Bayar Cicilan
            </h2>
            <p class="mt-1 text-sm text-gray-600">Catat pembayaran cicilan Anda</p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Payment Form -->
    <div class="lg:col-span-2">
        <div class="bg-white shadow rounded-lg">
            <form action="{{ route('installments.processPayment', $installment->id) }}" method="POST">
                @csrf
                <div class="px-4 py-5 sm:p-6">
                    <div class="grid grid-cols-1 gap-6">
                        <!-- Installment Info -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <h3 class="text-sm font-medium text-blue-900 mb-2">Detail Cicilan</h3>
                            <div class="space-y-1">
                                <div class="flex justify-between">
                                    <span class="text-sm text-blue-700">ID Cicilan:</span>
                                    <span class="text-sm font-medium text-blue-900">#{{ $installment->id }}</span>
                                </div>
                                @if($installment->paylaterTransaction)
                                    <div class="flex justify-between">
                                        <span class="text-sm text-blue-700">Transaksi:</span>
                                        <span class="text-sm font-medium text-blue-900">{{ $installment->paylaterTransaction->note ?? 'Paylater Transaction' }}</span>
                                    </div>
                                @endif
                                <div class="flex justify-between">
                                    <span class="text-sm text-blue-700">Jatuh Tempo:</span>
                                    <span class="text-sm font-medium text-blue-900">{{ $installment->due_date->format('d M Y') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-blue-700">Jumlah:</span>
                                    <span class="text-sm font-semibold text-blue-900">Rp {{ number_format($installment->amount, 0, ',', '.') }}</span>
                                </div>
                                @if($installment->is_overdue)
                                    <div class="mt-2 p-2 bg-red-100 rounded">
                                        <p class="text-xs text-red-700 font-medium">
                                            <i class="fas fa-exclamation-triangle mr-1"></i>
                                            TERLAMBAT {{ abs($installment->days_until_due) }} hari
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Account Selection -->
                        <div>
                            <label for="account_id" class="block text-sm font-medium text-gray-700">
                                Akun Pembayaran <span class="text-red-500">*</span>
                            </label>
                            <select name="account_id" id="account_id" required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <option value="">Pilih akun untuk pembayaran...</option>
                                @foreach(auth()->user()->accounts as $account)
                                    <option value="{{ $account->id }}"
                                            @if(old('account_id') == $account->id) selected @endif>
                                        {{ $account->name }} ({{ $account->type_label }})
                                        Saldo: Rp {{ number_format($account->balance, 0, ',', '.') }}
                                    </option>
                                @endforeach
                            </select>
                            @error('account_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Payment Date -->
                        <div>
                            <label for="payment_date" class="block text-sm font-medium text-gray-700">
                                Tanggal Pembayaran <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="payment_date" id="payment_date" required
                                   value="{{ old('payment_date', now()->format('Y-m-d')) }}"
                                   max="{{ now()->format('Y-m-d') }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            @error('payment_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">Tanggal saat pembayaran dilakukan</p>
                        </div>

                        <!-- Notes -->
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700">
                                Catatan Pembayaran (Opsional)
                            </label>
                            <textarea name="notes" id="notes" rows="3"
                                      placeholder="Tambahkan catatan tentang pembayaran ini..."
                                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">{{ old('notes') }}</textarea>
                            @error('notes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Confirmation Checkbox -->
                        <div>
                            <label class="flex items-start">
                                <input type="checkbox" name="confirm" value="1" required
                                       class="mt-1 h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">
                                    Saya konfirmasi bahwa pembayaran cicilan sebesar
                                    <strong>Rp {{ number_format($installment->amount, 0, ',', '.') }}</strong>
                                    telah dilakukan.
                                </span>
                            </label>
                            @error('confirm')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="px-4 py-3 bg-gray-50 text-right sm:px-6 sm:flex sm:justify-between">
                    <a href="{{ route('installments.index') }}"
                       class="inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Batal
                    </a>
                    <button type="submit"
                            class="mt-3 sm:mt-0 inline-flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        <i class="fas fa-check-circle mr-2"></i>Bayar Cicilan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Payment Summary -->
    <div>
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Ringkasan Pembayaran</h3>
            </div>
            <div class="p-4">
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Jumlah Cicilan:</span>
                        <span class="text-sm font-medium text-gray-900">Rp {{ number_format($installment->amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Tanggal Jatuh Tempo:</span>
                        <span class="text-sm font-medium text-gray-900">{{ $installment->due_date->format('d M Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Status:</span>
                        <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">
                            {{ $installment->status_label }}
                        </span>
                    </div>
                </div>

                <div class="mt-4 p-3 bg-green-50 border border-green-200 rounded-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-info-circle text-green-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-green-700">
                                Setelah klik "Bayar Cicilan", cicilan akan ditandai sebagai lunas dan transaksi pembayaran akan dicatat otomatis.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="mt-6 bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Aksi Cepat</h3>
            </div>
            <div class="p-4 space-y-2">
                <a href="{{ route('installments.index') }}"
                   class="w-full flex justify-center items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <i class="fas fa-list mr-2"></i> Lihat Semua Cicilan
                </a>
                <a href="{{ route('transactions.create.type', ['type' => 'expense']) }}"
                   class="w-full flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700">
                    <i class="fas fa-plus mr-2"></i> Tambah Pengeluaran Lain
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Set max date to today
document.getElementById('payment_date').max = new Date().toISOString().split('T')[0];
</script>
@endpush