@extends('layouts.finance')

@section('title', 'Detail Akun')
@section('page-title', 'Detail Akun')

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center gap-3 mb-4">
            <a href="{{ route('accounts.index') }}"
               class="text-gray-600 hover:text-gray-900 transition-colors">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Detail Akun</h1>
        </div>

        <!-- Account Card -->
        <div class="bg-gradient-to-r {{ $account->type === 'paylater' ? 'from-purple-500 to-purple-600' :
                                     'from-blue-500 to-blue-600' }} rounded-xl p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium mb-1">{{ $account->type_label }}</p>
                    <h2 class="text-3xl font-bold mb-2">{{ $account->name }}</h2>
                    @if($account->type === 'paylater')
                        <p class="text-blue-200 text-sm">
                            Limit: Rp {{ number_format($account->limit ?: 0, 0, ',', '.') }} •
                            Digunakan: Rp {{ number_format($account->limit - $account->balance, 0, ',', '.') }}
                        </p>
                    @endif
                </div>
                <div class="text-right">
                    <p class="text-blue-200 text-sm mb-1">Saldo Saat Ini</p>
                    <p class="text-4xl font-bold">
                        Rp {{ number_format($account->balance, 0, ',', '.') }}
                    </p>
                </div>
                <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center">
                    <i class="fas {{ $account->type === 'paylater' ? 'fa-credit-card' : 'fa-wallet' }} text-white text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Account Details -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-lg font-semibold text-gray-800">
                        <i class="fas fa-info-circle mr-2 text-blue-600"></i>
                        Informasi Akun
                    </h2>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <h3 class="text-sm font-medium text-gray-700 mb-2">Nama Akun</h3>
                        <p class="text-gray-900 font-medium">{{ $account->name }}</p>
                    </div>

                    <div>
                        <h3 class="text-sm font-medium text-gray-700 mb-2">Tipe Akun</h3>
                        <p class="text-gray-900">{{ $account->type_label }}</p>
                    </div>

                    @if($account->type === 'paylater' && $account->limit)
                    <div>
                        <h3 class="text-sm font-medium text-gray-700 mb-2">Limit Paylater</h3>
                        <p class="text-gray-900">Rp {{ number_format($account->limit, 0, ',', '.') }}</p>
                    </div>
                    @endif

                    <div>
                        <h3 class="text-sm font-medium text-gray-700 mb-2">Saldo</h3>
                        <p class="text-lg font-semibold {{ $account->type === 'paylater' ? 'text-purple-600' : 'text-blue-600' }}">
                            Rp {{ number_format($account->balance, 0, ',', '.') }}
                        </p>
                    </div>

                    @if($account->note)
                    <div>
                        <h3 class="text-sm font-medium text-gray-700 mb-2">Catatan</h3>
                        <p class="text-gray-900">{{ $account->note }}</p>
                    </div>
                    @endif

                    <div>
                        <h3 class="text-sm font-medium text-gray-700 mb-2">ID Akun</h3>
                        <p class="text-gray-900">#{{ str_pad($account->id, 6, '0', STR_PAD_LEFT) }}</p>
                        <p class="text-xs text-gray-500">
                            Dibuat {{ $account->created_at->diffForHumans() }}
                        </p>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    <div class="flex flex-col gap-2">
                        <a href="{{ route('accounts.edit', $account->id) }}"
                           class="w-full inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200">
                            <i class="fas fa-edit mr-2"></i>
                            Edit Akun
                        </a>

                        <form action="{{ route('accounts.destroy', $account->id) }}" method="POST"
                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="w-full inline-flex items-center justify-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors duration-200">
                                <i class="fas fa-trash mr-2"></i>
                                Hapus Akun
                            </button>
                        </form>

                        <a href="{{ route('accounts.index') }}"
                           class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors duration-200">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transactions List -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-800">
                            <i class="fas fa-exchange-alt mr-2 text-green-600"></i>
                            Transaksi Terakhir
                        </h2>
                        <a href="{{ route('accounts.transactions', $account->id) }}"
                           class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                            Lihat Semua
                        </a>
                    </div>
                </div>

                <!-- Loading State -->
                <div id="loadingState" class="p-6 text-center">
                    <i class="fas fa-spinner fa-spin text-gray-400 text-2xl mb-2"></i>
                    <p class="text-sm text-gray-500">Memuat transaksi...</p>
                </div>

                <!-- Transactions List -->
                <div id="transactionsList" class="divide-y divide-gray-200">
                    <!-- Transactions will be loaded here via JavaScript -->
                </div>

                <!-- Empty State -->
                <div id="emptyState" class="p-8 text-center hidden">
                    <i class="fas fa-inbox text-gray-400 text-4xl mb-3"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-1">Belum ada transaksi</h3>
                    <p class="text-sm text-gray-500 mb-4">Mulai dengan menambahkan transaksi pertama untuk akun ini</p>
                    <a href="{{ route('transactions.create') }}?type=income&account_id={{ $account->id }}"
                       class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors duration-200 mr-2">
                        <i class="fas fa-plus mr-2"></i>
                        Pemasukan
                    </a>
                    <a href="{{ route('transactions.create') }}?type=expense&account_id={{ $account->id }}"
                       class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors duration-200">
                        <i class="fas fa-minus mr-2"></i>
                        Pengeluaran
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    loadTransactions();
});

