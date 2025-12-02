@extends('layouts.finance')

@section('title', 'Edit Kategori')
@section('page-title', 'Edit Kategori')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-blue-100 rounded-full p-2 mr-3">
                        <i class="fas fa-edit text-blue-600"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900">Edit Kategori</h3>
                </div>
                <a href="{{ route('categories.index') }}" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </a>
            </div>
        </div>

        <!-- Loading state -->
        <div id="loadingState" class="p-6 text-center">
            <i class="fas fa-spinner fa-spin text-gray-400 text-2xl mb-2"></i>
            <p class="text-sm text-gray-500">Memuat data kategori...</p>
        </div>

        <!-- Form (hidden by default) -->
        <form id="categoryForm" class="hidden p-6 space-y-6">
            <!-- CSRF Token -->
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" id="categoryId" name="id">

            <!-- Nama Kategori -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    Nama Kategori <span class="text-red-500">*</span>
                </label>
                <input type="text"
                       id="name"
                       name="name"
                       required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       placeholder="Contoh: Makanan, Transportasi, Gaji">
                <p class="mt-1 text-sm text-gray-500">Nama kategori untuk mengelompokkan transaksi</p>

                <!-- Error message container -->
                <div id="nameError" class="hidden mt-1 text-sm text-red-600"></div>
            </div>

            <!-- Jenis Kategori -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Jenis Kategori <span class="text-red-500">*</span>
                </label>
                <div class="grid grid-cols-2 gap-4">
                    <label id="incomeLabel" class="relative flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                        <input type="radio"
                               name="type"
                               value="income"
                               class="sr-only"
                               @change="updateTypeSelection('income')">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-3">
                                <i class="fas fa-arrow-down text-green-600"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">Pemasukan</p>
                                <p class="text-sm text-gray-500">Uang yang masuk</p>
                            </div>
                        </div>
                    </label>

                    <label id="expenseLabel" class="relative flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                        <input type="radio"
                               name="type"
                               value="expense"
                               class="sr-only"
                               @change="updateTypeSelection('expense')">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center mr-3">
                                <i class="fas fa-arrow-up text-red-600"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">Pengeluaran</p>
                                <p class="text-sm text-gray-500">Uang yang keluar</p>
                            </div>
                        </div>
                    </label>
                </div>

                <!-- Error message container -->
                <div id="typeError" class="hidden mt-1 text-sm text-red-600"></div>
            </div>

            <!-- Category Info -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h4 class="text-sm font-medium text-gray-700 mb-2">Informasi Kategori</h4>
                <div class="space-y-1 text-sm text-gray-600">
                    <p>ID: <span id="displayId" class="font-mono">-</span></p>
                    <p>Dibuat: <span id="displayCreatedAt">-</span></p>
                    <p>Terakhir diubah: <span id="displayUpdatedAt">-</span></p>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-between pt-6 border-t border-gray-200">
                <div>
                    <button type="button"
                            onclick="deleteCategory()"
                            class="px-4 py-2 border border-red-300 rounded-md text-sm font-medium text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                        <i class="fas fa-trash mr-2"></i> Hapus
                    </button>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('categories.index') }}"
                       class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                        Batal
                    </a>
                    <button type="submit"
                            id="submitBtn"
                            class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors flex items-center">
                        <span id="submitText">Simpan Perubahan</span>
                        <i id="submitLoading" class="fas fa-spinner fa-spin ml-2" style="display: none;"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentCategory = null;

document.addEventListener('DOMContentLoaded', function() {
    const categoryId = {{ $category->id }};
    loadCategory(categoryId);

    // Setup form submission
    setupFormHandler();
});

// Load category data
async function loadCategory(categoryId) {
    try {
        const response = await window.api.getCategory(categoryId);

        if (response.success && response.data) {
            currentCategory = response.data;
            populateForm(currentCategory);
            showForm();
        } else {
            showError('Kategori tidak ditemukan');
        }
    } catch (error) {
        window.api.handleApiError(error, 'Memuat data kategori');
    }
}

// Populate form with category data
function populateForm(category) {
    document.getElementById('categoryId').value = category.id;
    document.getElementById('name').value = category.name;

    // Set type radio button
    const radioBtn = document.querySelector(`input[name="type"][value="${category.type}"]`);
    if (radioBtn) {
        radioBtn.checked = true;
        updateTypeSelection(category.type);
    }

    // Update info section
    document.getElementById('displayId').textContent = '#' + category.id;
    document.getElementById('displayCreatedAt').textContent = formatDate(category.created_at);
    document.getElementById('displayUpdatedAt').textContent = formatDate(category.updated_at);
}

// Show form and hide loading
function showForm() {
    document.getElementById('loadingState').classList.add('hidden');
    document.getElementById('categoryForm').classList.remove('hidden');
}

// Show error state
function showError(message) {
    document.getElementById('loadingState').innerHTML = `
        <div class="text-red-500">
            <i class="fas fa-exclamation-triangle text-2xl mb-2"></i>
            <p class="text-sm">${message}</p>
            <a href="{{ route('categories.index') }}" class="mt-4 inline-block text-blue-600 hover:text-blue-800">
                <i class="fas fa-arrow-left mr-1"></i> Kembali ke daftar kategori
            </a>
        </div>
    `;
}

