@extends('layouts.finance')

@section('title', 'Akun')
@section('page-title', 'Akun & Sumber Dana')

@section('content')
<div class="mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Akun & Sumber Dana</h2>
            <p class="mt-1 text-sm text-gray-600">Kelola semua sumber dana dan rekening Anda</p>
        </div>
        <a href="{{ route('accounts.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            <i class="fas fa-plus mr-2"></i> Tambah Akun
        </a>
    </div>
</div>

<!-- Loading state -->
<div id="loadingState" class="text-center py-12">
    <div class="flex-shrink-0 mx-auto bg-gray-200 rounded-full p-6 mb-4">
        <i class="fas fa-spinner fa-spin text-gray-600 text-4xl"></i>
    </div>
    <h3 class="text-lg font-medium text-gray-900 mb-2">Memuat data akun...</h3>
</div>

<!-- Account grid (hidden by default) -->
<div id="accountGrid" class="hidden">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="accountCards">
        <!-- Account cards will be inserted here -->
    </div>

    <!-- Add New Account Card -->
    <div class="bg-gray-50 border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center hover:bg-gray-100 transition-colors">
        <a href="{{ route('accounts.create') }}" class="block text-center p-6 w-full">
            <div class="flex-shrink-0 mx-auto bg-gray-200 rounded-full p-3 mb-3">
                <i class="fas fa-plus text-gray-600 text-xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900">Tambah Akun Baru</h3>
            <p class="text-sm text-gray-500 mt-1">Tambah sumber dana baru</p>
        </a>
    </div>

    <div class="mt-8 bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Ringkasan Akun</h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">Total saldo dari semua akun</p>
        </div>
        <div class="border-t border-gray-200">
            <dl>
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Total Saldo</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        <span class="text-2xl font-bold text-gray-900" id="totalBalance">Rp 0</span>
                    </dd>
                </div>
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Jumlah Akun</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        <span class="text-lg font-semibold" id="accountCount">0 akun</span>
                    </dd>
                </div>
            </dl>
        </div>
    </div>
</div>

<!-- Empty state (hidden by default) -->
<div id="emptyState" class="hidden text-center py-12">
    <div class="flex-shrink-0 mx-auto bg-gray-200 rounded-full p-6 mb-4">
        <i class="fas fa-wallet text-gray-600 text-4xl"></i>
    </div>
    <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada akun</h3>
    <p class="text-sm text-gray-500 mb-6">Mulai dengan menambahkan akun pertama Anda</p>
    <a href="{{ route('accounts.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
        <i class="fas fa-plus mr-2"></i> Tambah Akun Pertama
    </a>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    loadAccounts();
});

// Load accounts via API
async function loadAccounts() {
    try {
        document.getElementById('loadingState').classList.remove('hidden');
        document.getElementById('emptyState').classList.add('hidden');
        document.getElementById('accountGrid').classList.add('hidden');

        const response = await axios.get('{{ route("api.accounts.index") }}');

        console.log('API Response:', response.data); // Debug log

        // Handle response structure
        let accounts = [];
        if (response.data.data && response.data.data.accounts) {
             // Structure: { success: true, data: { accounts: [...] } }
            accounts = response.data.data.accounts;
        } else if (Array.isArray(response.data)) {
            // Structure: [...] (Direct array)
            accounts = response.data;
        } else if (response.data.data && Array.isArray(response.data.data)) {
             // Structure: { data: [...] } (Standard Laravel Resource)
            accounts = response.data.data;
        }

        if (accounts.length > 0) {
            displayAccounts(accounts);
            displaySummary(accounts);
            document.getElementById('accountGrid').classList.remove('hidden');
        } else {
            document.getElementById('emptyState').classList.remove('hidden');
        }
    } catch (error) {
        console.error('Error loading accounts:', error); // Debug log
        handleApiError(error, 'Memuat data akun');
    } finally {
        document.getElementById('loadingState').classList.add('hidden');
    }
}

// Display accounts
function displayAccounts(accounts) {
    const container = document.getElementById('accountCards');
    container.innerHTML = '';

    accounts.forEach(account => {
        const card = document.createElement('div');
        card.className = 'bg-white overflow-hidden shadow rounded-lg';

        const iconData = getAccountIconData(account.type);

        card.innerHTML = `
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 ${iconData.bgColor} rounded-full p-3">
                        <i class="fas ${iconData.icon} ${iconData.color} text-xl"></i>
                    </div>
                    <div class="ml-5">
                        <h3 class="text-lg font-medium text-gray-900">${account.name}</h3>
                        <p class="text-sm text-gray-500">${account.type_label}</p>
                        <p class="text-xs text-gray-400">
                            <i class="fas fa-user mr-1"></i>${account.user_name}
                        </p>
                    </div>
                </div>
                <div class="mt-4">
                    <p class="text-2xl font-bold text-gray-900">${window.api.formatCurrency(account.balance)}</p>
                    <p class="text-sm text-gray-500">Saldo saat ini</p>
                </div>
                ${account.note ? `
                    <div class="mt-2">
                        <p class="text-xs text-gray-400">${account.note}</p>
                    </div>
                ` : ''}
                <div class="mt-4 flex space-x-2">
                    <a href="{{ route('accounts.index') }}/${account.id}" class="flex-1 text-center bg-blue-50 text-blue-600 px-3 py-2 rounded-md text-sm font-medium hover:bg-blue-100">
                        <i class="fas fa-eye mr-1"></i> Detail
                    </a>
                    ${account.is_owner ? `
                        <a href="{{ route('accounts.index') }}/${account.id}/edit" class="flex-1 text-center bg-green-50 text-green-600 px-3 py-2 rounded-md text-sm font-medium hover:bg-green-100">
                            <i class="fas fa-edit mr-1"></i> Edit
                        </a>
                    ` : `
                        <div class="flex-1 text-center bg-gray-100 text-gray-400 px-3 py-2 rounded-md text-sm font-medium cursor-not-allowed">
                            <i class="fas fa-lock mr-1"></i> Read Only
                        </div>
                    `}
                </div>
            </div>
        `;
        container.appendChild(card);
    });
}

// Display summary
function displaySummary(accounts) {
    const totalBalance = accounts.reduce((sum, account) => sum + parseFloat(account.balance), 0);

    document.getElementById('totalBalance').textContent = window.api.formatCurrency(totalBalance);
    document.getElementById('accountCount').textContent = `${accounts.length} akun`;
}

// Get account icon data based on type
function getAccountIconData(type) {
    switch(type) {
        case 'cash':
            return {
                icon: 'fa-money-bill-wave',
                color: 'text-green-600',
                bgColor: 'bg-green-100'
            };
        case 'bank':
            return {
                icon: 'fa-university',
                color: 'text-blue-600',
                bgColor: 'bg-blue-100'
            };
        case 'paylater':
            return {
                icon: 'fa-credit-card',
                color: 'text-orange-600',
                bgColor: 'bg-orange-100'
            };
        default:
            return {
                icon: 'fa-wallet',
                color: 'text-gray-600',
                bgColor: 'bg-gray-100'
            };
    }
}
</script>
@endpush