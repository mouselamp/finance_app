@extends('layouts.finance')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<!-- Welcome Section -->
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Selamat datang, <span id="userName">User</span>! 👋</h2>
    <p class="mt-1 text-gray-600">Kelola keuangan pribadi Anda dengan mudah dan teratur.</p>
</div>

<!-- Loading state for Stats -->
<div id="statsLoading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    @foreach([1,2,3,4] as $i)
        <div class="bg-white overflow-hidden shadow rounded-lg animate-pulse">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-gray-200 rounded-full p-3">
                        <div class="w-6 h-6 bg-gray-300 rounded"></div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <div class="h-4 bg-gray-300 rounded mb-2"></div>
                        <div class="h-6 bg-gray-400 rounded w-3/4"></div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<!-- Stats Cards -->
<div id="statsCards" class="hidden grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Total Balance -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-blue-100 rounded-full p-3">
                    <i class="fas fa-wallet text-blue-600 text-xl"></i>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Total Saldo</dt>
                        <dd class="text-lg font-semibold text-gray-900" id="totalBalance">
                            Rp 0
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Income -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-100 rounded-full p-3">
                    <i class="fas fa-arrow-up text-green-600 text-xl"></i>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Pemasukan Bulan Ini</dt>
                        <dd class="text-lg font-semibold text-green-600" id="monthlyIncome">
                            +Rp 0
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Expenses -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-red-100 rounded-full p-3">
                    <i class="fas fa-arrow-down text-red-600 text-xl"></i>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Pengeluaran Bulan Ini</dt>
                        <dd class="text-lg font-semibold text-red-600" id="monthlyExpenses">
                            -Rp 0
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Net Balance -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-purple-100 rounded-full p-3">
                    <i class="fas fa-chart-line text-purple-600 text-xl"></i>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Saldo Bersih</dt>
                        <dd class="text-lg font-semibold text-green-600" id="netBalance">
                            +Rp 0
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- API Token Section -->
<div class="bg-white shadow rounded-lg mb-8">
    <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <h3 class="text-lg leading-6 font-medium text-gray-900">API Token</h3>
            <button onclick="regenerateApiToken()" class="text-sm text-blue-600 hover:text-blue-500 font-medium">
                <i class="fas fa-sync-alt mr-1"></i>
                Regenerate Token
            </button>
        </div>
    </div>
    <div class="p-6">
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Your API Token</label>
                <div class="relative">
                    <input type="text"
                           id="apiTokenDisplay"
                           readonly
                           value="Loading..."
                           class="block w-full px-3 py-2 pr-10 border border-gray-300 rounded-md bg-gray-50 text-sm font-mono"
                           placeholder="API token will appear here">
                    <button onclick="copyApiToken()"
                            class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600"
                            title="Copy token">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>
                <p class="mt-1 text-xs text-gray-500">
                    Use this token to authenticate API requests
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                <div class="bg-gray-50 rounded p-3">
                    <h4 class="font-medium text-gray-900 mb-1">Bearer Token</h4>
                    <code class="text-xs text-gray-600">Authorization: Bearer YOUR_TOKEN</code>
                </div>
                <div class="bg-gray-50 rounded p-3">
                    <h4 class="font-medium text-gray-900 mb-1">Header Token</h4>
                    <code class="text-xs text-gray-600">X-API-TOKEN: YOUR_TOKEN</code>
                </div>
                <div class="bg-gray-50 rounded p-3">
                    <h4 class="font-medium text-gray-900 mb-1">CURL Example</h4>
                    <code class="text-xs text-gray-600 break-all">curl -H "X-API-TOKEN: YOUR_TOKEN" ...</code>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Recent Transactions -->
    <div class="lg:col-span-2">
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Transaksi Terbaru</h3>
                    <a href="{{ route('transactions.index') }}" class="text-sm text-blue-600 hover:text-blue-500">
                        Lihat Semua →
                    </a>
                </div>
            </div>

            <!-- Loading state for transactions -->
            <div id="transactionsLoading" class="p-8">
                <div class="space-y-4">
                    @foreach([1,2,3] as $i)
                        <div class="animate-pulse">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-gray-200 rounded-full mr-3"></div>
                                    <div>
                                        <div class="h-4 bg-gray-300 rounded mb-2 w-32"></div>
                                        <div class="h-3 bg-gray-200 rounded w-48"></div>
                                        <div class="h-3 bg-gray-200 rounded w-40 mt-1"></div>
                                    </div>
                                </div>
                                <div class="h-6 bg-gray-300 rounded w-20"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Transaction list -->
            <div id="transactionsList" class="hidden overflow-hidden">
                <ul class="divide-y divide-gray-200" id="transactionItems">
                    <!-- Transaction items will be inserted here -->
                </ul>
            </div>

            <!-- Empty state -->
            <div id="transactionsEmpty" class="hidden px-4 py-8 text-center text-gray-500">
                <i class="fas fa-inbox text-4xl mb-2"></i>
                <p>Belum ada transaksi</p>
                <a href="{{ route('transactions.create', ['type' => 'income']) }}" class="mt-2 inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-plus mr-2"></i>Tambah Transaksi
                </a>
            </div>
        </div>
    </div>

    <!-- Right Sidebar -->
    <div class="space-y-8">
        <!-- Accounts Overview -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Akun & Saldo</h3>
            </div>

            <!-- Loading state for accounts -->
            <div id="accountsLoading" class="p-4">
                <div class="space-y-3">
                    @foreach([1,2] as $i)
                        <div class="animate-pulse">
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div>
                                    <div class="h-4 bg-gray-300 rounded mb-1 w-24"></div>
                                    <div class="h-3 bg-gray-200 rounded w-16"></div>
                                </div>
                                <div class="h-4 bg-gray-300 rounded w-20"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Account list -->
            <div id="accountsList" class="hidden p-4">
                <div class="space-y-3" id="accountItems">
                    <!-- Account items will be inserted here -->
                </div>
                <div id="accountsEmpty" class="hidden text-center text-gray-500 py-4">
                    <i class="fas fa-credit-card text-2xl mb-2"></i>
                    <p class="text-sm">Belum ada akun</p>
                </div>
                <div id="manageAccountsBtn" class="hidden mt-4">
                    <a href="{{ route('accounts.index') }}" class="w-full flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                        Kelola Akun
                    </a>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Aksi Cepat</h3>
            </div>
            <div class="p-4">
                <div class="space-y-2">
                    <a href="{{ route('transactions.create', ['type' => 'income']) }}" class="w-full flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                        <i class="fas fa-plus mr-2"></i> Tambah Pemasukan
                    </a>
                    <a href="{{ route('transactions.create', ['type' => 'expense']) }}" class="w-full flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700">
                        <i class="fas fa-minus mr-2"></i> Tambah Pengeluaran
                    </a>
                    <a href="{{ route('transactions.create', ['type' => 'transfer']) }}" class="w-full flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                        <i class="fas fa-exchange-alt mr-2"></i> Transfer Dana
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
    loadDashboardData();
    loadApiToken();
});

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