// Setup form handler
function setupFormHandler() {
    const form = document.getElementById('categoryForm');
    const submitBtn = document.getElementById('submitBtn');
    const submitText = document.getElementById('submitText');
    const submitLoading = document.getElementById('submitLoading');

    form.addEventListener('submit', async function(e) {
        e.preventDefault();

        // Reset error states
        clearErrors();

        // Get form data
        const formData = new FormData(form);
        const categoryId = formData.get('id');
        const data = {
            name: formData.get('name').trim(),
            type: formData.get('type')
        };

        // Validate
        if (!validateForm(data)) {
            return;
        }

        // Show loading state
        setLoading(true);

        try {
            const response = await window.api.updateCategory(categoryId, data);

            if (response.success) {
                window.api.handleApiSuccess(response, 'Kategori berhasil diperbarui!');

                // Update current category data
                currentCategory = response.data;

                // Update info section
                document.getElementById('displayUpdatedAt').textContent = formatDate(response.data.updated_at);
            } else {
                // Handle API errors
                if (response.errors) {
                    showFieldErrors(response.errors);
                } else {
                    window.api.handleApiError(response, 'Memperbarui kategori');
                }
            }
        } catch (error) {
            window.api.handleApiError(error, 'Memperbarui kategori');
        } finally {
            setLoading(false);
        }
    });
}

// Update type selection styling
function updateTypeSelection(type) {
    const incomeLabel = document.getElementById('incomeLabel');
    const expenseLabel = document.getElementById('expenseLabel');

    if (type === 'income') {
        incomeLabel.classList.add('border-green-500', 'bg-green-50');
        incomeLabel.classList.remove('border-gray-200');
        expenseLabel.classList.remove('border-red-500', 'bg-red-50');
        expenseLabel.classList.add('border-gray-200');
    } else {
        expenseLabel.classList.add('border-red-500', 'bg-red-50');
        expenseLabel.classList.remove('border-gray-200');
        incomeLabel.classList.remove('border-green-500', 'bg-green-50');
        incomeLabel.classList.add('border-gray-200');
    }
}

// Delete category
window.deleteCategory = async function() {
    if (!currentCategory) return;

    const categoryName = currentCategory.name;
    const confirmMessage = `Yakin ingin menghapus kategori "${categoryName}"?\n\nPeringatan: Kategori yang sudah digunakan dalam transaksi tidak dapat dihapus!`;

    if (confirm(confirmMessage)) {
        try {
            const response = await window.api.deleteCategory(currentCategory.id);

            if (response.success) {
                window.api.handleApiSuccess(response, 'Kategori berhasil dihapus!');

                // Redirect to categories list
                setTimeout(() => {
                    window.location.href = '{{ route('categories.index') }}';
                }, 1000);
            } else {
                window.api.handleApiError(response, 'Menghapus kategori');
            }
        } catch (error) {
            window.api.handleApiError(error, 'Menghapus kategori');
        }
    }
};

// Validate form
function validateForm(data) {
    let isValid = true;

    if (!data.name) {
        showFieldError('name', 'Nama kategori harus diisi');
        isValid = false;
    } else if (data.name.length < 2) {
        showFieldError('name', 'Nama kategori minimal 2 karakter');
        isValid = false;
    }

    if (!data.type) {
        showFieldError('type', 'Jenis kategori harus dipilih');
        isValid = false;
    }

    return isValid;
}

// Show field error
function showFieldError(field, message) {
    const errorElement = document.getElementById(field + 'Error');
    if (errorElement) {
        errorElement.textContent = message;
        errorElement.classList.remove('hidden');
    }

    // Add error styling to input
    const inputElement = document.getElementById(field);
    if (inputElement) {
        inputElement.classList.add('border-red-500');
    }
}

// Show multiple field errors from API
function showFieldErrors(errors) {
    Object.keys(errors).forEach(field => {
        const messages = Array.isArray(errors[field]) ? errors[field] : [errors[field]];
        showFieldError(field, messages[0]);
    });
}

// Clear all errors
function clearErrors() {
    document.querySelectorAll('[id$="Error"]').forEach(element => {
        element.classList.add('hidden');
        element.textContent = '';
    });

    document.querySelectorAll('input, textarea').forEach(element => {
        element.classList.remove('border-red-500');
    });
}

// Set loading state
function setLoading(loading) {
    const submitBtn = document.getElementById('submitBtn');
    const submitText = document.getElementById('submitText');
    const submitLoading = document.getElementById('submitLoading');

    if (loading) {
        submitBtn.disabled = true;
        submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
        submitText.textContent = 'Menyimpan...';
        submitLoading.style.display = 'inline';
    } else {
        submitBtn.disabled = false;
        submitBtn.classList.remove('opacity-75', 'cursor-not-allowed');
        submitText.textContent = 'Simpan Perubahan';
        submitLoading.style.display = 'none';
    }
}

// Format date
function formatDate(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}
</script>
@endpush