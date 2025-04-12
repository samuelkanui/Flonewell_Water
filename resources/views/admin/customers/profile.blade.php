@extends('layouts.admin')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Heading -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-white mb-2">Customer Profile</h1>
            <p class="text-lg text-gray-300">View and manage customer details</p>
        </div>

        <!-- Customer Information -->
        <div class="bg-gray-800 shadow-lg rounded-lg overflow-hidden border border-gray-700 mb-8">
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h2 class="text-xl font-semibold text-white mb-4">Personal Information</h2>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-400">Name</label>
                                <p class="mt-1 text-white">{{ $customer->name }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-400">Email</label>
                                <p class="mt-1 text-white">{{ $customer->email }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-400">Phone</label>
                                <p class="mt-1 text-white">{{ $customer->phone }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-400">Status</label>
                                <p class="mt-1">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $customer->is_active ? 'bg-green-900 text-green-300' : 'bg-red-900 text-red-300' }}">
                                        {{ $customer->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div>
                        <h2 class="text-xl font-semibold text-white mb-4">Account Information</h2>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-400">Water Units</label>
                                <p class="mt-1 text-white">{{ $customer->water_units }} units</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-400">Current Balance</label>
                                <p class="mt-1 text-white">KES {{ number_format($customer->balance, 2) }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-400">Last Payment</label>
                                <p class="mt-1 text-white">
                                    @if($customer->payments->isNotEmpty())
                                        KES {{ number_format($customer->payments->first()->amount, 2) }}
                                        <span class="text-gray-400 text-sm">({{ $customer->payments->first()->created_at->format('M d, Y') }})</span>
                                    @else
                                        No payments yet
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Meter Readings -->
        <div class="bg-gray-800 shadow-lg rounded-lg overflow-hidden border border-gray-700 mb-8">
            <div class="p-6">
                <h2 class="text-xl font-semibold text-white mb-4">Recent Meter Readings</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-700">
                        <thead class="bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Previous Reading</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Current Reading</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Units</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-gray-800 divide-y divide-gray-700">
                            @forelse($meterReadings as $reading)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                        {{ $reading->created_at->format('M d, Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                        {{ $reading->previous_reading }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                        {{ $reading->current_reading }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                        {{ $reading->units }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $reading->status === 'approved' ? 'bg-green-900 text-green-300' : 'bg-yellow-900 text-yellow-300' }}">
                                            {{ ucfirst($reading->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-400">
                                        No meter readings found
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Recent Payments -->
        <div class="bg-gray-800 shadow-lg rounded-lg overflow-hidden border border-gray-700">
            <div class="p-6">
                <h2 class="text-xl font-semibold text-white mb-4">Recent Payments</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-700">
                        <thead class="bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Reference</th>
                            </tr>
                        </thead>
                        <tbody class="bg-gray-800 divide-y divide-gray-700">
                            @forelse($payments as $payment)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                        {{ $payment->created_at->format('M d, Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                        KES {{ number_format($payment->amount, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $payment->status === 'completed' ? 'bg-green-900 text-green-300' : 'bg-yellow-900 text-yellow-300' }}">
                                            {{ ucfirst($payment->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                        {{ $payment->reference }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-400">
                                        No payments found
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection 