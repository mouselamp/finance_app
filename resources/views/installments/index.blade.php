@extends('layouts.finance')

@section('title', 'Cicilan')
@section('page-title', 'Manajemen Cicilan')

@section('content')
<div class="mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Cicilan & Paylater</h2>
        <p class="mt-1 text-sm text-gray-600">Kelola pembayaran cicilan paylater Anda</p>
    </div>
</div>

<!-- Unpaid Installments -->
<div class="bg-white shadow rounded-lg mb-6">
    <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">Cicilan Belum Dibayar</h3>
        <p class="mt-1 text-sm text-gray-500">Daftar cicilan yang perlu dibayar</p>
    </div>
    <div class="overflow-hidden">
        @if($unpaidInstallments->count() > 0)
            <ul class="divide-y divide-gray-200">
                @foreach($unpaidInstallments as $installment)
                    <li class="px-4 py-4 hover:bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 mr-3">
                                    @if($installment->is_overdue)
                                        <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-exclamation-triangle text-red-600"></i>
                                        </div>
                                    @else
                                        <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-clock text-yellow-600"></i>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">
                                        Cicilan #{{ $installment->id }}
                                        @if($installment->paylaterTransaction)
                                            - {{ $installment->paylaterTransaction->note ?? 'Paylater Transaction' }}
                                        @endif
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        Jatuh tempo: {{ $installment->due_date->format('d M Y') }}
                                        @if($installment->is_overdue)
                                            <span class="text-red-600 font-semibold"> (TERLAMBAT {{ abs($installment->days_until_due) }} hari)</span>
                                        @elseif($installment->days_until_due <= 3)
                                            <span class="text-yellow-600 font-semibold"> ({{ $installment->days_until_due }} hari lagi)</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-semibold text-red-600">
                                    Rp {{ number_format($installment->amount, 0, ',', '.') }}
                                </p>
                                <a href="{{ route('installments.pay', $installment->id) }}"
                                   class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded text-white bg-green-600 hover:bg-green-700">
                                    <i class="fas fa-check mr-1"></i> Bayar
                                </a>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        @else
            <div class="px-4 py-8 text-center text-gray-500">
                <i class="fas fa-check-circle text-4xl mb-2"></i>
                <p>Semua cicilan sudah dibayar!</p>
                <p class="text-sm mt-1">Tidak ada cicilan yang menunggu pembayaran</p>
            </div>
        @endif
    </div>
</div>

<!-- Upcoming Payments -->
@if($upcomingPayments->count() > 0)
<div class="bg-white shadow rounded-lg mb-6">
    <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">Pembayaran Mendatang (30 Hari)</h3>
        <p class="mt-1 text-sm text-gray-500">Cicilan yang akan jatuh tempo</p>
    </div>
    <div class="p-4">
        <div class="space-y-2">
            @foreach($upcomingPayments as $payment)
                <div class="flex items-center justify-between p-3 bg-blue-50 border border-blue-200 rounded-lg">
                    <div>
                        <p class="text-sm font-medium text-gray-900">Cicilan #{{ $payment->id }}</p>
                        <p class="text-xs text-blue-600">
                            <i class="fas fa-calendar mr-1"></i>
                            {{ $payment->due_date->format('d M Y') }}
                            ({{ $payment->days_until_due }} hari lagi)
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-semibold text-blue-600">
                            Rp {{ number_format($payment->amount, 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endif

<!-- Paid Installments -->
<div class="bg-white shadow rounded-lg mb-6">
    <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">Riwayat Pembayaran</h3>
        <p class="mt-1 text-sm text-gray-500">Cicilan yang sudah dibayar</p>
    </div>
    <div class="overflow-hidden">
        @if($paidInstallments->count() > 0)
            <ul class="divide-y divide-gray-200">
                @foreach($paidInstallments as $installment)
                    <li class="px-4 py-4 hover:bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 mr-3">
                                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-check text-green-600"></i>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">
                                        Cicilan #{{ $installment->id }}
                                        @if($installment->paylaterTransaction)
                                            - {{ $installment->paylaterTransaction->note ?? 'Paylater Transaction' }}
                                        @endif
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        Dibayar: {{ $installment->paid_at->format('d M Y H:i') }}
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-semibold text-green-600">
                                    Rp {{ number_format($installment->amount, 0, ',', '.') }}
                                </p>
                                <span class="text-xs text-green-600 bg-green-100 px-2 py-1 rounded">
                                    <i class="fas fa-check-circle mr-1"></i>Lunas
                                </span>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        @else
            <div class="px-4 py-8 text-center text-gray-500">
                <i class="fas fa-history text-4xl mb-2"></i>
                <p>Belum ada riwayat pembayaran</p>
                <p class="text-sm mt-1">Cicilan yang sudah dibayar akan muncul di sini</p>
            </div>
        @endif
    </div>
</div>

<!-- Installment Summary -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-yellow-100 rounded-full p-3">
                    <i class="fas fa-hourglass-half text-yellow-600 text-xl"></i>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Belum Dibayar</dt>
                        <dd class="text-lg font-semibold text-yellow-600">
                            {{ $unpaidInstallments->count() }} cicilan
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-100 rounded-full p-3">
                    <i class="fas fa-calendar-check text-green-600 text-xl"></i>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Total Dibayar</dt>
                        <dd class="text-lg font-semibold text-green-600">
                            {{ $paidInstallments->count() }} cicilan
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-red-100 rounded-full p-3">
                    <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Terlambat</dt>
                        <dd class="text-lg font-semibold text-red-600">
                            {{ $unpaidInstallments->where('is_overdue', true)->count() }} cicilan
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection