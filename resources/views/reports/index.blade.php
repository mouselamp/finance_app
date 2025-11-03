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
                        <dt class="text-sm font-medium text-gray-500 truncate">Saldo Bersih</dt>
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
@endsection

@push('scripts')
<script>
// Income vs Expense Chart
const incomeExpenseCtx = document.getElementById('incomeExpenseChart').getContext('2d');
new Chart(incomeExpenseCtx, {
    type: 'bar',
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
        datasets: [{
            label: 'Pemasukan',
            data: [0, 0, 0, 0, 0, 0],
            backgroundColor: 'rgba(34, 197, 94, 0.8)',
            borderColor: 'rgba(34, 197, 94, 1)',
            borderWidth: 1
        }, {
            label: 'Pengeluaran',
            data: [0, 0, 0, 0, 0, 0],
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
                        return 'Rp ' + value.toLocaleString('id-ID');
                    }
                }
            }
        }
    }
});

// Category Distribution Chart
const categoryCtx = document.getElementById('categoryChart').getContext('2d');
new Chart(categoryCtx, {
    type: 'doughnut',
    data: {
        labels: ['Makanan', 'Transportasi', 'Tagihan', 'Belanja', 'Lainnya'],
        datasets: [{
            data: [0, 0, 0, 0, 0],
            backgroundColor: [
                'rgba(59, 130, 246, 0.8)',
                'rgba(16, 185, 129, 0.8)',
                'rgba(251, 146, 60, 0.8)',
                'rgba(147, 51, 234, 0.8)',
                'rgba(107, 114, 128, 0.8)'
            ],
            borderColor: [
                'rgba(59, 130, 246, 1)',
                'rgba(16, 185, 129, 1)',
                'rgba(251, 146, 60, 1)',
                'rgba(147, 51, 234, 1)',
                'rgba(107, 114, 128, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});
</script>
@endpush