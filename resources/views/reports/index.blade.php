@extends('layouts.finance')

@section('title', 'Laporan')
@section('page-title', 'Laporan Keuangan')

@section('content')
<div class="mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Laporan Keuangan</h2>
        <p class="mt-1 text-sm text-gray-600">Analisis dan ringkasan keuangan Anda</p>
    </div>
</div>

<!-- Date Range Filter -->
<div class="bg-white shadow rounded-lg mb-6">
    <div class="px-4 py-5 sm:px-6">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-medium text-gray-900">Filter Periode</h3>
            <div class="flex space-x-3">
                <select class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                    <option>Bulan Ini</option>
                    <option>30 Hari Terakhir</option>
                    <option>Bulan Lalu</option>
                    <option>Tahun Ini</option>
                </select>
                <button class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    <i class="fas fa-filter mr-2"></i> Terapkan
                </button>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Income vs Expense Chart -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Pemasukan vs Pengeluaran</h3>
        </div>
        <div class="p-6">
            <canvas id="incomeExpenseChart" width="400" height="200"></canvas>
        </div>
    </div>

    <!-- Category Distribution -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Distribusi Kategori</h3>
        </div>
        <div class="p-6">
            <canvas id="categoryChart" width="400" height="200"></canvas>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- Summary Cards -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-100 rounded-full p-3">
                    <i class="fas fa-arrow-up text-green-600 text-xl"></i>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Total Pemasukan</dt>
                        <dd class="text-lg font-semibold text-green-600">
                            Rp 0
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-red-100 rounded-full p-3">
                    <i class="fas fa-arrow-down text-red-600 text-xl"></i>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Total Pengeluaran</dt>
                        <dd class="text-lg font-semibold text-red-600">
                            Rp 0
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-blue-100 rounded-full p-3">
                    <i class="fas fa-chart-line text-blue-600 text-xl"></i>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Selisih</dt>
                        <dd class="text-lg font-semibold text-blue-600">
                            Rp 0
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Report Tables -->
<div class="bg-white shadow rounded-lg">
    <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-medium text-gray-900">Detail Laporan</h3>
            <div class="flex space-x-2">
                <button class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    <i class="fas fa-download mr-2"></i> Export Excel
                </button>
                <button class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    <i class="fas fa-file-pdf mr-2"></i> Export PDF
                </button>
            </div>
        </div>
    </div>
    <div class="overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Kategori
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Pemasukan
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Pengeluaran
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Jumlah Transaksi
                    </th>
                    <th scope="col" class="relative px-6 py-3">
                        <span class="sr-only">Detail</span>
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <tr>
                    <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                        <i class="fas fa-chart-bar text-4xl mb-2"></i>
                        <p>Belum ada data laporan</p>
                        <p class="text-sm mt-1">Mulai menambahkan transaksi untuk melihat laporan</p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Transaction Detail Modal -->
<div id="detailModal" class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeDetailModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            Detail Transaksi: <span id="modalCategoryName">Kategori</span>
                        </h3>
                        <div class="mt-4">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200" id="detailTable">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Akun</th>
                                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <!-- Data will be inserted here -->
                                    </tbody>
                                </table>
                            </div>
                            <div id="modalLoading" class="text-center py-4 hidden">
                                <i class="fas fa-spinner fa-spin text-blue-500 text-2xl"></i>
                                <p class="text-sm text-gray-500 mt-2">Memuat data...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" onclick="closeDetailModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Helper function for currency formatting
function formatCurrency(amount) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(amount);
}

// Initialize Charts
let incomeExpenseChart = null;
let categoryChart = null;

document.addEventListener('DOMContentLoaded', function() {
    // Load initial data (This Month)
    loadReportData('this_month');

    // Filter change handler
    const periodSelect = document.querySelector('select');
    const applyBtn = document.querySelector('button.bg-blue-600'); // The "Terapkan" button

    applyBtn.addEventListener('click', function() {
        const periodMap = {
            'Bulan Ini': 'this_month',
            '30 Hari Terakhir': 'last_30_days',
            'Bulan Lalu': 'last_month',
            'Tahun Ini': 'this_year'
        };
        const selectedLabel = periodSelect.value;
        const periodKey = periodMap[selectedLabel] || 'this_month';
        loadReportData(periodKey);
    });
});

async function loadReportData(period) {
    try {
        // Show loading state (optional)
        // ...

        const response = await axios.get(`{{ route('api.reports.index') }}?period=${period}`);
        
        if (response.data.success) {
            const data = response.data.data;
            updateSummary(data.summary);
            updateTrendChart(data.trend);
            updateDistributionChart(data.distribution);
            updateDetailsTable(data.details);
        }
    } catch (error) {
        console.error('Error loading report data:', error);
        // Use the existing showAlert helper if available in layout, otherwise alert
        if (typeof showAlert === 'function') {
            showAlert('error', 'Gagal memuat data laporan');
        } else {
            alert('Gagal memuat data laporan');
        }
    }
}

function updateSummary(summary) {
    // Select all summary cards
    const summaryCards = document.querySelectorAll('.bg-white.overflow-hidden.shadow.rounded-lg .p-5');
    
    if (summaryCards.length >= 3) {
        // We assume the order is fixed based on the HTML structure:
        // 1. Income, 2. Expense, 3. Net Balance
        
        // Income
        summaryCards[0].querySelector('dd').textContent = formatCurrency(summary.total_income);
        
        // Expense
        summaryCards[1].querySelector('dd').textContent = formatCurrency(summary.total_expense);
        
        // Net Balance
        const netEl = summaryCards[2].querySelector('dd');
        netEl.textContent = (summary.net_balance >= 0 ? '+' : '') + formatCurrency(summary.net_balance);
        netEl.className = `text-lg font-semibold ${summary.net_balance >= 0 ? 'text-blue-600' : 'text-red-600'}`;
    }
}

