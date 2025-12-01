@extends('layouts.finance')

@section('title', 'Transaksi')
@section('page-title', 'Daftar Transaksi')

@section('content')
<!-- Header Section -->
<div class="mb-6 space-y-4">
    <!-- Mobile-Optimized Header -->
    <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-2xl p-6 text-white shadow-lg">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold mb-2">Transaksi Keuangan</h1>
                <p class="text-blue-100 text-sm sm:text-base">Kelola semua transaksi keuangan Anda dengan mudah</p>
            </div>

            <!-- Mobile Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
                <a href="{{ route('transactions.create', ['type' => 'income']) }}"
                   class="inline-flex items-center justify-center px-4 py-3 bg-green-500 hover:bg-green-600 text-white font-medium rounded-xl transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                    <i class="fas fa-arrow-down mr-2"></i>
                    <span class="hidden sm:inline">Pemasukan</span>
                    <span class="sm:hidden">+ Income</span>
                </a>
                <a href="{{ route('transactions.create', ['type' => 'expense']) }}"
                   class="inline-flex items-center justify-center px-4 py-3 bg-red-500 hover:bg-red-600 text-white font-medium rounded-xl transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                    <i class="fas fa-arrow-up mr-2"></i>
                    <span class="hidden sm:inline">Pengeluaran</span>
                    <span class="sm:hidden">- Expense</span>
                </a>
                <a href="{{ route('transactions.create', ['type' => 'transfer']) }}"
                   class="inline-flex items-center justify-center px-4 py-3 bg-white hover:bg-gray-50 text-blue-600 font-medium rounded-xl transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                    <i class="fas fa-exchange-alt mr-2"></i>
                    <span class="hidden sm:inline">Transfer</span>
                    <span class="sm:hidden">↔ Transfer</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-center gap-2">
                <i class="fas fa-filter text-gray-400"></i>
                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Filter Transaksi</h3>
            </div>

            <form id="filterForm" class="flex flex-col sm:flex-row gap-3 flex-1">
                <div class="flex-1 sm:flex-initial">
                    <select name="category" id="categoryFilter"
                            class="w-full sm:w-48 px-4 py-2.5 border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 rounded-lg text-sm transition-all duration-200">
                        <option value="">📁 Semua Kategori</option>
                    </select>
                </div>

                <div class="flex-1 sm:flex-initial">
                    <select name="account" id="accountFilter"
                            class="w-full sm:w-48 px-4 py-2.5 border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 rounded-lg text-sm transition-all duration-200">
                        <option value="">💳 Semua Akun</option>
                    </select>
                </div>

                <div class="flex-1 sm:flex-initial">
                    <select name="period" id="periodFilter"
                            class="w-full sm:w-48 px-4 py-2.5 border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 rounded-lg text-sm transition-all duration-200">
                        <option value="30">📅 30 Hari Terakhir</option>
                        <option value="month">📆 Bulan Ini</option>
                        <option value="year">🗓️ Tahun Ini</option>
                    </select>
                </div>

                <button type="submit"
                        class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg text-sm transition-all duration-200 shadow hover:shadow-md transform hover:-translate-y-0.5">
                    <i class="fas fa-search mr-2"></i>Filter
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="space-y-6">
    <!-- Loading State -->
    <div id="loadingState" class="bg-white rounded-xl shadow-sm p-8">
        <div class="flex flex-col items-center justify-center text-center">
            <div class="relative">
                <div class="w-16 h-16 border-4 border-blue-200 border-t-blue-600 rounded-full animate-spin"></div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <i class="fas fa-coins text-blue-600 text-xl"></i>
                </div>
            </div>
            <p class="mt-4 text-gray-600 font-medium">Memuat data transaksi...</p>
            <p class="text-sm text-gray-400 mt-1">Mohon tunggu sebentar</p>
        </div>
    </div>

    <!-- Empty State -->
    <div id="emptyState" class="hidden bg-white rounded-xl shadow-sm p-8">
        <div class="flex flex-col items-center justify-center text-center max-w-md mx-auto">
            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                <i class="fas fa-inbox text-gray-400 text-3xl"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-800 mb-2">Belum Ada Transaksi</h3>
            <p class="text-gray-500 mb-6">Mulai catat transaksi keuangan pertama Anda untuk mulai mengelola keuangan dengan lebih baik</p>

            <div class="flex flex-col sm:flex-row gap-3">
                <a href="{{ route('transactions.create', ['type' => 'income']) }}"
                   class="inline-flex items-center justify-center px-6 py-3 bg-green-500 hover:bg-green-600 text-white font-medium rounded-xl transition-all duration-200 shadow hover:shadow-lg transform hover:-translate-y-0.5">
                    <i class="fas fa-plus mr-2"></i>Tambah Pemasukan
                </a>
                <a href="{{ route('transactions.create', ['type' => 'expense']) }}"
                   class="inline-flex items-center justify-center px-6 py-3 bg-red-500 hover:bg-red-600 text-white font-medium rounded-xl transition-all duration-200 shadow hover:shadow-lg transform hover:-translate-y-0.5">
                    <i class="fas fa-plus mr-2"></i>Tambah Pengeluaran
                </a>
            </div>
        </div>
    </div>

    <!-- Transaction List -->
    <div id="transactionList" class="hidden space-y-4">
        <!-- Summary Cards (Mobile First) -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
            <div class="bg-gradient-to-br from-green-400 to-green-600 rounded-xl p-4 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm font-medium">Total Pemasukan</p>
                        <p class="text-2xl font-bold mt-1" id="totalIncome">Rp 0</p>
                    </div>
                    <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                        <i class="fas fa-arrow-down text-white"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-red-400 to-red-600 rounded-xl p-4 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-red-100 text-sm font-medium">Total Pengeluaran</p>
                        <p class="text-2xl font-bold mt-1" id="totalExpense">Rp 0</p>
                    </div>
                    <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                        <i class="fas fa-arrow-up text-white"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-blue-400 to-blue-600 rounded-xl p-4 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm font-medium">Saldo Bersih</p>
                        <p class="text-2xl font-bold mt-1" id="netBalance">Rp 0</p>
                    </div>
                    <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                        <i class="fas fa-wallet text-white"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transaction Items -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">Daftar Transaksi</h2>
                <p class="text-sm text-gray-600 mt-1">Menampilkan <span id="transactionCount">0</span> transaksi</p>
            </div>

            <div class="divide-y divide-gray-100" id="transactionItems">
                <!-- Transaction items will be inserted here -->
            </div>
        </div>

        <!-- Enhanced Pagination -->
        <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100" id="paginationContainer">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex-1 flex justify-between sm:hidden" id="mobilePagination">
                    <!-- Mobile pagination will be inserted here -->
                </div>
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between w-full">
                    <div id="paginationInfo" class="text-sm text-gray-600">
                        <!-- Pagination info will be inserted here -->
                    </div>
                    <div id="paginationLinks" class="flex items-center space-x-1">
                        <!-- Pagination links will be inserted here -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Helper functions
