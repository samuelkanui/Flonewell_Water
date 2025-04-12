<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Customer - {{ config('app.name', 'Water Payment System') }}</title>

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
    </style>
</head>
<body class="antialiased bg-gray-900 text-white">

    <!-- Main Flex Layout -->
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <div class="w-64 bg-gray-800 shadow-lg p-4">
            <div>
                <h2 class="text-2xl font-bold text-white">Customer Portal</h2>
            </div>
            <nav class="mt-4">
                <!-- Dashboard Link -->
                <a href="{{ route('dashboard') }}" 
                   class="block px-4 py-2 text-white hover:bg-gray-700 {{ Route::is('dashboard') ? 'bg-gray-700' : '' }} sidebar-link">
                    Dashboard
                </a>

                <!-- Payment Status Link -->
                <a href="{{ route('customer.payment_status') }}" 
                   class="block px-4 py-2 text-white hover:bg-gray-700 {{ Route::is('customer.payment_status') ? 'bg-gray-700' : '' }} sidebar-link">
                    Payment Status
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
            @yield('content')
        </div>
    </div>

    <!-- JavaScript Libraries -->
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/jquery-3.5.1.min.js') }}"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js"></script>

    <!-- Additional Custom Scripts -->
    @yield('scripts')
</body>
</html>