@extends('layouts.agent')

@section('content')
    <div class="container mx-auto px-4 py-8 max-w-7xl">
        <!-- Page Heading -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-white">My Customers</h1>
            <a href="{{ route('agent.meter_readings') }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                Meter Readings
            </a>
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div class="mb-4 bg-green-900 border-l-4 border-green-500 text-green-300 p-4 rounded-lg shadow-md">
                {{ session('success') }}
            </div>
        @endif

        <!-- Error Message -->
        @if(session('error'))
            <div class="mb-4 bg-red-900 border-l-4 border-red-500 text-red-300 p-4 rounded-lg shadow-md">
                {{ session('error') }}
            </div>
        @endif

        <!-- Customers Table -->
        <div class="bg-gray-800 shadow-lg rounded-xl overflow-hidden border border-gray-700">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-700" id="customers-table">
                    <thead class="bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Phone</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Current Units</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Balance</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Last Reading</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-gray-800 divide-y divide-gray-700">
                        @forelse($customers as $customer)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-white">{{ $customer->name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-300">{{ $customer->email }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-300">{{ $customer->phone }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-300">{{ $customer->water_units ?? 0 }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-300">KES {{ number_format($customer->balance ?? 0, 2) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($customer->meterReadings->isNotEmpty())
                                        <div class="text-sm text-gray-300">
                                            {{ $customer->meterReadings->first()->created_at->format('M d, Y') }}
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ 
                                                ($customer->meterReadings->first()->status === 'approved') 
                                                    ? 'bg-green-900 text-green-300' 
                                                    : ($customer->meterReadings->first()->status === 'pending' 
                                                        ? 'bg-yellow-900 text-yellow-300' 
                                                        : 'bg-red-900 text-red-300') 
                                            }} ml-2">
                                                {{ ucfirst($customer->meterReadings->first()->status) }}
                                            </span>
                                        </div>
                                    @else
                                        <div class="text-sm text-gray-500">No readings</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('messages.create', ['recipient' => $customer->id]) }}" 
                                       class="text-green-400 hover:text-green-300">
                                        Message
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-gray-400">
                                    No customers found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize DataTable with dark mode styling
            $('#customers-table').DataTable({
                "order": [[0, "asc"]],
                "paging": true,
                "searching": true,
                "info": true,
                "dom": '<"flex flex-col md:flex-row justify-between items-center mb-4"<"flex items-center mb-4 md:mb-0"l><"flex items-center"f>>rt<"flex flex-col md:flex-row justify-between items-center mt-4"<"flex items-center mb-4 md:mb-0"i><"flex items-center"p>>',
                "language": {
                    "search": "",
                    "searchPlaceholder": "Search customers...",
                    "lengthMenu": "Show _MENU_ entries",
                    "info": "Showing _START_ to _END_ of _TOTAL_ customers",
                    "paginate": {
                        "first": "First",
                        "last": "Last",
                        "next": "Next",
                        "previous": "Previous"
                    }
                }
            });
        });
    </script>
@endsection