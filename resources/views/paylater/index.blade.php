@extends('layouts.finance')

@section('title', 'Paylater')
@section('page-title', 'Pembayaran Paylater')

@section('content')
<!-- Header Section -->
<div class="mb-6 space-y-4">
    <!-- Paylater Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-gradient-to-br from-purple-400 to-purple-600 rounded-xl p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium">Total Paylater</p>
                    <p class="text-2xl font-bold mt-1">Rp {{ number_format($totalPaylater, 0, ',', '.') }}</p>
                    <p class="text-xs text-purple-200 mt-1">Total pembayaran paylater</p>
                </div>
                <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                    <i class="fas fa-credit-card text-white text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-green-400 to-green-600 rounded-xl p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium">Total Dibayar</p>
                    <p class="text-2xl font-bold mt-1">Rp {{ number_format($totalPaid, 0, ',', '.') }}</p>
                    <p class="text-xs text-green-200 mt-1">Jumlah yang sudah dibayar</p>
                </div>
                <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                    <i class="fas fa-check-circle text-white text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-orange-400 to-orange-600 rounded-xl p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-sm font-medium">Sisa Tagihan</p>
                    <p class="text-2xl font-bold mt-1">Rp {{ number_format($totalPaylater - $totalPaid, 0, ',', '.') }}</p>
                    <p class="text-xs text-orange-200 mt-1">Sisa yang harus dibayar</p>
                </div>
                <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                    <i class="fas fa-hourglass-half text-white text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Page Header -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-credit-card text-purple-600 text-xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Pembayaran Paylater</h1>
                    <p class="text-sm text-gray-600 mt-1">Kelola dan pantau semua transaksi paylater Anda</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <!-- Quick Actions -->
                <a href="{{ route('transactions.create.type', ['type' => 'expense']) }}?paylater=true"
                   class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-xl transition-all duration-200 shadow hover:shadow-lg transform hover:-translate-y-0.5">
                    <i class="fas fa-plus mr-2"></i>
                    <span class="hidden sm:inline">Transaksi Baru</span>
                    <span class="sm:hidden">+ Baru</span>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Paylater Transactions List -->
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-800">Riwayat Transaksi</h2>
            <div class="flex items-center gap-2 text-sm text-gray-600">
                <span>Menampilkan {{ $paylaterTransactions->count() }} transaksi</span>
                @if ($paylaterTransactions->hasPages())
                    <span>• Halaman {{ $paylaterTransactions->currentPage() }} dari {{ $paylaterTransactions->lastPage() }}</span>
                @endif
            </div>
        </div>
    </div>

    <!-- Transactions Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Akun</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($paylaterTransactions as $transaction)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ \Carbon\Carbon::parse($transaction->date)->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $transaction->note }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    #{{ str_pad($transaction->id, 5, '0', STR_PAD_LEFT) }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $transaction->account->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                      {{ $transaction->payment_type === 'full'
                                          ? 'bg-green-100 text-green-800'
                                          : 'bg-blue-100 text-blue-800' }}">
                                    {{ $transaction->payment_type_label }}
                                </span>
                                @if ($transaction->payment_type === 'installment')
                                    <span class="ml-2 text-xs text-gray-500">
                                        {{ $transaction->tenor }}x
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($transaction->payment_type === 'full')
                                    <?php
                                    $paidInstallments = $transaction->installments()->where('status', 'paid')->count();
                                    $totalInstallments = $transaction->installments()->count();
                                    $isFullyPaid = $paidInstallments === $totalInstallments && $totalInstallments > 0;
                                    ?>
                                    @if ($isFullyPaid)
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                            Lunas
                                        </span>
                                    @else
                                        @if ($totalInstallments > 0)
                                            <a href="{{ route('paylater.pay', $transaction->id) }}"
                                               class="text-green-600 hover:text-green-900" title="Bayar">
                                                <i class="fas fa-money-bill-wave"></i>
                                            </a>
                                        @endif
                                        <div class="w-full">
                                            <div class="flex items-center justify-between text-xs mb-1">
                                                <span>{{ $paidInstallments }}/{{ $totalInstallments }}</span>
                                                <span>{{ round(($paidInstallments / $totalInstallments) * 100) }}%</span>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-2">
                                                <div class="bg-green-600 h-2 rounded-full transition-all duration-300"
                                                     style="width: {{ ($paidInstallments / $totalInstallments) * 100 }}%"></div>
                                            </div>
                                        </div>
                                    @endif
                                @else
                                    <?php
                                    $paidInstallments = $transaction->installments()->where('status', 'paid')->count();
                                    $totalInstallments = $transaction->installments()->count(); // Use actual installments count instead of tenor
                                    $progressPercentage = $totalInstallments > 0 ? ($paidInstallments / $totalInstallments) * 100 : 0;
                                    ?>
                                    <div class="w-full">
                                        <div class="flex items-center justify-between text-xs mb-1">
                                            <span>{{ $paidInstallments }}/{{ $totalInstallments }}</span>
                                            <span>{{ round($progressPercentage) }}%</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                                                 style="width: {{ $progressPercentage }}%"></div>
                                        </div>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('paylater.details', $transaction->id) }}"
                                       class="text-blue-600 hover:text-blue-900" title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if ($transaction->payment_type === 'installment')
                                        <?php
                                        $unpaidInstallments = $transaction->installments()->where('status', 'unpaid')->count();
                                        ?>
                                        @if ($unpaidInstallments > 0)
                                            <a href="{{ route('paylater.pay', $transaction->id) }}"
                                               class="text-green-600 hover:text-green-900" title="Bayar Cicilan">
                                                <i class="fas fa-money-bill-wave"></i>
                                            </a>
                                        @endif
                                    @endif
                                    <a href="{{ route('paylater.edit', $transaction->id) }}"
                                       class="text-indigo-600 hover:text-indigo-900" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('paylater.destroy', $transaction->id) }}" method="POST"
                                          onsubmit="return confirm('Yakin ingin menghapus transaksi paylater ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="text-red-600 hover:text-red-900" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                    <i class="fas fa-credit-card text-gray-400 text-2xl"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-800 mb-2">Belum Ada Transaksi Paylater</h3>
                                <p class="text-gray-500 mb-6">Mulai catat transaksi paylater pertama Anda</p>
                                <a href="{{ route('transactions.create.type', ['type' => 'expense']) }}?paylater=true"
                                   class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-lg transition-all duration-200 shadow hover:shadow-lg">
                                    <i class="fas fa-plus mr-2"></i>
                                    Buat Transaksi Baru
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($paylaterTransactions->hasPages())
        <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
            <div class="flex-1 flex justify-between sm:hidden">
                @if($paylaterTransactions->previousPageUrl)
                    <a href="{{ $paylaterTransactions->previousPageUrl }}"
                       class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Sebelumnya
                    </a>
                @endif
                @if($paylaterTransactions->nextPageUrl)
                    <a href="{{ $paylaterTransactions->nextPageUrl }}"
                       class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Selanjutnya
                    </a>
                @endif
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm text-gray-700">
                        Menampilkan <span class="font-medium">{{ $paylaterTransactions->firstItem() }}</span>
                        hingga <span class="font-medium">{{ $paylaterTransactions->lastItem() }}</span>
                        dari <span class="font-medium">{{ $paylaterTransactions->total() }}</span> transaksi
                    </p>
                </div>
                <div>
                    {{ $paylaterTransactions->links() }}
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function formatRupiah(amount) {
    return 'Rp ' + Number(amount).toLocaleString('id-ID');
}
</script>
@endpush