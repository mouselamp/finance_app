@extends('layouts.finance')

@section('title', 'Detail Paylater')
@section('page-title', 'Detail Transaksi Paylater')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center gap-3 mb-4">
            <a href="{{ route('paylater.index') }}"
               class="text-gray-600 hover:text-gray-900 transition-colors">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Detail Transaksi</h1>
        </div>

        <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium mb-1">{{ $paylaterTransaction->note }}</p>
                    <p class="text-3xl font-bold">{{ $paylaterTransaction->account->name }}</p>
                    <p class="text-purple-200 text-sm mt-2">
                        Total: Rp {{ number_format($paylaterTransaction->total_amount, 0, ',', '.') }} •
                        Jenis: {{ $paylaterTransaction->payment_type_label }}
                        @if ($paylaterTransaction->payment_type === 'installment')
                            • Tenor: {{ $paylaterTransaction->tenor }}x • Cicilan: Rp {{ number_format($paylaterTransaction->monthly_amount, 0, ',', '.') }}
                        @endif
                    </p>
                </div>
                <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center">
                    <i class="fas fa-credit-card text-white text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Total Tagihan</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">Rp {{ number_format($paylaterTransaction->total_amount, 0, ',', '.') }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-file-invoice text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Sudah Dibayar</p>
                    <p class="text-2xl font-bold text-green-600 mt-1">Rp {{ number_format($totalPaid, 0, ',', '.') }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Sisa Tagihan</p>
                    <p class="text-2xl font-bold text-orange-600 mt-1">Rp {{ number_format($remainingBalance, 0, ',', '.') }}</p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-hourglass-half text-orange-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Progress Overview -->
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6 border border-gray-200">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Progress Pembayaran</h2>
        <?php
        $paidInstallments = $paylaterTransaction->installments()->where('status', 'paid')->count();
        $totalInstallments = $paylaterTransaction->installments()->count();
        $progressPercentage = $totalInstallments > 0 ? ($paidInstallments / $totalInstallments) * 100 : 0;
        ?>
        <div class="space-y-3">
            <div class="flex items-center justify-between text-sm">
                <span class="text-gray-600">Progress</span>
                <span class="font-semibold text-gray-900">{{ $paidInstallments }}/{{ $totalInstallments }} ({{ round($progressPercentage) }}%)</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-3">
                <div class="bg-gradient-to-r from-purple-500 to-purple-600 h-3 rounded-full transition-all duration-300"
                     style="width: {{ $progressPercentage }}%"></div>
            </div>
            @if ($remainingBalance > 0)
                <div class="text-sm text-gray-500 text-center">
                    Sisa {{ $totalInstallments - $paidInstallments }} cicilan lagi
                </div>
            @else
                <div class="text-sm text-green-600 font-medium text-center">
                    <i class="fas fa-check-circle mr-1"></i>
                    Semua cicilan telah lunas
                </div>
            @endif
        </div>
    </div>

    <!-- Installment Details -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-800">
                    <i class="fas fa-list mr-2 text-purple-600"></i>
                    Detail Cicilan
                </h2>
                @if ($remainingBalance > 0)
                    <a href="{{ route('paylater.pay', $paylaterTransaction->id) }}"
                       class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-all duration-200 shadow hover:shadow-lg">
                        <i class="fas fa-money-bill-wave mr-2"></i>
                        Bayar Cicilan
                    </a>
                @endif
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No.</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jatuh Tempo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($paylaterTransaction->installments()->orderBy('due_date')->get() as $installment)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $installment->id }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ \Carbon\Carbon::parse($installment->due_date)->format('d M Y') }}
                                @if ($installment->is_overdue && $installment->status === 'unpaid')
                                    <span class="ml-2 inline-flex items-center px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                        Terlambat {{ $installment->days_until_due * -1 }} hari
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                Rp {{ number_format($installment->amount, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($installment->status === 'paid')
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        <i class="fas fa-check mr-1"></i>
                                        Lunas
                                    </span>
                                @else
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-clock mr-1"></i>
                                        Belum Dibayar
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                @if ($installment->status === 'unpaid')
                                    <form action="{{ route('paylater.processPayment', $paylaterTransaction->id) }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="installment_ids[]" value="{{ $installment->id }}">
                                        <!-- We'll need to add account selection modal or redirect to pay page -->
                                        <button type="submit"
                                                onclick="alert('Silakan gunakan halaman pembayaran untuk memilih metode pembayaran'); return false;"
                                                class="text-green-600 hover:text-green-900"
                                                title="Bayar">
                                            <i class="fas fa-money-bill-wave"></i>
                                        </button>
                                    </form>
                                @else
                                    <span class="text-gray-400">
                                        <i class="fas fa-check"></i>
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="mt-6 flex items-center gap-3">
        <a href="{{ route('paylater.edit', $paylaterTransaction->id) }}"
           class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-all duration-200 shadow hover:shadow-lg">
            <i class="fas fa-edit mr-2"></i>
            Edit Transaksi
        </a>

        @if ($remainingBalance === 0)
            <form action="{{ route('paylater.destroy', $paylaterTransaction->id) }}" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit"
                        onclick="return confirm('Apakah Anda yakin ingin menghapus transaksi paylater ini?')"
                        class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-all duration-200 shadow hover:shadow-lg">
                    <i class="fas fa-trash mr-2"></i>
                    Hapus Transaksi
                </button>
            </form>
        @endif

        <a href="{{ route('paylater.index') }}"
           class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-all duration-200 shadow hover:shadow-lg">
            <i class="fas fa-arrow-left mr-2"></i>
            Kembali ke Daftar
        </a>
    </div>
</div>
@endsection