@extends('layouts.finance')

@section('title', 'Kategori')
@section('page-title', 'Kategori Transaksi')

@section('content')
<div class="mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Kategori</h2>
            <p class="mt-1 text-sm text-gray-600">Kelola kategori untuk mengelompokkan transaksi Anda</p>
        </div>
        <a href="{{ route('categories.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            <i class="fas fa-plus mr-2"></i> Tambah Kategori
        </a>
    </div>
</div>

<!-- Loading state -->
<div id="loadingState" class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-gray-100 rounded-full p-2 mr-3">
                    <i class="fas fa-spinner fa-spin text-gray-600"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900">Memuat kategori pemasukan...</h3>
            </div>
        </div>
        <div class="p-4">
            <div class="space-y-2">
                <div class="animate-pulse">
                    <div class="h-16 bg-gray-100 rounded-lg"></div>
                </div>
                <div class="animate-pulse">
                    <div class="h-16 bg-gray-100 rounded-lg"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-gray-100 rounded-full p-2 mr-3">
                    <i class="fas fa-spinner fa-spin text-gray-600"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900">Memuat kategori pengeluaran...</h3>
            </div>
        </div>
        <div class="p-4">
            <div class="space-y-2">
                <div class="animate-pulse">
                    <div class="h-16 bg-gray-100 rounded-lg"></div>
                </div>
                <div class="animate-pulse">
                    <div class="h-16 bg-gray-100 rounded-lg"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Category grid (hidden by default) -->
<div id="categoryGrid" class="hidden grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Income Categories -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-100 rounded-full p-2 mr-3">
                        <i class="fas fa-arrow-down text-green-600"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900">Kategori Pemasukan</h3>
                </div>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800" id="incomeCount">0</span>
            </div>
        </div>
        <div class="p-4">
            <div class="space-y-2" id="incomeCategories">
                <!-- Income categories will be inserted here -->
            </div>
            <div id="incomeEmptyState" class="hidden text-center py-8">
                <i class="fas fa-inbox text-gray-400 text-2xl mb-2"></i>
                <p class="text-sm text-gray-500">Belum ada kategori pemasukan</p>
            </div>
        </div>
    </div>

    <!-- Expense Categories -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-red-100 rounded-full p-2 mr-3">
                        <i class="fas fa-arrow-up text-red-600"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900">Kategori Pengeluaran</h3>
                </div>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800" id="expenseCount">0</span>
            </div>
        </div>
        <div class="p-4">
            <div class="space-y-2" id="expenseCategories">
                <!-- Expense categories will be inserted here -->
            </div>
            <div id="expenseEmptyState" class="hidden text-center py-8">
                <i class="fas fa-inbox text-gray-400 text-2xl mb-2"></i>
                <p class="text-sm text-gray-500">Belum ada kategori pengeluaran</p>
            </div>
        </div>
    </div>
</div>

<!-- Empty state (hidden by default) -->
<div id="emptyState" class="hidden text-center py-12">
    <div class="flex-shrink-0 mx-auto bg-gray-200 rounded-full p-6 mb-4">
        <i class="fas fa-tags text-gray-600 text-4xl"></i>
    </div>
    <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada kategori</h3>
    <p class="text-sm text-gray-500 mb-6">Mulai dengan menambahkan kategori pertama Anda</p>
    <a href="{{ route('categories.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
        <i class="fas fa-plus mr-2"></i> Tambah Kategori Pertama
    </a>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    loadCategories();
});

// Load categories via API
async function loadCategories() {
    try {
        document.getElementById('loadingState').classList.remove('hidden');
        document.getElementById('emptyState').classList.add('hidden');
        document.getElementById('categoryGrid').classList.add('hidden');

        const response = await window.api.getCategories();

        if (response.success && response.data.grouped) {
            const grouped = response.data.grouped;
            displayCategories(grouped);
            document.getElementById('categoryGrid').classList.remove('hidden');
        } else {
            document.getElementById('emptyState').classList.remove('hidden');
        }
    } catch (error) {
        window.api.handleApiError(error, 'Memuat data kategori');
    } finally {
        document.getElementById('loadingState').classList.add('hidden');
    }
}