// Load all dashboard data
async function loadDashboardData() {
    try {
        // Load user info
        await loadUserInfo();

        // Load statistics
        await loadStatistics();

        // Load recent transactions
        await loadRecentTransactions();

        // Load accounts
        await loadAccounts();

    } catch (error) {
        console.error('Error loading dashboard data:', error);
        handleApiError(error, 'Memuat data dashboard');
    }
}

// Load user information
async function loadUserInfo() {
    try {
        const response = await axios.get('{{ route("api.auth.me") }}');
        if (response.data.success) {
            document.getElementById('userName').textContent = response.data.data.name;
        }
    } catch (error) {
        console.error('Error loading user info:', error);
    }
}

// Load statistics
async function loadStatistics() {
    try {
        document.getElementById('statsLoading').classList.remove('hidden');
        document.getElementById('statsCards').classList.add('hidden');

        const response = await axios.get('{{ route("api.transactions.statistics") }}');

        if (response.data.success) {
            const stats = response.data.data;

            // Update stats cards
            document.getElementById('totalBalance').textContent = formatCurrency(stats.total_balance || 0);
            document.getElementById('monthlyIncome').textContent = '+' + formatCurrency(stats.monthly_income || 0);
            document.getElementById('monthlyExpenses').textContent = '-' + formatCurrency(stats.monthly_expenses || 0);

            const netBalance = (stats.monthly_income || 0) - (stats.monthly_expenses || 0);
            const netBalanceElement = document.getElementById('netBalance');
            netBalanceElement.textContent = (netBalance >= 0 ? '+' : '') + formatCurrency(netBalance);
            netBalanceElement.className = `text-lg font-semibold ${netBalance >= 0 ? 'text-green-600' : 'text-red-600'}`;
        }
    } catch (error) {
        console.error('Error loading statistics:', error);
    } finally {
        document.getElementById('statsLoading').classList.add('hidden');
        document.getElementById('statsCards').classList.remove('hidden');
    }
}

// Load recent transactions
async function loadRecentTransactions() {
    try {
        document.getElementById('transactionsLoading').classList.remove('hidden');
        document.getElementById('transactionsList').classList.add('hidden');
        document.getElementById('transactionsEmpty').classList.add('hidden');

        const response = await axios.get('{{ route("api.transactions.index") }}?page=1'); // Get first page

        if (response.data.success && response.data.data && response.data.data.data && response.data.data.data.length > 0) {
            displayTransactions(response.data.data.data.slice(0, 5)); // Show only 5 most recent
            document.getElementById('transactionsList').classList.remove('hidden');
        } else {
            document.getElementById('transactionsEmpty').classList.remove('hidden');
        }
    } catch (error) {
        console.error('Error loading transactions:', error);
    } finally {
        document.getElementById('transactionsLoading').classList.add('hidden');
    }
}

