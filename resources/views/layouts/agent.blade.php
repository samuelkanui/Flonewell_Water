<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Agent - {{ config('app.name', 'Water Payment System') }}</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Custom CSS -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css">

    <style>
        /* Custom CSS */
        .sidebar-link:hover {
            background-color: #4b5563;
        }

        .active-link {
            background-color: #4b5563 !important;
        }

        .mobile-menu {
            display: none;
        }

        @media (max-width: 768px) {
            .mobile-menu {
                display: block;
            }
            .navbar-links {
                display: none;
                flex-direction: column;
                width: 100%;
            }
            .navbar-links.active {
                display: flex;
            }
        }
    </style>
</head>
<body class="antialiased bg-gray-900 text-white">

    <!-- Main Flex Layout -->
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <div class="w-64 bg-gray-800 shadow-lg p-4">
            <div>
                <h2 class="text-2xl font-bold text-white">Agent Hub</h2>
            </div>
            <nav class="mt-4">
                <!-- Dashboard Link -->
                <a href="{{ route('agent.dashboard') }}" 
                   class="block px-4 py-2 text-white hover:bg-gray-700 {{ Route::is('agent.dashboard') ? 'bg-gray-700' : '' }} sidebar-link">
                    Dashboard
                </a>

                <!-- Customers Link -->
                <a href="{{ route('agent.customers') }}" 
                   class="block px-4 py-2 text-white hover:bg-gray-700 {{ Route::is('agent.customers') ? 'bg-gray-700' : '' }} sidebar-link">
                    Customers
                </a>

                <!-- Meter Readings Link -->
                <a href="{{ route('agent.meter_readings') }}" 
                   class="block px-4 py-2 text-white hover:bg-gray-700 {{ Route::is('agent.meter_readings') ? 'bg-gray-700' : '' }} sidebar-link">
                    Meter Readings
                </a>

                <!-- Messages Link -->
                <a href="{{ route('messages.index') }}" 
                   class="block px-4 py-2 text-white hover:bg-gray-700 {{ Route::is('messages.*') ? 'bg-gray-700' : '' }} sidebar-link">
                    Messages
                    @php
                        $unreadCount = auth()->user()->unreadMessages()->count();
                    @endphp
                    @if($unreadCount > 0)
                        <span class="ml-2 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-blue-600 rounded-full">{{ $unreadCount }}</span>
                    @endif
                </a>

                <!-- Logout Form -->
                <form method="POST" action="{{ route('logout') }}" class="block px-4 py-2">
                    @csrf
                    <button type="submit" class="w-full text-left text-white hover:bg-gray-700">Logout</button>
                </form>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 p-6">
            <!-- Navbar for Mobile -->
            <nav class="bg-gray-800 shadow-md lg:hidden">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16 items-center">
                        <div class="flex items-center">
                            <span class="text-white text-xl font-semibold">Agent Hub</span>
                        </div>
                        <div class="flex items-center">
                            <button class="mobile-menu text-white focus:outline-none" aria-label="Toggle menu" onclick="toggleMenu()">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <!-- Mobile Menu Links -->
                    <div class="navbar-links lg:hidden">
                        <a href="{{ route('agent.dashboard') }}" 
                           class="block px-4 py-2 text-white hover:bg-gray-700 {{ Route::is('agent.dashboard') ? 'bg-gray-700' : '' }}">
                            Dashboard
                        </a>
                        <a href="{{ route('agent.customers') }}" 
                           class="block px-4 py-2 text-white hover:bg-gray-700 {{ Route::is('agent.customers') ? 'bg-gray-700' : '' }}">
                            Customers
                        </a>
                        <a href="{{ route('agent.meter_readings') }}" 
                           class="block px-4 py-2 text-white hover:bg-gray-700 {{ Route::is('agent.meter_readings') ? 'bg-gray-700' : '' }}">
                            Meter Readings
                        </a>
                        <a href="{{ route('messages.index') }}" 
                           class="block px-4 py-2 text-white hover:bg-gray-700 {{ Route::is('messages.*') ? 'bg-gray-700' : '' }}">
                            Messages
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="block px-4 py-2">
                            @csrf
                            <button type="submit" class="w-full text-left text-white hover:bg-gray-700">Logout</button>
                        </form>
                    </div>
                </div>
            </nav>

            <!-- Main Content Area -->
            <div class="max-w-7xl mx-auto px-4 py-6">
                @yield('content')
            </div>
        </div>
    </div>

    <!-- JavaScript Files -->
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/jquery-3.5.1.min.js') }}"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js"></script>

    <script>
        // Toggle mobile menu
        function toggleMenu() {
            const navbarLinks = document.querySelector('.navbar-links');
            navbarLinks.classList.toggle('active');
        }
    </script>

    @yield('scripts')
</body>
</html>