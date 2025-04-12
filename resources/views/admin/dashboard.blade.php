@extends('layouts.admin')

@section('content')
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Heading -->
        <h1 class="text-3xl font-bold text-white mb-6 tracking-tight">Admin Dashboard</h1>

        <!-- Success/Error Messages -->
        @if (session('success'))
            <div class="bg-green-600 text-white px-4 py-3 rounded-lg mb-6 shadow-md transition-all duration-300 ease-in-out transform hover:scale-[1.01]">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="bg-red-600 text-white px-4 py-3 rounded-lg mb-6 shadow-md transition-all duration-300 ease-in-out transform hover:scale-[1.01]">
                {{ session('error') }}
            </div>
        @endif

        <!-- Add New Customer Button -->
        <a href="{{ route('customers.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-300 mb-6">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Add New Customer
        </a>

        <!-- Customer List Table -->
        <div class="bg-gray-800 shadow-xl rounded-xl p-6 border border-gray-700 overflow-hidden">
            <h2 class="text-xl font-semibold text-white mb-4">Customer List</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-700" id="customers-table">
                    <thead class="bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">#</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Phone</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Units</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Balance</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-gray-800 divide-y divide-gray-700">
                        @foreach ($customers as $index => $customer)
                            <tr class="hover:bg-gray-600 transition-colors duration-200">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">{{ $index + 1 }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">{{ $customer->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">{{ $customer->email }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">{{ $customer->phone }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">{{ $customer->water_units }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">{{ number_format($customer->balance, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300 space-x-2">
                                    <a href="{{ route('customers.edit', $customer) }}" class="inline-flex items-center px-3 py-1 bg-blue-500 text-white rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-400 transition-all duration-300">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        Edit
                                    </a>
                                    <form action="{{ route('customers.destroy', $customer) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center px-3 py-1 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-300">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5-4h4M4 7h16"></path>
                                            </svg>
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('#customers-table').DataTable({
                "paging": true,
                "searching": true,
                "info": true,
                "responsive": true,
                "order": [[0, "asc"]]
            });
        });
    </script>
@endsection