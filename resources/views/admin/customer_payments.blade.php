@extends('layouts.admin')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Heading -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-white mb-2">Customer Payment Status</h1>
            <p class="text-lg text-gray-300">View payment status for all customers</p>
        </div>

        <!-- Customer Payment Status Table -->
        <div class="bg-gray-800 shadow-lg rounded-lg overflow-hidden border border-gray-700">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-700">
                    <thead class="bg-gray-700">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Customer</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Phone</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Last Payment</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Balance</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-gray-800 divide-y divide-gray-700">
                        @foreach($customers as $customer)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="text-sm font-medium text-white">
                                            {{ $customer->name }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-300">{{ $customer->phone }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($customer->last_payment)
                                        <div class="text-sm text-gray-300">
                                            KES {{ number_format($customer->last_payment->amount, 2) }}
                                            <div class="text-xs text-gray-400">
                                                {{ $customer->last_payment->created_at->format('M d, Y H:i') }}
                                            </div>
                                        </div>
                                    @else
                                        <div class="text-sm text-gray-400">No payments yet</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusColors = [
                                            'completed' => 'bg-green-900 text-green-300',
                                            'pending' => 'bg-yellow-900 text-yellow-300',
                                            'failed' => 'bg-red-900 text-red-300',
                                            'no_payment' => 'bg-gray-900 text-gray-300'
                                        ];
                                        $statusText = [
                                            'completed' => 'Completed',
                                            'pending' => 'Pending',
                                            'failed' => 'Failed',
                                            'no_payment' => 'No Payment'
                                        ];
                                    @endphp
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$customer->payment_status] }}">
                                        {{ $statusText[$customer->payment_status] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-300">
                                        KES {{ number_format($customer->balance, 2) }}
                                        <div class="text-xs text-gray-400">
                                            {{ $customer->water_units }} units
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                    <a href="{{ route('admin.customers.profile', $customer) }}" 
                                       class="text-blue-400 hover:text-blue-300">
                                        View Details
                                    </a>
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
            $('table').DataTable({
                "order": [[2, "desc"]],
                "pageLength": 25,
                "responsive": true
            });
        });
    </script>
@endsection 