// Load accounts
async function loadAccounts() {
    try {
        document.getElementById('accountsLoading').classList.remove('hidden');
        document.getElementById('accountsList').classList.add('hidden');

        const response = await axios.get('{{ route("api.accounts.index") }}');

        if (response.data.success && response.data.data.accounts.length > 0) {
            displayAccounts(response.data.data.accounts);
            document.getElementById('manageAccountsBtn').classList.remove('hidden');
        } else {
            document.getElementById('accountsEmpty').classList.remove('hidden');
        }
    } catch (error) {
        console.error('Error loading accounts:', error);
    } finally {
        document.getElementById('accountsLoading').classList.add('hidden');
        document.getElementById('accountsList').classList.remove('hidden');
    }
}

// Display transactions
function displayTransactions(transactions) {
    const container = document.getElementById('transactionItems');
    container.innerHTML = '';

    transactions.forEach(transaction => {
        const li = document.createElement('li');
        li.className = 'px-4 py-4 hover:bg-gray-50';

        const iconClass = transaction.type === 'income' ? 'fa-arrow-down text-green-600 bg-green-100' :
                        transaction.type === 'expense' ? 'fa-arrow-up text-red-600 bg-red-100' :
                        'fa-exchange-alt text-blue-600 bg-blue-100';

        const amountClass = transaction.type === 'income' ? 'text-green-600' :
                          transaction.type === 'expense' ? 'text-red-600' : 'text-blue-600';

        const amountPrefix = transaction.type === 'income' ? '+' :
                           transaction.type === 'expense' ? '-' : '';

        li.innerHTML = `
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="flex-shrink-0 mr-3">
                        <div class="w-10 h-10 ${iconClass.split(' ')[1]} rounded-full flex items-center justify-center">
                            <i class="fas ${iconClass.split(' ')[0]} ${iconClass.split(' ')[2]}"></i>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">${transaction.category?.name || 'Tanpa Kategori'}</p>
                        <p class="text-sm text-gray-500">${transaction.note || 'Tidak ada catatan'}</p>
                        <p class="text-xs text-gray-400">
                            ${transaction.account?.name || 'Tidak ada akun'} • ${window.api.formatDate(transaction.date)}
                        </p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-lg font-semibold ${amountClass}">
                        ${amountPrefix} ${window.api.formatCurrency(transaction.amount)}
                    </p>
                </div>
            </div>
        `;
        container.appendChild(li);
    });
}

// Display accounts
function displayAccounts(accounts) {
    const container = document.getElementById('accountItems');
    container.innerHTML = '';

    accounts.forEach(account => {
        const div = document.createElement('div');
        div.className = 'flex items-center justify-between p-3 bg-gray-50 rounded-lg';

        div.innerHTML = `
            <div>
                <p class="text-sm font-medium text-gray-900">${account.name}</p>
                <p class="text-xs text-gray-500">${account.type_label}</p>
            </div>
            <div class="text-right">
                <p class="text-sm font-semibold text-gray-900">
                    ${window.api.formatCurrency(account.balance)}
                </p>
            </div>
        `;
        container.appendChild(div);
    });
}

// API Token Functions
async function loadApiToken() {
    try {
        const response = await axios.get('{{ route('api.auth.me') }}');
        const apiToken = response.data.data.api_token;

        if (apiToken) {
            document.getElementById('apiTokenDisplay').value = apiToken;
        } else {
            document.getElementById('apiTokenDisplay').value = 'No API token found. Please regenerate.';
        }
    } catch (error) {
        console.error('Failed to load API token:', error);
        document.getElementById('apiTokenDisplay').value = 'Failed to load API token';
    }
}

async function regenerateApiToken() {
    if (!confirm('Are you sure you want to regenerate your API token? The old token will become invalid.')) {
        return;
    }

    const button = event.target;
    const originalText = button.innerHTML;
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Generating...';

    try {
        // Generate new token by calling login again
        const response = await axios.post('{{ route('api.auth.login') }}', {
            email: '{{ auth()->user()->email }}',
            password: 'DUMMY_PASSWORD' // This will be handled in backend
        });

        const newToken = response.data.data.token;
        document.getElementById('apiTokenDisplay').value = newToken;
        showAlert('success', 'API token regenerated successfully!');
    } catch (error) {
        console.error('Failed to regenerate API token:', error);
        showAlert('error', 'Failed to regenerate API token: ' + (error.response?.data?.message || error.message));
    } finally {
        button.disabled = false;
        button.innerHTML = originalText;
    }
}

function copyApiToken() {
    const tokenInput = document.getElementById('apiTokenDisplay');

    if (tokenInput.value && tokenInput.value !== 'Loading...' && tokenInput.value !== 'No API token found.') {
        tokenInput.select();
        document.execCommand('copy');

        // Show copy feedback
        const originalValue = tokenInput.value;
        tokenInput.value = 'Copied!';
        setTimeout(() => {
            tokenInput.value = originalValue;
        }, 2000);

        showAlert('success', 'API token copied to clipboard!');
    }
}
</script>
@endpush