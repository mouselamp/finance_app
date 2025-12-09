@extends('layouts.finance')

@section('title', 'Tambah Transaksi')
@section('page-title', 'Tambah Transaksi')

@section('content')
<div class="mb-6">
    <div class="flex items-center">
        <a href="{{ route('transactions.index') }}" class="mr-4 text-gray-600 hover:text-gray-900">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h2 class="text-2xl font-bold text-gray-900">
                @if($type === 'income')
                    <i class="fas fa-arrow-down text-green-600 mr-2"></i>Tambah Pemasukan
                @elseif($type === 'expense')
                    <i class="fas fa-arrow-up text-red-600 mr-2"></i>Tambah Pengeluaran
                @elseif($type === 'transfer')
                    <i class="fas fa-exchange-alt text-blue-600 mr-2"></i>Transfer Dana
                @else
                    <i class="fas fa-plus-circle text-blue-600 mr-2"></i>Tambah Transaksi
                @endif
            </h2>
            <p class="mt-1 text-sm text-gray-600">Catat transaksi keuangan Anda</p>
        </div>
    </div>
</div>

<div class="bg-white shadow rounded-lg">
    <form id="transactionForm" action="{{ route('transactions.store') }}" method="POST">
        @csrf
        <div class="px-4 py-5 sm:p-6">
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <!-- Transaction Type -->
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">
                        Jenis Transaksi <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-1 grid grid-cols-3 gap-3">
                        <label class="relative">
                            <input type="radio" name="type" value="income"
                                   @if($type === 'income') checked @endif
                                   class="peer sr-only" required>
                            <div class="w-full px-4 py-3 text-center border rounded-lg cursor-pointer
                                        peer-checked:border-green-500 peer-checked:bg-green-50
                                        hover:bg-gray-50 transition-colors">
                                <i class="fas fa-arrow-down text-green-600 mr-2"></i>
                                <span class="font-medium">Pemasukan</span>
                            </div>
                        </label>
                        <label class="relative">
                            <input type="radio" name="type" value="expense"
                                   @if($type === 'expense') checked @endif
                                   class="peer sr-only">
                            <div class="w-full px-4 py-3 text-center border rounded-lg cursor-pointer
                                        peer-checked:border-red-500 peer-checked:bg-red-50
                                        hover:bg-gray-50 transition-colors">
                                <i class="fas fa-arrow-up text-red-600 mr-2"></i>
                                <span class="font-medium">Pengeluaran</span>
                            </div>
                        </label>
                        <label class="relative">
                            <input type="radio" name="type" value="transfer"
                                   @if($type === 'transfer') checked @endif
                                   class="peer sr-only">
                            <div class="w-full px-4 py-3 text-center border rounded-lg cursor-pointer
                                        peer-checked:border-blue-500 peer-checked:bg-blue-50
                                        hover:bg-gray-50 transition-colors">
                                <i class="fas fa-exchange-alt text-blue-600 mr-2"></i>
                                <span class="font-medium">Transfer</span>
                            </div>
                        </label>
                    </div>
                    @error('type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Amount -->
                <div>
                    <label for="amount" class="block text-sm font-medium text-gray-700">
                        Nominal <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">Rp</span>
                        </div>
                        <input type="text" name="amount" id="amount" required inputmode="numeric"
                               class="pl-12 block w-full border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                               placeholder="0">
                    </div>
                    @error('amount')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Date -->
                <div>
                    <label for="date" class="block text-sm font-medium text-gray-700">
                        Tanggal <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="date" id="date" required
                           value="{{ now()->format('Y-m-d') }}"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    @error('date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Note -->
                <div class="sm:col-span-2">
                    <label for="note" class="block text-sm font-medium text-gray-700">
                        Catatan <span class="text-red-500">*</span>
                    </label>
                    <textarea name="note" id="note" required rows="3"
                           placeholder="Contoh: Gaji bulanan, Belanja makanan, Transfer ke rekening BCA"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"></textarea>
                    @error('note')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Account (From Account for Transfer) -->
                <div>
                    <label for="account_id" class="block text-sm font-medium text-gray-700">
                        @if($type === 'transfer')
                            Dari Akun <span class="text-red-500">*</span>
                        @else
                            Akun <span class="text-red-500">*</span>
                        @endif
                    </label>
                    <select name="account_id" id="account_id" required
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="">Pilih akun...</option>
                        @foreach($accounts as $account)
                            <option value="{{ $account->id }}"
                                    @if(old('account_id') == $account->id) selected @endif>
                                {{ $account->name }} ({{ $account->type_label }}) -
                                Rp {{ number_format($account->balance, 0, ',', '.') }}
                            </option>
                        @endforeach
                    </select>
                    @error('account_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Related Account (for Transfer only) -->
                <div id="related_account_field" class="@if($type !== 'transfer') hidden @endif">
                    <label for="related_account_id" class="block text-sm font-medium text-gray-700">
                        Ke Akun <span class="text-red-500">*</span>
                    </label>
                    <select name="related_account_id" id="related_account_id"
                            @if($type === 'transfer') required @endif
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="">Pilih akun tujuan...</option>
                        @foreach($accounts as $account)
                            <option value="{{ $account->id }}"
                                    @if(old('related_account_id') == $account->id) selected @endif>
                                {{ $account->name }} ({{ $account->type_label }})
                            </option>
                        @endforeach
                    </select>
                    @error('related_account_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Category (not for Transfer) -->
                <div id="category_field" class="@if($type === 'transfer') hidden @endif">
                    <label for="category_id" class="block text-sm font-medium text-gray-700">
                        Kategori
                    </label>
                    <select name="category_id" id="category_id"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="">Pilih kategori...</option>
                        @if($type === 'income')
                            <optgroup label="Pemasukan">
                                @foreach($categories->where('type', 'income') as $category)
                                    <option value="{{ $category->id }}"
                                            @if(old('category_id') == $category->id) selected @endif>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @elseif($type === 'expense')
                            <optgroup label="Pengeluaran">
                                @foreach($categories->where('type', 'expense') as $category)
                                    <option value="{{ $category->id }}"
                                            @if(old('category_id') == $category->id) selected @endif>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endif
                    </select>
                    @error('category_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Paylater Options (only for expense transactions with paylater accounts) -->
                <div id="paylater_field" class="sm:col-span-2 hidden">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-center mb-3">
                            <i class="fas fa-credit-card text-blue-600 mr-2"></i>
                            <h3 class="text-sm font-semibold text-blue-800">Opsi Pembayaran Paylater</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Payment Type -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Jenis Pembayaran <span class="text-red-500">*</span>
                                </label>
                                <select name="payment_type" id="payment_type"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                    <option value="">Pilih jenis pembayaran...</option>
                                    <option value="full">🔹 Bayar Penuh</option>
                                    <option value="installment">🔹 Cicilan</option>
                                </select>
                                <p class="mt-1 text-xs text-gray-500">Pilih cara pembayaran Anda</p>
                            </div>

                            <!-- Installment Period (shown only when installment is selected) -->
                            <div id="installment_period_field" class="hidden">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Jangka Waktu Cicilan <span class="text-red-500">*</span>
                                </label>
                                <select name="installment_period" id="installment_period"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                    <option value="">Pilih jangka waktu...</option>
                                    <option value="3">🔹 3x Cicilan</option>
                                    <option value="6">🔹 6x Cicilan</option>
                                    <option value="12">🔹 12x Cicilan</option>
                                </select>
                                <p class="mt-1 text-xs text-gray-500">Pilih periode cicilan yang diinginkan</p>
                            </div>
                        </div>

                        <!-- Installment Calculation Display (shown only when installment is selected) -->
                        <div id="installment_calculation" class="hidden mt-4 p-3 bg-white rounded-md border border-gray-200">
                            <div class="text-sm">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-gray-600">Total Transaksi:</span>
                                    <span class="font-semibold" id="total_display">Rp 0</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Cicilan per bulan:</span>
                                    <span class="font-semibold text-blue-600" id="monthly_display">Rp 0</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Catatan field sudah dihapus karena schema hanya memiliki satu field 'note' -->
            </div>
        </div>

        <div class="px-4 py-3 bg-gray-50 text-right sm:px-6 sm:flex sm:justify-between">
            <a href="{{ route('transactions.index') }}"
               class="inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Batal
            </a>
            <button type="submit"
                    class="mt-3 sm:mt-0 inline-flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <i class="fas fa-save mr-2"></i>Simpan Transaksi
            </button>
        </div>
    </form>
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
    // Load accounts and categories on page load
    loadAccounts();
    loadCategories();

    // Handle transaction type change
    document.querySelectorAll('input[name="type"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const type = this.value;
            const relatedAccountField = document.getElementById('related_account_field');
            const categoryField = document.getElementById('category_field');
            const relatedAccountSelect = document.getElementById('related_account_id');

            if (type === 'transfer') {
                relatedAccountField.classList.remove('hidden');
                relatedAccountSelect.required = true;
                categoryField.classList.add('hidden');
            } else {
                relatedAccountField.classList.add('hidden');
                relatedAccountSelect.required = false;
                categoryField.classList.remove('hidden');

                // Reload categories for selected type
                loadCategories(type);
            }
        });
    });

    // Handle account selection to show/hide paylater options
    document.getElementById('account_id').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const accountText = selectedOption.textContent.toLowerCase();
        const accountType = accountText.includes('paylater');
        const paylaterField = document.getElementById('paylater_field');
        const expenseType = document.querySelector('input[name="type"][value="expense"]').checked;

        // Debug logging
        console.log('Account selection changed:');
        console.log('- Account text:', selectedOption.textContent);
        console.log('- Account text (lowercase):', accountText);
        console.log('- Is paylater account:', accountType);
        console.log('- Is expense type:', expenseType);
        console.log('- Show paylater field:', accountType && expenseType);

        // Show paylater field only for expense transactions with paylater accounts
        if (accountType && expenseType) {
            paylaterField.classList.remove('hidden');
            resetPaylaterFields();
            // Set payment_type as required for paylater accounts
            document.getElementById('payment_type').setAttribute('required', 'required');
        } else {
            paylaterField.classList.add('hidden');
            resetPaylaterFields();
            // Remove required attribute when not paylater account
            document.getElementById('payment_type').removeAttribute('required');
        }
    });

    // Handle payment type change
    document.getElementById('payment_type').addEventListener('change', function() {
        const installmentField = document.getElementById('installment_period_field');
        const calculationField = document.getElementById('installment_calculation');
        const installmentPeriod = document.getElementById('installment_period');

        if (this.value === 'installment') {
            installmentField.classList.remove('hidden');
            calculationField.classList.remove('hidden');
            installmentPeriod.required = true;
            updateInstallmentCalculation();
        } else {
            installmentField.classList.add('hidden');
            calculationField.classList.add('hidden');
            installmentPeriod.required = false;
            installmentPeriod.value = '';
        }
    });

    // Handle installment period change
    document.getElementById('installment_period').addEventListener('change', function() {
        updateInstallmentCalculation();
    });

    // Handle amount change to recalculate installment
    document.getElementById('amount').addEventListener('input', function() {
        updateInstallmentCalculation();
    });

    // Reset paylater fields
    function resetPaylaterFields() {
        document.getElementById('payment_type').value = '';
        document.getElementById('installment_period').value = '';
        document.getElementById('installment_period').required = false;
        document.getElementById('installment_period_field').classList.add('hidden');
        document.getElementById('installment_calculation').classList.add('hidden');
        document.getElementById('total_display').textContent = 'Rp 0';
        document.getElementById('monthly_display').textContent = 'Rp 0';
    }

    // Update installment calculation
    function updateInstallmentCalculation() {
        const amount = getRawAmount();
        const period = parseInt(document.getElementById('installment_period').value) || 0;

        if (amount > 0 && period > 0) {
            const monthlyAmount = amount / period;
            document.getElementById('total_display').textContent = formatCurrency(amount);
            document.getElementById('monthly_display').textContent = formatCurrency(monthlyAmount);
        } else {
            document.getElementById('total_display').textContent = 'Rp 0';
            document.getElementById('monthly_display').textContent = 'Rp 0';
        }
    }

    // Format currency helper
    function formatCurrency(amount) {
        return 'Rp ' + amount.toLocaleString('id-ID', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        });
    }

    // Handle form submission
    document.getElementById('transactionForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        // Prevent double submission using LoadingOverlay
        if (window.LoadingOverlay.isSubmitting()) {
            console.log('Submission blocked - already in progress');
            return;
        }

        const submitBtn = document.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;

        // Show loading overlay
        window.LoadingOverlay.show('Menyimpan transaksi...');

        try {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...';

            const formData = new FormData(this);
            const data = Object.fromEntries(formData.entries());

            // Remove empty fields that should be null
            if (!data.category_id || data.category_id === '') {
                delete data.category_id;
            }

            // Remove related_account_id for non-transfer transactions
            if (data.type !== 'transfer') {
                delete data.related_account_id;
            }

            // Convert amount to number (clean formatted string)
            data.amount = getRawAmount();

            // Handle paylater data
            const paylaterField = document.getElementById('paylater_field');
            if (!paylaterField.classList.contains('hidden')) {
                // This is a paylater transaction
                const paymentType = document.getElementById('payment_type').value;

                if (!paymentType) {
                    alert('Silakan pilih jenis pembayaran untuk transaksi paylater.');
                    return;
                }

                if (paymentType === 'installment') {
                    const installmentPeriod = document.getElementById('installment_period').value;
                    if (!installmentPeriod) {
                        alert('Silakan pilih jangka waktu cicilan.');
                        return;
                    }
                    data.installment_period = parseInt(installmentPeriod);
                    data.monthly_amount = data.amount / data.installment_period;
                } else if (paymentType === 'full') {
                    data.installment_period = 1;
                    data.monthly_amount = data.amount;
                }

                data.payment_type = paymentType;
            }

            const response = await axios.post('{{ route("api.transactions.store") }}', data);

            if (response.data.success) {
                console.log('Accounts API Response:', response.data.data);
                handleApiSuccess(response, 'Transaksi berhasil disimpan!');
                setTimeout(() => {
                    window.location.href = '{{ route('transactions.index') }}';
                }, 1500);
            }
        } catch (error) {
            handleApiError(error, 'Menyimpan transaksi');
            window.LoadingOverlay.hide(); // Hide overlay on error to allow retry
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    });

    // Format amount input
    const amountInput = document.getElementById('amount');

    amountInput.addEventListener('input', function(e) {
        // Remove non-digit characters
        let value = this.value.replace(/\D/g, '');

        // Convert to number and format
        if (value !== '') {
            this.value = new Intl.NumberFormat('id-ID').format(value);
        } else {
            this.value = '';
        }

        // Update calculation if paylater installment is active
        updateInstallmentCalculation();
    });

    // Helper to get raw numeric value from formatted input
    function getRawAmount() {
        const val = amountInput.value.replace(/\./g, ''); // Remove thousand separators
        return parseFloat(val) || 0;
    }
});