async function loadTransactions() {
    const loadingState = document.getElementById('loadingState');
    const transactionsList = document.getElementById('transactionsList');
    const emptyState = document.getElementById('emptyState');

    try {
        loadingState.classList.remove('hidden');
        transactionsList.innerHTML = '';
        emptyState.classList.add('hidden');

        const response = await axios.get('{{ route('api.transactions.index') }}', {
            params: {
                account_id: {{ $account->id }},
                limit: 10
            }
        });

        const transactions = response.data.data;

        if (transactions.length === 0) {
            emptyState.classList.remove('hidden');
        } else {
            transactions.forEach(transaction => {
                const transactionElement = createTransactionElement(transaction);
                transactionsList.appendChild(transactionElement);
            });
        }

    } catch (error) {
        console.error('Error loading transactions:', error);
        transactionsList.innerHTML = `
            <div class="p-4 text-center text-red-600">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                Gagal memuat transaksi
            </div>
        `;
    } finally {
        loadingState.classList.add('hidden');
    }
}

function createTransactionElement(transaction) {
    const div = document.createElement('div');
    div.className = 'p-4 hover:bg-gray-50 transition-colors duration-150';

    const iconClass = transaction.type === 'income' ? 'fa-arrow-down' :
                    transaction.type === 'expense' ? 'fa-arrow-up' : 'fa-exchange-alt';

    const iconBgClass = transaction.type === 'income' ? 'bg-green-100 text-green-600' :
                       transaction.type === 'expense' ? 'bg-red-100 text-red-600' :
                       'bg-blue-100 text-blue-600';

    const amountClass = transaction.type === 'income' ? 'text-green-600' :
                       transaction.type === 'expense' ? 'text-red-600' : 'text-blue-600';

    const amountPrefix = transaction.type === 'income' ? '+' :
                       transaction.type === 'expense' ? '-' : '';

    div.innerHTML = `
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 ${iconBgClass} rounded-full flex items-center justify-center">
                    <i class="fas ${iconClass} text-sm"></i>
                </div>
                <div>
                    <h3 class="font-medium text-gray-900">
                        ${transaction.note || 'Tanpa Catatan'}
                    </h3>
                    <p class="text-sm text-gray-500">
                        ${transaction.category ? transaction.category.name : 'Tanpa Kategori'} •
                        ${formatDate(transaction.date)}
                    </p>
                </div>
            </div>
            <div class="text-right">
                <p class="font-semibold ${amountClass}">
                    ${amountPrefix}Rp ${formatCurrency(transaction.amount)}
                </p>
                <a href="{{ route('transactions.index') }}/${transaction.id}"
                   class="text-xs text-blue-600 hover:text-blue-800">
                    Lihat Detail
                </a>
            </div>
        </div>
    `;

    return div;
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric'
    });
}

function formatCurrency(amount) {
    return new Intl.NumberFormat('id-ID').format(amount);
}
</script>
@endpush