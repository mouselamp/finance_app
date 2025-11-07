<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ mobileMenuOpen: false }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- API Token (if user is authenticated) -->
    @auth
    <meta name="api-token" content="{{ auth()->user()->api_token ?? '' }}">
    @endauth

    <title>{{ config('app.name', 'Finance App') }} - @yield('title', 'Personal Finance Manager')</title>

    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#2563eb">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="FinanceApp">

    <!-- PWA Manifest -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">

    <!-- Favicons -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('icons/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('icons/favicon-96x96.png') }}">
    <link rel="icon" href="{{ asset('icons/favicon.ico') }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('icons/favicon.svg') }}">
    <meta name="msapplication-TileColor" content="#2563eb">
    <meta name="theme-color" content="#2563eb">

    <!-- Compiled CSS (Tailwind + PWA Styles) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Chart.js from CDN (keep for flexibility) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Font Awesome Icons (CDN for now) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @stack('styles')
</head>
<body class="bg-gray-50">
    <div id="app" class="min-h-screen flex">
        <!-- Include Sidebar -->
        @include('components.sidebar')

        <!-- Main Content Area -->
        <div class="flex-1 lg:ml-0">
            <!-- Top Navigation Bar -->
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between items-center h-16">
                        <!-- Left: Mobile menu button (only visible when sidebar is not shown) -->
                        <div class="flex items-center">
                            <!-- Mobile menu button (hidden when sidebar is visible on desktop) -->
                            <button @click="window.dispatchEvent(new Event('toggle-sidebar'))"
                                    class="lg:hidden p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-primary">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                                </svg>
                            </button>

                            <!-- Page Title -->
                            <div class="ml-4 lg:ml-0">
                                <h1 class="text-xl font-semibold text-gray-900">@yield('page-title', 'Dashboard')</h1>
                            </div>
                        </div>

                        <!-- Right: User menu and notifications -->
                        <div class="flex items-center space-x-4">
                            <!-- Notifications -->
                            <button class="p-2 rounded-full text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                </svg>
                            </button>

                            <!-- User dropdown -->
                            <div class="relative" x-data="{ dropdownOpen: false }">
                                <button @click="dropdownOpen = !dropdownOpen"
                                        class="flex items-center space-x-3 text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                                    <div class="h-8 w-8 rounded-full bg-primary flex items-center justify-center text-white font-semibold">
                                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                    </div>
                                    <span class="hidden md:block text-gray-700 font-medium">{{ Auth::user()->name }}</span>
                                    <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                    </svg>
                                </button>

                                <!-- Dropdown menu -->
                                <div x-show="dropdownOpen"
                                     @click.away="dropdownOpen = false"
                                     x-transition:enter="transition ease-out duration-100"
                                     x-transition:enter-start="transform opacity-0 scale-95"
                                     x-transition:enter-end="transform opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-75"
                                     x-transition:leave-start="transform opacity-100 scale-100"
                                     x-transition:leave-end="transform opacity-0 scale-95"
                                     class="absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50"
                                     style="display: none;">
                                    <div class="py-1">
                                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <i class="fas fa-user mr-2"></i> Profil Saya
                                        </a>
                                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <i class="fas fa-cog mr-2"></i> Pengaturan
                                        </a>
                                        <hr class="my-1">
                                        <form action="{{ route('logout') }}" method="POST">
                                            @csrf
                                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                                <i class="fas fa-sign-out-alt mr-2"></i> Keluar
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="p-4 sm:p-6 lg:p-8 flex-1">
                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="mb-4 bg-green-50 border-l-4 border-green-400 p-4 rounded">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-check-circle text-green-400"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-green-700">{{ session('success') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4 bg-red-50 border-l-4 border-red-400 p-4 rounded">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-circle text-red-400"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-red-700">{{ session('error') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Main Content -->
                @yield('content')
            </main>
        </div>
    </div>

    <!-- PWA Install Prompt -->
    <div id="pwaInstallPrompt" class="pwa-install-prompt">
        <i class="fas fa-download"></i>
        <span>Install aplikasi ini di perangkat Anda</span>
        <button id="installBtn" class="install-btn">Install</button>
        <button id="dismissBtn" class="dismiss-btn">&times;</button>
    </div>

    <!-- Alpine.js Event Listener for Sidebar Toggle -->
    <script>
        // Setup axios default headers with API token
        document.addEventListener('DOMContentLoaded', function() {
            const apiToken = document.querySelector('meta[name="api-token"]');
            console.log('API Token meta tag:', apiToken);
            if (apiToken && apiToken.content) {
                console.log('Setting up axios with token:', apiToken.content.substring(0, 20) + '...');
                window.axios.defaults.headers.common['Authorization'] = 'Bearer ' + apiToken.content;
                console.log('Axios headers after setup:', window.axios.defaults.headers.common);
            } else {
                console.log('No API token found - user not logged in');
            }
        });

        document.addEventListener('toggle-sidebar', function() {
            // Find the sidebar element and toggle it
            const sidebarComponent = document.querySelector('[x-data*="sidebarOpen"]');
            if (sidebarComponent && sidebarComponent._x_dataStack) {
                const data = sidebarComponent._x_dataStack[0];
                if (data && data.sidebarOpen !== undefined) {
                    data.sidebarOpen = !data.sidebarOpen;
                }
            }
        });
    </script>



    @stack('scripts')
</body>
</html>