// Load accounts via API (own_only=1 to exclude group members' accounts)
async function loadAccounts() {
    try {
        const response = await axios.get('{{ route("api.accounts.index") }}?own_only=1');
        if (response.data.success) {
                console.log('Accounts API Response:', response.data.data);
            const accountSelect = document.getElementById('account_id');
            const relatedAccountSelect = document.getElementById('related_account_id');

            // Clear existing options
            accountSelect.innerHTML = '<option value="">Pilih akun...</option>';
            relatedAccountSelect.innerHTML = '<option value="">Pilih akun tujuan...</option>';

            // Add account options
            response.data.data.accounts.forEach(account => {
                const option1 = new Option(
                    `${account.name} (${account.type_label}) - ${formatCurrency(account.balance)}`,
                    account.id
                );
                const option2 = new Option(
                    `${account.name} (${account.type_label})`,
                    account.id
                );
                accountSelect.add(option1);
                relatedAccountSelect.add(option2);
            });
        }
    } catch (error) {
        handleApiError(error, 'Memuat data akun');
    }
}

// Load categories via API
async function loadCategories(type = null) {
    try {
        let url = '{{ route("api.categories.index") }}';
        if (type) {
            url += '?type=' + type;
        }

        const response = await axios.get(url);
        if (response.data.success) {
                console.log('Accounts API Response:', response.data.data);
            const categorySelect = document.getElementById('category_id');

            // Clear existing options
            categorySelect.innerHTML = '<option value="">Pilih kategori...</option>';

            // Group categories by type
            const grouped = response.data.data.grouped;

            if (type === 'income' && grouped.income) {
                const incomeGroup = document.createElement('optgroup');
                incomeGroup.label = 'Pemasukan';
                grouped.income.forEach(category => {
                    const option = new Option(category.name, category.id);
                    incomeGroup.appendChild(option);
                });
                categorySelect.appendChild(incomeGroup);
            } else if (type === 'expense' && grouped.expense) {
                const expenseGroup = document.createElement('optgroup');
                expenseGroup.label = 'Pengeluaran';
                grouped.expense.forEach(category => {
                    const option = new Option(category.name, category.id);
                    expenseGroup.appendChild(option);
                });
                categorySelect.appendChild(expenseGroup);
            } else {
                // Show all categories if no type specified
                if (grouped.income) {
                    const incomeGroup = document.createElement('optgroup');
                    incomeGroup.label = 'Pemasukan';
                    grouped.income.forEach(category => {
                        const option = new Option(category.name, category.id);
                        incomeGroup.appendChild(option);
                    });
                    categorySelect.appendChild(incomeGroup);
                }

                if (grouped.expense) {
                    const expenseGroup = document.createElement('optgroup');
                    expenseGroup.label = 'Pengeluaran';
                    grouped.expense.forEach(category => {
                        const option = new Option(category.name, category.id);
                        expenseGroup.appendChild(option);
                    });
                    categorySelect.appendChild(expenseGroup);
                }
            }
        }
    } catch (error) {
        handleApiError(error, 'Memuat data kategori');
    }
}
</script>
@endpush