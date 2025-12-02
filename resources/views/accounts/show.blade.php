@extends('layouts.finance')

@section('title', 'Detail Akun')
@section('page-title', 'Detail Akun')

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Loading state -->
    <div id="loadingState" class="text-center py-12">
        <div class="flex-shrink-0 mx-auto bg-gray-200 rounded-full p-6 mb-4">
            <i class="fas fa-spinner fa-spin text-gray-600 text-4xl"></i>
        </div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">Memuat data akun...</h3>
    </div>

    <!-- Account detail (hidden by default) -->
    <div id="accountDetail" class="hidden">
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
            <div id="accountCard" class="bg-gradient-to-r rounded-xl p-6 text-white shadow-lg">
                <!-- Account card will be populated via JavaScript -->
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
                        <h3 class="text-sm font-medium text-gray-700 mb-2">Pemilik Akun</h3>
                        <p id="ownerName" class="text-gray-900 font-medium">
                            <i class="fas fa-user mr-2 text-gray-400"></i>
                            <span id="ownerNameText">Loading...</span>
                            <span id="ownerBadge" class="ml-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"></span>
                        </p>
                    </div>

                    <div>
                        <h3 class="text-sm font-medium text-gray-700 mb-2">Nama Akun</h3>
                        <p id="accountName" class="text-gray-900 font-medium">Loading...</p>
                    </div>

                    <div>
                        <h3 class="text-sm font-medium text-gray-700 mb-2">Tipe Akun</h3>
                        <p id="accountType" class="text-gray-900">Loading...</p>
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
                <div id="actionButtons" class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    <!-- Action buttons will be populated via JavaScript -->
                </div>
            </div>
        </div>

        <!-- Transactions List -->
        <div id="transactionsSection" class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-800">
                            <i class="fas fa-exchange-alt mr-2 text-green-600"></i>
                            Transaksi Terakhir
                        </h2>
                        <a id="viewAllTransactions" href=""
                           class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                            Lihat Semua
                        </a>
                    </div>
                </div>

                <!-- Loading State -->
                <div id="transactionsLoadingState" class="p-6 text-center">
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
let accountData = null;

document.addEventListener('DOMContentLoaded', function() {
    loadAccountData();
});

async function loadAccountData() {
    const loadingState = document.getElementById('loadingState');
    const accountDetail = document.getElementById('accountDetail');
    const accountId = {{ request()->route('account')->id }};

    try {
        loadingState.classList.remove('hidden');
        accountDetail.classList.add('hidden');

        const response = await axios.get(`/api/accounts/${accountId}`);

        if (response.data.success) {
            accountData = response.data.data;
            displayAccountData(accountData);
            loadTransactions(accountId);
            accountDetail.classList.remove('hidden');
        } else {
            throw new Error(response.data.message || 'Failed to load account');
        }

    } catch (error) {
        console.error('Error loading account:', error);
        document.body.innerHTML = `
            <div class="flex items-center justify-center h-screen">
                <div class="text-center">
                    <i class="fas fa-exclamation-triangle text-red-500 text-4xl mb-4"></i>
                    <h2 class="text-xl font-semibold text-gray-900 mb-2">Gagal memuat data akun</h2>
                    <p class="text-gray-600 mb-4">${error.response?.data?.message || error.message}</p>
                    <a href="{{ route('accounts.index') }}" class="text-blue-600 hover:text-blue-800">
                        <i class="fas fa-arrow-left mr-2"></i>Kembali ke daftar akun
                    </a>
                </div>
            </div>
        `;
    } finally {
        loadingState.classList.add('hidden');
    }
}

