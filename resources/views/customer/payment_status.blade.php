@extends('layouts.customer')

@section('content')
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8 bg-gray-800 rounded-xl shadow-lg border border-gray-700">
        <!-- Heading -->
        <div>
            <h1 class="text-3xl font-bold text-blue-400 mb-2 tracking-tight">Payment Status</h1>
            <p class="text-lg text-gray-300">View your payment history and status</p>
        </div>

        <!-- Payment Status Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Completed Payments -->
            <div class="bg-gray-700 rounded-lg p-6 shadow-md border border-gray-600">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-semibold text-white">Completed</h3>
                    <span class="px-3 py-1 text-sm font-medium bg-green-900 text-green-300 rounded-full">
                        {{ $payments->has('completed') ? $payments['completed']->count() : 0 }}
                    </span>
                </div>
                <div class="mt-4 space-y-2">
                    @forelse($payments->has('completed') ? $payments['completed'] : [] as $payment)
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-300">KES {{ number_format($payment->amount, 2) }}</span>
                            <span class="text-gray-400">{{ $payment->created_at->format('M d, Y') }}</span>
                        </div>
                    @empty
                        <p class="text-gray-400 text-sm">No completed payments</p>
                    @endforelse
                </div>
            </div>

            <!-- Pending Payments -->
            <div class="bg-gray-700 rounded-lg p-6 shadow-md border border-gray-600">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-semibold text-white">Pending</h3>
                    <span class="px-3 py-1 text-sm font-medium bg-yellow-900 text-yellow-300 rounded-full">
                        {{ $payments->has('pending') ? $payments['pending']->count() : 0 }}
                    </span>
                </div>
                <div class="mt-4 space-y-2">
                    @forelse($payments->has('pending') ? $payments['pending'] : [] as $payment)
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-300">KES {{ number_format($payment->amount, 2) }}</span>
                            <span class="text-gray-400">{{ $payment->created_at->format('M d, Y') }}</span>
                        </div>
                    @empty
                        <p class="text-gray-400 text-sm">No pending payments</p>
                    @endforelse
                </div>
            </div>

            <!-- Failed Payments -->
            <div class="bg-gray-700 rounded-lg p-6 shadow-md border border-gray-600">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-semibold text-white">Failed</h3>
                    <span class="px-3 py-1 text-sm font-medium bg-red-900 text-red-300 rounded-full">
                        {{ $payments->has('failed') ? $payments['failed']->count() : 0 }}
                    </span>
                </div>
                <div class="mt-4 space-y-2">
                    @forelse($payments->has('failed') ? $payments['failed'] : [] as $payment)
                        <div class="flex justify-between items-center text-sm">
                            <div>
                                <span class="text-gray-300">KES {{ number_format($payment->amount, 2) }}</span>
                                <p class="text-red-400 text-xs">{{ $payment->failure_reason ?? 'Payment failed' }}</p>
                            </div>
                            <span class="text-gray-400">{{ $payment->created_at->format('M d, Y') }}</span>
                        </div>
                    @empty
                        <p class="text-gray-400 text-sm">No failed payments</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Current Balance -->
        <div class="bg-gray-700 rounded-lg p-6 shadow-md border border-gray-600">
            <h2 class="text-2xl font-bold text-white mb-4">Current Balance</h2>
            <div class="text-center">
                <p class="text-3xl font-bold text-white">KES {{ number_format($user->balance, 2) }}</p>
                <p class="text-gray-400 mt-2">{{ $user->water_units }} units remaining</p>
            </div>
        </div>
    </div>
@endsection 