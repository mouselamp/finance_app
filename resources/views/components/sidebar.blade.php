<!-- Sidebar Navigation -->
<div x-data="{ sidebarOpen: false }" class="flex bg-gray-100 min-h-screen">
    <!-- Mobile sidebar overlay -->
    <div x-show="sidebarOpen"
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-40 lg:hidden lg:z-auto lg:inset-0">
        <div x-show="sidebarOpen"
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="absolute inset-0 bg-gray-600 opacity-75"
             @click="sidebarOpen = false"></div>
    </div>

    <!-- Sidebar -->
    <div x-show="sidebarOpen"
         x-transition:enter="transition ease-in-out duration-300 transform"
         x-transition:enter-start="-translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in-out duration-300 transform"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="-translate-x-full"
         class="fixed inset-y-0 left-0 z-50 w-64 overflow-y-auto bg-white shadow-xl lg:static lg:inset-0 lg:transform-none lg:translate-x-0 lg:z-auto lg:shadow-none lg:min-h-screen">

        <!-- Logo -->
        <div class="flex items-center justify-between h-16 px-6 bg-blue-600">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <span class="ml-3 text-xl font-semibold text-white">FinanceApp</span>
            </div>
            <!-- Mobile close button -->
            <button @click="sidebarOpen = false" class="lg:hidden">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Navigation -->
        <nav class="px-3 py-4">
            <!-- Main Menu -->
            <div class="mb-6">
                <h3 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Menu Utama</h3>

                <a href="{{ route('dashboard') }}"
                   class="flex items-center px-3 py-2 mb-1 text-sm font-medium rounded-lg {{ request()->routeIs('dashboard') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    Dashboard
                </a>

                <a href="{{ route('transactions.index') }}"
                   class="flex items-center px-3 py-2 mb-1 text-sm font-medium rounded-lg {{ request()->routeIs('transactions*') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    Transaksi
                </a>

                <a href="{{ route('accounts.index') }}"
                   class="flex items-center px-3 py-2 mb-1 text-sm font-medium rounded-lg {{ request()->routeIs('accounts*') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                    </svg>
                    Akun/Sumber Dana
                </a>

                <a href="{{ route('categories.index') }}"
                   class="flex items-center px-3 py-2 mb-1 text-sm font-medium rounded-lg {{ request()->routeIs('categories*') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                    </svg>
                    Kategori
                </a>

                <a href="{{ route('installments.index') }}"
                   class="flex items-center px-3 py-2 mb-1 text-sm font-medium rounded-lg {{ request()->routeIs('installments*') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Cicilan
                </a>

                <a href="{{ route('paylater.index') }}"
                   class="flex items-center px-3 py-2 mb-1 text-sm font-medium rounded-lg {{ request()->routeIs('paylater*') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                    </svg>
                    Paylater
                </a>
            </div>

            <!-- Reports Menu -->
            <div class="mb-6">
                <h3 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Laporan</h3>

                <a href="{{ route('reports.index') }}"
                   class="flex items-center px-3 py-2 mb-1 text-sm font-medium rounded-lg {{ request()->routeIs('reports*') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v1a1 1 0 001 1h4a1 1 0 001-1v-1m3-2V8a2 2 0 00-2-2H8a2 2 0 00-2 2v7m3-2h6"></path>
                    </svg>
                    Laporan Keuangan
                </a>
            </div>

            <!-- Quick Actions -->
            <div>
                <h3 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Aksi Cepat</h3>

                <a href="{{ route('transactions.create.type', ['type' => 'income']) }}"
                   class="flex items-center px-3 py-2 mb-1 text-sm font-medium text-green-600 rounded-lg hover:bg-green-50">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Tambah Pemasukan
                </a>

                <a href="{{ route('transactions.create.type', ['type' => 'expense']) }}"
                   class="flex items-center px-3 py-2 mb-1 text-sm font-medium text-red-600 rounded-lg hover:bg-red-50">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                    </svg>
                    Tambah Pengeluaran
                </a>

                <a href="{{ route('transactions.create.type', ['type' => 'transfer']) }}"
                   class="flex items-center px-3 py-2 mb-1 text-sm font-medium text-blue-600 rounded-lg hover:bg-blue-50">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                    </svg>
                    Transfer Dana
                </a>

                <a href="{{ route('transactions.create.type', ['type' => 'expense']) }}?paylater=true"
                   class="flex items-center px-3 py-2 text-sm font-medium text-purple-600 rounded-lg hover:bg-purple-50">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                    </svg>
                    Paylater
                </a>
            </div>
        </nav>
    </div>

    <!-- Mobile menu button -->
    <button @click="sidebarOpen = !sidebarOpen"
            class="lg:hidden fixed top-4 left-4 z-50 p-2 rounded-md bg-white shadow-md">
        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
        </svg>
    </button>
</div>