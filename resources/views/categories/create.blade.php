@extends('layouts.finance')

@section('title', 'Tambah Kategori')
@section('page-title', 'Tambah Kategori Baru')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-blue-100 rounded-full p-2 mr-3">
                        <i class="fas fa-tags text-blue-600"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900">Tambah Kategori Baru</h3>
                </div>
                <a href="{{ route('categories.index') }}" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </a>
            </div>
        </div>

        <form id="categoryForm" class="p-6 space-y-6">
            <!-- CSRF Token -->
            <input type="hidden" name="_token" value="{{ csrf_token() }}">

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
                    <label class="relative flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors"
                           x-data="{ selected: false }"
                           x-init="$watch('selected', val => val && document.querySelectorAll('input[name=type]').forEach(el => el.checked = false); $el.querySelector('input').checked = true)">
                        <input type="radio"
                               name="type"
                               value="income"
                               class="sr-only"
                               @change="$el.closest('label').classList.toggle('border-green-500', $el.checked); $el.closest('label').classList.toggle('bg-green-50', $el.checked)">
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

                    <label class="relative flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors"
                           x-data="{ selected: false }"
                           x-init="$watch('selected', val => val && document.querySelectorAll('input[name=type]').forEach(el => el.checked = false); $el.querySelector('input').checked = true)">
                        <input type="radio"
                               name="type"
                               value="expense"
                               class="sr-only"
                               @change="$el.closest('label').classList.toggle('border-red-500', $el.checked); $el.closest('label').classList.toggle('bg-red-50', $el.checked)">
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

            <!-- Quick Suggestions -->
            <div>
                <p class="text-sm font-medium text-gray-700 mb-3">Saran Kategori Populer</p>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                    <button type="button"
                            onclick="fillCategory('Makanan', 'expense')"
                            class="px-3 py-2 text-sm bg-gray-100 hover:bg-gray-200 rounded-md text-gray-700 transition-colors">
                        <i class="fas fa-utensils mr-1"></i> Makanan
                    </button>
                    <button type="button"
                            onclick="fillCategory('Transportasi', 'expense')"
                            class="px-3 py-2 text-sm bg-gray-100 hover:bg-gray-200 rounded-md text-gray-700 transition-colors">
                        <i class="fas fa-car mr-1"></i> Transportasi
                    </button>
                    <button type="button"
                            onclick="fillCategory('Tagihan', 'expense')"
                            class="px-3 py-2 text-sm bg-gray-100 hover:bg-gray-200 rounded-md text-gray-700 transition-colors">
                        <i class="fas fa-file-invoice mr-1"></i> Tagihan
                    </button>
                    <button type="button"
                            onclick="fillCategory('Belanja', 'expense')"
                            class="px-3 py-2 text-sm bg-gray-100 hover:bg-gray-200 rounded-md text-gray-700 transition-colors">
                        <i class="fas fa-shopping-cart mr-1"></i> Belanja
                    </button>
                    <button type="button"
                            onclick="fillCategory('Gaji', 'income')"
                            class="px-3 py-2 text-sm bg-gray-100 hover:bg-gray-200 rounded-md text-gray-700 transition-colors">
                        <i class="fas fa-briefcase mr-1"></i> Gaji
                    </button>
                    <button type="button"
                            onclick="fillCategory('Bonus', 'income')"
                            class="px-3 py-2 text-sm bg-gray-100 hover:bg-gray-200 rounded-md text-gray-700 transition-colors">
                        <i class="fas fa-gift mr-1"></i> Bonus
                    </button>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                <a href="{{ route('categories.index') }}"
                   class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                    Batal
                </a>
                <button type="submit"
                        id="submitBtn"
                        class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors flex items-center">
                    <span id="submitText">Simpan Kategori</span>
                    <i id="submitLoading" class="fas fa-spinner fa-spin ml-2" style="display: none;"></i>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('categoryForm');
    const submitBtn = document.getElementById('submitBtn');
    const submitText = document.getElementById('submitText');
    const submitLoading = document.getElementById('submitLoading');

    // Handle form submission
    form.addEventListener('submit', async function(e) {
        e.preventDefault();

        // Reset error states
        clearErrors();

        // Get form data
        const formData = new FormData(form);
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
            const response = await window.api.createCategory(data);

            if (response.success) {
                window.api.handleApiSuccess(response, 'Kategori berhasil ditambahkan!');

                // Redirect to categories list
                setTimeout(() => {
                    window.location.href = '{{ route('categories.index') }}';
                }, 1000);
            } else {
                // Handle API errors
                if (response.errors) {
                    showFieldErrors(response.errors);
                } else {
                    window.api.handleApiError(response, 'Menambahkan kategori');
                }
            }
        } catch (error) {
            window.api.handleApiError(error, 'Menambahkan kategori');
        } finally {
            setLoading(false);
        }
    });
});

// Fill category from quick suggestions
function fillCategory(name, type) {
    document.getElementById('name').value = name;

    // Set radio button and update styling
    const radioBtn = document.querySelector(`input[name="type"][value="${type}"]`);
    if (radioBtn) {
        radioBtn.checked = true;

        // Update visual styling
        document.querySelectorAll('input[name="type"]').forEach(input => {
            const label = input.closest('label');
            if (input === radioBtn) {
                label.classList.add(type === 'income' ? 'border-green-500' : 'border-red-500');
                label.classList.add(type === 'income' ? 'bg-green-50' : 'bg-red-50');
            } else {
                label.classList.remove('border-green-500', 'border-red-500');
                label.classList.remove('bg-green-50', 'bg-red-50');
            }
        });
    }

    // Clear any previous errors
    clearErrors();
}

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
        submitText.textContent = 'Simpan Kategori';
        submitLoading.style.display = 'none';
    }
}
</script>
@endpush