function displayAccountData(data) {
    const { account, is_owner } = data;

    // Update account card
    const accountCard = document.getElementById('accountCard');
    const gradientClass = account.type === 'paylater' ? 'from-purple-500 to-purple-600' : 'from-blue-500 to-blue-600';

    accountCard.className = `bg-gradient-to-r ${gradientClass} rounded-xl p-6 text-white shadow-lg`;
    accountCard.innerHTML = `
        <div class="flex items-center justify-between">
            <div>
                <p class="text-blue-100 text-sm font-medium mb-1">${account.type_label}</p>
                <h2 class="text-3xl font-bold mb-2">${account.name}</h2>
                ${!is_owner ? `
                    <p class="text-blue-200 text-sm">
                        <i class="fas fa-user mr-1"></i>${account.user ? account.user.name : 'Unknown'}
                    </p>
                ` : ''}
                ${account.type === 'paylater' && account.limit ? `
                    <p class="text-blue-200 text-sm">
                        Limit: ${window.api.formatCurrency(account.limit || 0)} •
                        Digunakan: ${window.api.formatCurrency(account.limit - account.balance)}
                    </p>
                ` : ''}
            </div>
            <div class="text-right">
                <p class="text-blue-200 text-sm mb-1">Saldo Saat Ini</p>
                <p class="text-4xl font-bold">
                    ${window.api.formatCurrency(account.balance)}
                </p>
            </div>
            <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center">
                <i class="fas ${account.type === 'paylater' ? 'fa-credit-card' : 'fa-wallet'} text-white text-2xl"></i>
            </div>
        </div>
    `;

    // Update account details
    document.getElementById('ownerNameText').textContent = account.user ? account.user.name : 'Unknown';

    const ownerBadge = document.getElementById('ownerBadge');
    if (is_owner) {
        ownerBadge.className = 'ml-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800';
        ownerBadge.innerHTML = '<i class="fas fa-check mr-1"></i>Anda';
    } else {
        ownerBadge.className = 'ml-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600';
        ownerBadge.innerHTML = '<i class="fas fa-lock mr-1"></i>Read Only';
    }

    document.getElementById('accountName').textContent = account.name;
    document.getElementById('accountType').textContent = account.type_label;

    // Update paylater limit if exists
    const paylaterLimitDiv = document.querySelector('[id$="paylater-limit"]');
    if (paylaterLimitDiv) {
        if (account.type === 'paylater' && account.limit) {
            paylaterLimitDiv.style.display = 'block';
            paylaterLimitDiv.querySelector('p').textContent = `${formatCurrency(account.limit)}`;
        } else {
            paylaterLimitDiv.style.display = 'none';
        }
    }

    // Update balance
    const balanceElement = document.querySelector('[id$="balance"]');
    if (balanceElement) {
        balanceElement.textContent = `${formatCurrency(account.balance)}`;
        balanceElement.className = `text-lg font-semibold ${account.type === 'paylater' ? 'text-purple-600' : 'text-blue-600'}`;
    }

    // Update note if exists
    const noteDiv = document.querySelector('[id$="note"]');
    if (noteDiv && account.note) {
        noteDiv.style.display = 'block';
        noteDiv.querySelector('p').textContent = account.note;
    } else if (noteDiv) {
        noteDiv.style.display = 'none';
    }

    // Update action buttons
    const actionButtons = document.getElementById('actionButtons');
    actionButtons.innerHTML = `
        <div class="flex flex-col gap-2">
            ${is_owner ? `
                <a href="{{ route('accounts.index') }}/${account.id}/edit"
                   class="w-full inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200">
                    <i class="fas fa-edit mr-2"></i>
                    Edit Akun
                </a>

                <form action="{{ route('accounts.index') }}/${account.id}" method="POST"
                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="w-full inline-flex items-center justify-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors duration-200">
                        <i class="fas fa-trash mr-2"></i>
                        Hapus Akun
                    </button>
                </form>
            ` : `
                <div class="w-full inline-flex items-center justify-center px-4 py-2 bg-gray-100 text-gray-400 font-medium rounded-lg cursor-not-allowed">
                    <i class="fas fa-lock mr-2"></i>
                    Read Only Access
                </div>
            `}

            <a href="{{ route('accounts.index') }}"
               class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors duration-200">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali
            </a>
        </div>
    `;

    // Update "View All Transactions" link
    const viewAllLink = document.getElementById('viewAllTransactions');
    if (viewAllLink) {
        viewAllLink.href = `/accounts/${account.id}/transactions`;
    }
}

async function loadTransactions(accountId) {
    const transactionsLoadingState = document.getElementById('transactionsLoadingState');
    const transactionsList = document.getElementById('transactionsList');
    const emptyState = document.getElementById('emptyState');

    try {
        if (transactionsLoadingState) {
            transactionsLoadingState.classList.remove('hidden');
        }
        transactionsList.innerHTML = '';
        emptyState.classList.add('hidden');

        const response = await axios.get('{{ route('api.transactions.index') }}', {
            params: {
                account_id: accountId,
                limit: 10
            }
        });

        let transactions = [];

        // API returns Laravel pagination: { data: { data: [...], ... } }
        if (response.data.data && response.data.data.data && Array.isArray(response.data.data.data)) {
            transactions = response.data.data.data;
        } else if (response.data.data && Array.isArray(response.data.data)) {
            // Fallback for direct array
            transactions = response.data.data;
        } else {
            console.error('Unexpected response structure:', response.data);
            transactions = [];
        }

        console.log('Transactions loaded:', transactions); // Debug log

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
        if (transactionsLoadingState) {
            transactionsLoadingState.classList.add('hidden');
        }
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
                        ${transaction.user && transaction.user.id !== {{ Auth::id() }} ? `
                            <span class="text-xs text-gray-400 ml-2">
                                <i class="fas fa-user mr-1"></i>${transaction.user.name}
                            </span>
                        ` : ''}
                    </h3>
                    <p class="text-sm text-gray-500">
                        ${transaction.category ? transaction.category.name : 'Tanpa Kategori'} •
                        ${formatDate(transaction.date)}
                    </p>
                </div>
            </div>
            <div class="text-right">
                <p class="font-semibold ${amountClass}">
                    ${amountPrefix}${window.api.formatCurrency(transaction.amount)}
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

</script>
@endpush