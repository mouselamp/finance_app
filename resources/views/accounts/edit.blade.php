@extends('layouts.finance')

@section('title', 'Edit Akun')
@section('page-title', 'Edit Akun')

@section('content')
<div class="mb-6">
    <div class="flex items-center">
        <a href="{{ route('accounts.index') }}" class="mr-4 text-gray-600 hover:text-gray-900">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h2 class="text-2xl font-bold text-gray-900">
                <i class="fas fa-edit text-blue-600 mr-2"></i>Edit Akun
            </h2>
            <p class="mt-1 text-sm text-gray-600">Ubah informasi akun {{ $account->name }}</p>
        </div>
    </div>
</div>

<div class="bg-white shadow rounded-lg">
    <form id="accountForm" method="POST">
        @csrf
        @method('PUT')
        <div class="px-4 py-5 sm:p-6">
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <!-- Account Name -->
                <div class="sm:col-span-2">
                    <label for="name" class="block text-sm font-medium text-gray-700">
                        Nama Akun <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" required maxlength="255"
                           value="{{ $account->name }}"
                           placeholder="Contoh: BCA, Mandiri, Cash, Gopay, Shopee PayLater"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <p class="mt-1 text-sm text-red-600 hidden" id="nameError"></p>
                </div>

                <!-- Account Type (Read-only) -->
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">
                        Jenis Akun
                    </label>
                    <div class="mt-1 grid grid-cols-2 md:grid-cols-3 gap-3">
                        <div class="w-full px-3 py-4 text-center border rounded-lg
                                    {{ $account->type === 'cash' ? 'border-green-500 bg-green-50' : 'bg-gray-100' }}">
                            <div class="mb-2">
                                <i class="fas fa-money-bill-wave text-green-600 text-xl"></i>
                            </div>
                            <span class="text-xs font-medium">Cash</span>
                        </div>
                        <div class="w-full px-3 py-4 text-center border rounded-lg
                                    {{ $account->type === 'bank' ? 'border-blue-500 bg-blue-50' : 'bg-gray-100' }}">
                            <div class="mb-2">
                                <i class="fas fa-university text-blue-600 text-xl"></i>
                            </div>
                            <span class="text-xs font-medium">Bank</span>
                        </div>
                        <div class="w-full px-3 py-4 text-center border rounded-lg
                                    {{ $account->type === 'paylater' ? 'border-orange-500 bg-orange-50' : 'bg-gray-100' }}">
                            <div class="mb-2">
                                <i class="fas fa-credit-card text-orange-600 text-xl"></i>
                            </div>
                            <span class="text-xs font-medium">PayLater</span>
                        </div>
                    </div>
                    <p class="mt-2 text-xs text-gray-500">
                        <i class="fas fa-info-circle mr-1"></i>Jenis akun tidak dapat diubah setelah dibuat
                    </p>
                </div>

                <!-- Balance (Read-only) -->
                <div>
                    <label for="balance" class="block text-sm font-medium text-gray-700">
                        Saldo Saat Ini
                    </label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">Rp</span>
                        </div>
                        <input type="text" id="balance" readonly disabled
                               value="{{ number_format($account->balance, 0, ',', '.') }}"
                               class="pl-12 block w-full border-gray-300 rounded-md shadow-sm bg-gray-100 text-gray-500 cursor-not-allowed sm:text-sm">
                    </div>
                    <p class="mt-2 text-xs text-gray-500">
                        <i class="fas fa-lock mr-1"></i>Saldo hanya dapat diubah melalui transaksi
                    </p>
                </div>

                <!-- Note -->
                <div>
                    <label for="note" class="block text-sm font-medium text-gray-700">
                        Catatan (Opsional)
                    </label>
                    <input type="text" name="note" id="note" maxlength="255"
                           value="{{ $account->note }}"
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
                <i class="fas fa-save mr-2"></i>Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
// Helper functions
function showAlert(type, message) {
    const existingAlerts = document.querySelectorAll('.api-alert');
    existingAlerts.forEach(alert => alert.remove());

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

document.addEventListener('DOMContentLoaded', function() {
    // Handle form submission
    document.getElementById('accountForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        // Prevent double submission
        if (window.LoadingOverlay && window.LoadingOverlay.isSubmitting()) {
            console.log('Submission blocked - already in progress');
            return;
        }

        const submitBtn = document.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;

        // Show loading overlay
        if (window.LoadingOverlay) {
            window.LoadingOverlay.show('Menyimpan perubahan...');
        }

        // Clear previous errors
        clearErrors();

        try {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...';

            const formData = new FormData(this);
            const data = Object.fromEntries(formData.entries());

            const response = await axios.put('{{ route("api.accounts.update", $account->id) }}', data);

            if (response.data.success) {
                showAlert('success', 'Akun berhasil diperbarui!');
                setTimeout(() => {
                    window.location.href = '{{ route('accounts.index') }}';
                }, 1500);
            }
        } catch (error) {
            if (window.LoadingOverlay) {
                window.LoadingOverlay.hide();
            }
            handleApiErrors(error);
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    });
});

// Clear all error messages
function clearErrors() {
    document.getElementById('nameError').classList.add('hidden');
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
