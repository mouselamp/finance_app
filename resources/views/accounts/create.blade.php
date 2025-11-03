@extends('layouts.finance')

@section('title', 'Tambah Akun')
@section('page-title', 'Tambah Akun Baru')

@section('content')
<div class="mb-6">
    <div class="flex items-center">
        <a href="{{ route('accounts.index') }}" class="mr-4 text-gray-600 hover:text-gray-900">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h2 class="text-2xl font-bold text-gray-900">
                <i class="fas fa-plus-circle text-blue-600 mr-2"></i>Tambah Akun Baru
            </h2>
            <p class="mt-1 text-sm text-gray-600">Tambahkan sumber dana atau rekening baru</p>
        </div>
    </div>
</div>

<div class="bg-white shadow rounded-lg">
    <form id="accountForm" action="{{ route('accounts.store') }}" method="POST">
        @csrf
        <div class="px-4 py-5 sm:p-6">
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <!-- Account Name -->
                <div class="sm:col-span-2">
                    <label for="name" class="block text-sm font-medium text-gray-700">
                        Nama Akun <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" required maxlength="255"
                           placeholder="Contoh: BCA, Mandiri, Cash, Gopay, Shopee PayLater"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <p class="mt-1 text-sm text-red-600 hidden" id="nameError"></p>
                </div>

                <!-- Account Type -->
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">
                        Jenis Akun <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-1 grid grid-cols-2 md:grid-cols-3 gap-3">
                        <label class="relative">
                            <input type="radio" name="type" value="cash" class="peer sr-only" required>
                            <div class="w-full px-3 py-4 text-center border rounded-lg cursor-pointer
                                        peer-checked:border-green-500 peer-checked:bg-green-50
                                        hover:bg-gray-50 transition-colors">
                                <div class="mb-2">
                                    <i class="fas fa-money-bill-wave text-green-600 text-xl"></i>
                                </div>
                                <span class="text-xs font-medium">Cash</span>
                            </div>
                        </label>
                        <label class="relative">
                            <input type="radio" name="type" value="bank" class="peer sr-only" required>
                            <div class="w-full px-3 py-4 text-center border rounded-lg cursor-pointer
                                        peer-checked:border-blue-500 peer-checked:bg-blue-50
                                        hover:bg-gray-50 transition-colors">
                                <div class="mb-2">
                                    <i class="fas fa-university text-blue-600 text-xl"></i>
                                </div>
                                <span class="text-xs font-medium">Bank</span>
                            </div>
                        </label>
                        <label class="relative">
                            <input type="radio" name="type" value="paylater" class="peer sr-only" required>
                            <div class="w-full px-3 py-4 text-center border rounded-lg cursor-pointer
                                        peer-checked:border-orange-500 peer-checked:bg-orange-50
                                        hover:bg-gray-50 transition-colors">
                                <div class="mb-2">
                                    <i class="fas fa-credit-card text-orange-600 text-xl"></i>
                                </div>
                                <span class="text-xs font-medium">PayLater</span>
                            </div>
                        </label>
                    </div>
                    <p class="mt-1 text-sm text-red-600 hidden" id="typeError"></p>
                </div>

                <!-- Balance -->
                <div>
                    <label for="balance" class="block text-sm font-medium text-gray-700">
                        Saldo <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">Rp</span>
                        </div>
                        <input type="number" name="balance" id="balance" required step="0.01" min="0"
                               value="0"
                               class="pl-12 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                    <p class="mt-1 text-sm text-red-600 hidden" id="balanceError"></p>
                </div>

                <!-- Note -->
                <div>
                    <label for="note" class="block text-sm font-medium text-gray-700">
                        Catatan (Opsional)
                    </label>
                    <input type="text" name="note" id="note" maxlength="255"
                           placeholder="Catatan tambahan tentang akun ini"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <p class="mt-1 text-sm text-red-600 hidden" id="noteError"></p>
                </div>
            </div>
        </div>

        <div class="px-4 py-3 bg-gray-50 text-right sm:px-6 sm:flex sm:justify-between">
            <a href="{{ route('accounts.index') }}"
               class="inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Batal
            </a>
            <button type="submit"
                    class="mt-3 sm:mt-0 inline-flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <i class="fas fa-save mr-2"></i>Simpan Akun
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
// Helper functions
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
    // Handle form submission
    document.getElementById('accountForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        const submitBtn = document.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;

        // Clear previous errors
        clearErrors();

        try {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...';

            const formData = new FormData(this);
            const data = Object.fromEntries(formData.entries());

            // Convert balance to number
            data.balance = parseFloat(data.balance);

            const response = await axios.post('{{ route("api.accounts.store") }}', data);

            if (response.data.success) {
                handleApiSuccess(response, 'Akun berhasil disimpan!');
                setTimeout(() => {
                    window.location.href = '{{ route('accounts.index') }}';
                }, 1500);
            }
        } catch (error) {
            handleApiErrors(error);
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    });

    // Format balance input
    document.getElementById('balance').addEventListener('input', function(e) {
        if (e.target.value < 0) {
            e.target.value = 0;
        }
    });
});

// Clear all error messages
function clearErrors() {
    document.getElementById('nameError').classList.add('hidden');
    document.getElementById('typeError').classList.add('hidden');
    document.getElementById('balanceError').classList.add('hidden');
    document.getElementById('noteError').classList.add('hidden');
}

// Handle API errors and display validation messages
function handleApiErrors(error) {
    if (error.response && error.response.status === 422) {
        const errors = error.response.data.errors;

        if (errors.name) {
            document.getElementById('nameError').textContent = errors.name[0];
            document.getElementById('nameError').classList.remove('hidden');
        }
        if (errors.type) {
            document.getElementById('typeError').textContent = errors.type[0];
            document.getElementById('typeError').classList.remove('hidden');
        }
        if (errors.balance) {
            document.getElementById('balanceError').textContent = errors.balance[0];
            document.getElementById('balanceError').classList.remove('hidden');
        }
        if (errors.note) {
            document.getElementById('noteError').textContent = errors.note[0];
            document.getElementById('noteError').classList.remove('hidden');
        }
    } else {
        handleApiError(error, 'Menyimpan akun');
    }
}
</script>
@endpush