function formatCurrency(amount) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(amount);
}

function formatDate(date) {
    return new Date(date).toLocaleDateString('id-ID');
}

function formatDateTime(dateTime) {
    return new Date(dateTime).toLocaleString('id-ID');
}

function showAlert(type, message) {
    // Remove existing alerts
    const existingAlerts = document.querySelectorAll('.api-alert');
    existingAlerts.forEach(alert => alert.remove());

    // Create new alert
    const alertDiv = document.createElement('div');
    alertDiv.className = `api-alert mb-4 p-4 rounded-lg ${
        type === 'success' ? 'bg-green-100 border border-green-400 text-green-700' :
        type === 'error' ? 'bg-red-100 border border-red-400 text-red-700' :
        type === 'warning' ? 'bg-yellow-100 border border-yellow-400 text-yellow-700' :
        'bg-blue-100 border border-blue-400 text-blue-700'
    }`;
    alertDiv.innerHTML = `
        <div class="flex items-center">
            <div class="flex-shrink-0">
                ${type === 'success' ? '<i class="fas fa-check-circle"></i>' :
                  type === 'error' ? '<i class="fas fa-exclamation-circle"></i>' :
                  type === 'warning' ? '<i class="fas fa-exclamation-triangle"></i>' :
                  '<i class="fas fa-info-circle"></i>'}
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium">${message}</p>
            </div>
            <div class="ml-auto pl-3">
                <button onclick="this.closest('.api-alert').remove()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    `;

    // Add to top of container
    const container = document.querySelector('.container') || document.body;
    container.insertBefore(alertDiv, container.firstChild);

    // Auto remove after 5 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}

function handleApiError(error, context = 'Operation') {
    console.error(`${context} error:`, error);

    let message = 'An error occurred';

    if (error.response) {
        if (error.response.status === 401) {
            message = 'Please login to continue';
            setTimeout(() => {
                window.location.href = '{{ route('login') }}';
            }, 2000);
        } else if (error.response.status === 403) {
            message = 'Access forbidden';
        } else if (error.response.status === 404) {
            message = 'Resource not found';
        } else if (error.response.status === 422) {
            // Validation errors
            if (error.response.data.errors) {
                const errors = Object.values(error.response.data.errors).flat();
                message = errors.join(', ');
            } else {
                message = error.response.data.message || 'Validation failed';
            }
        } else if (error.response.data && error.response.data.message) {
            message = error.response.data.message;
        }
    } else if (error.message) {
        message = error.message;
    }

    showAlert('error', message);
}

function handleApiSuccess(response, message) {
    if (response.data && response.data.message) {
        showAlert('success', response.data.message);
    } else {
        showAlert('success', message);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    let currentPage = 1;

    // Load initial data
    loadTransactions();
    loadFilterData();

    // Handle filter form submission
    document.getElementById('filterForm').addEventListener('submit', function(e) {
        e.preventDefault();
        currentPage = 1;
        loadTransactions();
    });

    // Load transactions via API
    async function loadTransactions(page = 1) {
        try {
            document.getElementById('loadingState').classList.remove('hidden');
            document.getElementById('emptyState').classList.add('hidden');
            document.getElementById('transactionList').classList.add('hidden');

            // Build query parameters
            const params = new URLSearchParams();
            params.append('page', page);

            const categoryFilter = document.getElementById('categoryFilter').value;
            const accountFilter = document.getElementById('accountFilter').value;
            const periodFilter = document.getElementById('periodFilter').value;

            if (categoryFilter) params.append('category', categoryFilter);
            if (accountFilter) params.append('account', accountFilter);
            if (periodFilter) params.append('period', periodFilter);

            const response = await axios.get('{{ route("api.transactions.index") }}?' + params.toString());

            if (response.data.success) {
                // Update Summary Cards if summary data exists
                if (response.data.summary) {
                    updateSummaryCards(
                        response.data.summary.total_income, 
                        response.data.summary.total_expense, 
                        response.data.summary.net_balance
                    );
                }

                if (response.data.data && response.data.data.data && response.data.data.data.length > 0) {
                    displayTransactions(response.data.data);
                    displayPagination(response.data.data);
                    document.getElementById('transactionList').classList.remove('hidden');
                } else {
                    // If no transactions but we have summary (e.g. empty search result), show empty state
                    // But keep summary visible? Usually empty state replaces list.
                    // Let's just update summary to 0 if needed or keep last state.
                    // If filtering yields no result, showing 0 in summary is correct.
                    if (!response.data.summary) {
                        updateSummaryCards(0, 0, 0);
                    }
                    document.getElementById('emptyState').classList.remove('hidden');
                }
            }
        } catch (error) {
            handleApiError(error, 'Memuat data transaksi');
        } finally {
            document.getElementById('loadingState').classList.add('hidden');
        }
    }

    // Display transactions
    function displayTransactions(transactions) {
        const container = document.getElementById('transactionItems');
        container.innerHTML = '';

        // Note: Totals are now calculated by backend and passed via summary object
        // No need to calculate client-side anymore

        // Update transaction count
        document.getElementById('transactionCount').textContent = transactions.total;

        // Create transaction items with modern design
        transactions.data.forEach(transaction => {
            const div = document.createElement('div');
            div.className = 'p-4 sm:p-6 hover:bg-gray-50 transition-colors duration-150 border-l-4 ' +
                          (transaction.type === 'income' ? 'border-green-500 hover:bg-green-50' :
                           transaction.type === 'expense' ? 'border-red-500 hover:bg-red-50' :
                           'border-blue-500 hover:bg-blue-50');

            const iconClass = transaction.type === 'income' ? 'fa-arrow-down' :
                            transaction.type === 'expense' ? 'fa-arrow-up' : 'fa-exchange-alt';

            const iconBgClass = transaction.type === 'income' ? 'bg-green-100 text-green-600' :
                               transaction.type === 'expense' ? 'bg-red-100 text-red-600' : 'bg-blue-100 text-blue-600';

            const amountClass = transaction.type === 'income' ? 'text-green-600' :
                              transaction.type === 'expense' ? 'text-red-600' : 'text-blue-600';

            const amountPrefix = transaction.type === 'income' ? '+' :
                               transaction.type === 'expense' ? '-' : '';

            div.innerHTML = `
                <!-- Mobile First Transaction Card -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <!-- Left Section: Icon and Details -->
                    <div class="flex items-start sm:items-center gap-4 flex-1">
                        <!-- Transaction Icon -->
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 ${iconBgClass} rounded-full flex items-center justify-center shadow-sm">
                                <i class="fas ${iconClass} text-lg"></i>
                            </div>
                        </div>

                        <!-- Transaction Details -->
                        <div class="flex-1 min-w-0">
                            <!-- Title with Transfer Info -->
                            <div class="flex flex-col sm:flex-row sm:items-center sm:gap-2 mb-1">
                                <h3 class="text-base sm:text-lg font-semibold text-gray-900" title="${transaction.note || ''}">
                                    ${(transaction.note || 'Tanpa Catatan').length > 56 ? (transaction.note || 'Tanpa Catatan').substring(0, 56) + '...' : (transaction.note || 'Tanpa Catatan')}
                                </h3>
                                ${transaction.type === 'transfer' ?
                                    `<span class="text-xs sm:text-sm text-gray-500 bg-gray-100 px-2 py-1 rounded-full">
                                        <i class="fas fa-exchange-alt mr-1"></i>
                                        ${transaction.account?.name || 'Unknown'} → ${transaction.related_account?.name || 'Unknown'}
                                    </span>` : ''}
                            </div>

                            <!-- Meta Information -->
                            <div class="flex flex-col sm:flex-row sm:items-center sm:gap-4 text-sm text-gray-500 space-y-1 sm:space-y-0">
                                <div class="flex items-center gap-1">
                                    <i class="fas fa-tag text-xs"></i>
                                    <span>${transaction.category?.name || 'Tanpa Kategori'}</span>
                                </div>
                                <div class="flex items-center gap-1">
                                    <i class="fas fa-wallet text-xs"></i>
                                    <span>${transaction.account?.name || 'Tidak ada akun'}</span>
                                </div>
                                <div class="flex items-center gap-1">
                                    <i class="fas fa-calendar text-xs"></i>
                                    <span>${formatDate(transaction.date)}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Section: Amount and Actions -->
                    <div class="flex flex-col sm:items-end gap-2">
                        <!-- Amount -->
                        <div class="text-right">
                            <p class="text-xl sm:text-2xl font-bold ${amountClass}">
                                ${amountPrefix}${formatCurrency(transaction.amount)}
                            </p>
                            <p class="text-xs text-gray-500 mt-1">
                                ${transaction.type === 'income' ? 'Pemasukan' :
                                  transaction.type === 'expense' ? 'Pengeluaran' : 'Transfer'}
                            </p>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center gap-1 sm:gap-2">
                            <a href="{{ route('transactions.index') }}/${transaction.id}"
                               class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors duration-150"
                               title="Lihat Detail">
                                <i class="fas fa-eye text-sm"></i>
                            </a>
                            <a href="{{ route('transactions.index') }}/${transaction.id}/edit"
                               class="p-2 text-gray-600 hover:bg-gray-50 rounded-lg transition-colors duration-150"
                               title="Edit">
                                <i class="fas fa-edit text-sm"></i>
                            </a>
                            <button onclick="deleteTransaction(${transaction.id})"
                                    class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-150"
                                    title="Hapus">
                                <i class="fas fa-trash text-sm"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
            container.appendChild(div);
        });
    }

    // Update summary cards
    function updateSummaryCards(income, expense, netBalance) {
        document.getElementById('totalIncome').textContent = formatCurrency(income);
        document.getElementById('totalExpense').textContent = formatCurrency(expense);
        document.getElementById('netBalance').textContent = formatCurrency(netBalance);

        // Update net balance color based on positive/negative
        const netBalanceElement = document.getElementById('netBalance').parentElement.parentElement;
        if (netBalance < 0) {
            netBalanceElement.className = netBalanceElement.className.replace('from-blue-400 to-blue-600', 'from-orange-400 to-orange-600');
        } else {
            // Reset to blue if positive/zero (in case it was previously orange)
            netBalanceElement.className = netBalanceElement.className.replace('from-orange-400 to-orange-600', 'from-blue-400 to-blue-600');
        }
    }

    // Display pagination with modern design
    function displayPagination(transactions) {
        // Mobile pagination (simplified)
        const mobileContainer = document.getElementById('mobilePagination');
        mobileContainer.innerHTML = '';

        if (transactions.prev_page_url || transactions.next_page_url) {
            const mobileNav = document.createElement('div');
            mobileNav.className = 'flex justify-between w-full';

            if (transactions.prev_page_url) {
                const prevBtn = document.createElement('button');
                prevBtn.className = 'flex items-center px-4 py-2 bg-white border border-gray-300 text-sm font-medium rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200';
                prevBtn.innerHTML = '<i class="fas fa-chevron-left mr-2"></i>Sebelumnya';
                prevBtn.onclick = (e) => { e.preventDefault(); currentPage--; loadTransactions(currentPage); };
                mobileNav.appendChild(prevBtn);
            }

            if (transactions.next_page_url) {
                const nextBtn = document.createElement('button');
                nextBtn.className = 'flex items-center px-4 py-2 bg-blue-600 text-sm font-medium rounded-lg text-white hover:bg-blue-700 transition-colors duration-200';
                nextBtn.innerHTML = 'Selanjutnya<i class="fas fa-chevron-right ml-2"></i>';
                nextBtn.onclick = (e) => { e.preventDefault(); currentPage++; loadTransactions(currentPage); };
                mobileNav.appendChild(nextBtn);
            }

            mobileContainer.appendChild(mobileNav);
        }

        // Desktop pagination info with modern styling
        document.getElementById('paginationInfo').innerHTML = `
            <div class="flex items-center gap-2 text-sm">
                <span class="text-gray-500">Menampilkan</span>
                <span class="font-semibold text-gray-900">${transactions.from || 0}-${transactions.to || 0}</span>
                <span class="text-gray-500">dari</span>
                <span class="font-semibold text-gray-900">${transactions.total}</span>
                <span class="text-gray-500">transaksi</span>
                ${transactions.total > transactions.per_page ?
                    `<span class="ml-2 text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full">
                        Halaman ${transactions.current_page} dari ${transactions.last_page}
                    </span>` : ''}
            </div>
        `;

        // Enhanced pagination links
        const linksContainer = document.getElementById('paginationLinks');
        linksContainer.innerHTML = '';

        if (transactions.last_page > 1) {
            const nav = document.createElement('nav');
            nav.className = 'flex items-center gap-1';

            // Previous button
            if (transactions.prev_page_url) {
                const prevBtn = document.createElement('button');
                prevBtn.className = 'p-2 rounded-lg border border-gray-300 text-gray-500 hover:bg-gray-50 hover:text-gray-700 transition-colors duration-200';
                prevBtn.innerHTML = '<i class="fas fa-chevron-left"></i>';
                prevBtn.onclick = (e) => { e.preventDefault(); currentPage--; loadTransactions(currentPage); };
                nav.appendChild(prevBtn);
            } else {
                const prevBtn = document.createElement('button');
                prevBtn.className = 'p-2 rounded-lg border border-gray-200 text-gray-300 cursor-not-allowed';
                prevBtn.innerHTML = '<i class="fas fa-chevron-left"></i>';
                prevBtn.disabled = true;
                nav.appendChild(prevBtn);
            }

            // Page numbers with smart display
            const pages = [];
            const current = transactions.current_page;
            const last = transactions.last_page;

            // Always show first page
            if (current > 3) {
                pages.push(1);
                if (current > 4) {
                    pages.push('...');
                }
            }

            // Show pages around current
            for (let i = Math.max(1, current - 1); i <= Math.min(last, current + 1); i++) {
                if (i > 0 && i <= last) {
                    pages.push(i);
                }
            }

            // Always show last page
            if (current < last - 2) {
                if (current < last - 3) {
                    pages.push('...');
                }
                pages.push(last);
            }

            pages.forEach((page, index) => {
                if (page === '...') {
                    const dots = document.createElement('span');
                    dots.className = 'px-3 py-2 text-gray-400';
                    dots.textContent = '...';
                    nav.appendChild(dots);
                } else {
                    const btn = document.createElement('button');
                    btn.className = page === current ?
                        'px-4 py-2 bg-blue-600 text-white font-medium rounded-lg transition-colors duration-200' :
                        'px-4 py-2 text-gray-700 hover:bg-gray-50 font-medium rounded-lg transition-colors duration-200';
                    btn.textContent = page;
                    btn.onclick = () => { currentPage = page; loadTransactions(page); };
                    nav.appendChild(btn);
                }
            });

            // Next button
            if (transactions.next_page_url) {
                const nextBtn = document.createElement('button');
                nextBtn.className = 'p-2 rounded-lg border border-gray-300 text-gray-500 hover:bg-gray-50 hover:text-gray-700 transition-colors duration-200';
                nextBtn.innerHTML = '<i class="fas fa-chevron-right"></i>';
                nextBtn.onclick = (e) => { e.preventDefault(); currentPage++; loadTransactions(currentPage); };
                nav.appendChild(nextBtn);
            } else {
                const nextBtn = document.createElement('button');
                nextBtn.className = 'p-2 rounded-lg border border-gray-200 text-gray-300 cursor-not-allowed';
                nextBtn.innerHTML = '<i class="fas fa-chevron-right"></i>';
                nextBtn.disabled = true;
                nav.appendChild(nextBtn);
            }

            linksContainer.appendChild(nav);
        }
    }

    // Load filter data (categories and accounts)
    async function loadFilterData() {
        try {
            const [categoriesResponse, accountsResponse] = await Promise.all([
                axios.get('{{ route("api.categories.index") }}'),
                axios.get('{{ route("api.accounts.index") }}')
            ]);

            // Populate category filter
            if (categoriesResponse.data.success && categoriesResponse.data.data.grouped) {
                const categorySelect = document.getElementById('categoryFilter');
                const grouped = categoriesResponse.data.data.grouped;

                if (grouped.income) {
                    const incomeGroup = document.createElement('optgroup');
                    incomeGroup.label = 'Pemasukan';
                    grouped.income.forEach(category => {
                        const option = document.createElement('option');
                        option.value = category.id;
                        option.textContent = category.name;
                        incomeGroup.appendChild(option);
                    });
                    categorySelect.appendChild(incomeGroup);
                }

                if (grouped.expense) {
                    const expenseGroup = document.createElement('optgroup');
                    expenseGroup.label = 'Pengeluaran';
                    grouped.expense.forEach(category => {
                        const option = document.createElement('option');
                        option.value = category.id;
                        option.textContent = category.name;
                        expenseGroup.appendChild(option);
                    });
                    categorySelect.appendChild(expenseGroup);
                }
            }

            // Populate account filter
            if (accountsResponse.data.success && accountsResponse.data.data.accounts) {
                const accountSelect = document.getElementById('accountFilter');
                accountsResponse.data.data.accounts.forEach(account => {
                    const option = document.createElement('option');
                    option.value = account.id;
                    option.textContent = `${account.name} (${account.type_label})`;
                    accountSelect.appendChild(option);
                });
            }
        } catch (error) {
            console.error('Error loading filter data:', error);
        }
    }

    // Delete transaction
    window.deleteTransaction = async function(id) {
        if (confirm('Yakin ingin menghapus transaksi ini?')) {
            try {
                const response = await axios.delete('{{ route("api.transactions.destroy", ":id") }}'.replace(':id', id));
                if (response.data.success) {
                    handleApiSuccess(response, 'Transaksi berhasil dihapus!');
                    loadTransactions(currentPage); // Reload current page
                }
            } catch (error) {
                handleApiError(error, 'Menghapus transaksi');
            }
        }
    };
});
</script>
@endpush