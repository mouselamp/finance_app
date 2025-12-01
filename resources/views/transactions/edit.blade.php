@extends('layouts.finance')

@section('title', 'Edit Transaksi')
@section('page-title', 'Edit Transaksi')

@section('content')
<div class="mb-6">
    <div class="flex items-center">
        <a href="{{ route('transactions.index') }}" class="mr-4 text-gray-600 hover:text-gray-900">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h2 class="text-2xl font-bold text-gray-900">
                @if($transaction->type === 'income')
                    <i class="fas fa-arrow-down text-green-600 mr-2"></i>Edit Pemasukan
                @elseif($transaction->type === 'expense')
                    <i class="fas fa-arrow-up text-red-600 mr-2"></i>Edit Pengeluaran
                @elseif($transaction->type === 'transfer')
                    <i class="fas fa-exchange-alt text-blue-600 mr-2"></i>Edit Transfer
                @else
                    <i class="fas fa-edit text-blue-600 mr-2"></i>Edit Transaksi
                @endif
            </h2>
            <p class="mt-1 text-sm text-gray-600">Perbarui data transaksi</p>
        </div>
    </div>
</div>

<div class="bg-white shadow rounded-lg">
    <form id="transactionForm" action="{{ route('transactions.update', $transaction->id) }}" method="POST">
        @csrf
        @method('PUT')
        
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
                                   @if($transaction->type === 'income') checked @endif
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
                                   @if($transaction->type === 'expense') checked @endif
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
                                   @if($transaction->type === 'transfer') checked @endif
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
                        <input type="number" name="amount" id="amount" required step="0.01" min="0"
                               value="{{ old('amount', $transaction->amount) }}"
                               class="pl-12 block w-full border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
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
                           value="{{ old('date', $transaction->date->format('Y-m-d')) }}"
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
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">{{ old('note', $transaction->note) }}</textarea>
                    @error('note')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Account (From Account for Transfer) -->
                <div>
                    <label for="account_id" class="block text-sm font-medium text-gray-700">
                        @if($transaction->type === 'transfer')
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
                                    @if(old('account_id', $transaction->account_id) == $account->id) selected @endif>
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
                <div id="related_account_field" class="@if($transaction->type !== 'transfer') hidden @endif">
                    <label for="related_account_id" class="block text-sm font-medium text-gray-700">
                        Ke Akun <span class="text-red-500">*</span>
                    </label>
                    <select name="related_account_id" id="related_account_id"
                            @if($transaction->type === 'transfer') required @endif
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="">Pilih akun tujuan...</option>
                        @foreach($accounts as $account)
                            <option value="{{ $account->id }}"
                                    @if(old('related_account_id', $transaction->related_account_id) == $account->id) selected @endif>
                                {{ $account->name }} ({{ $account->type_label }})
                            </option>
                        @endforeach
                    </select>
                    @error('related_account_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Category (not for Transfer) -->
                <div id="category_field" class="@if($transaction->type === 'transfer') hidden @endif">
                    <label for="category_id" class="block text-sm font-medium text-gray-700">
                        Kategori
                    </label>
                    <select name="category_id" id="category_id"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="">Pilih kategori...</option>
                        <!-- Options will be populated by JS -->
                    </select>
                    @error('category_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

            </div>
        </div>

        <div class="px-4 py-3 bg-gray-50 text-right sm:px-6 sm:flex sm:justify-between">
            <div class="flex items-center">
                 <button type="button" onclick="deleteTransaction()" 
                        class="text-red-600 hover:text-red-900 text-sm font-medium focus:outline-none">
                    <i class="fas fa-trash-alt mr-1"></i> Hapus Transaksi
                </button>
            </div>
            <div>
                <a href="{{ route('transactions.index') }}"
                   class="inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Batal
                </a>
                <button type="submit"
                        class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-save mr-2"></i>Simpan Perubahan
                </button>
            </div>
        </div>
    </form>
    
    <!-- Delete Form -->
    <form id="deleteForm" action="{{ route('transactions.destroy', $transaction->id) }}" method="POST" class="hidden">
        @csrf
        @method('DELETE')
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
    // Implementation same as create.blade.php
    const existingAlerts = document.querySelectorAll('.api-alert');
    existingAlerts.forEach(alert => alert.remove());

    const alertDiv = document.createElement('div');
    alertDiv.className = `api-alert mb-4 p-4 rounded-lg ${
        type === 'success' ? 'bg-green-100 border border-green-400 text-green-700' :
        type === 'error' ? 'bg-red-100 border border-red-400 text-red-700' :
        'bg-blue-100 border border-blue-400 text-blue-700'
    }`;
    alertDiv.innerHTML = `
        <div class="flex items-center">
            <div class="flex-shrink-0">
                ${type === 'success' ? '<i class="fas fa-check-circle"></i>' :
                  type === 'error' ? '<i class="fas fa-exclamation-circle"></i>' :
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

    const container = document.querySelector('.container') || document.body;
    container.insertBefore(alertDiv, container.firstChild);

    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}

function handleApiError(error, context = 'Operation') {
    console.error(`${context} error:`, error);
    let message = 'An error occurred';
    if (error.response && error.response.data && error.response.data.message) {
        message = error.response.data.message;
    }
    showAlert('error', message);
}

document.addEventListener('DOMContentLoaded', function() {
    // Initial load
    loadCategories('{{ $transaction->type }}', '{{ $transaction->category_id }}');

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

    // Handle form submission
    document.getElementById('transactionForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        if (!confirm('Apakah Anda yakin ingin menyimpan perubahan transaksi ini?')) {
            return;
        }

        const submitBtn = document.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;

        try {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...';

            const formData = new FormData(this);
            const data = Object.fromEntries(formData.entries());

            // Clean data
            if (!data.category_id || data.category_id === '') delete data.category_id;
            if (data.type !== 'transfer') delete data.related_account_id;
            data.amount = parseFloat(data.amount);

            const response = await axios.put('{{ route("api.transactions.update", $transaction->id) }}', data);

            if (response.data.success) {
                showAlert('success', 'Transaksi berhasil diperbarui!');
                setTimeout(() => {
                    window.location.href = '{{ route('transactions.index') }}';
                }, 1500);
            }
        } catch (error) {
            handleApiError(error, 'Memperbarui transaksi');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    });
});

// Load categories via API
async function loadCategories(type = null, selectedId = null) {
    try {
        let url = '{{ route("api.categories.index") }}';
        if (type) {
            url += '?type=' + type;
        }

        const response = await axios.get(url);
        if (response.data.success) {
            const categorySelect = document.getElementById('category_id');
            
            // Preserve current selection if reloading same type
            const currentSelection = selectedId || categorySelect.value;

            categorySelect.innerHTML = '<option value="">Pilih kategori...</option>';
            const grouped = response.data.data.grouped;

            // Function to add options from list
            const addOptions = (list, label) => {
                if (list && list.length > 0) {
                    const group = document.createElement('optgroup');
                    group.label = label;
                    list.forEach(category => {
                        const option = new Option(category.name, category.id);
                        if (currentSelection == category.id) {
                            option.selected = true;
                        }
                        group.appendChild(option);
                    });
                    categorySelect.appendChild(group);
                }
            };

            if (type === 'income') addOptions(grouped.income, 'Pemasukan');
            else if (type === 'expense') addOptions(grouped.expense, 'Pengeluaran');
            else {
                addOptions(grouped.income, 'Pemasukan');
                addOptions(grouped.expense, 'Pengeluaran');
            }
        }
    } catch (error) {
        handleApiError(error, 'Memuat data kategori');
    }
}

function deleteTransaction() {
    if (confirm('Apakah Anda yakin ingin menghapus transaksi ini? Tindakan ini tidak dapat dibatalkan.')) {
        document.getElementById('deleteForm').submit();
    }
}
</script>
@endpush