// Display categories
function displayCategories(grouped) {
    // Display income categories
    const incomeContainer = document.getElementById('incomeCategories');
    const incomeEmptyState = document.getElementById('incomeEmptyState');
    const incomeCount = document.getElementById('incomeCount');

    incomeContainer.innerHTML = '';

    if (grouped.income && grouped.income.length > 0) {
        incomeCount.textContent = grouped.income.length;
        grouped.income.forEach(category => {
            incomeContainer.appendChild(createCategoryCard(category, 'income'));
        });
        incomeEmptyState.classList.add('hidden');
    } else {
        incomeCount.textContent = '0';
        incomeEmptyState.classList.remove('hidden');
    }

    // Display expense categories
    const expenseContainer = document.getElementById('expenseCategories');
    const expenseEmptyState = document.getElementById('expenseEmptyState');
    const expenseCount = document.getElementById('expenseCount');

    expenseContainer.innerHTML = '';

    if (grouped.expense && grouped.expense.length > 0) {
        expenseCount.textContent = grouped.expense.length;
        grouped.expense.forEach(category => {
            expenseContainer.appendChild(createCategoryCard(category, 'expense'));
        });
        expenseEmptyState.classList.add('hidden');
    } else {
        expenseCount.textContent = '0';
        expenseEmptyState.classList.remove('hidden');
    }
}

// Create category card element
function createCategoryCard(category, type) {
    const div = document.createElement('div');
    div.className = `flex items-center justify-between p-3 ${type === 'income' ? 'bg-green-50' : 'bg-red-50'} rounded-lg`;

    const iconData = getCategoryIconData(category.name, type);

    div.innerHTML = `
        <div class="flex items-center">
            <div class="w-8 h-8 ${iconData.bgColor} rounded-full flex items-center justify-center mr-3">
                <i class="fas ${iconData.icon} ${iconData.color} text-sm"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-900">${category.name}</p>
                ${category.note ? `<p class="text-xs text-gray-500">${category.note}</p>` : ''}
            </div>
        </div>
        <div class="flex space-x-1">
            <a href="{{ route('categories.index') }}/${category.id}/edit" class="p-1 text-gray-400 hover:text-blue-600">
                <i class="fas fa-edit"></i>
            </a>
            <button onclick="deleteCategory(${category.id}, '${category.name}')" class="p-1 text-gray-400 hover:text-red-600">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;
    return div;
}

// Get category icon data based on name and type
function getCategoryIconData(name, type) {
    const lowerName = name.toLowerCase();

    // Income category icons
    if (type === 'income') {
        if (lowerName.includes('gaji') || lowerName.includes('salary')) {
            return { icon: 'fa-briefcase', color: 'text-green-700', bgColor: 'bg-green-200' };
        } else if (lowerName.includes('bonus') || lowerName.includes('tunjangan')) {
            return { icon: 'fa-gift', color: 'text-green-700', bgColor: 'bg-green-200' };
        } else if (lowerName.includes('usaha') || lowerName.includes('bisnis')) {
            return { icon: 'fa-store', color: 'text-green-700', bgColor: 'bg-green-200' };
        } else if (lowerName.includes('investasi')) {
            return { icon: 'fa-chart-line', color: 'text-green-700', bgColor: 'bg-green-200' };
        }
        return { icon: 'fa-coins', color: 'text-green-700', bgColor: 'bg-green-200' };
    }

    // Expense category icons
    if (lowerName.includes('makan') || lowerName.includes('food')) {
        return { icon: 'fa-utensils', color: 'text-red-700', bgColor: 'bg-red-200' };
    } else if (lowerName.includes('transport') || lowerName.includes('kendaraan')) {
        return { icon: 'fa-car', color: 'text-red-700', bgColor: 'bg-red-200' };
    } else if (lowerName.includes('tagihan') || lowerName.includes('bill')) {
        return { icon: 'fa-file-invoice', color: 'text-red-700', bgColor: 'bg-red-200' };
    } else if (lowerName.includes('belanja') || lowerName.includes('shop')) {
        return { icon: 'fa-shopping-cart', color: 'text-red-700', bgColor: 'bg-red-200' };
    } else if (lowerName.includes('kesehatan') || lowerName.includes('medic')) {
        return { icon: 'fa-heartbeat', color: 'text-red-700', bgColor: 'bg-red-200' };
    } else if (lowerName.includes('hiburan') || lowerName.includes('entertainment')) {
        return { icon: 'fa-gamepad', color: 'text-red-700', bgColor: 'bg-red-200' };
    } else if (lowerName.includes('pendidikan') || lowerName.includes('education')) {
        return { icon: 'fa-graduation-cap', color: 'text-red-700', bgColor: 'bg-red-200' };
    }

    return { icon: 'fa-shopping-bag', color: 'text-red-700', bgColor: 'bg-red-200' };
}

// Delete category
window.deleteCategory = async function(id, name) {
    if (confirm(`Yakin ingin menghapus kategori "${name}"?`)) {
        try {
            const response = await window.api.deleteCategory(id);
            if (response.success) {
                window.api.handleApiSuccess(response, 'Kategori berhasil dihapus!');
                loadCategories(); // Reload categories
            }
        } catch (error) {
            window.api.handleApiError(error, 'Menghapus kategori');
        }
    }
};
</script>
@endpush