function updateTrendChart(trendData) {
    const ctx = document.getElementById('incomeExpenseChart').getContext('2d');
    
    if (incomeExpenseChart) {
        incomeExpenseChart.destroy();
    }

    incomeExpenseChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: trendData.labels,
            datasets: [{
                label: 'Pemasukan',
                data: trendData.datasets.income,
                backgroundColor: 'rgba(34, 197, 94, 0.8)',
                borderColor: 'rgba(34, 197, 94, 1)',
                borderWidth: 1
            }, {
                label: 'Pengeluaran',
                data: trendData.datasets.expense,
                backgroundColor: 'rgba(239, 68, 68, 0.8)',
                borderColor: 'rgba(239, 68, 68, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            // Shorten large numbers
                            if (value >= 1000000) return 'Rp ' + (value/1000000).toFixed(1) + 'Jt';
                            if (value >= 1000) return 'Rp ' + (value/1000).toFixed(0) + 'rb';
                            return value;
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed.y !== null) {
                                label += formatCurrency(context.parsed.y);
                            }
                            return label;
                        }
                    }
                }
            }
        }
    });
}

function updateDistributionChart(distributionData) {
    const ctx = document.getElementById('categoryChart').getContext('2d');
    
    if (categoryChart) {
        categoryChart.destroy();
    }

    // Prepare data
    const labels = distributionData.map(item => item.category_name);
    const data = distributionData.map(item => item.total);
    const bgColors = distributionData.map((item, index) => {
        // Use category color if available, otherwise fallback palette
        return item.color || getDefaultColor(index);
    });

    categoryChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: bgColors,
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 12
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            let value = context.parsed || 0;
                            let total = context.dataset.data.reduce((a, b) => a + b, 0);
                            let percentage = Math.round((value / total) * 100) + '%';
                            return `${label}: ${formatCurrency(value)} (${percentage})`;
                        }
                    }
                }
            }
        }
    });
}

function updateDetailsTable(detailsData) {
    const tbody = document.querySelector('table tbody');
    tbody.innerHTML = '';

    if (detailsData.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                    <i class="fas fa-chart-bar text-4xl mb-2"></i>
                    <p>Belum ada data laporan untuk periode ini</p>
                </td>
            </tr>
        `;
        return;
    }

    detailsData.forEach(item => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                ${item.category_name}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">
                ${formatCurrency(item.income)}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">
                ${formatCurrency(item.expense)}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                ${item.transaction_count} Transaksi
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <button onclick="openDetailModal('${item.category_id || 'null'}', '${item.category_name}')" class="text-blue-600 hover:text-blue-900">
                    Detail
                </button>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

function getDefaultColor(index) {
    const colors = [
        '#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', 
        '#EC4899', '#6366F1', '#14B8A6', '#F97316', '#64748B'
    ];
    return colors[index % colors.length];
}

// Modal Functions
function openDetailModal(categoryId, categoryName) {
    const modal = document.getElementById('detailModal');
    const title = document.getElementById('modalCategoryName');
    const tbody = document.querySelector('#detailTable tbody');
    const loading = document.getElementById('modalLoading');
    const table = document.getElementById('detailTable');
    
    // Setup initial state
    title.textContent = categoryName;
    tbody.innerHTML = '';
    modal.classList.remove('hidden');
    table.classList.add('hidden');
    loading.classList.remove('hidden');
    
    // Get current period
    const periodSelect = document.querySelector('select');
    const periodMap = {
        'Bulan Ini': 'this_month',
        '30 Hari Terakhir': 'last_30_days',
        'Bulan Lalu': 'last_month',
        'Tahun Ini': 'this_year'
    };
    const periodKey = periodMap[periodSelect.value] || 'this_month';

    // Fetch data
    axios.get(`{{ route('api.reports.details') }}?category_id=${categoryId}&period=${periodKey}`)
        .then(response => {
            if (response.data.success) {
                const transactions = response.data.data;
                
                if (transactions.length === 0) {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                Tidak ada transaksi
                            </td>
                        </tr>
                    `;
                } else {
                    transactions.forEach(trx => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                ${new Date(trx.date).toLocaleDateString('id-ID')}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                ${trx.note}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                ${trx.account?.name || '-'}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium ${trx.type === 'income' ? 'text-green-600' : 'text-red-600'}">
                                ${trx.type === 'income' ? '+' : '-'} ${formatCurrency(trx.amount)}
                            </td>
                        `;
                        tbody.appendChild(tr);
                    });
                }
            }
        })
        .catch(error => {
            console.error('Error fetching details:', error);
            tbody.innerHTML = `
                <tr>
                    <td colspan="4" class="px-6 py-4 text-center text-red-500">
                        Gagal memuat data transaksi
                    </td>
                </tr>
            `;
        })
        .finally(() => {
            loading.classList.add('hidden');
            table.classList.remove('hidden');
        });
}

function closeDetailModal() {
    const modal = document.getElementById('detailModal');
    modal.classList.add('hidden');
}
</script>
@endpush